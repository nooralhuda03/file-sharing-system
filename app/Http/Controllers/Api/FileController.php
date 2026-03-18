<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\File\CreateFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Http\Resources\FileResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileController extends Controller
{
    /**
     * Files list (search + sort + pagination)
     */
    public function index(Request $request)
    {
        $query = File::myFiles()->with('folder', 'user');

        if ($request->search) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        if ($request->sort_by == 'size') {
            $query->orderBy('size', $request->sort ?? 'asc');
        }

        if ($request->sort_by == 'created_at') {
            $query->orderBy('created_at', $request->sort ?? 'desc');
        }

        $files = $query->paginate(5);

        return FileResource::collection($files);
    }

    /**
     * Upload file
     */
public function store(CreateFileRequest $request)
{
     $data = $request->validated();
    $file = File::create($data);

    return new FileResource($file);
}

    /**
     * Helper: find file
     */
   private function find($folderId, $fileId)
{
    return File::where('id', $fileId)
        ->where('folder_id', $folderId)
        ->where('user_id', auth()->id())
        ->with('folder', 'user')
        ->firstOrFail();
}

    /**
     * File details
     */
  public function show($folderId, $fileId)
{
    $file = $this->find($folderId, $fileId);

    return new FileResource($file);
}

    /**
     * Update file info
     */
    public function update(UpdateFileRequest $request, $folderId, $fileId)
    {
        $file = $this->find($folderId, $fileId);

        $file->update($request->validated());

        return new FileResource($file);
    }

    /**
     * Delete file
     */
public function destroy($folderId, $fileId)
{
    $file = $this->find($folderId, $fileId);

    $file->delete();

    return response()->json(null, Response::HTTP_NO_CONTENT);
}

    /**
     * Download file
     */
public function download($folderId, $fileId)
{
    $file = $this->find($folderId, $fileId);

    if ($file->visibility === 'private' && !auth()->check()) {
        abort(Response::HTTP_FORBIDDEN);
    }

    $file->increment('downloads_count');

    return response()->json([
        'message' => 'Download simulated',
        'file' => new FileResource($file)
    ]);
}

    /**
     * Set visibility only
     */
    public function setVisibility(UpdateFileRequest $request, $folderId, $fileId)
    {
        $file = $this->find($folderId, $fileId);

        $file->update($request->validated());

        return new FileResource($file);
    }
}