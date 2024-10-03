<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleSearchRequest;
use App\Services\NewsAggregatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticlesController extends Controller
{

    public function __construct(protected NewsAggregatorService $newsAggregatorService)
    { }

    public function search(ArticleSearchRequest $request)
    {
        $query = $request->input('query');
        $page  = $request->input('page', 1);

        try {

            $news = $this->newsAggregatorService->getAggregatedNews($query,  [], $page);

            return response()->json([
                'articles' => $news,
                'page' => $page,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching news: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Error fetching news'], 500);
    }
}
