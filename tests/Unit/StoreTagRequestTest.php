<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常系：正しいタグ名を受け付けるか
     */
    public function test_store_tag_request_passes(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make([
            'name' => '新規タグ',
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * 異常系：重複したタグ名を弾くか
     */
    public function test_store_tag_request_fails_with_duplicate_name(): void
    {
        // 事前にタグを作成しておく
        Tag::create(['name' => '既存タグ']);

        $request = new StoreTagRequest();

        $validator = Validator::make([
            'name' => '既存タグ', // 重複する名前を渡す
        ], $request->rules());

        $this->assertFalse($validator->passes());
    }

    /**
     * 異常系：未入力（空）を弾くか
     */
    public function test_store_tag_request_fails_with_empty_name(): void
    {
        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => ''], $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * 異常系：50文字を超える入力を弾くか
     */
    public function test_store_tag_request_fails_with_long_name(): void
    {
        $request = new StoreTagRequest();
        $validator = Validator::make(['name' => str_repeat('あ', 51)], $request->rules());

        $this->assertFalse($validator->passes());
    }
}
