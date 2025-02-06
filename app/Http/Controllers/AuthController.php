<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\EmployeeAvailability;
use App\Models\Member;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AuthController extends Controller
{
  // ----------------- MEMBER AUTH --------------------- //
    public function MemberRegister(Request $request){

        sleep(1);
        // VALIDATE
        $fields = $request->validate([
            'firstName' => ['required', 'max:30'],
            'lastName' => ['required', 'max:30'],
            'email' => ['required', 'max:60', 'unique:members'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $fields['password'] = Hash::make($fields['password']);
        // REGISTER
        $user = Member::create($fields);

        // LOGIN
        Auth::guard('members')->login($user);


        // REDIRECT
        return redirect()->route('MemberDashboardBooking');
    }

    public function MemberLogin(Request $request){
        sleep(1);

        $fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        if (Auth::guard('members')->attempt($fields)) {
            $request->session()->regenerate();
            return redirect()->route('MemberDashboardBooking');
        }
    
        return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function MemberUpdate(Request $request, $id)
    {
        sleep(1);

        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric',
            'dateOfBirth' => 'nullable|date',
        ]);

        $member->update($validated);

        return redirect()->route('MemberDashboardProfile')->with('success', 'Profile updated successfully.');
    }

    public function MemberPasswordUpdate(Request $request, $id)
    {
        sleep(1);

        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if the current password matches
        if (!Hash::check($validated['current_password'], $member->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $member->password = Hash::make($validated['new_password']);
        $member->save();

        return redirect()->route('MemberDashboardProfile')->with('success', 'Password updated successfully.');
    }

    public function indexMember(Request $request)
  {
    $members = $request->user('members'); // Get the authenticated member

    return Inertia::render('FixFinderMember/MemberDashboardProfile', ['members' => $members]);
  }

  public function indexMemberBooking()
  {
    $services = Service::all(); // Fetch all available services
    $employees = Employee::all();
    $authMember = Auth::guard('members')->user();
    
    return Inertia::render('FixFinderMember/MemberDashboardBooking', [
      'employees' => $employees,
      'services' => $services,
      'auth' => ['user' => $authMember],
    ]);
  }

    // ------------------- EMPLOYEE AUTH ------------------- //
    public function EmployeeRegister(Request $request){

      sleep(1);
      // VALIDATE
      $fields = $request->validate([
          'firstName' => ['required', 'max:30'],
          'lastName' => ['required', 'max:30'],
          'email' => ['required', 'max:60', 'unique:employees'],
          'password' => ['required', 'min:8', 'confirmed'],
      ]);

      $fields['password'] = Hash::make($fields['password']);
      // REGISTER
      $user = Employee::create($fields);

      // LOGIN
      Auth::guard('employees')->login($user);


      // REDIRECT
      return redirect()->route('EmployeeDashboardManage');
  }

  public function EmployeeLogin(Request $request){
    sleep(1);

      $fields = $request->validate([
          'email' => ['required', 'email'],
          'password' => ['required'],
      ]);
  
      if (Auth::guard('employees')->attempt($fields)) {
          $request->session()->regenerate();
          return redirect()->route('EmployeeDashboardManage');
      }
  
      return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
      ])->onlyInput('email');
  }

  public function EmployeeUpdate(Request $request, $id)
    {
        sleep(1);

        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric',
            'dateOfBirth' => 'nullable|date',
            'coverageArea' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'service_ID' => 'nullable|exists:services,service_ID',
            'available' => 'nullable|date',
            'available_Time' => 'nullable|time'
        ]);

        $employee->update($validated);

        return redirect()->route('EmployeeDashboardProfile')->with('success', 'Profile updated successfully.');
    }

    public function EmployeePasswordUpdate(Request $request, $id)
    {
        sleep(1);

        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if the current password matches
        if (!Hash::check($validated['current_password'], $employee->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $employee->password = Hash::make($validated['new_password']);
        $employee->save();

        return redirect()->route('EmployeeDashboardProfile')->with('success', 'Password updated successfully.');
    }

    public function updateAvailability(Request $request, $employee_ID)
    {
        $availabilityData = $request->input('availability');

        foreach ($availabilityData as $day => $times) {
            EmployeeAvailability::updateOrCreate(
                [
                    'employee_ID' => $employee_ID,
                    'day' => $day,
                ],
                [
                    'start_time' => $times['start'] === 'Closed' ? null : $times['start'],
                    'end_time' => $times['start'] === 'Closed' ? null : $times['end'],
                    'is_closed' => $times['start'] === 'Closed',
                ]
            );
        }
        
        return redirect()->route('EmployeeDashboardProfile', [
            'success' => true,
            'message' => 'Availability updated successfully.',
            // Optionally, pass updated availability data
            'availability' => $this->getAvailability($employee_ID)
        ]);
    }

    public function getAvailability($employee_ID)
    {
        // Fetch availability data from the database
        $availability = EmployeeAvailability::where('employee_ID', $employee_ID)->get();

        // If availability data is empty, you may return default values
        if ($availability->isEmpty()) {
            $defaultAvailability = [
                'Monday' => ['start' => '09:00', 'end' => '17:00'],
                'Tuesday' => ['start' => '09:00', 'end' => '17:00'],
                'Wednesday' => ['start' => '09:00', 'end' => '17:00'],
                'Thursday' => ['start' => '09:00', 'end' => '17:00'],
                'Friday' => ['start' => '09:00', 'end' => '17:00'],
                'Saturday' => ['start' => '', 'end' => ''],
                'Sunday' => ['start' => '', 'end' => ''],
            ];

            return response()->json($defaultAvailability);
        }

        // Organize the data into a structure that fits the Vue template (days, start_time, end_time)
        $structuredAvailability = $availability->mapWithKeys(function ($item) {
            return [
                $item->day => [
                    'start' => $item->start_time,
                    'end' => $item->end_time,
                ]
            ];
        });

        // Return the availability as a structured JSON response
        return response()->json($structuredAvailability);
    }


    // LOGOUT
    public function Logout(Request $request){
        sleep(1);

        Auth::logout();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect()->route('homepage');
    }


    public function serviceAdd(Request $request)
  {
    sleep(1);
    // VALIDATE
    $fields = $request->validate([
      'serviceName' => ['required', 'max:20', 'unique:services'],
    ]);
    
    Service::create($fields);

    return redirect()->to('/AdminDashboardServices')->with('message', 'Service Added...');
  }

  public function indexService()
  {
    $services = Service::all();

    // dd($services);
    
    return Inertia::render('FixFinderAdmin/AdminDashboardServices', ['services' => $services]);
  }

  public function deleteService($service_ID)
{
    $service = Service::findOrFail($service_ID);

    // Now delete the service
    $service->delete();

    return redirect()->back()->with('message', 'Service deleted successfully.');
}

  public function indexServiceEmployee(Request $request)
  {
      $services = Service::all(); // Fetch all available services
      $employee = $request->user('employees'); // Get the authenticated employee

      // Fetch availability data for the employee
      $availability = DB::table('table_employee_availability')
          ->where('employee_ID', $employee->employee_ID)
          ->get()
          ->keyBy('day')
          ->map(function ($item) {
              return [
                  'start' => $item->start_time ?? '',
                  'end' => $item->end_time ?? '',
              ];
          });

      return Inertia::render('FixFinderEmployee/EmployeeDashboardProfile', [
          'services' => $services,
          'employees' => $employee,
          'availability' => $availability, // Pass availability to the frontend
          'auth' => ['user' => $employee],
      ]);
  }

  public function indexServiceEmployeeManage(Request $request)
  {
    $services = Service::all(); // Fetch all available services
    $employee = $request->user('employees'); // Get the authenticated employee
    $bookings = Booking::where('employee_ID', $employee->employee_ID)->with('member')->get();

    return Inertia::render('FixFinderEmployee/EmployeeDashboardManage', [
        'services' => $services,
        'employees' => $employee,
        'bookings' => $bookings,
        'auth' => [
        'user' => $employee,
      ],
    ]);
  }

  public function showViewProfile($employee_ID)
  {
      // Fetch the employee details based on the ID
      $employee = Employee::findOrFail($employee_ID); // Replace 'Employee' with your model name
      $services = Service::all(); // Fetch related services, if necessary
  
      // Pass the data to the Vue component
      return Inertia::render('FixFinderMember/MemberDashboardViewProfile', [
          'employee' => $employee,
          'services' => $services,
          'auth' => [
          'user' => Auth::guard('members')->user(),
          ],
      ]);
  }

//   -------------------- ADMIN MANAGE USERS ------------------------ //
    public function indexAdminManageUsers()
    {
        // Fetch all members or employees, depending on your use case
        $members = Member::all();
        $employee = Employee::all();

        return Inertia::render('FixFinderAdmin/AdminDashboardManageUsers', [
            'member' => $members, // Pass the users data
            'employee' => $employee
        ]);
    }

    public function updateUsers(Request $request, $id)
    {
        // Determine if it's a member or employee
        $userType = $request->input('userType'); // Sent from the frontend
        
        $user = null;

        // Check user type and fetch the appropriate user
        if ($userType === 'member') {
            $user = Member::find($id);
        } elseif ($userType === 'employee') {
            $user = Employee::find($id);
        }

        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        // Validate input
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $userType === 'member'
                    ? Rule::unique('members', 'email')->ignore($id, 'member_ID')
                    : Rule::unique('employees', 'email')->ignore($id, 'employee_ID'),
            ],
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->password);
        }

        // Update user
        $user->update($validatedData);

        // Return a success response (for API-based requests or frontend handling)
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User updated successfully!',
            ], 200);
        }

        // For traditional web-based requests, redirect with success message
        return redirect()->route('AdminDashboardManageUsers')->with('success', 'User updated successfully.');
    }

    public function deleteUser(Request $request, $id)
    {
        $userType = $request->input('userType');

        // Fetch the appropriate user based on userType
        if ($userType === 'member') {
            $user = Member::find($id);
        } elseif ($userType === 'employee') {
            $user = Employee::find($id);
        } else {
            return back()->withErrors(['error' => 'Invalid user type.']);
        }

        // Check if the user exists
        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        // Delete the user
        $user->delete();

        // Return a success response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'User deleted successfully!'], 200);
        }

        // Redirect back with success message
        return redirect()->route('AdminDashboardManageUsers')->with('success', 'User deleted successfully.');
    }


}
