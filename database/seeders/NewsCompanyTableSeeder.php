<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsCompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $companies = [
            ['name' =>'Jawlah', 'url'=>"https://jawlah.co/" , 'news_url' =>'https://jawlah.co/']
        ];
        foreach ($companies as $company) {
            \App\Models\NewsCompany::create($company);
        }
    }
}
