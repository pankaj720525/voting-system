<?php

namespace App\Http\Controllers;

use App\Events\Votes;
use App\Helpers\Helper;
use App\Models\Poll;
use App\Models\PollAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Get Polls.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPolls(Request $request)
    {
        $polls = Poll::with('poll_options','poll_answer')->orderBy('id','desc')->get()->append('answer_array');
        $view = view('common.poll_section',compact('polls'))->render();
        return response()->json(['status'=>true,'html'=>$view]);
    }

    /**
     * submit Poll.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function submitPoll(Request $request)
    {
        $poll = Poll::where('id',Helper::getDecryptedId($request->id))->first();

        if ($poll->close == 1) {
            return response()->json(['status'=>false,'message'=>'Oops, This poll is closed.']);
        }

        $update = PollAnswer::updateOrCreate([
            'poll_id' => $poll->id,
            'user_id' => Auth::id(),
        ],[
            'poll_id' => $poll->id,
            'user_id' => Auth::id(),
            'poll_option_id' => Helper::getDecryptedId($request->poll_option_id),
        ]);

        $poll = Poll::with('poll_options','poll_answer')->where('id',$poll->id)->first()->append(['secret','answer_array']);

        Redis::publish('poll:update', $poll->toJson());

        if ($update) {
            return response()->json(['status'=>true,'message'=>'success']);
        }
        return response()->json(['status'=>false,'message'=>'Something went wrong.']);
    }
}
