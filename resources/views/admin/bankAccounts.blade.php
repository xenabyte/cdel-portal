@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Bank Accounts</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Bank Accounts</li>
                </ol>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Bank Accounts</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Bank Account</button>
                    </div>
                </div><!-- end card header -->

                @if(!empty($bankAccounts) && $bankAccounts->count() > 0)
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-xl-12">
                            
                            <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Bank Name</th>
                                        <th scope="col">Account Purpose</th>
                                        <th scope="col">Account Name</th>
                                        <th scope="col">Account Number</th>
                                        <th scope="col">UpperLink Account Code</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bankAccounts as $bankAccount)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $bankAccount->bank_name }} </td>
                                        <td>{{ $bankAccount->account_purpose }} </td>
                                        <td>{{ $bankAccount->account_name }} </td>
                                        <td>{{ $bankAccount->account_number }} </td>
                                        <td>{{ $bankAccount->account_code }} </td>
                                        <td>
                                            <div class="hstack gap-3 fs-15">
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$bankAccount->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$bankAccount->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

                                                <div id="delete{{$bankAccount->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <div class="text-end">
                                                                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                                    </lord-icon>
                                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete bank details for <br/> {{ $bankAccount->account_purpose }}?</h4>
                                                                    <form action="{{ url('/admin/deleteBankAccount') }}" method="POST">
                                                                        @csrf
                                                                        <input name="bank_account_id" type="hidden" value="{{$bankAccount->id}}">
                                                                        <hr>
                                                                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light p-3 justify-content-center">

                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->

                                                <div id="edit{{$bankAccount->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 overflow-hidden">
                                                            <div class="modal-header p-3">
                                                                <h4 class="card-title mb-0">Edit Bank Account</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                    
                                                            <div class="modal-body">
                                                                <form action="{{ url('/admin/updateBankAccount') }}" method="post" enctype="multipart/form-data">
                                                                    @csrf

                                                                    <input name="bank_account_id" type="hidden" value="{{$bankAccount->id}}">

                                                                    <div class="mb-3">
                                                                        <label for="role" class="form-label">Select Account Purpose</label>
                                                                        <select class="form-select" aria-label="role" name="account_purpose">
                                                                            <option {{ $bankAccount->account_purpose =='ICT'?'selected':'' }} value="ICT">ICT</option>
                                                                            <option {{ $bankAccount->account_purpose =='Tuition Fee'?'selected':'' }} value="Tuition Fee">Tuition Fee</option>
                                                                            <option {{ $bankAccount->account_purpose =='PG Tuition Fee'?'selected':'' }} value="PG Tuition Fee">PG Tuition Fee</option>
                                                                            <option {{ $bankAccount->account_purpose =='Accomondation'?'selected':'' }} value="Accomondation">Accomondation</option>
                                                                            <option {{ $bankAccount->account_purpose =='Other Fee'?'selected':'' }} value="Other Fee">Other Fee</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="role" class="form-label">Select Bank</label>
                                                                        <select class="form-select" aria-label="role" name="bank_name">
                                                                            <option {{ $bankAccount->bank_name =='Access Bank'?'selected':'' }} value="Access Bank">Access Bank</option>
                                                                            <option {{ $bankAccount->bank_name =='Citibank Nigeria'?'selected':'' }} value="Citibank Nigeria">Citibank Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='Ecobank Nigeria'?'selected':'' }} value="Ecobank Nigeria">Ecobank Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='Fidelity Bank Nigeria'?'selected':'' }} value="Fidelity Bank Nigeria">Fidelity Bank Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='First Bank of Nigeria'?'selected':'' }} value="First Bank of Nigeria">First Bank of Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='First City Monument Bank (FCMB)'?'selected':'' }} value="First City Monument Bank (FCMB)">First City Monument Bank (FCMB)</option>
                                                                            <option {{ $bankAccount->bank_name =='Guaranty Trust Bank (GTBank)'?'selected':'' }} value="Guaranty Trust Bank (GTBank)">Guaranty Trust Bank (GTBank)</option>
                                                                            <option {{ $bankAccount->bank_name =='Heritage Bank Plc'?'selected':'' }} value="Heritage Bank Plc">Heritage Bank Plc</option>
                                                                            <option {{ $bankAccount->bank_name =='Keystone Bank Limited'?'selected':'' }} value="Keystone Bank Limited">Keystone Bank Limited</option>
                                                                            <option {{ $bankAccount->bank_name =='Polaris Bank'?'selected':'' }} value="Polaris Bank">Polaris Bank</option>
                                                                            <option {{ $bankAccount->bank_name =='Stanbic IBTC Bank Nigeria'?'selected':'' }} value="Stanbic IBTC Bank Nigeria">Stanbic IBTC Bank Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='Standard Chartered Bank Nigeria'?'selected':'' }} value="Standard Chartered Bank Nigeria">Standard Chartered Bank Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='Sterling Bank'?'selected':'' }} value="Sterling Bank">Sterling Bank</option>
                                                                            <option {{ $bankAccount->bank_name =='Union Bank of Nigeria'?'selected':'' }} value="Union Bank of Nigeria">Union Bank of Nigeria</option>
                                                                            <option {{ $bankAccount->bank_name =='United Bank for Africa (UBA)'?'selected':'' }} value="United Bank for Africa (UBA)">United Bank for Africa (UBA)</option>
                                                                            <option {{ $bankAccount->bank_name =='Unity Bank Plc'?'selected':'' }} value="Unity Bank Plc">Unity Bank Plc</option>
                                                                            <option {{ $bankAccount->bank_name =='Wema Bank'?'selected':'' }} value="Wema Bank">Wema Bank</option>
                                                                            <option {{ $bankAccount->bank_name =='Zenith Bank'?'selected':'' }} value="Zenith Bank">Zenith Bank</option>
                                                                        </select>
                                                                    </div>
                                                                    

                                                                    <div class="mb-3">
                                                                        <label for="account_name" class="form-label">Account Name</label>
                                                                        <input type="text" class="form-control" name="account_name" id="account_name" value="{{ $bankAccount->account_name }}">
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="account_number" class="form-label">Account Number</label>
                                                                        <input type="text" class="form-control" name="account_number" id="account_number" value="{{ $bankAccount->account_number }}">
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="account_code" class="form-label">Account Code</label>
                                                                        <input type="text" class="form-control" name="account_code" id="account_code" value="{{ $bankAccount->account_code }}">
                                                                    </div>

                                                                    <hr>
                                                                    <div class="text-end">
                                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            </div>
                                        </td>
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
</div>
<!-- end page title -->

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Bank Account</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addBankAccount') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Account Purpose</label>
                        <select class="form-select" aria-label="role" name="account_purpose" required>
                            <option selected value= "">Select Option </option>
                            <option value="ICT">ICT</option>
                            <option value="Tuition Fee">Tuition Fee</option>
                            <option value="PG Tuition Fee">PG Tuition Fee</option>
                            <option value="Accomondation">Accomondation</option>
                            <option value="Other Fee">Other Fee</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Select Bank</label>
                        <select class="form-select" aria-label="role" name="bank_name" required>
                            <option selected value= "">Select Option </option>
                            <option value="Access Bank">Access Bank</option>
                            <option value="Citibank Nigeria">Citibank Nigeria</option>
                            <option value="Ecobank Nigeria">Ecobank Nigeria</option>
                            <option value="Fidelity Bank Nigeria">Fidelity Bank Nigeria</option>
                            <option value="First Bank of Nigeria">First Bank of Nigeria</option>
                            <option value="First City Monument Bank (FCMB)">First City Monument Bank (FCMB)</option>
                            <option value="Guaranty Trust Bank (GTBank)">Guaranty Trust Bank (GTBank)</option>
                            <option value="Heritage Bank Plc">Heritage Bank Plc</option>
                            <option value="Keystone Bank Limited">Keystone Bank Limited</option>
                            <option value="Polaris Bank">Polaris Bank</option>
                            <option value="Stanbic IBTC Bank Nigeria">Stanbic IBTC Bank Nigeria</option>
                            <option value="Standard Chartered Bank Nigeria">Standard Chartered Bank Nigeria</option>
                            <option value="Sterling Bank">Sterling Bank</option>
                            <option value="Union Bank of Nigeria">Union Bank of Nigeria</option>
                            <option value="United Bank for Africa (UBA)">United Bank for Africa (UBA)</option>
                            <option value="Unity Bank Plc">Unity Bank Plc</option>
                            <option value="Wema Bank">Wema Bank</option>
                            <option value="Zenith Bank">Zenith Bank</option>
                        </select>
                    </div>
                    

                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name</label>
                        <input type="text" class="form-control" name="account_name" id="account_name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account_number" id="account_number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_code" class="form-label">Account Code</label>
                        <input type="text" class="form-control" name="account_code" id="account_code">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create a Bank Account</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection