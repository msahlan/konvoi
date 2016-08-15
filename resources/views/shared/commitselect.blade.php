@extends('layout.limitless')

@section('page_js')

    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ URL::to('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

@stop

@section('content')

<style type="text/css">
    .form-horizontal .controls {
        margin-left: 0px;
    }

    table{
        font-size: 12px;
    }

    .page-container, .page-header-content
    /*, .navbar*/
    {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .navbar
    {
        padding-left: 16px !important;
        /*padding-right: 16px !important;*/
    }

    .table-wrapper{
        display: table !important;
    }

    .command-bar{
        padding:15px 20px;
    }


</style>

<script type="text/javascript">
        $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });


        
        $('#select_all').on('click',function(){
            if($('#select_all').is(':checked')){
                $('.selector').prop('checked', true);
            }else{
                $('.selector').prop('checked',false);
            }
        });

        $('#edit_select_all').on('click',function(){
            if($('#edit_select_all').is(':checked')){
                $('.edit_selector').attr('checked', true);
            }else{
                $('.edit_selector').attr('checked', false);
            }
        });

        /*

        $('#select_all').on('ifChecked',function(){
            $('.selector').iCheck('check');
            //$('.selector').prop('checked', true);
        });

        $('#select_all').on('ifUnchecked',function(){
            $('.selector').iCheck('uncheck');
            //$('.selector').prop('checked', false);
        });


        $('#edit_select_all').on('ifChecked',function(){
            $('.edit_selector').iCheck('check');
            //$('.selector').prop('checked', true);
        });

        $('#edit_select_all').on('ifUnchecked',function(){
            $('.edit_selector').iCheck('uncheck');
            //$('.selector').prop('checked', false);
        });
        */

    });

</script>

                <div class="panel panel-flat  command-bar table-wrapper">

                    {{Former::open_for_files_vertical($submit,'POST',array('class'=>'custom addAttendeeForm'))}}

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Import {{ $title }} Preview</h5>
                                    {{ Former::select('force_all')->label('Commit All Records')->options(array(0=>'No', 1=>'Yes'))->id('importkey')->class('form-control importkey input-sm')->help('Disregard selection checkbox and commit all rows to import. Including all data not shown in current preview page.') }}
                                    {{ Former::select('edit_key')->label('Edit Key')->options($headselect)->id('importkey')->class('form-control importkey input-sm')->help('select to set which field used for update key') }}
                                </div>
                                <div class="col-md-5" style="padding-top:25px;">
                                    {{ Former::submit('Commit Import')->id('execute')->class('btn btn-raised btn-primary input-sm') }}&nbsp;&nbsp;
                                    {{ HTML::link($back,'Cancel',array('class'=>'btn btn-raised input-sm'))}}
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            <label>Select All</label>
                                            <input id="select_all" type="checkbox">
                                        </th>
                                        <th>
                                            <label>Force Update</label>
                                            <input id="edit_select_all" type="checkbox">
                                        </th>
                                        <th>
                                            _id
                                        </th>
                                        <?php $head_id = 0; ?>
                                        @foreach($heads as $head)
                                            <th>
                                                {{ Former::select()->name('headers[]')->label('')->options($headselect)->id($head)->class('heads form-control input-sm')->value($head) }}
                                                <?php $head_id++; ?>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $counter = 1;
                                    ?>
                                    @foreach($imports->toArray() as $row)
                                    <tr>
                                        <td>
                                            {{ $counter }}
                                        </td>
                                        <td>
                                            <input class="selector" name="selector[]" value="{{ $row['_id'] }}" type="checkbox">
                                        </td>
                                        <td>
                                            <input class="edit_selector" name="edit_selector[]" value="{{ $row['_id'] }}" type="checkbox">
                                        </td>
                                        @foreach($row as $d)
                                            <td>
                                                @if( $d instanceof Carbon || $d instanceof MongoDate )
                                                    {{ $d->toRfc822String() }}
                                                @else
                                                    {{ $d }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <?php
                                        $counter++;
                                    ?>
                                    @endforeach
                                </tbody>
                            </table>
                            <p>This preview only show max of 200 first data records to commit, therefore the selection checkbox only effective for imports with less than or equal to 200 data records.</p>

                        </div>

                    </div>
                    {{ Former::close() }}

                </div>


@stop