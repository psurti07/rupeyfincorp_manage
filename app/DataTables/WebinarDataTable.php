<?php

namespace App\DataTables;

use App\Models\WebinarLead;
use App\Models\WebinarRegistration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WebinarLeadsDataTable extends DataTable
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
            ->addColumn('action', function ($row) {
                return '
                    <ul class="action">
                        <li>
                            <a href="javascript:;" onclick="viewDetails(' . $row->id . ')">
                                <i class="icon-info-alt text-info"></i>
                            </a>
                        </li>
                        <li class="delete">
                            <a href="javascript:;" onclick="deleteLead(' . $row->user_id . ')">
                                <i class="icon-trash"></i>
                            </a>
                        </li>
                    </ul>
                ';
            })
            ->addColumn('date', function ($row) {
                return Carbon::parse($row->rec_date)->format('d-m-Y') . "<br/>" . Carbon::parse($row->rec_date)->format('h:i:s A');
            })
            ->addColumn('full_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->editColumn('email', function ($row) {
                return '<div style="width:100px;">'.$row->email.'</div>';
            })
            ->setRowId('id')->rawColumns(['action', 'date', 'full_name','email']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(WebinarRegistration $model): QueryBuilder
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

                // Event Details
                'webinar_event.id as event_id',
                'webinar_event.event_name',
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
            ->where('webinar_order.isDelete', 0)
            ->where('webinar_order.isUser', 1)
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
            ->setTableId('WebinarLeads-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->pageLength(100)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ])->parameters([
                'responsive' => true,
                'lengthMenu' => [[100, 250, 500, -1], [100, 250, 500, 'All']],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(5)->responsivePriority(1),
            Column::make('date')->title('Date')->searchable(false)->responsivePriority(2),
            Column::make('full_name')->data('full_name')->title('Full Name')->responsivePriority(3),
            Column::make('mobile')->data('mobile')->title('Mobile')->responsivePriority(4),
            Column::make('email')->data('email')->title('Email')->responsivePriority(5),
            Column::make('city')->data('city')->title('City')->responsivePriority(100),
            Column::make('state')->data('state')->title('State')->responsivePriority(101),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')->responsivePriority(6),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'WebinarLeads_' . date('YmdHis');
    }
}
