<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証済みのユーザーは管理画面(/admin)を表示できるか
     */
    public function test_authenticated_user_can_access_admin_page(): void
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // ユーザーとしてログインした状態で/admin にアクセス
        $response = $this->actingAs($user)->get('/admin');

        // 正常に表示されるか (200 OK)
        $response->assertStatus(200);
    }

    /**
     * 未認証のユーザーはログイン画面（/login）にリダイレクトされるか
     */
    public function test_guest_user_is_redirected_to_login(): void
    {
        // ログインせずに /admin にアクセス
        $response = $this->get('/admin');

        // /login にリダイレクトされるか
        $response->assertRedirect('/login');
    }
}
