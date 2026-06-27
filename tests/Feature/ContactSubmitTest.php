<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常系：確認画面が正しく表示されるか
     */
    public function test_contact_confirm_page_is_displayed_with_valid_data(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => 'テストタグ']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('山田'); // 入力内容が画面に表示されているか
    }

    /**
     * 異常系：確認画面への不正な送信が弾かれるか
     */
    public function test_contact_confirm_redirects_with_invalid_data(): void
    {
        $response = $this->post('/contacts/confirm', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name', 'email']);
    }

    /**
     * 正常系：データが保存され、タグが紐づき、サンクスページへリダイレクトされるか
     */
    public function test_contact_is_stored_and_redirects_to_thanks_page(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => 'テストタグ']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts', $data);

        // サンクスページへのリダイレクト確認
        $response->assertRedirect('/thanks');

        // データベースに保存されているか確認
        $this->assertDatabaseHas('contacts', [
            'email' => 'test@example.com',
        ]);

        // タグが紐づいているか確認
        $contact = Contact::first();
        $this->assertTrue($contact->tags->contains($tag));
    }

    /**
     * 異常系：保存への不正な送信が弾かれるか
     */
    public function test_contact_store_redirects_with_invalid_data(): void
    {
        $response = $this->post('/contacts', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name', 'email']);
    }
}
