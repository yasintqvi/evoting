<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Group;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SurveyController extends Controller
{
    /**
     * Display a listing of surveys for an event.
     */
    public function index(Group $group, Event $event)
    {
        $surveys = Survey::where('event_id', $event->id)
            ->filter(request()->all())
            ->get();

        return view('app.group.event.survey.index', compact('group', 'event', 'surveys'));
    }

    /**
     * Show form to create or edit a survey.
     */
    public function create(Group $group, Event $event, Request $request)
    {
        $survey = null;
        if ($request->has('survey_id')) {
            $survey = $event->surveys()->where('slug', $request->get('survey_id'))->firstOrFail();
        }

        $participants = $event->participants()->with('user')->get();
        return view('app.group.event.survey.create', compact('group', 'event', 'survey', 'participants'));
    }

    /**
     * Store a new survey.
     */
    public function store(Request $request, Group $group, Event $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'weight_by_stock' => 'nullable|boolean',
            'blocked_user_ids' => 'nullable|array',
            'blocked_user_ids.*' => 'integer|exists:users,id',
        ]);

        $data['is_anonymous'] = $request->has('is_anonymous') ? 1 : 0;
        $data['weight_by_stock'] = $request->has('weight_by_stock') ? 1 : 0;
        $data['created_by'] = auth()->id();

        $survey = $event->surveys()->create($data);
        $survey->blockedUsers()->sync((array) $request->input('blocked_user_ids', []));

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'نظرسنجی با موفقیت ایجاد شد.');
    }

    /**
     * Show the form to edit a survey (kept in create view).
     */
    public function edit(Group $group, Event $event, Survey $survey)
    {
        $participants = $event->participants()->with('user')->get();
        return view('app.group.event.survey.create', [
            'group' => $group,
            'event' => $event,
            'survey' => $survey,
            'editSurvey' => true,
            'showSurveyForm' => true,
            'showQuestionForm' => false,
            'participants' => $participants,
        ]);
    }

    /**
     * Update an existing survey.
     */
    public function update(Request $request, Group $group, Event $event, Survey $survey)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'weight_by_stock' => 'nullable|boolean',
            'blocked_user_ids' => 'nullable|array',
            'blocked_user_ids.*' => 'integer|exists:users,id',
        ]);

        $data['is_anonymous'] = $request->has('is_anonymous') ? 1 : 0;
        $data['weight_by_stock'] = $request->has('weight_by_stock') ? 1 : 0;
        $survey->update($data);
        $survey->blockedUsers()->sync((array) $request->input('blocked_user_ids', []));

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'نظرسنجی با موفقیت بروزرسانی شد.');
    }

    /**
     * Store a question for a survey.
     */
    public function storeQuestion(Request $request, Group $group, Event $event, Survey $survey)
    {
        $data = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required|integer|in:1,2',
            'options' => 'nullable|array',
            'options.*' => 'required|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);

        $question = $survey->questions()->create([
            'question_text' => $data['question_text'],
            'type' => $data['type'],
            'is_required' => $data['is_required'] ?? 0,
        ]);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $optionText) {
                $question->options()->create(['option_text' => $optionText]);
            }
        }

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'سوال با موفقیت ایجاد شد.');
    }

    /**
     * Show form to edit a question.
     */
    public function editQuestion(Group $group, Event $event, Survey $survey, Question $question)
    {
        return view('app.group.event.survey.create', [
            'group' => $group,
            'event' => $event,
            'survey' => $survey,
            'editQuestion' => $question,
        ]);
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, Group $group, Event $event, Survey $survey, Question $question)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:1,2',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'is_required' => 'required|boolean',
        ]);

        $question->update([
            'question_text' => $data['question_text'],
            'type' => $data['type'],
            'is_required' => $data['is_required'],
        ]);

        $question->options()->delete();

        if (!empty($data['options'])) {
            foreach ($data['options'] as $option) {
                $question->options()->create(['option_text' => $option]);
            }
        }

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'سوال بروزرسانی شد.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(Group $group, Event $event, Survey $survey, Question $question)
    {
        $question->options()->delete();
        $question->delete();

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'سوال با موفقیت حذف شد.');
    }

    /**
     * Show form for answering a survey.
     */
    public function showAnswerForm(Group $group, Event $event, Survey $survey)
    {
        $survey->load('questions.options');
        $isExpired = $survey->end_at && now()->gt($survey->end_at);
        $isNotStarted = $survey->start_at && now()->lt($survey->start_at);
        if ($survey->blockedUsers()->wherePivot('user_id', auth()->id())->exists()) {
            return back()->with('error', 'شما مجاز به پاسخ‌دادن به این نظرسنجی نیستید.');
        }
        return view('app.group.event.survey.answer', compact('group', 'event', 'survey', 'isExpired', 'isNotStarted'));
    }

    /**
     * Store answers of a survey.
     */
    public function storeAnswer(Request $request, Group $group, Event $event, Survey $survey)
    {
        if ($survey->blockedUsers()->wherePivot('user_id', auth()->id())->exists()) {
            return back()->with('error', 'شما مجاز به پاسخ‌دادن به این نظرسنجی نیستید.');
        }
        if ($survey->start_at && now()->lt($survey->start_at)) {
            return back()->with('error', 'این نظرسنجی هنوز شروع نشده است.');
        }

        if ($survey->end_at && now()->gt($survey->end_at)) {
            return back()->with('error', 'زمان این نظرسنجی به پایان رسیده است و امکان پاسخ دادن وجود ندارد.');
        }

        DB::transaction(function () use ($request, $survey) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => auth()->id(),
            ]);

            foreach ($survey->questions as $question) {
                $key = 'questions_' . $question->id;
                if ($question->is_required && !$request->has($key)) {
                    throw ValidationException::withMessages([
                        $key => 'پاسخ به این سؤال الزامی است.',
                    ]);
                }

                $input = $request->input($key);

                if ($question->type == 1 && $input) {
                    SurveyAnswer::create([
                        'response_id' => $response->id,
                        'question_id' => $question->id,
                        'option_id' => $input,
                    ]);
                } elseif ($question->type == 2 && $input) {
                    foreach ($input as $option_id) {
                        SurveyAnswer::create([
                            'response_id' => $response->id,
                            'question_id' => $question->id,
                            'option_id' => $option_id,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('surveys.answer', [$group->slug, $event->slug, $survey->slug])
            ->with('success', 'پاسخ شما با موفقیت ثبت شد.');
    }

    public function statistics(Group $group, Event $event, Survey $survey)
    {
        $isWeighted = (int) $survey->weight_by_stock === 1 && $group->type->value === \App\Enums\GroupType::SPECIAL->value;

        if ($isWeighted) {
            $raw = \DB::table('survey_answers')
                ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                ->join('survey_questions', 'survey_answers.question_id', '=', 'survey_questions.id')
                ->leftJoin('survey_options', 'survey_answers.option_id', '=', 'survey_options.id')
                ->leftJoin('participants', function ($join) use ($event) {
                    $join->on('participants.user_id', '=', 'survey_responses.user_id')
                        ->where('participants.event_id', '=', $event->id);
                })
                ->select(
                    'survey_questions.id as question_id',
                    'survey_questions.question_text as question_title',
                    'survey_options.id as option_id',
                    'survey_options.option_text as option_title',
                    \DB::raw('SUM(COALESCE(participants.normal_stock_count,0) + COALESCE(participants.prefered_stock_count,0)) as weight_sum')
                )
                ->where('survey_responses.survey_id', $survey->id)
                ->groupBy('survey_questions.id', 'survey_questions.question_text', 'survey_options.id', 'survey_options.option_text')
                ->get();

            $grouped = collect($raw)->groupBy('question_id')->map(function ($items) {
                $total = $items->sum('weight_sum');
                return $items->map(function ($item) use ($total) {
                    $item->percent = $total > 0 ? round(($item->weight_sum * 100.0) / $total, 1) : 0;
                    $item->count = $item->weight_sum;
                    return $item;
                });
            });
            $stats = $grouped;
        } else {
            $stats = \DB::table('survey_answers')
                ->join('survey_responses', 'survey_answers.response_id', '=', 'survey_responses.id')
                ->join('survey_questions', 'survey_answers.question_id', '=', 'survey_questions.id')
                ->leftJoin('survey_options', 'survey_answers.option_id', '=', 'survey_options.id')
                ->select(
                    'survey_questions.id as question_id',
                    'survey_questions.question_text as question_title',
                    'survey_options.id as option_id',
                    'survey_options.option_text as option_title',
                    \DB::raw('COUNT(survey_answers.id) as count')
                )
                ->where('survey_responses.survey_id', $survey->id)
                ->groupBy('survey_questions.id', 'survey_questions.question_text', 'survey_options.id', 'survey_options.option_text')
                ->get()
                ->groupBy('question_id')
                ->map(function ($items) {
                    $total = $items->sum('count');
                    return $items->map(function ($item) use ($total) {
                        $item->percent = $total > 0 ? round(($item->count * 100.0) / $total, 1) : 0;
                        return $item;
                    });
                });
        }

        return view('app.group.event.survey.statistics', compact('group', 'event', 'survey', 'stats'));
    }

    /**
     * Manually start a survey.
     */
    public function start(Group $group, Event $event, Survey $survey)
    {
        if ((int) $survey->status === 1) {
            return back()->with('error', 'این نظرسنجی هم‌اکنون فعال است.');
        }

        $survey->status = 1;

        $survey->start_at = now();

        $survey->save();

        return back()->with('success', 'نظرسنجی با موفقیت شروع شد.');
    }

    // میخوام وقتی تاریخ پایان اتفاق افتاد دیگ نشه توی نظرسنجی شرکت کرد

    /**
     * Manually end a survey.
     */
    public function end(Group $group, Event $event, Survey $survey)
    {
        if ((int) $survey->status !== 1) {
            return back()->with('error', 'این نظرسنجی در حال حاضر فعال نیست.');
        }

        $survey->status = 0;
        $survey->end_at = now();
        $survey->save();

        return back()->with('success', 'نظرسنجی با موفقیت پایان یافت.');
    }
}
