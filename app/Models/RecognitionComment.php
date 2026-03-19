<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionComment extends Model
{
    use HasFactory;

    protected $table = 'recognition_comments_hr1';

    protected $fillable = [
        'post_id',
        'user_id',
        'comment_text'
    ];

    public function post()
    {
        return $this->belongsTo(RecognitionPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
