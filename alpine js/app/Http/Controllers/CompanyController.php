<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\CompanyUpdateRequest;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::orderBy('id', 'desc')->get();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        if (!$request->image) {
            return response()->json(['message' => 'Missing file'], 422);
        }
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/img/companies', $imageName);
        $company = Company::create([
            'name' => $request->name,
            'image' => 'http://localhost:8000/storage/img/companies/' . $imageName
        ]);
        return response($company);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id);
        return response([
            "results" => "1",
            "message" =>"success",
            "data" => $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyUpdateRequest $request, $id)
    {
        $company = Company::find($id);

        if ($request->image) {
            $arr = explode('/', $company->image);

            if (File::exists(storage_path('app/public/img/companies/') . $arr[6])) {
                File::delete(storage_path('app/public/img/companies/') . $arr[6]);
            }

            $imageName = time() . '.' . $request->image->extension();

            $request->image->storeAs('public/img/companies', $imageName);
            $company->image = 'http://localhost:8000/storage/img/companies/' . $imageName;
        }
        $company->name = $request->name;
        $company->save();
        return response($company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $arr = explode('/', $company->image);
        if (File::exists(storage_path('app/public/img/companies/') . $arr[6])) {
            File::delete(storage_path('app/public/img/companies/') . $arr[6]);
        }
        $company->delete();
        return response($company);
    }
}