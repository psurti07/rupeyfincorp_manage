<?php

namespace Modules\Dashboard\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $saCustomers = array_reverse(DB::table('user_registrations')
        ->selectRaw('YEAR(rec_date) as recyear, MONTH(rec_date) as recmonth, DAY(rec_date) as recday, COUNT(id) as totaluser')
        ->where('isUser', 2)
        ->where('acc_type', 1)
        ->where('isDelete', 0)
        ->groupByRaw('YEAR(rec_date), MONTH(rec_date), DAY(rec_date)')
        ->orderByRaw('YEAR(rec_date) DESC, MONTH(rec_date) DESC, DAY(rec_date) DESC')
        ->limit(15)
        ->get()->toArray());

        $saLeads = array_reverse(DB::table('user_registrations')
        ->selectRaw('YEAR(update_date) as recyear, MONTH(update_date) as recmonth, DAY(update_date) as recday, COUNT(id) as totaluser')
        ->where('isUser', 1)
        ->where('acc_type', 1)
        ->where('isDelete', 0)
        ->groupByRaw('YEAR(update_date), MONTH(update_date), DAY(update_date)')
        ->orderByRaw('YEAR(update_date) DESC, MONTH(update_date) DESC, DAY(update_date) DESC')
        ->limit(15)
        ->get()->toArray());
    
        $laCustomers = array_reverse(DB::table('user_registrations')
        ->selectRaw('YEAR(rec_date) as recyear, MONTH(rec_date) as recmonth, DAY(rec_date) as recday, COUNT(id) as totaluser')
        ->where('isUser', 2)
        ->where('acc_type', 2)
        ->where('isDelete', 0)
        ->groupByRaw('YEAR(rec_date), MONTH(rec_date), DAY(rec_date)')
        ->orderByRaw('YEAR(rec_date) DESC, MONTH(rec_date) DESC, DAY(rec_date) DESC')
        ->limit(15)
        ->get()->toArray());

        $laLeads = array_reverse(DB::table('user_registrations')
        ->selectRaw('YEAR(update_date) as recyear, MONTH(update_date) as recmonth, DAY(update_date) as recday, COUNT(id) as totaluser')
        ->where('isUser', 1)
        ->where('acc_type', 2)
        ->where('isDelete', 0)
        ->groupByRaw('YEAR(update_date), MONTH(update_date), DAY(update_date)')
        ->orderByRaw('YEAR(update_date) DESC, MONTH(update_date) DESC, DAY(update_date) DESC')
        ->limit(15)
        ->get()->toArray());

        $webinarCustomers = array_reverse(
            DB::table('webinar_order as wo')
                ->join('webinar_event as we', 'wo.webinar_id', '=', 'we.id')
                ->selectRaw("YEAR(wo.rec_date) as recyear, MONTH(wo.rec_date) as recmonth, DAY(wo.rec_date) as recday, COUNT(wo.id) as totaluser ")
                ->where('wo.isUser', 2)
                ->where('wo.isDelete', 0)
                ->where('we.program_type', 0)
                ->groupByRaw('YEAR(wo.rec_date), MONTH(wo.rec_date), DAY(wo.rec_date)')
                ->orderByRaw('YEAR(wo.rec_date) DESC, MONTH(wo.rec_date) DESC, DAY(wo.rec_date) DESC')
                ->limit(15)
                ->get()
                ->toArray());

        $webinarLeads = array_reverse(
           DB::table('webinar_order as wo')
                ->join('webinar_event as we', 'wo.webinar_id', '=', 'we.id')
                ->selectRaw("YEAR(wo.rec_date) as recyear, MONTH(wo.rec_date) as recmonth, DAY(wo.rec_date) as recday, COUNT(wo.id) as totaluser ")
                ->where('wo.isUser', 1)
                ->where('wo.isDelete', 0)
                ->where('we.program_type', 0)
                ->groupByRaw('YEAR(wo.rec_date), MONTH(wo.rec_date), DAY(wo.rec_date)')
                ->orderByRaw('YEAR(wo.rec_date) DESC, MONTH(wo.rec_date) DESC, DAY(wo.rec_date) DESC')
                ->limit(15)
                ->get()
                ->toArray());

        $workshopCustomers = array_reverse(
            DB::table('webinar_order as wo')
                ->join('webinar_event as we', 'wo.webinar_id', '=', 'we.id')
                ->selectRaw("YEAR(wo.rec_date) as recyear, MONTH(wo.rec_date) as recmonth, DAY(wo.rec_date) as recday, COUNT(wo.id) as totaluser ")
                ->where('wo.isUser', 2)
                ->where('wo.isDelete', 0)
                ->where('we.program_type', 1)
                ->groupByRaw('YEAR(wo.rec_date), MONTH(wo.rec_date), DAY(wo.rec_date)')
                ->orderByRaw('YEAR(wo.rec_date) DESC, MONTH(wo.rec_date) DESC, DAY(wo.rec_date) DESC')
                ->limit(15)
                ->get()
                ->toArray());
        
        $workshopLeads = array_reverse(
            DB::table('webinar_order as wo')
                ->join('webinar_event as we', 'wo.webinar_id', '=', 'we.id')
                ->selectRaw("YEAR(wo.rec_date) as recyear, MONTH(wo.rec_date) as recmonth, DAY(wo.rec_date) as recday, COUNT(wo.id) as totaluser ")
                ->where('wo.isUser', 1)
                ->where('wo.isDelete', 0)
                ->where('we.program_type', 1)
                ->groupByRaw('YEAR(wo.rec_date), MONTH(wo.rec_date), DAY(wo.rec_date)')
                ->orderByRaw('YEAR(wo.rec_date) DESC, MONTH(wo.rec_date) DESC, DAY(wo.rec_date) DESC')
                ->limit(15)
                ->get()
                ->toArray());
        
        return view('dashboard::index',compact('saCustomers','saLeads','laCustomers','laLeads','webinarCustomers','webinarLeads','workshopCustomers','workshopLeads'));
    }

    public function logout(){
        Session::flush();
        Auth::logout();
        return redirect('/');
    }

    public function changePassword(){
        return view('dashboard::layouts.change-password');
    }

    public function updatePassword(Request $request){
        $user = auth()->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed',
        ]);
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(array('type' => 'ERROR', 'message' => 'Old password does not match!', 'data' => []));
        }
        $result = $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        if($result){
            return response()->json(array('type' => 'SUCCESS', 'message' => 'Password updated successfully!', 'data' => []));
        } else {
            return response()->json(array('type' => 'ERROR', 'message' => 'Oops! Something went wrong.', 'data' => []));
        }
    }
}
