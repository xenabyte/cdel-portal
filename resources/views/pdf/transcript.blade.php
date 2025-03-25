@php
    $levels = $registeredCourses->groupBy('level');
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Transcript</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-size: 12px; }
        .header-logo img { width: 25%; margin-bottom: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid; padding: 2px; text-align: center; }
        th { background-color: #f2f2f2; }
        .watermark { 
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            z-index: -1; background-repeat: no-repeat; background-position: center;
            background-size: 40%; opacity: 0.1;
            background-image: url('{{ env('SCHOOL_LOGO') }}');
        }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center">
        <img src="{{ env('SCHOOL_LOGO') }}" width="40%">
        <h1>Official Academic Transcript</h1>
    </div>
    <table>
        <tr>
            <td><strong>MATRIC NUMBER:</strong> {{ $info->matric_number }}</td>
            <td><strong>FULL NAME:</strong> {{ $info->applicant->lastname.' '. $info->applicant->othernames }}</td>
        </tr>
        <tr>
            <td><strong>FACULTY:</strong> {{ $info->faculty->name }}</td>
            <td><strong>DEPARTMENT:</strong> {{ $info->department->name }}</td>
        </tr>
    </table>
    <br>
    
    @foreach($levels as $level => $courses)
        @php
            $totalPoints = $courses->sum('points');
            $totalCreditUnits = $courses->sum('course_credit_unit');
        @endphp
        <h2 class="text-center">Level {{ $level }}</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Code</th>
                    <th>Course Title</th>
                    <th>Unit</th>
                    <th>Total Score</th>
                    <th>Grade</th>
                    <th>Point</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $registeredCourse)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $registeredCourse->course->code }}</td>
                        <td>{{ ucwords(strtolower($registeredCourse->course->name)) }}</td>
                        <td>{{ $registeredCourse->course_credit_unit }}</td>
                        <td>{{ $registeredCourse->total }}</td>
                        <td>{{ $registeredCourse->grade }}</td>
                        <td>{{ $registeredCourse->points }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td></td><td></td><td><strong>Total</strong></td>
                    <td>{{ $totalCreditUnits }}</td><td></td><td></td>
                    <td>{{ $totalPoints }}</td>
                </tr>
            </tbody>
        </table>
        <h4>G.P.A: {{ number_format($totalPoints / $totalCreditUnits, 2) }}</h4>
        <br>
    @endforeach
    
    <h2 class="text-center">Cumulative Summary</h2>
    <h4>Total Credit Unit: {{ $cgpaData->levelTotalUnit }}</h4>
    <h4>Total Credit Point: {{ $cgpaData->levelTotalPoint }}</h4>
    <h4>C.G.P.A: {{ $cgpaData->levelCGPA }}</h4>
</div>
<div class="watermark"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
