<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MethodSeeder extends Seeder
{
    public function run(): void{
        DB::table("payments")->insert([
            "payMethod" => "Thanh toán khi nhận hàng (COD)",
        ]);
        DB::table("payments")->insert([
            "payMethod" => "QR Pay",
        ]);
        DB::table("payments")->insert([
            "payMethod" => "Ví MoMo",
        ]);
    }
}
