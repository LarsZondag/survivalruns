@extends('layouts.app')

@section('title', 'Survivalruns ' . $year . " - " . ($year + 1))

@section('title-caption')
    @php
        $date = \Carbon\Carbon::now();
        $current_year = $date->year;
        if ($date->month < 9) {
            $current_year--;
        }
    @endphp
    <a class="btn" href="{{url($year-1)}}">Previous season</a>
    @if($year <= $current_year)<a class="btn" href="{{url($year+1)}}">Next season</a>
    @endif
@endsection

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
                    @if($run->ONK_JSR)
                        <div class="tooltipped badge yellow brd-yellow lighten-2"
                             data-position="top" data-tooltip="ONK JSR">ONK JSR
                        </div>
                    @elseif($run->ONK_KSR)
                        <div class="tooltipped badge blue brd-blue lighten-4"
                             data-position="top" data-tooltip="ONK KSR">ONK KSR
                        </div>
                    @elseif($run->ONK_MSR)
                        <div class="tooltipped badge red brd-red lighten-4"
                             data-position="top" data-tooltip="ONK MSR">ONK MSR
                        </div>
                    @elseif($run->ONK_LSR)
                        <div class="tooltipped badge grey brd-black lighten-4"
                             data-position="top" data-tooltip="ONK LSR">ONK LSR
                        </div>
                    @endif
                    <div class="badge green brd-green lighten-4 {{$run->enrollment_open ? '' : 'vis-hidden'}}"
                         style="float: right;">Enrollment open
                    </div>
                    <span class="badge {{$run->participants->count() > 0 ? '' : 'vis-hidden'}}">{{$run->participants->count()}}</span>
                </div>
                <div class="collapsible-body">
                    <div class="run-info">
                        <div class="participant-info">
                            @if($run->participants->count() > 0)
                                @php
                                    $part_per_cat = $run->participants->groupBy('category');
                                @endphp
                                @if(isset($run->uvponline_results_id))
                                    <h4>Results from Delft:</h4>
                                    @foreach($part_per_cat as $category => $participants)
                                        <h5>{{$category}}:</h5>
                                        <table>
                                            <tr>
                                                <th>Pos.</th>
                                                <th>Time</th>
                                                <th>Name</th>
                                                <th>Points</th>
                                            </tr>
                                            @foreach($participants as $participant)
                                                <tr>
                                                    <td class="pos-col">{{$participant->position}} {{$participant->DNS ? 'DNS' : ''}} {{$participant->DNF ? 'DNF' : ''}}</td>
                                                    <td class="time-col">{{$participant->time}}</td>
                                                    <td class="name-col">{{$participant->first_name}} {{$participant->last_name}}</td>
                                                    <td class="points-col">{{$participant->points/100}}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endforeach
                                @else
                                    <h4>Enrollments from Delft:</h4>
                                    @foreach($part_per_cat as $category => $participants)
                                        <h5>{{$category}}:</h5>
                                        <ul>
                                            @foreach($participants as $participant)
                                                <li>{{$participant->first_name . " " . $participant->last_name}}</li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                @endif
                            @elseif ($run->date->isPast())
                                <h4>No results from Delft</h4>
                            @else
                                <h4>No enrollments from Delft</h4>
                            @endif
                        </div>
                        <div class="organiser-info">
                            <h4>Organiser: <a target="_blank"
                                              href="//{{$run->organiser->url}}">{{$run->organiser->name}}</a>
                            </h4>
                            @if(isset($run->uvponline_id) && $run->enrollment_open)
                                <a class="btn" target="_blank"
                                   href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_id}}">enroll</a>
                            @endif
                            @if(isset($run->uvponline_id) && !isset($run->uvponline_results_id))
                                <a class="btn" target="_blank"
                                   href="https://www.uvponline.nl/uvponlineU/index.php/uitslag_rt/toonuitslag/{{$run->year}}/{{$run->uvponline_id}}">preliminary
                                    results</a>
                            @endif
                            @if(isset($run->uvponline_results_id))
                                <a class="btn" target="_blank"
                                   href="https://www.uvponline.nl/uvponlineU/index.php/uitslag/toonuitslag/{{$run->year}}/{{$run->uvponline_results_id}}">results</a>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
