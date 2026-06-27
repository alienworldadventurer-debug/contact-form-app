<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadContactApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * お問い合わせ一覧を取得できること（ページネーションのメタデータを含む）
     */
    public function test_can_get_contacts_list(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);

        Contact::create([
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'API一覧テスト',
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /**
     * 一覧取得時のバリデーションエラー（不正な性別で422が返ること）
     */
    public function test_contacts_list_validation_error(): void
    {
        $response = $this->getJson('/api/v1/contacts?gender=99');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gender']);
    }

    /**
     * お問い合わせ詳細を取得できること
     */
    public function test_can_get_contact_detail(): void
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
            'detail' => 'API一覧テスト',
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $contact->id);
    }

    /**
     * 存在しないIDの詳細取得で404が返ること
     */
    public function test_returns_404_for_non_existent_contact(): void
    {
        $response = $this->getJson('/api/v1/contacts/999');

        $response->assertStatus(404);
    }
}
