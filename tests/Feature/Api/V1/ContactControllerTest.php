<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function ap_iで条件を指定してお問い合わせい一覧を習得しページネーションできる(): void
    {
        // 準備：カテゴリと検索用・対象外のデータを作成
        $category = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '検索太郎',
            'gender' => 1,
            'category_id' => $category->id,
            'created_at' => '2026-06-30 10:00:00',
        ]);

        Contact::factory()->create([
            'first_name' => '対象外花子',
            'gender' => 2,
        ]);

        // 実行：検索条件とページネーションを指定してAPIにGETリクエスト
        $response = $this->getJson('/api/v1/contacts?keyword=検索太郎&gender=1&category_id='.$category->id.'&date=2026-06-30&per_page=10&page=1');

        // 検証：検索対象のみ取得でき、ページネーション情報が含まれているか
        $response->assertStatus(200);
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonFragment(['first_name' => '検索太郎']);
        $response->assertJsonMissing(['first_name' => '対象外花子']);
    }

    /** @test **/
    public function ap_iの検索で不正な値を送信すると422エラーになる(): void
    {
        // 実行：不正な性別(9)を指定してリクエスト
        $response = $this->getJson('/api/v1/contacts?gender=9');

        // 検証：バリデーションエラー(422)が返るか
        $response->assertStatus(422);
    }

    /** @test **/
    public function ap_iの更新で存在しない_i_dを指定すると404エラーになる(): void
    {
        // 準備：バリデーションを通る正しい更新データを用意
        $category = Category::factory()->create();
        $updateData = [
            'first_name' => '更新',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345768',
            'address' => '東京都渋谷区',
            'category_id' => $category->id,
            'detail' => 'テスト内容',
        ];

        // 実行：存在しないID(9999)を指定してPUTリクエスト
        $response = $this->putJson('/api/v1/contacts/9999', $updateData);

        // 検証：404エラー(Not Found)がかえるか
        $response->assertStatus(404);
    }
}
