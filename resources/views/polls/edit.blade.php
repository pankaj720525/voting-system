@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ route('polls') }}" class="btn btn-primary">Back</a>
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between">
                        <label>
                            {{ __('Edit Poll') }}
                        </label>
                        @if ($poll->close == 1)
                            <label class="text-danger"><i>This poll is closed. It will not update.</i></label>
                        @endif
                    </div>

                    <div class="card-body">
                        <form action="{{ route('poll.update',Helper::getEncryptedSecret($poll->id)) }}" class="form-validate" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ Helper::getEncryptedSecret($poll->id) }}">
                            <div class="mb-3">
                                <label for="question" class="form-label required">Question</label>
                                <input type="text" name="question" class="form-control" id="question" value="{{ $poll->question }}" @if ($poll->close == 1) readonly @endif>
                            </div>
                            <div class="mb-3 poll-options">
                                <label class="form-label required">Poll Options</label> <small class="text-info"><b>Note:</b> <i>If user voted any option then it will not editable.</i></small>
                                @forelse ($poll->poll_options as $key => $item)
                                    @if ($key == 0)
                                        <div class="mb-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control question_options" @if ($option_disabled) disabled @endif name="option[{{ Helper::getEncryptedSecret($item->id) }}]" placeholder="Enter Option" value="{{ $item->option }}">
                                            </div>
                                        </div>
                                    @elseif($key == 1)
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control question_options" @if ($option_disabled) disabled @endif name="option[{{ Helper::getEncryptedSecret($item->id) }}]" placeholder="Enter Option" data-error="#option_error_{{ $key }}" value="{{ $item->option }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary @if ($option_disabled == false) add-option @else disabled @endif" type="button">Add</button>
                                                </div>
                                            </div>
                                            <div id="option_error_{{ $key }}"></div>
                                        </div>
                                    @else
                                        <div class="mb-3 @if ($option_disabled == false) option-div-{{ $key }} @endif">
                                            <div class="input-group">
                                                <input type="text" class="form-control question_options" @if ($option_disabled) disabled @endif name="option[{{ Helper::getEncryptedSecret($item->id) }}]" placeholder="Enter Option" data-error="#option_error_{{ $key }}" value="{{ $item->option }}">
                                                <div class="input-group-append">
                                                    @if ($option_disabled)
                                                        <button class="btn btn-danger" disabled type="button">Remove</button>
                                                    @else
                                                        <button class="btn btn-danger remove-option" data-class=".option-div-{{ $key }}" type="button">Remove</button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div id="option_error_{{ $key }}"></div>
                                        </div>
                                    @endif
                                @empty
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
                                @endforelse
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="close" id="close_poll" @if ($poll->close == 1) checked disabled @endif>
                                    <label class="form-check-label" for="close_poll">
                                        Close Poll
                                    </label>
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
