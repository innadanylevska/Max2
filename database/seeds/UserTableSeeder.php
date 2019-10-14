<?php

use App\Organization;
use App\User;
use App\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            'first_name' => 'Inna',
            'last_name' => 'Dan',
            'role' => 'admin',
            'country' => 'USA',
            'city' => 'CA',
            'phone' => '0636445385',
            'email' => 'admin@localhost',
            'password' => Hash::make('123456'),
        ];
        User::create($admin);

        factory(App\User::class, 49)->create(['role' => 'employer'])->each(function ($user) {
        $user->organization()->save(factory(Organization::class)->create());
            
        });
    
    }
}
