@extends('layouts.app')

@section('header')
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Promo Activity Report
                <small>promo activity report</small>
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
            <span class="active">Promo Activity Reporting</span>
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
                        <span class="caption-subject font-blue bold uppercase">FILTER REPORT</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>
                
                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterRegion" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterArea" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterDistrict" class="select2select"></select>
                    </div>
                </div>

                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterStore" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterEmployee" class="select2select"></select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterProduct" class="select2select"></select>
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

                </div>

                <div class="portlet light display-hide bordered" id="dataContent">
                    <!-- MAIN CONTENT -->
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-o font-blue"></i>
                            <span class="caption-subject font-blue bold uppercase">Promo Activity</span>
                        </div>
                        
                        <div class="actions" style="text-align: left">
                            <a id="export" class="btn green-dark" >
                                <i class="fa fa-cloud-download"></i> DOWNLOAD TO EXCEL ALT </a>
                        </div>
                    </div>

                    <div class="portlet-body">

                        <table class="table table-striped table-hover table-bordered" id="competitorActivity" style="white-space: nowrap;">
                            <thead>
                            <tr>
                                <th></th>
                                <th> User </th>
                                <th> Region </th>
                                <th> Area </th>
                                <th> District </th>
                                <th> Store Name 1 </th>
                                <th> Customer Code </th>
                                <th> Store ID </th>
                                <th> Week </th>
                                <th> Promo Type </th>
                                <th> Information </th>
                                <th> Start Period </th>
                                <th> End Period </th>
                                <th> Photo </th>
                                <th> Product Name </th>
                                <th> Product Model </th>
                                <th> Product Variants </th>
                                <th> Date </th>
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
    <!-- END SELECT2 SCRIPTS -->

    <script>
        /*
         *
         *
         */
        var filterId = ['#filterProduct', '#filterStore', '#filterEmployee', '#filterRegion', '#filterArea', '#filterDistrict' ];
        var url = 'datatable/promoactivityreport';
        var order = [ [0, 'desc'] ];
        var columnDefs = [{"className": "dt-center", "targets": [0]}];

        var tableColumns = [{data: 'id', name: 'id', visible: false, orderable: false},
                            {data: 'user_name', name: 'user_name'},
                            {data: 'region_name', name: 'region_name'},
                            {data: 'area_name', name: 'area_name'},
                            {data: 'district_name', name: 'district_name'},
                            {data: 'store_name_1', name: 'store_name_1'},
                            {data: 'store_name_2', name: 'store_name_2'},
                            {data: 'storeid', name: 'storeid'},
                            {data: 'week', name: 'week'},
                            {data: 'promo_type', name: 'promo_type'},
                            {data: 'information', name: 'information'},
                            {data: 'start_period', name: 'start_period'},
                            {data: 'end_period', name: 'end_period'},
                            {data: 'photo', name: 'photo'},
                            {data: 'product_name', name: 'product_name'},
                            {data: 'product_model', name: 'product_model'},
                            {data: 'product_variants', name: 'product_variants'},
                            {data: 'date', name: 'date'},
                            {data: 'promo', name: 'promo'},
                            ];

        var paramFilter = ['competitorActivity', $('#competitorActivity'), url, tableColumns, columnDefs, order];
        var paramReset = [filterId, 'competitorActivity', $('#competitorActivity'), url, tableColumns, columnDefs, order];

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Set data for Data Table
            var table = $('#competitorActivity').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('datatable.promoactivityreport') }}",
                    type: 'POST',
                },
                "rowId": "id",
                "columns": tableColumns,
                "columnDefs": columnDefs,
                "order": order,
            });

            initSelect2();
            initDateTimePicker();

        });

        function initSelect2(){

            /*
             * Select 2 init
             *
             */

            $('#filterRegion').select2(setOptions('{{ route("data.region") }}', 'Region', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterRegion').on('select2:select', function () {
                self.selected('byRegion', $('#filterRegion').val());
            });

            $('#filterArea').select2(setOptions('{{ route("data.area") }}', 'Area', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterArea').on('select2:select', function () {
                self.selected('byArea', $('#filterArea').val());
            });

            $('#filterDistrict').select2(setOptions('{{ route("data.district") }}', 'District', function (params) {
                return filterData('name', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterDistrict').on('select2:select', function () {
                self.selected('byDistrict', $('#filterDistrict').val());
            });
            
            $('#filterProduct').select2(setOptions('{{ route("data.product") }}', 'Product', function (params) {
                return filterData('product', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterProduct').on('select2:select', function () {
                self.selected('byProduct', $('#filterProduct').val());
            });

            $('#filterStore').select2(setOptions('{{ route("data.store") }}', 'Store', function (params) {
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
            $('#filterStore').on('select2:select', function () {
                self.selected('byStore', $('#filterStore').val());
            });

            $('#filterEmployee').select2(setOptions('{{ route("data.employee") }}', 'Promoter', function (params) {
	        	filters['promoterGroup'] = 1;
	            return filterData('employee', params.term);
	        }, function (data, params) {
	            return {
	                results: $.map(data, function (obj) {
	                    return {id: obj.id, text: obj.nik + " - " + obj.name}
	                })
	            }
	        }));
            $('#filterEmployee').on('select2:select', function () {
                self.selected('byEmployee', $('#filterEmployee').val());
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
            $('#dataContent').addClass('display-hide');

            // Set to Month now
            $('#filterMonth').val(moment().format('MMMM YYYY'));
            filters['searchMonth'] = $('#filterMonth').val();

        });

        $("#filterButton").click( function(){

            // Set Table Content
            $('#dataContent').removeClass('display-hide');

        });

        $("#export").click( function(){

            if ($('#export').attr('disabled') != 'disabled') {

                // Export data
                exportFile = '';

                $.ajax({
                    type: 'POST',
                    url: 'util/export-promoactivity',
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


    </script>

    <style type="text/css">
        table.dataTable tbody td {
          vertical-align: middle;
        }
    </style>
@endsection
