<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;
    protected $fillable = ['priority'];

    public function project()
    {
        return $this->hasOne(Projects::class,'id','project_id');
    }

}
