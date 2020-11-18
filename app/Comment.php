<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey="id";
    protected $table="comments";
    protected $fillable=['user_id','content','product_id','rating','status','is_reply'];
    public $timestamps=false;
}
