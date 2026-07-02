<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function 管理画面で日付検索ができる(): void
    {
        // 準備：管理者ユーザーと、日付の異なるお問い合わせデータを作成
        $admin = User::factory()->create();

        $targetContact = Contact::factory()->create([
            'first_name' => '検索対象',
            'created_at' => '2026-06-30 10:00:00',
        ]);
        $otherContact = Contact::factory()->create([
            'first_name' => '対象外',
            'created_at' => '2026-06-29 10:00:00',
        ]);

        // 実行：管理者としてログインし、日付パラメータをつけて一覧画面にアクセス
        $response = $this->actingAs($admin)->get('/admin?date=2026-06-30');

        // 検証：検索対象の名前だけが画面に表示されているか確認
        $response->assertStatus(200);
        $response->assertSee('検索対象');
        $response->assertDontSee('対象外');

    }
}
