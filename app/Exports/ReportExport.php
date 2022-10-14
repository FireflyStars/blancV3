<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Sheets\RevenueByChannel;
use App\Exports\Sheets\RevenueByCategory;
use App\Exports\Sheets\RevenueByServiceType;
use App\Exports\Sheets\RevenueByDepartment;
use App\Exports\Sheets\RevenueByLane;

class ReportExport implements WithMultipleSheets, Responsable
{
    use Exportable;
    
    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'Reports.xlsx';
    
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
    public function __construct(array $period)
    {
        $this->period = $period;
    }    
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new RevenueByChannel($this->period);
        $sheets[] = new RevenueByCategory($this->period);
        $sheets[] = new RevenueByDepartment($this->period);
        $sheets[] = new RevenueByServiceType($this->period);
        $sheets[] = new RevenueByLane($this->period);

        return $sheets;
    }    
}
