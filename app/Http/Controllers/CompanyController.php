<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyRequest;
use App\Models\Company;
use App\Services\Image\ImageService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index(Company $company)
    {
        return view('app.company.index', compact('company'));
    }


    public function create()
    {
        return view('app.company.create');
    }

    public function store(CompanyRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService
                ->setImage($request->file('logo'))
                ->setExclusiveDirectory('images/companies')
                ->save();
        }

        $company = user()->ownerCompanies()->create($validated);

        $company->users()->attach(user()->id);

        return back()->with('success', 'شرکت جدید ایجاد شد.');
    }

    public function edit(Company $company)
    {
        return view('app.company.edit', compact('company'));
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService
                ->setImage($request->file('logo'))
                ->setExclusiveDirectory('images/companies')
                ->save();
        }

        $company = $company->update($validated);

        return back()->with('success', __('messages.company_updated'));
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return to_route('app.index');
    }

    public function leave(Company $company)
    {
        $company->users()->detach(user()->id);

        return to_route('app.index');
    }
}
