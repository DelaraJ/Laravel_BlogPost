<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlogExportController extends Controller
{
    public function index()
    {
        $files = Storage::disk('local')->allFiles('exports');  
        $exportFiles = array_filter($files, function($file) {
            return preg_match('/blogs_\d{4}-\d{2}-\d{2}\_to_\d{4}-\d{2}-\d{2}\.xlsx$/', $file);
        });

        return response()->json($exportFiles);
    }

    public function download($filename)
    {
        $filePath = 'exports/' . $filename . '.xlsx';  
        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
