<?php

namespace App\Helpers;

use Illuminate\Support\Str;

trait UUID {
    
    protected static function bootUUID() {
        static::creating(function($model){
            if (empty($model->{$model->getKeyName()})){
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getIncrementing(){
        return false;
    }

    //Tells laravel that primary key is String
    public function getKeyType(){
        return 'string';
    }
}