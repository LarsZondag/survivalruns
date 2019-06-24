@extends('layouts.app')

@section('title', 'Page Title')
@section('content')
<div class="content">
    <ul class="collapsible">
        @foreach($runs as $run)
            <li>
                <div class="collapsible-header">
                    <span class="date">{{$run->date->format('d-m-Y')}}</span>
                    <span class="location">{{$run->organiser->location}}</span>
                    <div class="badge yellow brd-yellow lighten-2 {{$run->JSR ? '' : 'vis-hidden'}}"> J</div>
                    <div class="badge blue brd-blue lighten-4 {{$run->KSR ? '' : 'vis-hidden'}}"> K</div>
                    <div class="badge red brd-red lighten-4 {{$run->MSR ? '' : 'vis-hidden'}}"> M</div>
                    <div class="badge grey brd-black lighten-4 {{$run->LSR ? '' : 'vis-hidden'}}"> L</div>

                    <span class="distances">{{$run->distances}}</span>

                    @if($run->participants->count() > 0)
                        <span class="badge">{{$run->participants->count()}}</span>
                    @endif
                </div>
                <div class="collapsible-body">
                    @if($run->qualification_run)
                        Yes
                    @endif
                    <a href="//{{$run->organiser->url}}">{{$run->organiser->name}}</a>
                    @if(isset($run->uvponline_enrollment_id) && $run->enrollment_open)
                        <a href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_enrollment_id}}">enroll</a>
                    @endif
                    @if(isset($run->uvponline_enrollment_id) && !isset($run->uvponline_results_id))
                        <a href="https://www.uvponline.nl/uvponlineU/index.php/uitslag_rt/toonuitslag/{{$run->year}}/{{$run->uvponline_enrollment_id}}">preliminary
                            results</a>
                    @endif
                    @if(isset($run->uvponline_results_id))
                        <a href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_enrollment_id}}">results</a>
                    @endif
                    @if($run->participants->count() > 0)
                        <h4>Enrollments from Delft:</h4>
                        <ul>
                            @foreach($run->participants as $participant)
                                <li>{{$participant->first_name . $participant->last_name}}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
