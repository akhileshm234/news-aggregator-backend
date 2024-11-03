<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\Response;

class UserPreferenceController extends Controller
{
    public function index()
    {
        return response()->json(auth()->user()->preferences);
    }

    public function store(UserPreferenceRequest $request)
    {
        $preference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json($preference, Response::HTTP_CREATED);
    }
} 