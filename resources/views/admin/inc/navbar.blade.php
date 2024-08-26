    <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">

            <ul class="navbar-item theme-brand flex-row  text-center">
                <li class="nav-item theme-logo">
                    <a href="{{ route('admin.index') }}">
                        <img src="{{asset('assets/img/logo.svg')}}" class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <a href="{{ route('admin.index') }}" class="nav-link"> {{ config('app.name')  }} </a>
                </li>
            </ul>

            <ul class="navbar-item flex-row ml-md-auto">
                
                <li class="nav-item dropdown user-profile-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						{{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="">
                            <div class="dropdown-item">
                                <a href="{{ route('logout') }}" 
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> {{ __('Sign Out') }}</a>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>

            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <div class="sub-header-container">
        <header class="header navbar navbar-expand-sm">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>

            <ul class="navbar-nav flex-row">
                <li>
                    <div class="page-header">

                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                            @if(request()->routeIs('admin.index'))
                                <li class="breadcrumb-item active">@lang('Home')</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.index') }}">@lang('Home')</a>
                                </li>
                                @if(request()->routeIs('admin.animes.*'))
                                    @if(request()->routeIs('admin.animes.index'))
                                        <li class="breadcrumb-item active">@lang('Tv shows')</li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.animes.index') }}">@lang('Tv shows')</a>
                                        </li>
                                        @if(request()->routeIs('admin.animes.create'))
                                            <li class="breadcrumb-item active">@lang('Add')</li>
                                        @endif
                                        @if(request()->routeIs('admin.animes.edit'))
                                            <li class="breadcrumb-item">{{ $anime->name }}</li>
                                            <li class="breadcrumb-item active">@lang('Edit')</li>
                                        @endif
                                        @if(request()->routeIs('admin.animes.episodes.*'))
                                            @if(request()->routeIs('admin.animes.episodes.index'))
                                                <li class="breadcrumb-item active">{{ $anime->name }}</li>
                                            @else
                                                <li class="breadcrumb-item">
                                                    <a href="{{ route('admin.animes.episodes.index',$anime->id) }}">{{ $anime->name }}</a>
                                                </li>
                                                @if(request()->routeIs('admin.animes.episodes.create'))
                                                    <li class="breadcrumb-item active">@lang('Add')</li>
                                                @endif
                                                @if(request()->routeIs('admin.animes.episodes.edit'))
                                                    <li class="breadcrumb-item">{{ $episode->number }}</li>
                                                    <li class="breadcrumb-item active">@lang('Edit')</li>
                                                @endif
                                                @if(request()->routeIs('admin.animes.episodes.generate'))
                                                    <li class="breadcrumb-item active">@lang('Generate episodes')</li>
                                                @endif
                                                @if(request()->routeIs('admin.animes.episodes.generatePlayers'))
                                                    <li class="breadcrumb-item active">@lang('Generate players')</li>
                                                @endif
                                                @if(request()->routeIs('admin.animes.episodes.players.*'))
                                                    @if(request()->routeIs('admin.animes.episodes.players.index'))
                                                        <li class="breadcrumb-item active">{{ $episode->number }}</li>
                                                    @else
                                                        <li class="breadcrumb-item">
                                                            <a href="{{ route('admin.animes.episodes.players.index',[$anime->id,$episode->id]) }}">{{ $episode->number }}</a>
                                                        </li>
                                                        @if(request()->routeIs('admin.animes.episodes.players.create'))
                                                            <li class="breadcrumb-item active">@lang('Add')</li>
                                                        @endif
                                                        @if(request()->routeIs('admin.animes.episodes.players.edit'))
                                                            <li class="breadcrumb-item">{{ $player->server->title }}</li>
                                                            <li class="breadcrumb-item active">@lang('Edit')</li>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @elseif(request()->routeIs('admin.genres.*'))
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.genres.index') }}">@lang('Genres')</a>
                                </li>
                                    @if(request()->routeIs('admin.genres.create'))
                                        <li class="breadcrumb-item active">@lang('Add')</li>
                                    @endif
                                    @if(request()->routeIs('admin.genres.edit'))
                                        <li class="breadcrumb-item">{{ $genre->title }}</li>
                                        <li class="breadcrumb-item active">@lang('Edit')</li>
                                    @endif
                                @elseif(request()->routeIs('admin.servers.*'))
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.servers.index') }}">@lang('Servers')</a>
                                </li>
                                    @if(request()->routeIs('admin.servers.instream'))
                                        <li class="breadcrumb-item active">@lang('Order')</li>
                                    @endif
                                    @if(request()->routeIs('admin.servers.create'))
                                        <li class="breadcrumb-item active">@lang('Add')</li>
                                    @endif
                                    @if(request()->routeIs('admin.servers.edit'))
                                        <li class="breadcrumb-item">{{ $server->title }}</li>
                                        <li class="breadcrumb-item active">@lang('Edit')</li>
                                    @endif
                                @endif
                            @endif
                            </ol>
                        </nav>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->