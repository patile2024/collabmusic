<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Webkul\Admin\DataGrids\User\UserKpiDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Models\UserKpi;

class UserKpiController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        if (request()->ajax()) {
            return datagrid(UserKpiDataGrid::class)->process();
        }

        return view('admin::user.kpi.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        } else {
            if (strpos(url()->previous(), 'admin') !== false) {
                $intendedUrl = url()->previous();
            } else {
                $intendedUrl = route('admin.dashboard.index');
            }

            session()->put('url.intended', $intendedUrl);

            return view('admin::sessions.login');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = $request->file('file')->getRealPath();

        // Read the Excel file
        $data = FacadesExcel::toArray([], $filePath);

        // Assuming the first sheet is used
        $rows = $data[0];

        // Extract dates from the second row (header row)
        $headers = $rows[0]; // First row is the header
        $dates = array_slice($headers, 1, -1); // Columns 2-31 are dates

        // Process each row for users and KPIs
        foreach (array_slice($rows, 1) as $row) {
            $userName = $row[0]; // First column is the user/channel name

            foreach ($dates as $index => $dateString) {
                $kpi = $row[$index + 1]; // Corresponding KPI value
                if ($kpi !== null) {
                    // Convert the date from the header
                    $date = Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');

                    // Insert into the database
                    UserKpi::create([
                        'user_id' => $userName,
                        'date' => $date,
                        'kpi' => $kpi,
                    ]);
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->guard('user')->logout();

        return redirect()->route('admin.session.create');
    }
}
