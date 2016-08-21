@extends('layouts.limitless')

@section('page_js')

    <!-- Theme JS files -->
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/core/app.js"></script>

    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/ui/ripple.min.js"></script>

    {{ HTML::script('js/Sortable.min.js')}}

    {{ HTML::script('js/autotoken.js')}}


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

    </style>

@stop


@section('content')

<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
 <![endif]-->

{{ HTML::style('css/leaflet.awesome-markers.css') }}
{{ HTML::style('leaflet-geosearch/css/l.geosearch.css') }}

<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

{{ HTML::script('js/leaflet-google.js') }}
{{ HTML::script('js/leaflet.awesome-markers.min.js') }}
{{ HTML::script('js/leaflet.polylineDecorator.min.js') }}
{{ HTML::script('leaflet-geosearch/js/l.control.geosearch.js') }}
{{ HTML::script('leaflet-geosearch/js/l.geosearch.provider.google.js') }}


<style type="text/css">
.act{
    cursor: pointer;
}

.pending{
    padding: 4px;
    background-color: yellow;
}

.canceled{
    padding: 4px;
    background-color: red;
    color:white;
}

.sold{
    padding: 4px;
    background-color: green;
    color:white;
}


.del,.upload,.upinv,.outlet,.action{
    cursor:pointer;
}


td.group{
    background-color: #AAA;
}

.ingrid.styled-select select{
    width:100px;
}

.table-responsive{
    overflow-x: auto;
}

th.action{

    /*
    min-width: 150px !important;
    max-width: 200px !important;
    width: 175px !important;
    */
}

td i.fa{
    font-size: 18px;
    line-height: 20px;
}

td a{
    line-height: 22px;
}

td{
    font-size: 11px;
    padding: 4px 6px 6px 4px !important;
    hyphens:none !important;
    color: black;
}

td .dropdown-menu{
    font-size: 11px !important;
}

td .dropdown-menu .action{
    padding: 7px 10px;
    font-size: 11px !important;
}


select.input-sm {
    height: 30px;
    line-height: 30px;
    padding-top: 0px !important;
}

.panel-heading{
    font-size: 20px;
    font-weight: bold;
}

.tag{
    padding: 2px 4px;
    margin: 2px;
    background-color: #CCC;
    display:inline-block;
}

.calendar-date thead th{
    border: none;
}

.column-amt{
    text-align: right;
}

.column-nowrap{
    white-space: nowrap !important;
}

.dataTable th{
    font-size: 10px !important;
}

.action{
    cursor: pointer;
}

.ui-menu .ui-menu-item a {
  font-size: 12px;
}
.ui-autocomplete {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1510 !important;
  float: left;
  display: none;
  min-width: 160px;
  width: 160px;
  padding: 4px 0;
  margin: 2px 0 0 0;
  list-style: none;
  background-color: #ffffff;
  border-color: #ccc;
  border-color: rgba(0, 0, 0, 0.2);
  border-style: solid;
  border-width: 1px;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  -webkit-background-clip: padding-box;
  -moz-background-clip: padding;
  background-clip: padding-box;
  *border-right-width: 2px;
  *border-bottom-width: 2px;
}
.ui-menu-item{
    padding: 8px 4px;
}

.ui-menu-item > a.ui-corner-all {
    display: block;
    padding: 3px 15px;
    clear: both;
    font-weight: normal;
    line-height: 18px;
    color: #555555;
    white-space: nowrap;
    text-decoration: none;
}
.ui-state-hover, .ui-state-active {
      color: #ffffff;
      text-decoration: none;
      background-color: #0088cc;
      border-radius: 0px;
      -webkit-border-radius: 0px;
      -moz-border-radius: 0px;
      background-image: none;
}

.ui-state-hover, .ui-state-focus {
      color: #ffffff;
      text-decoration: none;
      background-color: #0088cc;
      border-radius: 0px;
      -webkit-border-radius: 0px;
      -moz-border-radius: 0px;
      background-image: none;
}

.btn{
    margin-bottom: 5px;
}

.dupe{
    font-weight: bolder;
    background-color: yellow;
    padding: 2px 4px;
    display: inline-block;
}

.rt-handle{
    cursor:pointer;
}
.media-body{
    cursor:move;
}

.modal.large {
    width: 80%; /* respsonsive width */
    margin-left:-40%; /* width/2) */
}

