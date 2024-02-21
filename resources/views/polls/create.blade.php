@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ route('polls') }}" class="btn btn-primary">Back</a>
                <div class="card mt-3">
                    <div class="card-header">{{ __('Create Poll') }}</div>

                    <div class="card-body">
                        <form action="{{ route('poll.store') }}" class="form-validate" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="question" class="form-label">Question</label>
                                <input type="text" name="question" class="form-control" id="question">
                            </div>
                            <div class="mb-3 poll-options">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control question_options" name="option[0]" placeholder="Enter Option">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control question_options" name="option[1]" placeholder="Enter Option" data-error="#option_error_1">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary add-option" type="button">Add</button>
                                        </div>
                                    </div>
                                    <div id="option_error_1"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
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
