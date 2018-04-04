@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Employee
            <small>manage employee</small>
        </h1>
    </div>
    <!-- END PAGE TITLE -->
</div>
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="{{ url('usernon') }}">Employee Management</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
		<span>
			@if (empty($data))
				Add New Employee
			@else
				Update Employee
			@endif
		</span>
	</li>
</ul>
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">
	    <!-- BEGIN EXAMPLE TABLE PORTLET-->
	    <div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-group font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">
						@if (empty($data))
							ADD NEW EMPLOYEE
						@else
							UPDATE EMPLOYEE
						@endif
					</span>
				</div>

				<div class="btn-group" style="float: right; padding-top: 2px; padding-right: 10px;">
                	<a class="btn btn-md green" href="{{ url('usernon') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<form id="form_user" class="form-horizontal" action="{{ url('usernon', @$data->id) }}" method="POST">	        	
			        {{ csrf_field() }}
			        @if (!empty($data))
			          {{ method_field('PATCH') }}
			        @endif
			        <div class="form-body">
                    	<div class="alert alert-danger display-hide">
                        	<button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                        	<button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        
                        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">DETAILS</span>
                        	<input type="hidden" name="penampungUserId" id="penampungUserId">
                        	<hr>
                        </div>

                        <div class="form-group">
				          <label class="col-sm-2 control-label">Role</label>
				          <div class="col-sm-9">

				          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role_id" id="role" required>
                                	<option></option>                                	
                                </select>
                               	<input type="hidden" id="selectedRole" name="selectedRole">
                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>

              				</div>
				            
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">NIK</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="nik" name="nik" class="form-control" value="{{ @$data->nik }}" placeholder="Input NIK" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Name</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="name" name="name" class="form-control" value="{{ @$data->name }}" placeholder="Input Name" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Join Date</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="join_date" name="join_date" class="form-control" value="{{ @$data->join_date }}" placeholder="Join Date" />
				            </div>
				          </div>
				        </div>

						<div id="statusSpv" class="display-hide">
					        <div class="form-group">
	                            <label class="control-label col-md-2">Status
	                            </label>
	                            <div class="col-md-9">
	                                <div class="mt-radio-list" data-error-container="#form_employee_status_error">
	                                    <label class="mt-radio">
	                                        <input id="statusSpvCheck" type="radio" name="status_spv" value="Promoter" {{ (@$data->status == 'Promoter') ? "checked" : "" }}> Promoter
	                                        <span></span>
	                                    </label>
	                                    <label class="mt-radio">
	                                        <input id="statusSpvCheck2" type="radio" name="status_spv" value="Demonstrator" {{ (@$data->status == 'Demonstrator') ? "checked" : "" }}> Demonstrator
	                                        <span></span>
	                                    </label>
	                                </div>
	                                <div id="form_employee_status_error"> </div>
	                            </div>
	                        </div>
	                    </div>

	                    <div id="dedicateContent" class="display-hide form-group">
				          <label class="col-sm-2 control-label">Dedicate</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<select class="select2select" name="dedicate" id="dedicate">
				            		<option></option>
									<option value="DA" 
										{{ (@$spvDedicate->dedicate == 'DA') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'DA') ? "selected" : "" }}
										>
										DA
									</option>
									<option value="PC" 
										{{ (@$spvDedicate->dedicate == 'PC') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'PC') ? "selected" : "" }}
										>
										PC
									</option>
									<option value="MCC" 
										{{ (@$spvDedicate->dedicate == 'MCC') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'MCC') ? "selected" : "" }}
										>
										MCC
									</option>
									<option value="HYBRID" 
										{{ (@$spvDedicate->dedicate == 'HYBRID') ? "selected" : "" }}
										{{ (@$spvDemoDedicate->store->dedicate == 'HYBRID') ? "selected" : "" }}
										>
										HYBRID
									</option>
                                </select>

                                <span class="input-group-addon display-hide">
                                	<i class="fa"></i>
                                </span>
				            </div>
				          </div>
				        </div>

				        <div id="statusContent" class="display-hide">
					        <div class="form-group">
	                            <label class="control-label col-md-2">Status                               
	                            </label>
	                            <div class="col-md-9">
	                                <div class="mt-radio-list" data-error-container="#form_employee_status_error">
	                                    <label class="mt-radio">
	                                        <input id="statusCheck" type="radio" name="status" value="stay" {{ (@$data->status == 'stay') ? "checked" : "" }}> Stay
	                                        <span></span>
	                                    </label>
	                                    <label class="mt-radio">
	                                        <input type="radio" name="status" value="mobile" {{ (@$data->status == 'mobile') ? "checked" : "" }}> Mobile
	                                        <span></span>
	                                    </label>
	                                </div>
	                                <div id="form_employee_status_error"> </div>
	                            </div>
	                        </div>
	                    </div>

	                    <!-- BEGIN STORE DETAILS -->
	                    <div id="storeContent" class="display-hide">
		                    <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">STORE</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group newstore">
                        		<div style="padding-left: 4%;">
	                        		<a class="btn btn-md green" href="{{ url('store/create') }}" target="_blank">
				                		<i class="fa fa-plus"></i> New Store
				                	</a>
				                	<p class="btn btn-md red" id="clearStores">
				                		<i class="fa fa-refresh"></i>
	                                	Clear Selected
	                                </p>
			                	</div>
                        	</div>

	                        <div id="oneStoreContent" class="display-hide">
		                        <div class="form-group">
		                          <label class="col-sm-2 control-label">Employee's Store</label>
		                          <div class="col-sm-9">

		                          <div class="input-group" style="width: 100%;">
		     
		                                <select class="select2select" name="store_id" id="store" required="required"></select>
		                                
		                                <span class="input-group-addon display-hide">
		                                    <i class="fa"></i>
		                                </span>

		                            </div>
		                            
		                          </div>
		                        </div>
	                        </div>

	                        <div id="multipleStoreContent" class="display-hide">
		                        <div class="form-group">
		                          <label class="col-sm-2 control-label">Employee's Store</label>
		                          <div class="col-sm-9">

		                          	<div class="input-group col-sm-10" style="float: left;">
		     
		                                <select class="select2select" id="stores"></select>
		                                
		                                <span class="input-group-addon display-hide">
		                                    <i class="fa"></i>
		                                </span>

		                            </div>

		                            <div class="input-group col-sm-2 newstore" style="float: right;padding-left: 10px;">
		                            	<div class="display-hide" id="divDedicate2">
		                            		<select class="select2select" name="dedicate" id="dedicate2">
							            		<option></option>
												<!-- <option value="DA">
													DA
												</option>
												<option value="PC">
													PC
												</option> -->
												<option value="MCC">
													MCC
												</option>
												<option value="HYBRID">
													HYBRID
												</option>
			                                </select>
		                            	</div>
			                        	
		                                <!-- <p class="btn btn-md red" id="clearStores" style="float: right;margin-bottom: 1px;margin-top: 1px;width: 49%;margin-left: 1%;">
		                                	Clear
		                                </p> -->
		                                <p class="btn btn-md green" id="addStores" style="float: right;margin-bottom: 1px;margin-top: 2px;width: 49%;">
		                                	Add
		                                </p>
			                        </div>

		                            <hr>
			                        <div class="portlet light bg-inverse newstore col-sm-12" style="padding-top: 0px;">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span class="caption-subject font-green-haze bold uppercase">Stores</span>
                                                <span class="caption-helper">Selected (<span id="storeCount">0</span>)</span>
                                                <input type="hidden" name="check" id="check" value="0">
                                            </div>
                                            <div class="tools">
                                                <a id="toggleButton" href="" class="expand" data-original-title="show/hide" title="show/hide"> </a>
                                            </div>
                                        </div>
                                        <div id="toggleContent" class="portlet-body form" style="display: none;">
                                            
                                            <div class="row">
					                            <input type="text" id="myInput" onkeyup="searchFunction()" placeholder="Search for names.." title="Type in a name">
					                            <ul id="myUL">
					                            </ul>
					                        </div>
                                            
                                        </div>
                                    </div>
		                            
		                          </div>
		                        </div>
	                        </div>
	                    </div>
	                    <!-- END STORE DETAILS -->


				        <!-- BEGIN DM DETAILS -->				       
				        <div id="dmContent" class="display-hide">
					        <div class="caption padding-caption">
	                        	<span id="areaTitle" class="caption-subject font-dark bold uppercase">DM AREA</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Area</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="area[]" id="area" multiple="multiple"></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>
	                    </div>

                        <!-- END DM DETAILS -->

                        <!-- BEGIN RSM DETAILS -->				       
				        <div id="rsmContent" class="display-hide">
					        <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">RSM REGION</span>
	                        	<hr>
	                        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Region</label>
	                          <div class="col-sm-9">

	                          <div class="input-group" style="width: 100%;">
	     
	                                <select class="select2select" name="region[]" id="region" multiple="multiple"></select>
	                                
	                                <span class="input-group-addon display-hide">
	                                    <i class="fa"></i>
	                                </span>

	                            </div>
	                            
	                          </div>
	                        </div>
	                    </div>
                        <!-- END RSM DETAILS -->

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">PHOTO</span>
                        	<hr>
                        </div>		        

				        <!-- View for old image * PHOTO * -->
				        @if (!empty($data))				        	
				        	<div class="form-group">
				          		<label class="col-sm-2 control-label">Photo</label>
				         		<div class="col-sm-9">				          	
					    			<img width="90px" height="90px" src="{{ @$data->photo }}" onError="this.onerror=null;this.src='{{ asset('image/missing.png') }}';">
				         		</div>
				        	</div>

			          		<div class="form-group">
				          		<label class="col-sm-2 control-label">Photo URL</label>
				         		<div class="col-sm-9">				          	
					    			<input type="text" class="form-control" value="{{ @$data->photo }}" disabled="disabled" />
					    		</div>
				        	</div>
			        	@endif

				        <div class="form-group">
				          <label class="col-sm-2 control-label">{{ (!empty($data)) ? 'New ' : ' ' }}Photo</label>
				          <div class="col-sm-9">
				          	<div class="input-group" style="width: 100%;">
					          	<input type="file" accept=".jpg,.jpeg,.png,.gif,.svg" class="form-control" id="photo_file" name="photo_file">
					          	<p style="font-size: 10pt;" class="help-block"> (Type of file: jpg, jpeg, png, gif, svg - Max size : 2 MB) </p>
					          	<div class="file_error_message" style="display: none;"></div>
					      	</div>
				          </div>
				        </div>		 

				        <div class="caption padding-caption">
                        	<span class="caption-subject font-dark bold uppercase">Password</span>
                        	<hr>
                        </div>

                        <div class="form-group">
				          <label class="col-sm-2 control-label">Email</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="text" id="email" name="email" class="form-control" value="{{ @$data->email }}" placeholder="Input Email" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Password</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="password" id="password" name="password" class="form-control" placeholder="Input Password" />
				            </div>
				          </div>
				        </div>

				        <div class="form-group">
				          <label class="col-sm-2 control-label">Confirm Password</label>
				          <div class="col-sm-9">
				          	<div class="input-icon right">
				          		<i class="fa"></i>
				            	<input type="password" id="password-confirm" name="password_confirmation" class="form-control" placeholder="Confirm Password" />
				            </div>
				          </div>
				        </div>  
		        
				        <div class="form-group" style="padding-top: 15pt;">
				          <div class="col-sm-9 col-sm-offset-2">
				            <button type="submit" class="btn btn-primary green">Save</button>
				          </div>
				        </div>

			    	</div>
			    </form>
				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
