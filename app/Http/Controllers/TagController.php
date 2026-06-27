<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    // タグの新規追加
    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());

        return redirect('/admin');
    }

    // タグ編集画面の表示
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // タグの更新
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect('/admin');
    }

    // タグの削除
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin');
    }
}
