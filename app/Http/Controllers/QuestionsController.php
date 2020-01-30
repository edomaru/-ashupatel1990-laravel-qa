<?php

namespace App\Http\Controllers;

use App\Questions;
use Illuminate\Http\Request;
use App\Http\Requests\AskQuestionRequest;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Questions::with('user')->latest()->paginate(5);
        // $questions = Questions::with('user')->get();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $question = new Questions();
        return view('questions.create', compact('question'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\AskQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AskQuestionRequest $request)
    {
        $request->user()
            ->questions()
            ->create($request->only('title', 'body'));
        return redirect()->route('questions.index')->with('success', 'Your question has been submitted..');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Questions  $questions
     * @return \Illuminate\Http\Response
     */
    public function show(Questions $question)
    {
        $question->increment('views');
        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Questions  $questions
     * @return \Illuminate\Http\Response
     */
    public function edit($question)
    {
        $question = Questions::findOrFail($question);
        return view('questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\AskQuestionRequest  $request
     * @param  \App\Questions  $questions
     * @return \Illuminate\Http\Response
     */
    public function update(AskQuestionRequest $request, Questions $question)
    {
        if (\Gate::denies('update-question', $question)) {
            abort('403', 'You are not allow to Edit this question');
        }
        $question->update($request->only('title', 'body'));
        return redirect()->route('questions.index')
            ->with('success', 'Your question updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Questions  $questions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questions $question)
    {
        if (\Gate::denies('delete-question', $question)) {
            abort('403', 'Access Denies');
        }
        $question->delete();
        session()->flash('success', 'Question Deleted Successfully..');
        return redirect()->route('questions.index');
    }
}
