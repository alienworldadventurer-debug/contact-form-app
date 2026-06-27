<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;


class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常系：正しい保存データを受け付けるか
     */
    public function test_store_contact_request_passes(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => 'テストタグ']);

        $request = new StoreContactRequest();

        // 正常なデータ
        $validator = Validator::make([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678', // 10~11桁の数字のみ
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ], $request->rules());


        $this->assertTrue($validator->passes());
    }

    /**
     * 異常系：不正な電話番号（ハイフンあり等）を弾くか
     */
    public function test_store_contact_request_fails_with_invalid_tel()
    {
        $request = new StoreContactRequest();

        $validator = Validator::make([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '090-1234-5678', // ハイフンありはNG
            'address' => '東京都渋谷区',
            'category_id' => 1,
            'detail' => 'テスト内容',
            'tag_ids' => [2],
        ], $request->rules());


        $this->assertFalse($validator->passes());
    }
}
