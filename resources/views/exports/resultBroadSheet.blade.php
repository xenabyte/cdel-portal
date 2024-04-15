<div class="card-body table-responsive">
    <!-- Bordered Tables -->
    <table id="buttons-result" class="display table table-bordered table-striped p-3" style="width:100%">
        <thead>
            <tr>
                <th rowspan="17">SN</th>
                <th rowspan="17">Student Name</th>
                <th rowspan="17">Matric Number</th>
                <th rowspan="17">Degree Class</th>
                <th rowspan="17">Standing</th>
                <th rowspan="17">No of failed course</th>
                <th rowspan="17">Total failed unit</th>
                <th rowspan="17">Failed courses</th>
                <th class="bg bg-info text-light" rowspan="17">Previous Total Credit Units</th>
                <th class="bg bg-info text-light" rowspan="17">Previous Total Credit Points</th>
                <th class="bg bg-info text-light" rowspan="17">Previous CGPA</th>
                <th class="bg bg-primary text-light" rowspan="17">Current Total Credit Units</th>
                <th class="bg bg-primary text-light" rowspan="17">Current Total Credit Points</th>
                <th class="bg bg-primary text-light" rowspan="17">Current GPA</th>
                <th class="bg bg-dark text-light" rowspan="17">Cumulative Total Credit Units</th>
                <th class="bg bg-dark text-light" rowspan="17">Cumulative Total Credit Points</th>
                <th class="bg bg-dark text-light" rowspan="17">Cumulative CGPA</th>
                @foreach($classifiedCourses as $courseName => $students)
                    <th class="bg bg-dark text-light" colspan="6">{{ $courseName }}</th>
                @endforeach
            </tr>
            <tr>
                <!-- Subtable headers -->
                @foreach($classifiedCourses as $courseName => $students)
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
            @foreach($students as $student)
                @if(!empty($students))
                    @php
                        $degreeClass = new \App\Models\DegreeClass;
                        $viewSemesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession);
                        $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
                        $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
                        $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
                        $currentGPA = $currentRegisteredGradePoints > 0 ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) : 0;
                        $failedSemesterCourses = $semesterRegisteredCourses->where('grade', 'F');
                        $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                        $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
                        $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                        $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0;
                        $prevRegisteredCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academicLevel->id);
                        $prevRegisteredCreditUnits =  $prevRegisteredCourses->sum('course_credit_unit');
                        $prevRegisteredGradePoints = $prevRegisteredCourses->sum('points');
                        $prevCGPA = ($prevRegisteredCreditUnits != 0) ? number_format($prevRegisteredGradePoints / $prevRegisteredCreditUnits, 2) : 0.00;
    
                        $classGrade = $degreeClass->computeClass($CGPA);
                        $class = $classGrade->degree_class;
                        $standing = ($classGrade->id > 4) ? 'NGS' : 'GS'; 
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ strtoupper($student->applicant->lastname).', '.$student->applicant->othernames }}</td>
                        <td>{{ $student->matric_number }}</td>
                        <td>{{$class}}</td>
                        <td>{{ $standing }}</td>
                        <td class="text-danger">{{ $failedSemesterCourses->count() }}</td>
                        <td class="text-danger">{{ $failedSemesterCourses->sum('course_credit_unit') }}</td>
                        <td>
                            @if($failedSemesterCourses->count() > 0)
                                <span class="text-danger">
                                    @foreach($failedSemesterCourses as $failedSemesterCourse)
                                        {{ $failedSemesterCourse->course_code }}
                                    @endforeach
                                </span>
                            @endif    
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
                        @foreach($classifiedCourses as $courseName => $students)
                            @php
                                $courseDetails = $student->registeredCourses->where('course_code', $courseName)->first();
                            @endphp
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->course_credit_unit }}
                                @endif
                            </td>
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->ca_score }}
                                @endif
                            </td>
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->exam_score }}
                                @endif
                            </td>
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->total }}
                                @endif
                            </td>
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->points }}
                                @endif
                            </td>
                            <td>
                                @if($courseDetails)
                                    {{ $courseDetails->grade }}
                                @endif
                            </td>
                        @endforeach
                        
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>