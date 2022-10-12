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

class RevenueByServiceType implements FromCollection, WithTitle, WithColumnWidths, WithCustomStartCell, WithHeadings, WithStyles
{
    private $period;

    public function __construct(array $period)
    {
        $this->period = $period;
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        return collect([
            ['', '', ''],
            [  Carbon::parse($this->period[0])->format('d/m/Y').' To '.Carbon::parse($this->period[1])->format('d/m/Y'), '£ incl. VAT', '# of pieces'],
            ['', '', ''],
            ['Cleaning', '', ''],
            ['Tailoring & Alterations', '', ''],
            ['Service 3', '', ''],
            ['Service 4', '', ''],
            ['Service 5', '', ''],

            ['Sub-Total (Item Sales)', '', ''],
            ['Account discounts', '', ''],
            ['Order discounts', '', ''],
            ['Delivery Fees', '', ''],
            ['Failed Delivery Fees', '', ''],

            ['Total Sales', '', ''],
            ['growth % vs prev year', '', ''],
            ['growth % vs prev month', '', ''],
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Revenue By Service Type';
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
        return [ 'Revenue By Service Type', '', ''];
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
        $sheet->getStyle('C6:D10')->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C6:D10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);        

        $sheet->getStyle('C12:D15')->applyFromArray($yellowFieldStyle);
        $sheet->getStyle('C12:D15')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOTTED);        
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
        $sheet->getStyle('B11:D11')->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B11:D11')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->getStyle('B16:D16')->applyFromArray($subTotalCellStyle);
        $sheet->getStyle('B16:D16')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

    }
}