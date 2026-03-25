<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportUserRequest;
use App\Jobs\ImportUsersJob;
use Exception;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function importUsers(ImportUserRequest $request)
{
    $validatedData = $request->validated();

    try {
       
        $fileName = time() . '_' . $validatedData['file']->getClientOriginalName();
        $path = $validatedData['file']->storeAs('temp_imports', $fileName);
        
        $fullPath = Storage::path($path);

       
        ImportUsersJob::dispatch($fullPath);

        return response()->json([
            'status' => 'success',
            'message' => 'The file is being processed in the background.'
        ]);

    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}