<h4 class="card-title mb-0 flex-grow-1">Year 4</h4>
<div class="card">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#year4first" role="tab" aria-selected="false">
                    Harmattan Semester
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#year4second" role="tab" aria-selected="false">
                    Rain Semester
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content  text-muted">
            <div class="tab-pane active" id="year4first" role="tabpanel">
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
                        @foreach($programme->courses->where('level_id', 4)->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->where('semester', 1) as $course41)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course41->course->code}}</td>
                            <td>{{$course41->course->name }}</td>
                            <td>{{$course41->credit_unit}}</td>
                            <td>{{$course41->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="year4second" role="tabpanel">
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
                            @foreach($programme->courses->where('level_id', 4)->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->where('semester', 2) as $course42)
                            <tr>
                                <td scope="row"> {{ $loop->iteration }}</td>
                                <td>{{$course42->course->code}}</td>
                                <td>{{$course42->course->name }}</td>
                                <td>{{$course42->credit_unit}}</td>
                                <td>{{$course42->status}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end card-body -->
</div><!-- end card -->
