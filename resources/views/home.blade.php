@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a href="{{ route('polls') }}" class="btn btn-primary">Polls</a>
            <div class="mt-3" id="poll_sections">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ env('SOCKET_URL') }}/socket.io/socket.io.js"></script>

<script>
    var SOCKET_URL = "{{ env('SOCKET_URL') }}";
    var socket = io(SOCKET_URL);
    var auth_id = "{{ Auth::id() }}";

    var poll_url = "{{ route('get.polls') }}";
    var submit_poll_url = "{{ route('submit.poll') }}";
    let progress_bar_classes = @json(Helper::progressBarClasses());
</script>
<script src="{{ asset('assets/js/home.js?v='.env('JS_VERSION',time())) }}"></script>
@endsection
