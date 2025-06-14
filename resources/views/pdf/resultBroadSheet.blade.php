<!DOCTYPE html>
<html>
<head>
    <title>Result Broadsheet</title>
    <style>
        /* @page {
            margin-bottom: 80px;
        } */

        table.broadsheet th:nth-child(1),
        table.broadsheet td:nth-child(1) {
            width: 30px; /* SN */
        }

        table.broadsheet th:nth-child(2),
        table.broadsheet td:nth-child(2) {
            width: 180px; /* Student Name */
        }

        table.broadsheet th:nth-child(3),
        table.broadsheet td:nth-child(3) {
            width: 120px; /* Matric Number */
        }

        table.broadsheet th:nth-child(4),
        table.broadsheet td:nth-child(4) {
            width: 100px; /* Degree Class */
        }

        /* Optional: wrap long course columns if needed */
        table.broadsheet th,
        table.broadsheet td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
        }
        body {
            font-size: 11px;
            margin: 0;
            padding: 0;
            padding-top: 80px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        .page-break {
            page-break-before: always;
        }
        .signature td {
            border: none;
            padding-top: 40px;
            text-align: center;
        }
        .watermark {
            position: fixed;
            top: 35%;
            left: 25%;
            width: 50%;
            opacity: 0.04;
            z-index: -1;
        }
        .header-table td {
            padding-left: 5px !important;
        }
        .summary-row {
            width: 100%;
            margin-top: 20px;
        }

        .summary-col {
            float: left;
            width: 32%;
            margin-right: 2%;
            font-size: 12px;
        }

        .summary-col:last-child {
            margin-right: 0;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .summary-table caption {
            text-align: left;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 10px;
        }

        .summary-card h4 {
            margin-top: 0;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .legend-color {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<img src="{{ env('SCHOOL_LOGO') }}" class="watermark">
<table style="width: 100%; margin-top: -10px;">
    <tbody>
        <tr>
            <td style="width: 100%; border: none;">
                <img src="{{ env('SCHOOL_LOGO') }}" width="20%">
            </td>
        </tr>
    </tbody>
</table>

<div class="row" style="margin-top: 5px;">
    <div class="text-center">
        <h2 style="text-align: center; font-weight: bold; font-size: 14px; text-transform: capitalize; margin-bottom: 10px;">
            Examination Result for {{ $academicLevel->level }} Level,
            {{ $programme->name }} — {{ $semesterName }} Semester,
            {{ $academicSession }} Academic Session
        </h2>
        <hr style="width: 60px; border: 2px solid #000; margin: 10px auto;">
    </div>
</div>


{{-- PAGE 1: Registered Courses Summary --}}
<div>
    <h3 style="text-align: center; margin-top: 5px;">List of Registered Courses</h3>
    {{-- <p style="text-align: center;">
        {{ $academicLevel->level }} Level — {{ $programme->name }} — {{ $semesterName }} Semester<br>
        {{ $academicSession }} Academic Session
    </p> --}}

    <table style="width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 13px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 6px;">S/N</th>
                <th style="border: 1px solid #000; padding: 6px;">Course Code</th>
                <th style="border: 1px solid #000; padding: 6px;">Course Name</th>
                <th style="border: 1px solid #000; padding: 6px;">Credit Unit</th>
                <th style="border: 1px solid #000; padding: 6px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $sn = 1; @endphp
            @foreach($classifiedCourses as $code => $group)
                @php
                    $course = collect($group['students'])->first()->registeredCourses
                        ->where('course_code', $code)
                        ->where('semester', $semester)
                        ->where('level_id', $academicLevel->id)
                        ->where('academic_session', $academicSession)
                        ->first();

                    $courseName = $course->course->name ?? 'N/A';
                    $creditUnit = $course->course_credit_unit ?? '—';
                    $status = ucfirst($course->course_status ?? '—');
                @endphp
                <tr>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $sn++ }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $code }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: left;">{{ $courseName }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $creditUnit }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- PAGE 2 Broadsheet Key Summary --}}
@php
    $totalStudents = count($students);
    $goodStandingCount = 0;
    $notInGoodStandingCount = 0;

    $totalStudentsWithNullGrades = 0;

    $semesterGoodStandingCount = 0;
    $semesterNotInGoodStandingCount =0;

    $degreeClassCounts = (object) [
        'First Class' => 0,
        'Second Class Upper' => 0,
        'Second Class Lower' => 0,
        'Third Class' => 0,
        'Pass' => 0,
        'Fail' => 0,
    ];

    $degreeClass = new \App\Models\DegreeClass;

    foreach($students as $student){
        $semesterRegisteredCourses = $student->registeredCourses
            ->where('semester', $semester)
            ->where('level_id', $academicLevel->id)
            ->where('academic_session', $academicSession);

        $nullGradeCount = $semesterRegisteredCourses->where('grade', null)->count();

        $totalCoursesCount = $semesterRegisteredCourses->count();
        $eightyPercent = 0.8 * $totalCoursesCount;

        if ($nullGradeCount >= $eightyPercent) {
            $totalStudentsWithNullGrades++;
        }

        $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
        $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
        $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
        $currentGPA = $currentRegisteredGradePoints > 0 ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) : 0.00;

        $semesterRegisteredMissingCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession)->where('grade', null);

        $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
        $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
        $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
        $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0.00;

        $semesterClassGrade = $degreeClass->computeClass($currentGPA);
        $semesterClass = $semesterClassGrade->degree_class;
        $semesterStanding = $semesterClassGrade->id > 4? 'NGS' : 'GS'; 

        if ($semesterStanding === 'GS') {
            $semesterGoodStandingCount++;
        } else {
            $semesterNotInGoodStandingCount++;
        }

        $classGrade = $degreeClass->computeClass($CGPA);
        $class = $classGrade->degree_class;
        $standing = $classGrade->id > 4? 'NGS' : 'GS'; 

        if ($standing === 'GS') {
            $goodStandingCount++;
        } else {
            $notInGoodStandingCount++;
        }

        switch ($class) {
            case 'First Class':
                $degreeClassCounts->{'First Class'}++;
                break;
            case 'Second Class Upper':
                $degreeClassCounts->{'Second Class Upper'}++;
                break;
            case 'Second Class Lower':
                $degreeClassCounts->{'Second Class Lower'}++;
                break;
            case 'Third Class':
                $degreeClassCounts->{'Third Class'}++;
                break;
            case 'Pass':
                $degreeClassCounts->{'Pass'}++;
                break;
            default:
                $degreeClassCounts->{'Fail'}++;
                break;
        }
    }

    $goodStandingPercentage = $totalStudents > 0 ? ($goodStandingCount / $totalStudents) * 100 : 0;
    $notInGoodStandingPercentage = $totalStudents > 0 ? ($notInGoodStandingCount / $totalStudents) * 100 : 0;

    $semesterGoodStandingPercentage = $totalStudents > 0 ? ($semesterGoodStandingCount / $totalStudents) * 100 : 0;
    $semesterNotInGoodStandingPercentage = $totalStudents > 0 ? ($semesterNotInGoodStandingCount / $totalStudents) * 100 : 0;
    
