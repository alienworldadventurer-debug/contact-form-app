<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;


class ExportContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証ユーザーはログイン画面にリダイレクトされる
     */
    public function test_guest_cannot_export_csv(): void
    {
        $response = $this->get('/contacts/export');
        $response->assertRedirect('/login');
    }

    /**
     * 正常系：管理者はCSVをダウンロードできる
     */
    public function test_admin_can_export_csv_without_filters(): void
    {
        $user = User::factory()->create();

        // ダミーデータを作成
        $category = Category::create(['content' => 'テストカテゴリ']);
        Contact::create([
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'エクスポートテスト',
        ]);

        $response = $this->actingAs($user)->get('/contacts/export');
        $response->assertStatus(200);

        // Laravelが自動で付与するヘッダーに合わせて半角スペースをいれます
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertDownload('contacts.csv');
    }


    /**
     * 異常系：不正な性別を拒否する
     */
    public function test_export_rejects_invalid_gender(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/contacts/export?gender=99');

        $response->assertSessionHasErrors('gender');
    }

    /**
     * 異常系：存在しないカテゴリIDを拒否する
     */
    public function test_export_rejects_invalid_category_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/contacts/export?category_id=999');

        $response->assertSessionHasErrors('category_id');
    }
}
