<?php

namespace App\Http\Controllers;

use App\Services\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbonnementSettingsController extends Controller
{
    protected $smtpService;

    public function __construct(SmtpService $smtpService)
    {
        $this->smtpService = $smtpService;
    }
    public function modifier(){
        $abonnements_settings = DB::table('abonnement_settings')->first(); // Fetch the first record, not all
        return view('parametres.abonnementsSettings.modifier', compact('abonnements_settings'));
    }

    public function mettre_a_jour(Request $request)
    {
        // Validation des données du formulaire
        $validatedData = $request->validate([
            'emails' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required',
        ]);
        // Vérifier si une configuration SMTP existe déjà
        $abonnements_settings = DB::table('abonnement_settings')->first();

        // Si aucune configuration n'existe, en créer une nouvelle
        if (!$abonnements_settings) {
            DB::table('abonnement_settings')->insert([
                'emails' => $validatedData['emails'],
                'subject' => $validatedData['subject'],
                'content' => $validatedData['content'],
                'notifier_client' => $request->get('notifier_client') ?? false

            ]);
        } else {
            // Si une configuration existe, la mettre à jour
            DB::table('abonnement_settings')->update([
                'emails' => $validatedData['emails'],
                'subject' => $validatedData['subject'],
                'content' => $validatedData['content'],
                'notifier_client' => $request->get('notifier_client') ?? false


            ]);
        }
        return redirect()->route('abonnementsSettings.modifier')->with('success', "Paramètres d'abonnement mis à jour avec succès.");
    }



}
