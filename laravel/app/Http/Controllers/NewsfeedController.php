<?php

namespace App\Http\Controllers;

use App\Services\NewsAggregatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsfeedController extends Controller
{
    public function __construct(protected NewsAggregatorService $newsAggregatorService)
    { }

    public function get(Request $request)
    {

        $user        = $request->user();
        $query       = $request->input('query', '');
        $page        = $request->input('page', 1);
        $preferences = $user->preferences()->get()->toArray();

        if ($preferences && (!empty($preferences->categories) || !empty($preferences->sources))) {

            $personalizedNews = $this->newsAggregatorService->getAggregatedNews($query, $preferences, $page);

            return response()->json(['articles' => $personalizedNews, 'page' => $page], 200);
        }

        $randomNews = $this->newsAggregatorService->getAggregatedNews(page: $page);

        return response()->json(['articles' => $randomNews, 'page' => $page], 200);
    }

}
