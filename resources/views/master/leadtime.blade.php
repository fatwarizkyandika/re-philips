@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Leadtime
            <small>manage leadtime</small>
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
        <span class="active">Leadtime Management</span>
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
                    <i class="fa fa-map-o font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">LEADTIME</span>
                </div>
            </div>
            <div class="portlet-title">
            <!-- MAIN CONTENT -->
                <div class="btn-group">
                    <a id="add-leadtime" class="btn green" data-toggle="modal" href="#leadtime"><i
                        class="fa fa-plus"></i> Add Leadtime</a>
                </div>
                <div class="btn-group">
                    <a id="upload" class="btn btn-primary" data-toggle="modal" href="#upload-leadtime"><i
                        class="fa fa-cloud-upload"></i> Update Leadtime </a>

                </div>
                <div class="actions" style="text-align: left">
                    <a id="export" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                </div>
                <div class="actions" style="text-align: left; padding-right: 10px;">
                    <a id="exportAll" class="btn green-dark" >
                        <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                </div>
            </div>

            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

                    <table class="table table-striped table-hover table-bordered" id="leadtimeTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Area </th>
                                <th> Leadtime (days) </th>
                                <th> Options </th>
                            </tr>
                        </thead>
                    </table>

                </div>

                @include('partial.modal.leadtime-modal')
                @include('partial.modal.upload-leadtime-modal')

                <!-- END MAIN CONTENT -->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
@endsection

@section('additional-scripts')

<!-- BEGIN SELECT2 SCRIPTS -->
<script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
<!-- END SELECT2 SCRIPTS -->
<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/leadtime-handler.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/upload-modal/upload-leadtime-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>

    var dataAll = {};
    /*
     * AREA
     *
     */
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get data district to var data
        $.ajax({
            type: 'POST',
            url: 'data/leadtime',
            dataType: 'json',
            global: false,
            async: false,
            success: function (results) {
                var count = results.length;

                        if(count > 0){
                            $('#exportAll').removeAttr('disabled');
                        }else{
                            $('#exportAll').attr('disabled','disabled');
                        }

                dataAll = results;
            }
        });

        // Set data for Data Table
        var table = $('#leadtimeTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.leadtime') }}",
                type: 'POST',
                dataSrc: function (res) {
                        var count = res.data.length;

                        if(count > 0){
                            $('#export').removeAttr('disabled');
                        }else{
                            $('#export').attr('disabled','disabled');
                        }

                        this.data = res.data;
                        return res.data;
                    },
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'area_name', name: 'area_name'},
                {data: 'leadtime', name: 'leadtime'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [3]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#districtTable').on('click', 'tr td button.deleteButton', function () {
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
                            url:  'leadtime/' + id,
                            success: function (data) {
                                $("#"+id).remove();

                                $.ajax({
                                    type: 'POST',
                                    url: 'data/leadtime',
                                    dataType: 'json',
                                    global: false,
                                    async: false,
                                    success: function (results) {
                                        var count = results.length;

                                                if(count > 0){
                                                    $('#exportAll').removeAttr('disabled');
                                                }else{
                                                    $('#exportAll').attr('disabled','disabled');
                                                }

                                        dataAll = results;
                                    }
                                });
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


        initSelect2AreaApp();

    });

    $("#export").click( function(){

        if ($('#export').attr('disabled') != 'disabled') {

            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: 'util/export-leadtime',
                dataType: 'json',
                data: {data: data},
                global: false,
                async: false,
                success: function (data) {

                    console.log(data);

                    window.location = data.url;

                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'util/export-delete',
                            dataType: 'json',
                            data: {data: data.url},
                            global: false,
                            async: false,
                            success: function (data) {
                                console.log(data);
                            }
                        });
                    }, 1000);


                }
            });

        }


    });

    $("#exportAll").click( function(){

        if ($('#export').attr('disabled') != 'disabled') {

            $.ajax({
                type: 'POST',
                url: 'data/leadtime',
                dataType: 'json',
                global: false,
                async: false,
                success: function (results) {
                    dataAll = results;
                }
            });

            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: 'util/export-leadtime',
                dataType: 'json',
                data: {data: dataAll},
                global: false,
                async: false,
                success: function (data) {

                    console.log(data);

                    window.location = data.url;

                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'util/export-delete',
                            dataType: 'json',
                            data: {data: data.url},
                            global: false,
                            async: false,
                            success: function (data) {
                                console.log(data);
                            }
                        });
                    }, 1000);


                }
            });

        }


    });

    $("#exportTemplate").click( function(){

        if ($('#export').attr('disabled') != 'disabled') {

            $.ajax({
                type: 'POST',
                url: 'data/leadtime',
                dataType: 'json',
                global: false,
                async: false,
                success: function (results) {
                    dataAll = results;
                }
            });

            // Export data
            exportFile = '';

            $.ajax({
                type: 'POST',
                url: 'util/export-leadtime-template',
                dataType: 'json',
                data: {data: dataAll},
                global: false,
                async: false,
                success: function (data) {

                    console.log(data);

                    window.location = data.url;

                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'util/export-delete',
                            dataType: 'json',
                            data: {data: data.url},
                            global: false,
                            async: false,
                            success: function (data) {
                                console.log(data);
                            }
                        });
                    }, 1000);


                }
            });

        }


    });

    // Init add form
    $(document).on("click", "#add-leadtime", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        $('#name').val('');
        select2Reset($("#area"));

        // Set action url form for add
        var postDataUrl = "{{ url('leadtime') }}";
        $("#form_leadtime").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-leadtime", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('leadtime/edit/') }}";
        var postDataUrl = "{{ url('leadtime') }}"+"/"+id;

        // Set action url form for update
        $("#form_leadtime").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_leadtime").append("<input type='hidden' name='_method' value='PATCH'>");
        }

        $.get(getDataUrl + '/' + id, function (data) {

                    $('#name').val(data.leadtime);

                    setSelect2IfPatchModal($("#area"), data.area_id, data.area.name);

        })

    });

    $(document).on("click", "#upload", function () {

        resetUploadValidation();

        $('#upload_file').val('');

    });

    function initSelect2AreaApp(){

        /*
         * Select 2 init
         *
         */

         $('#area').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {
            return filterData('name', params.term);
        }, function (data, params) {
            return {
                results: $.map(data, function (obj) {
                    return {id: obj.id, text: obj.name}
                })
            }
        }));

    }


</script>
@endsection
