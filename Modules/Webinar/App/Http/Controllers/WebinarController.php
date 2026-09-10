<?php

namespace Modules\Webinar\App\Http\Controllers;

use App\DataTables\CustomerDataTable;
use App\DataTables\webinarAttendDataTable;
use App\DataTables\WebinarDataTable;
use App\DataTables\WebinarEventDetailsDataTable;
use App\DataTables\WebinarLeadsDataTable;
use App\DataTables\webinarOnboardDetailsDataTable;
use App\DataTables\workshopCustomerDataTable;
use App\DataTables\workshopLeadsDataTable;
use App\Http\Controllers\Controller;
use App\Models\WebinarEvent;
use App\Models\WebinarOrder;
use App\Models\WebinarRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class WebinarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WebinarDataTable $dataTable)
    {
        return $dataTable->render('webinar::index');
    }

    public function webinarEventDeleteCustomer($id)
    {
        $customer = WebinarRegistration::find($id);

        if (!$customer) {
            return response()->json(['status' => false]);
        }

        $customer->isdelete = 1;
        $customer->save();

        return response()->json(['status' => true]);
    }

    public function webinarLeads(WebinarLeadsDataTable $dataTable)
    {
        return $dataTable->render('webinar::webinarLeads');
    }

    public function webinarEventDeleteLead($id)
    {
        $customer = WebinarRegistration::find($id);

        if (!$customer) {
            return response()->json(['status' => false]);
        }

        $customer->isdelete = 1;
        $customer->save();

        return response()->json(['status' => true]);
    }

    public function webinarEventDetails(WebinarEventDetailsDataTable $dataTable)
    {
        return $dataTable->render('webinar::webinarEventDetails');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function webinarEventCreate()
    {
        return view('webinar::modals.webinarEventCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function webinarEventStore(Request $request)
    {
        $request->validate([
            'eventdate' => 'required',
            'program_type'   => 'required|in:0,1',
            'eventname' => 'required',
            'event_title' => 'required',
            'mainprice' => 'required|numeric',
            'offerprice' => 'required|numeric',
            'event_mentor' => 'required',
            'event_lag' => 'required',
            'event_desc' => 'required',
            'event_image' => 'required|image|mimes:jpg,jpeg,png',
            'link' => 'required',
            'community_link' => 'required',
        ]);

        // Image Upload
        $imageName = 'register-img.png';

        if ($request->hasFile('event_image')) {
            $imageName = time() . '.' . $request->event_image->extension();
            $request->event_image->move(public_path('webinar/images/webinarpage'), $imageName);
        }

        WebinarEvent::create([
            'event_name' => $request->eventname,
            'event_datetime' => $request->eventdate,
            'event_main_price' => $request->mainprice,
            'event_offer_price' => $request->offerprice,
            'event_title' => $request->event_title,
            'event_desc_1' => $request->event_desc,
            'event_image' => $imageName,
            'mentor_name' => $request->event_mentor,
            'language' => $request->event_lag,
            'program_type' => $request->program_type,
            'link' => $request->link,
            'community_link' => $request->community_link,
            'isActive' => 1,
            'isDelete' => 0
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Event Created Successfully'
        ]);
    }


    public function webinarEventEdit($id)
    {
        $event = WebinarEvent::findOrFail($id);
        return view('webinar::modals.webinarEventEdit', compact('event'));
    }

    public function webinarEventUpdate(Request $request, $id)
    {
        $event = WebinarEvent::findOrFail($id);

        $request->validate([
            'eventdate' => 'required',
            'program_type'   => 'required|in:0,1',
            'eventname' => 'required',
            'event_title' => 'required',
            'mainprice' => 'required|numeric',
            'offerprice' => 'required|numeric',
            'event_mentor' => 'required',
            'event_lag' => 'required',
            'event_desc' => 'required',
            'link' => 'required',
            'community_link' => 'required',
        ]);

        $imageName = $event->event_image;

        if ($request->hasFile('event_image')) {
            $imageName = time() . '.' . $request->event_image->extension();
            $request->event_image->move(public_path('webinar/images/webinarpage'), $imageName);
            $event->event_image = $imageName;
        }

        $event->update([
            'event_name' => $request->eventname,
            'event_datetime' => $request->eventdate,
            'event_main_price' => $request->mainprice,
            'event_offer_price' => $request->offerprice,
            'event_title' => $request->event_title,
            'event_desc_1' => $request->event_desc,
            'event_image' => $imageName,
            'mentor_name' => $request->event_mentor,
            'language' => $request->event_lag,
            'program_type' => $request->program_type,
            'link' => $request->link,
            'community_link' => $request->community_link,
        ]);

        return response()->json([
            'message' => 'Event Updated Successfully'
        ]);
    }

    public function webinarOnboardDetails(webinarOnboardDetailsDataTable $dataTable)
    {
        return $dataTable->render('webinar::webinarOnboardDetails');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function workshopCustomer(workshopCustomerDataTable $dataTable)
    {
        return $dataTable->render('webinar::workshopCustomer');
    }

    public function workshopDeleteCustomer($id)
    {
        $customer = WebinarRegistration::find($id);

        if (!$customer) {
            return response()->json(['status' => false]);
        }

        $customer->isdelete = 1;
        $customer->save();

        return response()->json(['status' => true]);
    }

    public function workshopLeads(workshopLeadsDataTable $dataTable)
    {
        return $dataTable->render('webinar::workshopLeads');
    }

    public function workshopDeleteLead($id)
    {
        $customer = WebinarRegistration::find($id);

        if (!$customer) {
            return response()->json(['status' => false]);
        }

        $customer->isdelete = 1;
        $customer->save();

        return response()->json(['status' => true]);
    }

    public function webinarJoinCommunity($id)
    {
        try {
            $user = WebinarRegistration::findOrFail($id);
            $user->is_joincommunity = !$user->is_joincommunity;
            $user->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'User status updated successfully',
                'status' => $user->isJoinCommunity
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $webinar = WebinarEvent::findOrFail($id);
            $webinar->isActive = !$webinar->isActive;
            $webinar->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'Webinar status updated successfully',
                'status' => $webinar->isActive
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function showLeads($id)
    {
        $records = DB::table('user_webinar_registration as uwr')
            ->leftJoin('webinar_order as wo', function ($join) {
                $join->on('uwr.id', '=', 'wo.userid')
                    ->where('wo.isUser', '!=', 2)
                    ->where('wo.isDelete', 0);
            })
            ->leftJoin('webinar_event as w', 'w.id', '=', 'wo.webinar_id')
            ->select(
                'uwr.*',
                'uwr.rec_date as user_rec_date',

                'wo.id as order_id',
                'wo.webinar_id',
                'wo.amount',
                'wo.paymentid',
                'wo.orderid',
                'wo.isAttend',
                'wo.isActive',
                'wo.rec_date as order_date',

                'w.event_title',
                'w.event_datetime',
                'w.mentor_name',
                'w.language',
                'w.event_image'
            )
            ->where('wo.id', $id)
            ->where('uwr.isDelete', 0)
            ->orderBy('wo.rec_date', 'desc')
            ->get();

        if ($records->isEmpty()) {
            return view('webinar::modals.webinarleadDetails', [
                'user' => null,
                'orders' => collect(),
            ]);
        }

        return view('webinar::modals.webinarleadDetails', [
            'user'   => $records->first(),
            'orders' => $records,
        ]);
    }

    public function accDeactivate($id)
    {
        try {
            $user = WebinarRegistration::find($id);

            if (!$user) {
                return response()->json([
                    'type' => 'ERROR',
                    'message' => 'User not found.'
                ], 404);
            }

            if ($user->isActive == 0) {
                return response()->json([
                    'type' => 'SUCCESS',
                    'message' => 'User is already deactivated.'
                ]);
            }

            $user->isActive = 0;
            $user->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'Account deactivated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::info('Account Webinar Users deactivate error ' . $e->getMessage());
            return response()->json(['type' => 'ERROR', 'message' => 'Something went wrong.']);
        }
    }

    public function accDelete($id)
    {
        try {
            $user = WebinarRegistration::find($id);

            if (!$user) {
                return response()->json([
                    'type' => 'ERROR',
                    'message' => 'User not found.'
                ], 404);
            }

            if ($user->isDelete == 1) {
                return response()->json([
                    'type' => 'SUCCESS',
                    'message' => 'User is already Delete.'
                ]);
            }

            $user->isDelete = 1;
            $user->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'Account Deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::info('Account Webinar Users Fintech delete error ' . $e->getMessage());
            return response()->json(['type' => 'ERROR', 'message' => 'Something went wrong.']);
        }
    }

    public function showcustomer($id)
    {
        $records = DB::table('user_webinar_registration as uwr')
            ->leftJoin('webinar_order as wo', function ($join) {
                $join->on('uwr.id', '=', 'wo.userid')
                    ->where('wo.isUser', '!=', 1)
                    ->where('wo.isDelete', 0);
            })
            ->leftJoin('webinar_event as w', 'w.id', '=', 'wo.webinar_id')
            ->select(
                'uwr.*',
                'uwr.rec_date as user_rec_date',

                'wo.id as order_id',
                'wo.webinar_id',
                'wo.amount',
                'wo.paymentid',
                'wo.orderid',
                'wo.isAttend',
                'wo.isActive',
                'wo.rec_date as order_date',

                'w.event_title',
                'w.event_datetime',
                'w.mentor_name',
                'w.language',
                'w.event_image'
            )
            ->where('wo.id', $id)
            ->where('uwr.isDelete', 0)
            ->orderBy('wo.rec_date', 'desc')
            ->get();

        if ($records->isEmpty()) {
            return view('webinar::modals.webinarCustomerDetails', [
                'user' => null,
                'orders' => collect(),
            ]);
        }

        return view('webinar::modals.webinarCustomerDetails', [
            'user'   => $records->first(),
            'orders' => $records,
        ]);
    }

    public function webinarAttend(webinarAttendDataTable $dataTable)
    {
        return $dataTable->render('webinar::webinarAttendCustomer');
    }

    public function webinarStatus($id)
    {
        try {
            $webinaruser = WebinarOrder::findOrFail($id);
            $webinaruser->isAttend = !$webinaruser->isAttend;
            $webinaruser->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'Webinar User status updated successfully',
                'status' => $webinaruser->isAttend
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function isDndStatus($id)
    {
        try {
            $webinaruser = WebinarRegistration::findOrFail($id);
            $webinaruser->isDnd = !$webinaruser->isDnd;
            $webinaruser->save();

            return response()->json([
                'type' => 'SUCCESS',
                'message' => 'Webinar User status updated successfully',
                'status' => $webinaruser->isDnd
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update status'
            ], 500);
        }
    }
}
