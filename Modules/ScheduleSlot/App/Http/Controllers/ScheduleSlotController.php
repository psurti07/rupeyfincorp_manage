<?php

namespace Modules\ScheduleSlot\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ScheduleSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ScheduleSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                0 => 'ss.id',
                1 => 'ss.updated_at',
                3 => 'c.first_name',
                4 => 'c.email',
                5 => 'c.mobile',
                6 => 'ss.date',
                7 => 'ss.time',
                8 => 'ss.language',
                9 => 'ss.status',
            ];
            $start_date = $request->fromDate ?? NULL;
            $end_date = $request->toDate ?? NULL;
            $status = $request->status ?? NULL;
            $language = $request->language ?? NULL;
            $search = $request->input('search')['value'] ?? NULL;
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'asc');

            $query = ScheduleSlot::from('schedule_slots as ss')
                ->join('user_webinar_registration as c', 'c.id', 'ss.user_id')
                ->select(
                    'ss.id',
                    'ss.user_id',
                    'ss.date',
                    'ss.time',
                    'ss.language',
                    'ss.status',
                    'ss.updated_at',
                    DB::raw('CONCAT(c.first_name, " ", c.last_name) as fullName'),
                    'c.email',
                    'c.mobile',
                )->where('ss.is_deleted', 0);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->Where('c.first_name', 'like', "%{$search}%")
                        ->orWhere('c.last_name', 'like', "%{$search}%")
                        ->orWhere('c.email', 'like', "%{$search}%")
                        ->orWhere('c.mobile', 'like', "%{$search}%")
                        ->orWhere('ss.date', 'like', "%{$search}%")
                        ->orWhere('ss.time', 'like', "%{$search}%");
                });
            }
            if ($status) {
                $query = $query->where('ss.status', $status);
            }

            if ($language) {
                $query = $query->where('ss.language', $language);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date);
                $query = $query->whereRaw('DATE(ss.updated_at) BETWEEN ? AND ?', [$start_date, $end_date]);
            }
            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('ss.updated_at', 'desc');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('updated_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->updated_at));
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('Y-m-d');
                })
                ->editColumn('time', function ($row) {
                    return Carbon::parse($row->time)->format('h:i A');
                })
                ->editColumn('language', function ($row) {
                    return $row->language_text;
                })
                ->editColumn('status', function ($row) {
                    switch ($row->status) {
                        case 1:
                            $status = '<span class="text-info">' . $row->status_text . '</span>';
                            break;
                        case 2:
                            $status = '<span class="text-success">' . $row->status_text . '</span>';
                            break;
                        case 3:
                            $status = '<span class="text-danger">' . $row->status_text . '</span>';
                            break;
                        case 4:
                            $status = '<span class="text-warning">' . $row->status_text . '</span>';
                            break;
                        default:
                            $status = "-";
                            break;
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:;" onclick="openDetailsModal(' . $row->id . ')">
                                <i class="fa fa-info-circle"></i>
                            </a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('scheduleslot::index');
    }

    public function show($id)
    {
        $schedule = ScheduleSlot::from('schedule_slots as ss')
            ->join('user_webinar_registration as c', 'c.id', 'ss.user_id')
            ->select(
                'ss.id',
                'ss.user_id',
                'ss.date',
                'ss.time',
                'ss.language',
                'ss.remarks',
                'ss.status',
                'ss.updated_at',
                DB::raw('CONCAT(c.first_name, " ", c.last_name) as fullName'),
                'c.email',
                'c.mobile',

            )->where('ss.id', $id)
            ->first();
        return view('scheduleslot::modals.info', compact('schedule'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:schedule_slots,id',
                'status' => 'required',
                'remarks' => 'nullable'
            ]);
            $res = ScheduleSlot::where('id', $request->id)->update([
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            if ($res) {
                return response()->json(['type' => 'SUCCESS', 'data' => [], 'message' => 'Updated successfully'], 200);
            } else {
                return response()->json(['type' => 'SUCCESS', 'message' => 'Server is busy right now. Try after some time'], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['type' => 'ERROR', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("update", [$e->getMessage()]);
            return response()->json(['type' => 'ERROR', 'message' => 'Something went wrong'], 422);
        }
    }
}
