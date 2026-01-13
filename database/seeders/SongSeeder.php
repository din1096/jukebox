<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Faker\Factory as Faker;

class SongSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // pakt alle genre id
        $genreids = DB::table('genres')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            // maakt songtitel aan
            $songTitle = $faker->realText(rand(10, 20)); 
            // maakt random artist naam aan de firstname of firstname and lastname
            $artistName = rand(0, 1) ? $faker->firstName : $faker->firstName . ' ' . $faker->lastName;

            DB::table('songs')->insert([
                'name' => $songTitle,
                'artist' => $artistName,
                'duration' => $faker->numberBetween(180, 600),
                'genre_id' => $faker->randomElement($genreids),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
