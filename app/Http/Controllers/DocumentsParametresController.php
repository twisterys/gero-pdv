<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentsParametre;
use App\Models\GlobalSetting;
use App\Models\Template;
use App\Models\Unite;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\FileService;
use App\Traits\ArticleHelper;
use App\Traits\DocumentHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\FileHelpers;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class DocumentsParametresController extends Controller
{
    use DocumentHelper;

    public function modifier()
    {
        $templates = Template::all();
        $o_document_parametres = DocumentsParametre::get()->first();

        return view('parametres.documents.modifier', compact('o_document_parametres', 'templates'));
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $o_document_parametres = DocumentsParametre::findOrFail($id);
        $request->validate([
            'image_arriere_plan' => 'nullable',
            'image_en_tete' => 'nullable',
            'image_pied_page' => 'nullable',
            'afficher_total_en_chiffre' => 'nullable',
            'espace_en_tete' => 'nullable',
            'espace_pied_page' => 'nullable',
            'cachet' => 'nullable',
//           'template_id' => 'nullable|exists:templates,id',
        ]);

        $o_document_parametres->update([
            'template_id' => $request->get('template'),
        ]);
        $template = Template::find($request->get('template'));
        $logo = $request->hasFile('logo') ? $this->store_document_image($request->file('logo')) : ($request->get('logo-input_delete') == 1 ? null : $template->logo);
        $image_en_bas = $request->hasFile('image_en_bas') ? $this->store_document_image($request->file('image_en_bas')) : ($request->get('image-en-bas-input_delete') == 1 ? null : $template->image_en_bas);;
        $image_arriere_plan = $request->hasFile('image_arriere_plan') ? $this->store_document_image($request->file('image_arriere_plan')) : ($request->get('image-arriere-plan-input_delete') == 1 ? null : $template->image_arriere_plan);;
        $image_en_tete = $request->hasFile('image_en_tete') ? $this->store_document_image($request->file('image_en_tete')) : ($request->get('image-en-tete-input_delete') == 1 ? null : $template->image_en_tete);;
        $cachet = $request->hasFile('cachet') ? $this->store_document_image($request->file('cachet')) : ($request->get('cachet-input_delete') == 1 ? null : $template->cachet);
        $template->update([
            'couleur' => $request->get('couleur'),
            'logo' => $logo,
            'image_en_bas' => $image_en_bas,
            'image_en_tete' => $image_en_tete,
            'image_arriere_plan' => $image_arriere_plan,
            'cachet' => $cachet,
            'afficher_total_en_chiffre' => $request->get('afficher_total_en_chiffre') ?? false
        ]);
        session()->flash('success', 'Les paramètres du document ont été mis à jour');
        return redirect()->route('documents.modifier');
    }

    public function loadPicture($file)
    {
        return $this->load($file);
    }

    public function templateSettings(Request $request)
    {
        $template = Template::findOrFail($request->get('template'));
        return \view('parametres.documents.parials.template-parametre', compact('template'));
    }

    public function preview($id)
    {
        $o_template = Template::findOrFail($id);
        $o_vente = new Vente();
        $o_vente->reference = 'FA-0000';
        $o_vente->date_emission = now()->toDateString();
        $o_vente->date_expiration = now()->toDateString();
        $o_vente->type_document = 'fa';
        $o_vente->total_ttc = 7000.00;

        $o_ligne1 = new VenteLigne();
        $o_ligne1->quantite = 2;
        $o_ligne1->nom_article = "Exemple article 1";
        $o_ligne1->ht = 100;
        $o_ligne1->unite = new Unite(['nom' => 'U']);
        $o_ligne1->reduction = 0;
        $o_ligne1->mode_reduction = 'fixe';
        $o_ligne1->taxe = 20;
        $o_ligne1->total_ttc = $o_ligne1->quantite * $o_ligne1->ht * (1 + $o_ligne1->taxe / 100);

        $o_ligne2 = new VenteLigne();
        $o_ligne2->quantite = 1;
        $o_ligne2->mode_reduction = 'fixe';
        $o_ligne2->nom_article = "Exemple article 2";
        $o_ligne2->ht = 50;
        $o_ligne2->unite = new Unite(['nom' => 'U']);;
        $o_ligne2->reduction = 0;
        $o_ligne2->taxe = 20;
        $o_ligne2->total_ttc = $o_ligne2->quantite * $o_ligne2->ht * (1 + $o_ligne2->taxe / 100);

        $o_ligne3 = new VenteLigne();
        $o_ligne3->quantite = 1;
        $o_ligne3->nom_article = "Exemple article 3";
        $o_ligne3->ht = 50;
        $o_ligne3->unite = new Unite(['nom' => 'U']);
        $o_ligne3->reduction = 0;
        $o_ligne3->mode_reduction = 'fixe';
        $o_ligne3->taxe = 20;
        $o_ligne3->total_ttc = $o_ligne3->quantite * $o_ligne3->ht * (1 + $o_ligne3->taxe / 100);

        $o_ligne4 = new VenteLigne();
        $o_ligne4->quantite = 3;
        $o_ligne4->nom_article = "Exemple article 4";
        $o_ligne4->ht = 75;
        $o_ligne4->unite = new Unite(['nom' => 'U']);
        $o_ligne4->reduction = 0;
        $o_ligne4->mode_reduction = 'fixe';
        $o_ligne4->taxe = 20;
        $o_ligne4->total_ttc = $o_ligne4->quantite * $o_ligne4->ht * (1 + $o_ligne4->taxe / 100);

        $o_vente->lignes = collect([$o_ligne1, $o_ligne2, $o_ligne3, $o_ligne4]);


        $o_vente->total_ttc = $o_vente->lignes->sum('total_ttc');
        $o_vente->total_ht = $o_vente->lignes->sum('ht');
        $o_vente->total_tva = $o_vente->total_ttc - $o_vente->total_ht;

        $o_vente->client = new Client();
        $o_vente->client->nom = 'Client';
        $type = $o_vente->type_document;

        $images = [
            'image_en_tete' => $o_template->image_en_tete ? $this->base64_img($o_template->image_en_tete) : $this->base64_img('template-1-en-tete.png', true),
            'image_en_bas' => $o_template->image_en_bas ? $this->base64_img($o_template->image_en_bas) : $this->base64_img('template-1-en-bas.jpg', true),
            'image_arriere_plan' => $o_template->image_arriere_plan ? $this->base64_img($o_template->image_arriere_plan) : null,
            'logo' => $o_template->logo ? $this->base64_img($o_template->logo) : $this->base64_img('logo.png', true),
            'cachet'=> $o_template->cachet ? $this->base64_img($o_template->cachet): $this->base64_img('template-2-cachet.png', true)
        ];
        foreach (explode(',', $o_template->elements) as $element) {
            if (!$o_template[$element]) {
                $o_template->$element = 1;
            }
        }
        $pdf = Pdf::loadView('documents.ventes.' . $o_template->blade, compact('type', 'o_vente', 'o_template', 'images'))->setOptions(['defaultFont' => 'Rubik'])->set_option("isPhpEnabled", true);
        return $pdf->stream($o_vente->client->nom . ' ' . $o_vente->date_emission . ' ' . $o_vente->reference . '.pdf');
    }

    public function base64_img(string $image, $local = false): string|null
    {
        if (!$local) {
            $path = 'public/documents/' . $image;
            if (Storage::disk('external_storage')->exists($path)) {
                $type = pathinfo($image, PATHINFO_EXTENSION);
                $data = Storage::disk('external_storage')->get($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                return $base64;
            }
            return null;
        } else {
            $path = 'public/uploads/documents/' . $image;
            if (Storage::has($path)) {
                $type = pathinfo($image, PATHINFO_EXTENSION);
                $data = Storage::get($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                return $base64;
            }
        }

        return null;
    }

}