@endsection

@section('additional-scripts')		
	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->
	<!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->	
	<!-- BEGIN PAGE VALIDATION SCRIPTS -->
    <script src="{{ asset('js/handler/user-handler.js') }}" type="text/javascript"></script>
    <!-- END PAGE VALIDATION SCRIPTS -->

    <script>
    	var userId = "{{ collect(request()->segments())->last() }}";

		$(document).ready(function () {

			$.ajaxSetup({
	        	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

			$('#penampungUserId').val(userId);

	    	$('#area').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#region').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {            
	            return filterData('name', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    return {id: obj.id, text: obj.name}
	                })
	            }
	        }));

	        $('#store').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    if(obj.store_name_2 != null){
                            return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
	                    return {id: obj.id, text: obj.store_id + " - " + obj.store_name_1}
	                })
	            }
	        }));

	         $('#stores').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
	         	var selectedRolev = $('#selectedRole').val();
				selectedRolev = selectedRolev.split('`');
				filters['bySpvNew'] = $('#penampungUserId').val();
	         	if (selectedRolev[1] == 'Supervisor') {
		        	var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
		        	if (statusSpv == "Demonstrator") {
		        		filters['byDedicateSpv'] = "DA";
		        	}
		        	// else{
		        	// 	filters['byDedicateSpv'] = $('#dedicate').val();
		        	// }
		        }
		        // else if (selectedRolev[1] == 'Supervisor Hybrid') {
		        // 	filters['byDedicateSpvHybrid'] = $('#dedicate').val();
		        // }
	            return filterData('store', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {                                
	                    if(obj.store_name_2 != null){
                            return {id: obj.id+'`'+obj.store_id + "`" + obj.store_name_1 + " (" + obj.store_name_2 + ")", text: obj.store_id + " - " + obj.store_name_1 + " (" + obj.store_name_2 + ")"}
                        }
	                    return {id: obj.id+'`'+obj.store_id + "`" + obj.store_name_1, text: obj.store_id + " - " + obj.store_name_1}
	                })
	            }
	        }));

	       	$('#role').select2(setOptions('{{ route("data.role") }}', 'Role', function (params) {
	       		filters['nonPromoterGroup'] = '1';
	       		@if (Auth::user()->role->role_group != 'Master')
		          filters['nonMaster'] = '1';
		        @endif
	            return filterData('role', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {            
	                    return {id: obj.id+"`"+obj.role_group, text: obj.role}
	                })
	            }
	        }));
			setSelect2IfPatch($("#role"), "{{ @$data->role->id }}`{{ @$data->role->role_group }}", "{{ @$data->role->role }}");
			$('#selectedRole').val("{{ @$data->role->id }}`{{ @$data->role->role_group }}");

            $('#dedicate').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            $('#dedicate2').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            setForm($('#selectedRole').val());

		});

		// Reset form
		function resetForm(){

			$('#area').removeAttr('required');			
			select2Reset($('#area'));
			$('#region').removeAttr('required');
			select2Reset($('#region'));

			$('#dmContent').children('.form-group').removeClass('has-error');
			$('#rsmContent').children('.form-group').removeClass('has-error');
			// $('#dedicateContent').children('.form-group').removeClass('has-error');

			$('#dmContent').addClass('display-hide');
			$('#rsmContent').addClass('display-hide');

			// NIK
			$('#nik').removeAttr('required');
			$('#nik').closest('.form-group').removeClass('has-error');
			var icon = $('#nik').parent('.input-icon').children('i');
            icon.removeClass("fa-warning").removeClass("fa-check");

            // STATUS
            $('#statusContent').addClass('display-hide');
            $('#statusCheck').removeAttr('required');
            $('#statusSpv').addClass('display-hide');
            $('#statusSpvCheck').removeAttr('required');

            //SPV
            $('#dedicate').removeAttr('required');
	       	$('#statusSpvCheck').removeAttr('required');
	       	$( "#statusSpvCheck" ).prop( "checked",{{ (@$spvDedicate->user_id != '') ? "true" : "false" }} );
	       	$( "#statusSpvCheck2" ).prop( "checked",{{ (@$spvDemo->user_id != '') ? "true" : "false" }} );

		}

		// Set and init dm and rsm
		function setForm(role){

			resetForm();
			resetStore();
			// clearStore();

			$('#dedicate2').removeAttr('name');
			$('#divDedicate2').addClass('display-hide');

			role = role.split('`');

			if(role[1] == 'DM'){
				$('#area').attr('required', 'required');
				updateArea();
				// setSelect2IfPatch($("#area"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				// setSelect2IfPatch($("#dedicate"), "{{ @$data->dmArea->dedicate }}", "{{ @$data->dmArea->dedicate }}");
				document.getElementById('areaTitle').innerHTML = "DM AREA";
				$('#dmContent').removeClass('display-hide');

				// $('#dedicate').attr('required', 'required');
				// setSelect2IfPatch($("#dedicate"), "{{ @$data->dmArea->area_id }}", "{{ @$data->dmArea->area->name }}");
				// $('#dedicateContent').removeClass('display-hide');
			}else{
				$('#dedicateContent').addClass('display-hide');
			}

			if(role[1] == 'Trainer'){
				$('#area').attr('required', 'required');
				// setSelect2IfPatch($("#area"), "{{ @$data->trainerArea->area_id }}", "{{ @$data->trainerArea->area->name }}");
				updateAreaTrainer();
				document.getElementById('areaTitle').innerHTML = "TRAINER AREA";
				$('#dmContent').removeClass('display-hide');
			}

			if(role[1] == 'RSM'){
				$('#region').attr('required', 'required');
				// setSelect2IfPatch($("#region"), "{{ @$data->rsmRegion->region_id }}", "{{ @$data->rsmRegion->region->name }}");
				updateRegion();
				$('#rsmContent').removeClass('display-hide');
			}

			if (role[1] == 'Supervisor' || role[1] == 'Supervisor Hybrid') {

				$('#storeContent').removeClass('display-hide');				
				$('#multipleStoreContent').removeClass('display-hide');			
	            // $('#stores').attr('required', 'required');
	            $('#dedicateContent').removeClass('display-hide');

	            if (role[1] == 'Supervisor') {
			       	$('#statusSpv').removeClass('display-hide');
			       	$('#dedicate').attr('required', 'required');
			       	$('#statusSpvCheck').attr('required', 'required');
			       	var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
			       	if (statusSpv) {
				       	if (statusSpv == "Demonstrator") 
				       	{
				       		$('#dedicateContent').addClass('display-hide');
				            $('#dedicate').removeAttr('required');
				       	}
				    }
                }else if (role[1] == 'Supervisor Hybrid') {
                	$('#dedicate').removeAttr('required');
                	$('#dedicateContent').addClass('display-hide');
                	$('#divDedicate2').removeClass('display-hide');
                	$('#dedicate2').attr('name', 'dedicate');
                }
                
			}

			if(!checkAdmin()){
				$('#nik').attr('required', 'required');
			}

			if(!checkPromoter()){
				$('input[type=radio][name=status]').prop('checked', false);
			}

			if(checkPromoter()){
				$('#statusCheck').attr('required', 'required');

				if(role[1] == 'Salesman Explorer'){
					$('input[type=radio][name=status][value=mobile]').attr('checked', 'checked');
//				    $('#statusContent').removeClass('display-hide');
				}else{
					$('#statusContent').removeClass('display-hide');
				}


				//Set Store
			    var status = $('input[type=radio][name=status]:checked').val();

			    if(!(typeof(status) === 'undefined')){
			    	setStore(status);
		    	}

			}
		}

		/* STORE METHOD */

		// Reset store
		function resetStore(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');
			// console.log("role"+selectedRolev[1]);

			$('#store').removeAttr('required');
			$('#stores').removeAttr('required');

			$('#storeContent').children('.form-group').removeClass('has-error');
			$('#storeContent').each(function(){
                $(this).children('.form-group').removeClass('has-error');
            });

   			$('#oneStoreContent').children('.form-group').removeClass('has-error');
   			$('#multipleStoreContent').children('.form-group').removeClass('has-error');
			$('#storeContent').addClass('display-hide');
			$('#oneStoreContent').addClass('display-hide');
			$('#multipleStoreContent').addClass('display-hide');

			// Reset selection
			if($('input[name=_method]').val() != "PATCH" || !checkPromoter()){
				select2Reset($('#store'));
				select2Reset($('#stores'));
			}			

			
			if($('input[name=_method]').val() == "PATCH" && (selectedRolev[1] == 'Supervisor' || selectedRolev[1] == 'Supervisor Hybrid')){
				select2Reset($('#stores'));
				// updateStoreSpv();
				// updateStoreSpvDemo();
			}			
		}

		function updateStoreSpv(){
			var getDataUrl = "{{ url('util/spvstore/') }}";

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#stores");
                    $.each(data, function() {
                    
						console.log('patch2#'+this.id);

						if(this.store_name_2 != null){
                        	setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
                    	}else{
						setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1);
						}

					});

            	}	

        	})
		}

		function updateStoreSpvDemo(){
			var getDataUrl = "{{ url('util/spvdemostore/') }}";

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#stores");
                    $.each(data, function() {

						if(this.store_name_2 != null){
                        	setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
                    	}else{
							setSelect2IfPatch(element, this.id+'`'+this.store_id, this.store_id + " - " + this.store_name_1);
						}
					});

            	}	

        	})
		}
		

		// Set and init store select2
		function setStore(value){			

			$('#storeContent').removeClass('display-hide');				

			if(value == 'stay'){			
				$('#oneStoreContent').removeClass('display-hide');
	            $('#store').attr('required', 'required');
			}else if(value == 'mobile'){	
				$('#multipleStoreContent').removeClass('display-hide');			
	            // $('#stores').attr('required', 'required');
			}			
		}		

		// Check admin
		function checkAdmin(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');

			if(selectedRolev[1] == 'DM' || selectedRolev[1] == 'RSM' || selectedRolev[1] == 'Admin' || selectedRolev[1] == 'Trainer' || selectedRolev[1] == 'Head Trainer'){
				return true;
			}

			return false;
		} 

		// Check promoter group
		function checkPromoter(){
			var role = $('#selectedRole').val();
			var selectedRolev = role.split('`');

			if(selectedRolev[1] == 'Promoter' || selectedRolev[1] == 'Promoter Additional' || selectedRolev[1] == 'Promoter Event' || selectedRolev[1] == 'Demonstrator MCC' || selectedRolev[1] == 'Demonstrator DA' || selectedRolev[1] == 'ACT'  || selectedRolev[1] == 'PPE' || selectedRolev[1] == 'BDT' || selectedRolev[1] == 'Salesman Explorer' || selectedRolev[1] == 'SMD' || selectedRolev[1] == 'SMD Coordinator' || selectedRolev[1] == 'HIC' || selectedRolev[1] == 'HIE' || selectedRolev[1] == 'SMD Additional' || selectedRolev[1] == 'ASC'){
				return true;
			}

			return false;
		} 


		function initDateTimePicker(){

            // Filter Month
            $('#join_date').datetimepicker({
                format: "yyyy-mm-dd",
                startView: "2",
                minView: "2",
                autoclose: true,
            });
            // Set to Month now
            $('#join_date').val(
            	{{( @$data->join_date ) ? "" : "moment().format(" }}
            	'{{( @$data->join_date ) ? @$data->join_date : "YYYY-MM-DD" }}'
            	{{( @$data->join_date ) ? "" : ")" }}
            );

        }

        function updateRegion(){
			var getDataUrl = "{{ url('util/rsmregion/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#region"), this.id, this.name);
						});

            	}	

        	})
		}

		function updateArea(){
			var getDataUrl = "{{ url('util/dmarea/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#area"), this.id, this.name);
						});

            	}	

        	})
		}

		function updateAreaTrainer(){
			var getDataUrl = "{{ url('util/trainerarea/') }}";
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
		                 $.each(data, function() {
							setSelect2IfPatch($("#area"), this.id, this.name);
						});

            	}	

        	})
		}

		/*
		 * Select2 change
		 *
		 */ 
		$(document.body).on("change","#role",function(){

		    setForm($('#selectedRole').val());
		    
		});

		$(document.body).on("change","#statusSpvCheck2",function(){
			var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
			// clearStore();
	       	if (statusSpv) {
		       	if (statusSpv == "Demonstrator")
		       	{
		       		select2Reset($('#stores'));
		       		$('#dedicateContent').addClass('display-hide');
		            $('#dedicate').removeAttr('required');
		       	}
		    }
		});

		$(document.body).on("change","#statusSpvCheck",function(){
			var statusSpv = $('input[type=radio][name=status_spv]:checked').val();
			// clearStore();
	       	if (statusSpv) {
		       	if (statusSpv == "Promoter") 
		       	{
		       		select2Reset($('#stores'));
		       		$('#dedicateContent').removeClass('display-hide');
		            $('#dedicate').attr('required','required');
		       	}
		    }
		});

		$(document).ready(function(){
			
			initDateTimePicker();

			// On Change status
		    // $('input[type=radio][name=status]').change(function() {
		    //     resetStore();
		    //     setStore(this.value);
		    // });

		    // On Change Role
		    $('#role').change(function() {
		        $("#selectedRole").val($("#role option:selected").val());
		    });

		    // $('div').removeClass('display-hide');
		});

		function clearStore() {
			$("#myUL").html('');
			addNumber = 0;
			$("#storeCount").html('0');
		}

	</script>

	<!-- New Multiple Store -->
	<script>
		var addNumber = 0;
		var storeArray= [];
		var storeIndex;

		$(document).ready(function () {
		  $.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		  });

		  // $('[data-toggle="tooltip"]').tooltip(); 
		  $('#addStores').tooltip();

		  var idx = 0;
		  // Clear Store Click
		  $("#clearStores").click(function(){
		  	clearStore();
		  });

		  // Add Store Click
		  $("#addStores").click(function(){
		  	console.log("before: "+storeArray);
		  	var role = $('#selectedRole').val()
			role = role.split('`');
		    var selectedStore = $('#stores').val();
		        
		    if (selectedStore != '' && selectedStore != null) {
		    	if(($('#dedicate2').val() == null || $('#dedicate2').val() == '') && role[1] == 'Supervisor Hybrid'){
                    swal("Warning", "You Have to Select Dedicate First!", "warning");
                    return;
                }

                var temp = selectedStore.toString().split('`');
		      	var inputId = temp[0] + '`' + temp[1];
		      	var inputValue = '<b>' + temp[1] + '</b> ' + temp[2];
	      		var hiddenInput = "<input type='hidden' name='store_ids[]' value='"+inputId+"'>";
	      		var storeKey = temp[1];
	      		if (role[1] == 'Supervisor Hybrid') {
	      			hiddenInput += "<input type='hidden' name='dedicate[]' value='"+$('#dedicate2').val()+"'>";
	      			inputValue += ' <b>'+ $('#dedicate2').val() + '</b>';
	      			storeKey += $('#dedicate2').val();
	      			select2Reset($('#dedicate2'));
	      		}

				storeIndex = storeArray.indexOf(storeKey);
				if (storeIndex > -1) {
					// console.log(inputId);
					console.log(storeArray);
					select2Reset($('#stores'));
					swal("Warning", "You have already selected "+temp[2]+"!", "warning");
					return;
				    // storeArray.splice(storeIndex, 1);
				}else{
		    		if (idx == 0) {
		    			$('#toggleButton').removeClass('expand');
		    			$('#toggleButton').addClass('collapse');
		    			$('#toggleContent').removeAttr('style');
			    		$('#toggleContent').attr('style','display:block');
		    		}
		      		storeArray.push(storeKey);
		      		console.log("after: "+storeArray);
		      		$("#myUL").append("<li><div class='col-sm-12'><span class='col-sm-10'>"+inputValue+"</span>"+hiddenInput+" <p id='p"+idx+"' onClick='deleteItem(p"+idx+","+storeKey+")' class='col-sm-2 btn btn-danger delete fa fa-trash-o liDelete"+idx+"'></p></div></li>");
		      		var x = document.getElementsByClassName("liDelete"+idx+"");
		      		x[0].setAttribute('onClick', "deleteItem('p"+idx+"','"+inputId+"')");
		      		idx++;
		      		addNumber++;
		      		$('#cek').val('ok');
		      		$('#storeCount').html(addNumber);
		      		$('#check').val(addNumber);
		      		select2Reset($('#stores'));
		      		$(this).attr('data-toggle','tooltip');
		      		$(this).attr('title','Select Store First');
		      		$(this).attr('data-placement','top');
		      		$(this).attr('data-original-title','Please Select Store First');
		      		$('.box-default').removeClass('collapsed-box');
				}
                
		    }else{
		    	swal("Warning", "You Have to Select Store First!", "warning");
                return;
		    }
		      
		  });

		  $(document.body).on("change","#stores",function(){
		    var input = $('#stores').val();
		    if (input != '' && input != null) {
		      $('#addStores').removeAttr('data-toggle');
		      $('#addStores').removeAttr('title');
		      $('#addStores').removeAttr('data-placement');
		      $('#addStores').removeAttr('data-original-title');
		    } else {
		      $('#addStores').attr('data-toggle','tooltip');
		      $('#addStores').attr('title','Select Store First');
		      $('#addStores').attr('data-placement','top');
		      $('#addStores').attr('data-original-title','Please Select Store First');
		    }
		  });

		});


		if ($('input[name=_method]').val() == "PATCH") {

		  $('.box-default').removeClass('collapsed-box');
		  var roles = "{{ @$data->role->role_group }}";

			  // Set STORE for Supervisor Promoter
			  // console.log("PATCH SPV Promoter Store");
			  var getDataUrl = "{{ url('util/spvstore/') }}";
			  var index = 0;
			  var hiddenInput = '';
			  var storeName = '';
			  var inputId = '';
			  var storeKey = '';
				$.get(getDataUrl + '/' + userId, function (data) {
					if (data) {
						$.each(data, function() {
							storeName = '<b>' + this.store_id + '</b> ' +this.store_name_1;
							if (this.store_name_2 != null) {
								storeName += '('+this.store_name_2+')';
							}
							// console.log("Store Name: "+storeName+userId);
				    		$('#toggleButton').removeClass('expand');
				    		$('#toggleButton').addClass('collapse');
				    		$('#toggleContent').removeAttr('style');
				    		$('#toggleContent').attr('style','display:block');
				    		// console.log('NowYouSeeMe #2');
					    
					      	index = 1000 + addNumber;
					      	inputId = this.id+'`'+this.store_id;
					      	storeKey = this.store_id;
					      	hiddenInput = "<input type='hidden' name='store_ids[]' value='"+inputId+"'>";

					      	if (roles == 'Supervisor Hybrid') {
					      		hiddenInput += "<input type='hidden' name='dedicate[]' value='"+this.dedicate+"'>";
					      		storeName += ' <b>'+ this.dedicate + '</b>';
					      		storeKey += this.dedicate;
					      		select2Reset($('#dedicate2'));
					      	}
					    	storeArray.push(storeKey);
						    addNumber++;
						    $('#storeCount').html(addNumber);
						    $('#check').val(addNumber);
					      	$("#myUL").append("<li><div class='col-sm-12'><span class='col-sm-10'>"+storeName+"</span>"+hiddenInput+" <p id='p"+index+"' onClick='col-sm-2 deleteItem(p"+index+","+inputId+")' class='btn btn-danger delete fa fa-trash-o liDelete"+index+"'> </p></div></li>");
					      	var x = document.getElementsByClassName("liDelete"+index);
					      	x[0].setAttribute('onClick', "deleteItem('p"+index+"','"+storeKey+"')");
						});
					}
			  	});
			
			  	// Set STORE for Supervisor Demonstrator
			  	// console.log("PATCH SPV Demonstrator Store");
			  	var getDataUrl = "{{ url('util/spvdemostore/') }}";
			  	var index = 0;
			  	var hiddenInput = '';
			  	var storeName = '';
			  	var inputId = '';
				$.get(getDataUrl + '/' + userId, function (data) {
					if (data) {
						$.each(data, function() {
							storeName = '<b>' + this.store_id + '</b> ' +this.store_name_1;
							if (this.store_name_2 != null) {
								storeName += '('+this.store_name_2+')';
							}
							// console.log("Store Name: "+storeName+userId);
				    		$('#toggleButton').removeClass('expand');
				    		$('#toggleButton').addClass('collapse');
				    		$('#toggleContent').removeAttr('style');
				    		$('#toggleContent').attr('style','display:block');
				    		// console.log('NowYouSeeMe #2');
					    
					      	index = 1000 + addNumber;
					      	inputId = this.id+'`'+this.store_id;
					      	storeArray.push(this.store_id);
					      	hiddenInput = "<input type='hidden' name='store_ids[]' value='"+inputId+"'>";
					    
						    addNumber++;
						    $('#storeCount').html(addNumber);
						    $('#check').val(addNumber);
					      	$("#myUL").append("<li><div class='col-sm-12'><span class='col-sm-10'>"+storeName+"</span>"+hiddenInput+" <p id='p"+index+"' onClick='col-sm-2 deleteItem(p"+index+","+inputId+")' class='btn btn-danger delete fa fa-trash-o liDelete"+index+"'> </p></div></li>");
					      	var x = document.getElementsByClassName("liDelete"+index);
					      	x[0].setAttribute('onClick', "deleteItem('p"+index+"','"+this.store_id+"')");
						});
					}
			  	});
		}


		function deleteItem(id,inputId) {
		  $('#'+id).parent().parent().remove();
		  addNumber--;
		  $('#storeCount').html(addNumber);
		  $('#check').val(addNumber);
		  if (addNumber == 0) {
		    $('#cek').val('');
		  }
		  
		  	storeIndex = storeArray.indexOf(inputId);
			if (storeIndex > -1) {
			    storeArray.splice(storeIndex, 1);
			}
			console.log(storeArray);
		}

		function searchFunction() {
		    var input, filter, ul, li, a, i;
		    input = document.getElementById("myInput");
		    filter = input.value.toUpperCase();
		    ul = document.getElementById("myUL");
		    li = ul.getElementsByTagName("li");
		    for (i = 0; i < li.length; i++) {
		        a = li[i].getElementsByTagName("span")[0];
		        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
		            li[i].style.display = "";
		        } else {
		            li[i].style.display = "none";

		        }
		    }
		}
		</script>
	<style type="text/css">
		#myInput {
		  background-image: url('{{ asset('image/searchicon.png') }}');
		  background-position: 10px 12px;
		  background-repeat: no-repeat;
		  width: 100%;
		  font-size: 13px;
		  padding: 12px 20px 12px 40px;
		  border: 1px solid #ddd;
		  margin-bottom: 12px;
		}

		#myUL {
		  list-style-type: none;
		  padding: 0;
		  margin: 0;
		}

		#myUL li div {
		  border: 1px solid #ddd;
		  margin-top: -1px; /* Prevent double borders */
		  background-color: #f6f6f6;
		  padding: 12px;
		  text-decoration: none;
		  font-size: 13px;
		  color: black;
		  display: block;
		}

		#myUL li div p {
		  float: right;
		  margin-top: -2px;
		  margin-bottom: -2px;
	      width: 36px;
		}

		#myUL li div:hover:not(.header) {
		  background-color: #eee;
		}

	</style>
	<!-- END New Multiple Store -->
@endsection