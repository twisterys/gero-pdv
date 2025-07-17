<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Magasin;
use App\Models\User;
use App\Services\LimiteService;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class UserController extends Controller
{
    public function liste()
    {
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }
        if (request()->ajax()) {
            $query = User::query();
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($row) {
                    $id = $row->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->addColumn('actions', function ($row) {
                $edit = 'modifier';
                $delete = 'supprimer';
                $connexion = 'connexion';
                $crudRoutePart = 'utilisateurs';
                $id = $row?->id;
                return view(
                    'partials.__datatable-action',
                    compact(
                        'edit',
                        'delete',
                        'connexion',

                        'crudRoutePart',
                        'id',
                    )
                )->render();
            })->rawColumns(['selectable_td', 'actions']);
            return $table->make();
        }
        return view('users.liste');
    }

    public function ajouter()
    {
        $this->guard_custom(['utilisateur.sauvegarder']);
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }
        $roles = Role::whereNot('name', 'super_admin')->get();
        $dashboards = Dashboard::all();

        if (Magasin::count() > 1) {
            $magasins = Magasin::all();
            return view('users.ajouter', compact('magasins', 'roles','dashboards'));

        } else {
            return view('users.ajouter',compact('roles','dashboards'));

        }
    }

    public function sauvegarder(Request $request)
    {
//        dd($request->all());
        $this->guard_custom(['utilisateur.sauvegarder']);
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }
        if (LimiteService::get_value('users') <= User::count()){
            session()->flash('warning',"Vous avez atteint le nombre maximum d'utilisateurs");
            return redirect()->route('utilisateurs.liste');
        }

        $validator = Validator::make($request->all(), [
            'i_nom' => 'required|min:3|max:255|string',
            'i_email' => 'required|email|unique:users,email',
            'i_password' => 'required|min:8|max:255',
            'i_role' => 'required|exists:roles,name',
            'i_dashboard' => 'required|exists:dashboards,id',
        ], [], [
            'i_nom' => 'nom complet',
            'i_email' => 'email',
            'i_password' => 'Mot de passe',
            'i_role' => 'Role',
            'i_dashboard' => 'Tableau de bord',
        ]);

        // Check validation and redirect with errors if any
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \DB::beginTransaction();
        try {
            $o_utilisateur = new User();
            $o_utilisateur->name = $request->get('i_nom');
            $o_utilisateur->email = $request->get('i_email');
            $o_utilisateur->email_verified_at = Carbon::now();
            $o_utilisateur->password = \Hash::make($request->get('i_password'));
            $o_utilisateur->save();
            if($request->get('i_magasins')){
                $o_utilisateur->magasins()->sync($request->get('i_magasins'));
            }else{
                $magasin = Magasin::first();
                $o_utilisateur->magasins()->sync($magasin->id);
            }
            $o_utilisateur->dashboards()->sync([$request->get('i_dashboard')]);
            $o_utilisateur->syncRoles( $request->get('i_role'));
            \DB::commit();
            session()->flash('success','Utilisateur ajouté !');
            return redirect()->route('utilisateurs.liste');
        }catch (\Exception $exception){
            \DB::rollBack();
            LogService::logException($exception);
            session()->flash('error','Erreur !');
            return redirect()->route('utilisateurs.liste');
        }
    }

    public function afficher($id)
    {
    }

    public function modifier($id)
    {
        $this->guard_custom(['utilisateur.mettre_a_jour']);
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }
        $o_utilisateur = User::find($id);
        if (!$o_utilisateur) {
            session()->flash('error', "Utilisateur n'existe pas !");
            return redirect()->route('utilisateurs.liste');
        }
        $roles = Role::whereNot('name', 'super_admin')->get();
        $magasins = Magasin::all();
        $dashboards = Dashboard::all();
        return view('users.modifier', compact('o_utilisateur', 'magasins','roles','dashboards'));
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['utilisateur.mettre_a_jour']);
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }
        $o_utilisateur = User::find($id);
        if (!$o_utilisateur) {
            session()->flash('error', "Utilisateur n'existe pas !");
            return redirect()->route('utilisateurs.liste');
        }

        $validator = Validator::make($request->all(), [
            'i_nom' => 'required|min:3|max:255|string',
            'i_email' => 'required|email|unique:users,email,' . $id,
            'i_password' => 'nullable|min:8|max:255',
            'i_role' => 'required|exists:roles,name',
            'i_dashboard' => 'required|exists:dashboards,id',
        ], [], [
            'i_nom' => 'nom complet',
            'i_email' => 'email',
            'i_password' => 'Mot de passe',
            'i_role' => 'Role',
            'i_dashboard' => 'Tableau de bord',
        ]);

        // Check validation and redirect with errors if any
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \DB::beginTransaction();
        try {
            $o_utilisateur->name = $request->get('i_nom');
            $o_utilisateur->email = $request->get('i_email');
            $o_utilisateur->email_verified_at = Carbon::now();
            $o_utilisateur->password = trim($request->get('i_password')) ? \Hash::make($request->get('i_password')) : $o_utilisateur->password;
            $o_utilisateur->role = $request->get('i_role');
            $o_utilisateur->save();
            $o_utilisateur->magasins()->sync($request->get('i_magasins'));
            $o_utilisateur->syncRoles( $request->get('i_role'));
            $o_utilisateur->dashboards()->sync([$request->get('i_dashboard')]);
            \DB::commit();
            session()->flash('success', 'Utilisateur modifié !');
            return redirect()->route('utilisateurs.liste');
        } catch (\Exception $exception) {
            \DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Erreur !');
            return redirect()->route('utilisateurs.liste');
        }
    }

    public function connexion(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            abort(404);
        }
        if ($request->ajax()) {
            $date = Carbon::today();
            if ($request->get('i_date')) {
                $selectedDateRange = $request->get('i_date');
                $parsedDate = Carbon::createFromFormat('d/m/Y', $selectedDateRange);
                $formattedDate = $parsedDate->format('Y-m-d');
                $date = $formattedDate;
            }
            $query = DB::table('authentication_log')
                ->select('authentication_log.*', 'users.name as user_name')
                ->join('users', 'authentication_log.authenticatable_id', '=', 'users.id')
                ->whereDate('authentication_log.login_at', $date)
                ->where('users.id', $id);

            if ($request->get('i_search')) {
                $searched_user = $request->get('i_search');
                $query->where('users.name', $searched_user);
            }
            $query->get();
            $table = \Yajra\DataTables\DataTables::of($query);
            $table->editColumn('login_at', function ($row) {
                return Carbon::parse($row->login_at)->format('H:i:s');
            });
            $table->editColumn('logout_at', function ($row) {
                if ($row->logout_at !== null) {
                    return Carbon::parse($row->logout_at)->format('H:i:s');
                } else {
                    return null;
                }
            });
            $table->editColumn('location', function ($row) {
                $location = json_decode($row->location, true);
                return $location['city'] . ', ' . $location['country'];
            });


            return $table->make();
        }
        return view('authentication_logs.liste', compact('user'));
    }


    public function supprimer($id)
    {
        $this->guard_custom(['utilisateur.supprimer']);
        if (!LimiteService::is_enabled('users')){
            abort(404);
        }

        if (\request()->ajax()) {
            $o_utilisateur = User::find($id);
            if ($o_utilisateur) {
                $o_utilisateur->delete();
                return response('Utilisateur supprimé  avec success', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }

    public function maLicence(){
        $user = auth()->user();
        $tenant = tenant();
        $users_count = User::count();
        $max_users_count = LimiteService::get_value('users');
        return view('users.ma_licence',compact('tenant','user','max_users_count','users_count'));
    }
}
