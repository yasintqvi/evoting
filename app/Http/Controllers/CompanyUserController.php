<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreCompanyUserRequest;
use App\Http\Requests\User\UpdateCompanyUserRequest;
use App\Models\Election;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        $company = $company->load(['users' => function ($query) {
            $query->latest();
        }]);

        return view('app.company.users.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company)
    {
        $users = User::whereDoesntHave('companies', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        return view('app.company.users.create', compact('company', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyUserRequest $request, Company $company)
    {
        if ($request->has('phone')) {
            $phone = convert_persian_to_english($request->input('phone'));

            $userData = $request->validated();
            $userData['phone'] = $phone;

            $user = User::create($userData);

            $company->users()->attach($user->id);

            return redirect()->route('company.users.index', $company->slug)
                ->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه اضافه شد.');
        } else {
            $company->users()->syncWithoutDetaching($request->input('user_ids'));

            return redirect()->route('company.users.index', $company->slug)
                ->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه اضافه شد.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, User $user)
    {
        return view('app.company.users.edit', compact('company', 'user'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyUserRequest  $request, Company $company, User $user)
    {
        $userData = $request->validated();
        $user->update($userData);

        return redirect()->route('company.users.index', $company->slug)
            ->with('success', 'ویرایش جدید با موفقیت ایجاد و به گروه اضافه شد.');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
