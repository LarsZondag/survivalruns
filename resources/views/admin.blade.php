@extends('layouts.default')

@section('content')
<div class="content">
    <h1>Members:</h1>
    @foreach($members as $member)
        <p>{{$member->first_name}}</p>
    @endforeach

    Replace all members:
    <form action="new_members" method="POST" >
        @csrf
        <textarea name="new_members"></textarea>
        <input type = "submit" value = "submit" />
    <form>
</div>
@endsection
