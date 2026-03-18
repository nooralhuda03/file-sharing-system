<?php

namespace App\Http\Controllers\Api;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Folder\CreateFolderRequest;
use App\Http\Requests\Folder\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FolderController extends Controller
{
    /**
     * List folders
     */
    public function index()
    {
        $folders = Folder::where('user_id', auth()->id())
            ->withCount('files')
            ->get();

        return FolderResource::collection($folders);
    }

    /**
     * Create folder
     */
    public function store(CreateFolderRequest $request)
    {
        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return new FolderResource($folder);
    }

    /**
     * Helper: find folder
     */
    private function find($id)
    {
        $folder = Folder::where('id', $id)
            ->where('user_id', auth()->id())
            ->withCount('files')
            ->with('user')
            ->first();

        if (!$folder) {
            throw new NotFoundHttpException("Folder not found");
        }

        return $folder;
    }

    /**
     * Show folder details
     */
    public function show(string $id)
    {
        return new FolderResource($this->find($id));
    }

    /**
     * Update folder
     */
    public function update(UpdateFolderRequest $request, string $id)
    {
        $folder = $this->find($id);

        $folder->update($request->validated());

        return new FolderResource($folder);
    }

    /**
     * Delete folder
     */
    public function destroy(string $id)
    {
        $folder = $this->find($id);

        $folder->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Show files inside folder
     */
    public function files(Request $request, string $id)
{
    $folder = $this->find($id);

    $query = $folder->files()->with('user');

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
}