@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Visit Plan Report
                <small>report visit plan</small>
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
            <span class="active">Visit PLan Reporting</span>
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
                        <i class="fa fa-cog font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER REPORT</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> FILTERS BY</span>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterNik" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterRole" class="select2select">
                            <option value=""></option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Supervisor Hybrid">Supervisor Hybrid</option>
                            <option value="Salesman Explorer">Salesman Explorer</option>
                        </select>
                    </div>
                </div>

                <div class="row filter" id="monthContent" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <input type="text" id="filterMonth" class="form-control" placeholder="Month">
                    </div>
                </div>

                <br>

                <div class="btn-group">
                    <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerReset(paramReset)">
                        <i class="fa fa-refresh"></i> Reset </a>
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">Visit PLan</span>
                        </div>
                        <div class="actions" style="text-align: left">
                            <a id="export" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (SELECTED) </a>
                        </div>
                        <div class="actions" style="text-align: left; padding-right: 10px;">
                            <a id="exportAll" class="btn green-dark" disabled="disabled">
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL (ALL) </a>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="visitPlan" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th> No. </th>
                                <th> User Name </th>
                                <th> User NIK </th>
                                <th> User Role </th>
                                <th> Store Name 1 </th>
                                <th> Customer Code </th>
                                <th> Store ID </th>
                                <th> Date </th>
                                <th> Check IN</th>
                                <th> Check Out </th>
                                <th> Check In location </th>
                                <th> Check Out location </th>
                                <th> Status </th>
                            </tr>
                            </thead>
                        </table>

                    </div>

                <!-- END MAIN CONTENT -->

            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@endsection

@section('additional-scripts')

    <!-- BEGIN SELECT2 SCRIPTS -->
    <script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/handler/datetimepicker-handler.js') }}" type="text/javascript"></script>
    <!-- END SELECT2 SCRIPTS -->

    <script>
        /*
         *
         *
         */
        var filterId = ['#filterNik', '#filterRole'];
        var url = 'datatable/visitplan';
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];
        var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                            {data: 'user_name', name: 'user_name'},
                            {data: 'user_nik', name: 'user_nik'},
                            {data: 'user_role', name: 'user_role'},
                            {data: 'store_name_1', name: 'store_name_1'},
                            {data: 'store_name_2', name: 'store_name_2'},
                            {data: 'storeId', name: 'storeId'},
                            {data: 'date', name: 'date'},
                            {data: 'check_in', name: 'check_in'},
                            {data: 'check_out', name: 'check_out'},
                            {data: 'check_in_location', name: 'check_in_location'},
                            {data: 'check_out_location', name: 'check_out_location'},
                            {data: 'visit_status', name: 'visit_status'},
                            ];

        

        var paramFilter = ['visitPlan', $('#visitPlan'), url, tableColumns, columnDefs, order];

        var paramReset = [filterId, 'visitPlan', $('#visitPlan'), url, tableColumns, columnDefs, order];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            initSelect2();
            initDateTimePicker();

            var table = $('#visitPlan').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.visitplan') }}",
                    data: filters,
                    dataType: 'json',
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
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });

        });

        function initSelect2(){

            /*
             * Select 2 init
             *
             */

            $('#filterNik').select2(setOptions('{{ route("data.employee") }}', 'NIK', function (params) {
                filters['roleGroup'] = ['Supervisor', 'Supervisor Hybrid', 'Salesman Explorer'];
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.nik+' - '+obj.name+' - '+obj.role_group}
                    })
                }
            }));
            $('#filterNik').on('select2:select', function () {
                self.selected('byNik', $('#filterNik').val());
            });

            $('#filterRole').select2({
                width: '100%',
                placeholder: 'Role'
            });

            $('#filterRole').on('select2:select', function () {
                self.selected('role', $('#filterRole').val());
            });


        }

        function initDateTimePicker (){

            // Filter Month
            $('#filterMonth').datetimepicker({
                format: "MM yyyy",
                startView: "3",
                minView: "3",
                autoclose: true,
            });

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        }

        // On Change Search Date
		$(document).ready(function() {

            $('#filterMonth').change(function(){
				filters['searchMonth'] = this.value;
				console.log(filters);
            });

        });

        $("#resetButton").click( function(){

            // Hide Table Content
            // $('#dataContent').addClass('display-hide');

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        });

        $("#filterButton").click( function(){

            // Set Table Content
            // $('#dataContent').removeClass('display-hide');

        });

        

    </script>
@endsection
