<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BackupController extends BaseController
{

    public function export(Request $request)
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . DB::getDatabaseName();

        $sqlDump = "-- Backup Database: " . DB::getDatabaseName() . "\n";
        $sqlDump .= "-- Date: " . now()->toDateTimeString() . "\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey;

            // Get table create statement
            $create = DB::select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlDump .= $create . ";\n\n";

            // Get data
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $columns = array_map(fn($col) => "`$col`", array_keys((array)$row));
                $values = array_map(function ($value) {
                    if (is_null($value)) return "NULL";
                    return "'" . str_replace("'", "''", $value) . "'";
                }, array_values((array)$row));

                $sqlDump .= "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Simpan ke file
        $fileName = "backup_" . DB::getDatabaseName() . "_" . now()->format("Ymd_His") . ".sql";
        Storage::disk('local')->put("Laravel/{$fileName}", $sqlDump);

        return response()->download(storage_path("app/private/Laravel/{$fileName}"))->deleteFileAfterSend(true);
    }
}

