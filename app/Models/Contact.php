<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // 保存を許可するカラム
    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    // Categoryとのリレーション（多対1）
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tagとのリレーション（多対多）
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
