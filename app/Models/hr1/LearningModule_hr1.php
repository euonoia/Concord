<?php

namespace App\Models\hr1;

use App\Models\hr1\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule_hr1 extends Model
{
    use HasFactory;

    protected $table = 'learning_modules_hr1';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_learning_modules_hr1', 'learning_module_id', 'user_id')
            ->withPivot('completed')
            ->withTimestamps();
    }
}

