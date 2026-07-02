<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function 管理者はフィルタ条件付きで_cs_vをダウンロードでき無指定時は新着順になる(): void
    {
        // 準備：管理者と、日付の異なるデータを2件作成
        $admin = User::factory()->create();

        $oldContact = Contact::factory()->create([
            'first_name' => '古い太郎',
            'created_at' => '2026-06-01 10:00:00',
        ]);

        $newContact = Contact::factory()->create([
            'first_name' => '新しい花子',
            'created_at' => '2026-06-30 10:00:00',
        ]);

        // 1. 無指定時のテスト（新着順で出力されるか）
        $response = $this->actingAs($admin)->get('/contacts/export');
        $response->assertStatus(200);

        $csv = $response->streamedContent(); // CSVの中身を取得
        $this->assertTrue(strpos($csv, '新しい花子') < strpos($csv, '古い太郎'));

        // 2. フィルタ条件付きのテスト（名前で絞り込み）
        $responseFilterd = $this->actingAs($admin)->get('/contacts/export?keyword=新しい');
        $responseFilterd->assertStatus(200);

        $csvFilterd = $responseFilterd->streamedContent();
        $this->assertStringContainsString('新しい花子', $csvFilterd);
        $this->assertStringNotContainsString('古い太郎', $csvFilterd);
    }
}
