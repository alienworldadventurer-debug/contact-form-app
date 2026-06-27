<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    /**
     * お問い合わせ一覧取得API
     */
    public function index(IndexContactRequest $request)
    {
        // 関連データ(category,tags)を一緒に取得してN+1問題を回避
        $query = Contact::with(['category', 'tags'])->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // per_pageパラメータがなければデフォルトｄ20件とする
        $perPage = $request->input('per_page', 20);
        $contacts = $query->paginate($perPage);

        // API Resourceを使ってJSON形式に変換
        return ContactResource::collection($contacts);
    }

    /**
     * お問い合わせ詳細取得API
     */
    public function show(Contact $contact)
    {
        // 関連データをロード
        $contact->load(['category', 'tags']);

        // API Resourceを使ってJSON形式に変換
        return new ContactResource($contact);
    }
}
