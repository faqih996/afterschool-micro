<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{

    public function index(Request $request)
    {

        // menampilan data berdasar query
        $chapters = Chapter::query();

        // filter data berdasarkan relation 
        $courseId = $request->query('course_id');

        $chapters->when($courseId, function ($query) use ($courseId) {
            return $query->where('course_id', '=', $courseId);
        });

        // kirim response
        return response()->json([
            'status' => 'success',
            'data' => $chapters->get()
        ]);
    }

    public function show($id)
    {
        // mencari data dari database
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        // show response
        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function create(Request $request)
    {
        // request
        $rules = [
            'name' => 'required|string',
            'course_id' => 'required|integer'
        ];

        // ambil inputan berdasarkan rule
        $data = $request->all();

        // membuat validasi berdasarkan data dan rules
        $validator = Validator::make($data, $rules);

        // validasi data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // memasukan course id
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        // jikda data course tidak ada
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // input kedatabase dan berikan response
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function update(Request $request, $id)
    {
        // request
        $rules = [
            'name' => 'required|string',
            'course_id' => 'required|integer'
        ];

        // ambil inputan berdasarkan rule
        $data = $request->all();

        // membuat validasi berdasarkan data dan rules
        $validator = Validator::make($data, $rules);

        // validasi data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // find data chapter by id
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        // find data course by id
        $courseId = $request->input('courses_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
            }
        }

        // proccess data
        $chapter->fill($data);
        $chapter->save();

        // send respond
        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function destroy($id)
    {
        // mencari data by id
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        // menghapus dan mengirimkan pesan
        $chapter->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'chapter deleted'
        ]);
    }
}