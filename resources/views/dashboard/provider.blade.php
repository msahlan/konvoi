@extends('layouts.limitless')

@section('content')

                <!-- Dashboard content -->
                <div class="row">
                    <div class="col-lg-8">

                        <!-- Marketing campaigns -->
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h5 class="panel-title">Payment</h5>
                                <div class="heading-elements">
                                    <span class="label bg-success heading-text">2 upcoming visit</span>
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i> <span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="#"><i class="icon-sync"></i> Update data</a></li>
                                                <li><a href="#"><i class="icon-list-unordered"></i> Detailed log</a></li>
                                                <li><a href="#"><i class="icon-pie5"></i> Statistics</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#"><i class="icon-cross3"></i> Clear list</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-lg text-nowrap">
                                    <tbody>
                                        <tr>
                                            <td class="col-md-5">
                                                <div class="media-left">
                                                    <div id="campaigns-donut"></div>
                                                </div>

                                                <div class="media-left">
                                                    <h5 class="text-semibold no-margin">38,289 <small class="text-success text-size-base"><i class="icon-arrow-up12"></i> (+16.2%)</small></h5>
                                                    <ul class="list-inline list-inline-condensed no-margin">
                                                        <li>
                                                            <span class="status-mark border-success"></span>
                                                        </li>
                                                        <li>
                                                            <span class="text-muted">May 12, 12:30 am</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            <td class="col-md-5">
                                                <div class="media-left">
                                                    <div id="campaign-status-pie"></div>
                                                </div>

                                                <div class="media-left">
                                                    <h5 class="text-semibold no-margin">2,458 <small class="text-danger text-size-base"><i class="icon-arrow-down12"></i> (- 4.9%)</small></h5>
                                                    <ul class="list-inline list-inline-condensed no-margin">
                                                        <li>
                                                            <span class="status-mark border-danger"></span>
                                                        </li>
                                                        <li>
                                                            <span class="text-muted">Jun 4, 4:00 am</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            <td class="text-right col-md-2">
                                                <a href="#" class="btn bg-indigo-300"><i class="icon-statistics position-left"></i> View report</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th class="col-md-2">JC Visit Date</th>
                                            <th class="col-md-2">Due Date</th>
                                            <th class="col-md-2">Value</th>
                                            <th class="col-md-2">Payment Status</th>
                                            <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="active border-double">
                                            <td colspan="5">Upcoming JC Visit</td>
                                            <td class="text-right">
                                                <span class="progress-meter" id="today-progress" data-progress="30"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/facebook.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Facebook</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-blue position-left"></span>
                                                        02:00 - 03:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Mintlime</span></td>
                                            <td><span class="text-success-600"><i class="icon-stats-growth2 position-left"></i> 2.43%</span></td>
                                            <td><h6 class="text-semibold">$5,489</h6></td>
                                            <td><span class="label bg-blue">Active</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/youtube.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Youtube videos</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-danger position-left"></span>
                                                        13:00 - 14:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">CDsoft</span></td>
                                            <td><span class="text-success-600"><i class="icon-stats-growth2 position-left"></i> 3.12%</span></td>
                                            <td><h6 class="text-semibold">$2,592</h6></td>
                                            <td><span class="label bg-danger">Closed</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/spotify.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Spotify ads</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-grey-400 position-left"></span>
                                                        10:00 - 11:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Diligence</span></td>
                                            <td><span class="text-danger"><i class="icon-stats-decline2 position-left"></i> - 8.02%</span></td>
                                            <td><h6 class="text-semibold">$1,268</h6></td>
                                            <td><span class="label bg-grey-400">Hold</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/twitter.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Twitter ads</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-grey-400 position-left"></span>
                                                        04:00 - 05:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Deluxe</span></td>
                                            <td><span class="text-success-600"><i class="icon-stats-growth2 position-left"></i> 2.78%</span></td>
                                            <td><h6 class="text-semibold">$7,467</h6></td>
                                            <td><span class="label bg-grey-400">Hold</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>

                                        <tr class="active border-double">
                                            <td colspan="5">Yesterday</td>
                                            <td class="text-right">
                                                <span class="progress-meter" id="yesterday-progress" data-progress="65"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/bing.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Bing campaign</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-success position-left"></span>
                                                        15:00 - 16:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Metrics</span></td>
                                            <td><span class="text-danger"><i class="icon-stats-decline2 position-left"></i> - 5.78%</span></td>
                                            <td><h6 class="text-semibold">$970</h6></td>
                                            <td><span class="label bg-success-400">Pending</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropup">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/amazon.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Amazon ads</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-danger position-left"></span>
                                                        18:00 - 19:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Blueish</span></td>
                                            <td><span class="text-success-600"><i class="icon-stats-growth2 position-left"></i> 6.79%</span></td>
                                            <td><h6 class="text-semibold">$1,540</h6></td>
                                            <td><span class="label bg-blue">Active</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropup">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#"><img src="assets/images/brands/dribbble.png" class="img-circle img-xs" alt=""></a>
                                                </div>
                                                <div class="media-left">
                                                    <div class=""><a href="#" class="text-default text-semibold">Dribbble ads</a></div>
                                                    <div class="text-muted text-size-small">
                                                        <span class="status-mark border-blue position-left"></span>
                                                        20:00 - 21:00
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted">Teamable</span></td>
                                            <td><span class="text-danger"><i class="icon-stats-decline2 position-left"></i> 9.83%</span></td>
                                            <td><h6 class="text-semibold">$8,350</h6></td>
                                            <td><span class="label bg-danger">Closed</span></td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropup">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="#"><i class="icon-file-stats"></i> View statement</a></li>
                                                            <li><a href="#"><i class="icon-file-text2"></i> Edit campaign</a></li>
                                                            <li><a href="#"><i class="icon-file-locked"></i> Disable campaign</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"><i class="icon-gear"></i> Settings</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /marketing campaigns -->

                    </div>

                    <div class="col-lg-4">

                        <!-- Daily sales -->
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h5 class="panel-title">Accounts</h5>
                                <div class="heading-elements">
                                    <span class="heading-text">Balance: <span class="text-bold text-danger-600 position-right">$4,378</span></span>
                                    <ul class="icons-list">
                                        <li class="dropdown text-muted">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-cog3"></i> <span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="#"><i class="icon-sync"></i> Update data</a></li>
                                                <li><a href="#"><i class="icon-list-unordered"></i> Detailed log</a></li>
                                                <li><a href="#"><i class="icon-pie5"></i> Statistics</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#"><i class="icon-cross3"></i> Clear list</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Contract #</th>
                                            <th>Creditor</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#" class="btn bg-primary-400 btn-rounded btn-icon btn-xs">
                                                        <span class="letter-icon"></span>
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="letter-icon-title">Sigma application</a>
                                                    </div>

                                                    <div class="text-muted text-size-small"><i class="icon-checkmark3 text-size-mini position-left"></i> New order</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted text-size-small">06:28 pm</span>
                                            </td>
                                            <td>
                                                <h6 class="text-semibold no-margin">$49.90</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#" class="btn bg-danger-400 btn-rounded btn-icon btn-xs">
                                                        <span class="letter-icon"></span>
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="letter-icon-title">Alpha application</a>
                                                    </div>

                                                    <div class="text-muted text-size-small"><i class="icon-spinner11 text-size-mini position-left"></i> Renewal</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted text-size-small">04:52 pm</span>
                                            </td>
                                            <td>
                                                <h6 class="text-semibold no-margin">$90.50</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#" class="btn bg-indigo-400 btn-rounded btn-icon btn-xs">
                                                        <span class="letter-icon"></span>
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="letter-icon-title">Delta application</a>
                                                    </div>

                                                    <div class="text-muted text-size-small"><i class="icon-lifebuoy text-size-mini position-left"></i> Support</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted text-size-small">01:26 pm</span>
                                            </td>
                                            <td>
                                                <h6 class="text-semibold no-margin">$60.00</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#" class="btn bg-success-400 btn-rounded btn-icon btn-xs">
                                                        <span class="letter-icon"></span>
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="letter-icon-title">Omega application</a>
                                                    </div>

                                                    <div class="text-muted text-size-small"><i class="icon-lifebuoy text-size-mini position-left"></i> Support</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted text-size-small">11:46 am</span>
                                            </td>
                                            <td>
                                                <h6 class="text-semibold no-margin">$55.00</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="media-left media-middle">
                                                    <a href="#" class="btn bg-danger-400 btn-rounded btn-icon btn-xs">
                                                        <span class="letter-icon"></span>
                                                    </a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="letter-icon-title">Alpha application</a>
                                                    </div>

                                                    <div class="text-muted text-size-small"><i class="icon-spinner11 text-size-mini position-left"></i> Renewal</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted text-size-small">10:29 am</span>
                                            </td>
                                            <td>
                                                <h6 class="text-semibold no-margin">$90.50</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /daily sales -->

                    </div>
                </div>
                <!-- /dashboard content -->


@endsection