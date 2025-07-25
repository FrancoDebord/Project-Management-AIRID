 <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-category">Main</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("indexPage") }}">
                            <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("project.index") }}">
                            <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                            <span class="menu-title">Manage Project Info</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("activityPage") }}">
                            <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                            <span class="menu-title">Master schedule</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("scheduleActivityForProject") }}">
                            <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                            <span class="menu-title">Schedule Activity for Project</span>
                        </a>
                    </li>

                    {{-- <li class="nav-item nav-category">UI Features</li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="icon-bg"><i class="mdi mdi-crosshairs-gps menu-icon"></i></span>
                            <span class="menu-title">Basic UI Elements</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="ui-basic">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="../ui-features/buttons.html">Buttons</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="../ui-features/dropdowns.html">Dropdowns</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="../ui-features/typography.html">Typography</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false"
                            aria-controls="icons">
                            <span class="icon-bg"><i class="mdi mdi-contacts menu-icon"></i></span>
                            <span class="menu-title">Icons</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="icons">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="../icons/mdi.html">Material</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#forms" aria-expanded="false"
                            aria-controls="forms">
                            <span class="icon-bg"><i class="mdi mdi-format-list-bulleted menu-icon"></i></span>
                            <span class="menu-title">Forms</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="forms">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="../forms/basic_elements.html">Form
                                        Elements</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item nav-category">Data Representation</li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#charts" aria-expanded="false"
                            aria-controls="charts">
                            <span class="icon-bg"><i class="mdi mdi-chart-bar menu-icon"></i></span>
                            <span class="menu-title">Charts</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="charts">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="../charts/chartjs.html">ChartJs</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#tables" aria-expanded="false"
                            aria-controls="tables">
                            <span class="icon-bg"><i class="mdi mdi-table-large menu-icon"></i></span>
                            <span class="menu-title">Tables</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="tables">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="../tables/basic-table.html">Basic
                                        Table</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item nav-category">Sample Pages</li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false"
                            aria-controls="auth">
                            <span class="icon-bg"><i class="mdi mdi-lock menu-icon"></i></span>
                            <span class="menu-title">User Pages</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="auth">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="blank-page.html"> Blank Page </a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="login.html"> Login </a></li>
                                <li class="nav-item"> <a class="nav-link" href="register.html"> Register </a></li>
                                <li class="nav-item"> <a class="nav-link" href="error-404.html"> 404 </a></li>
                                <li class="nav-item"> <a class="nav-link" href="error-500.html"> 500 </a></li>

                            </ul>
                        </div>
                    </li>
                    <li class="nav-item documentation-link">
                        <a class="nav-link" href="#">
                            <span class="icon-bg">
                                <i class="mdi mdi-file-document menu-icon"></i>
                            </span>
                            <span class="menu-title">Documentation</span>
                        </a>
                    </li>
                    <li class="nav-item sidebar-user-actions">
                        <div class="user-details">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="sidebar-profile-img">
                                            <img src="../../assets/images/faces/face28.png" alt="image">
                                        </div>
                                        <div class="sidebar-profile-text">
                                            <p class="mb-1">Henry Klein</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="badge badge-danger">3</div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item sidebar-user-actions">
                        <div class="sidebar-user-menu">
                            <a href="#" class="nav-link"><i class="mdi mdi-weather-sunny menu-icon"></i>
                                <span class="menu-title">Settings</span>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item sidebar-user-actions">
                        <div class="sidebar-user-menu">
                            <a href="#" class="nav-link"><i class="mdi mdi-speedometer menu-icon"></i>
                                <span class="menu-title">Take Tour</span></a>
                        </div>
                    </li>
                    <li class="nav-item sidebar-user-actions">
                        <div class="sidebar-user-menu">
                            <a href="#" class="nav-link"><i class="mdi mdi-logout menu-icon"></i>
                                <span class="menu-title">Log Out</span></a>
                        </div>
                    </li> --}}
                </ul>
            </nav>