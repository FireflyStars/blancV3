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

class RevenueByPayment implements FromCollection, WithTitle, WithColumnWidths, WithCustomStartCell, WithHeadings, WithStyles
{
    private $period;
    private $cnt;

    public function __construct(array $period)
    {
        $this->period = $period;
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        $data = [];
        $data[] = ['', '', ''];
        $data[] = [  Carbon::parse($this->period[0])->format('d/m/Y').' To '.Carbon::parse($this->period[1])->format('d/m/Y'), '£ incl. VAT', '# of pieces'];
        $data[] = ['', '', ''];
        $salesByPayment = DB::table('payments')->join('infoOrder', 'infoOrder.id', '=', 'payments.order_id')
            ->whereBetween('infoOrder.detailed_at', $this->period)
            ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
            ->select(
                DB::raw('IFNULL(ROUND(SUM(payments.montant), 2), 0) as amount'),
                'payments.type as channel',
                DB::raw('count(*) as count'),
            )->groupBy('payments.type')->orderByDesc('amount')->get();
        foreach ($salesByPayment as $item) {
            $data[] = [$item->channel, $item->amount, $item->amount];
        }
        $this->cnt = $salesByPayment->count();
        $salesByPaymentTotal = InfoOrder::whereBetween('detailed_at', $this->period)
            ->where('deliverymethod', '!=','')
            ->select(
                DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
            )
            ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING', 'VOID', 'VOIDED', 'CANCEL', 'PENDING', 'DELETED'])
            ->value('amount');
        $month_period = [ Carbon::parse($this->period[0])->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subMonth()->endOfMonth()->endOfDay()->toDateTimeString()];
        $year_period = [ Carbon::parse($this->period[0])->subYear()->startOfYear()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subYear()->endOfYear()->endOfDay()->toDateTimeString()];            
        $salesTotalToCompareMonthMode =  InfoOrder::whereBetween('detailed_at', $month_period)
                ->where('deliverymethod', '!=','')
                ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                ->select(
                    DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
                )->value('amount');
        $salesTotalToCompareYearMode =  InfoOrder::whereBetween('detailed_at', $year_period)
                ->where('deliverymethod', '!=','')
                ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                ->select(
                    DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
                )->value('amount');
        $salesItemTotal = DB::table('detailingitem')
                ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                ->whereBetween('infoOrder.detailed_at', $this->period)
                ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                ->select(
                    DB::raw('IFNULL(ROUND(SUM(detailingitem.tailoring_price+detailingitem.cleaning_addon_price+detailingitem.dry_cleaning_price), 2), 0) as amount'),
                    DB::raw('count(*) as count')
                )->first();
        $salesItemTotalToCompareYearMode = DB::table('detailingitem')
                ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                ->whereBetween('infoOrder.detailed_at', $year_period)
                ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                ->select(
                    DB::raw('count(*) as amount')
                )->value('amount');
        $salesItemTotalToCompareMonthMode = DB::table('detailingitem')
                        ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                        ->whereBetween('infoOrder.detailed_at', $month_period)
                        ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                        ->select(
                            DB::raw('count(*) as amount')
                        )->value('amount');
        $data[] = ['Sub-Total (Item Sales)', $salesItemTotal->amount, $salesItemTotal->count];
        $data[] = ['Account discounts', '', ''];
        $data[] = ['Order discounts', '', ''];
        $data[] = ['Delivery Fees', '', ''];
        $data[] = ['Failed Delivery Fees', '', ''];
        $data[] = ['Total Sales', $salesByPaymentTotal, $salesItemTotal->count];
        $data[] = ['growth % vs prev year', ($salesTotalToCompareYearMode != 0 ? ($salesByPaymentTotal/$salesTotalToCompareYearMode - 1)*100 : '--'), ($salesItemTotalToCompareYearMode != 0 ? ($salesItemTotal->count/$salesItemTotalToCompareYearMode -1)*100 : '--')];
        $data[] = ['growth % vs prev month', ($salesItemTotalToCompareMonthMode != 0 ? ($salesByPaymentTotal/$salesTotalToCompareMonthMode - 1)*100 : '--'), ($salesItemTotalToCompareMonthMode != 0 ? ($salesItemTotal->count/$salesItemTotalToCompareMonthMode -1)*100 : '--')];
        return collect([ $data ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Revenue By Payment Type';
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
        return [ 'Revenue By Payment Type', '', ''];
    }
    public function styles(Worksheet $sheet)
    {
        $fontStyle = [
            'font'  =>[
                'color' => ['argb' => Color::COLOR_BLACK],
                'size' => 10
            ]
        ];
        $sheet->getStyle('B2:B18')->applyFromArray($fontStyle);
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
        $sheet->getStyle('C6:D'.(5 + $this->cnt))->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C6:D'.(5 + $this->cnt))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);        

        $sheet->getStyle('C'.(7 + $this->cnt).':D'.(10 + $this->cnt))->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C'.(7 + $this->cnt).':D'.(10 + $this->cnt))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);        
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
        $sheet->getStyle('B'.(6 + $this->cnt).':D'.(6 + $this->cnt))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(6 + $this->cnt).':D'.(6 + $this->cnt))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->getStyle('B'.(11 + $this->cnt).':D'.(11 + $this->cnt))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(11 + $this->cnt).':D'.(11 + $this->cnt))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

    }
}