@endphp

<div class="summary-row page-break">

    {{-- Summary 1: This Semester --}}
    <div class="summary-col">
        <table class="summary-table">
            <caption>Summary by Standing — <em>This Semester</em></caption>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Students</td>
                    <td>{{ $totalStudents }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Batch B/C</td>
                    <td>{{ $totalStudentsWithNullGrades }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>GS</td>
                    <td>{{ $semesterGoodStandingCount }}</td>
                    <td>{{ number_format($semesterGoodStandingPercentage, 2) }}%</td>
                </tr>
                <tr>
                    <td>NGS</td>
                    <td>{{ $semesterNotInGoodStandingCount }}</td>
                    <td>{{ number_format($semesterNotInGoodStandingPercentage, 2) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Summary 2: Overall --}}
    <div class="summary-col">
        <table class="summary-table">
            <caption>Summary by Standing — <em>Overall</em></caption>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Students</td>
                    <td>{{ $totalStudents }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>GS</td>
                    <td>{{ $goodStandingCount }}</td>
                    <td>{{ number_format($goodStandingPercentage, 2) }}%</td>
                </tr>
                <tr>
                    <td>NGS</td>
                    <td>{{ $notInGoodStandingCount }}</td>
                    <td>{{ number_format($notInGoodStandingPercentage, 2) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Summary 3: Degree Class --}}
    <div class="summary-col">
        <div class="summary-card">
            <h4>Summary by Degree Class</h4>
            <p><strong>Total:</strong> {{ $totalStudents }}</p>

            @foreach($degreeClassCounts as $degreeClass => $count)
                @php
                    $percentage = $totalStudents > 0 ? number_format(($count / $totalStudents) * 100, 2) : 0;
                    $color = match($degreeClass) {
                        'First Class' => '#007bff',
                        'Second Class Upper' => '#6c757d',
                        'Second Class Lower' => '#28a745',
                        'Third Class' => '#17a2b8',
                        'Pass' => '#ffc107',
                        default => '#dc3545',
                    };
                @endphp
                <div class="legend-item">
                    <span><span class="legend-color" style="background-color: {{ $color }}"></span> {{ $degreeClass }}</span>
                    <span>{{ $count }} ({{ $percentage }}%)</span>
                </div>
            @endforeach
        </div>
    </div>

</div>




{{-- PAGE 3 onward: Broadsheet Results --}}

<table class="header-table page-break">
    <tr>
        <td colspan="100" style="font-size:14px; font-weight:bold; padding: 15px 0;">
            Examination Result Broadsheet for {{ $academicLevel->level }} Level, {{ $programme->name }} — {{ $semesterName }} Semester, {{ $academicSession }} Academic Session
        </td>
    </tr>
    {{-- <tr>
        <td colspan="100" style="text-align:left; font-weight:bold; padding: 5px;">
            Faculty: {{ $faculty->name }} | Department: {{ $department->name }} | Programme: {{ $programme->name }} | Session: {{ $academicSession }}
        </td>
    </tr> --}}
</table>

<table class="broadsheet">
    <thead>
        <tr>
            <th>SN</th>
            <th>Student Name</th>
            <th>Matric Number</th>
            <th>Degree Class</th>
            <th>CCU</th>
            <th>CGP</th>
            <th>CGPA</th>
            @foreach($classifiedCourses as $code => $group)
                @php
                    $course = $group['course'];
                    $creditUnit = $course->course_credit_unit ?? '';
                    $courseStatus = strtoupper(substr($course->course_status ?? '', 0, 1));
                @endphp
                <th>{{ $code }} ({{ $creditUnit }} {{ $courseStatus }})</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $sns = 1; @endphp
        @foreach($students as $index => $student)
            @php
                $sns++;
                $degreeClass = new \App\Models\DegreeClass;

                $viewSemesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)
                    ->where('level_id', $academicLevel->id)
                    ->where('academic_session', $academicSession);

                $semesterRegisteredCourses = $viewSemesterRegisteredCourses->where('grade', '!=', null);
                $currentCU = $semesterRegisteredCourses->sum('course_credit_unit');
                $currentCP = $semesterRegisteredCourses->sum('points');
                $currentGPA = $currentCU ? number_format($currentCP / $currentCU, 2) : '0.00';

                $failedCourses = $semesterRegisteredCourses->where('grade', 'F');

                $allCourses = $student->registeredCourses->where('grade', '!=', null);
                $cummCU = $allCourses->sum('course_credit_unit');
                $cummCP = $allCourses->sum('points');
                $cummCGPA = $cummCU ? number_format($cummCP / $cummCU, 2) : '0.00';

                $prevCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academicLevel->id);
                $prevCU = $prevCourses->sum('course_credit_unit');
                $prevCP = $prevCourses->sum('points');
                $prevCGPA = $prevCU ? number_format($prevCP / $prevCU, 2) : '0.00';

                $classGrade = $degreeClass->computeClass($cummCGPA);
                $class = $classGrade->degree_class ?? 'N/A';
                $standing = ($classGrade->id ?? 5) > 4 ? 'NGS' : 'GS';
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ strtoupper(optional($student->applicant)->lastname . ', ' . optional($student->applicant)->othernames) }}</td>
                <td>{{ $student->matric_number }}</td>
                <td>{{ $class }}</td>
                <td>{{ $cummCU }}</td>
                <td>{{ $cummCP }}</td>
                <td>{{ $cummCGPA }}</td>
                @foreach($classifiedCourses as $code => $group)
                    @php $course = $student->registeredCourses->where('course_code', $code)->first(); @endphp
                    <td>
                        @if(isset($course->total, $course->grade))
                            @if(strtoupper($course->grade) === 'F')
                                <span style="color: red;">{{ $course->total . strtoupper($course->grade) }}</span>
                            @else
                                {{ $course->total . strtoupper($course->grade) }}
                            @endif
                        @else
                            --
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>


