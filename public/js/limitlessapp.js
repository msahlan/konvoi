    // shared functions for dynamic table
    function addTableRow(table)
    {
        // clone the last row in the table
        var $tr = $(table).find('thead tr').clone();

        var trow = $('<tr></tr>');

        $tr.find('input').each(function(){
            console.log(this);
            var dt = $('<input type="text">').attr('name',$(this).attr('name')+'[]').attr('value',$(this).val()).attr('class',$(this).attr('class')).attr('readonly','readonly');
            trow.append($('<td></td>').append(dt));
        })

        var act = $('<td><span class="btn del" style="cursor:pointer" ><b class="icon-minus-alt"></b></span></td>');

        trow.append(act);

        // append the new row to the table
        $(table).find('tbody').append(trow);

        $(table).find('thead input').val('');

    }

    /*
    $('table').on('click','.del',function(){
        console.log($(this).closest('tr').html());
        $(this).closest('tr').remove();
    });
    */

    function string_to_slug(str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to   = "aaaaeeeeiiiioooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

        return str;

    }

    $(document).ready(function(){
        $('.colorpicker').spectrum({
            preferredFormat: 'hex',
            allowEmpty: true
        });

        accounting.settings = {
            currency: {
                symbol : 'IDR',   // default currency symbol is '$'
                format: '%s %v', // controls output: %s = symbol, %v = value/number (can be object: see below)
                decimal : ',',  // decimal point separator
                thousand: '.',  // thousands separator
                precision : 2   // decimal places
            },
            number: {
                precision : 0,  // default precision on numbers is 0
                thousand: '.',
                decimal : ','
            }
        }

        $('.filterdaterangepicker').daterangepicker({
            opens:'right',
            locale:{
                format:'DD-MM-YYYY'
            }
        });

        $('.daterangespicker').daterangepicker({
            locale: {
                format:'YYYY-MM-DD'
            }
        });

        $('.datetimerangepicker').daterangepicker({
            locale: {
                format:'DD-MM-YYYY hh:mm:ss',
            },
            timePicker: true,
            timePicker12Hour: false,
            timePickerSeconds: true
        });

        /*
        $('.px-datepicker').pickadate({
            format:'yyyy-mm-dd',
            editable: true
        });
        */

        $('.p-datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            forceParse:true,
            zIndexOffset:10000
        });

        /*
        $('.p-datepicker').daterangepicker({
            locale: {
                format:'YYYY-MM-DD'
            },
            singleDatePicker: true,
            showDropdowns: true
        });
        */




        $('.pop').click(function(){
            var _id = $(this).attr('id');

            var _rel = $(this).attr('rel');

            $.fancybox({
                type:'iframe',
                href: base + '/' + _rel + '/' + _id,
                autosize: true
            });

        })


    });
