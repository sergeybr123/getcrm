<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i><i class="fa fa-tachometer-alt"></i></i> @lang('sidebar.dashboard')
                </a>
            </li>
            @role('admin')
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i><i class="fa fa-cogs"></i></i> {{ __('Управление') }}
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin::plans::index') }}">
                            <i><i class="fa fa-clipboard-list"></i></i> {{ __('Тарифные планы') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin::subscribes::index') }}">
                            <i><i class="fa fa-clipboard-list"></i></i> {{ __('Подписки') }}
                        </a>
                    </li>
                </ul>
            </li>
{{--            <li class="nav-item nav-dropdown">--}}
{{--                <a class="nav-link nav-dropdown-toggle" href="#">--}}
{{--                    <i><i class="fa fa-database"></i></i> @lang('sidebar.dictionary')--}}
{{--                </a>--}}
{{--                <ul class="nav-dropdown-items">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="#">--}}
{{--                            <i><i class="fa fa-minus"></i></i> @lang('sidebar.activity')--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}
            @endrole
            @role('admin|manager')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.users.show', ['id' => \Auth::user()->id]) }}">
                    <i><i class="fa fa-file"></i></i> {{ __('Моя страница') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.users.index') }}">
                    <i><i class="fa fa-users"></i></i> @lang('sidebar.users')
                </a>
            </li>
            {{--<li class="nav-item">--}}
                {{--<a class="nav-link" href="{{ route('manager.pages.index') }}">--}}
                    {{--<i><i class="fa fa-copy"></i></i> {{ __('MultiLink') }}--}}
                {{--</a>--}}
            {{--</li>--}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.bots.bot') }}">
                    <i><i class="far fa-comments"></i></i> {{ __('sidebar.bots') }}
                </a>
            </li>
            {{--<li class="nav-item nav-dropdown">--}}
                {{--<a class="nav-link nav-dropdown-toggle" href="#">--}}
                    {{--<i><i class="fa fa-comments"></i></i> {{ __('sidebar.bots') }}--}}
                {{--</a>--}}
                {{--<ul class="nav-dropdown-items">--}}
                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link" href="{{ url('manager/bots/old') }}">--}}
                            {{--<i><i class="far fa-circle"></i></i> {{ __('Старые') }}--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link" href="{{ url('manager/bots/new') }}">--}}
                            {{--<i><i class="far fa-circle"></i></i> {{ __('Новые') }}--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</li>--}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.templates.index') }}">
                    <i><i class="fa fa-copy"></i></i> {{ __('Шаблоны') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.invoices.index') }}">
                    <i><i class="fa fa-file-invoice"></i></i> @lang('sidebar.invoices')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manager.subscribes.index') }}">
                    <i><i class="fa fa-file-alt" aria-hidden="true"></i></i> {{ __('Подписки') }}
                </a>
            </li>
            @endrole
            @role('analyst')
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i><i class="fa fa-signature"></i></i> {{ __('sidebar.analytics') }}
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i><i class="fa fa-clipboard-list"></i></i> {{ __('sidebar.subscriptions') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i><i class="fa fa-donate"></i></i> {{ __('sidebar.income') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i><i class="fa fa-file-invoice"></i></i> {{ __('sidebar.invoices_pay') }}
                        </a>
                    </li>
                </ul>
            </li>
            @endrole
        </ul>
    </nav>
    <div class="text-center py-3" style="background-color:#36a9e1;">
        <span>GETCHAT.ME © 2016-{{ date('Y') }}</span>
    </div>
</div>
