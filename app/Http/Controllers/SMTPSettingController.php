<?php

namespace App\Http\Controllers;

use App\Services\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SMTPSettingController extends Controller
{
    protected $smtpService;

    public function __construct(SmtpService $smtpService)
    {
        $this->smtpService = $smtpService;
    }
    public function modifier(){
        $smtp_settings = DB::table('smtp_settings')->first(); // Fetch the first record, not all
        return view('parametres.smtpSettings.modifier', compact('smtp_settings'));
    }

    public function mettre_a_jour(Request $request)
    {
        // Validation des données du formulaire
        $validatedData = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|max:50',
            'from_address' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
        ]);
        // Vérifier si une configuration SMTP existe déjà
        $smtpSettings = DB::table('smtp_settings')->first();

        // Si aucune configuration n'existe, en créer une nouvelle
        if (!$smtpSettings) {
            DB::table('smtp_settings')->insert([
                'host' => $validatedData['host'],
                'port' => $validatedData['port'],
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'encryption' => $validatedData['encryption'],
                'from_address' => $validatedData['from_address'],
                'from_name' => $validatedData['from_name'],
            ]);
        } else {
            // Si une configuration existe, la mettre à jour
            DB::table('smtp_settings')->update([
                'host' => $validatedData['host'],
                'port' => $validatedData['port'],
                'username' => $validatedData['username'],
                'password' => $validatedData['password'] ,
                'encryption' => $validatedData['encryption'],
                'from_address' => $validatedData['from_address'],
                'from_name' => $validatedData['from_name'],
            ]);
        }
        return redirect()->route('smtpSettings.modifier')->with('success', 'Paramètres SMTP mis à jour avec succès.');
    }



}
