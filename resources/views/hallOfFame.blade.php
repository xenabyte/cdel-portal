@extends('layouts.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Hall of Fame</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Hall of Fame</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <ul class="list-inline categories-filter" id="filter">
                                <li class="list-inline-item">
                                    <button class="btn btn-sm btn-primary active" data-filter="all">All Levels</button>
                                </li>
                                <li class="list-inline-item">
                                    <button class="btn btn-sm btn-secondary" data-filter="100">100 Level</button>
                                </li>
                                <li class="list-inline-item">
                                    <button class="btn btn-sm btn-secondary" data-filter="200">200 Level</button>
                                </li>
                                <li class="list-inline-item">
                                    <button class="btn btn-sm btn-secondary" data-filter="300">300 Level</button>
                                </li>
                                <li class="list-inline-item">
                                    <button class="btn btn-sm btn-secondary" data-filter="400">400 Level</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="hofTable" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Level</th>
                                <th>CGPA</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $hofStudent)
                                <tr data-category="{{ ($hofStudent->level_id - 1) * 100 }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img class="img-thumbnail" 
                                            src="{{ asset($hofStudent->image) }}" 
                                            alt="Student Image" width="50" />
                                    </td>
                                    <td>{{ ucwords(strtolower($hofStudent->applicant->lastname .' '. $hofStudent->applicant->othernames)) }}</td>
                                    <td>{{ ($hofStudent->level_id - 1) * 100 }} Level</td>
                                    <td>{{ $hofStudent->cgpa }}</td>
                                    <td>
                                        <a href="{{ url('studentDetails/'.$hofStudent->slug) }}" class="btn btn-sm btn-info">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End DataTable -->
            </div>
        </div>
    </div>
</div>
@endsection