<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用のダミー連絡先を作成するヘルパー
     */
    private function createDummyContact($categoryId, $firstName, $gender)
    {
        return Contact::create([
            'first_name' => $firstName,
            'last_name' => '太郎',
            'gender' => $gender,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => 'テスト住所',
            'category_id' => $categoryId,
            'detail' => 'テストのお問い合わせ内容',
        ]);
    }

    /**
     * 管理画面の一覧で検索とページネーションが機能するか
     */
    public function test_admin_can_search_and_paginate_contacts()
    {
        $user = User::factory()->create();
        $category1 = Category::create(['content' => 'カテゴリ1']);
        $category2 = Category::create(['content' => 'カテゴリ2']);

        // ページネーション（7件）の検証用に、合計10件のデータを作成
        for ($i = 0; $i < 5; $i++) {
            $this->createDummyContact($category1->id, '山田', 1);
            $this->createDummyContact($category2->id, '田中', 2);
        }

        // 検索条件（山田、男性、カテゴリ1）を指定してアクセス
        $response = $this->actingAs($user)->get('/admin?keyword=山田&gender=1&category_id='.$category1->id);

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('田中');

        // 条件なしでアクセスし、7件のみ表示（ページネーション）されているか
        $responseAll = $this->actingAs($user)->get('/admin');
        $responseAll->assertStatus(200);
        $this->assertCount(7, $responseAll->viewData('contacts'));
    }

    /**
     * 詳細ページが正しく表示されるか
     */
    public function test_admin_can_view_contact_detail()
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = $this->createDummyContact($category->id, '山田', 1);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee('テストカテゴリ'); // カテゴリ名が表示されているか
        $response->assertSee('テストのお問い合わせ内容');
    }

    /**
     * お問い合わせデータが削除できるか
     */
    public function test_admin_can_delete_contact()
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = $this->createDummyContact($category->id, '山田', 1);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
