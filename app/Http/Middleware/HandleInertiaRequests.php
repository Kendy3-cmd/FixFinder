<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
        // Lazily share the authenticated member's details
        'auth.members' => fn () => $request->user('members') 
        ? $request->user('members')->only('member_ID', 'firstName' ,'lastName') 
        : null,

        // Lazily share the authenticated employee's details
        'auth.employees' => fn () => $request->user('employees') 
        ? $request->user('employees')->only('employee_ID', 'firstName' , 'lastName', 'address', 'description', 'coverageArea', 'available', 'dateOfBirth', 'phone', 'created_at', 'service_ID') 
        : null,

        'auth' => [
            'user' => $request->user('members') ?? $request->user('employees'),
        ],
        
        ]);
    }

    public function onExcept()
{
    return [
        '/bookings', // Exclude bookings route
        '/bookings/*', // Include nested routes like /bookings/{id}
    ];
}
}
