<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Group;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group, Event $event)
    {
        $surveys = Survey::all();

        return view('app.group.event.survey.index', compact('group', 'event', 'surveys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Event $event, Request $request)
    {
        $survey = null;

        if ($request->has('survey_id')) {
            $survey = $event->surveys()->where('id', $request->get('survey_id'))->first();
        }

        return view('app.group.event.survey.create', compact('group', 'event', 'survey'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Group $group, Event $event)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data["is_anonymous"] = $request->has('is_anonymous') ? 1 : 0;
        $data["created_by"] = auth()->id();


        $survey = $event->surveys()->create($data);


        return redirect()
            ->route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id])
            ->with('success', 'نظرسنجی با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Event $event, Survey $survey)
    {
        return view('app.group.event.survey.edit', compact('group', 'event', 'survey'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, Event $event, Survey $survey)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $data["is_anonymous"] = $request->has('is_anonymous') ? 1 : 0;

        $survey->update($data);

        return redirect()
            ->route('surveys.create', [
                $group->slug,
                $event->id,
                'survey_id' => $survey->id
            ])
            ->with('success', 'نظرسنجی با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeQuestion(Request $request, Group $group, Event $event, Survey $survey)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:1,2'],
            'options' => ['nullable', 'array'],
            'options.*' => ['required', 'string', 'max:255'],
        ]);

        $question = $survey->questions()->create([
            'text' => $data['question_text'],
            'type' => $data['type'],
        ]);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $optionText) {
                $question->options()->create([
                    'text' => $optionText,
                ]);
            }
        }

        return redirect()->route('surveys.create', [
            $group->slug,
            $event->id,
            'survey_id' => $survey->id
        ])->with('success', 'سوال با موفقیت ایجاد شد.');
    }
}
