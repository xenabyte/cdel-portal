<div class="card-body table-responsive">
    <table id="buttons-result" class="display table table-bordered table-striped p-3" style="width:100%">
        <thead>
            <tr>
                <th colspan="{{ 17 + (count($classifiedCourses) * 6) }}" class="text-center fw-bold h5 py-3">
                    Result(s) for {{ $academiclevel->level }} Level, {{ !empty($programme) ? $programme->name : null }} for {{ $academicSession }} Academic Session
                </th>
            </tr>
            <tr>
                <th rowspan="2">SN</th>
                <th rowspan="2">Student Name</th>
                <th rowspan="2">Matric Number</th>
                <th rowspan="2">Degree Class</th>
                <th rowspan="2">Standing</th>
                <th rowspan="2">No of Failed Course</th>
                <th rowspan="2">Total Failed Unit</th>
                <th rowspan="2">Failed Courses</th>
                <th class="bg bg-info text-light" rowspan="2">Previous Total Credit Units</th>
                <th class="bg bg-info text-light" rowspan="2">Previous Total Credit Points</th>
                <th class="bg bg-info text-light" rowspan="2">Previous CGPA</th>
                <th class="bg bg-primary text-light" rowspan="2">Current Total Credit Units</th>
                <th class="bg bg-primary text-light" rowspan="2">Current Total Credit Points</th>
                <th class="bg bg-primary text-light" rowspan="2">Current GPA</th>
                <th class="bg bg-dark text-light" rowspan="2">Cumulative Total Credit Units</th>
                <th class="bg bg-dark text-light" rowspan="2">Cumulative Total Credit Points</th>
                <th class="bg bg-dark text-light" rowspan="2">Cumulative CGPA</th>
                @foreach($classifiedCourses as $courseName => $groupedStudents)
                    <th class="bg bg-dark text-light" colspan="6">{{ $courseName }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($classifiedCourses as $courseName => $groupedStudents)
                    <th class="bg bg-soft-dark">Credit Unit</th>
                    <th class="bg bg-soft-dark">Test Score</th>
                    <th class="bg bg-soft-dark">Exam Score</th>
                    <th class="bg bg-soft-dark">Total Score</th>
                    <th class="bg bg-soft-dark">Point</th>
                    <th class="bg bg-soft-dark">Grade</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(!empty($students))
                @foreach($students as $student)
                    @php
                        $degreeClass = new \App\Models\DegreeClass;
                        $viewSemesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)
                            ->where('level_id', $academicLevel->id)
                            ->where('academic_session', $academicSession);

                        $semesterRegisteredCourses = $viewSemesterRegisteredCourses->where('grade', '!=', null);

                        $currentRegisteredCreditUnits = $semesterRegisteredCourses->sum('course_credit_unit');
                        $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
                        $currentGPA = $currentRegisteredCreditUnits > 0 
                            ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) 
                            : 0;

                        $failedSemesterCourses = $semesterRegisteredCourses->where('grade', 'F');

                        $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                        $allRegisteredCreditUnits = $allRegisteredCourses->sum('course_credit_unit');
                        $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                        $CGPA = $allRegisteredCreditUnits > 0 
                            ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) 
                            : 0;

                        $prevRegisteredCourses = $student->registeredCourses
                            ->where('semester', '!=', $semester)
                            ->where('level_id', '!=', $academicLevel->id);
                        $prevRegisteredCreditUnits = $prevRegisteredCourses->sum('course_credit_unit');
                        $prevRegisteredGradePoints = $prevRegisteredCourses->sum('points');
                        $prevCGPA = $prevRegisteredCreditUnits > 0 
                            ? number_format($prevRegisteredGradePoints / $prevRegisteredCreditUnits, 2) 
                            : 0;

                        $classGrade = $degreeClass->computeClass($CGPA);
                        $class = $classGrade->degree_class ?? 'N/A';
                        $standing = ($classGrade->id ?? 5) > 4 ? 'NGS' : 'GS'; 
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ strtoupper(optional($student->applicant)->lastname).', '.optional($student->applicant)->othernames }}</td>
                        <td>{{ $student->matric_number }}</td>
                        <td>{{ $class }}</td>
                        <td>{{ $standing }}</td>
                        <td class="text-danger">{{ $failedSemesterCourses->count() }}</td>
                        <td class="text-danger">{{ $failedSemesterCourses->sum('course_credit_unit') }}</td>
                        <td class="text-danger">
                            @foreach($failedSemesterCourses as $failedCourse)
                                {{ $failedCourse->course_code }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="bg bg-soft-info">{{ $prevRegisteredCreditUnits }}</td>
                        <td class="bg bg-soft-info">{{ $prevRegisteredGradePoints }}</td>
                        <td class="bg bg-soft-info">{{ $prevCGPA }}</td>
                        <td class="bg bg-soft-primary">{{ $currentRegisteredCreditUnits }}</td>
                        <td class="bg bg-soft-primary">{{ $currentRegisteredGradePoints }}</td>
                        <td class="bg bg-soft-primary">{{ $currentGPA }}</td>
                        <td class="bg bg-soft-dark">{{ $allRegisteredCreditUnits }}</td>
                        <td class="bg bg-soft-dark">{{ $allRegisteredGradePoints }}</td>
                        <td class="bg bg-soft-dark">{{ $CGPA }}</td>
                        
                        @foreach($classifiedCourses as $courseName => $groupedStudents)
                            @php
                                $courseDetails = $student->registeredCourses->where('course_code', $courseName)->first();
                            @endphp
                            <td>{{ $courseDetails->course_credit_unit ?? '' }}</td>
                            <td>{{ $courseDetails->ca_score ?? '' }}</td>
                            <td>{{ $courseDetails->exam_score ?? '' }}</td>
                            <td>{{ $courseDetails->total ?? '' }}</td>
                            <td>{{ $courseDetails->points ?? '' }}</td>
                            <td>{{ $courseDetails->grade ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>