<?php

namespace Modules\SearchData\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchDataController extends Controller
{
    public function index()
    {
        return view('searchdata::index');
    }

    public function searchData(Request $request)
    {
        try {

            $validated = $request->validate([
                'module' => 'required|in:1,2,3',
                'mobile_no' => ['required', 'numeric', 'regex:/^[6-9]\d{9}$/'],
            ]);

            if ($validated['module'] == 4) {
                $userData = DB::table('user_webinar_registration as uwr')
                    ->join('webinar_order as wo', 'wo.userid', '=', 'uwr.id')
                    ->join('webinar_event as we', 'we.id', '=', 'wo.webinar_id')
                    ->select(
                        'uwr.*',
                        'wo.id as order_id',
                        'wo.webinar_id',
                        'wo.amount',
                        'wo.paymentid',
                        'wo.orderid',
                        'wo.isAttend',
                        'wo.rec_date as order_date',
                        'we.*',
                    )
                    ->where('uwr.mobile', $validated['mobile_no'])
                    ->where('uwr.isDelete', 0)
                    ->where('wo.isDelete', 0)
                    ->where('we.program_type', 0)
                    ->where('we.isDelete', 0)
                    ->get();
            }

            if ($validated['module'] == 5) {
                $userData = DB::table('user_webinar_registration as uwr')
                    ->join('webinar_order as wo', 'wo.userid', '=', 'uwr.id')
                    ->join('webinar_event as we', 'we.id', '=', 'wo.webinar_id')
                    ->select(
                        'uwr.*',
                        'wo.id as order_id',
                        'wo.webinar_id',
                        'wo.amount',
                        'wo.paymentid',
                        'wo.orderid',
                        'wo.isAttend',
                        'wo.rec_date as order_date',
                        'we.*',
                    )
                    ->where('uwr.mobile', $validated['mobile_no'])
                    ->where('uwr.isDelete', 0)
                    ->where('wo.isDelete', 0)
                    ->where('we.program_type', 1)
                    ->where('we.isDelete', 0)
                    ->get();
            }

            if (in_array($validated['module'], [1, 2, 3])) {
                $userData = UserRegistration::where('mobile', $validated['mobile_no'])
                    ->where('acc_type', $validated['module'])->where('isDelete', 0)
                    ->first();
            }

            $module = $validated['module'];
            $dataHtml = view('searchdata::data_list', compact('userData', 'module'))->render();
            $data = '';

            if (in_array($module, [4, 5])) {

                if ($userData->isNotEmpty()) {
                    return response()->json([
                        'type' => 'SUCCESS',
                        'data' => '',
                        'html' => $dataHtml
                    ]);
                }
            } else {
                $data = $userData->id;
                return response()->json(['type' => 'SUCCESS', 'data' => $data, 'html' => $dataHtml]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['type' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['type' => 'ERROR', 'message' => 'User not found!', 'data' => ''], 200);
        }
    }
}
