<?php

namespace App\DataTables;

use App\Models\WebinarEvent;
use App\Models\WebinarEventDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WebinarEventDetailsDataTable extends DataTable
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

            ->editColumn('event_name', function ($row) {
                return $row->program_type == 0 ? 'Webinar' : 'Workshop';
            })

            ->editColumn('event_type', function ($row) {
                return $row->event_type == 0 ? 'Online' : 'Workshop';
            })

            ->addColumn('details', function ($row) {
                return '<a href="javascript:void(0);" onclick="openEditModal(' . $row->id . ')"  class="btn btn-icon btn-outline-dark btn-sm"> <i class="fa fa-pencil"></i> </a';
            })

            ->addColumn('date', function ($row) {
                return Carbon::parse($row->event_datetime)->format('d-m-Y') . "<br>" .
                    Carbon::parse($row->event_datetime)->format('h:i A');
            })

            ->editColumn('event_main_price', function ($row) {
                return number_format($row->event_main_price, 2);
            })

            ->editColumn('event_offer_price', function ($row) {
                return number_format($row->event_offer_price, 2);
            })

            ->addColumn('image', function ($row) {
                return '<img src="' . asset("webinar/images/webinarpage/" . $row->event_image) . '" width="200" height="120">';
            })

            ->editColumn('isActive', function ($row) {
                $cls = $row->isActive ? 'btn-outline-success' : 'btn-outline-danger';
                $txt = $row->isActive ? 'Active' : 'Inactive';
                return '<span class="btn btn-xs '.$cls.'" onclick="toggleWebinarStatus('.$row->id.')">'.$txt.'</span>';
            })

            ->setRowId('id')
            ->rawColumns(['details', 'date', 'image', 'isActive']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(WebinarEvent $model): QueryBuilder
    {

        $query = $model->newQuery()
            ->where('isDelete', 0)
            ->orderByDesc('id');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('webinarEventDetails-table')
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
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false),
            Column::make('date')->title('Event Date'),
            Column::make('event_type')->title('Event Type'),
            Column::make('event_name')->title('Event Name'),
            Column::make('event_main_price')->title('Event Price'),
            Column::make('event_offer_price')->title('Event Offer Price'),
            Column::make('event_title')->title('Event Title'),
            Column::make('mentor_name')->title('Mentor Name'),
            Column::make('language')->title('Language'),
            Column::computed('image')->title('Image'),
            Column::make('isActive')->title('Status'),
            Column::computed('details')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'WebinarEventDetails_' . date('YmdHis');
    }
}
