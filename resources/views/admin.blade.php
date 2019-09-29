@extends('layouts.default')

@section('content')
<div class="content">
    <h1>Members:</h1>
    <ul>
        @foreach($members as $member)
            <li>{{$member->full_name}}</li>
        @endforeach
    </ul>

    Replace all members:
    <form action="new_members" method="POST" >
        @csrf
        <textarea name="new_members"></textarea>
        <input type = "submit" value = "submit" />
    <form>
</div>
@endsection
