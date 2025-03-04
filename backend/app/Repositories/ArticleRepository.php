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
    public function createArticle($articleId, $userId, $request)
    {
        DB::table('articles')->insert([
            'article_id' => $articleId,
            'article_title' => $request->article_title,
            'article_description' => $request->article_description,
            'user_id' => $userId,
            'system_id' => $request->system_id,
            'article_type_id' => $request->article_type_id,
            'delete_flag' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createArticleDetail($articleId, $filePath, $fileName, $fileType)
    {
        DB::table('article_details')->insert([
            'article_detail_id' => Str::random(10),
            'article_id' => $articleId,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);
    }

    public function createActivity($articleId, $userId, $systemId, $request){
        DB::table('activities')->insert([
            'activity_id' => Str::uuid(),
            'article_id' => $articleId,
            'user_id' => $userId,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getSystemId($userId){
        $systemId = DB::table('system_datas as sd')
            ->join('user as u', 'u.faculy_id', '=', 'sd.faculy_id')
            ->where('u.id', $userId)
            ->pluck('sysetm_id');
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
                'art.article_title',
                'art.user_id',
                'u.user_name',
                'u.user_photo_path',
                'u.gender',
                'art.created_at',
                'art.updated_at',
                DB::raw("(SELECT ad.file_path FROM article_details ad WHERE ad.article_id = art.article_id AND ad.file_type = 'WORD' LIMIT 1) AS file_path")
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
        $files = ArticleDetail::where('article_id', $articleId)
            ->pluck('file_path')
            ->toArray();
        return $files ?: []; // Ensures an empty array if no files found
    }
    
}
