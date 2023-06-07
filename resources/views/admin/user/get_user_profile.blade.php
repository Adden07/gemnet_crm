<div class="tab-pane fade show" id="service_details" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-center bg-primary">
                            <h3 class="m-0 p-2 text-white">Package Details</h3>
                        </div>
                        <table class="table  table-hover">
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($user_details->user_status == 1)
                                        @if($user_details->status == 'registered')
                                            <span class="badge badge-info">Registered</span>
                                        @elseif($user_details->status == 'active' && $user_details->last_logout_time != null && date('Y',strtotime($user_details->last_logout_time)) == 1970)
                                            <span class="badge badge-success">Active</span>-<span class="badge badge-warning">Never Online</span>  
                                        @elseif($user_details->status == 'expired' && $user_details->last_logout_time != null && date('Y',strtotime($user_details->last_logut_time)) == 1970)
                                            <span class="badge badge-danger">Expired</span>-<span class="badge badge-warning">Never Online</span>
                                        @elseif($user_details->status == 'active' && $user_details->last_logout_time == null)
                                            <span class="badge badge-success">Active</span>-<span class="badge badge-success">Online</span>
                                        @elseif($user_details->status == 'active' && $user_details->last_logout_time != null)
                                            <span class="badge badge-success">Active</span>-<span class="badge badge-danger">Offline</span>
                                        @elseif($user_details->status == 'expired' && $user_details->last_logout_time == null)
                                            <span class="badge badge-danger">Expired</span>-<span class="badge badge-success">Online</span>
                                        @elseif($user_details->status == 'expired' && $user_details->last_logout_time != null)
                                            <span class="badge badge-danger">Expired</span>-<span class="badge badge-danger">Offline</span>
                                        @else
                                            <span class="badge badge-danger">Expired</span>
                                        @endif
                                    @elseif($user_details->user_status == 2)
                                        <span class="badge badge-danger">Disabled By Admin</span>
                                    @else
                                        <span class="badge badge-danger">Disabled</span>
                                    @endif    

                                </td>
                            </tr>
                            <tr>
                                <th>Total Volume</th>
                                <td>{{ number_format($user_details->qt_total/pow(1024,3),2) }} GB</td>
                            </tr>
                            <tr>
                                <th>Used Volume</th>
                                <td>{{ number_format($user_details->qt_used/pow(1024,3),2) }} GB</td>
                            </tr>
                            <tr>
                                <th>Remaining Volume</th>
                                <td>
                                    @if($user_details->status != 'registered')
                                        @php 
                                            @$remaining_volume = $user_details->qt_total - $user_details->qt_used;
                                            @$remaining_percent = ($remaining_volume * 100)/$user_details->qt_total;
                                        @endphp
                                        @if($remaining_percent >= 60)
                                            <span class="badge badge-info">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                        @elseif(@$remaining_percent >= 26 )
                                            <span class="badge badge-success">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                        @else
                                            <span class="badge badge-danger">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                        @endif
                                    @else   
                                        <span class="badge badge-danger">0 GB</span>
                                    @endif     
                            </tr>
                            <tr>
                                <th>Primary Package</th>
                                <td id="primary_package">{{ @$user_details->primary_package->name }}</td>
                                <td>
                                    <select class="form-control d-none" name="primary_package_ddl" id="primary_package_ddl">
                                        <option value="">Select Package</option>
                                    </select>
                                </td>
                            </tr>
                            @if(!empty($user_details->last_package) && !empty($user_details->last_expiration_date))
                                <tr>
                                    <th>Last Packge</th>
                                    <td>{{ @$user_details->lastPackage->name }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Current Package</th>
                                <td id="current_package">{{ @$user_details->current_package->name }}</td>
                                <td>
                                    <select class="form-control d-none" name="current_package_ddl" id="current_package_ddl">
                                </td>
                            </tr>
                            @if(date('Y',strtotime($user_details->last_expiration_date)) != 1970)
                            <tr>
                                <th>Last Expiration</th>
                                <td>
                                    <span class="badge badge-primary">{{ date('d-M-Y H:i:s',strtotime($user_details->last_expiration_date)) }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Current Expiration</th>
                                <td>
                                    @if($user_details->status == 'active')
                                        <span class="badge badge-success">{{ date('d-M-Y H:i:s',strtotime($user_details->current_expiration_date)) }}</span>
                                    @elseif($user_details->status == 'expired')
                                        <span class="badge badge-danger">{{ date('d-M-Y H:i:s',strtotime($user_details->current_expiration_date)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            {{-- @if($user_details->status != 'registered')
                                <tr>
                                    <th>Required Amount</th>
                                    <td>
                                        {{ $user_details->packages->price }}   
                                    </td>
                                </tr>
                            @endif --}}
                        </table>
                    </div>

                    <div class="col-md-6">
                        <div class="text-center bg-primary">
                            <h3 class="m-0 p-2 text-white">Personal Details</h3>
                        </div>
                        <table class="table  table-hover">
                            <tr>
                                <th>Name</th>
                                <td>{{ $user_details->name }}</td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>{{ $user_details->nic }}</td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td>{{ @$user_details->mobile }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ @$user_details->address }}</td>
                            </tr>
                            <tr>
                                <th>Area</th>
                                <td>{{ @$user_details->area->area_name }}</td>
                            </tr>
                            <tr>
                                <th>Subarea</th>
                                <td>{{ @$user_details->subarea->area_name }}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>{{ @$user_details->city->city_name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>