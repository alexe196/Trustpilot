<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\test;
use Illuminate\Http\Request;

class DownloadLinkController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.download-link');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt|max:5000',
        ]);

        $request->file('file')->storeAs('public/parce-uploads', 'links.txt');

        return back()->with('success', 'Файл успешно загружен!');
    }

    public function test() {
         (new test())->handle();
        return 'set';
    }
}
