<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    private $disk = 'backup';

    public function index()
    {
        $files = Storage::disk($this->disk)->files();
        return view('backup.index', compact('files'));
    }

    public function download(Request $request)
    {
        $filename = $request->filename;
        $directory = storage_path('app/backup');

        $file = $directory . '/' . $filename;
        if (!is_file($file)) {
            return response('没有这个文件', 404);
        }
        return response()->download($file);
    }
}
