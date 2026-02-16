<?php
namespace App\Models;

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