                if ($(e.target).is('.editProvinceQuota')) {
                    var target = $(e.target);
                    var province = target.data('province');
                    $('#province-name').html(province);

                    $('#device-num').val( target.data('devnum') );
                    $('#device-cap').val( target.data('devcap') );

                    $('#update-quota-modal').modal();

                }
