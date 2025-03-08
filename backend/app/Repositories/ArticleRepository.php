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
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $activity;
    }

    public function getSystemId($userId){
        $systemId = DB::select("
                    SELECT sd.*
                    FROM system_datas AS sd
                    JOIN academic_years AS ay ON ay.academic_year = CONCAT(YEAR(CURDATE()), '-', YEAR(CURDATE()) + 1)
                    JOIN users AS u ON u.faculty_id = sd.faculty_id
                    WHERE u.id = ?
                    LIMIT 1
                ", [$userId])[0]->system_id; // Use parameter binding to prevent SQL injection

        return $systemId;
    }

    public function getCountData(){
        $userId = Auth::id();
        return DB::table('articles')
        ->selectRaw('
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE act.react = 1 and art.user_id = ?) as reactCount,
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE art.user_id = ?) as totalViewCount,
            (SELECT COUNT(*) FROM articles art JOIN system_datas sd ON sd.system_id = art.system_id JOIN academic_years ay ON ay.academic_year_id = sd.academic_year_id WHERE art.user_id = ?) as currentYearArticleCount
        ',[$userId, $userId, $userId]) // WHERE ay.academic_year = CURRENT_YEAR() AND 
        ->first();
    }

    public function getAllArticles($userId, $request)
    {
        $articles = DB::table('articles as art')
                ->select([
                    'art.article_id',
                    'art.article_title',
                    'art.user_id',
                    'u.user_name',
                    DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', u.user_photo_path) AS user_photo_path"),
                    'u.gender',
                    'art.created_at',
                    'art.updated_at',
                    DB::raw("(SELECT ad.file_path FROM article_details ad WHERE ad.article_id = art.article_id AND ad.file_type = 'WORD' LIMIT 1) AS file_path"),
                    DB::raw("(SELECT act.status FROM activities act WHERE act.article_id = art.article_id ORDER BY act.created_at DESC LIMIT 1) AS status")
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
        
        if (!empty($request->myArticles)) {
            $articles->where('art.user_id','=',$userId);
            $articles->havingRaw("status IN (1, 2, 3)");
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

        // 1: create_at ASC, 2: created_at DESC, 3: title ASC, 4: title DESC
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
        if ($request->displayNumber > 0) {
            $articles->limit($request->displayNumber);

            // Apply OFFSET only if `pageNumber` > 1
            if ($request->pageNumber > 1) {
                $offset = ($request->pageNumber - 1) * $request->displayNumber;
                $articles->offset($offset);
            }
        }
        return $articles;
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
            '4' => ['academic_years', ['academic_year_id', 'academic_year']]
        ];

        if (!isset($tables[$item])) {
            return []; // Return empty array if item is invalid
        }

        [$table, $selectColumns] = $tables[$item];

        return DB::table($table)->select($selectColumns)->get();
    }

    
}
