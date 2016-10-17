$(document).ready(function(){

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

});

