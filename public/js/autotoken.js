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
            url : base + '/ajax/district',
            replace : function(url, uriEncodedQuery) {
                city = $('.tokenfield-city').val();

                return url + '?term=' + uriEncodedQuery + '&city=' + city;
            },

        }

    });

    // Initialize engine
    districtengine.initialize();

    // Initialize tokenfield
    $('.tokenfield-district').tokenfield({
        typeahead: [null, { source: districtengine.ttAdapter() }]
    });

    //device autocomplete
    var devengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/device?term=%QUERY'
        }

    });

    // Initialize engine
    devengine.initialize();

    $('.auto-device').typeahead(null, {
      name: 'merchant_id',
      display: 'name',
      templates:{
          suggestion: function(data) {
                return '<p><strong>' + data.name + '</strong><br />' + data.dev_id + '</p>';
            }
      },
      source: devengine.ttAdapter()
    }).on('typeahead:selected',function(e, datum){
        $('.device-id').val(datum.dev_id);
        $('.device-key').val(datum.id);
    }).on('typeahead:asyncrequest',function(){
        $('.th-loading').show();
    }).on('typeahead:asyncreceive',function(){
        $('.th-loading').hide();
    });

    var progdevengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/creditprogram',
            replace : function(url, uriEncodedQuery) {
                creditor = $('#creditor-id').val();

                return url + '?term=' + uriEncodedQuery + '&cat=' + creditor;
            },
        }

    });

    // Initialize engine
    progdevengine.initialize();

    $('.auto-program').typeahead(null, {
      name: 'merchant_id',
      display: 'name',
      templates:{
          suggestion: function(data) {
                return '<p><strong>' + data.name + '</strong><br />' + data.creditor + '</p>';
            }
      },
      source: progdevengine.ttAdapter()
    }).on('typeahead:selected',function(e, datum){
        $('.program-name').val(datum.value);
    }).on('typeahead:asyncrequest',function(){
        $('.th-loading').show();
    }).on('typeahead:asyncreceive',function(){
        $('.th-loading').hide();
    });



    var userengine = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url : base + '/ajax/user?term=%QUERY'
        }

    });

    // Initialize engine
    userengine.initialize();

    $('.auto-user').typeahead(null, {
      name: 'user_id',
      display: 'email',
      templates:{
          suggestion: function(data) {
                return '<p><strong>' + data.email + '</strong><br />' + data.name + '<br />' + data.id + '</p>';
            }
      },
      source: userengine.ttAdapter()
    }).on('typeahead:selected',function(e, datum){
        console.log(datum);
        $('#picId').val(datum.id);
        $('#picName').val(datum.name);
        $('#picPhone').val(datum.phone);
        $('#picMobile').val(datum.mobile);

    }).on('typeahead:asyncrequest',function(){
        $('.th-loading').show();
    }).on('typeahead:asyncreceive',function(){
        $('.th-loading').hide();
    });

});

