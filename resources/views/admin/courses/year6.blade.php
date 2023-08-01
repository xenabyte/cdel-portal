<h4 class="card-title mb-0 flex-grow-1">Year 6</h4>
<div class="card">
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#year6first" role="tab" aria-selected="false">
                    First Semester
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#year6second" role="tab" aria-selected="false">
                    Second Semester
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content  text-muted">
            <div class="tab-pane active" id="year6first" role="tabpanel">
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
                        @foreach($programme->courses->where('level_id', 6)->where('semester', 1) as $course61)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course61->code}}</td>
                            <td>{{$course61->name }}</td>
                            <td>{{$course61->credit_unit}}</td>
                            <td>{{$course61->status}}</td>
                        </tr>

                        <div id="editCourse{{$course61->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 overflow-hidden">
                                    <div class="modal-header p-3">
                                        <h4 class="card-title mb-0">Update Course</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{ url('/admin/updateCourse') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course61->id }}">

                                            <div class="mb-3">
                                                <label for="code" class="form-label">Course Code</label>
                                                <input type="text" required class="form-control" name='code' id="code" value="{{ $course61->code }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="title" class="form-label">Course Title</label>
                                                <input type="text" required class="form-control" name='title' id="title" value="{{ $course61->title }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="unit" class="form-label">Course Credit Unit</label>
                                                <input type="number" required class="form-control" name='unit' id="unit" value="{{ $course61->unit }}">
                                            </div>


                                            <div class="mb-3">
                                                <label class="form-label">Select Semester</label>
                                                <select class="form-select" aria-label="semester" name="semester">
                                                    <option value= "" >Select Semester</option>
                                                    <option {{ $course61->semester == 1? 'selected' : ''  }} value="1">First Semester</option>
                                                    <option {{ $course61->semester == 2? 'selected' : ''  }} value="2">Second Semester</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Select Course Status</label>
                                                <select class="form-select" aria-label="status" name="status">
                                                    <option value= "" >Select Course Status</option>
                                                    <option {{ $course61->status == 'Core' ? 'selected' : ''  }} value="Core">Core</option>
                                                    <option {{ $course61->status == 'Elective' ? 'selected' : ''  }} value="Elective">Elective</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Select Level</label>
                                                <select class="form-select" aria-label="status" name="level">
                                                    <option value= "" >Select Year</option>
                                                    <option {{ $course61->level == 1 ? 'selected' : ''  }} value="1">Year 1</option>
                                                    <option {{ $course61->level == 2 ? 'selected' : ''  }} value="2">Year 2</option>
                                                    <option {{ $course61->level == 3 ? 'selected' : ''  }} value="3">Year 3</option>
                                                    <option {{ $course61->level == 4 ? 'selected' : ''  }} value="4">Year 4</option>
                                                    <option {{ $course61->level == 5 ? 'selected' : ''  }} value="5">Year 5</option>
                                                    <option {{ $course61->level == 6 ? 'selected' : ''  }} value="6">Year 6</option>
                                                    <option {{ $course61->level == 7 ? 'selected' : ''  }} value="7">Year 7</option>
                                                    <option {{ $course61->level == 8 ? 'selected' : ''  }}  value="8">Year 8</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="course_outline" class="form-label">Course Outline</label>
                                                <textarea class="form-control" name="course_outline" id="course_outline" placeholder="Course Outline">{{ $course61->course_outline }}</textarea>
                                            </div>

                                            <hr>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary">Update Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->


                        <div id="deleteCourse{{$course61->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <div class="text-end">
                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="mt-2">
                                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                            </lord-icon>
                                            <h4 class="mb-3 mt-4">Are you sure you want to delete {{ $course61->title }}?</h4>
                                            <form action="{{ url('/admin/deleteCourse') }}" method="POST">
                                                @csrf
                                                <input name="course_id" type="hidden" value="{{$course61->id}}">

                                                <hr>
                                                <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 justify-content-center">

                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="year6second" role="tabpanel">
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
                            @foreach($programme->courses->where('level_id', 6)->where('semester', 2) as $course62)
                            <tr>
                                <td scope="row"> {{ $loop->iteration }}</td>
                                <td>{{$course62->code}}</td>
                                <td>{{$course62->title }} <hr> <?php echo $course62->course_outline ?></td>
                                <td>{{$course62->credit_unit}}</td>
                                <td>{{$course62->status}}</td>
                            </tr>

                            <div id="editCourse{{$course62->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Update Course</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="{{ url('/admin/updateCourse') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $course62->id }}">

                                                <div class="mb-3">
                                                    <label for="code" class="form-label">Course Code</label>
                                                    <input type="text" required class="form-control" name='code' id="code" value="{{ $course62->code }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Course Title</label>
                                                    <input type="text" required class="form-control" name='title' id="title" value="{{ $course62->title }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="unit" class="form-label">Course Credit Unit</label>
                                                    <input type="number" required class="form-control" name='unit' id="unit" value="{{ $course62->unit }}">
                                                </div>


                                                <div class="mb-3">
                                                    <label class="form-label">Select Semester</label>
                                                    <select class="form-select" aria-label="semester" name="semester">
                                                        <option value= "" >Select Semester</option>
                                                        <option {{ $course62->semester == 1? 'selected' : ''  }} value="1">First Semester</option>
                                                        <option {{ $course62->semester == 2? 'selected' : ''  }} value="2">Second Semester</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Select Course Status</label>
                                                    <select class="form-select" aria-label="status" name="status">
                                                        <option value= "" >Select Course Status</option>
                                                        <option {{ $course62->status == 'Core' ? 'selected' : ''  }} value="Core">Core</option>
                                                        <option {{ $course62->status == 'Elective' ? 'selected' : ''  }} value="Elective">Elective</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Select Level</label>
                                                    <select class="form-select" aria-label="status" name="level">
                                                        <option value= "" >Select Year</option>
                                                        <option {{ $course62->level == 1 ? 'selected' : ''  }} value="1">Year 1</option>
                                                        <option {{ $course62->level == 2 ? 'selected' : ''  }} value="2">Year 2</option>
                                                        <option {{ $course62->level == 3 ? 'selected' : ''  }} value="3">Year 3</option>
                                                        <option {{ $course62->level == 4 ? 'selected' : ''  }} value="4">Year 4</option>
                                                        <option {{ $course62->level == 5 ? 'selected' : ''  }} value="5">Year 5</option>
                                                        <option {{ $course62->level == 6 ? 'selected' : ''  }} value="6">Year 6</option>
                                                        <option {{ $course62->level == 7 ? 'selected' : ''  }} value="7">Year 7</option>
                                                        <option {{ $course62->level == 8 ? 'selected' : ''  }}  value="8">Year 8</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="course_outline" class="form-label">Course Outline</label>
                                                    <textarea class="form-control" name="course_outline" id="course_outline" placeholder="Course Outline">{{ $course62->course_outline }}</textarea>
                                                </div>

                                                <hr>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Update Course</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->


                            <div id="deleteCourse{{$course62->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete {{ $course62->title }}?</h4>
                                                <form action="{{ url('/admin/deleteCourse') }}" method="POST">
                                                    @csrf
                                                    <input name="course_id" type="hidden" value="{{$course62->id}}">

                                                    <hr>
                                                    <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 justify-content-center">

                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end card-body -->
</div><!-- end card -->
