<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LetterDownloadController extends Controller
{
    public function __invoke(Letter $letter)
    {
        Gate::authorize('view-letters');

        if (! Storage::disk('local')->exists($letter->file_path)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $letter->file_path,
            $letter->original_file_name
        );
    }
}
