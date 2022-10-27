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

class RevenueByChannel implements FromCollection, WithTitle, WithColumnWidths, WithCustomStartCell, WithHeadings, WithStyles
{
    private $period;
    public $cnt;

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

        $salesData = InfoOrder::whereBetween('detailed_at', $this->period)
                                ->where('deliverymethod', '!=','')
                                ->select(
                                    DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount'), 'TypeDelivery as channel'
                                )
                                ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                                ->where('infoOrder.TypeDelivery', '!=', 'DELIVERY')
                                ->groupBy('TypeDelivery')->orderBy('amount', 'DESC')->get();
        $salesTotal = $salesData->sum('amount');
        $this->cnt = $salesData->count();
        $month_period = [ Carbon::parse($this->period[0])->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subMonth()->endOfMonth()->endOfDay()->toDateTimeString()];
        $year_period = [ Carbon::parse($this->period[0])->subYear()->startOfYear()->startOfDay()->toDateTimeString(), Carbon::parse($this->period[1])->subYear()->endOfYear()->endOfDay()->toDateTimeString()];
        $salesTotalToCompareYearMode =  InfoOrder::whereBetween('detailed_at', $year_period)
                    ->where('deliverymethod', '!=','')
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
                    )->value('amount');
        $salesTotalToCompareMonthMode =  InfoOrder::whereBetween('detailed_at', $month_period)
                    ->where('deliverymethod', '!=','')
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(total), 2), 0) as amount')
                    )->value('amount');
        $salesItemTotal = DB::table('detailingitem')
                        ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                        ->whereBetween('infoOrder.detailed_at', $this->period)
                        ->where('infoOrder.TypeDelivery', '!=', 'DELIVERY')
                        ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                        ->select(
                            DB::raw('count(*) as amount')
                        )->value('amount');
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
        foreach ($salesData as  $item) {
            $data[] = [ $item->channel, $item->amount, DB::table('detailingitem')->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
            ->whereBetween('infoOrder.detailed_at', $this->period)
            ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
            ->where('infoOrder.TypeDelivery', $item->channel)->count() ];
        }
        $b2bSales = InfoOrder::join('infoCustomer', function($join){
                        $join->on('infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')->where('infoOrder.CustomerID', '!=', '');
                    })    
                    ->whereBetween('detailed_at', $this->period)            
                    ->where('infoOrder.deliverymethod', '!=','')
                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                    ->where('infoCustomer.btob', 1)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(infoOrder.total)), 0) as amount')
                    )
                    ->value('amount');
        $b2cSales = InfoOrder::join('infoCustomer', function($join){
                        $join->on('infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')->where('infoOrder.CustomerID', '!=', '');
                    })    
                    ->whereBetween('detailed_at', $this->period)            
                    ->where('infoOrder.deliverymethod', '!=','')
                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                    ->where('infoCustomer.btob', 0)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('IFNULL(ROUND(SUM(infoOrder.total)), 0) as amount')
                    )
                    ->value('amount');
        $b2bItemTotal = DB::table('detailingitem')
                    ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                    ->join('infoCustomer', function($join){
                        $join->on('infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')->where('infoOrder.CustomerID', '!=', '');
                    }) 
                    ->whereBetween('infoOrder.detailed_at', $this->period)
                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                    ->where('infoCustomer.btob', 1)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('count(*) as amount')
                    )->value('amount');        
        $b2cItemTotal = DB::table('detailingitem')
                    ->join('infoOrder', 'infoOrder.id', '=', 'detailingitem.order_id')
                    ->join('infoCustomer', function($join){
                        $join->on('infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')->where('infoOrder.CustomerID', '!=', '');
                    }) 
                    ->whereBetween('infoOrder.detailed_at', $this->period)
                    ->where('infoOrder.TypeDelivery', 'DELIVERY')
                    ->where('infoCustomer.btob', 0)
                    ->whereNotIn('infoOrder.Status', ['DELETE', 'IN DETAILING','VOID','VOIDED', 'CANCEL','PENDING','DELETED'])
                    ->select(
                        DB::raw('count(*) as amount')
                    )->value('amount');        
        $data[] = ['Total Store Revenue', $salesTotal, $salesItemTotal];
        $data[] = ['Business Deliveries Revenue', '', ''];
        $data[] = ['Home Deliveries Revenue', '', ''];
        $data[] = ['Atelier Revenue', '', ''];
        $data[] = ['Other sales (Shopify etc.)', '', ''];
        $data[] = ['Other Revenue', '', ''];
        $data[] = ['Total Sales', $salesTotal + $b2bSales + $b2cSales, $salesItemTotal + $b2bItemTotal + $b2cItemTotal];
        $data[] = ['growth % vs prev year', ($salesTotalToCompareYearMode != 0 ? (($salesTotal + $b2bSales + $b2cSales)/$salesTotalToCompareYearMode - 1)*100 : '--'), ($salesItemTotalToCompareYearMode != 0 ? (($salesItemTotal + $b2bItemTotal + $b2cItemTotal)/$salesItemTotalToCompareYearMode -1)*100 : '--')];
        $data[] = ['growth % vs prev month', ($salesItemTotalToCompareMonthMode != 0 ? (($salesTotal + $b2bSales + $b2cSales)/$salesTotalToCompareMonthMode - 1)*100 : '--'), ($salesItemTotalToCompareMonthMode != 0 ? (($salesItemTotal + $b2bItemTotal + $b2cItemTotal)/$salesItemTotalToCompareMonthMode -1)*100 : '--')];

        return collect([ $data ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Revenue By Channel';
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
        return [ 'Revenue By Channel', '', ''];
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
        $sheet->getStyle('B4:D4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

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

        $sheet->getStyle('B5:D5')->getBorders()->getInside()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getRowDimension(5)->setRowHeight(10, 'px');

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
        $sheet->getStyle('C'.(7 + $this->cnt).':D'.(8 + $this->cnt))->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C'.(10 + $this->cnt).':D'.(10 + $this->cnt))->applyFromArray($yellowFieldStyle);

        $sheet->getStyle('C6:D15')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);
        $subTotalCellStyle = [
            'font'  =>[
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_BLACK],
                'size' => 10
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('B'.(6 + $this->cnt).':D'.(6 + $this->cnt))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(9 + $this->cnt).':D'.(9 + $this->cnt))->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B'.(11 + $this->cnt).':D'.(11 + $this->cnt))->applyFromArray($subTotalCellStyle);

        $sheet->getStyle('C'.(7 + $this->cnt).':D'.(8 + $this->cnt))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);
        $sheet->getStyle('C'.(10 + $this->cnt).':D'.(10 + $this->cnt))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);
        $sheet->getStyle('B'.(12 + $this->cnt).':D'.(12 + $this->cnt))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $lastRowStyle = [
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
        $sheet->getStyle('B'.(12 + $this->cnt).':D'.(12 + $this->cnt))->applyFromArray($lastRowStyle);
    }
}