<?php

namespace App\Services;

use App\Repositories\ActivityRepository;

class ActivityService
{
    protected $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getArticleData($request){
        if(count($this->activityRepository->getUserArticlePair($request)) > 0){
            $this->activityRepository->increaseViewCount($request);
        }
        $activityDetail = $this->activityRepository->getArticleDetail($request);

        return [
            'articleDetail' => $this->activityRepository->getArticleDetail($request)
        ];
    }

    public function likeArticle($request){

    }

    public function commentArticle($request){

    }
}
