<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Survivalruns</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="title m-b-md">
        Survivalruns
    </div>
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
                    <a href="{{$run->organiser->url}}">{{$run->organiser->name}}</a>
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
</body>
</html>
