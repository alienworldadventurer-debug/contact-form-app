<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常系：正しい検索条件を受け付けるか
     */
    public function test_index_contact_request_passes()
    {
        // 準備：データベースにカテゴリを1つ作成
        $category = Category::factory()->create();

        $request = new IndexContactRequest;

        // 検索条件の正常なデータ
        $validator = Validator::make([
            'keyword' => 'テスト',
            'gender' => 1, // 0,1,2,3　はOK
            'category_id' => $category->id,
            'date' => '2026-01-01',
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * 異常系：不正な性別（gender）を弾くか
     */
    public function test_index_contact_request_fails_with_invalid_gender()
    {
        $request = new IndexContactRequest;

        // 存在しない性別（例：4）
        $validator = Validator::make([
            'gender' => 4,
        ], $request->rules());

        $this->assertFalse($validator->passes());
    }
}
