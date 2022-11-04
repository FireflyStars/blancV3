<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Voucher\VoucherSheet;
use App\Exports\Voucher\VoucherDetailSheet;

use Carbon\Carbon;

class VoucherExport implements WithMultipleSheets, Responsable
{
    use Exportable;
    
    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'voucher.xlsx';
    
    /**
    * Optional Writer Type
    */
    private $writerType = Excel::XLSX;
    
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];    

    protected $period;
    public function __construct()
    {
        // $this->period = $period;
        // $this->fileName = sprintf("all-voucher-%s-%s.xlsx", Carbon::parse($period[0])->format('Ymd'), Carbon::parse($period[1])->format('Ymd'));
    }    
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new VoucherDetailSheet();
        $sheets[] = new VoucherSheet();

        return $sheets;
    }    
}
