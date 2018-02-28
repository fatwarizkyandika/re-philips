@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Guidelines
            <small>manage guidelines</small>
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
        <span class="active">Guidelines Management</span>
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
					<i class="fa fa-edit font-blue"></i>
					<span class="caption-subject font-blue bold uppercase">GUIDELINES</span>
				</div>
	        </div>
	        <div class="portlet-body" style="padding: 15px;">
	        	<!-- MAIN CONTENT -->
	        	<div class="table-toolbar">
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="btn-group">
                             	<a class="btn green" href="{{ url('product-knowledge/create') }}"><i
									class="fa fa-plus"></i> Add New </a>

                            </div>
                    	</div>
                    </div>
                </div>

	        	<table class="table table-striped table-hover table-bordered" id="productKnowledgeTable" style="white-space: nowrap;">
                	<thead>
                    	<tr>
                    		<th> No. </th>
                            <th> Type </th>
                            <th> Date & Time</th>
                            <th> Admin </th>
                            <th> Sender </th>
                        	<th> Subject </th>
                            <th> File </th>
                            <th> Target </th>
                            <th> Target Details </th>
                            <th> Total Read </th>
                            <th> Options </th>
                        </tr>
                    </thead>
				</table>

				<!-- END MAIN CONTENT -->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->

        @include('partial.util.read-who-modal')
	</div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN TEXT MODAL SCRIPTS -->
<script src="{{ asset('js/text-modal/popup.js') }}" type="text/javascript"></script>
<!-- END TEXT MODAL SCRIPTS -->

<script>
	$(document).ready(function () {

		$.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table '#athletesTable'
        var table = $('#productKnowledgeTable').dataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
                url: "{{ route('datatable.productknowledge') }}",
                type: 'POST',
            },
	        "rowId": "id",
	        "columns": [
	            {data: 'id', name: 'id'},
                {data: 'type', name: 'type'},
                {data: 'date', name: 'date'},
                {data: 'user_name', name: 'user_name'},
	            {data: 'from', name: 'from'},
                {data: 'subject', name: 'subject'},
                {data: 'file', name: 'file'},
                {data: 'target_type', name: 'target_type'},
                {data: 'target_detail', name: 'target_detail'},
                {data: 'total_read', name: 'total_read'},
	            {data: 'action', name: 'action', searchable: false, sortable: false},
	        ],
	        "columnDefs": [
        		{"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [7]},
                {"className": "dt-center", "targets": [9]},
                {"className": "dt-center", "targets": [10]},
      		],
            "order": [ [0, 'desc'] ],
    	});


    	// Delete data with sweet alert
        $('#productKnowledgeTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

            	swal({
					title: "Are you sure?",
                    text: "You will not be able to recover data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it",
                    cancelButtonText: "No, cancel",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })


                        $.ajax({

                            type: "DELETE",
                            url:  'product-knowledge/' + id,
                            success: function (data) {
                                console.log(data);

                                $("#"+id).remove();

                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });

                        swal("Deleted!", "Data has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Data is safe ", "success");
                    }
                });
        });

    });

</script>
@endsection
