@extends('layouts.app')

@section('title', __('Мои авточаты'))

@section('styles')
    <link href="{{ asset('vendors/css/toastr.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    {{--@if(count($companies) <= $subscribe->plan->bot_count)--}}
    <button class="btn btn-sm btn-blue float-right" onclick="newLink()"><i class="fas fa-plus"></i> {{ __('Добавить авточат') }}</button>
    {{--@endif--}}
    <p class="h3">{{ __('Мои авточаты') }}</p>
    <div class="card card-accent-primary mt-3">
        <div class="card-body p-2 pb-0">
                @forelse($companies as $company)
                <div class="card card-accent-secondary mb-2">
                    <div class="card-body py-2">
                        {{--<p class="mb-2 text-blue">{{ __('Авточат') }}</p>--}}
                        <div class="d-flex w-100 justify-content-between px-1 mb-2">
                            <p class="mb-0"><b>{{ __('Ссылка:') }}</b> <span class="text-blue ml-1" title="{{ __('Ссылка на авточат') }}">{{ $company->slug }}</span></p>
                            <small>
                                <a href="https://getchat.me/{{ $company->slug }}" target="_blank" class="text-muted" title="{{ __('Просмотр авточата') }}"><i class="fas fa-eye"></i></a>
                                <a href="#" class="ml-1" onclick="EditLink({{ $company->id }}, '{{ $company->slug }}')"><i class="fas fa-pencil-alt" title="{{ __('Редактировать ссылку') }}"></i></a>
                                <a href="#" class="ml-1" onclick="addBot({{ $company->id }}, '{{ $company->slug }}')"><i class="fas fa-plus" title="{{ __('Добавить авточат к ссылке') }}"></i></a>
                                <a href="{{ route('partner::bots::data', $company->id) }}" class="ml-1 text-muted"><i class="fas fa-database" title="{{ __('Данные авточата') }}"></i></a>
                                <a href="#" class="text-red ml-1" title="{{ __('Удалить ссылку и все авточаты') }}" onclick="event.preventDefault();document.getElementById('removeCompany{{ $company->id }}').submit();"><i class="fas fa-trash-alt"></i></a>
                                <form id="removeCompany{{ $company->id }}" action="{{ route('partner::bots::delete_company', $company->id) }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </small>
                        </div>
                        @foreach($company->bots as $bot)
                        <hr class="my-1">
                        <div class="d-flex w-100 justify-content-between mt-1">
                            <div>
                                <strong><i class="far fa-comment mr-1"></i><span title="{{ __('Наименование авточата') }}">{{ $bot->name }}</span></strong>
                            </div>
                            <div class="form-inline">
                                @if($bot->active != 1 && ($new_bot_count >= $plan_bot_count) && $plan_bot_count != 0)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input activate_new_bot" id="activate_new_bot_{{ $bot->id }}" onclick="activate_new_bot({{ $bot->id }})" {{ $bot->active == 1 ? 'checked' : '' }} disabled>
                                        <label class="custom-control-label" for="activate_new_bot_{{ $bot->id }}"></label>
                                    </div>
                                @else
                                    <div class="custom-control custom-checkbox text-center">
                                        <input type="checkbox" class="custom-control-input activate_new_bot" id="activate_new_bot_{{ $bot->id }}" onclick="activate_new_bot({{ $bot->id }})" {{ $bot->active == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activate_new_bot_{{ $bot->id }}"></label>
                                    </div>
                                @endif
                                <a href="https://getchat.me/constructor2/{{ $bot->id }}" target="_blank" title="{{ __('Перейти в конструктор') }}"><i class="fas fa-wrench"></i></a>
                                <div class="dropdown mx-2">
                                    <a class="" href="#" role="button" id="dropdownMenuLink{{ $bot->id }}" title="{{ __('Дополнительные функции') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink{{ $bot->id }}">
                                        <a class="dropdown-item" href="https://getchat.me/botpreview/{{ $bot->id }}" target="_blank"><i class="far fa-eye-slash mr-2"></i>{{ __('Предпросмотр') }}</a>
                                        <a class="dropdown-item" href="#" onclick="copyTemplate({{ $bot->id }}, '{{ $company->slug }}', '{{ $bot->name }}')"><i class="far fa-copy mr-2"></i>{{ __('Копировать авточат') }}</a>
                                        <a class="dropdown-item text-red" href="#" onclick="event.preventDefault();document.getElementById('removeBot{{ $bot->id }}').submit();"><i class="far fa-trash-alt mr-2"></i>{{ __('Удалить авточат') }}</a>
                                        <form id="removeBot{{ $bot->id }}" action="{{ route('partner::bots::delete_bot', $bot->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <ul class="list-group mb-3">
                    <li class="list-group-item">Записи отсутствуют</li>
                </ul>
                @endforelse
            {{--<div class="mt-3 text-center">--}}
                {{--{{ $companies->links() }}--}}
            {{--</div>--}}
        </div>
    </div>
    {{--Добавление новой ссылки--}}
    <div class="modal fade" id="newLinkModal" tabindex="-1" role="dialog" aria-labelledby="newLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="newLinkForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Добавление нового авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input class="form-control mb-2" type="text" id="newNameSlug" name="name" placeholder="{{ __('Введите название авточата') }}">
                        <input class="form-control" type="text" id="newLinkSlug" name="slug" placeholder="{{ __('Введите название ссылки на авточат') }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="newLinkModalClose()" data-dismiss="modal">{{ __('Отмена') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Изменение ссылки--}}
    <div class="modal fade" id="editLinkModal" tabindex="-1" role="dialog" aria-labelledby="editLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editLinkForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Редактирование ссылки') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="link_id" name="company_id">
                        <input class="form-control" type="text" value="{{ old('slug') }}" name="slug" id="link_slug">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="editLinkCloseForm()" data-dismiss="modal">{{ __('Отмена') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Изменение ссылки--}}
    <div class="modal fade" id="addBotModal" tabindex="-1" role="dialog" aria-labelledby="addBotModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addBotForm">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Добавление нового авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="mb-2">
                            <input type="hidden" id="addBotId" name="company_id">
                            <span>{{ __('Ссылка:') }} <span id="addBotSlug"></span></span>
                            {{--<input class="form-control readonly" type="text" value="{{ old('slug') }}" name="slug" id="link_slug">--}}
                        </div>
                        <div>
                            <input class="form-control" type="text" id="addBotName" name="name" placeholder="{{ __('Введите наименование авточата') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="addBotModalClose()" data-dismiss="modal">{{ __('Отмена') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Копирование авточата--}}
    <div class="modal fade" id="copyTemplateModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="copyBotForm">
                    @csrf
                    <input type="hidden" id="template_id" name="template_id">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Копирование авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Наименование:') }}</strong>
                        <input id="copy_slug_link" class="form-control" type="text" disabled>
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Ссылка:') }}</strong>
                        <input id="user_link" class="form-control" type="text" name="link">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="copyTemplateModalClose()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Копировать') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    /*-----Добавление новой ссылки-----*/
    function newLink() {
        $('#newLinkModal').modal('show');
    }
    $('#newLinkForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: '{{ route('partner::bots::add_company') }}',
            data: $('#newLinkForm').serialize(),
            success: function (request) {
                if (request.error === 0) {
                    newLinkModalClose();
                    location.reload();
                } else {
                    $('#newLinkSlug').addClass('is-invalid');
                }
            }
        });
    });
    function newLinkModalClose() {
        $('#newNameSlug').val('');
        $('#newLinkSlug').val('');
        $('#newLinkModal').modal('hide');
    }
    /*----------Активация нового авточата----------*/
    var new_bot_count = parseInt({{ $new_bot_count }});
    var plan_bot_count = parseInt({{ $plan_bot_count }});
    function activate_new_bot(id) {
        var url = '../../manager/bots/activate-bot/' + {{ $user->id }} +'/' + id;

        var active_count_bot = $('.activate_new_bot:checked').length;

        if(plan_bot_count === 0) {
            $.get(url,
                function(data) {
                    if(data.error === 0) {
                        toastr.success(data.message, 'Ok');
                    } else if(data.error === 2) {
                        $('#activate_new_bot_' + id).prop('checked', false);
                        toastr.warning('На данную ссылку уже активирован авточат!', 'Внимание!');
                    } else {
                        $('#activate_new_bot_' + id).prop('checked', false);
                        toastr.error('Произошла ошибка!', 'Внимание!');
                    }
                }
            );
        } else {
            $.get(url,
                function(data) {
                    // console.log(data);
                    if(data.error === 0) {
                        toastr.success(data.message, 'Ok');
                        if(plan_bot_count > 0) {
                            if(active_count_bot >= plan_bot_count && plan_bot_count !== 0) {
                                $('.activate_new_bot:not(:checked)').prop('disabled', true);
                                toastr.warning('Достигнуто максимальное количество!', 'Внимание!');
                            } /*else {
                                $('.activate_new_bot:not(:checked)').prop('disabled', false);
                                toastr.warning('Доступно активирование!', 'Внимание!', {
                                    "positionClass": "toast-top-right",
                                    "hideDuration": "200",
                                    "closeDuration": "200",
                                });
                            }*/
                        }
                    } else if(data.error === 2) {
                        $('#activate_new_bot_' + id).prop('checked', false);
                        toastr.warning('На данную ссылку уже активирован авточат!', 'Внимание!');
                    } else {
                        $('#activate_new_bot_' + id).prop('checked', false);
                        toastr.error('Произошла ошибка!', 'Внимание!');
                    }
                }
            );
        }

    }

    /*-----Редактирование ссылки-----*/
    function EditLink(link_id, link_slug) {
        $('#link_id').val(link_id);
        $('#link_slug').val(link_slug);
        $('#editLinkModal').modal('show');
    }
    $('#editLinkForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: '{{ route('partner::bots::edit_slug') }}',
            data: $('#editLinkForm').serialize(),
            success: function (request) {
                if (request.error === 0) {
                    editLinkCloseForm();
                    location.reload();
                } else {
                    $('#link_slug').addClass('is-invalid');
                }
            }
        });
    });
    function editLinkCloseForm() {
        $('#id').val('');
        $('#slug').val('');
        $('#editLinkModal').modal('hide');
    }
    /*-----Добавление авточата к ссылке-----*/
    function addBot(company_id, company_slug) {
        $('#addBotId').val(company_id);
        $('#addBotSlug').text(company_slug);
        $('#addBotModal').modal('show');
    }
    $('#addBotForm').on('submit', function (e) {
        e.preventDefault();
        if($('#addBotName').val() !== '') {
            console.log($('#addBotName').val());
            $.ajax({
                type: 'post',
                url: '{{ route('partner::bots::add_bot') }}',
                data: $('#addBotForm').serialize(),
                success: function (request) {
                    if (request.error === 0) {
                        addBotModalClose();
                        location.reload();
                    }
                }
            });
        } else {
            $('#addBotName').addClass('is-invalid');
        }

    });
    function addBotModalClose() {
        $('#addBotId').val('');
        $('#addBotName').val('');
        $('#addBotSlug').text('');
        $('#addBotModal').modal('hide');
    }

    /*-----Копирование авточата-----*/
    function copyTemplate(id, slug, bot_name) {
        $('#template_id').val(id);
        $('#copy_slug_link').val(bot_name);
        $('#user_link').val(slug);
        $('#copyTemplateModal').modal();
    }
    $('#copyBotForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: '{{ route('partner::bots::copy_templates') }}',
            data: $('#copyBotForm').serialize(),
            success: function (request) {
                console.log(request);
                if (request.error === 0) {
                    copyTemplateModalClose();
                    location.reload();
                } else {
                    $('#user_link').addClass('is-invalid');
                }
            }
        });
    });
    function copyTemplateModalClose() {
        $('#template_id').val('');
        $('#copy_slug_link').val('');
        $('#user_link').val('');
        $('#copyTemplateModal').modal('hide');
    }
</script>
@endsection