.modal.large .modal-body{
    max-height: 800px;
    height: 500px;
    overflow: auto;
}

</style>

{{ HTML::style('css/syscolors.css') }}

{{--
<div class="row-fluid box">
   <div class="col-md-12 box-content">
        <table class="table table-condensed dataTable">
<div class="container" style="padding-top:40px;">--}}
{{-- </div> --}}
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div id="refreshingMap" style="display:none;">Refreshing map points...</div>
            <div id="lmap" style="width:100%;height:800px;">

            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    {!! Former::text('assigmentdate','Assignment Date')->id('rt-ts')->class('form-control p-datepicker')  !!}
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    {!! Former::text('Device')->id('devname')->class('form-control auto-device')  !!}
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    {!! Former::text('Device ID')->id('devid')->class('form-control device-id')  !!}
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    {!! Former::select('line_weight','Line Size')->options(range(4,8))->id('lineWeight')  !!}
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        {!! Former::button('Load')->id('loadOrder') !!}
                        {!! Former::button('Save')->id('saveRoute') !!}
                </div>
            </div>
            <br/>
            <div class="row" style="overflow:auto;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="route-list-container">

                    <p>Set Assignment Date and Device then click Refresh</p>




                </div>
            </div>


        </div>
    </div>

<div id="addresspick-modal" class="modal fade large" tabindex="-1" role="dialog" aria-labelledby="addressPickLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="addressPickLabel">Pick Location</h3>
    </div>
        <div class="modal-body" style="overflow:hidden;">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        {!! Former::text('sellat','Latitude')->id('sel-lat') !!}
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        {!! Former::text('sellon','Longitude')->id('sel-lon') !!}
                    </div>
                </div>
                <div id="map-picker" style="height:400px;"></div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        {!! Former::hidden('targetobj','')->id('target-obj') !!}
                        {!! Former::text('selsearch','Search')->id('sel-search') !!}
                    </div>
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                        <button type="button" class="btn btn-primary" id="bt-locsearch">search</button>
                    </div>
                </div>
                <div id="sel-result" style="overflow-y:auto;height:350px;padding:8px;"></div>

            </div>
        </div>

        </div>
    <div class="modal-footer">
    <button class="btn btn-raised" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-raised btn-primary" id="use-location">Use Location</button>
    </div>
</div>



