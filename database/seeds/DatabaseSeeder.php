<?php

use Illuminate\Database\Seeder;
//tdickinson@example.net worker
//flatley.lura@example.org employer 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        //Model::unguard();


         $this->call(UserTableSeeder::class);
         $this->call(OrganizationTableSeeder::class);
         $this->call(VacancyTableSeeder::class);
         $this->call(UserNextTableSeeder::class);

       
         //Model::reguard();

    }
}