<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function タグ１つとお問い合わせ１つを作成し、中間テーブルで紐づける(): void
    {
        // 準備:　タグ１つとお問い合わせ１つを作成、中間テーブルで紐づける
        $tag = Tag::factory()->create();
        $contact = Contact::factory()->create();

        $tag->contacts()->attach($contact->id);

        // 検証：タグから紐づいたお問い合わせが正しく取得できるか
        $this->assertTrue($tag->contacts->contains($contact));
        $this->assertEquals(1, $tag->contacts->count());
    }
}
