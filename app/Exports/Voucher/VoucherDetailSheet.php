<?php

namespace App\Exports\Voucher;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use App\Models\InfoOrder;

class VoucherDetailSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    private $period;

    public function __construct()
    {
        // $this->period = $period;
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        $data = InfoOrder::join('vouchers_histories', 'infoOrder.id', '=', 'vouchers_histories.order_id')
                        ->join('infoCustomer', 'infoCustomer.CustomerID', 'vouchers_histories.CustomerID')
                        ->select( 
                            'infoOrder.id', 'infoOrder.deliverymethod', 'infoOrder.VoucherDiscount', 'infoOrder.Total',
                            'infoOrder.TypeDelivery', 'infoOrder.created_at', 'infoOrder.detailed_at', 'vouchers_histories.code',
                            'infoCustomer.Name', 'infoCustomer.EmailAddress', 'infoCustomer.LastName', 'infoCustomer.FirstName'
                        )
                        ->whereNotIn('infoOrder.status', [ 'DELETE', 'IN DETAILING', 'VOID'])
                        // ->whereBetween('vouchers_histories.created_at', $this->period)
                        ->get();
        return $data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Voucher Detailing Blanc V3';
    }

    public function headings(): array
    {
        return [ 'Order Id', 'deliverymethod', 'VoucherDiscount', 'Total', 'TypeDelivery', 'created_at', 'detailed_at', 'code', 'Name', 'EmailAddress', 'LastName', 'FirstName'];
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
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
    }    
}