    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-indigo">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ URL::to('/')}}" style="text-transform:uppercase">{{ Config::get('site.name')}}</a>

            <ul class="nav navbar-nav pull-right visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            </ul>
        </div>

        <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-inbox-alt"></i>
                        <span class="visible-xs-inline-block position-right">New Order</span>
                        <span class="badge bg-warning-400">9</span>
                    </a>

                </li>

            </ul>


            <ul class="nav navbar-nav navbar-right">
                {{--

                <li class="dropdown language-switch">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ URL::to('limitless')}}/assets/images/flags/gb.png" class="position-left" alt="">
                        English
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="deutsch"><img src="{{ URL::to('limitless')}}/assets/images/flags/de.png" alt=""> Deutsch</a></li>
                        <li><a class="ukrainian"><img src="{{ URL::to('limitless')}}/assets/images/flags/ua.png" alt=""> Українська</a></li>
                        <li><a class="english"><img src="{{ URL::to('limitless')}}/assets/images/flags/gb.png" alt=""> English</a></li>
                        <li><a class="espana"><img src="{{ URL::to('limitless')}}/assets/images/flags/es.png" alt=""> España</a></li>
                        <li><a class="russian"><img src="{{ URL::to('limitless')}}/assets/images/flags/ru.png" alt=""> Русский</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-bubbles4"></i>
                        <span class="visible-xs-inline-block position-right">Messages</span>
                        <span class="badge bg-warning-400">2</span>
                    </a>

                    <div class="dropdown-menu dropdown-content width-350">
                        <div class="dropdown-content-heading">
                            Messages
                            <ul class="icons-list">
                                <li><a href="#"><i class="icon-compose"></i></a></li>
                            </ul>
                        </div>

                        <ul class="media-list dropdown-content-body">
                            <li class="media">
                                <div class="media-left">
                                    <img src="{{ URL::to('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
                                    <span class="badge bg-danger-400 media-badge">5</span>
                                </div>

                                <div class="media-body">
                                    <a href="#" class="media-heading">
                                        <span class="text-semibold">James Alexander</span>
                                        <span class="media-annotation pull-right">04:58</span>
                                    </a>

                                    <span class="text-muted">who knows, maybe that would be the best thing for me...</span>
                                </div>
                            </li>

                            <li class="media">
                                <div class="media-left">
                                    <img src="{{ URL::to('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
                                    <span class="badge bg-danger-400 media-badge">4</span>
                                </div>

                                <div class="media-body">
                                    <a href="#" class="media-heading">
                                        <span class="text-semibold">Margo Baker</span>
                                        <span class="media-annotation pull-right">12:16</span>
                                    </a>

                                    <span class="text-muted">That was something he was unable to do because...</span>
                                </div>
                            </li>

                            <li class="media">
                                <div class="media-left"><img src="{{ URL::to('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                <div class="media-body">
                                    <a href="#" class="media-heading">
                                        <span class="text-semibold">Jeremy Victorino</span>
                                        <span class="media-annotation pull-right">22:48</span>
                                    </a>

                                    <span class="text-muted">But that would be extremely strained and suspicious...</span>
                                </div>
                            </li>

                            <li class="media">
                                <div class="media-left"><img src="{{ URL::to('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                <div class="media-body">
                                    <a href="#" class="media-heading">
                                        <span class="text-semibold">Beatrix Diaz</span>
                                        <span class="media-annotation pull-right">Tue</span>
                                    </a>

                                    <span class="text-muted">What a strenuous career it is that I've chosen...</span>
                                </div>
                            </li>

                            <li class="media">
                                <div class="media-left"><img src="{{ URL::to('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                <div class="media-body">
                                    <a href="#" class="media-heading">
                                        <span class="text-semibold">Richard Vango</span>
                                        <span class="media-annotation pull-right">Mon</span>
                                    </a>

                                    <span class="text-muted">Other travelling salesmen live a life of luxury...</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dropdown-content-footer">
                            <a href="#" data-popup="tooltip" title="All messages"><i class="icon-menu display-block"></i></a>
                        </div>
                    </div>
                </li>
                --}}

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ Auth::user()->avatar }}" alt="">
                        <span>{{ Auth::user()->fullname }}</span>
                        <i class="caret"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ URL::to('profile')}}"><i class="icon-user-plus"></i> My profile</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ URL::to('profile')}}"><i class="icon-cog5"></i> Account settings</a></li>
                        <li><a href="{{ URL::to('logout')}}"><i class="icon-switch2"></i> Logout</a></li>
                    </ul>

                </li>
            </ul>


        </div>
    </div>
    <!-- /main navbar -->
