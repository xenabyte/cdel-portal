<h4 class="card-title mb-0 flex-grow-1">Year 5</h4>
<div class="card">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#year5first" role="tab" aria-selected="false">
                    First Semester
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#year5second" role="tab" aria-selected="false">
                    Second Semester
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content  text-muted">
            <div class="tab-pane active" id="year5first" role="tabpanel">
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
                        @foreach($programme->courses->where('level_id', 5)->where('semester', 1) as $course51)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course51->course->code}}</td>
                            <td>{{$course51->course->name }}</td>
                            <td>{{$course51->credit_unit}}</td>
                            <td>{{$course51->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="year5second" role="tabpanel">
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
                            @foreach($programme->courses->where('level_id', 5)->where('semester', 2) as $course52)
                            <tr>
                                <td scope="row"> {{ $loop->iteration }}</td>
                                <td>{{$course52->course->code}}</td>
                                <td>{{$course52->course->name }}</td>
                                <td>{{$course52->credit_unit}}</td>
                                <td>{{$course52->status}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end card-body -->
</div><!-- end card -->
