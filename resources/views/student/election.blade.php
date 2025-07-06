@extends('student.layout.dashboard')
@php
$student = Auth::guard('student')->user();
$studentId = $student->id;
@endphp
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Election/poll Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Election/poll Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row project-wrapper">
    <div class="col-xxl-8 card-height-100">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary rounded-2 fs-2">
                                    <i data-feather="briefcase"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Positions</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $election->positions->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-2 fs-2">
                                    <i data-feather="award"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Candidates</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $election->candidates->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Election/Poll Overview - {{ $election->title }}</h4>
                    </div><!-- end card header -->

                    <div class="card-body border border-dashed border-start-0">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <p><strong>Type:</strong> {{ ucfirst($election->type) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    @if(now() < $election->start_time)
                                        <span class="badge bg-warning">Upcoming</span>
                                    @elseif(now() >= $election->start_time && now() <= $election->end_time)
                                        <span class="badge bg-success">Ongoing</span>
                                    @else
                                        <span class="badge bg-secondary">Ended</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($election->start_time)->format('D, M j, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($election->end_time)->format('D, M j, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-12">
                                <p><strong>Description:</strong> {!! nl2br(e($election->description)) !!}</p>
                            </div>
                        </div>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
           
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card card-height-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Election/Poll Image</h4>
            </div><!-- end cardheader -->
            @if(!empty($election->image))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $election->image }}" alt="Card image cap">
                
                <div class="card-header p-0 border-0 bg-soft-light">
                    <div class="row g-0 text-center">
                        <div class="col-12 col-sm-12">
                            <div class="p-3 border border-dashed border-start-0">

                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div><!-- end cardbody -->
            @endif
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


@if($election->positions->count() > 0)
    <div class="row">
        @foreach($election->positions as $position)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-truncate">Position: {{ $position->title }}</h5>
                </div>

                <div class="card-body table-responsive">
                    @php
                        $positionCandidates = $election->candidates->where('position_id', $position->id);
                        $hasVoted = $position->votes->where('student_id', $studentId)->count() > 0;
                    @endphp

                    @if($positionCandidates->count() > 0)
                    <div class="mt-3">
                        <canvas id="chart-{{ $position->id }}" height="150"></canvas>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const ctx{{ $position->id }} = document.getElementById('chart-{{ $position->id }}').getContext('2d');

                            new Chart(ctx{{ $position->id }}, {
                                type: 'bar',
                                data: {
                                    labels: [
                                        @foreach($positionCandidates as $c)
                                            "{{ $c->student->applicant->lastname }} {{ $c->student->applicant->othernames }}",
                                        @endforeach
                                    ],
                                    datasets: [{
                                        label: 'Votes',
                                        data: [
                                            @foreach($positionCandidates as $c)
                                                {{ $c->votes->count() }},
                                            @endforeach
                                        ],
                                        backgroundColor: '#3b76e1',
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.parsed.y + ' vote(s)';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            },
                                            title: {
                                                display: true,
                                                text: 'Votes'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Candidates'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Candidate</th>
                                <th>Votes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positionCandidates as $candidate)
                            @php
                                $photo = $candidate->photo 
                                    ?: ($candidate->student->image ?? asset('assets/images/users/avatar-1.jpg'));
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $photo }}"
                                            alt="Candidate Photo"
                                            class="rounded-circle"
                                            width="80"
                                            height="80">
                                        <div>
                                            <strong>{{ $candidate->student->applicant->lastname }} {{ $candidate->student->applicant->othernames }}</strong><br>
                                            <small>{{ $candidate->student->matric_number }}</small><br>
                                            <small>{{ $candidate->student->academicLevel->level }}L • {{ $candidate->student->programme->name }}</small><br>
                                            @if($candidate->manifesto)
                                            <button class="btn btn-link p-0 text-info" data-bs-toggle="modal" data-bs-target="#manifestoModal{{ $candidate->id }}">
                                                <small><i class="ri-file-text-line"></i> View Manifesto</small>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $candidate->votes->count() }}</td>
                                <td class="text-end">
                                    @if(now() >= $election->start_time && now() <= $election->end_time)
                                        @if(!$hasVoted)
                                            <form action="{{ url('/student/castVote') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    Vote
                                                </button>
                                            </form>
                                        @elseif($candidate->votes->where('student_id', $studentId)->count() > 0)
                                            <span class="badge bg-info">Your Vote</span>
                                        @else
                                            <span class="badge bg-secondary">Voted</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Voting closed</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Manifesto Modal --}}
                            <div class="modal fade" id="manifestoModal{{ $candidate->id }}" tabindex="-1" aria-labelledby="manifestoModalLabel{{ $candidate->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="manifestoModalLabel{{ $candidate->id }}">Manifesto of {{ $candidate->student->applicant->lastname }} {{ $candidate->student->applicant->othernames }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body border border-dashed border-start-0">
                                            {!! nl2br(e($candidate->manifesto)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p class="text-muted mb-0">No candidates available.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <p class="text-muted">No positions have been added for this election.</p>
@endif


@endsection