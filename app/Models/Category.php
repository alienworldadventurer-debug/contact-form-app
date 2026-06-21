<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // 保存を許可するカラム（テーブル仕様書通り content のみ）
    protected $fillable = [
        'content',
    ];

    // Contactとのリレーション（1体多）
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
