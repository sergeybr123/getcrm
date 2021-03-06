@extends('layouts.app')

@section('title', __('Новые автоЧаты'))

@section('content')
    <h1>{{ __('Авточаты') }} @if($link){{ '('.$bots->total().')' }}@endif</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="card-body">
                <div class="px-3 mb-3">
                    <form>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3 pl-0">
                                        <select class="form-control" name="type">
                                            <option value="1" {{ $type == 1 ? 'selected' : '' }}>по ссылке</option>
                                            <option value="2" {{ $type == 2 ? 'selected' : '' }}>по email пользователя</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" name="text" type="text" placeholder="{{ __('Введите для поиска') }}" value="{{ $text }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-info btn-block" type="submit">{{ __('Поиск') }}</button>
                            </div>
                            <div class="col-md-1 pr-0">
                                <a href="{{ route('manager.bots.bot') }}" class="btn btn-warning btn-block" title="{{ __('Очистить поиск') }}"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            <ul class="list-group mb-3">
                @forelse($bots as $key => $bot)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">https://getchat.me/{{ $bot->company->slug }}</h5>
                            <span id="bot_link_{{ $key }}" style="display: none;">https://getchat.me/{{ $bot->company->slug }}</span>
                            <small class="text-muted" title="Дата создания">
                                @if(!is_null($bot->created_at))
                                    {{ \Carbon\Carbon::parse($bot->created_at)->format('d.m.Y') }}
                                @endif
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                #{{ $bot->id }}
                                <strong>{{ __('pages.owner') }}: </strong>
                                <a href="{{ route('manager.users.show', ['id' => $bot->company->owner->id]) }}">{{ $bot->company->owner->email }}</a>
                            </p>
                            <div class="form-inline">
                                {{--<a href="https://getchat.me/constructor2/{{ $botn->id }}" target="_blank" class="btn btn-circle btn-sm btn-outline-blue">--}}
                                    {{--<i class="fa fa-wrench"></i>--}}
                                {{--</a>--}}
                                <div class="dropdown">
                                    <button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="width:30px;height:30px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="https://getchat.me/{{ $bot->company->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                        {{--<button class="dropdown-item" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-exchange-alt"></i> {{ __('buttons.change_owner') }}</button>--}}
                                        <a class="dropdown-item" href="#" onclick="copyBotToClipboard({{ $key }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Авточаты отсутствуют</li>
                @endforelse
            </ul>
            @if($link)
            <div class="px-3">
                {{ $bots->links() }}
            </div>
            @endif
        </div>
    </div>



    {{--<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
        {{--<div class="modal-dialog" role="document">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<h5 class="modal-title" id="exampleModalLabel">{{ __('pages.change_owner') }}</h5>--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                        {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--...--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection
@section('scripts')
    <script>
        function copyBotToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#bot_link_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }
        function copyPageToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#page_slug_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }
    </script>
@endsection