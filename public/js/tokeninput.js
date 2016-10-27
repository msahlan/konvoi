$(document).ready(function(){
//toke city
    var cityengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/city?term=%QUERY'
        }

    });

    // Initialize engine
    cityengine.initialize();

    // Initialize tokenfield
    $('.tokenfield-city').tokenfield({
        typeahead: [null, { source: cityengine.ttAdapter() }]
    });
//district
    var districtengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/status?term=%QUERY'
        }

    });

    // Initialize engine
    districtengine.initialize();

    // Initialize tokenfield
    $('.tokenfield-district').tokenfield({
        typeahead: [null, { source: districtengine.ttAdapter() }]
    });


//status
    var sengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/status?term=%QUERY'
        }

    });

    // Initialize engine
    sengine.initialize();

    // Initialize tokenfield
    $('.tokenfield-status').tokenfield({
        typeahead: [null, { source: sengine.ttAdapter() }]
    });

// Use Bloodhound engine
    var cengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/courierstatus?term=%QUERY'
        }

    });

    // Initialize engine
    cengine.initialize();

    // Initialize tokenfield
    $('.tokenfield-courierstatus').tokenfield({
        typeahead: [null, { source: cengine.ttAdapter() }]
    });


    var mcengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/merchant?term=%QUERY'
        }

    });

    // Initialize engine
    mcengine.initialize();

    $('.auto-merchant').typeahead(null, {
      name: 'merchant_id',
      display: 'label',
      templates:{
          suggestion: function(data) {
                return '<p><strong>' + data.label + '</strong><br />' + data.email + '</p>';
            }
      },
      source: mcengine.ttAdapter()
    }).on('typeahead:selected',function(e, datum){
        console.log(datum);
        $('.merchant-id').val(datum.value);

        $('.th-loading').show();
        $.post(base + '/ajax/merchantapp',
            {
                'term':datum.value
            },
            function(data){
                $('.merchant-app').html(data);
                $('.th-loading').hide();
            },'html');

    }).on('typeahead:asyncrequest',function(){
        $('.th-loading').show();
    }).on('typeahead:asyncreceive',function(){
        $('.th-loading').hide();
    });


});

