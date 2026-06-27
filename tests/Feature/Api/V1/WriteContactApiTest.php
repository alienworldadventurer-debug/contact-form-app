<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriteContactApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * お問い合わせを作成できること（タグ紐づけ含む）
     */
    public function test_can_create_contact(): void
    {
        $this->withoutExceptionHandling();  // エラー特定のため

        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => 'テストタグ']);

        $data = [
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'API作成テスト',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->postJson('/api/v1/contacts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', 'テスト');

        // DBに保存されたか確認
        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('contact_tag', ['tag_id' => $tag->id]);
    }

    /**
     * 作成時のバリデーションエラー（必須項目不足で422が返ること）
     */
    public function test_create_validation_error(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'email']);
    }

    /**
     * お問い合わせを更新できること（タグ同期含む）
     */
    public function test_can_update_contact(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = Contact::create([
            'first_name' => '古い名前',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'old@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '古い内容',
        ]);
        $tag = Tag::create(['name' => '新しいタグ']);

        $data = [
            'first_name' => '新しい名前',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'new@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '新しい内容',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', '新しい名前');

        // DBに保存されたか確認
        $this->assertDatabaseHas('contacts', ['email' => 'new@example.com']);
        $this->assertDatabaseHas('contact_tag', ['contact_id' => $contact->id, 'tag_id' => $tag->id]);
    }

    /**
     * 更新時のバリデーションエラー（電話番号形式エラーで422が返ること）
     */
    public function test_update_validation_error(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = Contact::create([
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '内容',
        ]);

        $data = [
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '090-1234',  // 不正な電話番号
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '内容',
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tel']);
    }

    /**
     * お問い合わせを削除できること
     */
    public function test_can_delete_contact(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = Contact::create([
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '削除対象',
        ]);

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    /**
     * 存在しないIDの削除で404が返ること
     */
    public function test_delete_returns_404_for_non_existent_contact(): void
    {
        $response = $this->deleteJson('/api/v1/contacts/999');

        $response->assertStatus(404);
    }
}
