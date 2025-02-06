<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
{
    $employeeId = Auth::guard('employees')->id(); // Get authenticated employee's ID
    $bookings = Booking::with('member') // Eager load the 'member' relationship
                        ->select('booking_ID', 'member_ID', 'employee_ID', 'statusBooking', 'bookingDate', 'bookingTime', 'location', 'message')
                        ->where('employee_ID', $employeeId)
                        ->get(); // Adjust query as needed

    if ($request->header('X-Inertia')) {
        return Inertia::render('FixFinderMember/MemberDashboardBooking', [
            'bookings' => $bookings
        ]);
    }

    return response()->json([
        'success' => true,
        'data' => $bookings,
    ]);
}


    public function create(Request $request)
{
    // Validate incoming request data
    $request->validate([
        'member_ID' => 'required|exists:members,member_ID',
        'employee_ID' => 'required|exists:employees,employee_ID',
        'statusBooking' => 'nullable|in:pending,completed,confirmed,cancelled', // Allow optional status, default to 'pending'
        'bookingDate' => 'required|date',
        'bookingTime' => 'required|date_format:H:i',
        'message' => 'nullable|string',
        'location' => 'nullable|string',
    ]);

    // Default statusBooking to 'pending' if not provided
    $statusBooking = $request->statusBooking ?: 'pending';

    // Extract member_ID and employee_ID from the request
    $memberID = $request->member_ID;
    $employeeID = $request->employee_ID;

    // Check if an active booking exists for this member and the SAME employee
    $exists = DB::table('bookings')
        ->where('member_ID', $memberID)
        ->where('employee_ID', $employeeID)
        ->whereNotIn('statusBooking', ['completed', 'cancelled'])
        ->exists();

    if ($exists) {
        $errorResponse = ['error' => 'An active booking already exists for this member and employee.'];

        if ($request->wantsJson()) {
            return response()->json($errorResponse, 422);
        }

        return back()->withErrors($errorResponse);
    }

    // Proceed with storing the new booking
    $booking = Booking::store([
        'member_ID' => $memberID,
        'employee_ID' => $employeeID,
        'bookingDate' => $request->bookingDate,
        'bookingTime' => $request->bookingTime,
        'message' => $request->message,
        'location' => $request->location,
        'statusBooking' => $statusBooking, // Use the default or provided status
    ]);

    if ($request->wantsJson()) {
        return response()->json(['message' => 'Booking created successfully!', 'data' => $booking], 201);
    }

    return back()->with('success', 'Booking created successfully!');
}


    public function updateStatus(Request $request, $booking_ID)
    {
        // Validate the incoming request
        $request->validate([
            'statusBooking' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        // Find the booking by ID
        $booking = Booking::where('booking_ID', $booking_ID)->firstOrFail();

        // Ensure status transitions are valid
        if ($booking->statusBooking === 'completed' && $request->input('statusBooking') === 'pending') {
            return response()->json(['error' => 'Cannot revert booking from completed to pending.'], 400);
        }

        $booking->statusBooking = $request->input('statusBooking');
        $booking->save();

        return response()->json(['message' => 'Booking status updated successfully.']);
    }


    public function getStatus($booking_ID)
    {
        $booking = Booking::find($booking_ID);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
    
        // If booking exists, return the status
        return response()->json(['status' => $booking->statusBooking]);
    }    

    public function confirmBooking($booking_ID)
    {
        // Find the booking by ID
        $booking = Booking::where('booking_ID', $booking_ID)->firstOrFail();

        // Ensure the booking status is 'pending' before confirming
        if ($booking->statusBooking === 'pending') {
            $booking->statusBooking = 'confirmed';  // Update the status to 'confirmed'
            $booking->save();

            return response()->json(['message' => 'Booking confirmed successfully!']);
        }

        return response()->json(['error' => 'Booking cannot be confirmed as it is not in pending status.'], 400);
    }

    public function destroy($id)
    {
        // Use `findOrFail` to handle non-existent bookings gracefully
        $booking = Booking::findOrFail($id);

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }
}
