<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function カテゴリは複数のお問い合わせを持つことができる(): void
    {
        // 準備: カテゴリ１つと、それに紐づくお問い合わせ１つ作成
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // 検証：カテゴリから $category->contacts でデータが正しく取得できるか
        $this->assertTrue($category->contacts->contains($contact));
        $this->assertEquals(1, $category->contacts->count());
    }
}
