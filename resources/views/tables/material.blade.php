@extends('layouts.limitless')

@section('page_js')

    <!-- Theme JS files -->

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>


    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/app.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/ripple.min.js"></script>

    <!-- /theme JS files -->

    <style type="text/css">
        .content-wrapper{

        }

        .page-container, .page-header-content
        /*, .navbar*/
        {
            padding-left: 16px !important;
            padding-right: 16px !important;
        }

        .navbar
        {
            padding-left: 16px !important;
            /*padding-right: 16px !important;*/
        }

        .table-wrapper{
            display: table !important;
        }

        .command-bar{
            padding:15px 20px;
        }

        .command-bar .btn{
            margin:0px 8px 8px 0px;
        }

        .dataTables_info {
            float: none;
            display: inline-block;
            padding: 9px 0;
            margin-bottom: 20px;
        }

        .dataTables_length {
            float: none;
            display: inline-block;
            margin: 0 40px 20px 0px;
        }

        .dataTables_paginate {
            float: none;
            display: inline-block;
            text-align: left;
            margin: 0 20px 0 0;
        }

        .dataTables_processing{
            top: 0;
            left: 200px;
            width: 145px;
            color: white;
            background: transparent;
            background-color: red;
            text-align: left;
            margin: 0px 0px 0px 10px;
            padding: 8px 20px 8px 20px;
            font-weight: bold;
        }

        div.clear{
            clear: both;
            display: block;
            float: none;
            min-height:45px;
            position:relative;
        }

        .del,.upload{
            cursor:pointer;
            padding:4px;
        }

    </style>

@endsection

