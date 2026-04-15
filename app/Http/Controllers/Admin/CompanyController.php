<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function index()
{
    $companies = Company::withCount('rentals')
        ->orderBy('name')
        ->get()
        ->map(function ($c) {
            return [
                'id'         => $c->id,
                'name'       => $c->name,
                'owner'      => $c->owner_name,
                'email'      => $c->email,
                'phone'      => $c->phone,
                'address'    => $c->address,
                'status'     => $c->status,
                'properties' => $c->rentals_count ?? 0,
                'login_email' => optional($c->lessors()->first())->email,
            ];
        });

    return response()->json($companies);
}

    public function store(Request $request)
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'owner'       => 'required|string|max:255',
        'email'       => 'required|email|max:255|unique:companies,email',
        'phone'       => 'required|string|max:50',
        'address'     => 'required|string',
        'status'      => 'required|in:active,inactive',
        'login_email' => 'required|email|max:255|unique:users,email',
        'password'    => 'required|string|min:6',
    ]);

    $company = null;

    DB::transaction(function () use (&$company, $data) {
        // 1) Create company
        $company = Company::create([
            'name'       => $data['name'],
            'owner_name' => $data['owner'],   // CHANGE to 'owner' if your column is 'owner'
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'status'     => $data['status'],
        ]);

        // 2) Create lessor user attached to this company
        User::create([
            'name'       => $data['owner'],
            'email'      => $data['login_email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'lessor',
            'company_id' => $company->id,
        ]);
    });

    return response()->json([
        'success' => true,
        'company' => $company->fresh(),
    ]);
}


    public function update(Request $request, Company $company)
{
    $primaryLessor = $company->lessors()->first();

    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'owner'       => 'required|string|max:255',
        'email'       => 'required|email|max:255|unique:companies,email,' . $company->id,
        'phone'       => 'required|string|max:50',
        'address'     => 'required|string',
        'status'      => 'required|in:active,inactive',
        'login_email' => 'required|email|max:255'
            . ($primaryLessor ? '|unique:users,email,' . $primaryLessor->id : '|unique:users,email'),
        'password'    => 'nullable|string|min:6',
    ]);

    DB::transaction(function () use ($company, $data, $primaryLessor) {
        // 1) Update company
        $company->update([
            'name'       => $data['name'],
            'owner_name' => $data['owner'],  // or 'owner'
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'status'     => $data['status'],
        ]);

        // 2) Update or create lessor user
        if ($primaryLessor) {
            $primaryLessor->name  = $data['owner'];
            $primaryLessor->email = $data['login_email'];

            if (!empty($data['password'])) {
                $primaryLessor->password = Hash::make($data['password']);
            }

            $primaryLessor->save();
        } else {
            User::create([
                'name'       => $data['owner'],
                'email'      => $data['login_email'],
                'password'   => Hash::make($data['password'] ?? 'changeme123'),
                'role'       => 'lessor',
                'company_id' => $company->id,
            ]);
        }
    });

    return response()->json([
        'success' => true,
        'company' => $company->fresh(),
    ]);
}


    public function destroy(Company $company)
{
    $company->delete();

    return response()->json(['success' => true]);
}
}
