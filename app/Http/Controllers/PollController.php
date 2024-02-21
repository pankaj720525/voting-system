<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Poll;
use App\Models\PollAnswer;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $polls = Poll::where('user_id', Auth::id())->orderBy('id', 'desc');

            return DataTables::of($polls)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('poll.show',Helper::getEncryptedSecret($row->id)).'" class="btn btn-primary" title="Result">Result</a>';
                    $btn .= '<a href="'.route('poll.edit',Helper::getEncryptedSecret($row->id)).'" class="btn btn-info mx-2" title="Edit">Edit</a>';
                    $btn .= '<a href="Javascript:;"  data-id="' . $row->id . '" class="btn btn-danger delete" title="Delete">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('polls.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('polls.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => ['required', 'string', 'min:4'],
            'option' => ['required']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $poll = Poll::create([
            'question' => $request->question,
            'user_id' => Auth::id()
        ]);

        foreach ($request->option as $key => $option_value) {
            PollOption::create([
                'option'    => $option_value,
                'poll_id'   => $poll->id
            ]);
        }

        $poll = Poll::with('poll_options','poll_answer')->where('id',$poll->id)->first()->append(['secret','answer_array']);

        Redis::publish('new:poll', $poll->toJson());

        return redirect()->route('polls')->with('success',"Poll created successfully.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $poll = Poll::with('poll_options','poll_answer.user_detail')->where('user_id',Auth::id())->where('id',Helper::getDecryptedId($id))->first();
            if ($poll) {
                $poll_count = [];
                foreach ($poll->poll_options as $value) {
                    $poll_count[$value->id] = collect($poll->poll_answer)->where('poll_option_id', $value->id)->count('id');
                }
                return view('polls.view',compact('poll','poll_count'));
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $poll = Poll::with('poll_options','poll_answer')->where('user_id',Auth::id())->where('id',Helper::getDecryptedId($id))->first();
            if ($poll) {
                $option_disabled = ($poll->close == 1)? true : ((count($poll->poll_answer) > 0)? true : false);
                return view('polls.edit',compact('poll','option_disabled'));
            }
        } catch (\Throwable $th) {
            Log::error($th->getTraceAsString());
        }
        return redirect()->route('polls')->with('error','Something went wrong.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'question' => ['required', 'string', 'min:4']
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $poll = Poll::with('poll_options','poll_answer')->where('user_id',Auth::id())->where('id',Helper::getDecryptedId($id))->first();
            if (!$poll) {
                return redirect()->back()->with('error','Something went wrong. Please try again.');
            }

            if ($poll->close == 1) {
                return redirect()->route('polls')->with('warning', 'This poll is closed. It will not update.');
            }

            $close = ($request->has('close'))? 1 : 0;
            $poll->update(['question' => $request->question, 'close' => $close]);

            /* If user voted poll then option will not editable */
            if ( count($poll->poll_answer) == 0 ) {
                $poll_options_ids = [];
                foreach ($request->option as $key => $option_value) {
                    $id = Helper::getDecryptedId($key);
                    if ( $id !== false ) {
                        PollOption::where('id', $id)->update([
                            'option'    => $option_value
                        ]);
                        $poll_options_ids[] = $id;
                        Log::info($poll_options_ids);
                    } else {
                        $poll_option = PollOption::create([
                            'option'    => $option_value,
                            'poll_id'   => $poll->id
                        ]);
                        $poll_options_ids[] = $poll_option->id;
                        Log::info($poll_option);
                    }
                }
                /* Removed Poll option */
                PollOption::whereNotIn('id', $poll_options_ids)->where('poll_id', $poll->id)->delete();
            }


            $poll = Poll::with('poll_options','poll_answer')->where('id',$poll->id)->first()->append(['secret','answer_array']);

            Redis::publish('poll:update', $poll->toJson());

            DB::commit();
            return redirect()->route('polls')->with('success','Poll updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getTraceAsString());
            return redirect()->back()->with('error','Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $poll = Poll::where('id',$request->id)->where('user_id', Auth::id())->first()->append('secret');
        if ($poll) {
            $poll->delete();

            Redis::publish('poll:delete', $poll->toJson());
            return response()->json(['status' => true, 'message' => 'Poll deleted successfully.']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong.']);
    }

    /**
     * Check exists polls
     *
     * @return \Illuminate\Http\Response
     */
    public function exists(Request $request)
    {
        $poll = Poll::where('question',$request->question)->where('user_id', Auth::id());
        if ($request->has('id') && $request->id) {
            $poll = $poll->where('id','!=', Helper::getDecryptedId($request->id));
        }
        $poll = $poll->exists();

        if ($poll) {
            return "false";
        } else {
            return "true";
        }
    }
}