@section('content')
    @inject('request', 'Request')
    @inject('form','Form')
    @inject('html','HTML')
    @inject('view','View')
    @inject('fupload','App\Helpers\Fupload')


                <!-- Page length options -->
                <div class="panel panel-flat  command-bar">

                    <div class="row">
                        <div class="col-md-12">

                            @if(isset($can_add) && $can_add == true)
                                <a href="{{ url($addurl) }}" class="btn btn-raised btn-raised  btn-sm btn-transparent btn-primary"><i class="fa fa-plus"></i> Add</a>
                                <a href="{{ url($importurl) }}" class="btn btn-raised btn-raised  btn-sm btn-transparent btn-primary"><i class="fa fa-upload "></i> Excel</a>
                            @endif

                            @if(isset($can_download) && $can_download == true)
                                <a class="btn btn-raised btn-raised  btn-sm btn-info btn-transparent" id="download-xls"><i class="fa fa-download "></i> Excel</a>
                                <a class="btn btn-raised btn-raised  btn-sm btn-info btn-transparent" id="download-csv"><i class="fa fa-download"></i> CSV</a>
                            @endif

                            @if(isset($is_report) && $is_report == true)
                                {!! $report_action !!}
                            @endif
                            @if(isset($is_additional_action) && $is_additional_action == true)
                                {!! $additional_action !!}
                            @endif

                            <?php
                                $in = Request::input();
                                if(count($in) > 0){
                                    $get = array();
                                    foreach($in as $k=>$v){
                                        $get[] = $k.'='.$v;
                                    }
                                    $print_url = $printlink.'?'.implode('&', $get);
                                }else{
                                    $print_url = $printlink;
                                }
                            ?>
                            <a href="{{ url($print_url) }}" class="btn btn-raised btn-raised  btn-sm btn-transparent btn-primary"><i class="fa fa-print"></i> Print Preview</a>

                            {!! $additional_filter !!}

                         </div>
                    </div>
                </div>
                <div class="panel panel-flat table-wrapper command-bar">

                    <table class="table  dataTable">

                        <thead>

                            <tr>
                                @foreach($heads as $head)
                                    @if(is_array($head))
                                        <th
                                            @foreach($head[1] as $key=>$val)
                                                @if(!is_array($val))
                                                    {{ $key }}="{{ $val }}"
                                                @endif
                                            @endforeach
                                        >
                                        {!! $head[0] !!}
                                        </th>
                                    @else
                                    <th>
                                        {{ $head }}
                                    </th>
                                    @endif
                                @endforeach
                            </tr>
                            @if(isset($secondheads) && !is_null($secondheads))
                                <tr>
                                @foreach($secondheads as $head)
                                    @if(is_array($head))
                                        <th
                                            @foreach($head[1] as $key=>$val)
                                                @if($key != 'search')
                                                    {{ $key }}="{{ $val }}"
                                                @endif
                                            @endforeach
                                        >
                                        {{ $head[0] }}
                                        </th>
                                    @else
                                    <th>
                                        {{ $head }}
                                    </th>
                                    @endif
                                @endforeach
                                </tr>
                            @endif
                        </thead>

                        <?php
                            $form = new Former();
                        ?>

                        <thead id="searchinput">
                            <tr>
                            <?php $index = $start_index ;?>
                            @foreach($heads as $in)
                                @if( $in[0] != 'select_all' && $in[0] != '')
                                    @if(isset($in[1]['search']) && $in[1]['search'] == true)
                                        @if(isset($in[1]['date']) && $in[1]['date'])
                                            <td>
                                                <div id="{{ $index }}" class="input-append datepickersearch">
                                                    <input id="{{ $index }}" name="search_{{$in[0]}}" data-format="dd-MM-yyyy" class="search_init form-control input-sm dateinput" type="text" placeholder="{{$in[0]}}" ></input>
                                                    <span class="add-on">
                                                        <i data-time-icon="fa fa-clock" data-date-icon="fa fa-calendar">
                                                        </i>
                                                    </span>
                                                </div>

                                            </td>
                                        @elseif(isset($in[1]['datetime']) && $in[1]['datetime'])
                                            <td>
                                                <div id="{{ $index }}" class="input-append datetimepickersearch">
                                                    <input id="{{ $index }}" name="search_{{$in[0]}}" data-format="dd-MM-yyyy hh:mm:ss" class="search_init form-control input-sm datetimeinput" type="text" placeholder="{{$in[0]}}" ></input>
                                                    <span class="add-on">
                                                        <i data-time-icon="fa fa-clock" data-date-icon="fa fa-calendar">
                                                        </i>
                                                    </span>
                                                </div>
                                            </td>
                                        @elseif(isset($in[1]['daterange']) && $in[1]['daterange'])
                                            <td>
                                                <div class="input-append datetimerangepickersearch">
                                                    <input id="{{ $index }}" name="search_{{$in[0]}}" class="search_init form-control input-sm daterangespicker" type="text" placeholder="{{$in[0]}}" />
                                                </div>
                                            </td>
                                        @elseif(isset($in[1]['datetimerange']) && $in[1]['datetimerange'])
                                            <td>
                                                <div class="input-append datetimerangepickersearch">
                                                    <input id="{{ $index }}" name="search_{{$in[0]}}" class="search_init form-control input-sm datetimerangepicker" type="text" placeholder="{{$in[0]}}" />
                                                </div>
                                            </td>
                                        @elseif(isset($in[1]['select']) && is_array($in[1]['select']))
                                            <td>
                                                <input id="{{ $index }}" type="text" name="search_{{$in[0]}}" id="search_{{$in[0]}}" placeholder="{{$in[0]}}" value="" style="display:none;" class="search_init form-control input-sm {{ (isset($in[1]['class']))?$in[1]['class']:'filter'}}" />
                                                <div class="styled-select">
                                                    <?php $select_class = (isset($in[1]['class']))?$in[1]['class']:'filter' ?>
                                                    {{ Form::select('select_'.$in[0],$in[1]['select'],null,array('class'=>'selector form-control form-white input-sm select-'.$select_class,'id'=>$index ))}}
                                                </div>
                                            </td>
                                        @else
                                            <td>
                                                <input id="{{ $index }}" type="text" name="search_{{$in[0]}}" id="search_{{$in[0]}}" placeholder="{{$in[0]}}" value="" class="search_init form-control input-sm {{ (isset($in[1]['class']))?$in[1]['class']:'filter'}}" />
                                            </td>
                                        @endif
                                    @else
                                        @if(isset($in[1]['clear']) && $in[1]['clear'] == true)
                                            <td><span id="clearsearch" style="cursor:pointer;">Clear Search</span></td>
                                        @else
                                            <td>&nbsp;</td>
                                        @endif
                                    @endif

                                    <?php $index++; ?>

                                @elseif($in[0] == 'select_all')
                                    <td>{{ Former::checkbox('select_all') }}</td>
                                @elseif($in[0] == '')
                                    <td>&nbsp;</td>
                                @endif


                            @endforeach
                            </tr>
                        </thead>

                     <tbody>
                        <!-- will be replaced by ajax content -->
                     </tbody>
                    </table>

                </div>
                <!-- /page length options -->

    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>


    <script type="text/javascript">

        var oTable;

        var current_pay_id = 0;
        var current_del_id = 0;
        var current_print_id = 0;

        function toggle_visibility(id) {
            $('#' + id).toggle();
        }

        /* Formating function for row details */
        function fnFormatDetails ( nTr )
        {
            var aData = oTable.fnGetData( nTr );

            //console.log(aData);

            var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';


            sOut += '</table>';

            return sOut;
        }

            $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });



            $.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
                if(oSettings.oFeatures.bServerSide === false){
                    var before = oSettings._iDisplayStart;

                    oSettings.oApi._fnReDraw(oSettings);

                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };

            $.fn.dataTableExt.oApi.fnFilterClear  = function ( oSettings )
            {
                /* Remove global filter */
                oSettings.oPreviousSearch.sSearch = "";

                /* Remove the text of the global filter in the input boxes */
                if ( typeof oSettings.aanFeatures.f != 'undefined' )
                {
                    var n = oSettings.aanFeatures.f;
                    for ( var i=0, iLen=n.length ; i<iLen ; i++ )
                    {
                        $('input', n[i]).val( '' );
                    }
                }

                /* Remove the search text for the column filters - NOTE - if you have input boxes for these
                 * filters, these will need to be reset
                 */
                for ( var i=0, iLen=oSettings.aoPreSearchCols.length ; i<iLen ; i++ )
                {
                    oSettings.aoPreSearchCols[i].sSearch = "";
                }

                /* Redraw */
                oSettings.oApi._fnReDraw( oSettings );
            };


            $('.activity-list').tooltip();

            asInitVals = new Array();

            oTable = $('.dataTable').DataTable(
                {
                    'bProcessing': true,
                    'bServerSide': true,
                    'sAjaxSource': '{{$ajaxsource}}',
                    'oLanguage': { 'sSearch': 'Search '},
                    'sPaginationType': 'full_numbers',
                    'sDom': '<"clear" lr>p<"clear" i>t',
                    'iDisplayLength':150,
                    'lengthMenu': [[100, 150, 200, 250], [100, 150, 200, 250]],
                    'initComplete': function(settings, json){
                        //alert( 'DataTables has finished its initialisation.' );
                        //$('.dataTables_length select').select2('destroy');
                    },
                    @if(isset($excludecol) && $excludecol != '')
                    'oColVis': {
                        'aiExclude': [ {{ $excludecol }} ]
                    },
                    @endif

                    'oTableTools': {
                        'sSwfPath': '{{ url('/')  }}/swf/copy_csv_xls_pdf.swf'
                    },

                    'aoColumnDefs': [
                        { 'bSortable': false, 'aTargets': [ {{ $disablesort }} ] },
                        {!! $column_styles !!}
                     ],

                    'fnServerData': function ( sSource, aoData, fnCallback ) {
                        {!! $js_additional_param !!}
                        $.ajax( {
                            "dataType": 'json',
                            "type": "POST",
                            "beforeSend" : function(request){
                                request.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
                            },
                            "url": sSource,
                            "data": aoData,
                            "success": fnCallback

                        } );
                    }

                }
            );


            @if($table_dnd == true)
                oTable.rowReordering(
                    {
                        sURL:'{{ url( $table_dnd_url ) }}',
                        sRequestType: 'GET',
                        iIndexColumn: {{ $table_dnd_idx }}
                    }
                );
            @elseif($table_group == true)
                oTable.rowGrouping({
                    bExpandableGrouping: {{ ($table_group_collapsible)?'true':'false' }},
                    iGroupingColumnIndex: {{ $table_group_idx }}
                });
            @endif


            //$('div.dataTables_length select').wrap('<div class="ingrid styled-select" />');


            $('.dataTable tbody tr td span.expander').on( 'click', function () {

                //console.log('expand !');

                var nTr = $(this).parents('tr')[0];

                if ( oTable.fnIsOpen(nTr) )
                {
                    oTable.fnClose( nTr );
                }
                else
                {
                    oTable.fnOpen( nTr, fnFormatDetails(nTr), 'details-expand' );
                }
            } );


            //header search

            $('thead input.filter').keyup( function () {
                var search_index = this.id;
                oTable.column( search_index )
                        .search( this.value )
                        .draw();
            } );



            eldatetime = $('.datetimepickersearch').datepicker({
                minView:2,
                maxView:2
            });

            eldate = $('.dateinput').datepicker({
                minView:2,
                maxView:2
            });


            eldate.on('changeDate', function(e) {

                if(e.date.valueOf() != null){
                    var dateval = e.date.valueOf();
                }else{
                    var dateval = '';
                }
                var search_index = e.currentTarget.id;

                oTable.column( search_index )
                        .search( dateval )
                        .draw();
            });

            eldatetime.on('changeDate', function(e) {

                if(e.date.valueOf() != null){
                    var dateval = e.date.valueOf();
                }else{
                    var dateval = '';
                }
                var search_index = e.target.id;

                oTable.column( search_index )
                        .search( dateval )
                        .draw();
            });

            $('.datetimerangepicker').on('apply.daterangepicker',function(ev, picker){
                console.log(this.value);
                var search_index = this.id;
                var datevals = this.value;

                oTable.column( search_index )
                        .search( datevals )
                        .draw();

            });

            $('.daterangespicker').on('apply.daterangepicker',function(ev, picker){
                console.log(this.value);
                var search_index = this.id;
                var datevals = this.value;

                oTable.column( search_index )
                        .search( datevals )
                        .draw();

            });

            $('thead select.selector').change( function () {
                var search_index = this.id;
                oTable.column( search_index )
                        .search( this.value )
                        .draw();
            } );

            $('#clearsearch').click(function(){
                $('thead td input').val('');
                oTable.search( '' )
                    .columns().search( '' )
                    .draw();
            });

            $('#download-xls').on('click',function(){
                var flt = $('thead td input, thead td select');
                var dlfilter = [];

                flt.each(function(){
                    if($(this).hasClass('datetimeinput') || $(this).hasClass('dateinput')){
                        console.log(this.parentNode);
                        dlfilter[parseInt(this.parentNode.id)] = this.value ;
                    }else{
                        dlfilter[parseInt(this.id)] = this.value ;
                    }
                });
                console.log(dlfilter);

                //var sort = oTable.fnSettings().aaSorting;
                var sort = oTable.order();
                console.log(sort);
                $.post('{{ url($ajaxdlxl) }}',{'filter' : dlfilter, 'sort':sort[0], 'sortdir' : sort[1] }, function(data) {
                    if(data.status == 'OK'){

                        window.location.href = data.urlxls;

                    }
                },'json');

                return false;
            });

            $('#download-csv').on('click',function(){
                var flt = $('thead td input, thead td select');
                var dlfilter = [];

                flt.each(function(){
                    if($(this).hasClass('datetimeinput') || $(this).hasClass('dateinput')){
                        console.log(this.parentNode);
                        dlfilter[parseInt(this.parentNode.id)] = this.value ;
                    }else{
                        dlfilter[parseInt(this.id)] = this.value ;
                    }
                });
                console.log(dlfilter);

                //var sort = oTable.fnSettings().aaSorting;
                var sort = oTable.order();

                console.log(sort);
                $.post('{{ url($ajaxdlxl) }}',{'filter' : dlfilter, 'sort':sort[0], 'sortdir' : sort[1] }, function(data) {
                    if(data.status == 'OK'){

                        window.location.href = data.urlcsv;

                    }
                },'json');

                return false;
            });

            /*
             * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
             * the footer
             */
            /*
            $('thead input').each( function (i) {
                asInitVals[i] = this.value;
            } );

            $('thead input.filter').focus( function () {

                console.log(this);

                if ( this.className == 'search_init form-control input-sm' )
                {
                    this.className = '';
                    this.value = '';
                }
            } );

            $('thead input.filter').blur( function (i) {
                console.log(this);
                if ( this.value == '' )
                {
                    this.className = 'search_init form-control input-sm';
                    this.value = asInitVals[$('thead input').index(this)];
                }
            } );

            */

            $('#select_all').on('click',function(){
                if($('#select_all').is(':checked')){
                    $('.selector').prop('checked', true);
                }else{
                    $('.selector').prop('checked',false);
                }
            });

            $('#select_all').on('ifChecked',function(){
                $('.selector').prop('checked', true);
            });

            $('#select_all').on('ifUnchecked',function(){
                $('.selector').prop('checked', false);
            });


            $('#confirmdelete').click(function(){

                $.post('{{ url($ajaxdel) }}',{'id':current_del_id}, function(data) {
                    if(data.status == 'OK'){
                        //redraw table


                        oTable.fnStandingRedraw();

                        $('#delstatusindicator').html('Payment status updated');

                        $('#deleteWarning').modal('toggle');

                    }
                },'json');
            });

            $('#printstart').click(function(){

                var pframe = document.getElementById('print_frame');
                var pframeWindow = pframe.contentWindow;
                pframeWindow.print();

            });

            $('#upload-modal').on('hidden',function(){
                $('#pictureupload_files ul').html('');
                $('#pictureupload_uploadedform ul').html('');
            });

            $('#do-upload').on('click',function(){
                var form = $('#upload-form');
                console.log(form.serialize());

                $.post(
                    '{{ url('ajax/productpicture')}}',
                        form.serialize(),
                        function(data){
                            if(data.result == 'OK:UPLOADED'){
                                $('#upload-modal').modal('hide');
                                oTable.draw();
                            }else if( data.result == 'ERR:UPDATEFAILED' ){
                                alert('Upload failed');
                            }
                        },
                        'json'
                    );

            });

            $('#upinv-modal').on('hidden',function(){
                $('#upinv-id').val('');
                $('#upinv-sku').val('');
                $('#upinv-container').html('');
            });

            $('#do-upinv').on('click',function(){
                var form = $('#upinv-form');
                console.log(form.serialize());

                $.post(
                    '{{ url('ajax/updateinventory')}}',
                        form.serialize(),
                        function(data){
                            if(data.result == 'OK:UPDATED'){
                                $('#upinv-modal').modal('hide');
                                oTable.draw();
                            }else if( data.result == 'ERR:UPDATEFAILED' ){
                                alert('Update failed');
                            }
                        },
                        'json'
                    );

            });

            $('table.dataTable').click(function(e){

                if ($(e.target).is('.del')) {
                    var _id = e.target.id;
                    var answer = confirm("Are you sure you want to delete this item ?");

                    console.log(answer);

                    if (answer == true){

                        $.post('{{ url($ajaxdel) }}',{'id':_id}, function(data) {
                            if(data.status == 'OK'){
                                //redraw table

                                oTable.draw();
                                alert("Item id : " + _id + " deleted");
                            }
                        },'json');

                    }else{
                        alert("Deletion cancelled");
                    }
                }

                {!! $js_table_event !!}


                if ($(e.target).is('.thumbnail')) {
                    var _id = e.target.id;
                    var links = [];

                    var g = $('.g_' + _id);

                    g.each(function(){
                        links.push({
                            href:$(this).val(),
                            title:$(this).data('caption')
                        });
                    })
                    var options = {
                        carousel: false
                    };

                    blueimp.Gallery(links, options);
                    console.log(links);

                }


                if ($(e.target).is('.pop')) {
                    var _id = e.target.id;
                    var _rel = $(e.target).attr('rel');

                    $.fancybox({
                        type:'iframe',
                        href: '{{ url('/')  }}' + '/' + _rel + '/' + _id,
                        autosize: true
                    });

                }


                if ($(e.target).is('.upinv')) {
                    var _id = e.target.id;
                    var _rel = $(e.target).attr('rel');
                    var _status = $(e.target).data('status');

                    $('#inv-loading-pictures').show();

                    $('#upinv-id').val(_id);
                    $('#upinv-sku').val(_rel);

                    $.post('{{ url('ajax/inventoryinfo') }}', { product_id: _id },
                        function(data){

                            $('#inv-loading-pictures').hide();

                            if(data.result == 'OK:FOUND'){
                                $('#upinv-container').html(data.html);
                            }

                        },'json');

                    $('#upinv-modal').modal();

                    $('#upinv-id').val(_id);

                    $('#upinv-title-id').html('SKU : ' + _rel);

                }


                if ($(e.target).is('.chg')) {
                    var _id = e.target.id;
                    var _rel = $(e.target).attr('rel');
                    var _status = $(e.target).data('status');

                    $('#chg-modal').modal();

                    $('#trx-chg').val(_id);
                    $('#stat-chg').val(_status);

                    $('#trx-order').html('Order # : ' + _rel);

                }

                if ($(e.target).is('.propchg')) {
                    var _id = e.target.id;
                    var _rel = $(e.target).attr('rel');
                    var _status = $(e.target).data('status');

                    console.log(_status);

                    $('#prop-chg-modal').modal();
                    $('#prop-trx-chg').val(_id);
                    $('#prop-stat-chg').val(_status);
                    $('#prop-trx-order').html('Property ID : ' + _rel);

                }

            });

            function updateselector(data){
                var opt = '';
                for(var k in data){
                    opt += '<option value="' + k + '">' + data[k] +'</option>';
                }
                return opt;
            }

            function dateFormat(indate) {
                var yyyy = indate.getFullYear().toString();
                var mm = (indate.getMonth()+1).toString(); // getMonth() is zero-based
                var dd  = indate.getDate().toString();

                return (dd[1]?dd:"0"+dd[0]) + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + yyyy;
            }


        });
      </script>



