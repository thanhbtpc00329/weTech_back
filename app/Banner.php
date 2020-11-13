<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $primaryKey="id";
    protected $table="banners";
    protected $fillable=['image','type','status'];
    public $timestamps=false;
}
