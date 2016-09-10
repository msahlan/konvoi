<?php
    use App\Models\Uploaded;
?>
<div class="control-group">
    <div class="controls">
        <div class="fileupload fileupload-new margin-none" data-provides="fileupload">
            <span class="btn btn-default btn-file">
                <span class="fileupload-new">{{ $title }} or just drop it here</span>
                <input id="{{ $element_id }}" type="file" name="files[]" {{ ($multi)?'multiple':''}}  >
            </span>
        </div>
        <br />

        <div  id="{{ $element_id }}_progress" class="progress progress-xxs" >
            <div class="bar progress-bar progress-bar-info">
            </div>
        </div>
        {{--
        <br />
        <span id="loading-pictures" style="display:none;" ><img src="{{URL::to('/') }}/images/loading.gif" />loading existing pictures...</span>
        --}}

        <br/>


        <div id="{{ $element_id }}_files" class="files">
            <?php
                $allin = Request::old();
                if(isset($allin['parent_id']) && !isset($formdata) ){
                    $parent_id = $allin['parent_id'];
                }
            ?>

            <input type="hidden" name="parent_id" value="{{ $parent_id }}" />
            <input type="hidden" name="{{ $element_id }}_ns" value="{{ $ns }}" />
            <ul>
                <?php

                    $allin = Request::old();
                    $showold = false;

                    if( count($allin) > 0){
                        $showold = true;
                    }

                    if( !is_null($formdata) && isset($formdata['_id']) && $showold == false ){

                        //print 'this 1';
                        /* external detail template */

                                                // display previously saved data
                        //for($t = 0; $t < count($filename);$t++){

                        if($singlefile){
                            if( isset($formdata['fileid']) && $formdata['fileid'] != ''){
                                $files = Uploaded::where('_id', new MongoId( $formdata['fileid']) )
                                            ->where('deleted',0)
                                            ->orderBy('createdDate','desc')
                                            ->take(1)
                                            ->get();

                            }else{
                                $files = Uploaded::where('parent_id',$parent_id )
                                            ->where('deleted',0)
                                            ->orderBy('createdDate','desc')
                                            ->take(1)
                                            ->get();
                            }
                        }else{
                            //print "multi";
                            $files = Uploaded::where('parent_id',$parent_id )
                                        //->orWhere('parent_id', new MongoId($parent_id))
                                        ->where('deleted',0)
                                        ->orderBy('createdDate','desc')
                                        ->get();
                        }



                        if( $files && count($files->toArray()) > 0 ){
                            foreach ($files->toArray() as $fd) {
                                //print_r($fd);

                                if($prefix != '' && view()->exists($prefix.'.wdetail') ){
                                    $detailview = $prefix.'.wdetail';
                                }else{
                                    $detailview = 'wupload.detail';
                                }

                                //print $detailview;



                                try {
                                    $thumb = view($detailview)
                                                    ->with('filedata',$fd)
                                                    ->render();

                                } catch (Exception $e) {

                                }

                                if($fd['ns'] == $ns){
                                    print $thumb;
                                }



                            }

                        }


                    }

                    // display re-populated data from error form
                    if($showold && isset( $allin['parent_id'])){

                        //print_r($allin);

                        if($singlefile){
                            /*
                            $files = Uploaded::where('parent_id',$allin['parent_id'] )
                                        ->where('deleted',0)
                                        ->orderBy('createdDate','desc')
                                        ->take(1)
                                        ->get();
                            */

                            if( isset($allin['fileid']) && $allin['fileid'] != ''){
                                $files = Uploaded::where('_id', new MongoId($allin['fileid']) )
                                            ->where('deleted',0)
                                            ->orderBy('createdDate','desc')
                                            ->take(1)
                                            ->get();

                            }else{
                                $files = Uploaded::where('parent_id',$allin['parent_id'] )
                                            ->where('deleted',0)
                                            ->orderBy('createdDate','desc')
                                            ->take(1)
                                            ->get();

                            }

                        }else{
                            $files = Uploaded::where('parent_id',$allin['parent_id'] )
                                        ->where('deleted',0)
                                        ->orderBy('createdDate','desc')
                                        ->get();
                        }

                        $ns = (isset($allin[$element_id.'_ns']))?$allin[$element_id.'_ns']:'';

                        if( count($files->toArray()) > 0){
                            foreach ($files->toArray() as $fd) {
                                //print_r($fd);

                                if($prefix != '' && view()->exists($prefix.'.wdetail') ){
                                    $detailview = $prefix.'.wdetail';
                                }else{
                                    $detailview = 'wupload.detail';
                                }


                                try {

                                    $thumb = View::make($detailview)
                                                    ->with('filedata',$fd)
                                                    ->render();

                                    if($fd['ns'] == $ns){
                                        print $thumb;
                                    }

                                } catch (Exception $e) {

                                }




                            }

                        }

                    }
                ?>
            </ul>
        </div>
        <div id="{{ $element_id }}_uploadedform">
            <ul style="list-style:none">
            </ul>
        </div>
    </div>
</div>

<style type="text/css">
    .file_del, .file_copy{
        cursor: pointer;
    }
</style>

<?php
    $uploadsession = str_random(12);
?>

<script type="text/javascript">

    $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });



    var url = '{{ URL::to($url) }}?parclass={{ $parent_class }}&parid={{ $parent_id }}&ns={{ $ns }}&singlefile={{$singlefile}}&usession={{$uploadsession}}';

    $('#{{ $element_id }}_files').on('click',function(e){

        if ($(e.target).is('.file_del')) {
            var _id = e.target.id;
            var answer = confirm("Are you sure you want to delete this item ?");

            console.log($(e.target).parent());

            if (answer == true){

                $.post('{{ URL::to('ajax/delfile')}}',
                    { id: _id },
                    function(data) {
                        if(data.status == 'OK'){
                            $('#par_' + _id).remove();
                            $('#fdel_'+e.target.id).remove();
                        }
                    },
                    'json');

            }else{
                alert("Deletion cancelled");
            }
        }
    });

    $('#{{ $element_id }}').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $('#{{ $element_id }}_progress .bar').css(
                'width',
                '0%'
            );

            if(data.result.status == 'OK'){

                var thumbs = atob(data.result.thumbs);

                console.log(thumbs);

                $('#{{ $element_id }}_files ul').html(thumbs);

            }else{
                alert(data.result.message)
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#{{ $element_id }}_progress .bar').css(
                'width',
                progress + '%'
            );
        }
    })
    .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

});


</script>

