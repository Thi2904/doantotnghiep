<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table("users")->insert([
            "username" => "thilc123",
            "name" => "Hoang Dinh Thi",
            "email" => "kurobakarma@gmail.com",
            "street_address" => "Lao Cai",
            "phone" => "1951941056",
            "password" => Hash::make("Thideptrai@12"),
            "role" => "customer"
        ]);

        DB::table("users")->insert([
            "username" => "teolc123",
            "name" => "Hoang Dinh Teo",
            "email" => "ad@gmail.com",
            "street_address" => "TQB",
            "phone" => "0709292929",
            "password" => Hash::make("123456789"),
            "role" => "admin"
        ]);


        DB::table("sizes")->insert([
            "sizeName" => "S",
        ]);
        DB::table("sizes")->insert([
            "sizeName" => "M",
        ]);
        DB::table("sizes")->insert([
            "sizeName" => "L",
        ]);
        DB::table("sizes")->insert([
            "sizeName" => "XL",
        ]);
        DB::table("sizes")->insert([
            "sizeName" => "XXL",
        ]);
        DB::table("sizes")->insert([
            "sizeName" => "XXXL",
        ]);


        DB::table("colors")->insert([
            "colorName" => "Red",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Blue",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Pink",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Purple",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Black",
        ]);
        DB::table("colors")->insert([
            "colorName" => "White",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Green",
        ]);
        DB::table("colors")->insert([
            "colorName" => "Orange",
        ]);


    }
}
