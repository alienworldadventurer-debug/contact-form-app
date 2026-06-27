<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        // 20件のデータを作成し、それぞれに1~3件のタグをランダムに紐づける
        Contact::factory(20)->create()->each(function ($contact) use ($tags) {
            $contact->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
