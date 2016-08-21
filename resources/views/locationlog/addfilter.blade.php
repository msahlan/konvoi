<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
 <![endif]-->

{{ HTML::style('css/leaflet.awesome-markers.css') }}
{{-- HTML::style('leaflet-routing/leaflet-routing-machine.css') --}}

<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>

{{ HTML::script('js/leaflet-google.js') }}
{{ HTML::script('js/leaflet.awesome-markers.min.js') }}
{{ HTML::script('js/leaflet.polylineDecorator.min.js') }}
{{-- HTML::script('leaflet-routing/leaflet-routing-machine.min.js') --}}

<style type="text/css">
    .use-loc{
        display: block;
        cursor:pointer;
        text-align: right;
    }
</style>

<div class="row" style="margin-bottom:10px;">
    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
        {!! Former::text('search_timestamp')->id('search_deliverytime')->class('form-control daterangespicker') !!}

    </div>
    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
        {!! Former::text('search_device')->id('search_device') !!}

    </div>
    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
        {!! Former::text('search_status')->id('search_status') !!}

    </div>
    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
            {!! Former::select('line_weight')->options( array_combine(range(6,24), range(6,24)) )->value(15)->id('lineWeight') !!}
    </div>
    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
        {!! Former::select('stepping', 'Interval ( minutes )')->options(array_combine(range(0,30), range(0,30)))
                ->value(20)
                ->id('stepping') !!}

    </div>
    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
        <input type="checkbox" id="showLocUpdate"> Show Location Update<br />

    </div>
    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
        {!! Former::button('Refresh')->id('refreshMap')!!}

    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="refreshingMap" style="display:none;">Refreshing map points...</div>
            <div id="lmap" style="width:100%;height:800px;">

            </div>

    </div>
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

        var map = L.map('lmap').setView([-6.17742,106.828308], 12);

        var lineWeight = 18;

        /*
        var googleLayer = new L.Google('ROADMAP');
        map.addLayer(googleLayer);
        */

        L.tileLayer(OSM_URL, {
            attribution: OSM_ATTRIB,
            maxZoom: 18
        }).addTo(map);

        $('#refreshMap').on('click',function(){
            refreshMap();
        });

        $('#lineWeight').on('change',function(){
            refreshMap();
        });

        $('#stepping').on('change',function(){
            refreshMap();
        });

        $('#showLocUpdate').on('click',function(){
            refreshMap();
        });

        var lg;
        var icsize = new L.Point(19,47);
        var icanchor = new L.Point(9,20);
        var shanchor = new L.Point(4,5);
        /*
        $('#map').gmap3({
            action:'init',
            options:{
                  center:[-6.17742,106.828308],
                  zoom: 11
                }
        });
        */

        var markers = [];
        var paths = [];

        function setLoc(delivery_id, lat, lon){
            $.post('{{ URL::to('/')}}/ajax/saveloc', {
                delivery_id:delivery_id,
                lat:lat,
                lon:lon
            }, function(data) {
                if(data.result == 'ok'){
                    alert('Location successfully set');
                }else{
                    alert('Failed to set location');
                }
            },'json');
        }

        function refreshMap(){

            $('#refreshingMap').show();

            var currtime = new Date();
            lineWeight = $('#lineWeight').val();
            //console.log(currtime.getTime());

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

            $.post('{{ URL::to('ajax/locationlog')}}?' + currtime.getTime() ,
                {
                    'device_identifier':$('#search_device').val(),
                    'timestamp':$('#search_deliverytime').val(),
                    //'courier':$('#search_courier').val(),
                    'status':$('#search_status').val(),
                    'stepping':$('#stepping').val()
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

                        /*
                        $.each(data.paths, function(){

                            var wp = [];

                            for(var i = 0; i < this.poly.length; i++ ){
                                wp.push( L.latLng(this.poly[i][0], this.poly[i][1]) );
                            }

                            L.Routing.control({
                              waypoints: wp
                            }).addTo(map);

                        });
                        */

                        $.each(data.paths, function(){
                            var polyline = L.polyline( this.poly,
                                {
                                    color: this.color,
                                    weight: lineWeight
                                } ).addTo(map);

                            paths.push(polyline);
                        });


                        $.each(data.locations,function(){

                            for( var i = 0; i < this.length; i++){
                                console.log(this[i].data);
                                var d = this[i].data;

                                if(d.status == 'report'){
                                    icon = icon_yellow;
                                }else if(d.status == 'delivered'){
                                    icon = icon_green;
                                }else{
                                    icon =  icon_red;
                                }

                                var content = '<div style="background-color:white;padding:3px;width:200px;">' +
                                    '<div class="bg"></div>' +
                                    '<div class="text">' + d.identifier + '<br />' + d.timestamp + '<br />' + d.delivery_id +
                                    '<br />' + d.status;

                                if(d.pod != 0 && (d.status == 'delivered' || d.status == 'pending' || d.status == 'returned') ){
                                    content += '<br />sign : ' + d.pod.sign +
                                    '<br />photo : ' + d.pod.photo;
                                    if(d.pod.total > 0){
                                        content += '<br />' + d.pod.images;
                                        //for(var i = 0; i < d.pod.total; i++){
                                            //content += '<img src="' + d.pod.images[0] + '" alt="pod" style="width:75px;height:auto;" />';
                                        //}
                                    }
                                }
                                    content += '</div>' +
                                '</div>';

                                /* hide set loc button
                                var link ='<a href="#" class="use-loc" data-did="'+ d.delivery_id +'" data-lat="'+ d.lat +'" data-lon="'+ d.lng +'">use loc</a>';

                                mlink = $(link).on('click',function(e){
                                            console.log( $(this) );
                                            var lat = $(this).data('lat');
                                            var lon = $(this).data('lon');
                                            var delivery_id = $(this).data('did');
                                            setLoc(delivery_id,lat, lon);
                                        });
                                */

                                if(d.pod != 0 && (d.status == 'delivered' || d.status == 'pending' || d.status == 'returned') ){

                                    /* hide set loc button
                                    dlink = $('<div class="set-loc" />').append(mlink);
                                    var mcontent = $(content).append(dlink)[0];
                                    */
                                    var mcontent = content;

                                }else{

                                    var mcontent = content;

                                }



                                if($('#showLocUpdate').is(':checked')){
                                    var m = L.marker(new L.LatLng( d.lat, d.lng ), { icon: icon }).addTo(map).bindPopup(mcontent);
                                    markers.push(m);

                                }else{
                                    if(d.status != 'report' && d.status != ''){
                                        var m = L.marker(new L.LatLng( d.lat, d.lng ), { icon: icon }).addTo(map).bindPopup(mcontent);
                                        markers.push(m);
                                    }
                                }

                            }


                        });


                    }

                    $('#refreshingMap').hide();

                },'json');

        }


        refreshMap();

    } );


</script>
