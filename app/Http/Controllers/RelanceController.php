<?php

namespace App\Http\Controllers;

use App\Models\RelanceSettings;
use App\Services\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelanceController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['parametres.relance']);
        $o_templates = RelanceSettings::all();
        return view('parametres.relance.liste',compact('o_templates'));
    }

    public function ajouter()
    {
        $this->guard_custom(['parametres.relance']);
        return view('parametres.relance.ajouter');
    }


    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.relance']);
        $validatedData = $request->validate([
            'emails' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = explode(';', $value);
                    foreach ($emails as $email) {
                        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                            $fail("L'un des emails dans le champ $attribute n'est pas valide.");
                        }
                    }
                },
            ],
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'active' => 'boolean',
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'in:dv,fa,fp'],
            ], [], [
            'emails' => 'Veuillez fournir des adresses e-mail valides, séparées par des virgules.',
            'subject' => 'Le sujet est requis et ne doit pas dépasser 255 caractères.',
            'content' => 'Le contenu est requis.',
            'name' => 'Le nom est requis et ne doit pas dépasser 255 caractères.',
            'type' => 'Le type est requis.',
        ]);

        if (!$validatedData) {
            return redirect()->back()->withErrors($request->errors())->withInput();
        }

        \DB::beginTransaction();
        try {

            if (!empty($validatedData['active']) && $validatedData['active'] == 1) {
                RelanceSettings::where('active', true)->where('type', $validatedData['type'])->update(['active' => false]);
            }
            RelanceSettings::create([
                'emails_cc' => $validatedData['emails'] ?? null,
                'subject' => $validatedData['subject'],
                'content' => $validatedData['content'],
                'active' => $validatedData['active'] ?? false,
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
            ]);

            DB::commit();
            return redirect()->route('relance.liste')->with('success', "Template de relance ajoutée avec succès.");

        }catch (Exception $exception){
            LogService::logException($exception);
            DB::rollBack();
            return redirect()->back()->with('error', "Une erreur s'est produite lors de l'ajout du template de relance.");
        }
    }

    public function modifier($id)
    {
        $this->guard_custom(['parametres.relance']);
        $template = RelanceSettings::find($id);

        if (!$template) {
            return redirect()->route('relance.liste')->with('error', "Template introuvable.");
        }

        return view('parametres.relance.modifier', compact('template'));
    }


    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['parametres.relance']);
        $validatedData = $request->validate([
            'emails' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = explode(';', $value);
                    foreach ($emails as $email) {
                        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                            $fail("L'un des emails dans le champ $attribute n'est pas valide.");
                        }
                    }
                },
            ],
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'active' => 'boolean',
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'in:dv,fa,fp'],
        ]);

        try {

            $template = RelanceSettings::findOrFail($id);

            if (!empty($validatedData['active']) && $validatedData['active'] == 1) {
                RelanceSettings::where('active', true)->where('type',$validatedData['type'])
                    ->where('id', '!=', $template->id)->update(['active' => false]);
            }

            $template->update([
                'emails_cc' => $validatedData['emails'] ?? null,
                'subject' => $validatedData['subject'],
                'content' => $validatedData['content'],
                'active' => $validatedData['active'] ?? false,
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
            ]);

            return redirect()->route('relance.liste')->with('success', "Template de relance mise à jour avec succès.");
        } catch (Exception $exception) {
            LogService::logException($exception);
            return redirect()->back()->with('error', "Une erreur s'est produite lors de la mise à jour du template de relance.");
        }
    }

    public function supprimer($id)
    {
        $this->guard_custom(['parametres.relance']);
        $template = RelanceSettings::find($id);

        if (!$template) {
            return redirect()->route('relance.liste')->with('error', "Template introuvable.");
        }

        if (\request()->ajax()) {
            $template = RelanceSettings::find($id);
            if ($template) {
                $template->delete();
                return response('Template supprimée avec succès', 200);
            } else {
                return response('Erreur de suppression', 404);
            }
        }
    }

    public function modifier_active(Request $request, int $id)
    {
        $this->guard_custom(['parametres.relance']);
        $template = RelanceSettings::find($id);

        if (!$template) {
            return response()->json(['error' => 'Template introuvable.'], 404);
        }

        if ($request->get('active') == 1) {
            RelanceSettings::where('active', true)
                ->where('type', $template->type)
                ->update(['active' => false]);
        }

        $template->active = $request->get('active');
        $template->save();

        return response()->json(['success' => 'Template '. $template->name . ' modifiée avec succès.'], 200);
    }
}
