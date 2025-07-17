<?php

namespace App\Imports;

use App\Models\Client;
use App\Services\ReferenceService;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToModel,WithHeadingRow,SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $formeJuridiqueMapping = [
            'S.A.R.L' => ['id' => 4, 'name' => 'sarl'],
            'Personne Physique' => ['id' => 1, 'name' => 'personne_physique'],
            'Auto Entrepreneur' => ['id' => 5, 'name' => 'auto_entrepreneur'],
            'Société Anonyme' => ['id' => 2, 'name' => 'sa'],
            'Société Anonyme Simplifiée' => ['id' => 3, 'name' => 'sas'],
            'groupement d’intérêt économique' => ['id' => 6, 'name' => 'gie'],
            'Société en nom collectif' => ['id' => 7, 'name' => 'snc'],
            'Société en Commandite par Actions' => ['id' => 8, 'name' => 'sca'],
            'Société en Commandite Simple' => ['id' => 9, 'name' => 'scs'],
            'Particulier' => ['id' => 10, 'name' => 'p']
        ];


        $formeJuridique = $formeJuridiqueMapping[$row['forme_juridique']];
        if(!$row['reference']){
            $row['reference']= ReferenceService::generateReference('clt');
            ReferenceService::incrementCompteur('clt');

        }
        return new Client([
            'forme_juridique_id' => $formeJuridique['id'] ,
            'reference' => $row['reference'],
            'nom' => $row['raison_sociale'],
            'ice' => $row['ice'],
            'email' => $row['email'],
            'telephone' => $row['telephone'],
            'note' => $row['note'],
            'adresse' => $row['adresse'],
        ]);
    }

}
