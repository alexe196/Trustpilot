<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\ExportCSVService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $link = '';
        if ($request->post('csv')) {
            $link = (new ExportCSVService)->getLincExportFile();
        }

        return view('dashboard', ['link' => $link]);
    }
}
