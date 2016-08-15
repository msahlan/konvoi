    <!-- Second navbar -->
    <div class="navbar navbar-default" id="navbar-second">
        <ul class="nav navbar-nav no-border visible-xs-block">
            <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
        </ul>

        <div class="navbar-collapse collapse" id="navbar-second-toggle">
            <ul class="nav navbar-nav navbar-nav-material">
                <li><a href="{{ URL::to('/')}}"><i class="icon-display4 position-left"></i> Dashboard</a></li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-stack2 position-left"></i> Orders <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                           <li class="{{ sa('incoming') }}" >
                                <a href="{{ URL::to('incoming') }}"><i class="icon-inbox-alt"></i> Incoming Order</a>
                            </li>
                            <li class="{{ sa('zoning') }}" >
                                <a href="{{ URL::to('zoning') }}"><i class="icon-map"></i> Device Zone Assignment</a>
                            </li>
                            <li class="{{ sa('courierassign') }}" >
                                <a href="{{ URL::to('courierassign') }}"><i class="icon-person"></i> Courier Assignment</a>
                            </li>
                            <li class="{{ sa('dispatched') }}" >
                                <a href="{{ URL::to('dispatched') }}"><i class="icon-paperplane"></i> In Progress</a>
                            </li>

                        <li class="dropdown-header">Archives</li>
                            <li class="{{ sa('delivered') }}" ><a href="{{ URL::to('delivered') }}"><i class="icon-clippy"></i> Delivery Status</a></li>

                            <li class="{{ sa('canceled') }}" ><a href="{{ URL::to('canceled') }}"><i class="icon-stack-cancel"></i> Canceled</a></li>

                            <li class="{{ sa('orderarchive') }}" >
                                <a href="{{ URL::to('orderarchive') }}"><i class="icon-archive"></i> Order Archive</a>
                            </li>

                            <li>
                                <a href="{{ URL::to('deliverylog') }}"><i class="icon-database-time2"></i> Delivery Log</a>
                            </li>

                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cube3 position-left"></i> Assets <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li>
                            <a href="{{ URL::to('device') }}"><i class="icon-android"></i> Devices</a>
                        </li>
                        <li>
                            <a href="{{ URL::to('parsedevice') }}"><i class="icon-arrow-right16"></i> Parse Devices</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-strategy position-left"></i> Reports <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li class="{{ sa('devmanifest') }}" ><a href="{{ URL::to('devmanifest') }}"> Manifest</a></li>
                        <li class="{{ sa('deliverytime') }}" ><a href="{{ URL::to('deliverytime') }}"> Delivery Time</a></li>
                        <li class="{{ sa('deliverybydate') }}" ><a href="{{ URL::to('deliverybydate') }}"> Delivery By Date</a></li>
                        <li class="{{ sa('deliveryreport') }}" ><a href="{{ URL::to('deliveryreport') }}"> Delivery Report</a></li>
                        <li class="{{ sa('devicerecon') }}" ><a href="{{ URL::to('devicerecon') }}"> Device Reconciliation</a></li>
                        <li class="{{ sa('devicerecondetail') }}" ><a href="{{ URL::to('devicerecondetail') }}"> Device Reconciliation Detail</a></li>
                        <li class="{{ sa('cashier') }}" ><a href="{{ URL::to('cashier') }}"> Cashier</a></li>
                        <li class="{{ sa('datatool') }}" ><a href="{{ URL::to('datatool') }}"> Data Tool</a></li>

                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-strategy position-left"></i> Location <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li class="{{ sa('route') }}" ><a href="{{ URL::to('route') }}"> Routing</a></li>
                        <li class="{{ sa('locationlog') }}" ><a href="{{ URL::to('locationlog') }}"> Location Log</a></li>

                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-strategy position-left"></i> Logs <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li class="{{ sa('orderlog') }}" ><a href="{{ URL::to('orderlog') }}"> Order Log</a></li>
                        <li class="{{ sa('notelog') }}" ><a href="{{ URL::to('notelog') }}"> Note Log</a></li>
                        <li class="{{ sa('photolog') }}" ><a href="{{ URL::to('photolog') }}"> Photo Log</a></li>

                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-strategy position-left"></i> Released Documents <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li class="{{ sa('docs') }}" ><a href="{{ URL::to('docs') }}"> Manifests</a></li>
                    </ul>
                </li>


                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cogs position-left"></i> System <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li class="{{ sa('user') }}" >
                          <a href="{{ URL::to('user') }}" class="{{ sa('user') }}" ><span class="fa fa-group"></span>
                           Users</a>
                        </li>
                        <li class="{{ sa('usergroup') }}">
                          <a href="{{ URL::to('usergroup') }}" class="{{ sa('usergroup') }}" ><span class="fa fa-group"></span>
                           Roles</a>
                        </li>
                        <li class="{{ sa('holiday') }}"><a href="{{ URL::to('holiday') }}"><span class="fa fa-calendar"></span> Holidays</a></li>
                        <li class="{{ sa('option') }}">
                          <a href="{{ URL::to('option') }}" class="{{ sa('option') }}" ><span class="fa fa-wrench"></span>
                           Options</a>
                        </li>
                    </ul>
                </li>

            </ul>

        </div>
    </div>
    <!-- /second navbar -->
