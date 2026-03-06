<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [

            // ASET LANCAR
            ['code' => '100.00.00', 'name' => 'Aset Lancar', 'parent' => null],
            ['code' => '101.00.00', 'name' => 'Kas dan Setara Kas', 'parent' => '100.00.00'],
            ['code' => '101.00.01', 'name' => 'Kas Tunai', 'parent' => '101.00.00'],
            ['code' => '101.00.02', 'name' => 'BCA', 'parent' => '101.00.00'],
            ['code' => '101.00.03', 'name' => 'Tabungan Koperasi', 'parent' => '101.00.00'],
            ['code' => '101.00.04', 'name' => 'Kas Rumah', 'parent' => '101.00.00'],

            ['code' => '102.00.00', 'name' => 'Piutang Pegawai', 'parent' => '100.00.00'],

            ['code' => '103.00.00', 'name' => 'Persediaan', 'parent' => '100.00.00'],
            ['code' => '103.00.01', 'name' => 'Persediaan Etalase', 'parent' => '103.00.00'],
            ['code' => '103.00.02', 'name' => 'Persediaan Sepuh', 'parent' => '103.00.00'],

            ['code' => '104.00.00', 'name' => 'Uang Muka Pajak', 'parent' => '100.00.00'],
            ['code' => '104.00.01', 'name' => 'Uang Muka', 'parent' => '104.00.00'],

            ['code' => '105.00.00', 'name' => 'Biaya Dibayar Dimuka', 'parent' => '100.00.00'],

            // ASET TIDAK LANCAR
            ['code' => '200.00.00', 'name' => 'Aset Tidak Lancar', 'parent' => null],
            ['code' => '201.01.01', 'name' => 'Kendaraan', 'parent' => '200.00.00'],
            ['code' => '201.01.02', 'name' => 'Peralatan', 'parent' => '200.00.00'],
            ['code' => '201.02.01', 'name' => 'Akl Kendaraan', 'parent' => '200.00.00'],
            ['code' => '201.02.02', 'name' => 'Akl Peralatan', 'parent' => '200.00.00'],

            ['code' => '202.00.00', 'name' => 'Piutang Lain-lain', 'parent' => '200.00.00'],

            // KEWAJIBAN
            ['code' => '300.00.00', 'name' => 'Kewajiban', 'parent' => null],
            ['code' => '301.00.00', 'name' => 'Utang Usaha', 'parent' => '300.00.00'],
            ['code' => '302.00.00', 'name' => 'Uang Muka Masuk', 'parent' => '300.00.00'],
            ['code' => '303.00.00', 'name' => 'Pendapatan Diterima Dimuka', 'parent' => '300.00.00'],
            ['code' => '304.00.00', 'name' => 'Utang Pajak', 'parent' => '300.00.00'],
            ['code' => '304.00.01', 'name' => 'Utang PPh 4 Ayat 2', 'parent' => '304.00.00'],
            ['code' => '305.00.00', 'name' => 'Utang Pemegang Saham', 'parent' => '300.00.00'],
            ['code' => '306.00.00', 'name' => 'Utang Bank', 'parent' => '300.00.00'],

            // MODAL
            ['code' => '400.00.00', 'name' => 'Modal', 'parent' => null],
            ['code' => '401.00.00', 'name' => 'Modal Saham', 'parent' => '400.00.00'],
            ['code' => '402.00.00', 'name' => 'Laba Tahun Berjalan', 'parent' => '400.00.00'],
            ['code' => '403.00.00', 'name' => 'Saldo Laba', 'parent' => '400.00.00'],
            ['code' => '404.00.00', 'name' => 'Prive', 'parent' => '400.00.00'],

            // LABA / RUGI
            ['code' => '500.00.00', 'name' => 'Laba / Rugi', 'parent' => null],
            ['code' => '501.00.00', 'name' => 'Penjualan', 'parent' => '500.00.00'],
            ['code' => '501.00.01', 'name' => 'Penjualan Paserpan', 'parent' => '501.00.00'],
            ['code' => '501.00.02', 'name' => 'Penjualan Pasuruan', 'parent' => '501.00.00'],
            ['code' => '501.00.03', 'name' => 'Penjualan SA', 'parent' => '501.00.00'],
            ['code' => '501.00.04', 'name' => 'Keuntungan Kenaikan Persediaan', 'parent' => '501.00.00'],
            ['code' => '501.00.05', 'name' => 'Penjualan Online', 'parent' => '501.00.00'],

            ['code' => '502.00.00', 'name' => 'Beban Langsung', 'parent' => '500.00.00'],
            ['code' => '502.01.00', 'name' => 'HPP', 'parent' => '502.00.00'],
            ['code' => '502.01.01', 'name' => 'HPP Paserpan', 'parent' => '502.01.00'],
            ['code' => '502.01.02', 'name' => 'HPP Pasuruan', 'parent' => '502.01.00'],
            ['code' => '502.01.03', 'name' => 'HPP SA', 'parent' => '502.01.00'],

            ['code' => '502.02.00', 'name' => 'Beban Gaji', 'parent' => '502.00.00'],

            ['code' => '502.03.00', 'name' => 'Beban Operasional', 'parent' => '502.00.00'],
            ['code' => '502.03.01', 'name' => 'Beban Operasional Paserpan', 'parent' => '502.03.00'],
            ['code' => '502.03.02', 'name' => 'Beban Operasional Pasuruan', 'parent' => '502.03.00'],
            ['code' => '502.03.03', 'name' => 'Beban Operasional SA', 'parent' => '502.03.00'],

            ['code' => '502.04.00', 'name' => 'Beban Operasional Lain', 'parent' => '502.00.00'],
            ['code' => '502.05.00', 'name' => 'Beban Utilitas', 'parent' => '502.00.00'],
            ['code' => '502.06.00', 'name' => 'Beban Patri', 'parent' => '502.00.00'],
            ['code' => '502.07.00', 'name' => 'Beban Sepuh', 'parent' => '502.00.00'],

            ['code' => '503.00.00', 'name' => 'Beban Administrasi Umum', 'parent' => '500.00.00'],
            ['code' => '503.01.00', 'name' => 'Beban Sewa', 'parent' => '503.00.00'],
            ['code' => '503.04.00', 'name' => 'Beban Perizinan', 'parent' => '503.00.00'],
            ['code' => '503.05.00', 'name' => 'Beban Gaji Administrasi Umum', 'parent' => '503.00.00'],
            ['code' => '503.06.00', 'name' => 'Beban Konsumsi dan Umum', 'parent' => '503.00.00'],
            ['code' => '503.07.00', 'name' => 'Beban Sumbangan', 'parent' => '503.00.00'],
            ['code' => '503.08.00', 'name' => 'Beban Pemeliharaan', 'parent' => '503.00.00'],
            ['code' => '503.09.00', 'name' => 'Beban Promosi dan Umum', 'parent' => '503.00.00'],

            ['code' => '504.00.00', 'name' => 'Pendapatan Lain-lain', 'parent' => '500.00.00'],
            ['code' => '504.00.01', 'name' => 'Pendapatan Bunga', 'parent' => '504.00.00'],

            ['code' => '505.00.00', 'name' => 'Beban Lain-lain', 'parent' => '500.00.00'],
            ['code' => '505.00.01', 'name' => 'Beban Adm Bank', 'parent' => '505.00.00'],
            ['code' => '505.00.02', 'name' => 'Beban Bunga Bank', 'parent' => '505.00.00'],
            ['code' => '505.00.03', 'name' => 'Selisih Buku', 'parent' => '505.00.00'],

            ['code' => '506.00.00', 'name' => 'Beban Pajak Final', 'parent' => '505.00.00'],
        ];

        foreach ($accounts as $acc) {

            $parentId = null;

            if ($acc['parent']) {
                $parent = ChartOfAccount::where('code', $acc['parent'])->first();
                $parentId = $parent ? $parent->id : null;
            }

            ChartOfAccount::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'parent_id' => $parentId
            ]);
        }
    }
}
