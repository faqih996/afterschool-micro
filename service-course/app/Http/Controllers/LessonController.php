<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        // mengambil data secara query
        $lessons = Lesson::query();

        // menamilkan data berdasarkan chapter
        $chapterId = $request->query('chapter_id');

        $lessons->when($chapterId, function ($query) use ($chapterId) {
            return $query->where('chapter_id', '=', $chapterId);
        });

        // kirim status
        return response()->json([
            'status' => 'success',
            'data' => $lessons->get()
        ]);
    }

    public function show($id)
    {
        // mengambil data berdasarkan id
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        // menampilkan response
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function create(Request $request)
    {
        // request
        $rules = [
            'name' => 'required|string',
            'video' => 'required|string',
            'chapter_id' => 'required|integer'
        ];

        // get all input data
        $data = $request->all();

        // validasi data
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // cek data chapter
        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ]);
        }

        // simpan data dan kirim status
        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $id)
    {
        // request
        $rules = [
            'name' => 'required|string',
            'video' => 'required|string',
            'chapter_id' => 'required|integer'
        ];

        // get all input data
        $data = $request->all();

        // validasi data
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // mencari dan cek data by id
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        // mencari dan cek data chapter
        $chapterId = $request->input('chapter_id');
        if ($chapterId) {
            $chapter = Chapter::find($chapterId);
            if (!$chapter) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'chapter not found'
                ]);
            }
        }

        // simpan data dan kirim status
        $lesson->fill($data);
        $lesson->save();
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        // mencari data berdasarkan id
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ]);
        }

        // hapus dan kirim
        $lesson->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'lesson deleted'
        ]);
    }
}