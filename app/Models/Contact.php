<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    // キーワード検索スコープ
    public function scopeKeywordSearch($query, $keyword)
    {
        if (! empty($keyword)) {
            $searchWord = str_replace([' ', '　'], '', $keyword);
            $query->where(function ($q) use ($searchWord, $keyword) {
                $q->where(DB::raw('CONCAT(first_name,last_name)'), 'like', '%'.$searchWord.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%');
            });
        }

        return $query;
    }

    // 性別検索スコープ
    public function scopeGenderSearch($query, $gender)
    {
        if (! empty($gender) && $gender != 0) {
            $query->where('gender', $gender);
        }

        return $query;
    }

    //　カテゴリ検索スコープ
    public function scopeCategorySearch($query, $categoryId)
    {
        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        return $query;
    }

    // 日付検索スコープ
    public function scopeDateSearch($query, $date)
    {
        if (! empty($date)) {
            $query->whereDate('created_at', $date);
        }

        return $query;
    }
}
