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

        return view('app.group.event.survey.create', compact('group', 'event', 'survey'));
    }

    /**
     * Store a new survey.
     */
    public function store(Request $request, Group $group, Event $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['is_anonymous'] = $request->has('is_anonymous') ? 1 : 0;
        $data['created_by'] = auth()->id();

        $survey = $event->surveys()->create($data);

        return redirect()
            ->route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug])
            ->with('success', 'نظرسنجی با موفقیت ایجاد شد.');
    }

    /**
     * Show the form to edit a survey (kept in create view).
     */
    public function edit(Group $group, Event $event, Survey $survey)
    {
        return view('app.group.event.survey.create', [
            'group' => $group,
            'event' => $event,
            'survey' => $survey,
            'editSurvey' => true,
            'showSurveyForm' => true,
            'showQuestionForm' => false,
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
        ]);

        $survey->update($data);

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
        return view('app.group.event.survey.answer', compact('group', 'event', 'survey'));
    }

    /**
     * Store answers of a survey.
     */
    public function storeAnswer(Request $request, Group $group, Event $event, Survey $survey)
    {
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
}