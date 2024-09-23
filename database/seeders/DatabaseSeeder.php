<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $koord = array([107.63534, -6.89608], // A
        [107.6355, -6.89703], // B
        [107.63548, -6.89793], // C
        [107.63593, -6.89803], // D
        [107.63699, -6.89832], // E
        [107.63751, -6.89755], // F
        [107.63772, -6.89675], // G
        [107.63757, -6.89642], // H
        [107.63689, -6.89628], // I
        [107.636, -6.89611]);
        foreach ($koord as $key=>$value) {
            DB::table('benchmark')->insert(['koord_id'=>'BM'.$key,'longitude'=>$value[0],'latitude'=>$value[1]]);
        };
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
