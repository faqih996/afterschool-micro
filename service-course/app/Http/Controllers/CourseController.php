<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\MyCourse;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        // mengambil data
        $courses = Course::query();

        // mendefinisikan filter berdasarkan query atau kata dari judul course
        $q = $request->query('q');

        // mendefinisikan filter berdasarkan status course
        $status = $request->query('status');

        // pencarian filter berdasarkan query atau kata
        $courses->when($q, function ($query) use ($q) {
            return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
        });

        // pencarian filter berdasarkan status
        $courses->when($status, function ($query) use ($status) {
            return $query->where('status', '=', $status);
        });

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10)
        ]);
    }

    public function show($id)
    {
        // mencari data course
        // chapters.lessons karna di model chapters terhubung ke lessons
        // juga mengambil dari relation di model yaitu mentor dan images
        $course = Course::with('chapters.lessons')
            ->with('mentor')
            ->with('images')
            ->find($id);

        // jika tidak ada memberi respons 404
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ]);
        }

        // mengambil review dari data ini
        $reviews = Review::where('course_id', '=', $id)->get()->toArray();

        // jika tidak ada review
        if (count($reviews) > 0) {
            $userIds = array_column($reviews, 'user_id');
            $users = getUserByIds($userIds);
            // menampilkan error kosong jika service user down
            if ($users['status'] === 'error') {
                $reviews = [];
            } else {
                // jika server berjalan tampilkan data user
                foreach ($reviews as $key => $review) {
                    // combine user id di service user dan di data, supaya tampil nama
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    // jika sudah ketemu maka menampilkan data
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }
        }

        // menghitung total user yang mengikuti course ini
        $totalStudent = MyCourse::where('course_id', '=', $id)->count();
        // mengambil jumlah total video materi yang ada di lessons, dipanggil melalui chapther
        $totalVideos = Chapter::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();
        // menghitung total jumlah video dengan array_sum, jika tidak akan terbagi berdasaar chapter
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));

        $course['reviews'] = $reviews;
        $course['total_videos'] = $finalTotalVideos;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function create(Request $request)
    {
        // request rule
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'string|url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'integer',
            'level' => 'required|in:all-level,beginner,intermediate,advance',
            'mentor_id' => 'required|integer',
            'description' => 'string'
        ];

        // mengambil data inputan
        $data = $request->all();

        // validator
        $validator = Validator::make($data, $rules);

        // cek validasi
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // cek data mentor
        $mentorId = $request->input('mentor_id');
        $mentor = Mentor::find($mentorId);
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        // save dan kirim kan status
        $course = Course::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        // membuat request
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'mentor_id' => 'integer',
            'description' => 'string'
        ];

        // menggambil data
        $data = $request->all();

        // validasi
        $validator = Validator::make($data, $rules);

        // cek validasi
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // cek data
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // cek mentor
        $mentorId = $request->input('mentor_id');
        if ($mentorId) {
            $mentor = Mentor::find($mentorId);
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found'
                ], 404);
            }
        }

        // edit data
        $course->fill($data);
        $course->save();

        // send status
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function destroy($id)
    {
        $course = Course::find($id);

        // cek data ada atau tidak
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ]);
        }

        // hapus data
        $course->delete();

        // memberikan response
        return response()->json([
            'status' => 'success',
            'message' => 'course deleted'
        ]);
    }
}