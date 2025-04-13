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
        $search = request('search');

        $company->load(['users' => function ($query) use ($search) {
            if ($search) {
                $query->where(fn($q) =>
                    $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                );
            }

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
            ->with('success', __('messages.company_user_update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, User $user)
    {
        $company->users()->detach($user);
        return back()->with('success', __('messages.company_user_delete'));
    }
}
