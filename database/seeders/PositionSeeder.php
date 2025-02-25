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
            // divisi ww
            ['id' => 1, 'name' => 'Wood Working', 'type' => 'Divisi', 'parent_id' => null],
            // department ww
            ['id' => 2, 'name' => 'GA', 'type' => 'Departemen', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Maintenance', 'type' => 'Departemen', 'parent_id' => 1],
            ['id' => 4, 'name' => 'Production', 'type' => 'Departemen', 'parent_id' => 1],
            ['id' => 5, 'name' => 'Sawmill', 'type' => 'Departemen', 'parent_id' => 1],
            ['id' => 6, 'name' => 'Warehouse', 'type' => 'Departemen', 'parent_id' => 1],
            // bagian GA
            ['id' => 7, 'name' => 'Soper', 'type' => 'Bagian', 'parent_id' => 2],
            ['id' => 8, 'name' => 'Soper Forklift', 'type' => 'Bagian', 'parent_id' => 2],
            ['id' => 9, 'name' => 'Kernet', 'type' => 'Bagian', 'parent_id' => 2],
            ['id' => 10, 'name' => 'Kebersihan', 'type' => 'Bagian', 'parent_id' => 2],
            // bagian maintenance
            ['id' => 11, 'name' => 'Teknisi Listrik', 'type' => 'Bagian', 'parent_id' => 3],
            // bagian production
            ['id' => 12, 'name' => 'Cross Cut', 'type' => 'Bagian', 'parent_id' => 4],
            ['id' => 13, 'name' => 'Finger Joint', 'type' => 'Bagian', 'parent_id' => 4],
            ['id' => 14, 'name' => 'Moulding', 'type' => 'Bagian', 'parent_id' => 4],
            ['id' => 15, 'name' => 'Packing', 'type' => 'Bagian', 'parent_id' => 4],
            // bagian Sawmil
            ['id' => 16, 'name' => 'Bandsaw', 'type' => 'Bagian', 'parent_id' => 5],
            // bagian Warehouse
            ['id' => 17, 'name' => 'Kiln Dry', 'type' => 'Bagian', 'parent_id' => 6],
            ['id' => 18, 'name' => 'Stick', 'type' => 'Bagian', 'parent_id' => 6],

            // divis plywood
            ['id' => 19, 'name' => 'Plywood', 'type' => 'Divis', 'parent_id' => null],
            // department ply
            ['id' => 20, 'name' => 'Koordinator', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 21, 'name' => 'Rotary 5', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 22, 'name' => 'Rotary 9', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 23, 'name' => 'Repair OPC/PPC', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 24, 'name' => 'Repair Core', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 25, 'name' => 'Assembling 1', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 26, 'name' => 'Assembling 2', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 27, 'name' => 'Assembling 3', 'type' => 'Departemen', 'parent_id' => 19],
            ['id' => 28, 'name' => 'Warehouse', 'type' => 'Departemen', 'parent_id' => 19],
            
        ]);
    }
}
