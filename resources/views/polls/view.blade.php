@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ route('polls') }}" class="btn btn-primary">Back</a>
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between">
                        <label>
                            {{ __('Poll Result') }}
                        </label>
                        @if ($poll->close == 1)
                            <label class="text-danger"><i>This poll is closed. It will not update.</i></label>
                        @endif
                    </div>

                    <div class="card-body">
                        <h3>{{ $poll->question }}</h3>
                        <hr>
                            <table class="table">
                                <thead class="table-primary">
                                <tr>
                                    <th scope="col">User Name</th>
                                    @foreach ($poll->poll_options as $item)
                                        <th scope="col">{{ $item->option }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                    @if (count($poll->poll_answer) > 0)
                                        @foreach ($poll->poll_answer as $answer_value)
                                            <tr>
                                                <td>{{ $answer_value->user_detail->name }}</td>
                                                @foreach ($poll->poll_options as $item)
                                                    <td> {{ ($answer_value->poll_option_id == $item->id)? "1" : 0 }} </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                        <tr class="table-primary">
                                            <td class="text-end fw-bold">Result:</td>
                                            @foreach ($poll->poll_options as $item)
                                                <td class="fw-bold"> {{ (isset($poll_count[$item->id]))? $poll_count[$item->id] : 0 }} </td>
                                            @endforeach
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="{{ count($poll->poll_options) }}">No data found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var exists_url = "{{ route('poll.exists') }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
<script src="{{ asset('assets/js/polls.js?v='.env('JS_VERSION',time())) }}"></script>
@endsection
