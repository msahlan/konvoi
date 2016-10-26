
            <div class="text-center">
                <div class="icon-object border-slate-300 text-slate-300"><i class="icon-office"></i></div>
                <h5 class="content-group">Company Information <small class="display-block">Enter your company informations below</small></h5>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="coName" type="coName" class="form-control" placeholder="Company Name" name="coName" value="{{ old('coName') }}">

                <div class="form-control-feedback">
                    <i class="icon-office text-muted"></i>
                </div>
                @if ($errors->has('coName'))
                    <span class="help-block">
                        <strong>{{ $errors->first('coName') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="address_1" type="address_1" class="form-control" placeholder="Address line 1" name="address_1" value="{{ old('address_1') }}">

                <div class="form-control-feedback">
                    <i class="icon-address-book text-muted"></i>
                </div>
                @if ($errors->has('address_1'))
                    <span class="help-block">
                        <strong>{{ $errors->first('address_1') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="address_2" type="address_2" class="form-control" placeholder="Address line 2" name="address_2" value="{{ old('address_2') }}">

                <div class="form-control-feedback">
                    <i class="icon-address-book text-muted"></i>
                </div>
                @if ($errors->has('address_2'))
                    <span class="help-block">
                        <strong>{{ $errors->first('address_2') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="coPhone" type="phone" class="form-control" placeholder="Phone" name="coPhone" value="{{ old('coPhone') }}">

                <div class="form-control-feedback">
                    <i class="icon-phone text-muted"></i>
                </div>
                @if ($errors->has('coPhone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('coPhone') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="coFax" type="fax" class="form-control" placeholder="Fax" name="coFax" value="{{ old('fax') }}">

                <div class="form-control-feedback">
                    <i class="icon-address-book text-muted"></i>
                </div>
                @if ($errors->has('coFax'))
                    <span class="help-block">
                        <strong>{{ $errors->first('coFax') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="city" type="city" class="form-control" placeholder="City" name="city" value="{{ old('city') }}">

                <div class="form-control-feedback">
                    <i class="icon-city text-muted"></i>
                </div>
                @if ($errors->has('city'))
                    <span class="help-block">
                        <strong>{{ $errors->first('city') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="province" type="province" class="form-control" placeholder="Province" name="province" value="{{ old('province') }}">

                <div class="form-control-feedback">
                    <i class="icon-flag7 text-muted"></i>
                </div>
                @if ($errors->has('province'))
                    <span class="help-block">
                        <strong>{{ $errors->first('province') }}</strong>
                    </span>
                @endif

            </div>


            <div class="form-group has-feedback has-feedback-left">


                <div class="form-control-feedback">
                    <i class="icon-flag3 text-muted"></i>
                </div>
                {{ Form::select('countryOfOrigin', config('country.countries'),null,['class'=>'bootstrap-select'] ) }}

                @if ($errors->has('countryOfOrigin'))
                    <span class="help-block">
                        <strong>{{ $errors->first('countryOfOrigin') }}</strong>
                    </span>
                @endif

            </div>
