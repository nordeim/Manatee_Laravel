<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Domain\Utility\Services\QuizService;
use App\Domain\Utility\Models\QuizResult; // For saving results
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    public function __construct(protected QuizService $quizService)
    {
    }

    public function show(): View
    {
        return view('frontend.quiz.show');
    }

    public function submit(Request $request): JsonResponse // Or redirect with results
    {
        $validatedAnswers = $request->validate([
            'feeling' => ['required', 'string', 'in:relax,energize,focus,sleep'],
            'scentFamily' => ['required', 'string', 'in:floral,citrus,woody,herbal'],
            'format' => ['required', 'string', 'in:oil,soap,both'],
        ]);

        $recommendations = $this->quizService->getRecommendations($validatedAnswers);

        // Save the quiz result (optional)
        QuizResult::create([
            'user_id' => auth()->id(), // Null if guest
            'email' => auth()->check() ? auth()->user()->email : $request->input('email_for_results'), // If collecting email
            'answers' => $validatedAnswers,
            'recommendations' => $recommendations->pluck('id')->toArray(), // Save product IDs or full data
        ]);

        return response()->json(['recommendations' => $recommendations]);
    }
}
