<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\Request as HttpRequest;
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
    public function store()
    {
        // dd(request()->all());
        $this->validate(request(), [
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $filePath = request()->file('file')->getRealPath();

        $data = FacadesExcel::toArray([], $filePath);

        $rows = $data[0];

        // Get the current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Headers contain the days (1, 2, 3, ..., 31)
        $headers = $rows[0];
        $dates = array_slice($headers, 1, -1);

        foreach (array_slice($rows, 1) as $row) {
            $userName = $row[0];

            foreach ($dates as $index => $day) {
                $kpi = $row[$index + 1];
                if ($kpi !== null) {
                    // Create a date string using the current month and year
                    $dateString = sprintf('%02d/%02d/%d', $day, $currentMonth, $currentYear);
                    $date = Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');

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
