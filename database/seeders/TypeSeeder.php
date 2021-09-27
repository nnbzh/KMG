<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ["slug" => "fact", "name" => "Fact"],
            ["slug" => "forecast", "name" => "Forecast"],
        ];

        foreach ($types as $type) {
            Type::query()->updateOrCreate($type);
        }
    }
}
