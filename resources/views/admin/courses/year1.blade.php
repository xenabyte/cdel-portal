<h4 class="card-title mb-0 flex-grow-1">Year 1</h4>
<div class="card">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#year1first" role="tab" aria-selected="false">
                    First Semester
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#year1second" role="tab" aria-selected="false">
                    Second Semester
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content  text-muted">
            <div class="tab-pane active" id="year1first" role="tabpanel">
                <!-- Tables Without Borders -->
                <table class="table table-borderless table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programme->courses->where('level_id', 1)->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->where('semester', 1) as $course11)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course11->course->code}}</td>
                            <td>{{$course11->course->name }}</td>
                            <td>{{$course11->credit_unit}} </td>
                            <td>{{$course11->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="year1second" role="tabpanel">
                <div class="tab-pane active" id="year1first" role="tabpanel">
                    <!-- Tables Without Borders -->
                    <table class="table table-borderless table-nowrap">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Course Code</th>
                                <th scope="col">Course Title</th>
                                <th scope="col">Course Unit</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programme->courses->where('level_id', 1)->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->where('semester', 2) as $course12)
                            <tr>
                                <td scope="row"> {{ $loop->iteration }}</td>
                                <td>{{$course12->course->code}}</td>
                                <td>{{$course12->course->name }}</td>
                                <td>{{$course12->credit_unit}}</td>
                                <td>{{$course12->status}}</td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end card-body -->
</div><!-- end card -->
