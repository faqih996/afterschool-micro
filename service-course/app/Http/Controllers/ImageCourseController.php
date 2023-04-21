<?php

namespace App\Http\Controllers;

use App\Models\ImageCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function create(Request $request)
    {
        // definisi validasi
        $rules = [
            'image' => 'required|url',
            'course_id' => 'required|integer'
        ];

        // request data
        $data = $request->all();

        // validasi data
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // validasi data course
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // input data dan kirim response
        $imageCourse = ImageCourse::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $imageCourse
        ]);
    }

    public function destroy($id)
    {
        $imageCourse = ImageCourse::find($id);

        if (!$imageCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'image course not found'
            ], 404);
        }

        $imageCourse->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'image course deleted'
        ]);
    }
}