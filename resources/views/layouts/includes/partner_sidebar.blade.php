<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('partner::index') }}">
                    <i><i class="fa fa-address-card"></i></i> {{ __('Мои данные') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('partner::bots::index') }}">
                    <i><i class="fa fa-comments"></i></i> {{ __('Мои авточаты') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('partner::invoices::index') }}">
                    <i><i class="fa fa-file-invoice"></i></i> {{ __('Мои счета') }}
                </a>
            </li>
        </ul>
    </nav>
    <div class="text-center py-3" style="background-color:#36a9e1;">
        <span>GETCHAT.ME © 2016-{{ date('Y') }}</span>
    </div>
</div>