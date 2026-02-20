<?php
namespace App\Models\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $table = 'competencies_hr2';


    protected $fillable = [
        'code', 
        'title', 
        'description', 
        'competency_group'
    ]; 
}