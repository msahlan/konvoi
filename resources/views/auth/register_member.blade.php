    <?php
        use App\Helpers\Prefs;
        //use Route;

        $route = Route::current()->getUri();

        if($route == 'creditor/register'){
            $regrole = 'Creditor';
        }else{
            $regrole = 'Member';            
        }

        //print $route;
        //var_dump($route);

    ?>

                    <div class="text-center">
                        <div class="icon-object border-slate-300 text-slate-300"><i class="icon-user-plus"></i></div>
                        <h5 class="content-group">Personal Information <small class="display-block">Enter your personal informations below</small></h5>
                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input id="name" type="name" class="form-control" placeholder="Full Name" name="name" value="{{ old('name') }}">

                        <div class="form-control-feedback">
                            <i class="icon-user text-muted"></i>
                        </div>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input id="email" type="email" class="form-control" placeholder="Email Address" name="email" value="{{ old('email') }}">

                        <div class="form-control-feedback">
                            <i class="icon-envelop text-muted"></i>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input id="password" type="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}">

                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input id="password_confirmation" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}">

                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                        <input type="hidden" name="roleId" value="{!! Prefs::getRoleId($regrole) !!}" />
                    </div>

                    <div class="form-group has-feedback has-feedback-left">


                        <div class="form-control-feedback">
                            <i class="icon-credit-card text-muted"></i>
                        </div>
                        {{ Form::select('bankCard', array_merge([''=>'Select Debit Card used'] ,config('card.issuer') ) ,null,['class'=>'bootstrap-select'] ) }}

                        @if ($errors->has('bankCard'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bankCard') }}</strong>
                            </span>
                        @endif

                    </div>


                    <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="yes" name="agreeToTerms"> I have read {{ config('site.name')}} Data Use Policy, Terms & Condition, including Cookie Use, and Agree to all terms cited.
                                </label>
                            </div>
                        @if ($errors->has('agreeToTerms'))
                            <span class="help-block">
                                <strong>{{ $errors->first('agreeToTerms') }}</strong>
                            </span>
                        @endif
                    </div>

