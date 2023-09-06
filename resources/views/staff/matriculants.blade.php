@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Matriculants for {{ $pageGlobalData->sessionSetting->admission_session }} admission session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Matriculants</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Matriculants for {{ $pageGlobalData->sessionSetting->admission_session }} admission session</h4>

            </div><!-- end card header -->

            <div class="card-body">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Applied Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matriculants as $matriculant)
                        @if($matriculant->student->is_active)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $matriculant->lastname .' '. $matriculant->othernames }}</td>
                            <td>{{ $matriculant->student->matric_number }}</td>
                            <td>{{ $matriculant->student->programme->name }}</td>
                            <td>{{ $matriculant->student->email }} </td>
                            <td>{{ $matriculant->phone_number }} </td>
                            <td>{{ $matriculant->academic_session }} </td>
                            <td>{{ $matriculant->created_at }} </td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$matriculant->student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View matriculant</a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
