<?php

namespace Webkul\Admin\DataGrids\User;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Models\User;

class UserKpiDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('user_kpis')
            ->join('users', 'user_kpis.user_id', '=', 'users.id')
            ->select(
                'user_kpis.id',
                'user_kpis.user_id',
                'user_kpis.date',
                'user_kpis.kpi',
                'users.name as user_name'
            );

        $this->addFilter('id', 'user_kpis.id');
        $this->addFilter('user_id', 'user_kpis.user_id');
        $this->addFilter('date', 'user_kpis.date');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.settings.attributes.index.datagrid.id'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'user_name',
            'label'      => 'User name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'date',
            'label'      => 'Date',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'kpi',
            'label'      => 'KPI',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.attributes.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.attributes.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.attributes.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.attributes.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.settings.attributes.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.attributes.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.attributes.mass_delete'),
        ]);
    }
}
