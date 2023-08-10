<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index() {
        $invoices = Invoice::orderByDesc('id')->paginate(100);
        foreach ($invoices as $invoice) {
            list($tmp, $path) = explode('htdocs',  $invoice->getStorageDir());
            $path .= '/'.$invoice->numero.'.pdf';
            $invoice->path = $path;
        }
        return view('admin.factures.index', compact('invoices'));
    }
}
