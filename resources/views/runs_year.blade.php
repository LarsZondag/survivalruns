@extends('layouts.app')

@section('title', 'Survivalruns ' . $year . " - " . ($year + 1))

@section('content')
<div class="content">
    <div class="list-header">
        <span class="date">Date</span>
        <span class="location">Location</span>
        <div class="circuits"><span>Circuits</span></div>
        <span class="distances">Distances</span>
    </div>
    <ul class="collapsible">
        @foreach($runs as $run)
            <li>
                <div class="collapsible-header">
                    <span class="date">{{$run->date->format('d-m-Y')}}</span>
                    <span class="location">{{$run->organiser->location}}</span>
                    <div class="circuits">
                        <div class="tooltipped badge yellow brd-yellow lighten-2 {{$run->JSR ? '' : 'vis-hidden'}}"
                             data-position="top" data-tooltip="JSR">J
                        </div>
                        <div class="tooltipped badge blue brd-blue lighten-4 {{$run->KSR ? '' : 'vis-hidden'}}"
                             data-position="top" data-tooltip="KSR">K
                        </div>
                        <div class="tooltipped badge red brd-red lighten-4 {{$run->MSR ? '' : 'vis-hidden'}}"
                             data-position="top" data-tooltip="MSR">M
                        </div>
                        <div class="tooltipped badge grey brd-black lighten-4 {{$run->LSR ? '' : 'vis-hidden'}}"
                             data-position="top" data-tooltip="LSR">L
                        </div>
                        <div class="tooltipped badge orange brd-orange lighten-4 {{$run->qualification_run ? '' : 'vis-hidden'}}"
                             data-position="top" data-tooltip="Qualification run">Q
                        </div>
                    </div>
                    <span class="distances">{{$run->distances}}</span>
                    <div class="flex-spacer"></div>
                    <div class="badge green brd-green lighten-4 {{$run->enrollment_open ? '' : 'vis-hidden'}}"
                         style="float: right;">Enrollment open
                    </div>
                    <span class="badge {{$run->participants->count() > 0 ? '' : 'vis-hidden'}}">{{$run->participants->count()}}</span>
                </div>
                <div class="collapsible-body">
                    <h5>Organiser: <a href="//{{$run->organiser->url}}">{{$run->organiser->name}}</a></h5>
                    @if(isset($run->uvponline_id) && $run->enrollment_open)
                        <a class="btn" href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_id}}">enroll</a>
                    @endif
                    @if(isset($run->uvponline_id) && !isset($run->uvponline_results_id))
                        <a class="btn"
                           href="https://www.uvponline.nl/uvponlineU/index.php/uitslag_rt/toonuitslag/{{$run->year}}/{{$run->uvponline_id}}">preliminary
                            results</a>
                    @endif
                    @if(isset($run->uvponline_results_id))
                        <a class="btn" href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_id}}">results</a>
                    @endif
                    @if($run->participants->count() > 0)
                        <h4>Enrollments from Delft:</h4>
                        <ul>
                            @foreach($run->participants as $participant)
                                <li>{{$participant->first_name . " " . $participant->last_name}}
                                    - {{$participant->category}}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
