<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->truncate();

        DB::table('positions')->insert([
            // divisi plywood
            ['id' => 1, 'name' => 'Wood Working', 'type' => 'Divisi', 'parent' => null],
            // department plywood
            ['id' => 2, 'name' => 'GA', 'type' => 'Departemen', 'parent' => 1],
            ['id' => 3, 'name' => 'Maintenance', 'type' => 'Departemen', 'parent' => 1],
            ['id' => 4, 'name' => 'Production', 'type' => 'Departemen', 'parent' => 1],
            ['id' => 5, 'name' => 'Sawmill', 'type' => 'Departemen', 'parent' => 1],
            ['id' => 6, 'name' => 'Warehouse', 'type' => 'Departemen', 'parent' => 1],
            // bagian GA
            ['id' => 7, 'name' => 'Soper', 'type' => 'Bagian', 'parent' => 2],
            ['id' => 8, 'name' => 'Soper Forklift', 'type' => 'Bagian', 'parent' => 2],
            ['id' => 9, 'name' => 'Kernet', 'type' => 'Bagian', 'parent' => 2],
            ['id' => 10, 'name' => 'Kebersihan', 'type' => 'Bagian', 'parent' => 2],
            // bagian maintenance
            ['id' => 11, 'name' => 'Teknisi Listrik', 'type' => 'Bagian', 'parent' => 3],
            // bagian production
            ['id' => 12, 'name' => 'Cross Cut', 'type' => 'Bagian', 'parent' => 4],
            ['id' => 13, 'name' => 'Finger Joint', 'type' => 'Bagian', 'parent' => 4],
            ['id' => 14, 'name' => 'Moulding', 'type' => 'Bagian', 'parent' => 4],
            ['id' => 15, 'name' => 'Packing', 'type' => 'Bagian', 'parent' => 4],
            // bagian Sawmil
            ['id' => 16, 'name' => 'Bandsaw', 'type' => 'Bagian', 'parent' => 5],
            // bagian Warehouse
            ['id' => 17, 'name' => 'Kiln Dry', 'type' => 'Bagian', 'parent' => 6],
            ['id' => 18, 'name' => 'Stick', 'type' => 'Bagian', 'parent' => 6],
        ]);
    }
}
