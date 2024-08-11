<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('oauth_clients')->insert([
            'id'                     => 2,
            'name'                   => 'Laravel Password Grant Client',
            'secret'                 => 'xJaJ3xfq2H1yvZkaCwR2H3XTwBrj37r8bxITj6Il',
            'provider'               => 'users',
            'redirect'               => 'http://localhost',
            'personal_access_client' => 0,
            'revoked'                => 0,
            'password_client'        => 1,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        $this->call(ConfigSeeder::class);
    }
}
