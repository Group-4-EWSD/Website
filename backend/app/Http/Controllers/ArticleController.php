<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {
        return response()->json($this->articleService->getAllArticles());
    }

    public function show($id)
    {
        return response()->json($this->articleService->getArticleById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        return response()->json($this->articleService->createArticle($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        return response()->json($this->articleService->updateArticle($id, $data));
    }

    public function destroy($id)
    {
        return response()->json(['deleted' => $this->articleService->deleteArticle($id)]);
    }
}
