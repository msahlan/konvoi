        <?php
            use App\Helpers\Prefs;
            use App\Helpers\Ks;
         ?>

    <script type="text/javascript">

    var base = '{{ url('/')}}';

    </script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

    {{ HTML::script('js/autotoken.js')}}


    <script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });

        /*
        $('#creditor-id').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/creditprogram') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#program-name').html(data);
                    $('#program-name').selectpicker('refresh');
                },'html');
        });
        */

        $('#product-type').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/creditprogram') }}',
                {
                    id : $('#creditor-id').val(),
                    type : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#program-name').html(data);
                    $('#program-name').selectpicker('refresh');
                },'html');
        });

        $('#province').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/city') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#city').html(data);
                    $('#city').selectpicker('refresh');
                },'html');
        });

        $('#city').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/district') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#district').html(data);
                    $('#district').selectpicker('refresh');
                },'html');
        });

        $('#name').keyup(function(){
            var title = $('#name').val();
            var slug = string_to_slug(title);
            $('#permalink').val(slug);
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


    });

    </script>


            <div class="text-center">
                <div class="icon-object border-slate-300 text-slate-300"><i class="icon-info3"></i></div>
                <h5 class="content-group">Informasi Kontrak Kredit <small class="display-block">Data kontrak kredit</small></h5>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="contractNumber" type="contractNumber" class="form-control" placeholder="Nomor Kontrak Kredit" name="contractNumber" value="{{ old('contractNumber') }}">

                <div class="form-control-feedback">
                    <i class="icon-office text-muted"></i>
                </div>
                @if ($errors->has('contractNumber'))
                    <span class="help-block">
                        <strong>{{ $errors->first('contractNumber') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="contractName" type="contractName" class="form-control" placeholder="Atas Nama" name="contractName" value="{{ old('contractName') }}">

                <div class="form-control-feedback">
                    <i class="icon-person text-muted"></i>
                </div>
                @if ($errors->has('contractName'))
                    <span class="help-block">
                        <strong>{{ $errors->first('contractName') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-office text-muted"></i>
                </div>
                {!! Form::select('creditor', Prefs::getCreditor()->CreditorToSelection( 'id','coName',true ),null,['class'=>'bootstrap-select', 'id'=>'creditor-id'] )  !!}

                @if ($errors->has('creditor'))
                    <span class="help-block">
                        <strong>{{ $errors->first('creditor') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-bag text-muted"></i>
                </div>
                {{ Form::select('Type', array_merge([''=>'Pilih Jenis Barang'] ,config('jc.credit_type') ) ,null,['class'=>'bootstrap-select','id'=>'product-type'] ) }}

                @if ($errors->has('Type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('Type') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-credit-card text-muted"></i>
                </div>

                {{ Form::select('programName', [''=>'Pilih Program Kredit'] ,null,['class'=>'bootstrap-select','id'=>'program-name'] ) }}

                @if ($errors->has('programName'))
                    <span class="help-block">
                        <strong>{{ $errors->first('programName') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-credit-card text-muted"></i>
                </div>
                {{ Form::select('bankCard', array_merge([''=>'Pilih Debit Card'] ,config('card.issuer') ) ,null,['class'=>'bootstrap-select'] ) }}

                @if ($errors->has('bankCard'))
                    <span class="help-block">
                        <strong>{{ $errors->first('bankCard') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="productDescription" type="productDescription" class="form-control" placeholder="Penjelasan Produk" name="productDescription" value="{{ old('productDescription') }}">

                <div class="form-control-feedback">
                    <i class="icon-info22 text-muted"></i>
                </div>
                @if ($errors->has('productDescription'))
                    <span class="help-block">
                        <strong>{{ $errors->first('productDescription') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="dueDate" type="dueDate" class="form-control" placeholder="Tanggal Jatuh Tempo" name="dueDate" value="{{ old('dueDate') }}">

                <div class="form-control-feedback">
                    <i class="icon-calendar text-muted"></i>
                </div>
                @if ($errors->has('dueDate'))
                    <span class="help-block">
                        <strong>{{ $errors->first('dueDate') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="installmentAmt" type="installmentAmt" class="form-control" placeholder="Jumlah Tagihan" name="installmentAmt" value="{{ old('installmentAmt') }}">

                <div class="form-control-feedback">
                    <i class="icon-cash text-muted"></i>
                </div>
                @if ($errors->has('installmentAmt'))
                    <span class="help-block">
                        <strong>{{ $errors->first('installmentAmt') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="pickupDate" type="pickupDate" class="form-control" placeholder="Tanggal Pembayaran Yang Diinginkan ( min. 2 hari sebelum Jatuh Tempo)" name="pickupDate" value="{{ old('pickupDate') }}">

                <div class="form-control-feedback">
                    <i class="icon-calendar text-muted"></i>
                </div>
                @if ($errors->has('pickupDate'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pickupDate') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="pickupAddress" type="pickupAddress" class="form-control" placeholder="Alamat Pengambilan Pembayaran" name="pickupAddress" value="{{ old('pickupAddress') }}">

                <div class="form-control-feedback">
                    <i class="icon-address-book text-muted"></i>
                </div>
                @if ($errors->has('pickupAddress'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pickupAddress') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-city text-muted"></i>
                </div>
                {{ Form::select('province', Prefs::getProvince()->ProvinceToSelection('province','province') ,null,['class'=>'bootstrap-select','id'=>'province'] ) }}

                @if ($errors->has('province'))
                    <span class="help-block">
                        <strong>{{ $errors->first('province') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-city text-muted"></i>
                </div>
                {{ Form::select('pickupCity', [''=>'Pilih Kota'] ,null,['class'=>'bootstrap-select','id'=>'city'] ) }}

                @if ($errors->has('pickupCity'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pickupCity') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">

                <div class="form-control-feedback">
                    <i class="icon-city text-muted"></i>
                </div>
                {{ Form::select('pickupDistrict', [''=>'Pilih Kecamatan'] ,null,['class'=>'bootstrap-select','id'=>'district'] ) }}

                @if ($errors->has('pickupDistrict'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pickupDistrict') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="pickupZIP" type="pickupZIP" class="form-control" placeholder="Kode Pos" name="pickupZIP" value="{{ old('pickupZIP') }}">

                <div class="form-control-feedback">
                    <i class="icon-address-book text-muted"></i>
                </div>
                @if ($errors->has('pickupZIP'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pickupZIP') }}</strong>
                    </span>
                @endif

            </div>
