<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AuthenticationLogsController extends Controller
{
    public function liste(Request $request)
    {
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
                ->whereDate('authentication_log.login_at', $date);

            if ($request->get('i_search')) {
                $searched_user = $request->get('i_search');
                $query->where('users.name', $searched_user);
            }
                $query->get();
                $table = DataTables::of($query);
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
                    return   $location['city'] . ', ' . $location['country'];
                });


            return $table->make();
        }
        return view('authentication_logs.liste');
    }
}
