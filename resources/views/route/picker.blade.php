@extends('layout.dialog')

@section('page_js')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyB6dqCEJT8VMgMKJJy-tXKKuOnLdcp89K8"></script>
    
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    {{ HTML::script('js/typeahead-addresspicker.min.js')}}

    <script type="text/javascript">
    	$(document).ready(function(){
	        // instantiate the addressPicker suggestion engine (based on bloodhound)
	        var addressPicker = new AddressPicker({
	                                            map: {
	                                                    id: '#mapPicker'
	                                                },
	                                            marker:{
	                                                draggable: true,
	                                                visible: true
	                                            }
	                                        });

	        // instantiate the typeahead UI
	        $('#address').typeahead(null, {
	          displayKey: 'description',
	          source: addressPicker.ttAdapter()
	        });

	        // Bind some event to update map on autocomplete selection
	        $('#address').bind('typeahead:selected', addressPicker.updateMap);
	        $('#address').bind('typeahead:cursorchanged', addressPicker.updateMap);

    	});


    </script>

@stop

@section('content')
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div id="mapPicker" style="width:100%;height:100%;min-width:350px;min-height:350px;"></div>
                    
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <input id="address" class="form-control" type="text" placeholder="Enter an address">
                    
                </div>
            </div>

@stop