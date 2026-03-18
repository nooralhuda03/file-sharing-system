<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Reader\XLSX\Reader;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $reader = new Reader();
        $reader->open($this->filePath);

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
                    User::upsert($usersBatch, ['email'], ['name', 'password']);
                    $usersBatch = [];
                }
            }
        }

        if (!empty($usersBatch)) {
            User::upsert($usersBatch, ['email'], ['name', 'password']);
        }

        $reader->close();
        
       
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}