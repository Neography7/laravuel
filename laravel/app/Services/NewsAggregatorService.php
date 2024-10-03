<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NewsAggregatorService {

    public function __construct(
        protected NewsApiService $newsApiService,
        protected GuardianApiService $guardianApiService,
        protected NewYorkApiService $newYorkApiService
    ) { }

    public function getAggregatedNews($query = null, $preferences = [], $page = 1)
    {

        $aggregatedArticles = [];
        $newsApiArticles    = [];
        $guardianArticles   = [];
        $newYorkArticles    = [];

        $preferredSources = $preferences["sources"] ?? [];

        // News API
        if (in_array('newsapi', $preferredSources) || empty($preferredSources)) {
            try {
                $newsApiArticlesRaw = $this->newsApiService->getArticles($query, $preferences, $page);
                $newsApiArticles = $this->newsApiService->formatArticles($newsApiArticlesRaw);
                $aggregatedArticles = array_merge($aggregatedArticles, $newsApiArticles);
            } catch (\Exception $e) {
                Log::error("NewsAPI Error: " . $e->getMessage());
            }
        }

        // Guardian API
        if (in_array('guardian', $preferredSources) || empty($preferredSources)) {
            try {
                $guardianArticlesRaw = $this->guardianApiService->getArticles($query,$preferences, $page);
                $guardianArticles = $this->guardianApiService->formatArticles($guardianArticlesRaw);
                $aggregatedArticles = array_merge($aggregatedArticles, $guardianArticles);
            } catch (\Exception $e) {
                Log::error("Guardian API Error: " . $e->getMessage());
            }
        }

        // New York Times API
        if (in_array('newyork', $preferredSources) || empty($preferredSources)) {
            try {
                $newYorkArticlesRaw = $this->newYorkApiService->getArticles($query, $preferences, $page);
                $newYorkArticles = $this->newYorkApiService->formatArticles($newYorkArticlesRaw);
                $aggregatedArticles = array_merge($aggregatedArticles, $newYorkArticles);
            } catch (\Exception $e) {
                Log::error("New York Times API Error: " . $e->getMessage());
            }
        }

        usort($aggregatedArticles, function ($a, $b) {
            return strtotime($b['published_at']) <=> strtotime($a['published_at']);
        });

        return $aggregatedArticles;
    }
}
