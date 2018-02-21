<!-- @extends('layouts.app')

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
        <a href="{{ url('userpromoter') }}">Employee Management</a>
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
                	<a class="btn btn-md green" href="{{ url('userpromoter') }}">
                		<i class="fa fa-chevron-left"></i> Back
                	</a>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">

	        	<!-- MAIN CONTENT -->
	        	<form id="form_user" class="form-horizontal" action="{{ url('userpromoter', @$data->id) }}" method="POST">	
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

				        <div id="statusContentSalesman" class="display-hide">
				        	<div class="form-group">
					          <label class="col-sm-2 control-label">Dedicate</label>
					          <div class="col-sm-9">
					          	<div style="width: 100%;" class="input-group input-icon right">
						          		<i class="fa"></i>
	                                        <select class="select2select" name="salesman_dedicate" id="salesman_dedicate">
	                                        	<option></option>
												<option value="Traditional Retail"
												{{ ( @$salesmanDedicate->dedicate == 'Traditional Retail' ) ? 'selected' : '' }}
												>Traditional Retail</option>
												<option value="Mother Care & Child"
												{{ ( @$salesmanDedicate->dedicate == 'Mother Care & Child' ) ? 'selected' : '' }}
												>Mother Care & Child</option>
											</select>
	                                        <span></span>
	                                </div>
	                            </div>
					        </div>
	                    </div>

				        <div id="statusContent" class="display-hide">
				        	<div class="form-group">
					          <label class="col-sm-2 control-label">Grading</label>
					          <div class="col-sm-9">
					          	<div style="width: 100%;" class="input-group input-icon right">
						          		<i class="fa"></i>
	                                        <select class="select2select" name="grading_id" id="grading">
	                                        	<option></option>
											</select>
	                                        <span></span>
	                                </div>
	                            </div>
					        </div>

	                        <div class="form-group">
	                          <label class="col-sm-2 control-label">Certificate</label>
	                          <div class="col-sm-9">
	                            <div class="input-icon right">
	                                <i class="fa"></i>
	                                <textarea id="certificate" name="certificate" class="form-control"  placeholder=" (Please add ' , ' to separate certificate &nbsp || &nbsp tolong tambahkan tanda ' , ' untuk memisahkan certificate )" rows="10" >{{ @$data->certificate }}</textarea>
	                            </div>
	        					<p class="help-block"> * Please add "<b> , </b>" to separate certificate &nbsp || &nbsp tolong tambahkan tanda "<b> , </b>" untuk memisahkan certificate </p>
	                            
	                          </div>
	                        </div> 

					        <div class="form-group" style="margin-bottom: 0px;">
	                            <label class="control-label col-md-2">Status                               
	                            </label>
	                            <div class="col-md-9">
	                                <div class="mt-radio-list" data-error-container="#form_employee_status_error">
	                                    <label class="mt-radio">
	                                        <input id="statusCheck" type="radio" name="status" value="stay" {{ (@$data->status == 'stay') ? "checked" : "" }}> Stay
	                                        <span></span>
	                                    </label>
	                                    <label class="mt-radio">
	                                        <input id="statusCheck2" type="radio" name="status" value="mobile" {{ (@$data->status == 'mobile') ? "checked" : "" }}> Mobile
	                                        <span></span>
	                                    </label>
	                                </div>
	                                <div id="form_employee_status_error"> </div>
	                            </div>
	                        </div>

	                        <div class="form-group">
	                            <label class="control-label col-md-2">
	                            Dedicate                     
	                            </label>
	                            <div class="col-md-9">
	                                <div style="width: 100%;" class="input-group input-icon right">
						          		<i class="fa"></i>
                                        <select class="select2select" name="dedicate" id="dedicate" required>
                                        	<option></option>
											<option value="DA" 
												{{ (@$data->dedicate == 'DA') ? "selected" : "" }}>
											DA</option>
											<option value="PC" 
												{{ (@$data->dedicate == 'PC') ? "selected" : "" }}>
											PC</option>
											<option value="MCC" 
												{{ (@$data->dedicate == 'MCC') ? "selected" : "" }}>
											MCC</option>
											<option value="HYBRID" 
												{{ (@$data->dedicate == 'HYBRID') ? "selected" : "" }}>
											HYBRID</option>
		                                </select>
                                        <span></span>
	                                </div>
	                            </div>
	                        </div>
	                    </div>

	                    <!-- BEGIN STORE DETAILS -->
	                    <div id="storeContent" class="display-hide">
		                    <div class="caption padding-caption">
	                        	<span class="caption-subject font-dark bold uppercase">STORE</span>
	                        	<hr>
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

		                          <div class="input-group" style="width: 100%;">
		     
		                                <select class="select2select" name="store_ids[]" id="stores" multiple="multiple" required="required"></select>
		                                
		                                <span class="input-group-addon display-hide">
		                                    <i class="fa"></i>
		                                </span>

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
	                                <select class="select2select" name="area" id="area"></select>
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
	     
	                                <select class="select2select" name="region" id="region"></select>
	                                
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

			var x = unescape("{{ @$salesmanDedicate->dedicate }}");

			// $( "#statusCheck2" ).prop( "checked", true );
			// var temp = "{{ @$data }}";
			
			// if(temp === ""){
				// console.log('asd');
				// $('input:radio[name=status]:nth(0)').attr('checked',true);
				// document.getElementById('statusCheck').checked = true;
			// }
			// console.log("{{ @$data }}");

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
	        	var role = $('#selectedRole').val();
	        	role = role.split('`');
	            if(role[1] != 'Salesman Explorer')
	            {
            		// filters['byDedicate'] = $('#dedicate').val();
            		filters['byDedicateSpv'] = $('#dedicate').val();
	        	}
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
	         	var role = $('#selectedRole').val();
	        	role = role.split('`');

	         	if (role[1] == 'Supervisor' || role[1] == 'Supervisor Hybrid') {
		        	filters['bySpv'] = $('#penampungUserId').val();
		        }
		        if(role[1] != 'Salesman Explorer')
		        {
	        		// filters['byDedicate'] = $('#dedicate').val();
	        		filters['byDedicateSpv'] = $('#dedicate').val();
		    	}
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

	       	$('#role').select2(setOptions('{{ route("data.role") }}', 'Role', function (params) {
	       		filters['promoterGroup'] = '1';
	            return filterData('role', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {            
	                    return {id: obj.id+"`"+obj.role_group, text: obj.role}
	                })
	            }
	        }));
			
			$('#selectedRole').val("{{ @$data->role_id }}`{{ @$data->role_group }}");				

			setSelect2IfPatch($("#role"), $('#selectedRole').val(), "{{ @$data->role }}");

            $('#dedicate').select2({
                width: '100%',
                placeholder: 'Dedicate'
            });

            
            $('#grading').select2(setOptions('{{ route("data.grading") }}', 'Grading', function (params) {
	            return filterData('grading', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {            
	                    return {id: obj.id, text: obj.grading}
	                })
	            }
	        }));
            setSelect2IfPatch(
		    	$("#grading"),'{{( @$data->grading_id ) ? @$data->grading_id  : "" }}','{{( @$data->grading ) ? @$data->grading  : "" }}'
			    );

            $('#salesman_dedicate').select2({
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

			$('#dmContent').addClass('display-hide');
			$('#rsmContent').addClass('display-hide');

			$('#statusContentSalesman').addClass('display-hide');

			// NIK
			$('#nik').removeAttr('required');
			$('#nik').closest('.form-group').removeClass('has-error');
			var icon = $('#nik').parent('.input-icon').children('i');
            icon.removeClass("fa-warning").removeClass("fa-check");

            // STATUS
            $('#statusContent').addClass('display-hide');
            $('#statusCheck').removeAttr('required');
		}

		// Set and init dm and rsm
		function setForm(role){
			var status = $('input[name=status]:checked').val();
			resetForm();
			resetStore();


			role = role.split('`');

			if(!checkPromoter()){
				$('input[type=radio][name=status]').prop('checked', false);
			}

			if(checkPromoter()){
				$('#statusCheck').attr('required', 'required');

				if(role[1] == 'Salesman Explorer'){
					$('input[type=radio][name=status][value=mobile]').prop('checked', true);
					status = 'mobile';
				    $('#multipleStoreContent').removeClass('display-hide');
				    $('#statusContentSalesman').removeClass('display-hide');
				    $('#storeContent').removeClass('display-hide');				
		            $('#stores').attr('required', 'required');
					$('#dedicate').prop('required',false);
				}else{
					$('#statusContent').removeClass('display-hide');
					$('#storeContent').removeClass('display-hide');
					$('#dedicate').prop('required',true);
					
					if (status == 'mobile') {
						$('#multipleStoreContent').removeClass('display-hide');
					}else{
						$('#oneStoreContent').removeClass('display-hide');
					}
					
				}

				//Set Store
		    	setStore(status);
			}
		}

		/* STORE METHOD */

		// Reset store
		function resetStore(){

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

			if($('input[name=_method]').val() == "PATCH"){
				updateStore();
			}

		}

		function updateStore(){
			var getDataUrl = "{{ url('util/empstore/') }}";
			var status = $('input[name=status]:checked').val();
                    // console.log(status);

			$.get(getDataUrl + '/' + userId, function (data) {
				if(data){
                    var element = $("#store");
                    if(status == 'mobile'){
                    	element = $("#stores");
                    }
                    select2Reset($('#store'));
                    select2Reset($('#stores'));


	                    $.each(data, function() {
	                    	if(this.store_name_2 != null){
                            	setSelect2IfPatch(element, this.id, this.store_id + " - " + this.store_name_1 + " (" + this.store_name_2 + ")");
                        	}

							setSelect2IfPatch(element, this.id, this.store_id + " - " + this.store_name_1);
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
	            $('#stores').attr('required', 'required');
			}			
		}		

		// Check promoter group
		function checkPromoter(){
			var role = $('#selectedRole').val();
			role = role.split('`');

			if(role[1] == 'Promoter' || role[1] == 'Promoter Additional' || role[1] == 'Promoter Event' || role[1] == 'Demonstrator MCC' || role[1] == 'Demonstrator DA' || role[1] == 'ACT'  || role[1] == 'PPE' || role[1] == 'BDT' || role[1] == 'Salesman Explorer' || role[1] == 'SMD' || role[1] == 'SMD Coordinator' || role[1] == 'HIC' || role[1] == 'HIE' || role[1] == 'SMD Additional' || role[1] == 'ASC'){
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

		/*
		 * Select2 change
		 *
		 */ 
		$(document.body).on("change","#role",function(){

		    setForm($('#selectedRole').val());
		    
		});

		$(document).ready(function(){
			
			initDateTimePicker();

		    // On Change status
		    $('input[type=radio][name=status]').change(function() {
		        resetStore();
		        setStore(this.value);
		    });

		    // On Change Role
		    $('#role').change(function() {
		        $("#selectedRole").val($("#role option:selected").val());
		    });

		    // On Change Dedicate
		    $('#dedicate').change(function() {
		        $("#store").val('').change();
		        $("#stores").val('').change();
		    });

		});

	</script>	
@endsection -->