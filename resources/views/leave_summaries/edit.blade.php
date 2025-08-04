@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The leave summary edit form is now accessible via the "Edit" action on the <a href="{{ route('leave-summaries.index') }}">Leave Summaries</a> page.</p>
    </div>
</div>
@endsection