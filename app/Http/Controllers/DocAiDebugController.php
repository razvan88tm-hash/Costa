<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class DocAiDebugController extends Controller
{
    public function index()
    {
        $files = [];

        if (Storage::exists('docai_debug')) {
            foreach (Storage::files('docai_debug') as $path) {
                $files[] = [
                    'name'  => basename($path),
                    'path'  => $path,
                    'size'  => Storage::size($path),
                    'mtime' => Storage::lastModified($path),
                ];
            }
        }

        // cele mai noi primele
        usort($files, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
        });

        return view('docai.debug', compact('files'));
    }

    public function show(string $file)
    {
        $path = 'docai_debug/' . $file;

        if (! Storage::exists($path)) {
            abort(404);
        }

        $json = Storage::get($path);

        return view('docai.show', [
            'file' => $file,
            'json' => $json,
        ]);
    }
}
