@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Campus Capacity</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Campus Capacity</li>
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
                <h4 class="card-title mb-0 flex-grow-1">University Programmes (Undergraduates and Postgraduates)</h4>
            </div><!-- end card header -->

            @if(!empty($programmes) && $programmes->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Programmes</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Student Strength</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programmes as $programme)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $programme->name }} </td>
                                    <td>{{ $programme->programmeCategory->category }}</td>
                                    <td>{{ $programme->students->count() }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- end col -->
                </div>
            </div>
            @endif
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@endsection
