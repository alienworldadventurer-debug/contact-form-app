<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Tag;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * お問い合わせフォーム入力ページが正常に表示され、データが渡されているか
     */
    public function test_contact_page_is_displayed_with_categories_and_tags(): void
    {
        // テスト用のカテゴリとタグを作成
        Category::create(['content' => 'テストカテゴリ']);
        Tag::create(['name' => 'テストタグ']);

        // トップページ(/)にアクセス
        $response = $this->get('/');

        // 正常に表示されるか（200 Ok）
        $response->assertStatus(200);

        // ビューにカテゴリとタグの変数が渡されているか
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        // 画面上に作成したカテゴリ名とタグ名が表示されているか
        $response->assertSee('テストカテゴリ');
        $response->assertSee('テストタグ');
    }

    /**
     * サンクスページ(/thanks)が正常に表示されるか
     */

    public function test_thanks_page_is_displayed(): void
    {
        $response = $this->get('/thanks');

        // 正常に表示されるか（200 Ok）
        $response->assertStatus(200);
    }
}
