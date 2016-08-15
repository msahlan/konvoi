        <div class="header_user_actions dropdown">
            <div data-toggle="dropdown" class="dropdown-toggle user_dropdown">
                <div class="user_avatar">
                    <img src="{{ Auth::user()->avatar }}" alt="" title="{{ Auth::user()->fullname }}" width="38" height="38">&nbsp;&nbsp;
                    <span class="username" style="color:white;">{{ Auth::user()->fullname }}</span>
                </div>
                <span class="caret"></span>
            </div>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="{{ URL::to('profile')}}">User Profile</a></li>
                <li><a href="{{ URL::to('logout')}}">Logout</a></li>
            </ul>
        </div>
        {{--
        <div class="search_section hidden-sm hidden-xs">
            <input type="text" class="form-control input-sm">
            <button class="btn btn-raised btn-link btn-sm" type="button"><span class="icon_search"></span></button>
        </div>
        --}}
