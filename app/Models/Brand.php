<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $date = ['deleted_at'];

    public function categories(){

        return $this->belongsToMany(Category::class , 'category_brands')->where('parent_id','!=',null);

    }

}
