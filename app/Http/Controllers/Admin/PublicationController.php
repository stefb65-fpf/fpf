<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index()
    {
        return view('admin.publications.index');
    }

    public function routageFP()
    {
        return view('admin.publications.routageFP');
    }

    public function routageFede()
    {
        return view('admin.publications.routageFede');
    }

    public function etiquettes()
    {
        return view('admin.publications.etiquettes');
    }

    public function emargements()
    {
        return view('admin.publications.emargements');
    }
}
