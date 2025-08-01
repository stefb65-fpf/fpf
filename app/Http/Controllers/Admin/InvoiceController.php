<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Invoice;
use App\Models\Personne;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function index($term = null) {
        $query = Invoice::orderByDesc('id');
        if($term){
            $this->getInvoicesByTerm($term, $query);
            $term = trim($term, 'RE=');
        }
        $invoices = $query->paginate(100);
        foreach ($invoices as $invoice) {
            list($tmp, $path) = explode('htdocs',  $invoice->getStorageDir());
            $path .= '/'.$invoice->numero.'.pdf';
            $invoice->path = $path;
        }
        return view('admin.factures.index', compact('invoices', 'term'));
    }

    protected function getInvoicesByTerm($term, $query) {
        $valid = 0;
        if (is_numeric($term)) {
            $club = Club::where('numero', $term)->first();
            if ($club) {
                $query->where('club_id', $club->id);
                $valid = 1;
            }
        } else {
            if (str_starts_with($term, 'RE=')) {
                $reference = substr($term, 3);
                $query->where('reference', $reference);
                $valid = 1;
            }
            elseif (str_contains($term, '-')) {
                $personne = Personne::join('utilisateurs', 'utilisateurs.personne_id', '=', 'personnes.id')
                    ->where('utilisateurs.identifiant', $term)
                    ->selectRaw('personnes.id')
                    ->first();
                if ($personne) {
                    $query->where('personne_id', $personne->id);
                    $valid = 1;
                }
            } else {
                $personnes = Personne::where('nom', 'LIKE', '%' . $term . '%')
                    ->orWhere('prenom', 'LIKE', '%' . $term . '%')
                    ->selectRaw('id')
                    ->get();
                $in = [];
                foreach($personnes as $personne) {
                    $in[] = $personne->id;
                    $valid = 1;
                }
                $query->whereIn('personne_id', $in);
            }
        }
        if ($valid == 0) {
            $query->where('id', 0);
        }
        return $query;
    }

    public function avoirs() {
        // on chercke tous les clubs pour lesquels le champ créance est supérieur à 0
        $clubs = Club::where('creance', '>', 0)
            ->orderBy('numero')
            ->get();

        // on cherche toutes les personnes pour lesquelles le champ créance est supérieur à 0
        $personnes = Personne::where('creance', '>', 0)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('admin.factures.avoirs', compact('personnes', 'clubs'));
    }
}
