<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Reader\XLSX\Reader;
use Exception;

class UserController extends Controller
{
    public function importUsers(Request $request)
{
    set_time_limit(600);

    $request->validate([
        'file' => 'required|file|mimes:xlsx'
    ]);

    try {
        $filePath = $request->file('file')->getRealPath();
        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($filePath);

        $batchSize = 100;
        $usersBatch = [];
        $now = now();

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue;

                $cells = $row->toArray();
                if (empty($cells[1])) continue; 

                $usersBatch[] = [
                    'name'       => $cells[0],
                    'email'      => $cells[1],
                    'password'   => Hash::make($cells[2] ?? 'password123'),
                    
                ];

                if (count($usersBatch) >= $batchSize) {
                   
                    User::upsert($usersBatch, ['email'], ['name', 'password', 'updated_at']);
                    $usersBatch = [];
                }
            }
        }

        if (!empty($usersBatch)) {
            User::upsert($usersBatch, ['email'], ['name', 'password', 'updated_at']);
        }

        $reader->close();

        return response()->json(['status' => 'success', 'message' => 'Users synced successfully.']);

    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}