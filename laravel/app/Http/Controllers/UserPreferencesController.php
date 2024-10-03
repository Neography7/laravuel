<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;

class UserPreferencesController extends Controller
{
    public function get(): JsonResponse
    {
        $userPreferences = auth()->user()->preferences()->get(['categories', 'sources', 'authors']);

        return response()->json([
            'message' => 'Preferences getted successfully.',
            'data' => $userPreferences
        ]);
    }

    public function store(PreferenceRequest $request): JsonResponse
    {
        $userPreference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json(['message' => 'Preferences updated successfully.']);
    }

}
