<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\ArticleDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

//use Your Model

class ArticleRepository
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

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

    public function deleteArticleDetail($file_name)
    {
        ArticleDetail::where('file_name', $file_name)->delete();
    }

    public function createActivity($articleId, $userId, $request)
    {
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

    public function getSystemId($userId)
    {
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

    public function getStudentHomeCountData()
    {
        $userId = Auth::id();
        return DB::table('articles')
            ->selectRaw('
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE act.react = 1 and art.user_id = ?) as reactCount,
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE art.user_id = ?) as totalViewCount,
            (SELECT COUNT(*) FROM articles art JOIN system_datas sd ON sd.system_id = art.system_id JOIN academic_years ay ON ay.academic_year_id = sd.academic_year_id WHERE art.user_id = ?) as currentYearArticleCount
        ', [$userId, $userId, $userId]) // WHERE ay.academic_year = CURRENT_YEAR() AND 
            ->first();
    }

    public function getCoordinatorManagerHomeCountData($facultyId = null)
    {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $lastYearData = ['academicYear' => $lastYear];
        $currentYearData = ['academicYear' => $currentYear];

        if ($facultyId !== null) {
            $lastYearData['facultyId'] = $facultyId;
            $currentYearData['facultyId'] = $facultyId;
        }

        // Create request objects
        $lastYearRequest = new Request($lastYearData);
        $currentYearRequest = new Request($currentYearData);

        $lastYearUserActivePoint = $this->userRepository->getActiveUserList($lastYearRequest)->sum('total_score');
        $currentYearUserActivePoint = $this->userRepository->getActiveUserList($currentYearRequest)->sum('total_score');

        // Avoid division by zero
        $deriActiveUser = ($lastYearUserActivePoint > 0)
            ? ($currentYearUserActivePoint / $lastYearUserActivePoint) * 100
            : 0;

        $latestActivity = DB::table('activities')
            ->selectRaw('article_id, MAX(created_at) as latest_created_at')
            ->groupBy('article_id');

        $query = DB::table('articles as a')
            ->join('system_datas as sd', 'sd.system_id', '=', 'a.system_id')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->joinSub($latestActivity, 'la', function ($join) {
                $join->on('a.article_id', '=', 'la.article_id');
            })
            ->join('activities as act', function ($join) {
                $join->on('act.article_id', '=', 'la.article_id')
                    ->on('act.created_at', '=', 'la.latest_created_at');
            })
            ->selectRaw("
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status != '0' THEN 1 END) AS total_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status != '0' THEN 1 END) AS total_previous_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status IN ('2', '3', '4') THEN 1 END) AS reviewed_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status = '1' THEN 1 END) AS pending_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status = '2' THEN 1 END) AS approved_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status = '3' THEN 1 END) AS rejected_articles,
                COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status = '4' THEN 1 END) AS published_articles,
                (COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status != '0' THEN 1 END) * 100.0 /
                NULLIF(COUNT(CASE WHEN ay.academic_year_start = ? AND act.article_status != '0' THEN 1 END), 0)) 
                AS deri_participate_rate
            ", [$currentYear, $lastYear, $currentYear, $currentYear, $currentYear, $currentYear, $currentYear, $currentYear, $lastYear]);

        if ($facultyId !== null) {
            $query->where('u.faculty_id', '=', $facultyId);
        }

        $results = $query->first();

        // reject ratio
        $subquery = DB::table('activities as act')
            ->select('act.article_id', 'ay.academic_year_start')
            ->join('articles as art', 'art.article_id', '=', 'act.article_id')
            ->join('system_datas as sd', 'sd.system_id', '=', 'art.system_id')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->join(DB::raw('(
                SELECT article_id, MAX(created_at) as latest_created
                FROM activities
                GROUP BY article_id
            ) as latest_act'), function ($join) {
                $join->on('latest_act.article_id', '=', 'act.article_id')
                    ->on('latest_act.latest_created', '=', 'act.created_at');
            })
            ->where('act.article_status', 3)
            ->whereIn('ay.academic_year_start', [$currentYear, $lastYear]);

        if ($facultyId !== null) {
            $subquery->where('sd.faculty_id', $facultyId);
        }

        $result = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
            ->mergeBindings($subquery)
            ->selectRaw("
                COUNT(CASE WHEN academic_year_start = ? THEN 1 END) as count_current_year,
                COUNT(CASE WHEN academic_year_start = ? THEN 1 END) as count_last_year
            ", [$currentYear, $lastYear])
            ->first();
        $finalResults['deri_reject_ratio'] = ($result->count_last_year > 0)
            ? ($result->count_current_year / $result->count_last_year) * 100
            : 0;
        $finalResults['deri_reject_ratio'] = number_format($finalResults['deri_reject_ratio'], 2);
        $finalResults['deri_participate_rate'] = number_format($results->deri_participate_rate, 2);

        // Convert object to array and add the derived field
        $finalResults = (array) $results;
        $finalResults['deri_active_user'] = $deriActiveUser;
        $finalResults['deri_active_user'] = number_format($finalResults['deri_active_user'], 2);

        return $finalResults;
    }


    public function getCoordinatorCountData($facultyId)
    {
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
        //$state = 0; // Student Common Dashboard
        //$state = 1; // Student My Articles
        //$state = 2; // Student My Draft Articles
        //$state = 3; // Coordinator Common Dashboard, Coordinator My Articles
        //$state = 4; // All Articles By Manager
        //$state = 5; // All Articles By Guest (Only published status)
        $articles = DB::table('articles as art')
            ->select([
                'art.article_id',
                'art.article_title',
                'art.article_description',
                'art.user_id',
                'u.user_name',
                'at.article_type_id',
                'at.article_type_name',
                DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', u.user_photo_path) AS user_photo_path"),
                'u.gender',
                'art.created_at',
                'art.updated_at',
                'sd.actual_submission_date as submission_deadline',
                DB::raw("(SELECT MIN(avt.created_at) FROM activities avt WHERE avt.article_id = art.article_id AND avt.article_status != 0) AS submission_date"),
                DB::raw("(SELECT act.article_status FROM activities act WHERE act.article_id = art.article_id ORDER BY act.created_at DESC LIMIT 1) AS status"),
                DB::raw("(SELECT COUNT(*) FROM actions actn WHERE actn.article_id = art.article_id) AS view_count"),
                DB::raw("(SELECT COUNT(*) FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1) AS like_count"),
                DB::raw("(SELECT COUNT(*) FROM comments cmmt WHERE cmmt.article_id = art.article_id ) AS comment_count"),
            ])
            ->join('users as u', 'u.id', '=', 'art.user_id')
            ->join('system_datas as sd', 'sd.system_id', '=', 'art.system_id')
            ->join('faculties as f', 'f.faculty_id', '=', 'sd.faculty_id')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->join('article_types as at', 'at.article_type_id', '=', 'art.article_type_id');

        // Apply search filter if `articleTitle` exists in the request
        if (!empty($request->academicYearId)) {
            $articles->where('ay.academic_year_id', '=', $request->academicYearId);
        }
        
        if (!empty($request->facultyId)) {
            $articles->where('f.faculty_id', '=', $request->facultyId);
        }

        if (!empty($request->articleTitle)) {
            $articles->where('art.article_title', 'LIKE', '%' . $request->articleTitle . '%');
        }
        if ($state == 0) { // All Student (userId)
            $articles->addSelect(
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1 AND actn.user_id = '$primaryKey')) AS current_user_react")
            );
        } else if ($state == 1) { // My Articles Student (userId)
            $articles->addSelect(
                DB::raw("(SELECT fb.message FROM feedbacks fb WHERE fb.article_id = art.article_id ORDER BY fb.created_at DESC LIMIT 1) AS last_feedback"),
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions actn WHERE actn.article_id = art.article_id AND actn.react = 1 AND actn.user_id = '$primaryKey')) AS current_user_react")
            );
            $articles->where('art.user_id', '=', $primaryKey);
        } else if ($state == 2) { // My Draft Articles Student (userId)
            $articles->where('art.user_id', '=', $primaryKey);
            $articles->havingRaw("status = 0");
        }

        if (!empty($request->status)) {
            $status = $request->status;
            $articles->havingRaw(" status = ". $status);
        }

        if ($state == 3 || $state == 4 || $state == 0) { // All Articles for Coordinator 3 (facultyId), All articles for all faculties Manager 4
            if ($state != 0) {
                $articles->addSelect(DB::raw("
                    CASE 
                        WHEN (SELECT act.article_status FROM activities act WHERE act.article_id = art.article_id ORDER BY act.created_at DESC LIMIT 1) = 3 
                        THEN (SELECT fb.message FROM feedbacks fb WHERE fb.article_id = art.article_id ORDER BY fb.created_at DESC LIMIT 1) 
                        ELSE NULL 
                    END AS reject_reason
                "));
            }

            if ($state == 3 || $state == 0) {
                //  0: No feedback, 1: Feedback within 14 days, 2: Feedback after 14 days
                $articles->addSelect(DB::raw("
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 
                            FROM feedbacks fb 
                            WHERE fb.article_id = art.article_id
                        ) THEN (
                            CASE 
                                WHEN (
                                    SELECT fb.created_at 
                                    FROM feedbacks fb 
                                    WHERE fb.article_id = art.article_id 
                                    ORDER BY fb.created_at DESC 
                                    LIMIT 1
                                ) <= DATE_ADD(art.created_at, INTERVAL 14 DAY) 
                                THEN 1
                                ELSE 2
                            END
                        )
                        ELSE 0
                    END AS feedback_status
                "));
                if (!empty($request->feedbackStatus) || $request->feedbackStatus == '0') {
                    $articles->whereRaw("
                        CASE 
                            WHEN EXISTS (
                                SELECT 1 
                                FROM feedbacks fb 
                                WHERE fb.article_id = art.article_id
                            ) THEN (
                                CASE 
                                    WHEN (
                                        SELECT fb.created_at 
                                        FROM feedbacks fb 
                                        WHERE fb.article_id = art.article_id 
                                        ORDER BY fb.created_at DESC 
                                        LIMIT 1
                                    ) <= DATE_ADD(art.created_at, INTERVAL 14 DAY)
                                    THEN 1
                                    ELSE 2
                                END
                            )
                            ELSE 0
                        END = ?
                    ", [$request->feedbackStatus]);
                }
                if ($state == 3) {
                    $articles->where('sd.faculty_id', '=', $primaryKey);
                    $articles->havingRaw("status != 0");
                } 
                else if ($state == 0){
                    $articles->havingRaw("status IN (2, 4)");
                }
            } else {
                $articles->addSelect('sd.actual_submission_date AS final_submission_deadline');
                $articles->havingRaw("status IN (2, 4)");
            }
        }
        else if ($state == 5){
            $articles->havingRaw("status = 4 ");
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

        return $articles;
    }

    public function limitArticleList($request, $articles){
        // Apply LIMIT only if `displayNumber` is set
        $articleList = clone $articles;
        if (!empty($request->displayNumber)) {
            if ($request->displayNumber > 0) {
                $articleList->limit($request->displayNumber);

                // Apply OFFSET only if `pageNumber` > 1
                if ($request->pageNumber > 1) {
                    $offset = ($request->pageNumber - 1) * $request->displayNumber;
                    $articleList->offset($offset);
                }
            }
        }
        return $articleList;
    }

    public function getArticlePerYear($facultyId = null)
    {
        $query = DB::table('academic_years as ay')
            ->selectRaw('COUNT(DISTINCT a.article_id) as article_count, ay.academic_year_start as academic_year')
            ->leftJoin('system_datas as sd', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->leftJoin('articles as a', 'sd.system_id', '=', 'a.system_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id');
    
        if ($facultyId !== null) {
            // Wrap condition to ensure null articles are excluded unless they belong to the given faculty
            $query->leftJoin('faculties as f', 'f.faculty_id', '=', 'u.faculty_id')
                  ->where(function($q) use ($facultyId) {
                      $q->where('f.faculty_id', $facultyId)
                        ->orWhereNull('f.faculty_id'); // include years where no articles at all
                  });
        }
    
        $results = $query
            ->groupBy('ay.academic_year_start')
            ->orderBy('ay.academic_year_start', 'asc')
            ->get();
    
        return $results;
    }
    

    public function draftArticleList($userId)
    {
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

    public function getDeadlines($facultyId)
    {
        $deadlines = DB::table('system_datas as sd')
            ->select('sd.pre_submission_date', 'sd.actual_submission_date')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->where('ay.academic_year_start', date('Y'))
            ->where('faculty_id', $facultyId)
            ->first();
        return $deadlines;
    }

    public function getFileList($articleId, $request)
    {
        $files = DB::table('article_details as ad')->select('a.article_id','a.article_title','ad.file_path')
                    ->join('articles as a', 'a.article_id', 'ad.article_id')
                    ->join('system_datas as sd', 'sd.system_id', 'a.system_id')
                    ->join('academic_years as ay', 'ay.academic_year_id', 'sd.academic_year_id');

        if (!empty($articleId)) {
            $files = $files->where('a.article_id', $articleId)->get();
        } else if (!empty($request->articleIdList) || !empty($request->academicYearId)) {
            if (!empty($request->articleIdList)) {
                $files = $files->whereIn('a.article_id', $request->articleIdList)->get();
            }
            if (!empty($request->academicYearId)) {
                $files = $files
                    ->where('ay.academic_year_id', '=', $request->academicYearId)
                    ->get();
            }
        } else {
            $files = collect(); // In case no condition matched, return an empty collection
        }
        return $files;
    }


    public function getItemList($item)
    {
        $tables = [
            '1' => ['article_types', ['article_type_id', 'article_type_name']],
            '2' => ['user_types', ['user_type_id', 'user_type_name']],
            '3' => ['faculties', ['faculty_id', 'faculty_name']],
            '4' => ['academic_years', ['academic_year_id', 'academic_year_description', 'academic_year_start', 'academic_year_end']]
        ];

        if (!isset($tables[$item])) {
            return []; // Return empty array if item is invalid
        }

        [$table, $selectColumns] = $tables[$item];

        return DB::table($table)->select($selectColumns)->get();
    }

    public function getPreviousLogin($userId)
    {
        $prevLogin = DB::table('login_histories')
            ->where('user_id', $userId)
            ->orderByDesc('login_datetime')
            ->limit(1)
            ->value('login_datetime');
        return $prevLogin;
    }

    public function getCurrentSystemData($facultyId)
    {
        $currentYear = date('Y');

        $systemData = DB::table('system_datas as sd')
            ->join('academic_years as ay', 'ay.academic_year_id', '=', 'sd.academic_year_id')
            ->where('ay.academic_year_start', '=', $currentYear)
            ->where('sd.faculty_id', '=', $facultyId)
            ->first();

        return $systemData;
    }


    public function getSubmissionStatus($facultyId)
    {
        // Get the system data
        $systemData = $this->getCurrentSystemData($facultyId);
        // Get today's date
        $today = Carbon::today(); // Carbon instance of today's date

        // Assuming the system data contains the correct date format for comparison (Y-m-d or any standard format)
        if ($today->lt($systemData->pre_submission_date)) {
            return '0'; // Before Pre Submission
        } else if ($today->gt($systemData->pre_submission_date) && $today->lt($systemData->actual_submission_date)) {
            return '1'; // Between Pre Submission And Final Submission
        } else if ($today->gt($systemData->actual_submission_date)) {
            return '2'; // After Final Submission
        }
    }

    public function getRemainingFinalPublish($facultyId)
    {
        // Get the system data
        $systemData = $this->getCurrentSystemData($facultyId);

        // Get today's date
        $today = Carbon::today(); // Carbon instance of today's date

        // Check if today is before the final submission date
        if ($today->lt($systemData->actual_submission_date)) {
            // Calculate the difference between today and the final submission date
            $remainingTime = $today->diffInDays($systemData->actual_submission_date);

            return $remainingTime; // Return the remaining days
        }

        return '0'; // Return '0' if the final submission date has passed
    }

    public function getPublishedList()
    {
        $publishedList = DB::table('articles')
            ->join('activities', 'articles.article_id', '=', 'activities.article_id')
            ->join('system_datas', 'articles.system_id', '=', 'system_datas.system_id')
            ->join('academic_years', 'system_datas.academic_year_id', '=', 'academic_years.academic_year_id')
            ->select('academic_years.academic_year_start')
            ->where('activities.article_status', 4)
            ->groupBy('academic_years.academic_year_start', 'academic_years.academic_year_end')
            ->get();

        return $publishedList;
    }
}
