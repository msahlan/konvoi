
<table style="width:100%;">
    <tr>
        <td colspan="5" style="min-width:1000px;width:100%;">
            <div id="refreshingMap" style="display:none;">Refreshing map points...</div>
            <div id="lmap" style="width:90%;height:800px;">

            </div>

        </td>
        <td style="vertical-align:top;">
            <table style="width:100%;">
                <tr>
                    <td>
                        {{ Former::text('search_timestamp')->id('search_deliverytime')->class('form-control daterangespicker') }}
                    </td>
                    <td>
                        {{ Former::text('search_device')->id('search_device') }}
                    </td>
                    <td>
                        {{ Former::select('line_weight')->options(range(4,8))->id('lineWeight') }}
                    </td>
                    <td>
                        <input type="checkbox" checked="true" id="showLocUpdate"> Show Location Update
                    </td>
                    <td>
                        {{ Former::button('Refresh')->id('refreshMap')}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

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

        var lineWeight = 4;

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


        refreshMap();

    } );


</script>
