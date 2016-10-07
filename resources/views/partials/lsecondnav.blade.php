<?php
    $menu = env('MENU_SET','LOGISTIC');
    use App\Helpers\Ks;
?>
       <!-- Second navbar -->
        <div class="navbar navbar-inverse navbar-transparent" id="navbar-second">
            <ul class="nav navbar-nav visible-xs-block">
                <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-paragraph-justify3"></i></a></li>
            </ul>

            <div class="navbar-collapse collapse" id="navbar-second-toggle">
            <ul class="nav navbar-nav navbar-nav-material">
                @if( Ks::is('Member') || Ks::is('Employee') || Ks::is('Manager') )
                    <li><a href="{{ url('member')}}"><i class="icon-display4 position-left"></i> Dashboard</a></li>
                @elseif(Ks::is('Creditor'))
                    <li><a href="{{ url('creditor')}}"><i class="icon-display4 position-left"></i> Dashboard</a></li>
                @elseif(Ks::is('Admin') || Ks::is('Superuser') || Ks::is('Administrator') || Ks::is('Root'))
                    <li><a href="{{ url('/')}}"><i class="icon-display4 position-left"></i> Dashboard</a></li>

                @endif

                @if($menu == 'DOCUMENT' || $menu == 'ASSET' || $menu == 'FINANCE')
                <li><a href="{{ url('docs')}}"><i class="icon-archive position-left"></i> Documents</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cube3 position-left"></i> Finance <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li>
                            <a href="{{ url('invoice') }}"><i class="icon-android"></i> Invoices</a>
                        </li>
                        <li>
                            <a href="{{ url('receipt') }}"><i class="icon-money"></i> Receipts</a>
                        </li>
                        <li>
                            <a href="{{ url('payment') }}"><i class="icon-arrow-right16"></i> Payment Vouchers</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cube3 position-left"></i> Rolodex <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li>
                            <a href="{{ url('companydex') }}"><i class="icon-android"></i> Companies</a>
                        </li>
                        <li>
                            <a href="{{ url('persondex') }}"><i class="icon-money"></i> Persons</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($menu == 'ASSET' || $menu == 'FINANCE')

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-cube3 position-left"></i> Assets <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu width-200">
                        <li>
                            <a href="{{ url('asset') }}"><i class="icon-android"></i> Asset List</a>
                        </li>
                        <li>
                            <a href="{{ url('assetlocation') }}"><i class="icon-arrow-right16"></i> Asset Location</a>
                        </li>
                        <li>
                            <a href="{{ url('assetlocation') }}"><i class="icon-arrow-right16"></i> Asset Transaction</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if($menu == 'LOGISTIC')

                    @if( Ks::is('Member') )
                        <li><a href="{{ url('member/account')}}"><i class="icon-display4 position-left"></i> Accounts</a></li>

                        <li><a href="{{ url('member/transaction')}}"><i class="icon-display4 position-left"></i> Transactions</a></li>

                    @elseif( Ks::is('Creditor') )
                        <li><a href="{{ url('creditor/account')}}"><i class="icon-display4 position-left"></i> Accounts</a></li>

                        <li><a href="{{ url('creditor/transaction')}}"><i class="icon-display4 position-left"></i> Transactions</a></li>

                    @else

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-stack2 position-left"></i> Orders <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                   <li class="{{ sa('incoming') }}" >
                                        <a href="{{ url('incoming') }}"><i class="icon-inbox-alt"></i> Incoming Order</a>
                                    </li>
                                    <li class="{{ sa('zoning') }}" >
                                        <a href="{{ url('zoning') }}"><i class="icon-map"></i> Device Zone Assignment</a>
                                    </li>
                                    <li class="{{ sa('courierassign') }}" >
                                        <a href="{{ url('courierassign') }}"><i class="icon-person"></i> Courier Assignment</a>
                                    </li>
                                    <li class="{{ sa('dispatched') }}" >
                                        <a href="{{ url('dispatched') }}"><i class="icon-paperplane"></i> In Progress</a>
                                    </li>

                                <li class="dropdown-header">Archives</li>
                                    <li class="{{ sa('delivered') }}" ><a href="{{ url('delivered') }}"><i class="icon-clippy"></i> Delivery Status</a></li>

                                    <li class="{{ sa('canceled') }}" ><a href="{{ url('canceled') }}"><i class="icon-stack-cancel"></i> Canceled</a></li>

                                    <li class="{{ sa('orderarchive') }}" >
                                        <a href="{{ url('orderarchive') }}"><i class="icon-archive"></i> Order Archive</a>
                                    </li>

                                    <li>
                                        <a href="{{ url('deliverylog') }}"><i class="icon-database-time2"></i> Delivery Log</a>
                                    </li>

                            </ul>
                        </li>

                    @endif


                @endif

                    @if( Ks::is('Superuser') || Ks::is('Admin'))
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-cube3 position-left"></i> Op Assets <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                <li>
                                    <a href="{{ url('device') }}"><i class="icon-android"></i> Devices</a>
                                </li>
                                <li>
                                    <a href="{{ url('parsedevice') }}"><i class="icon-arrow-right16"></i> Parse Devices</a>
                                </li>
                                <li>
                                    <a href="{{ url('fcmdevice') }}"><i class="icon-arrow-right16"></i> FCM Devices</a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-strategy position-left"></i> Reports <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                <li class="{{ sa('devmanifest') }}" ><a href="{{ url('devmanifest') }}"> Manifest</a></li>
                                <li class="{{ sa('deliverytime') }}" ><a href="{{ url('deliverytime') }}"> Delivery Time</a></li>
                                <li class="{{ sa('deliverybydate') }}" ><a href="{{ url('deliverybydate') }}"> Delivery By Date</a></li>
                                <li class="{{ sa('deliveryreport') }}" ><a href="{{ url('deliveryreport') }}"> Delivery Report</a></li>
                                <li class="{{ sa('devicerecon') }}" ><a href="{{ url('devicerecon') }}"> Device Reconciliation</a></li>
                                <li class="{{ sa('devicerecondetail') }}" ><a href="{{ url('devicerecondetail') }}"> Device Reconciliation Detail</a></li>
                                <li class="{{ sa('cashier') }}" ><a href="{{ url('cashier') }}"> Cashier</a></li>
                                <li class="{{ sa('datatool') }}" ><a href="{{ url('datatool') }}"> Data Tool</a></li>

                                <li class="dropdown-header">Document Archives</li>
                                <li class="{{ sa('docs') }}" ><a href="{{ url('docs') }}"> Manifests</a></li>


                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-strategy position-left"></i> Location <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                <li class="{{ sa('route') }}" ><a href="{{ url('route') }}"> Routing</a></li>
                                <li class="{{ sa('locationlog') }}" ><a href="{{ url('locationlog') }}"> Location Log</a></li>

                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-strategy position-left"></i> Logs <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                <li class="{{ sa('orderlog') }}" ><a href="{{ url('orderlog') }}"> Order Log</a></li>
                                <li class="{{ sa('notelog') }}" ><a href="{{ url('notelog') }}"> Note Log</a></li>
                                <li class="{{ sa('photolog') }}" ><a href="{{ url('photolog') }}"> Photo Log</a></li>

                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-users position-left"></i> Users <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu width-200">
                                <li>
                                    <a href="{{ url('user') }}"><i class="icon-users"></i> Admins</a>
                                </li>
                                <li>
                                    <a href="{{ url('member') }}"><i class="icon-users"></i> Members</a>
                                </li>
                                <li>
                                    <a href="{{ url('creditor') }}"><i class="icon-users"></i> Creditors</a>
                                </li>
                            </ul>
                        </li>


                    @else

                    @endif

            </ul>

                <ul class="nav navbar-nav navbar-nav-material navbar-right">

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-cog3"></i>
                            <span class="visible-xs-inline-block position-right">Settings</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu width-200 dropdown-menu-right">
                            <li class="{{ sa('organization') }}" >
                              <a href="{{ url('organization') }}" class="{{ sa('organization') }}" ><span class="fa fa-group"></span>
                               Organization</a>
                            </li>
                            <li class="{{ sa('user') }}" >
                              <a href="{{ url('user') }}" class="{{ sa('user') }}" ><span class="fa fa-group"></span>
                               Users</a>
                            </li>
                            <li class="{{ sa('usergroup') }}">
                              <a href="{{ url('usergroup') }}" class="{{ sa('usergroup') }}" ><span class="fa fa-group"></span>
                               Roles</a>
                            </li>
                            <li class="{{ sa('holiday') }}"><a href="{{ url('holiday') }}"><span class="fa fa-calendar"></span> Holidays</a></li>
                            <li class="{{ sa('option') }}">
                              <a href="{{ url('option') }}" class="{{ sa('option') }}" ><span class="fa fa-wrench"></span>
                               Options</a>
                            </li>
                        </ul>

                    </li>
                </ul>
            </div>
        </div>
        <!-- /second navbar -->
