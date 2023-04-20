<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Http\Requests\MentorRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = Mentor::all();
        return response()->json([
            'status' => 'success',
            'data' => $mentors
        ]);
    }

    public function show($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mentor
        ]);
    }

    public function create(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'profile' => 'required|url',
            'profession' => 'required|string',
            'email' => 'required|email'
        ];

        // request semua data
        $data =  $request->all();

        // validasi data
        $validator = Validator::make($data, $rules);

        // menampilkan pesan jika validator error 
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // $data =  $request->all();

        // if (!$data()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $validatorerrors()
        //     ], 400);
        // }

        $mentor = Mentor::create($data);

        return response()->json(['status' => 'success', 'data' => $mentor]);
    }

    public function update(Request $request, $id)
    {

        $rules = [
            'name' => 'string',
            'profile' => 'url',
            'profession' => 'string',
            'email' => 'email'
        ];

        // request semua data
        $data =  $request->all();

        // validasi data
        $validator = Validator::make($data, $rules);

        // menampilkan pesan jika validator error 
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }


        $mentor = Mentor::find($id);


        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }


        $mentor->fill($data);
        $mentor->save();

        return response()->json([
            'status' => 'success',
            'data' => $mentor
        ]);
    }

    public function destroy($id)
    {
        $mentor = Mentor::find($id);

        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        $mentor->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'mentor deleted'
        ]);
    }
}
