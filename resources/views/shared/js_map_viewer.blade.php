                if ($(e.target).is('.loc-static')) {
                    var target = $(e.target);
                    var lat = target.data('lat');
                    var lon = target.data('lon');

                    var gs_src = 'https://maps.googleapis.com/maps/api/staticmap?center=' + lat +','+ lon + '&markers=color:red&zoom=12&size=300x200';

                    $('#map-view').attr('src', gs_src);

                    $('#map-view-modal').modal();

                }