<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<script>
    var asInitVals = new Array();
    //var locdata = <?php //print $locdata;?>;

    CM_ATTRIB = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://cloudmade.com">CloudMade</a>';

    CM_URL = 'http://{s}.tile.cloudmade.com/bc43265d42be42e3bfd603f12a8bf0e9/997/256/{z}/{x}/{y}.png';

    OSM_URL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    OSM_ATTRIB = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';

    $(document).ready(function() {

        $('#sel-result').on('click',function(e){
            if ($(e.target).is('.bt-viewmap')) {
                var lat = $(e.target).data('lat');
                var lon = $(e.target).data('lon');

                if(lat != '' && lon != ''){
                    markerPicker.setLatLng([ parseFloat(lat),parseFloat(lon) ]);
                    mapPicker.panTo([ parseFloat(lat),parseFloat(lon) ]);
                }

            }

        });

        $('#use-location').on('click',function(e){
            var id = $('#target-obj').val();

        });

        $('#route-list-container').on('click',function(e){

            console.log(e.target);

            if ($(e.target).is('.pick-location')) {
                var lat = $(e.target).data('lat');
                var lon = $(e.target).data('lon');
                var id = $(e.target).data('id');

                if(lat != '' && lon != ''){
                    markerPicker.setLatLng([ parseFloat(lat),parseFloat(lon) ]);
                    mapPicker.panTo([ parseFloat(lat),parseFloat(lon) ]);
                }

                $('#target-obj').val(id);

                $('#addresspick-modal').modal();
            }

            if ($(e.target).is('.fa-caret-up')) {
                var pid = $(e.target).data('pid');
                var parent = $('#'+pid);
                console.log(pid);
                console.log(parent);
                parent.insertBefore(parent.prev('div.panel'));

                updateMarkers();

            }

            if ($(e.target).is('.fa-caret-down')) {
                var pid = $(e.target).data('pid');
                console.log(pid);
                var parent = $('#'+pid);
                console.log(parent);

                parent.insertAfter(parent.next('div'));

                updateMarkers();
            }

            e.preventDefault();
        });

        var defLatLng = L.latLng(-6.17742,106.828308);

        var map = L.map('lmap').setView(defLatLng, 12);

        var mapPicker = L.map('map-picker').setView(defLatLng, 16);

        var markerPicker = L.marker(defLatLng,{
            draggable:true
        }).on('dragend',function(e){
            var pos = e.target.getLatLng();
            reposMarker(pos);
        }).addTo(mapPicker);

        mapPicker.panTo([-6.17742,106.828308]);

        var lineWeight = 4;

        mapboxAccessToken = 'pk.eyJ1IjoiYXdpZGFydG8iLCJhIjoiY2lrZTZkajlxMDAzanVqbTJ0MzBobHJvbyJ9.YcHo8fdjY0xvWsi2mhbsaA';

        /*
        var googleLayer = new L.Google('ROADMAP');
        map.addLayer(googleLayer);
        */

        L.tileLayer(OSM_URL, {
            attribution: OSM_ATTRIB,
            maxZoom: 18
        }).addTo(map);

        L.tileLayer(
            'https://api.mapbox.com/styles/v1/mapbox/streets-v9/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            tileSize: 512,
            zoomOffset: -1,
            attribution: '© <a href="https://www.mapbox.com/map-feedback/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            test: function() { return Math.random(); }
        }).addTo(mapPicker);

        $('#addresspick-modal').on('shown.bs.modal', function(){
            setTimeout(function() {
                console.log('invalidateSize');
                mapPicker.invalidateSize();
            }, 10);

            var pos = markerPicker.getLatLng();
            $('#sel-lat').val(pos.lat);
            $('#sel-lon').val(pos.lng);

        });

        $('#bt-locsearch').on('click',function(){
            $.post('{{ URL::to('route/locsearch')}}',{
                term: $('#sel-search').val()
            },function(data){
                $('#sel-result').html(data);
            },'html');
        });

        $('#loadOrder').on('click',function(){
            //refreshMap();
            pullOrderData();
        });

        $('#lineWeight').on('change',function(){
            refreshMap();
        });

        $('#showLocUpdate').on('click',function(){
            refreshMap();
        });

        $('#saveRoute').on('click',function(){
            saveSeq();
        });

        var lg;
        var icsize = new L.Point(19,47);
        var icanchor = new L.Point(9,20);
        var shanchor = new L.Point(4,5);

        var icon_yellow = L.AwesomeMarkers.icon({
            icon: 'icon-gift',
            color: 'orange',
            iconSize: icsize,
            iconAnchor: icanchor,
            shadowAnchor: shanchor
        });
        var icon_green = L.AwesomeMarkers.icon({
            icon: 'icon-location-arrow',
            color: 'green',
            iconSize: icsize,
            iconAnchor: icanchor,
            shadowAnchor: shanchor
        });
        var icon_red = L.AwesomeMarkers.icon({
            icon: 'icon-exchange',
            color: 'red',
            iconSize: icsize,
            iconAnchor: icanchor,
            shadowAnchor: shanchor
        });

        /*
        $('#map').gmap3({
            action:'init',
            options:{
                  center:[-6.17742,106.828308],
                  zoom: 11
                }
        });
        */

        function reposMarker(pos){
            mapPicker.panTo(pos);
            $('#sel-lat').val(pos.lat);
            $('#sel-lon').val(pos.lng);
        }

        var markers = [];
        var paths = [];

        function pullOrderData(){


            $.post('{{ URL::to('ajax/routelist')}}',
            {
                'timestamp':$('#rt-ts').val(),
                'devname': $('#devname').val(),
                'devid':$('#devid').val()
            }, function(data){

                $('#route-list-container').html(data);

                updateMarkers();

            },'html');
        }


        function refreshMap(){

            $('#refreshingMap').show();

            var currtime = new Date();
            lineWeight = $('#lineWeight').val();
            //console.log(currtime.getTime());


            $.post('{{ URL::to('ajax/locationlog')}}?' + currtime.getTime() ,
                {
                    'device_identifier':$('#search_device').val(),
                    'timestamp':$('#search_deliverytime').val(),
                    'courier':$('#search_courier').val(),
                    'status':$('#search_status').val()
                },

                function(data) {


                    if(data.result == 'ok'){


                        if(paths.length > 0){

                            for(m = 0; m < paths.length; m++){
                                map.removeLayer(paths[m]);
                            }

                            paths = [];

                        }

                        if(markers.length > 0){

                            for(m = 0; m < markers.length; m++){
                                map.removeLayer(markers[m]);
                            }

                            markers = [];

                        }

                        $.each(data.paths, function(){
                            var polyline = L.polyline( this.poly,
                                {
                                    color: this.color,
                                    weight: lineWeight
                                } ).addTo(map);

                            paths.push(polyline);
                        });


                        $.each(data.locations,function(){

                            if(this.data.status == 'report'){
                                icon = icon_yellow;
                            }else if(this.data.status == 'delivered'){
                                icon = icon_green;
                            }else{
                                icon =  icon_red;
                            }

                            var content = '<div style="background-color:white;padding:3px;width:150px;">' +
                                '<div class="bg"></div>' +
                                '<div class="text">' + this.data.identifier + '<br />' + this.data.timestamp + '<br />' + this.data.delivery_id + '<br />' + this.data.status +  '</div>' +
                            '</div>';

                            if($('#showLocUpdate').is(':checked')){
                                var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon }).addTo(map).bindPopup(content);
                                markers.push(m);

                            }else{
                                if(this.data.status != 'report' && this.data.status != ''){
                                    var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon }).addTo(map).bindPopup(content);
                                    markers.push(m);
                                }
                            }

                        });

                    }

                    $('#refreshingMap').hide();

                },'json');

        }

        function makeSortable(){
            var container = document.getElementById('route-list-container');
            var sort = Sortable.create(container, {
                animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
                handle: ".media-body", // Restricts sort start click/touch to the specified element
                draggable: ".rt-item", // Specifies which items inside the element should be sortable
                onUpdate: function (evt/**Event*/){
                 var item = evt.item; // the current dragged HTMLElement
                },
                onSort: function(evt){
                    //console.log(this);
                    updateMarkers();
                }
            });
        }

        function updateMarkers(){
            $('#refreshingMap').show();

            if(paths.length > 0){

                for(m = 0; m < paths.length; m++){
                    map.removeLayer(paths[m]);
                }

                paths = [];

            }

            if(markers.length > 0){

                for(m = 0; m < markers.length; m++){
                    map.removeLayer(markers[m]);
                }

                markers = [];

            }


            var coords = [];

            $('#route-list-container').children().each(function(idx){
                var ob = {
                    id:this.id,
                    lat:parseFloat($(this).data('lat')),
                    lon:parseFloat($(this).data('lon')),
                    address: $(this).data('address')
                };

                var content = '<div style="background-color:white;padding:3px;width:150px;">' +
                    '<div class="bg"></div>' +
                    '<div class="text">' + ob.id + '<br />' + ob.address +
                '</div>';


                if( $(this).data('lat') == '' || $(this).data('lon') == '' || isNaN($(this).data('lon')) || isNaN($(this).data('lat'))){

                }else{
                    var m = L.marker(new L.LatLng( ob.lat, ob.lon ), { icon: icon_green }).addTo(map).bindPopup(content);
                    markers.push(m);
                    coords.push([ob.lat,ob.lon]);
                }

            });

            var polyline = L.polyline( coords,
                {
                    color: '#000',
                    weight: 5
                } ).addTo(map);

            paths.push(polyline);


            $('#refreshingMap').hide();

            console.log(coords);

        }

        function saveSeq(){
            var seqs = [];
            $('#route-list-container').children().each(function(idx){
                var ob = {
                    id:this.id,
                    seq: idx
                };

                seqs.push(ob);
            });

            $.post('{{ URL::to('route/saveseq') }}',{
                seq:seqs
            },function(data){
                if(data.result == 'OK'){
                    alert('sequence saved');
                }
            },'json');

        }

        makeSortable();

        //refreshMap();






    } );


</script>



@stop

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


<div id="chg-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Change Transaction Status</h3>
  </div>
  <div class="modal-body">
    <h4 id="trx-order"></h4>
    {{ Former::hidden('trx_id')->id('trx-chg') }}
    {{ Former::select('status', 'Status')->options(config('ia.trx_status'))->id('stat-chg')}}
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-raised btn-primary" id="save-chg">Save changes</button>
  </div>
</div>


{{ $modal_sets }}


@stop