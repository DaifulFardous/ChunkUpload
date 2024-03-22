<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');

        $chunkIndex = $request->input('dzChunkIndex');
        $chunkTotal = $request->input('dzTotalChunkCount');
        $chunkSize = $request->input('dzChunkSize');

        $file->storeAs(
            'temp',
            "{$file->getClientOriginalName()}.part{$chunkIndex}",
            'local'
        );

        if ($chunkIndex == $chunkTotal - 1) {
            $chunks = [];
            for ($i = 0; $i < $chunkTotal; $i++) {
                $chunkPath = storage_path("app/temp/{$file->getClientOriginalName()}.part{$i}");
                $chunks[] = file_get_contents($chunkPath);
            }
            $fileContent = implode('', $chunks);

            $uniqueId = uniqid();

            Storage::disk('public')->put("uploads/{$uniqueId}.{$file->getClientOriginalExtension()}", $fileContent);
            $fileModel = new File();
            $fileModel->unique_id = $uniqueId;
            $fileModel->filename = $file->getClientOriginalName();
            $fileModel->path = "uploads/{$uniqueId}.{$file->getClientOriginalExtension()}";
            $fileModel->save();
            Storage::disk('local')->deleteDirectory('temp');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }
}