<div class="col-md-4" id="poll-card-{{ $poll->secret }}">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $poll->question }} @if ($poll->close == 1) <small class="text-danger"><i>This poll is closed</i></small> @endif</h5>
            <p class="mb-0"><strong>Vote Count:</strong> {{ $poll->answer_array['total_count'] }}</p>
        </div>
        <div class="card-body">
            @php $index = 1; @endphp
            @foreach ($poll->poll_options as $option_value)
                @php
                    if ($index > 5) {
                        $index = 1;
                    }
                    $progress_class = isset(Helper::progressBarClasses()[$index])? Helper::progressBarClasses()[$index] : '';
                    $index++;
                @endphp
                <div class="row {{ (isset($poll->answer_array['data_array'][Auth::id()]) && $poll->answer_array['data_array'][Auth::id()] == $option_value->id)? 'checked-border my-2' : '' }}">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input @if ($poll->close == 0) poll-answer @endif" type="radio" value="{{ Helper::getEncryptedSecret($option_value->id) }}"
                                data-poll_id="{{ Helper::getEncryptedSecret($poll->id) }}"
                                name="option[{{ Helper::getEncryptedSecret($poll->id) }}]"
                                {{ (isset($poll->answer_array['data_array'][Auth::id()]) && $poll->answer_array['data_array'][Auth::id()] == $option_value->id)? 'Checked' : '' }}
                                @if ($poll->close == 1) disabled @endif
                                id="option-{{ $option_value->id }}">
                            <label class="form-check-label" for="option-{{ $option_value->id }}">
                                {{ $option_value->option }}
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="progress">
                            <div class="progress-bar {{ $progress_class }}" role="progressbar" style="width: {{ (isset($poll->answer_array['poll_percentage'][$option_value->id]))? $poll->answer_array['poll_percentage'][$option_value->id] : 0 }}%" aria-valuenow="{{ $poll->answer_array['total_count'] }}" aria-valuemin="0" aria-valuemax="100">
                                {{ (isset($poll->answer_array['poll_option_count'][$option_value->id]))? $poll->answer_array['poll_option_count'][$option_value->id] : 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
