@extends('staff.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Programme(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Programme(s)</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">Programmes</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adviserProgrammes as $adviserProgramme)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$adviserProgramme->programme->name}}</td>
                            <td>{{$adviserProgramme->level->level}} Level</td>
                            <td>
                                <a href="{{ url('/staff/programme/'.$adviserProgramme->programme->slug) }}" class="btn btn-lg btn-primary">Programme Details</a>
                                <a href="{{ url('/staff/levelCourseReg/'.$adviserProgramme->id) }}" class="btn btn-lg btn-primary">Course Registrations</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                      
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

@endsection
