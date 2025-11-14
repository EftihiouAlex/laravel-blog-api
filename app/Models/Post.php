<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'post_content',
        'slug',
    ];


    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags() {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'category_post')->withTimestamps();
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
