<?php

namespace App\Services;

use App\Models\Compteur;
use App\Models\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ReferenceService
{
    public const COMPTEUR_TYPES = [
        'clt',
        'fr',
        'cms',
        'art',
        'dv',
        'fa',
        'av',
        'bl',
        'bc',
        'br',
        'fp',
        'dva',
        'bca',
        'bla',
        'bra',
        'faa',
        'ava',
        'dpa'
    ];

    /**
     * Generates a formatted reference string based on the provided type, date, and exercice.
     *
     * If no date or exercice is specified, the current date and session exercice are used respectively.
     * Determines the appropriate reference details such as template and counter based on the type of reference.
     *
     * Reference Types:
     * - Global counters (independent of year): 'clt' (client), 'fr' (fournisseur), 'cms' (commission),
     *   'art' (article), 'dpa'
     * - Year-specific counters: 'dv' (devis), 'fa' (facture), 'av' (avoir), 'bl' (bon de livraison),
     *   'bc' (bon de commande), 'br' (bon de réception), 'fp' (facture proforma), 'dva' (devis d'achat),
     *   'bca' (bon de commande achat), 'bla' (bon de livraison achat), 'bra' (bon de réception achat),
     *   'faa' (facture achat), 'ava' (avoir achat)
     *
     * Date Format Placeholders in templates:
     * - [a] - Two-digit year (e.g., '23' for 2023)
     * - [A] - Four-digit year (e.g., '2023')
     * - [j] - Day of month with leading zeros (01-31)
     * - [m] - Month with leading zeros (01-12)
     * - [n] - Counter value padded with leading zeros to match longueur_compteur
     *
     * Example: A template "FAC-[A]/[m]/[n]" with counter length 4 will generate something like "FAC-2023/06/0042"
     *
     * @param string $referenceType The type of reference to generate (from COMPTEUR_TYPES)
     * @param \Carbon\Carbon|null $date The date to use in the reference generation (optional)
     * @param mixed|null $exercice The exercice value to use in the reference generation (optional)
     * @return string The generated reference
     */

    public static function generateReference($referenceType, $date = null, $exercice = null){
        if ($date === null){
            $date = Carbon::now();
        }
        if ($exercice === null){
            $exercice = session('exercice');
        }
        $o_reference = Reference::where('type', $referenceType)->first();
        if (in_array($referenceType,['clt','fr','cms','art','dpa'])){
            $compteur = Compteur::where('type',$referenceType)->first()->compteur;
        }else {
            $compteur = Compteur::where('annee', $exercice)->where('type', $referenceType)->value('compteur');
        }
        $number = str_pad($compteur, $o_reference->longueur_compteur, '0', STR_PAD_LEFT) ;
        $template = $o_reference->template;
        $functions =[
            '[a]'=>  $date->format('y'),
            '[A]'=> $date->format('Y'),
            '[j]'=> $date->format('d'),
            '[m]'=> $date->format('m'),
            '[n]'=> $number,
        ];
        $reference = str_replace(array_keys($functions),$functions,$template);
        return $reference;
    }

    public static function incrementCompteur($referenceType,$exercice = null)
    {
        if (!$exercice){
            $exercice = session('exercice');
        }
        if (in_array($referenceType,['clt','fr','cms','art','dpa'])){
            $compteur = Compteur::where('type',$referenceType)->first();
        }else {
            $compteur = Compteur::where('annee', $exercice)->where('type', $referenceType)->first();
        }
        if ($compteur) {
            $compteur->compteur += 1;
            $compteur->save();
        }
    }

    public static function generer_les_compteur(int $annee)
    {
        $types = self::COMPTEUR_TYPES;
//        check if there is already a counters if no create all counters if not create just the necessary ones
        $o_compteur = Compteur::first();
        if ($o_compteur){
            $types = array_diff(self::COMPTEUR_TYPES,['clt','fr','cms','art','dpa']);
        }
        DB::beginTransaction();
        try {
            foreach ($types as $type){
                Compteur::where('type',$type)->where('annee',$annee)->firstOr(function () use ($type,$annee) {
                    $o_compteur= new Compteur();
                    $o_compteur->type = $type;
                    $o_compteur->annee = $annee;
                    $o_compteur->compteur = 1;
                    $o_compteur->save();
                });
            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            LogService::logException($exception);
            abort(500);
        }
    }
}
