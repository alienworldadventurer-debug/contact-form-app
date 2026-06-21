<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    // 保存を許可するカラム
    protected $fillable = [
        'name',
    ];

    // Contactとのリレーション（多対多）
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
