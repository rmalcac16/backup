    <!--  BEGIN SIDEBAR  -->
    <div class="sidebar-wrapper sidebar-theme">
            
        <nav id="sidebar">
            <div class="shadow-bottom"></div>

            <ul class="list-unstyled menu-categories" id="accordionExample">

                    <li class="menu {{ ($category_name === 'dashboard') ? 'active' : '' }}">
                        <a href="#dashboard" data-active="{{ ($category_name === 'dashboard') ? 'true' : 'false' }}" data-toggle="collapse" aria-expanded="{{ ($category_name === 'dashboard') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                <span>{{ __('Dashboard') }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled {{ ($category_name === 'dashboard') ? 'show' : '' }}" id="dashboard" data-parent="#accordionExample">
                            <li class="{{ ($page_name === 'analytics') ? 'active' : '' }}">
                                <a href="{{ route('admin.index') }}"> {{ __('Analytics') }} </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="menu {{ ($category_name === 'animes') ? 'active' : '' }}">
                        <a href="#animes" data-active="{{ ($category_name === 'animes') ? 'true' : 'false' }}" data-toggle="collapse" aria-expanded="{{ ($category_name === 'animes') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tv"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                                <span>{{ __('Animes') }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled {{ ($category_name === 'animes') ? 'show' : '' }}" id="animes" data-parent="#accordionExample">
                            <li class="{{ ($page_name === 'list') ? 'active' : '' }}">
                                <a href="{{ route('admin.animes.index') }}">{{ __('List') }}</a>
                            </li>
                            <li class="{{ ($page_name === 'listLatino') ? 'active' : '' }}">
                                <a href="{{ route('admin.animes.indexLatino') }}">{{ __('Latino') }}</a>
                            </li>
                            <li class="{{ ($page_name === 'create') ? 'active' : '' }}">
                                <a href="{{ route('admin.animes.create') }}">{{ __('Añadir') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu {{ ($category_name === 'servers') ? 'active' : '' }}">
                        <a href="#servers" data-active="{{ ($category_name === 'servers') ? 'true' : 'false' }}" data-toggle="collapse" aria-expanded="{{ ($category_name === 'servers') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                <span>{{ __('Servers') }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled {{ ($category_name === 'servers') ? 'show' : '' }}" id="servers" data-parent="#accordionExample">
                            <li class="{{ ($page_name === 'list') ? 'active' : '' }}">
                                <a href="{{ route('admin.servers.index') }}">{{ __('List') }}</a>
                            </li>
                            <li class="{{ ($page_name === 'create') ? 'active' : '' }}">
                                <a href="{{ route('admin.servers.create') }}">{{ __('Añadir') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu {{ ($category_name === 'genres') ? 'active' : '' }}">
                        <a href="#genres" data-active="{{ ($category_name === 'genres') ? 'true' : 'false' }}" data-toggle="collapse" aria-expanded="{{ ($category_name === 'genres') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                <span>{{ __('Genres') }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled {{ ($category_name === 'genres') ? 'show' : '' }}" id="genres" data-parent="#accordionExample">
                            <li class="{{ ($page_name === 'list') ? 'active' : '' }}">
                                <a href="{{ route('admin.genres.index') }}">{{ __('List') }}</a>
                            </li>
                            <li class="{{ ($page_name === 'create') ? 'active' : '' }}">
                                <a href="{{ route('admin.genres.create') }}">{{ __('Añadir') }}</a>
                            </li>
                        </ul>
                    </li>

            </ul>
            
        </nav>

    </div>
    <!--  END SIDEBAR  -->