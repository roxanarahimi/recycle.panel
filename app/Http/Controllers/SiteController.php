<?php

namespace App\Http\Controllers;

use App\Http\Resources\SiteResource;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function show()
    {
        try {
            $data = Site::all()->sortBy('id')->first();
            return response(new SiteResource($data),200);
        } catch (\Exception $exception) {
            return response($exception);
        }
    }

    public function update(Request $request, Site $site)
    {
        $validator = Validator::make($request->all(),
            [
//                'title' => 'required|unique:sites,title,' . $site['id'],
            ],
            [
//                'title.required' => 'لطفا عنوان را وارد کنید',
//                'title.unique' => 'این عنوان قبلا ثبت شده است',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        try {
            $site->update($request->all());
            return response(new SiteResource($site), 200);
        } catch (\Exception $exception) {
            return response($exception);
        }
    }

}
