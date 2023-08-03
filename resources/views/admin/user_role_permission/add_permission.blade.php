@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">User Role Permission</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($update) ? 'Edit' : 'Add'}} User Role Permission</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($update) ? 'Edit' : 'Add'}} User Role Permission</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($update) ? 'Edit' : 'Add'}} User Role Permission.
            </p>

            <form action="{{ route('admin.role_permissions.store') }}" class="ajaxForm" method="post" id="form" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row">
                    <div class="form-group mb-3 col-md-12">
                        <label for="name">Role Name<span class="text-danger">*</span></label>
                        <select class="form-control" name="role_name" id="role_name" @if(isset($update)) disabled @endif required>
                            <option value="">Select Role</option>
                            
                            
                            {{-- <option value="superadmin" @if(@@$edit_permission->role_name == 'superadmin') selected @endif>Super Admin</option>
                            <option value="admin" @if(@@$edit_permission->role_name == 'admin') selected @endif>Admin</option>
                            <option value="franchise" @if(@@$edit_permission->role_name == 'franchise') selected @endif>Franchise</option>
                            <option value="dealer" @if(@@$edit_permission->role_name == 'dealer') selected @endif>Dealer</option>
                            <option value="sub_dealer" @if(@@$edit_permission->role_name == 'sub_dealer') selected @endif>Sub Dealer</option>
                            <option value="limited" @if(@@$edit_permission->role_name == 'limited') selected @endif>Limited</option> --}}
                            @foreach($roles AS $role)
                                <option value="{{ $role->role_name }}" @if(@$edit_permission->role_name == $role->role_name) selected @endif>{{ $edit_permission->role_name }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>    

                <div class="row" id="rights">
                    <div class="col-sm-12">
                        <h5 for="Rights">Permissions</h5>
                    </div>    
                    <div class="col-sm-6">
                        <div class="form-group" id="rights">
                            <label for="Rights">Activity Logs</label>
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td>
                                            <input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-activity-log" @if(isset($update)){{  @(in_array('view-activity-log',@$edit_permission->permissions)) ? 'checked' : '' }} @endif />
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                    
                    
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Admin</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        {{-- <th>Documents</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-admin" @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-admin" 
                                            @if(isset($update)){{ @(in_array('add-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-admin" 
                                            @if(isset($update)){{ @(in_array('edit-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-admin" @if(isset($update)){{ @(in_array('view-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        {{-- <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="update-admin-documents" @if(isset($update)){{ @(in_array('update-admin-documents',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td> --}}
        
                                    </tr>
                                </tbody>
                            </table>
                      
                        </div>
                    </div>
                    {{-- <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Admin</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Documents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-admin" @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-admin" 
                                            @if(isset($update)){{ @(in_array('add-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-admin" 
                                            @if(isset($update)){{ @(in_array('edit-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-admin" @if(isset($update)){{ @(in_array('view-admin',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="update-admin-documents" @if(isset($update)){{ @(in_array('update-admin-documents',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-admin',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
        
                                    </tr>
                                </tbody>
                            </table>
                      
                        </div>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Staff</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-staff" @if(isset($update)){{ @(in_array('enabled-staff',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-staff" 
                                            @if(isset($update)){{ @(in_array('add-staff',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-staff',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-staff" 
                                            @if(isset($update)){{ @(in_array('edit-staff',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-staff',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="delete-staff" @if(isset($update)){{ @(in_array('delete-staff',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-staff',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                            </tr>
                                </tbody>
                            </table>
                      
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group" >
                            <label for="Rights">Finance</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>View Invoice</th>
                                        <th>View Payments</th>
                                        <th>Add Payments</th>
                                        <th>Delete Payments</th>
                                        <th>Print Payments</th>
                                        <th>View Approve Payments</th>
                                        <th>Approve Payments</th>
                                        <th>Transactions</th>
                                        <th>Taxation</th>
                                        <th>Ledger</th>
                                        <th>Taxes Summary</th>
                                        <th>Rejected Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-finance" @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-invoice" 
                                            @if(isset($update)){{ @(in_array('view-invoice',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-payments" 
                                            @if(isset($update)){{ @(in_array('view-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-payments" @if(isset($update)){{ @(in_array('add-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="delete-payments" @if(isset($update)){{ @(in_array('delete-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                                @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="print-payments" @if(isset($update)){{ @(in_array('print-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-approve-payments" @if(isset($update)){{ @(in_array('view-approve-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                                    @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="approve-payments" @if(isset($update)){{ @(in_array('approve-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                                        @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="transaction" @if(isset($update)){{ @(in_array('transaction',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="taxation" @if(isset($update)){{ @(in_array('taxation',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                                                @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="ledger" @if(isset($update)){{ @(in_array('ledger',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                            
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="taxes-summary" @if(isset($update)){{ @(in_array('taxes-summary',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                            
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="rejected-payments" @if(isset($update)){{ @(in_array('rejected-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        </tr>
                                </tbody>
                            </table>
                      
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Credit Note</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled-Finance</th>
                                        <th>View</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-finance" @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-credit-note" @if(isset($update)){{ @(in_array('view-credit-note',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-credit-note" @if(isset($update)){{ @(in_array('add-credit-note',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                        @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>    
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-credit-note" @if(isset($update)){{ @(in_array('edit-credit-note',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>    

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="delete-credit-note" @if(isset($update)){{ @(in_array('delete-credit-note',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Deposit Slip</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled-Depost-slip</th>
                                        <th>View</th>
                                        <th>Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-deposit-slip" @if(isset($update)){{ @(in_array('enabled-deposit-slip',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-deposit-slip" @if(isset($update)){{ @(in_array('view-deposit-slip',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-deposit-slip',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-deposit-slip" @if(isset($update)){{ @(in_array('add-deposit-slip',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                        @if(isset($update)){{ @(in_array('enabled-deposit-slip',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>    
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row" id="rights">
                   
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Settings-General</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-settings" @if(isset($update)){{ @(in_array('enabled-settings',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-general-settings" @if(isset($update)){{ @(in_array('view-general-settings',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-settings',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Settings-Locations</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        {{-- <th>All</th> --}}
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-settings-locations" @if(isset($update)){{ @(in_array('enabled-settings-locations',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>
                                        
                                        {{-- <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="all-settings-locations" @if(isset($update)){{ @(in_array('all-settings-locations',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-settings-locations',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> --}}

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-settings-locations" @if(isset($update)){{ @(in_array('add-settings-locations',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-settings-locations',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-settings-locations" @if(isset($update)){{ @(in_array('edit-settings-locations',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-settings-locations',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="delete-settings-locations" @if(isset($update)){{ @(in_array('delete-settings-locations',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-settings-locations',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- <div class="row" id="rights">
                   
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Franchises</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Packages</th>
                                        <th>Documents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-franchise" @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-franchise" @if(isset($update)){{ @(in_array('add-franchise',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-franchise" @if(isset($update)){{ @(in_array('edit-franchise',@$edit_permission->permissions)) ? 'checked' : '' }}
                                            @endif @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-franchise" 
                                            @if(isset($update)){{ @(in_array('view-franchise',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="franchise-packages" 
                                            @if(isset($update)){{ @(in_array('franchise-packages',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="update-franchise-documents" 
                                            @if(isset($update)){{ @(in_array('update-franchise-documents',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-franchise',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Dealers</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Packages</th>
                                        <th>Documents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-dealer" @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-dealer" @if(isset($update)){{ @(in_array('add-dealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-dealer" @if(isset($update)){{ @(in_array('edit-dealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-dealer" @if(isset($update)){{ @(in_array('view-dealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="dealer-packages" @if(isset($update)){{ @(in_array('dealer-packages',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="update-dealer-documents" 
                                            @if(isset($update)){{ @(in_array('update-dealer-documents',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-dealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> --}}

                <div class="row" id="rights">
                   
                    {{-- <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Sub Dealers</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Packages</th>
                                        <th>Documents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-subdealer" @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-subdealer" @if(isset($update)){{ @(in_array('add-subdealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-subdealer" @if(isset($update)){{ @(in_array('edit-subdealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-subdealer" @if(isset($update)){{ @(in_array('view-subdealer',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="subdealer-packages" @if(isset($update)){{ @(in_array('subdealer-packages',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>    
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="update-subdealer-documents" 
                                            @if(isset($update)){{ @(in_array('update-subdealer-documents',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-subdealer',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                    {{-- <div class="col-sm-6">
                        <div class="form-group" id="rights">
                            <label for="Rights">Finance</label>
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>View Invocies</th>
                                        <th>View Payments</th>
                                        <th>Add Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td>
                                            <input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-finance" @if(isset($update)){{  @(in_array('enabled-finance',@$edit_permission->permissions)) ? 'checked' : '' }} @endif />
                                        </td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-invoices" @if(isset($update)){{ @(in_array('enabled-invoices',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  

                                

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-payments" @if(isset($update)){{ @(in_array('enabled-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-payments" @if(isset($update)){{ @(in_array('add-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-payments',@$edit_permission->permissions) && in_array('enabled-finance',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                        
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div> --}}
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">SMS</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>All SMS</th>
                                        <th>Manual SMS</th>
                                        <th>SMS By User</th>
                                        <th>SMS Logs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-sms" @if(isset($update)){{ @(in_array('enabled-sms',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="all-sms" 
                                            @if(isset($update)){{ @(in_array('all-sms',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-sms',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="manual-sms" 
                                            @if(isset($update)){{ @(in_array('manual-sms',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-sms',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="sms-by-user" @if(isset($update)){{ @(in_array('sms-by-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-sms',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td> 
                                            
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="sms-logs" @if(isset($update)){{ @(in_array('sms-logs',@$edit_permission->permissions)) ? 'checked' : '' }}@endif 
                                            @if(isset($update)){{ @(in_array('enabled-sms',@$edit_permission->permissions)) ? '' : 'disabled' }}@endif/></td> 
                                    </tr>
                                </tbody>
                            </table>
                      
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group" >
                            <label for="Rights">Users</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>All</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Activate</th>
                                        <th>Renew</th>
                                        <th>Online</th>
                                        <th>Offline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-user" @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif /></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="all-user" @if(isset($update)){{ @(in_array('all-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-user" @if(isset($update)){{ @(in_array('add-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-user" @if(isset($update)){{ @(in_array('edit-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-user" @if(isset($update)){{ @(in_array('view-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="active-user" @if(isset($update)){{ @(in_array('active-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
    
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="renew-user" @if(isset($update)){{ @(in_array('renew-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                                
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="online-users" @if(isset($update)){{ @(in_array('online-users',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="offline-users" @if(isset($update)){{ @(in_array('offline-users',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                    @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                            
    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group" >
                            <label for="Rights">Users</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Login Fails</th>
                                        <th>Mac vendor</th>
                                        <th>Login Detail</th>
                                        <th>Search</th>
                                        <th>Credit Limit</th>
                                        <th>Queue User</th>
                                        <th>Quota User</th>
                                        <th>Quota Low</th>
                                        <th>Edit Expiraiton</th>
                                        <th>Qouta Reset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="login-fail-users" @if(isset($update)){{ @(in_array('login-fail-users',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="mac-vendor-users" @if(isset($update)){{ @(in_array('mac-vendor-users',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="user-login-detail" @if(isset($update)){{ @(in_array('user-login-detail',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="search-user" @if(isset($update)){{ @(in_array('search-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                            
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="user-credit-limit" @if(isset($update)){{ @(in_array('user-credit-limit',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                                
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="queue-user" @if(isset($update)){{ @(in_array('queue-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> 
                                                
                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="quota-user" @if(isset($update)){{ @(in_array('quota-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> 

                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="quota-low" @if(isset($update)){{ @(in_array('quota-low',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> 

                                            <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="edit-expiration" @if(isset($update)){{ @(in_array('edit-expiration',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> 
                                            
                                                <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="qouta-reset" @if(isset($update)){{ @(in_array('qouta-reset',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                                @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td> 
    

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group" >
                            <label for="Rights">User Profile Permisssion</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enable</th>
                                        <th>Disable</th>
                                        <th>Change Password</th>
                                        <th>Kick User</th>
                                        <th>Remove Mac</th>
                                        <th>Invoice</th>
                                        <th>Documnets Browse</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enable-user" @if(isset($update)){{ @(in_array('enable-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                            

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="disable-user" @if(isset($update)){{ @(in_array('disable-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="change-user-password" @if(isset($update)){{ @(in_array('change-user-password',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                            
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="kick-user" @if(isset($update)){{ @(in_array('kick-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  

                                            
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="remove-mac" @if(isset($update)){{ @(in_array('remove-mac',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  

                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="user-invoice" @if(isset($update)){{ @(in_array('user-invoice',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                        
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="document-browse" @if(isset($update)){{ @(in_array('document-browse',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>  
                                          
    
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">User Package</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Upgrade</th>
                                        <th>Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="upgrade-user-package" @if(isset($update)){{ @(in_array('upgrade-user-package',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="change-user-package" @if(isset($update)){{ @(in_array('change-user-package',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>     
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">Billing Cycle Group</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Monthly</th>
                                        <th>Half Month</th>
                                        <th>Full Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="monthly" @if(isset($update)){{ @(in_array('monthly',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                        
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="half-month" @if(isset($update)){{ @(in_array('half-month',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>     

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="full-month" @if(isset($update)){{ @(in_array('full-month',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>        
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group" id="rights">
                            <label for="Rights">Invoice</label>
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td>
                                            <input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-invoices" @if(isset($update)){{  @(in_array('enabled-invoices',@$edit_permission->permissions)) ? 'checked' : '' }} @endif />
                                        </td>
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="view-invoices" @if(isset($update)){{ @(in_array('view-invoices',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-invoice',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" id="rights">
                            <label for="Rights">Payments</label>
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Enabled</th>
                                        <th>Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td>
                                            <input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="enabled-payments" @if(isset($update)){{  @(in_array('enabled-payments',@$edit_permission->permissions)) ? 'checked' : '' }} @endif />
                                        </td>
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="add-payments" @if(isset($update)){{ @(in_array('add-payments',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-payments',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>       
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="row" id="rights">
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="Rights">User Package</label>  
                            <table class=" table-bordered w-100 nowrap responsive">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th>Activate</th>
                                        <th>Renew</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="active-user" @if(isset($update)){{ @(in_array('active-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>

                                        <td><input type="checkbox" class="form-control" name="permissions[]"  data-toggle="switchery" data-size="small" data-color="#1bb99a" value="renew-user" @if(isset($update)){{ @(in_array('renew-user',@$edit_permission->permissions)) ? 'checked' : '' }}@endif  
                                            @if(isset($update)){{ @(in_array('enabled-user',@$edit_permission->permissions)) ? '' : 'readonly' }}@endif/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> --}}


                <input type="hidden" name="permission_id" value="{{ @$edit_permission->hashid }}">
                @if(isset($update))
                    <input type="hidden" name="role_name" value="{{ @$edit_permission->role_name }}">
                @endif
                <div class="form-group mb-3 text-right">
                    {{-- <input type="hidden" value="{{ @$edit_isp->hashid }}" name="isp_id"> --}}
                    <button class="btn btn-primary waves-effect waves-light" type="submit" onclick="removeDisabled()">
                        {{ (isset($update)) ? 'Update' : 'Add' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
    function removeDisabled(){
        $('#form input').removeAttr('disabled');
    }
</script>
@endsection
@section('page-scripts')
<script>        
    @include('admin.partials.datatable', ['load_swtichery' => true])
</script>
@endsection