<table class="header-table page-break">
    <tr>
        <td colspan="100" style="text-align:left; font-weight:bold; padding: 5px;">
            Result Summary
        </td>
    </tr>
</table>
<table class="broadsheet">
    <thead>
        <tr>
            <th>SN</th>
            <th>Student Name</th>
            <th>Matric Number</th>
            <th>Degree Class</th>
            <th>Standing</th>
            <th>No of Failed Course</th>
            <th>Total Failed Unit</th>
            <th>Failed Courses</th>
            <th>Prev CU</th>
            <th>Prev CP</th>
            <th>Prev CGPA</th>
            <th>Curr CU</th>
            <th>Curr CP</th>
            <th>Curr GPA</th>
            <th>Cumm CU</th>
            <th>Cumm CP</th>
            <th>Cumm CGPA</th>
        </tr>
    </thead>
    <tbody>
        @php $counter = 1; @endphp
        @foreach($students as $index => $student)
            @php
                $counter++;
                $degreeClass = new \App\Models\DegreeClass;

                $viewSemesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)
                    ->where('level_id', $academicLevel->id)
                    ->where('academic_session', $academicSession);

                $semesterRegisteredCourses = $viewSemesterRegisteredCourses->where('grade', '!=', null);
                $currentCU = $semesterRegisteredCourses->sum('course_credit_unit');
                $currentCP = $semesterRegisteredCourses->sum('points');
                $currentGPA = $currentCU ? number_format($currentCP / $currentCU, 2) : '0.00';

                $failedCourses = $semesterRegisteredCourses->where('grade', 'F');

                $allCourses = $student->registeredCourses->where('grade', '!=', null);
                $cummCU = $allCourses->sum('course_credit_unit');
                $cummCP = $allCourses->sum('points');
                $cummCGPA = $cummCU ? number_format($cummCP / $cummCU, 2) : '0.00';

                $prevCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academicLevel->id);
                $prevCU = $prevCourses->sum('course_credit_unit');
                $prevCP = $prevCourses->sum('points');
                $prevCGPA = $prevCU ? number_format($prevCP / $prevCU, 2) : '0.00';

                $classGrade = $degreeClass->computeClass($cummCGPA);
                $class = $classGrade->degree_class ?? 'N/A';
                $standing = ($classGrade->id ?? 5) > 4 ? 'NGS' : 'GS';
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ strtoupper(optional($student->applicant)->lastname . ', ' . optional($student->applicant)->othernames) }}</td>
                <td>{{ $student->matric_number }}</td>
                <td>{{ $class }}</td>
                <td>{{ $standing }}</td>
                <td style="color:red">{{ $failedCourses->count() }}</td>
                <td style="color:red">{{ $failedCourses->sum('course_credit_unit') }}</td>
                <td style="color:red">
                    @foreach($failedCourses as $f)
                        {{ $f->course_code }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td>{{ $prevCU }}</td>
                <td>{{ $prevCP }}</td>
                <td>{{ $prevCGPA }}</td>
                <td>{{ $currentCU }}</td>
                <td>{{ $currentCP }}</td>
                <td>{{ $currentGPA }}</td>
                <td>{{ $cummCU }}</td>
                <td>{{ $cummCP }}</td>
                <td>{{ $cummCGPA }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <table style="width:100%; border: none;">
        <tr>
            <td style="text-align:center; border: none;">Examination Officer's Signature: __________________</td>
            <td style="text-align:center; border: none;">HOD's Signature: __________________</td>
            <td style="text-align:center; border: none;">Dean's Signature: __________________</td>
        </tr>
    </table>
    <div style="margin-top: 5px; text-align: center;">DATE: {{ date('F j, Y') }}</div>
</div>
</body>
</html>
