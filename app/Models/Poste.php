<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;

    public static function getAssemblyPostes(){
        $formatted_postes=array(
            ['formatted_name'=>'CC','group_name'=>'CustomerCare'],
            ['formatted_name'=>'Partner','group_name'=>'Partner'],
            ['formatted_name'=>'Tailoring','group_name'=>'Tailoring'],
            ['formatted_name'=>'Cleaning','group_name'=>'Spotting'],
            ['formatted_name'=>'Quality Control 1','group_name'=>'Quality Control 1'],
            ['formatted_name'=>'Pressing','group_name'=>'Pressing'],
            ['formatted_name'=>'Quality Control 2','group_name'=>'Quality Control 2'],
            ['formatted_name'=>'Loading Station','group_name'=>'Loading Station'],
            ['formatted_name'=>'Conveyor','group_name'=>'Conveyor'],
            ['formatted_name'=>'Storage','group_name'=>'Storage'],
            ['formatted_name'=>'shelving','group_name'=>'shelving'],
        );

        return $formatted_postes;
    }    
}
