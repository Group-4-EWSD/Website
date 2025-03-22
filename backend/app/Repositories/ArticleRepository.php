<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\ArticleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//use Your Model

class ArticleRepository
{
    public function createOrUpdateArticle($articleId, $userId, $systemId, $request)
    {
        DB::table('articles')->updateOrInsert(
            ['article_id' => $articleId], // Condition to check if the record exists
            [
                'article_title' => $request->article_title,
                'article_description' => $request->article_description,
                'user_id' => $userId,
                'system_id' => $systemId,
                'article_type_id' => $request->article_type_id,
                'delete_flag' => false,
                'updated_at' => now(),
                'created_at' => DB::raw('IFNULL(created_at, NOW())') // Preserve `created_at` if updating
            ]
        );
    }

    public function createArticleDetail($articleId, $filePath, $fileName, $fileType)
    {
        DB::table('article_details')->insert([
            'article_detail_id' => Str::uuid(),
            'article_id' => $articleId,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);
    }

    public function deleteArticleDetail($file_name){
        ArticleDetail::where('file_name', $file_name)->delete();
    }

    public function createActivity($articleId, $userId, $request){
        $activity = DB::table('activities')->insert([
            'activity_id' => Str::uuid(),
            'article_id' => $articleId,
            'user_id' => $userId,
            'article_status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $activity;
    }

    public function getSystemId($userId){
        $systemId = DB::select("
                    SELECT sd.*
                    FROM system_datas AS sd
                    JOIN academic_years AS ay ON ay.academic_year_start = YEAR(CURDATE())
                    JOIN users AS u ON u.faculty_id = sd.faculty_id
                    WHERE u.id = ?
                    LIMIT 1
                ", [$userId])[0]->system_id; // Use parameter binding to prevent SQL injection

        return $systemId;
    }

    public function getStudentHomeCountData(){
        $userId = Auth::id();
        return DB::table('articles')
        ->selectRaw('
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE act.react = 1 and art.user_id = ?) as reactCount,
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE art.user_id = ?) as totalViewCount,
            (SELECT COUNT(*) FROM articles art JOIN system_datas sd ON sd.system_id = art.system_id JOIN academic_years ay ON ay.academic_year_id = sd.academic_year_id WHERE art.user_id = ?) as currentYearArticleCount
        ',[$userId, $userId, $userId]) // WHERE ay.academic_year = CURRENT_YEAR() AND 
        ->first();
    }

    public function getCoordinatorHomeCountData($userId){
        return
        "previous_login,
        pre_submission_total,
        pre_submission_reviewed,
        pre_submission_not_review,
        final_total,
        final_approved,
        final_rejected,
        current_participation_rate,
        current_interest_rate,
        current_in_time_rate";
    }

    public function getCoordinatorCountData($facultyId){
        $currentYear = date('Y');

        // Get Total Submission Count
        $totalSubmissions = DB::table('articles as a')
            ->join('system_datas as sd', 'sd.system_id', '=', 'a.system_id')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->where('ay.academic_year_start', $currentYear)
            ->where('sd.faculty_id', $facultyId)
            ->count();

        // Function to get count by article status
        $getStatusCount = function ($status) use ($currentYear, $facultyId) {
            return DB::table('articles as a')
                ->join('system_datas as sd', 'sd.system_id', '=', 'a.system_id')
                ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
                ->join('activities as avt', function ($join) {
                    $join->on('avt.article_id', '=', 'a.article_id')
                        ->whereRaw('avt.created_at = (SELECT MAX(avt2.created_at) FROM activities avt2 WHERE avt2.article_id = a.article_id)');
                })
                ->where('ay.academic_year_start', $currentYear)
                ->where('sd.faculty_id', $facultyId)
                ->where('avt.article_status', $status)
                ->count();
        };

        // Get counts for different statuses
        $pendingReview = $getStatusCount(1);
        $approvedArticles = $getStatusCount(2);
        $rejectedArticles = $getStatusCount(3);

        return [
            'totalSubmissions' => $totalSubmissions,
            'pendingReview' => $pendingReview,
            'approvedArticles' => $approvedArticles,
            'rejectedArticles' => $rejectedArticles,
        ];
    }

    public function getAllArticles($state, $primaryKey = null, $request = null)
    {
        $articles = DB::table('articles as art')
                ->select([
                    'art.article_id',
                    'art.article_title',
                    'art.article_description',
                    'art.user_id',
                    'at.article_type_id',
                    'at.article_type_name',
                    'u.user_name',
                    DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', u.user_photo_path) AS user_photo_path"),
                    'u.gender',
                    'art.created_at',
                    'art.updated_at',
                    DB::raw("(SELECT act.article_status FROM activities act WHERE act.article_id = art.article_id ORDER BY act.created_at DESC LIMIT 1) AS status"),
                    DB::raw("(SELECT COUNT(*) FROM actions actn WHERE actn.article_id = art.article_id) AS view_count"),
                    DB::raw("(SELECT COUNT(*) FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1) AS like_count"),
                    DB::raw("(SELECT COUNT(*) FROM comments cmmt WHERE cmmt.article_id = art.article_id ) AS comment_count")
                ])
                ->join('users as u', 'u.id', '=', 'art.user_id')
                ->join('system_datas as sd', 'sd.system_id', '=', 'art.system_id')
                ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
                ->join('article_types as at', 'at.article_type_id', '=', 'art.article_type_id');

        // Apply search filter if `articleTitle` exists in the request
        if (!empty($request->academicYearId)) {
            $articles->where('ay.academic_year_id', '=', $request->academicYearId);
        }

        if (!empty($request->articleTitle)) {
            $articles->where('art.article_title', 'LIKE', '%' . $request->articleTitle . '%');
        }
        if ($state == 0){ // All Student (userId)
            $articles->addSelect(
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1 AND actn.user_id = '$primaryKey')) AS current_user_react")
            );
        }
        else if ($state == 1) { // My Articles Student (userId)
            $articles->addSelect(
                DB::raw("(SELECT fb.message FROM feedbacks fb WHERE fb.article_id = art.article_id ORDER BY fb.created_at DESC LIMIT 1) AS last_feedback"),
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1 AND actn.user_id = '$primaryKey')) AS current_user_react")
            );
            $articles->where('art.user_id','=',$primaryKey);
            $articles->havingRaw("status IN (1, 2, 3)");
        } else if ($state == 2) { // My Draft Articles Student (userId)
            $articles->where('art.user_id','=',$primaryKey);
            $articles->havingRaw("status = 0");
        } else {
            if (!empty($request->status)) {
                $status = $request->status;
                if($status == '0'){
                    $articles->havingRaw(" status IN (0, 1, 2, 3) ");
                } else if($status == '1'){
                    $articles->havingRaw(" status IN (1, 2, 3) ");
                } else if($status == '2'){
                    $articles->havingRaw(" status IN (2, 3) ");
                } else if($status == '3'){
                    $articles->havingRaw(" status = 3 ");
                } else {
                    $articles->havingRaw(" status IN (2, 3) ");
                }
            }
        }
        if($state == 3){ // All Articles for Coordinator (facultyId)
            $articles->addSelect(DB::raw("
                CASE 
                    WHEN (SELECT act.article_status FROM activities act WHERE act.article_id = art.article_id ORDER BY act.created_at DESC LIMIT 1) = 3 
                    THEN (SELECT fb.message FROM feedbacks fb WHERE fb.article_id = art.article_id ORDER BY fb.created_at DESC LIMIT 1) 
                    ELSE NULL 
                END AS reject_reason
            "));
            $articles->where('sd.faculty_id','=',$primaryKey);
        }
        // Ensure GROUP BY is valid by including `art.article_id`
        $articles->groupBy([
            'art.article_id',
            'art.article_title',
            'art.user_id',
            'u.user_name',
            'u.user_photo_path',
            'u.gender',
            'art.created_at',
            'art.updated_at'
        ]);

        // 1: created_at ASC, 2: created_at DESC, 3: title ASC, 4: title DESC
        if (!empty($request->sorting)) {
            switch ($request->sorting) {
                case '1':
                    $articles->orderBy('art.created_at', 'asc');
                    break;
                case '2':
                    $articles->orderBy('art.created_at', 'desc');
                    break;
                case '3':
                    $articles->orderBy('art.article_title', 'asc');
                    break;
                case '4':
                    $articles->orderBy('art.article_title', 'desc');
                    break;
                default:
                    $articles->orderBy('art.created_at', 'desc');
            }
        } else {
            $articles->orderBy('art.created_at', 'desc');
        }        

        // Apply LIMIT only if `displayNumber` is set
        if(!empty($request->displayNumber)){
            if ($request->displayNumber > 0) {
                $articles->limit($request->displayNumber);

                // Apply OFFSET only if `pageNumber` > 1
                if ($request->pageNumber > 1) {
                    $offset = ($request->pageNumber - 1) * $request->displayNumber;
                    $articles->offset($offset);
                }
            }
        }
        return $articles;
    }

    public function getArticlePerYear(){
        $articlePerYear = DB::table( 'articles as a')
            ->select([
                'COUNT(a.article_id)',
                DB::raw("SUBSTRING_INDEX(ay.academic_year_name, '-', 1) as academic_year")
            ])
            ->join('users as u','u.id','a.user_id')
            ->join('faculties as f','f.faculty_id','u.faculty_id')
            ->join('system_datas as sd','sd.academic_year_id','ay.academic_year_id')
            ->join('academic_years as ay','sd.academic_year_id','ay.academic_year_id')
            ->groupBy([
                'ay.academic_year_id',
                'ay.academic_year_name',
            ])
            ->get();
        return $articlePerYear;
    }

    public function draftArticleList($userId) {
        $subQuery = DB::table('activities as act')
            ->select('act.article_id', 'act.status', 'act.created_at')
            ->whereRaw('act.created_at = (SELECT MAX(created_at) FROM activities WHERE article_id = act.article_id)')
            ->where('act.status', 0); // Ensure only drafts (status = 0) are considered
    
        $articles = DB::table('articles as a')
            ->select('a.article_title', 'a.article_description')
            ->joinSub($subQuery, 'atv', function ($join) {
                $join->on('a.article_id', '=', 'atv.article_id');
            })
            ->where('a.user_id', '=', $userId)
            ->get();
    
        return $articles;
    }    

    public function getDeadlines($facultyId){
        $deadlines = DB::table('system_datas as sd')
                ->select('sd.pre_submission_date', 'sd.actual_submission_date')
                // ->where('faculty_id', $facultyId)
                ->first();
        return $deadlines;
    }

    public function getFileList($articleId){
        $files = ArticleDetail::where('article_id', $articleId)->get();
        return $files ?: []; // Ensures an empty array if no files found
    }

    public function getItemList($item)
    {
        $tables = [
            '1' => ['article_types', ['article_type_id', 'article_type_name']],
            '2' => ['user_types', ['user_type_id', 'user_type_name']],
            '3' => ['faculties', ['faculty_id', 'faculty_name']],
            '4' => ['academic_years', ['academic_year_id', 'academic_year_description','academic_year_start','academic_year_end']]
        ];

        if (!isset($tables[$item])) {
            return []; // Return empty array if item is invalid
        }

        [$table, $selectColumns] = $tables[$item];

        return DB::table($table)->select($selectColumns)->get();
    }

    public function getCountDataByFaculty($facultyId){
        return DB::table('articles')
        ->selectRaw('
            (SELECT COUNT(*) FROM articles art JOIN users as u ON u.id = art.user_id WHERE u.faculty_id = ?) as totalSubmissionCount,
            (SELECT COUNT(*) FROM (SELECT act.status, MAX(act.created_at) FROM articles act JOIN activities act ON act.article_id = art.article_id JOIN users as u ON u.id = art.user_id WHERE u.faculty_id = ? GROUP BY act.activity_id)) subquery WHERE subquery.status = 1) as pendingReviewCount,
            (SELECT COUNT(*) FROM (SELECT act.status, MAX(act.created_at) FROM articles act JOIN activities act ON act.article_id = art.article_id JOIN users as u ON u.id = art.user_id WHERE u.faculty_id = ? GROUP BY act.activity_id)) subquery WHERE subquery.status = 2) as pendingReviewCount,
            (SELECT COUNT(*) FROM (SELECT act.status, MAX(act.created_at) FROM articles act JOIN activities act ON act.article_id = art.article_id JOIN users as u ON u.id = art.user_id WHERE u.faculty_id = ? GROUP BY act.activity_id)) subquery WHERE subquery.status = 3) as pendingReviewCount,
        ',[$facultyId, $facultyId, $facultyId, $facultyId]) // WHERE ay.academic_year = CURRENT_YEAR() AND 
        ->first();
    }
    
}
