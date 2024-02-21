<div class="row" id="poll-list-section">
    @foreach ($polls as $poll)
        @include('polls.card')
    @endforeach
</div>