@endsection

@section('modals')

    <div id="print-modal" class="modal fade large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Print Barcode Tag</h3>
        </div>
            <div class="modal-body">

            </div>
        <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-raised btn-primary" id="prop-save-chg">Save changes</button>
        </div>
    </div>

    <div id="upload-modal" class="modal fade" tabindex="-1" data-width="760" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Upload Pictures</span></h3>
      </div>
      <div class="modal-body" >
            <h4 id="upload-title-id"></h4>
            {{ Former::open()->id('upload-form') }}
            {{ Former::hidden('upload_id')->id('upload-id') }}

            <?php
                /*
                $fupload = new Fupload();
                {{ $fupload->id('pictureupload')->title('Select Images')->label('Upload Images')->make() }}
                */
            ?>


            {{ Former::close() }}
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-upload">Save changes</button>
      </div>
    </div>

    <div id="upinv-modal" class="modal fade large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Update Inventory</span></h3>
      </div>
      <div class="modal-body" >
            <h4 id="upinv-title-id"></h4>

            {{ Former::open()->id('upinv-form') }}
            {{ Former::hidden('id')->id('upinv-id') }}
            {{ Former::hidden('SKU')->id('upinv-sku') }}
                <span id="inv-loading-pictures" style="display:none;" ><img src="{{url('/') }}/images/loading.gif" />loading existing pictures...</span>
            <div id="upinv-container">

            </div>
            {{ Former::close() }}
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-upinv">Save changes</button>
      </div>
    </div>

    {!! $modal_sets !!}


@endsection