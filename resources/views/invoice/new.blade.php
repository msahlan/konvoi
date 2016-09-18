@extends('layouts.formone')

@section('content')

                <!-- Invoice template -->
                <div class="panel panel-white" id="invoice-form" >
                    <div class="panel-heading">
                        <h6 class="panel-title"> Invoice </h6>
                        <div class="heading-elements">

                            <button type="button" class="btn btn-default btn-xs heading-btn"><i class="icon-file-check position-left"></i> Save</button>
                            <button type="button" class="btn btn-default btn-xs heading-btn"><i class="icon-printer position-left"></i> Print</button>


                        </div>
                    </div>

                    <div class="panel-body no-padding-bottom">
                        <div class="row">
                            <div class="col-sm-4 content-group">
                                <select v-model="activeOrg" id="org-select" class="position-left form-control" >
                                    <option v-for="org in organization" v-bind:value="org">
                                        @{{ org.name }}
                                    </option>
                                </select>
                                <br />
                                <img :src="activeOrg.logo" class="content-group mt-10" alt="" style="height: 100px;">
                                <ul class="list-condensed list-unstyled">
                                    <li>@{{ activeOrg.name }}</li>
                                    <li>@{{ activeOrg.address_1 }}</li>
                                    <li>@{{ activeOrg.address_2 }}</li>
                                </ul>
                            </div>

                            <div class="col-sm-6 content-group pull-right">
                                <div class="invoice-details form-horizontal">
                                    <h5 class="text-uppercase text-semibold">Invoice #{!! Former::text('InvoiceNumber','') !!}</h5>
                                    <ul class="list-condensed list-unstyled">
                                        <li>Date: <span class="text-semibold">{!! Former::text('IssueDate','') !!}</span></li>
                                        <li>Due date: <span class="text-semibold">{!! Former::text('DueDate','') !!}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-lg-4 content-group">
                                <span class="text-muted">Invoice To:</span>
                                <ul class="list-condensed list-unstyled">
                                    <li><h5>{!! Former::text('IssuedTo','')->placeholder('attention') !!}</h5></li>
                                    <li><span class="text-semibold">{!! Former::text('IssuedToOrg','')->placeholder('company / organization') !!}</span></li>
                                    <li>{!! Former::text('IssuedToAddr1','')->placeholder('address line 1') !!}</li>
                                    <li>{!! Former::text('IssuedToAddr2','')->placeholder('address line 2') !!}</li>
                                    <li>{!! Former::text('IssuedToCity','')->placeholder('city') !!}</li>
                                    <li>{!! Former::text('IssuedToPhone','')->placeholder('phone') !!}</li>
                                    <li><a href="#">{!! Former::text('IssuedToEmail','')->placeholder('email') !!}</a></li>
                                </ul>
                            </div>

                            <div class="col-md-6 col-lg-3 content-group pull-right">
                                <span class="text-muted">Payment Details:</span>
                                <ul class="list-condensed list-unstyled invoice-payment-details">
                                    <li><h5>Total Due: <span class="text-right text-semibold">@{{ cGrandTotal }}</span></h5></li>
                                    <li>{!! Former::text('BankName','')->placeholder('Bank name / payment method') !!}</li>
                                    <li>{!! Former::text('BankAddr1','')->placeholder('address line 1') !!}</li>
                                    <li>{!! Former::text('BankAddr2','')->placeholder('address line 2') !!}</li>
                                    <li>{!! Former::text('BankCity','')->placeholder('city') !!}</li>
                                    <li>{!! Former::text('SWIFT','')->placeholder('SWIFT Code') !!}</li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="" >
                        <table class="table table-lg">
                            <thead>                                    
                                <tr>
                                    <th>Description</th>
                                    <th class="col-sm-1">Unit</th>
                                    <th class="text-center col-sm-2">Unit Price</th>
                                    <th class="text-center col-sm-1">Qty</th>
                                    <th class="text-center col-sm-2">Total</th>
                                    <th class="col-sm-1">{!! Former::select('Currency')->options(array('USD'=>'USD','IDR'=>'IDR'))->label('Currency')->class('form-control bootstrap-select')  !!}</th>
                                </tr>
                                <tr>
                                    <th>
                                    <input type="text" name="Description" placeholder="item description" v-model="newForm.Description" class="form-control" />
                                    <input type="text" name="Explanation" placeholder="more detail ( optional )" v-model="newForm.Explanation" class="form-control" />
                                    <th>
                                    <select class="form-control bootstrap-select" id="Unit" name="Unit" tabindex="-98" v-model="newForm.Unit" >
                                        <option value="Pc" selected="selected">Pc</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Hour">Hour</option>
                                        <option value="Day">Day</option>
                                    </select>

                                    </th>
                                    <th>
                                        <input type="text" name="UnitPrice" placeholder="" v-model="newForm.UnitPrice" class="form-control" />
                                    </th>
                                    <th>
                                        <input type="text" name="UnitPrice" placeholder="" v-model="newForm.Qty" class="form-control" />
                                    </th>
                                    <th></th>
                                    <th><span type="submit" class="btn btn-raised" @click="addInvoiceItem()" ><i class="icon-add"></i></span></th>
                                </tr>

                            </thead>
                            <tbody>

                                <tr v-for="item in items" >
                                    <td>
                                        <h6 class="no-margin">@{{ item.Description }}</h6>
                                        <span class="text-muted">@{{ item.Explanation }}</span>
                                    </td>
                                    <td>@{{ item.Unit }}</td>
                                    <td class="text-right">@{{ item.UnitPrice }}</td>
                                    <td class="text-right">@{{ item.Qty }}</td>
                                    <td class="text-right"><span class="text-semibold">@{{ item.UnitPrice * item.Qty }}</span></td>
                                    <td><span class="btn btn-raised"><i class="icon-trash"></i></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="panel-body">
                        <div class="row invoice-payment">
                            <div class="col-sm-7">
                                <div class="content-group">
                                    <h6>Authorization</h6>
                                    <div class="mb-15 mt-15">
                                        <img src="assets/images/signature.png" class="display-block" style="width: 150px;" alt="">
                                    </div>

                                    <ul class="list-condensed list-unstyled text-muted">
                                        <li>Eugene Kopyov</li>
                                        <li>2269 Elba Lane</li>
                                        <li>Paris, France</li>
                                        <li>888-555-2311</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="content-group">
                                    <h6>Total due</h6>
                                    <div class="table-responsive no-border">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th class="col-sm-2">Subtotal:</th>
                                                    <td class="col-sm-2"></td>
                                                    <td class="text-right">@{{ subTotal }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Disc (%):</th>
                                                    <td >
                                                        <input type="text" class="form-control" name="Discount" value="0" v-model="Discount" />
                                                    </td>
                                                    <td class="text-right" >@{{ (subTotal * Discount )/ 100  }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tax (%):</th>
                                                    <td >
                                                        <input type="text" class="form-control" name="Tax" value="0" v-model="Tax" />
                                                    </td>
                                                    <td class="text-right" >@{{ (subTotal * Tax )/ 100  }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total:</th>
                                                    <td></td>
                                                    <td class="text-right text-primary"><h5 class="text-semibold">@{{ cGrandTotal }}</h5></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <h6>Terms & Conditions</h6>
                        <ol>
                            <li>Payment due within 5 days of Invoice Issue Date.</li>
                            <li>Late payment is possible, but with with a fee of 10% per month.</li>
                        </ol>

                        <h6>Other information</h6>

                        <p class="text-muted">Thank you for using Limitless. This invoice can be paid via PayPal, Bank transfer, Skrill or Payoneer. Payment is due within 30 days from the date of delivery. Late payment is possible, but with with a fee of 10% per month. Company registered in England and Wales #6893003, registered office: 3 Goodman Street, London E1 8BF, United Kingdom. Phone number: 888-555-2311</p>
                    </div>
                </div>
                <!-- /invoice template -->

                <script type="text/javascript">

                    Vue.http.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                    
                    var vm = new Vue({
                        'ready': function(){
                            this.fetchOrgSelection();
                        },
                        'data': {
                            'newForm': {},
                            'form': {},
                            'items':[],
                            'subTotal': 0,
                            'Discount':0,
                            'Tax':0,
                            'grandTotal':0,
                            'organization':[],
                            'activeOrg':{}

                        },

                        'computed':{
                            cGrandTotal(){
                                return ( this.subTotal - (( this.subTotal * this.Discount ) / 100 ) ) + (( this.subTotal * this.Tax ) / 100 );
                            }
                        },

                        'methods': {
                            fetchOrgSelection(){
                                this.$http.get('{{ url('ajax/org') }}').then(function(response){

                                    console.log(response.data);
                                    this.organization = response.data;

                                    console.log($('#org-select'));

                                    this.activeOrg = response.data[0];
                                });
                            },
                            addInvoiceItem( ){
                               //console.log(this.newForm);
                               var n = this.newForm;
                               this.items.push( { Description: n.Description, Explanation: n.Explanation, Unit:n.Unit, 'UnitPrice': n.UnitPrice, Qty: n.Qty  } );

                               this.items.reverse();

                               var sum = 0;

                               for( i = 0; i < this.items.length; i++ ){
                                    var b = this.items[i];
                                    sum += parseFloat(b.UnitPrice) * parseFloat(b.Qty);
                               }

                               console.log(sum);

                               this.subTotal = sum;


                            }

                        }
                    }).$mount('#invoice-form');

                </script>



@endsection

@section('left')

        <h5>Document Info</h5>


        {!! Former::text('Subject','Subject') !!}

        {!! Former::text('DocRef','Doc. Ref.') !!}

        {!! Former::text('DocDate','Doc. Date') !!}

        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('Tipe')->options(array('inactive'=>'Inactive','active'=>'Active'))->label('Type')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('IO')->options(array('incoming'=>'Incoming','outgoing'=>'Outgoing'))->label('I/O')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('IODate','IO Date') !!}
            </div>
        </div>



        <h5>Actors</h5>

        {!! Former::text('Sender','Sender') !!}

        {!! Former::text('Recipient','Recipient') !!}

        {!! Former::text('Action','Action') !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary'))!!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}

@stop

@section('right')
        <h5>Call Code</h5>
        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Topic','Topic') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Coy','Company') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('MMYY','MMYY') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Urut','Sequence') !!}
            </div>
        </div>


        {!! Former::text('Fcallcode','File Call Code')->id('Fcallcode') !!}

        <h5>Location</h5>

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Location','Location') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Boxing','Boxing') !!}
            </div>
        </div>

        <h5>Retention</h5>

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('RetPer','Retention Period') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('RetDate','Retention Date') !!}
            </div>
        </div>

        <h5>File</h5>

        {!! $fupload->id('docupload')
            ->parentclass('document')
            ->ns('document')
            ->title('Select Document')
            ->label('Upload Document')
            ->url('upload/docs')
            ->singlefile(false)
            ->prefix('document')
            ->multi(true)->make() !!}

@stop

@section('modals')

@stop

@section('aux')
{!! HTML::style('css/summernote.css') !!}
{!! HTML::style('css/summernote-bs3.css') !!}

{!! HTML::script('js/summernote.min.js') !!}

<script type="text/javascript">


$(document).ready(function() {


    $('.pick-a-color').pickAColor();

    $('#name').keyup(function(){
        var title = $('#name').val();
        var slug = string_to_slug(title);
        $('#permalink').val(slug);
    });

    $('.editor').summernote({
        height:500
    });

    $('#location').on('change',function(){
        var location = $('#location').val();
        console.log(location);

        $.post('{{ URL::to('asset/rack' ) }}',
            {
                loc : location
            },
            function(data){
                var opt = updateselector(data.html);
                $('#rack').html(opt);
            },'json'
        );

    })

    $('.auto_merchant').autocomplete({
        source: base + 'ajax/merchant',
        select: function(event, ui){
            $('#merchant-id').val(ui.item.id);
        }
    });

    function updateselector(data){
        var opt = '';
        for(var k in data){
            opt += '<option value="' + k + '">' + data[k] +'</option>';
        }
        return opt;
    }


});

</script>

@stop