        <!-- Floating menu -->
        <ul class="fab-menu fab-menu-top-right" data-fab-toggle="click">
            <li>
                <a class="fab-menu-btn btn bg-pink-300 btn-float btn-rounded btn-icon">
                    <i class="fab-icon-open icon-plus3"></i>
                    <i class="fab-icon-close icon-cross2"></i>
                </a>

                <ul class="fab-menu-inner">
                    <li>
                        <div data-fab-label="Create Order">
                            <a href="{{ url('incoming/add')}}" class="btn btn-default btn-rounded btn-icon btn-float">
                                <i class="icon-pencil"></i>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div data-fab-label="Upload Excel">
                            <a href="{{ url('incoming/import')}}" class="btn btn-default btn-rounded btn-icon btn-float">
                                <i class="fa fa-upload"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- /floating menu -->
