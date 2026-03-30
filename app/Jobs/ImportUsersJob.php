<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Reader\XLSX\Reader;    
use App\Models\Role;

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
        $emails = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {

                if ($index === 1) continue;

                $cells = $row->toArray();
                if (empty($cells[1])) continue;

                $emails[] = $cells[1];

                $usersBatch[] = [
                    'name'     => $cells[0],
                    'email'    => $cells[1],
                    'password' => Hash::make($cells[2] ?? 'password123'),
                ];

                if (count($usersBatch) >= $batchSize) {
                    $this->insertAndSendEmails($usersBatch);
                    $usersBatch = [];
                }
            }
        }

        if (!empty($usersBatch)) {
            $this->insertAndSendEmails($usersBatch);
        }

        $reader->close();

        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }


private function insertAndSendEmails($usersBatch)
{
    $emails = array_column($usersBatch, 'email');

    $existingEmails = User::whereIn('email', $emails)
        ->pluck('email')
        ->toArray();

    User::upsert($usersBatch, ['email'], ['name', 'password']);

    $users = User::whereIn('email', $emails)->get();

    $role = Role::where('name', 'user')->first();

    foreach ($users as $user) {

        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    foreach ($usersBatch as $userData) {
        if (!in_array($userData['email'], $existingEmails)) {
            Mail::to($userData['email'])
                ->queue(new WelcomeUserMail((object)$userData));
        }
    }
}
}