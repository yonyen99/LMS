@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The delegation creation form is now accessible via the "Add Delegation" button on the <a href="{{ route('delegations.index') }}">Delegations</a> page.</p>
    </div>
</div>
@endsection