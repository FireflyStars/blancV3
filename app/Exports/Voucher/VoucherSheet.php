<?php

namespace App\Exports\Voucher;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use Illuminate\Support\Facades\DB;

class VoucherSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    private $period;
    private $dataCnt;

    public function __construct()
    {
        // $this->period = $period;
    }

    /**
     * @return Builder
     */
    public function collection()
    {

        $data = DB::table('pickup')->join('infoCustomer', 'infoCustomer.CustomerID', '=', 'pickup.CustomerID')
                ->where('pickup.GarmentInstruction', 'Not Like', '%"Voucher":""%')
                ->where('pickup.GarmentInstruction', 'Like', '%"Voucher":"%')
                ->select(
                    'pickup.id', 'pickup.GarmentInstruction', DB::raw("0 as Voucher"), 'pickup.created_at', 'infoCustomer.Name',
                    'infoCustomer.EmailAddress', 'infoCustomer.LastName', 'infoCustomer.FirstName'
                )->get();
        $this->dataCnt = $data->count();
        foreach ($data as $item) {
            if( gettype(json_decode($item->GarmentInstruction)) == 'object'){
                $item->Voucher = json_decode($item->GarmentInstruction)->Voucher;
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Voucher from Booking';
    }

    public function headings(): array
    {
        return [ 'id', 'GarmentInstruction', 'Voucher', 'created_at', 'Name', 'EmailAddress', 'LastName', 'FirstName'];
    }
    public function styles(Worksheet $sheet)
    {
        $headerStyle = [
            'font'  =>[
                'bold'  => true,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $yellowFieldStyle = [
            'font'  =>[
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ];
        $sheet->getStyle('C1:C'.$this->dataCnt)->applyFromArray($yellowFieldStyle);
    }
}