<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Routing\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用に「どのタグを更新しているか」をリクエストにセットするヘルパー
     */
    private function setupRequestWithTag($tag)
    {
        $request = new UpdateTagRequest();

        $route = new Route('PUT', '/admin/tags/{tag}', []);
        $route->bind($request); // ルートをバインド状態にする
        $route->setParameter('tag', $tag);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        return $request;
    }

    /**
     * 正常系：自身の名前ををのまま維持して更新できるか
     */

    public function test_update_tag_request_passes_with_same_name(): void
    {
        // 事前にタグを作成
        $tag = Tag::create(['name' => '既存タグ']);

        // そのタグを更新しようとしているリクエストを作成
        $request = $this->setupRequestWithTag($tag);

        $validator = Validator::make([
            'name' => '既存タグ',
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * 異常系：他で既に使用されているタグ名への変更を弾くか
     */

    public function test_update_tag_request_fails_with_duplicate_name(): void
    {
        // 事前にタグを作成
        $tag1 = Tag::create(['name' => 'タグA']);
        $tag2 = Tag::create(['name' => 'タグB']);

        $request = $this->setupRequestWithTag($tag1);

        $validator = Validator::make([
            'name' => 'タグB',
        ], $request->rules());

        $this->assertFalse($validator->passes());
    }
}
