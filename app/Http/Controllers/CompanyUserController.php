<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreCompanyUserRequest;
use App\Http\Requests\User\UpdateCompanyUserRequest;
use App\Models\Election;
use App\Models\Company;
use App\Models\User;
use Hash;
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

        $stockWeight = $company->prefered_stock_weight;

        return view('app.company.users.create', compact('company', 'users' , 'stockWeight'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyUserRequest $request, Company $company)
    {   
        $phone = convert_persian_to_english($request->input('phone'));
        $nationalcode = convert_persian_to_english($request->input('nationalcode'));
        
        $userData = $request->validated();
        $userData['phone'] = $phone;
        $userData['nationalcode'] = $nationalcode;
        $userData['password'] = Hash::make($nationalcode);

        $user = User::create($userData);

        $pivotData = [];

        if ($company->type == \App\Enums\CompanyType::SPECIAL) {
            $pivotData = [
                'normal_stock_count' => $request->input('normal_stock_count', 0), 
                'prefered_stock_count' => $request->input('prefered_stock_count', 0),
            ];
        }
        $company->users()->attach($user->id, $pivotData);          

        return redirect()->route('company.users.index', $company->slug)
            ->with('success', 'کاربر جدید با موفقیت ایجاد و به شرکت اضافه شد.');
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
        $userStock = $company->users()->where('user_id', $user->id)->first()->pivot;
        return view('app.company.users.edit', compact('company', 'user', 'userStock'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyUserRequest $request, Company $company, User $user)
    {
        $nationalcode = convert_persian_to_english($request->input('nationalcode'));
    
        $userData = $request->validated();
        $userData['nationalcode'] = $nationalcode;
    
        if ($user->nationalcode !== $nationalcode) {
            $userData['password'] = Hash::make($nationalcode);
        }
    
        $user->update($userData);
    
        if ($company->type == \App\Enums\CompanyType::SPECIAL) {
            $pivotData = [
                'normal_stock_count' => $request->input('normal_stock_count', 0),
                'prefered_stock_count' => $request->input('prefered_stock_count', 0),
            ];
        }
        $company->users()->updateExistingPivot($user->id, $pivotData);
        
        return redirect()->route('company.users.index', $company->slug)
            ->with('success', 'اطلاعات کاربر با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
