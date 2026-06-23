<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category; //追加
use App\Models\Tag; // 追加
use App\Http\Requests\StoreContactRequest; // 追加

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // 画面表示用に選択されたカテゴリとタグのデータを取得
        $category = Category::find($validated['category_id']);

        $tags = collect();
        if (!empty($contact['tag_ids'])) {
            $tags = Tag::find($validated['tag_ids']);
        }

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }
}
