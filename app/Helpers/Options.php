<?php
namespace App\Helpers;
use App\Models\Option;

class Options {

    public static function refresh(){
        $defaults = config('options.defaults');

        foreach($defaults as $d=>$v){

            $opt = Option::where('varname', $d)->first();
            if($opt){

            }else{
                $opt = new Option();
                $opt->varname = $d;
                foreach ($v as $k=>$v){
                    $opt->$k = $v;
                }
                $opt->save();
            }

        }
    }

    public static function set($varname, $value){
        $opt = Option::where('varname', $varname)->first();
        if($opt){
            $option->value = $value;
            $option->save();
            return $value;
        }else{
            return '';//return empty string
        }
    }

    public static function get($varname, $defvalue = null){
        $opt = Option::where('varname', $varname)->first();
        if($opt){
            return $opt->value;
        }else{
            return ( isset($opt->value) && is_null($opt->value))?'':$defvalue;//return specified default value
        }
    }

}
