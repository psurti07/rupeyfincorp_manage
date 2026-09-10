<?php

namespace App\DataTables;

use App\Models\webinarAttend;
use App\Models\WebinarOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class webinarAttendDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->addColumn('rec_date', function ($row) {
                return Carbon::parse($row->rec_date)->format('d-m-Y') . "<br/>" . Carbon::parse($row->rec_date)->format('h:i:s A');
            })

            ->addColumn('webinar_name', function ($row) {
                return $row->event_title ?? '-';
            })

            ->addColumn('webinar_date', function ($row) {
                return $row->event_datetime ? Carbon::parse($row->event_datetime)->format('d-m-Y') : '-';
            })

            ->addColumn('customer_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->mobile ?? '-';
            })

            ->addColumn('isDnd', function ($row) {

                $statusClass = $row->isDnd == 1
                    ? 'btn-outline-success'
                    : 'btn-outline-danger';

                $content = $row->isDnd == 1
                    ? '<i class="fa fa-check-circle me-1"></i> DND'
                    : '<i class="fa fa-times-circle me-1"></i> Not DND';

                return '<span
                            class="btn btn-xs ' . $statusClass . ' isdnd-btn"
                            style="cursor:pointer;"
                            data-id="' . $row->user_id . '">
                            ' . $content . '
                        </span>';
            })

            ->addColumn('isAttend', function ($row) {
                $isAttend = $row->isAttend;
                $btnClass = $isAttend ? 'btn-success' : 'btn-outline-secondary';
                $btnText  = $isAttend ? 'Attended' : 'Not Attended';

                return '<button 
                            type="button"
                            class="btn btn-sm ' . $btnClass . ' attend-btn"
                            data-id="' . $row->id . '">
                            ' . $btnText . '
                        </button>';
            })

             ->setRowId('id')->rawColumns(['rec_date','isDnd','isAttend']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(WebinarOrder $model): QueryBuilder
    {
        $start_date = $this->request()->get('start_date');
        $end_date = $this->request()->get('end_date');

        $query = $model->newQuery()
            ->from('webinar_order')
            ->select([
                // Webinar Order
                'webinar_order.id',
                'webinar_order.userid',
                'webinar_order.webinar_id',
                'webinar_order.amount',
                'webinar_order.orderid',
                'webinar_order.isAttend',
                'webinar_order.isUser',
                'webinar_order.isActive',
                'webinar_order.rec_date',

                // User Details
                'user_webinar_registration.id as user_id',
                'user_webinar_registration.first_name',
                'user_webinar_registration.last_name',
                'user_webinar_registration.email',
                'user_webinar_registration.mobile',
                'user_webinar_registration.city',
                'user_webinar_registration.state',
                'user_webinar_registration.pincode',
                'user_webinar_registration.occupation',
                'user_webinar_registration.earning_goal',
                'user_webinar_registration.process_step',
                'user_webinar_registration.is_joincommunity',
                'user_webinar_registration.isDnd',

                // Event Details
                'webinar_event.id as event_id',
                'webinar_event.event_name',
                'webinar_event.event_title',
                'webinar_event.event_datetime',
                'webinar_event.event_main_price',
                'webinar_event.event_offer_price',
                'webinar_event.mentor_name',
                'webinar_event.language',
                'webinar_event.program_type',
                'webinar_event.link',
                'webinar_event.community_link',
            ])
            ->leftJoin(
                'user_webinar_registration',
                'user_webinar_registration.id',
                '=',
                'webinar_order.userid'
            )
            ->leftJoin(
                'webinar_event',
                'webinar_event.id',
                '=',
                'webinar_order.webinar_id'
            )
            ->where('user_webinar_registration.isDelete', 0)
            ->where('user_webinar_registration.isActive', 1)
            ->where('webinar_order.isDelete', 0)
            ->where('webinar_order.isUser', 2)
            ->where('webinar_order.isActive', 1)
            ->orderByDesc('webinar_order.id');

        if (!empty($start_date) && !empty($end_date)) {
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);
            $query = $query->whereRaw('DATE(webinar_order.rec_date) BETWEEN ? AND ?', [$start_date, $end_date]);
        } else {
            $start_date = date('Y-m-d', strtotime('-2 days'));
            $end_date = date('Y-m-d');
            $query = $query->whereRaw('DATE(webinar_order.rec_date) BETWEEN ? AND ?', [$start_date, $end_date]);
        }
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('webinarattend-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->orderable(false)->width(50),
            Column::make('rec_date')->title('Reg. Date')->searchable(false),
            Column::make('webinar_name')->title('Event Name')->width(200),
            Column::make('webinar_date')->title('Event Date')->width(100),
            Column::make('customer_name')->title('Customer Name')->width(150)->searchable(true),
            Column::make('customer_mobile')->title('Mobile')->width(120)->searchable(true),
            Column::make('isDnd')->title('DND Status')->width(120)->orderable(false)->searchable(false),
            Column::make('isAttend')->title('Attend Status')->width(120)->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'webinarAttend_' . date('YmdHis');
    }
}
