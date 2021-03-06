<?php

use Illuminate\Database\Seeder;
use App\Model\Exigency;

class ExigencySeeder extends Seeder {

    public function run()
    {
        DB::table('exigency')->truncate();

        $exigencies = [
            ['name' => 'Đang tìm việc'],
            ['name' => 'Chưa sẵn sàng']
        ];

        Exigency::insert($exigencies);
    }

}