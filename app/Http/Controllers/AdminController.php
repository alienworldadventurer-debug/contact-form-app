<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Http\Requests\IndexContactRequest;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        // スコープを使って検索処理を１つに繋げる
        $contacts = Contact::with(['category', 'tags'])
            ->keywordSearch($request->keyword)
            ->genderSearch($request->gender)
            ->categorySearch($request->category_id)
            ->dateSearch($request->date)
            ->paginate(7);

        // 検索フォームのセレクトボックス用にカテゴリー一覧も取得
        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    }
}
