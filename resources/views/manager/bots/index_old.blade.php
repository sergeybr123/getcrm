@extends('layouts.app')

@section('title', __('users.users'))

@section('content')
    <h1>{{ __('Авточаты старые') }} ({{ $bots_old->total() }})</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="px-3 mb-3">
                <form>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3 pl-0">
                                    <select class="form-control" name="type">
                                        <option value="1" {{ $type == 1 ? 'selected' : '' }}>по ссылке</option>
                                        <option value="2" {{ $type == 2 ? 'selected' : '' }}>по email пользователя</option>
                                        {{--<option value="3">по дате регистрации</option>--}}
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control" name="text" type="text" placeholder="{{ __('Введите для поиска') }}" value="{{ $text }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 pr-0">
                            <button class="btn btn-outline-info btn-block" type="submit">{{ __('Поиск') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group mb-3">
                @forelse($bots_old as $key => $boto)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $boto->slug }}</h5>
                            <span id="page_slug_{{ $boto->id }}" style="display: none;">https://getchat.me/{{ $boto->slug }}</span>
                            <small class="text-muted" title="Дата создания">
                                {{ \Carbon\Carbon::parse($boto->created_at)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                <strong>{{ __('pages.owner') }}: </strong>
                                <a href="{{ route('manager.users.show', ['id' => $boto->owner->id]) }}">{{ $boto->owner->email }}</a>
                            </p>
                            <div class="form-inline">
                                <a href="https://getchat.me/constructor/{{ $boto->id }}" class="btn btn-circle btn-sm btn-outline-blue" target="_blank">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="width:30px;height:30px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="https://getchat.me/{{ $boto->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                        <button class="dropdown-item" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-exchange-alt"></i> {{ __('buttons.change_owner') }}</button>
                                        <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $boto->id }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Авточаты отсутствуют</li>
                @endforelse
            </ul>
            <div class="px-3">
                {{ $bots_old->links() }}
            </div>
        </div>
    </div>



    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('pages.change_owner') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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