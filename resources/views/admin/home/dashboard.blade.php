@extends('admin.layout')

@section('styles')
    <link href="{{ asset('assets/css/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/loader.js')}}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('plugins/apex/apexcharts.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/dashboard/dash_1.css')}}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
@endsection

@section('content')

    <div class="layout-px-spacing" style="width: 100%">

        <div class="row layout-top-spacing" >

            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">

                    <div class="widget-heading">
                        <h5 class="">{{ __('Most viewed episodes') }}</h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><div class="th-content">{{ __('Title') }}</div></th>
                                        <th><div class="th-content">{{ __('Created at') }}</div></th>
                                        <th><div class="th-content">{{ __('Views') }}</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($episodes as $episode)
                                    <tr>
                                        <td><div class="td-content">{{ $episode->anime->name.' '.$episode->number }}</div></td>
                                        <td><div class="td-content">{{ date('d-M-Y', strtotime($episode->created_at)) }}</div></td>
                                        <td><div class="td-content">{{ $episode->views_app.' '.__('Views') }}</div></td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

           <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">

                    <div class="widget-heading">
                        <h5 class="">{{ __('Recent animes') }}</h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><div class="th-content">{{ __('Title') }}</div></th>
                                        <th><div class="th-content">{{ __('Title original') }}</div></th>
                                        <th><div class="th-content">{{ __('Created at') }}</div></th>
                                        <th><div class="th-content">{{ __('Status') }}</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animes as $anime)
                                    <tr>
                                        <td><div class="td-content customer-name">{{ mb_strimwidth($anime->name,0,25,'...') }}</div></td>
                                        <td><div class="td-content">{{ mb_strimwidth($anime->name_alternative,0,25,'...') }}</div></td>
                                        <td><div class="td-content">{{ date('d-M-Y', strtotime($anime->created_at)) }}</div></td>
                                        @if($anime->status === 1)
                                        <td><div class="td-content"><span class="badge outline-badge-success">{{__('Aired')}}</span></div></td>
                                        @else
                                        <td><div class="td-content"><span class="badge outline-badge-danger">{{__('Finished')}}</span></div></td>
                                        @endif
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    
@endsection