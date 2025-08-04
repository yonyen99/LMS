
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The leave type details are now accessible via the "View" action on the <a href="{{ route('leave-types.index') }}">Leave Types</a> page.</p>
    </div>
</div>
@endsection
