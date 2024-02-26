@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('home') }}" class="btn btn-primary">Back</a>
            <a href="{{ route('poll.create') }}" class="btn btn-primary float-end">Create</a>
            <div class="card mt-3">
                <div class="card-header">{{ __('Polls') }}</div>

                <div class="card-body">
                    <table class="table table-striped table-bordered data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    var filter_url = "{{ route('polls') }}";
    var delete_url = "{{ route('poll.delete') }}";
</script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets/js/polls.js?v='.env('JS_VERSION',time())) }}"></script>
@endsection
