            <nav id="main_menu">
                <div class="menu_wrapper">
                      <ul>
                      </ul>


                    <ul>
                        <li class="first_level">
                            <a href="{{ URL::to('/') }}">
                                <span class="icon_house_alt first_level_icon"></span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="first_level">
                            <a href="{{ URL::to('incoming') }}">
                                <span class="fa fa-paper-plane"></span>
                                <span>Shipment Order</span> <span class="fa arrow"></span>
                            </a>
                          <ul>
                            <li>
                                <a href="{{ URL::to('incoming') }}"> Incoming Order</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('canceled') }}"> Canceled Order</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('zoning') }}"> Device Zone Assignment</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('courierassign') }}"> Courier Assignment</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('dispatched') }}"> In Progress</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('delivered') }}"> Delivery Status</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('deliverylog') }}"> Delivery Log</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('orderarchive') }}"> Order Archive</a>
                            </li>
                          </ul>
                        </li>
                        <li class="first_level">
                          <a href="#"><span class="fa fa-cog"></span>
                            <span>Assets</span> <span class="fa arrow"></span></a>
                          <ul>
                            <li>
                                <a href="{{ URL::to('device') }}"> Devices</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('parsedevice') }}"> Parse Devices</a>
                            </li>
                          </ul>
                        </li>
                        <li class="first_level {{ hsa( array('manifest','devmanifest') ) }} ">
                          <a href=""><span class="fa fa-table"></span>
                            <span>Reports</span><span class="fa arrow"></span></a>
                          <ul>
                            {{--
                            <li class="{{ sa('manifest') }}" ><a href="{{ URL::to('manifest') }}"> Manifest To Hub</a></li>
                            --}}
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
                        <li class="first_level {{ hsa( array('docs') ) }} ">
                          <a href=""><span class="fa fa-table"></span>
                            <span>Released Documents</span><span class="fa arrow"></span></a>
                          <ul>
                            <li class="{{ sa('docs') }}" ><a href="{{ URL::to('docs') }}"> Manifests</a></li>
                          </ul>
                        </li>
                        <li class="first_level {{ hsa( array('logs') ) }} ">
                          <a href=""><span class="fa fa-table"></span>
                            <span>Location</span><span class="fa arrow"></span></a>
                          <ul>
                            <li class="{{ sa('route') }}" ><a href="{{ URL::to('route') }}"> Routing</a></li>
                            <li class="{{ sa('locationlog') }}" ><a href="{{ URL::to('locationlog') }}"> Location Log</a></li>
                          </ul>
                        </li>
                        <li class="first_level {{ hsa( array('logs') ) }} ">
                          <a href=""><span class="fa fa-table"></span>
                            <span>Logs</span><span class="fa arrow"></span></a>
                          <ul>
                            <li class="{{ sa('orderlog') }}" ><a href="{{ URL::to('orderlog') }}"> Order Log</a></li>
                            <li class="{{ sa('notelog') }}" ><a href="{{ URL::to('notelog') }}"> Note Log</a></li>
                            <li class="{{ sa('photolog') }}" ><a href="{{ URL::to('photolog') }}"> Photo Log</a></li>
                          </ul>
                        </li>
                        <li class="first_level">
                          <a href=""><span class="fa fa-cogs"></span>
                            <span>System </span><span class="fa arrow"></span></a>

                            <ul>
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

                        {{--
                        <li class="first_level">
                            <a href="dashboard.html">
                                <span class="icon_house_alt first_level_icon"></span>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="first_level">
                            <a href="javascript:void(0)">
                                <span class="icon_document_alt first_level_icon"></span>
                                <span class="menu-title">Forms</span>
                            </a>
                            <ul>
                                <li class="submenu-title">Forms</li>
                                <li><a href="forms-regular_elements.html">Regular Elements</a></li>
                                <li><a href="forms-extended_elements.html">Extended Elements</a></li>
                                <li><a href="forms-gridforms.html">Gridforms</a></li>
                                <li><a href="forms-validation.html">Validation</a></li>
                                <li><a href="forms-wizard.html">Wizard</a></li>
                            </ul>
                        </li>
                        <li class="first_level">
                            <a href="javascript:void(0)">
                                <span class="icon_folder-alt first_level_icon"></span>
                                <span class="menu-title">Pages</span>
                                <span class="label label-danger">12</span>
                            </a>
                            <ul>
                                <li class="submenu-title">Pages</li>
                                <li><a href="pages-chat.html">Chat</a></li>
                                <li><a href="pages-contact_list.html">Contact List</a></li>
                                <li><a href="error_404.html">Error 404</a></li>
                                <li><a href="pages-help_faq.html">Help/Faq</a></li>
                                <li><a href="pages-invoices.html">Invoices</a></li>
                                <li><a href="login_page.html">Login Page</a></li>
                                <li><a href="login_page2.html">Login Page 2</a></li>
                                <li><a href="pages-mailbox.html">Mailbox</a></li>
                                <li><a href="pages-mailbox_compose.html">Mailbox (compose)</a></li>
                                <li><a href="pages-mailbox_message.html">Mailbox (details)</a></li>
                                <li><a href="pages-search_page.html">Search Page</a></li>
                                <li><a href="pages-user_list.html">User List</a></li>
                                <li><a href="pages-user_profile.html">User Profile</a></li>
                                <li><a href="pages-user_profile2.html">User Profile 2</a></li>
                            </ul>
                        </li>
                        <li class="first_level">
                            <a href="javascript:void(0)">
                                <span class="icon_puzzle first_level_icon"></span>
                                <span class="menu-title">Components</span>
                            </a>
                            <ul>
                                <li class="submenu-title">Components</li>
                                <li><a href="components-bootstrap.html">Bootstrap</a></li>
                                <li><a href="components-gallery.html">Gallery</a></li>
                                <li><a href="components-grid.html">Grid</a></li>
                                <li><a href="components-icons.html">Icons</a></li>
                                <li><a href="components-notifications_popups.html">Notifications/Popups</a></li>
                                <li><a href="components-typography.html">Typography</a></li>
                            </ul>
                        </li>
                        <li class="first_level">
                            <a href="javascript:void(0)">
                                <span class="icon_lightbulb_alt first_level_icon"></span>
                                <span class="menu-title">Plugins</span>
                                <span class="label label-danger">6</span>
                            </a>
                            <ul>
                                <li class="submenu-title">Plugins</li>
                                <li><a href="plugins-ace_editor.html">Ace Editor</a></li>
                                <li><a href="plugins-calendar.html">Calendar</a></li>
                                <li><a href="plugins-charts.html">Charts</a></li>
                                <li><a href="plugins-gantt_chart.html">Gantt Chart</a></li>
                                <li><a href="plugins-google_maps.html">Google Maps</a></li>
                                <li><a class="act_nav" href="plugins-tables_footable.html">Tables</a></li>
                                <li><a href="plugins-vector_maps.html">Vector Maps</a></li>
                            </ul>
                        </li>
                        <li class="first_level has_submenu">
                            <a href="javascript:void(0)">
                                <span class="first_level_icon icon_menu-circle_alt2"></span>
                                <span class="menu-title">Sub menu</span>
                            </a>
                            <ul>
                                <li class="submenu-title">Sub menu</li>
                                <li><a href="javascript:void(0)">01. Lorem ipsum</a></li>
                                <li class="has_submenu">
                                    <a href="javascript:void(0)">02. Lorem ipsum</a>
                                    <ul>
                                        <li class="has_submenu">
                                            <a href="javascript:void(0)">02.1 Lorem ipsum dolor sit amet</a>
                                            <ul>
                                                <li><a href="javascript:void(0)">02.1.1 Lorem ipsum</a></li>
                                                <li><a href="javascript:void(0)">02.1.2 Lorem ipsum</a></li>
                                                <li><a href="javascript:void(0)">02.1.3 Lorem ipsum</a></li>
                                                <li><a href="javascript:void(0)">02.1.4 Lorem ipsum</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="javascript:void(0)">02.2 Lorem ipsum</a></li>
                                        <li><a href="javascript:void(0)">02.3 Lorem ipsum</a></li>
                                        <li><a href="javascript:void(0)">02.4 Lorem ipsum</a></li>
                                    </ul>
                                </li>
                                <li class="has_submenu">
                                    <a href="javascript:void(0)">03. Lorem ipsum</a>
                                    <ul>
                                        <li><a href="javascript:void(0)">03.1 Lorem ipsum</a></li>
                                        <li><a href="javascript:void(0)">03.2 Lorem ipsum</a></li>
                                        <li><a href="javascript:void(0)">03.3 Lorem ipsum</a></li>
                                        <li><a href="javascript:void(0)">03.4 Lorem ipsum</a></li>
                                    </ul>
                                </li>
                                <li><a href="javascript:void(0)">04. Lorem ipsum</a></li>
                            </ul>
                        </li>
                        --}}

                    </ul>


                </div>
                <div class="menu_toggle">
                    <span class="icon_menu_toggle">
                        <i class="arrow_carrot-2left toggle_left"></i>
                        <i class="arrow_carrot-2right toggle_right" style="display:none"></i>
                    </span>
                </div>
            </nav>
