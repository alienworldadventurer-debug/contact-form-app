<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Tag;


class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証ユーザーがタグ操作を拒否され、ログイン画面へリダイレクトされるか
     */
    public function test_quest_user_cannot_manage_tags(): void
    {
        $tag = Tag::create(['name' => 'テストタグ']);

        $this->get("/admin/tags/{$tag->id}/edit")->assertRedirect('/login');
        $this->post('/admin/tags', ['name' => '新規タグ'])->assertRedirect('/login');
        $this->put("/admin/tags/{$tag->id}", ['name' => '更新タグ'])->assertRedirect('/login');
        $this->delete("/admin/tags/{$tag->id}")->assertRedirect('/login');
    }

    /**
     * 認証ユーザーがタグを登録できるか
     */
    public function test_authenticated_user_can_store_tag(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/tags', [
            'name' => '新規タグ',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);
    }

    /**
     * 認証ユーザーがタグ編集画面を表示できるか
     */
    public function test_authenticated_user_can_view_edit_tag_page(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create(['name' => '既存タグ']);

        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('既存タグ');
    }

    /**
     * 認証ユーザーがタグを更新できるか
     */
    public function test_authenticated_user_can_update_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create(['name' => '古いタグ']);

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", [
            'name' => '新しいタグ',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新しいタグ']);
    }

    /**
     * 認証ユーザーがタグを削除できるか
     */
    public function test_authenticated_user_can_delete_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create(['name' => '削除対象タグ']);

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
