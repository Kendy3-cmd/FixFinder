<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TotalController extends Controller
{

    public function total()
    {
        $bookings = \App\Models\Booking::count();
        $members = \App\Models\Member::count();
        $employees = \App\Models\Employee::count();
        $services = \App\Models\Service::count();

        return Inertia::render('FixFinderAdmin/AdminDashboardStatistics', [
            'bookings' => $bookings,
            'members' => $members,
            'employees' => $employees,
            'services' => $services,
        ]);
    }
}
