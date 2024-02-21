@if ($message = Session::get('success'))
<div class="alert alert-info alert-block">
    <span>{{ $message }}</span>
</div>
@endif


@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <span>{{ $message }}</span>
</div>
@endif


@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
	<span>{{ $message }}</span>
</div>
@endif


@if ($message = Session::get('info'))
<div class="alert alert-success alert-block">
	<span>{{ $message }}</span>
</div>
@endif


@if ($message = Session::get('status'))
<div class="alert alert-default alert-block">
	<span>{{ $message }}</span>
</div>
@endif
