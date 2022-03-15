<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;

    public static function getAssemblyPostes(){
        $formatted_postes=array(
            ['formatted_name'=>'In <br> CC','group_name'=>'CustomerCare'],
            ['formatted_name'=>'With <br> Partner','group_name'=>'Partner'],
            ['formatted_name'=>'In <br> Tailoring','group_name'=>'Tailoring'],
            ['formatted_name'=>'Awaiting <br> Cleaning','group_name'=>'Spotting'],
            ['formatted_name'=>'In <br> Cleaning','group_name'=>'Quality Control 1'],
            ['formatted_name'=>'Awaiting <br> Pressing','group_name'=>'Pressing'],
            ['formatted_name'=>'Awaiting <br> Quality Control 2','group_name'=>'Quality Control 2'],
            ['formatted_name'=>'In <br> Loading Station','group_name'=>'Loading Station'],
            ['formatted_name'=>'On Assembly <br> Conveyor','group_name'=>'Conveyor'],
            ['formatted_name'=>'On Storage <br> Conveyor','group_name'=>'Storage'],
            ['formatted_name'=>'In <br> shelving','group_name'=>'shelving'],
        );
        return $formatted_postes;
    }    
}
