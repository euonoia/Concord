<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionPost extends Model
{
    use HasFactory;

    protected $table = 'recognition_posts_hr1';

    protected $fillable = [
        'admin_id',
        'employee_id',
        'title',
        'content',
        'image_path',
        'likes_count',
        'comments_count'
    ];

    public function likes()
    {
        return $this->hasMany(RecognitionLike::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(RecognitionComment::class, 'post_id')->orderBy('created_at', 'desc');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
