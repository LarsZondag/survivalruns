@extends('layouts.app')

@section('title', 'Page Title')
@section('content')
<div class="content">
    <table>
        <tr>
            <th>
                Date
            </th>
            <th>
                Location
            </th>
            <th colspan="4">
                Circuits
            </th>
            <th>
                Qual. run
            </th>
            <th>
                Distances
            </th>
            <th>
                Organiser
            </th>
            <th>
                Enrollment
            </th>
            <th>
                Preliminary results
            </th>
            <th>
                Final results
            </th>
        </tr>
        @foreach($runs as $run)
            <tr>
                <td>
                    {{$run->date->format('d-m-Y')}}
                </td>
                <td>
                    {{$run->organiser->location}}
                </td>
                <td>
                    @if($run->LSR)
                        L
                    @endif
                </td>
                <td>
                    @if($run->MSR)
                        M
                    @endif
                </td>
                <td>
                    @if($run->KSR)
                        K
                    @endif
                </td>
                <td>
                    @if($run->JSR)
                        J
                    @endif
                </td>
                <td>
                    @if($run->qualification_run)
                        Yes
                    @endif
                </td>
                <td>
                    {{$run->distances}}
                </td>
                <td>
                    <a href="//{{$run->organiser->url}}">{{$run->organiser->name}}</a>
                </td>
                <td>
                    @if(isset($run->uvponline_enrollment_id) && $run->enrollment_open)
                        <a href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_enrollment_id}}">enroll</a>
                    @endif
                </td>
                <td>
                    @if(isset($run->uvponline_enrollment_id) && !isset($run->uvponline_results_id))
                        <a href="https://www.uvponline.nl/uvponlineU/index.php/uitslag_rt/toonuitslag/{{$run->year}}/{{$run->uvponline_enrollment_id}}">preliminary
                            results</a>
                    @endif
                </td>
                <td>
                    @if(isset($run->uvponline_results_id))
                        <a href="https://www.uvponline.nl/uvponlineF/inschrijven/{{$run->uvponline_enrollment_id}}">results</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
