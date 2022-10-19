<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use App\Models\InfoOrder;
use Illuminate\Support\Facades\DB;

class RevenueByCategory implements FromCollection, WithTitle, WithColumnWidths, WithCustomStartCell, WithHeadings, WithStyles
{
    private $period;
    private $dataCnt;

    public function __construct(array $period)
    {
        $this->period = $period;
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        $data =[];
        $data[] = ['', '', ''];
        $data[] = [  Carbon::parse($this->period[0])->format('d/m/Y').' To '.Carbon::parse($this->period[1])->format('d/m/Y'), '£ incl. VAT', '# of pieces'];
        $data[] = ['', '', ''];

        $salesData = DB::table('detailingitem')->leftJoin('categories', 'categories.id', '=', 'detailingitem.category_id')
                    ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                    ->whereBetween('infoOrder.detailed_at', $this->period)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(categories.name, "Other")  as channel'),
                        DB::raw('IFNULL(ROUND(SUM(detailingitem.tailoring_price+detailingitem.cleaning_addon_price+detailingitem.dry_cleaning_price), 2), 0) as amount'),
                        DB::raw('IFNULL(COUNT(*), 0) as count')
                    )
                    ->groupBy('categories.name')->orderBy('amount', 'DESC')->get();
        $salesTotal = $salesData->sum('amount');
        $this->dataCnt = $salesData->count(); 
        
        $month_period = [ Carbon::parse($this->period[0])->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subMonth()->endOfMonth()->endOfDay()->toDateTimeString()];
        $year_period = [ Carbon::parse($this->period[0])->subYear()->startOfYear()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subYear()->endOfYear()->endOfDay()->toDateTimeString()];
        
        $salesTotalToCompareYearMode =  DB::table('detailingitem')
                    ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                    ->whereBetween('infoOrder.detailed_at', $year_period)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount'),
                        DB::raw('IFNULL(COUNT(*), 0) as count')
                    )->first();
        $salesTotalToCompareMonthMode =  DB::table('detailingitem')
                    ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                    ->whereBetween('infoOrder.detailed_at', $month_period)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount'),
                        DB::raw('IFNULL(COUNT(*), 0) as count')
                    )->first();
        foreach ($salesData as  $item) {
            $data[] = [ $item->channel, $item->amount, $item->count ];
        }
        $salesByTypeitemTotal = InfoOrder::whereBetween('detailed_at', $this->period)
            ->where('deliverymethod', '!=','')
            ->select(
                DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
            )
            ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING', 'VOID', 'VOIDED', 'CANCEL', 'PENDING', 'DELETED'])
            ->value('amount');
        $salesByTypeitemTotalOfItem = DB::table('detailingitem')
            ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
            ->whereBetween('infoOrder.detailed_at', $this->period)
            ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
            ->select(
                DB::raw('IFNULL(ROUND(SUM(detailingitem.tailoring_price+detailingitem.cleaning_addon_price+detailingitem.dry_cleaning_price), 2), 0) as amount')
            )->value('amount');
        $data[] = ['Sub-Total (Item Sales)', $salesData->sum('amount'), $salesData->sum('count')];
        $data[] = ['Account discounts', '', ''];
        $data[] = ['Order discounts', '', ''];
        $data[] = ['Vouchers', '', ''];
        $data[] = ['Delivery Fees', '', ''];
        $data[] = ['Failed Delivery Fees', '', ''];
        $data[] = ['Other', $salesByTypeitemTotal - $salesByTypeitemTotalOfItem, ''];
        $data[] = ['Total (incl discount & add. fees)', $salesData->sum('amount'), $salesData->sum('count')];
        $data[] = ['growth % vs prev year', ($salesTotalToCompareYearMode->amount != 0 ? ($salesTotal/$salesTotalToCompareYearMode->amount - 1)*100 : '--'), ($salesTotalToCompareYearMode->count != 0 ? ($salesData->sum('count')/$salesTotalToCompareYearMode->count -1)*100 : '--')];
        $data[] = ['growth % vs prev month', ($salesTotalToCompareMonthMode->amount != 0 ? ($salesTotal/$salesTotalToCompareMonthMode->amount - 1)*100 : '--'), ($salesTotalToCompareMonthMode->count != 0 ? ($salesData->sum('count')/$salesTotalToCompareMonthMode->count -1)*100 : '--')];
        return collect([ $data ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Revenue By Category';
    }
    public function startCell(): string
    {
        return 'B2';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 40,
            'C' => 20,
            'D' => 20,
        ];
    }
    public function headings(): array
    {
        return [ 'Revenue By Category', '', ''];
    }
    public function styles(Worksheet $sheet)
    {
        $fontStyle = [
            'font'  =>[
                'color' => ['argb' => Color::COLOR_BLACK],
                'size' => 10
            ]
        ];
        $sheet->getStyle('B2:B24')->applyFromArray($fontStyle);
        $headerStyle = [
            'font'  =>[
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 10
            ],
            'fill'  =>[
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '1F3864'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('B2:D2')->applyFromArray($headerStyle);

        $sheet->getRowDimension(3)->setRowHeight(10, 'px');
        $sheet->getStyle('B3:D3')->getBorders()->getInside()->setBorderStyle(Border::BORDER_NONE);
        
        $headerStyle = [
            'font'  =>[
                'bold'  => true,
                'color' => ['rgb' => Color::COLOR_BLACK],
                'size' => 10
            ],
            'fill'  =>[
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'eaeaea'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        
        $sheet->getRowDimension(4)->setRowHeight(40, 'px');
        $sheet->getStyle('B4:D4')->applyFromArray($headerStyle);
        $sheet->getStyle('B4:D4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        
        $sheet->getRowDimension(5)->setRowHeight(10, 'px');
        $sheet->getStyle('B5:D5')->getBorders()->getInside()->setBorderStyle(Border::BORDER_NONE);
        $subTotalCellStyle = [
            'font'  =>[
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_BLACK],
                'size' => 10
            ],
            'fill'  =>[
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'd9e2f3'],
            ],            
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('B'.(5 + $this->dataCnt + 1).':D'.(5 + $this->dataCnt + 1))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(5 + $this->dataCnt + 1).':D'.(5 + $this->dataCnt + 1))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $yellowFieldStyle = [
            'font'  =>[
                'bold'  => false,
                'color' => ['argb' => Color::COLOR_BLACK],
                'size' => 10
            ],
            'fill'  =>[
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'ffff99'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('C'.(5 + $this->dataCnt + 2).':D'.(5 + $this->dataCnt + 7))->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C'.(5 + $this->dataCnt + 2).':D'.(5 + $this->dataCnt + 7))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);

        $sheet->getStyle('B'.(5 + $this->dataCnt + 8).':D'.(5 + $this->dataCnt + 8))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(5 + $this->dataCnt + 8).':D'.(5 + $this->dataCnt + 8))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
    }
}