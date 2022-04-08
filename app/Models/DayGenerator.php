<?php
/**
 * Created by PhpStorm.
 * User: reyewat
 * Date: 9/24/2021
 * Time: 10:49 AM
 */

namespace App\Models;


class DayGenerator
{

    private $days=array();

    private $current='';

    public function __construct($days)
    {
        $this->days=$days;

    }

    public function next(){


        if($this->current!='') {


            foreach ($this->days as $k => $dayname) {

                    if($dayname==$this->current){

                        if(isset($this->days[($k+1)])){

                            $this->current=$this->days[($k+1)];

                            break;
                        }else{
                            $this->current=$this->days[0];
                            break;
                        }
                    }
            }
        }
        $obj=new \stdClass();
        if($this->current=='') {
            $this->current = $this->days[0];
            $obj->return_to_start=false;
        }else{
            $obj->return_to_start=$this->days[0]==$this->current;
        }

        $obj->dayname= $this->current;

        return $obj;
    }

}
