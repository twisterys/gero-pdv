<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clients permission
        $permissions = [
            /**
             * Permissions de model client
             */
            'client.*',
            'client.liste',
            'client.afficher',
            'client.sauvegarder',
            'client.mettre_a_jour',
            'client.supprimer',
            /**
             * Permissions de model fournisseur
             */
            'fournisseur.*',
            'fournisseur.liste',
            'fournisseur.afficher',
            'fournisseur.sauvegarder',
            'fournisseur.mettre_a_jour',
            'fournisseur.supprimer',
            /**
             * Permissions de model commercial
             */
            'commercial.*',
            'commercial.liste',
            'commercial.afficher',
            'commercial.sauvegarder',
            'commercial.mettre_a_jour',
            'commercial.supprimer',
            /**
             * Permissions de model vente
             */
            'vente.*',
            'vente.liste',
            'vente.afficher',
            'vente.sauvegarder',
            'vente.mettre_a_jour',
            'vente.supprimer',
            'vente.valider',
            'vente.devalider',
            'vente.convertir',
            'vente.convertir_mass',
            'vente.cloner',
            'vente.telecharger',
            'vente.historique',
            /**
             * Permissions de model achat
             */
            'achat.*',
            'achat.liste',
            'achat.afficher',
            'achat.sauvegarder',
            'achat.mettre_a_jour',
            'achat.supprimer',
            'achat.valider',
            'achat.devalider',
            'achat.convertir',
            'achat.convertir_mass',
            'achat.cloner',
            'achat.telecharger',
            'achat.historique',
            /**
             * Permissions de model depense
             */
            'depense.*',
            'depense.liste',
            'depense.afficher',
            'depense.sauvegarder',
            'depense.mettre_a_jour',
            'depense.supprimer',
            /**
             * Permissions de model stock
             */
            'stock.*',
            'transfert_stock.*',
            'inventaire.*',
            /**
             * Permissions de model compte
             */
            'compte.*',
            'compte.liste',
            'compte.afficher',
            'compte.sauvegarder',
            'compte.mettre_a_jour',
            'compte.supprimer',
            /**
             * Permissions de module paiement
             */
            'paiement.*',
            'paiement.liste',
            'paiement.supprimer',
            'paiement.operation_bancaire',
            'paiement.mettre_a_jour',
            'paiement.achat',
            'paiement.vente',
            'paiement.depense',
            /**
             * Permissions de module rapport
             */
            'rapport.*',
            /**
             * Permissions de model utilisateur
             */
            'utilisateur.*',
            'utilisateur.liste',
            'utilisateur.afficher',
            'utilisateur.sauvegarder',
            'utilisateur.mettre_a_jour',
            'utilisateur.supprimer',
            /**
             * Permissions de model parametres
             */
            'parametres.*',
            'parametres.reference',
            'parametres.mise_en_page',
            'parametres.modules',
            'parametres.methode_de_paiement',
            'parametres.magasins',
            'parametres.unite',
            'parametres.taxes',
            'parametres.balises',
            'parametres.forme_juridique',
            'parametres.fonctionnalités',
            'parametres.compteurs',
            'parametres.pos',
            'parametres.methodes_livraison',
            /**
             * Permissions de module pos
             */
            'pos.*',
            'pos.historique',
            'pos.demande_transfert',
            /**
             * Permissions de import/export
             */
            'importer.*',
            'exporter.*',

            /**
             * Permissions des permission
             */
            'permission.*',
            'permission.liste',
            'permission.sauvegarder',
            'permission.mettre_a_jour',
            'permission.supprimer',
            /**
             * Permissions des affaires
             */
            'affaire.*',
            'affaire.liste',
            'affaire.sauvegarder',
            'affaire.mettre_a_jour',
            'affaire.supprimer',
            /**
             * Permissions des activites
             */
            'activite.*',
            'activite.liste',
            'activite.sauvegarder',
            'activite.mettre_a_jour',
            'activite.supprimer',

            /**
             * Permissions des abonnements
             */
            'abonnement.*',
            'abonnement.liste',
            'abonnement.sauvegarder',
            'abonnement.afficher',
            'abonnement.mettre_a_jour',
            'abonnement.supprimer',
            'abonnement.renouveler',

            /**
             * Permissions des cheques
             */
            'cheque.*',
            'cheque.liste',
            'cheque.sauvegarder',
            'cheque.mettre_a_jour',
            'cheque.annuler',
            'cheque.encaisser',
            'cheque.decaisser',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }
        Role::firstOrCreate([
            'name' => 'admin',
        ]);
        Role::firstOrCreate([
            'name' => 'super_admin',
        ]);
        Role::firstOrCreate([
            'name'=>'vendeur'
        ]);
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo($permissions);

        $vendeur = Role::where('name','vendeur')->first();
        $vendeur->givePermissionTo([ 'pos.*', 'pos.historique', 'pos.demande_transfert',]);
        foreach (User::all() as $user){
            if ($user->role == 'admin'){
                $user->assignRole('admin');
            }elseif($user->role = 'vendeur'){
                $user->assignRole('vendeur');
            }
        }
    }
}
