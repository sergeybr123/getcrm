@extends('layouts.app')

@section('title', __('users.users'))

@section('styles')
    <link href="{{ asset('vendors/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
    <style>
        #DataTables_Table_0_wrapper, #DataTables_Table_1_wrapper, #DataTables_Table_2_wrapper, #DataTables_Table_3_wrapper {
            padding: 0;
        }
        .list-group-item, .list-group-item:first-child, .list-group-item:last-child {
            border-radius: 0;
        }
        .list-group-item {
            border: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        .list-group-item:last-child {
            border: 0;
        }
    </style>
@endsection

@section('content')
    <div>
        <a href="{{ route('manager.users.index') }}" class="btn btn-outline-blue">
            <i class="fa fa-angle-double-left"></i> {{ __('Назад') }}
        </a>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="d-flex w-100 justify-content-between">
                <div>
                    <p class="h2">{{ $user->email }} {{-- $plan_bot_count --}} {{-- $new_bot_count --}}</p>
                    <div>
                        <strong>Телефон: </strong>
                        @if($user->phone)
                            +{{ $user->phone->country_code . $user->phone->phone }}
                        @else
                            Номер отсутствует
                        @endif
                    </div>
                    <div>
                        <strong>Дата регистрации: </strong>{{ \Carbon\Carbon::parse($user->created_at)->format('d.m.Y') }}
                    </div>
                </div>
                <div class="form-inline" style="align-items: normal;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-blue" title="{{ __('Редактировать данные') }}" style="border-radius:50%;width:30px;height:30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editEmailModal">{{ __('Изменить email') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editPhoneModal">{{ __('Изменить телефон') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editProfileModal">{{ __('Редактировать профиль') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editPasswordModal">{{ __('Изменить пароль') }}</a>
                        </div>
                    </div>
                    <a href="{{ route('manager.users.create_invoice', $user->id) }}" class="btn btn-sm btn-outline-blue ml-1"
                       style="border-radius:50%;width:30px;height:30px;" title="Выставить счет">
                        <i class="fa fa-file-invoice"></i>
                    </a>
{{--                    @if($subscribe->plan->code == 'unlimited')--}}
                        <a href="{{ route('manager.users.create_bot', ['user_id' => $user->id]) }}" style="border-radius:50%;width:30px;height:30px;" class="btn btn-sm btn-outline-blue ml-1" title="Добавить новый авточат">
                            <i class="fa fa-comments"></i>
                        </a>
{{--                    @else--}}
{{--                        @if(count($bots) <= 10)--}}
{{--                            <a href="{{ route('manager.users.create_bot', ['user_id' => $user->id]) }}" style="border-radius:50%;width:30px;height:30px;" class="btn btn-sm btn-outline-blue ml-1" title="Добавить новый авточат">--}}
{{--                                <i class="fa fa-comments"></i>--}}
{{--                            </a>--}}
{{--                        @endif--}}
{{--                    @endif--}}
                </div>
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="d-flex w-100 justify-content-between">
                @if($subscribe)
                    <div>
                        <p class="h4">{{ __("Тарифный план: ") . $subscribe->plan->name ?? __('Free') }}</p>
                        <span>{{ __('Дата регистрации: ') . \Carbon\Carbon::parse($subscribe->created_at)->format('d.m.Y') ?? '' }}</span><br>
                        <strong>Подписка с:</strong> {{ \Carbon\Carbon::parse($subscribe->start_at)->format('d.m.Y') ?? '' }}
                        <strong>по:</strong>
                        @if($subscribe->end_at != null)
                            {{ \Carbon\Carbon::parse($subscribe->end_at)->format('d.m.Y') }}
                        @else
                            {{ __('Бессрочная') }}
                        @endif
                        <br><strong>Статус:</strong>
                        @if($subscribe->active == 1)
                            <span class="badge badge-success">Активная</span>
                        @else
                            <span class="badge badge-danger">Не активная</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab1" role="tablist">
                <li class="nav-item"><a class="nav-link active show" id="new-bot-tab" data-toggle="tab" href="#new-bot-tab-content" role="tab" aria-controls="new-bot" aria-selected="true">Авточаты</a></li>
                <li class="nav-item"><a class="nav-link" id="invoice-tab" data-toggle="tab" href="#invoice-tab-content" role="tab" aria-controls="contact" aria-selected="false">Счета</a></li>
            </ul>
            <div class="tab-content" id="myTab1Content">
                <div class="tab-pane fade active show p-0" id="new-bot-tab-content" role="tabpanel" aria-labelledby="new-bot-tab">
                    {{--<div class="card card-accent-primary mt-3">--}}
                        <div class="card-body p-2 pb-0">



                            @forelse($bots as $company)
                                <div class="card card-accent-secondary mb-2">
                                    <div class="card-body py-2">
                                        {{--<p class="mb-2 text-blue">{{ __('Авточат') }}</p>--}}
                                        <div class="d-flex w-100 justify-content-between px-1 mb-2">
                                            <p class="mb-0"><b>{{ __('Ссылка:') }}</b> <span class="text-blue ml-1" title="{{ __('Ссылка на авточат') }}">{{ $company->slug }}</span></p>
                                            <small>
                                                <a href="https://getchat.me/{{ $company->slug }}" target="_blank" class="text-muted" title="{{ __('Просмотр авточата') }}"><i class="fas fa-eye"></i></a>
                                                <span id="page_slug_{{ $company->id }}" style="display: none">https://getchat.me/{{ $company->slug }}</span>
                                                {{--<button class="btn float-right btn-sm btn-outline-blue ml-2" type="button"--}}
                                                        {{--title="Копировать ссылку"--}}
                                                        {{--onclick="copyPageToClipboard({{ $company->id }})" style="border-radius:50%;">--}}
                                                    {{--<i class="fa fa-copy"></i>--}}
                                                {{--</button>--}}
                                                <a href="#" class="ml-1" onclick="copyPageToClipboard({{ $company->id }})"><i class="fas fa-copy" title="{{ __('Копировать ссылку') }}"></i></a>
                                                <a href="#" class="ml-1" onclick="EditLink({{ $company->id }}, '{{ $company->slug }}')"><i class="fas fa-pencil-alt" title="{{ __('Редактировать ссылку') }}"></i></a>
                                                <a href="#" class="ml-1" onclick="addBot({{ $company->id }}, '{{ $company->slug }}')" {{ count($company->bots) == 5 ? 'disabled' : '' }}><i class="fas fa-plus" title="{{ __('Добавить авточат к ссылке') }}"></i></a>
{{--                                                <a href="{{ route('partner::bots::data', $company->id) }}" class="ml-1 text-muted"><i class="fas fa-database" title="{{ __('Данные авточата') }}"></i></a>--}}
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
                                                    <a href="{{ config('app.constructor').$bot->id }}?account={{ $user->email }}" target="_blank" title="{{ __('Перейти в конструктор') }}"><i class="fas fa-wrench"></i></a>
                                                    <div class="dropdown mx-2">
                                                        <a class="" href="#" role="button" id="dropdownMenuLink{{ $bot->id }}" title="{{ __('Дополнительные функции') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink{{ $bot->id }}">
                                                            <a class="dropdown-item" href="https://getchat.me/botpreview/{{ $bot->id }}" target="_blank"><i class="far fa-eye-slash mr-2"></i>{{ __('Предпросмотр') }}</a>
                                                            <a class="dropdown-item" href="#" onclick="copyTemplate({{ $bot->id }}, '{{ $company->slug }}', '{{ $bot->name }}')"><i class="far fa-copy mr-2"></i>{{ __('Копировать авточат') }}</a>
                                                            <a class="dropdown-item text-red" href="#" onclick="event.preventDefault();document.getElementById('delete-chat-{{ $bot->id }}').submit();"><i class="far fa-trash-alt mr-2"></i>{{ __('Удалить авточат') }}</a>
                                                            <form id="delete-chat-{{$bot->id}}" action="{{ route('manager.users.delete_chat', ['id' => $company->id, 'user_id' => $user->id, 'bot_id' => $bot->id ]) }}" method="POST" style="display: none;">
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



                        </div>
                    {{--</div>--}}


                    {{--<table class="table table-bordered table-striped table-responsive-sm dataTable">--}}
                        {{--<thead>--}}
                        {{--<tr>--}}
                            {{--<th width="30">#</th>--}}
                            {{--<th width="5"></th>--}}
                            {{--<th width="120">Наименование</th>--}}
                            {{--<th class="d-none d-md-table-cell">Ссылка</th>--}}
                            {{--<th width="100">Дата создания</th>--}}
                            {{--<th width="54"></th>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody>--}}
                        {{--@foreach($bots as $key => $new_bot)--}}
                            {{--@if(is_null($new_bot->BotDelete))--}}
                            {{--<tr>--}}
                                {{--<td>{{ $new_bot->BotId }}</td>--}}
                                {{--<td>--}}
                                    {{--                                    <input id="activate_new_bot" type="checkbox" {{ $new_bot->BotActive == 1 ? 'checked' : '' }} data-toggle="toggle" data-onstyle="success" data-size="xs" onclick="activate_new_bot({{ $new_bot->BotId }})">--}}
                                    {{--@if($new_bot->BotActive != 1 && ($new_bot_count >= $plan_bot_count) && $plan_bot_count != 0)--}}
                                        {{--<div class="custom-control custom-checkbox">--}}
                                            {{--<input type="checkbox" class="custom-control-input activate_new_bot" id="activate_new_bot_{{ $new_bot->BotId }}" onclick="activate_new_bot({{ $new_bot->BotId }})" {{ $new_bot->BotActive == 1 ? 'checked' : '' }} disabled>--}}
                                            {{--<label class="custom-control-label" for="activate_new_bot_{{ $new_bot->BotId }}"></label>--}}
                                        {{--</div>--}}
                                    {{--@else--}}
                                        {{--<div class="custom-control custom-checkbox text-center">--}}
                                            {{--<input type="checkbox" class="custom-control-input activate_new_bot" id="activate_new_bot_{{ $new_bot->BotId }}" onclick="activate_new_bot({{ $new_bot->BotId }})" {{ $new_bot->BotActive == 1 ? 'checked' : '' }}>--}}
                                            {{--<label class="custom-control-label" for="activate_new_bot_{{ $new_bot->BotId }}"></label>--}}
                                        {{--</div>--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                                {{--<td>{{ $new_bot->BotName }}</td>--}}
                                {{--<td class="d-none d-md-table-cell">--}}
                                    {{--<span>{{ $new_bot->Slug }}</span>--}}
                                    {{--<span id="page_slug_{{ $key }}" style="display: none">https://getchat.me/{{ $new_bot->Slug }}</span>--}}
                                    {{--<button class="btn float-right btn-sm btn-outline-blue ml-2" type="button"--}}
                                            {{--title="Копировать ссылку"--}}
                                            {{--onclick="copyPageToClipboard({{ $key }})" style="border-radius:50%;">--}}
                                        {{--<i class="fa fa-copy"></i>--}}
                                    {{--</button>--}}
                                {{--</td>--}}
                                {{--<td class="text-center">{{ \Carbon\Carbon::parse($new_bot->CompanyCreated)->format('d.m.Y') ?? '' }}</td>--}}
                                {{--<td>--}}

                                    {{--<div class="form-inline">--}}
                                        {{--<a href="https://getchat.me/constructor2/{{ $new_bot->BotId }}" target="_blank" class="btn btn-sm btn-outline-blue mr-1" style="border-radius:50%;">--}}
                                            {{--<i class="fa fa-wrench"></i>--}}
                                        {{--</a>--}}
                                        {{--<div class="dropdown">--}}
                                            {{--<button class="btn btn-sm btn-outline-blue ml-1" type="button"--}}
                                                    {{--id="dropdownMenuButton"--}}
                                                    {{--data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
                                                    {{--style="border-radius:50%;width:30px;height:30px;">--}}
                                                {{--<i class="fa fa-ellipsis-v"></i>--}}
                                            {{--</button>--}}
                                            {{--<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">--}}
                                                {{--<a class="dropdown-item" href="https://getchat.me/{{ $new_bot->Slug }}" target="_blank">--}}
                                                    {{--<i class="far fa-eye"></i> {{ __('buttons.view') }}--}}
                                                {{--</a>--}}
                                                {{--<button class="dropdown-item" onclick="createOnExist({{ $new_bot->Id }}, 2)"><i class="fa fa-comments"></i> {{ __('Создать мультилинк') }}</button>--}}
                                                {{--<button class="dropdown-item" onclick="copyTemplate({{ $new_bot->BotId }}, '{{ $new_bot->Slug }}')"><i class="fa fa-copy"></i> {{ __('Копировать авточат') }}</button>--}}
                                                {{--<a class="dropdown-item" href="{{ route('manager.bots.change_owner', [$user->id, $new_bot->Id]) }}">--}}
                                                    {{--<i class="fa fa-exchange-alt"></i> {{ __('Изменить владельца') }}--}}
                                                {{--</a>--}}
                                                {{--<a class="dropdown-item text-danger" href="#"  onclick="event.preventDefault();document.getElementById('delete-chat-{{$new_bot->BotId}}').submit();">--}}
                                                    {{--<i class="fa fa-trash"></i> {{ __('Удалить') }}--}}
                                                {{--</a>--}}
                                                {{--<form id="delete-chat-{{$new_bot->BotId}}" action="{{ route('manager.users.delete_chat', ['id' => $new_bot->Id, 'user_id' => $user->id, 'bot_id' => $new_bot->BotId ]) }}" method="POST" style="display: none;">--}}
                                                    {{--@csrf--}}
                                                {{--</form>--}}
                                                {{--<a class="dropdown-item text-danger" href="#" onclick="event.preventDefault();document.getElementById('delete-full-{{$new_bot->BotId}}').submit();">--}}
                                                    {{--<i class="fa fa-trash"></i> {{ __('Удалить полностью') }}--}}
                                                {{--</a>--}}
                                                {{--<form id="delete-full-{{$new_bot->BotId}}" action="{{ route('manager.users.delete_full', ['id' => $new_bot->Id, 'user_id' => $user->id, 'bot_id' => $new_bot->BotId ]) }}" method="POST" style="display: none;">--}}
                                                    {{--@csrf--}}
                                                {{--</form>--}}
                                                {{--@role('admin')--}}
                                                {{--<a class="dropdown-item text-danger" href="#" onclick="event.preventDefault();document.getElementById('delete-force-{{$new_bot->BotId}}').submit();">--}}
                                                    {{--<i class="fa fa-trash"></i> {{ __('Удалить без возврата') }}--}}
                                                {{--</a>--}}
                                                {{--<form id="delete-force-{{$new_bot->BotId}}" action="{{ route('manager.users.delete_force', ['id' => $new_bot->Id, 'user_id' => $user->id, 'bot_id' => $new_bot->BotId ]) }}" method="POST" style="display: none;">--}}
                                                    {{--@csrf--}}
                                                {{--</form>--}}
                                                {{--@endrole--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                        {{--</tbody>--}}
                    {{--</table>--}}
                </div>
                {{--Закладка счета пользователя--}}
                <div class="tab-pane fade" id="invoice-tab-content" role="tabpanel" aria-labelledby="invoice-tab">
                    <table class="table table-bordered table-striped table-responsive-sm dataTable">
                        <thead>
                        <tr>
                            <th width="70">#</th>
                            <th width="120">Вид</th>
                            {{--<th>Наименование</th>--}}
                            <th width="70">Сумма</th>
                            <th width="70">Статус</th>
                            <th width="100">Дата создания</th>
                            <th width="100">Дата оплаты</th>
                            <th width="10"></th>
                            @permission('confirm-pay')
                            <th width="10"></th>
                            @endpermission
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->types->name }}</td>
                                {{--<td>--}}
                                    {{--@if($invoice->plan != null)--}}
                                        {{--{{ $invoice->plan->name }}--}}
                                    {{--@else--}}
                                        {{--@if($invoice->service != null)--}}
                                            {{--{{ $invoice->service->name }}--}}
                                        {{--@endif--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                                <td class="text-right">{{ number_format($invoice->amount, 0, '', ' ') }} тг.</td>
                                <td class="text-center">
                                    @if($invoice->status == 'active')
                                        <span class="badge badge-danger">Не оплачен</span>
                                    @elseif($invoice->status == 'paid')
                                        <span class="badge badge-success">Оплачен</span>
                                    @else
                                        <span class="badge badge-warning">Завершен</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d.m.Y') ?? '' }}</td>
                                <td class="text-center">
                                    @if($invoice->paid_at != null)
                                        {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d.m.Y') }}
                                    @endif
                                </td>
                                <td>
                                    <span id="invoice_{{ $invoice->id }}" style="display:none;">https://getchat.me/order/pay/{{ $invoice->id }}</span>
                                    @if($invoice->status == 'active')
                                        <button class="btn btn-outline-blue btn-sm" title="{{ __('Скопировать ссылку на оплату') }}"
                                                onclick="copyInvoiceToClipboard({{ $invoice->id }})" type="button"><i class="fa fa-copy"></i>
                                        </button>
                                    @endif
                                </td>
                                @permission('confirm-pay')
                                <td>
                                    @if($invoice->status == 'active')
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-blue" title="Подтвердить оплату" id="dropdownMenuButton_{{ $invoice->id }}" data-toggle="dropdown" onclick="selectInvoice({{ $invoice->id }})"><i class="fa fa-credit-card"></i></button>
                                            <div id="dropdownCalendar_{{ $invoice->id }}" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <div style="height:27px;padding-right:10px;">
                                                    <button type="button" class="close" aria-label="Close" onclick="closeDatapicker({{ $invoice->id }})">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="datepicker"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                @endpermission
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    {{--Добавление авточата к ссылке--}}
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
                        <input class="form-control" type="text" value="{{ old('slug') }}" name="slug" onkeyup="keyPressed()" id="link_slug">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="editLinkCloseForm()" data-dismiss="modal">{{ __('Отмена') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Изменение владельца--}}
    <div class="modal fade" id="changeOwnerModal" tabindex="-1" role="dialog" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="changeOwnerForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Изменить владельца') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ __('Ссылка:') }}</strong> https://getchat.me/<strong id="owner_link_slug"></strong></p>
                        <input type="hidden" id="company_owner_id" name="company_id">
                        <input id="user" class="form-control" type="text" name="user" list="user_list">
                        <datalist id="user_list">
                        </datalist>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="CloseChangeOwnerForm()">{{ __('Закрыть') }}</button>
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
                <form id="copyBotForm" method="post" action="{{ route('copy_templates') }}">
                    @csrf
                    <input type="hidden" id="template_id" name="template_id">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Копирование авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Ссылка:') }}</strong>
                        <input id="user_link" class="form-control" type="text" name="link">
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Пользователь:') }}</strong>
                        <input id="user_email" class="form-control" type="text" name="user_email" value="{{ $user->email }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Копировать') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Редактировать email--}}
    <div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editEmailForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmailModalLabel">{{ __('Редактрование email') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-2 col-md-1">
                                {{ __('Email:') }}
                            </div>
                            <div class="col-10 col-md-11">
                                <input type="email" class="form-control" id="editEmailInput" name="email" value="{{ $user->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditEmailModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Редактировать телефон--}}
    <div class="modal fade" id="editPhoneModal" tabindex="-1" role="dialog" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editPhoneForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPhoneModalLabel">{{ __('Редактирование номера телефона') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                                {{ __('Код страны:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <select id="select2-1" class="form-control select2-single" name="country_code">
                                    @foreach($phones as $phone)
                                        <option value="{{ str_replace('+', '', $phone['dial_code']) }}" {{ str_replace('+', '', $phone['dial_code']) == $user_phone->country_code ? 'selected' : '' }}>{{ $phone['name'] . ' ' . $phone['dial_code'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Телефон:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editPhoneInput" name="phone" value="{{ $user_phone->phone }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditPhoneModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Редактировать профиль пользователя--}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editProfileForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">{{ __('Редактрование профиля пользователя') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                                {{ __('Фамилия:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editLastNameInput" name="last_name" value="{{ $profile->last_name }}" autofocus>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Имя:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editFirstNameInput" name="first_name" value="{{ $profile->first_name }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Организация:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editCompanyInput" name="company" value="{{ $profile->company }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Расположение:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editLocationInput" name="location" value="{{ $profile->location }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Редактировать пароль пользователя--}}
    <div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editPasswordForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPasswordModalLabel">{{ __('Изменение пароля пользователя') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                                {{ __('Пароль:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="password" class="form-control" id="passwordInput" name="password" autofocus>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Повторить:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="password" class="form-control" id="confirmInput" name="confirm_password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditPasswordModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('vendors/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>
    <script>
        /*-------------------Копирование ссылок---------------------*/
        function copyPageToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#page_slug_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }
        /*-------------------Копирование ссылок---------------------*/
        var user_id = {{ $user->id }};
        var billing_token = '{{ config('app.billing_token') }}';
        var billing_url = '{{ config('app.billing_url') }}';

        $('.dataTable').DataTable({
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[30, 50, 100, -1], [30, 50, 100, "Все"]],
            // "pagingType": "full_numbers",
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
            }
        });
        $('.dataTable').attr('style','border-collapse: collapse !important');

        $('#change-plan').submit(function ( e ) {
            var data;

            data = $('#change-plan').serialize();

            $.ajax({
                url: "{{ route('change_plan') }}",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                success: function (req) {
                    if(req.error === 0) {
                        location.reload();
                    } else {
                        console.log(req);
                    }
                }
            });
            e.preventDefault();
        });

        /*------------Выставить оплату счета------------*/
        var invoiceId = 0;
        var datePay = '';
        function selectInvoice(id) {
            invoiceId = id;
            $('#dropdownCalendar_' + id).show();
        }
        $( ".datepicker" ).datepicker({
            onSelect: function () {
                datePay = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker('getDate'));
                // console.log(datePay);
                var data = {
                    id: invoiceId,
                    date: datePay
                };
                // console.log(data);
                $.ajax({
                    type: "POST",
                    url: billing_url + "/pay-with-day",
                    dataType: 'json',
                    async: false,
                    data: data,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        console.log(request);
                        if(request.error === 0) {
                            $('#dropdownCalendar_' + invoiceId).hide();
                            location.reload();
                        }
                    }
                });
            }
        });
        function closeDatapicker(id) {
            $('#dropdownCalendar_' + id).hide();
        }

        /*-----Запрет воода спец символов-----*/
        function keyPressed(){
            var presslId = $(event.target)[0].id;
            // console.log(presslId);
            ChangeSymbol(presslId);
        }
        function ChangeSymbol(id) {
            var text = document.getElementById(id).value;
            // console.log(text);
            var transl = new Array();
            transl[' ']='_';    transl['!']='_';
            transl['%']='_';    transl['^']='_';
            transl['&']='_';    transl['*']='_';
            transl['@']='_';    transl['#']='_';
            transl['$']='_';    transl['&']='_';
            transl['/']='_';

            var result='';
            for(i=0;i<text.length;i++) {
                if(transl[text[i]] !== undefined) {
                    result += transl[text[i]];
                }
                else {
                    result += text[i];
                }
            }
            document.getElementById(id).value=result;
        };

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
                        toastr.error('Произошла ошибка!', 'Внимание!');
                    }
                }
            });
        });
        function editLinkCloseForm() {
            $('#id').val('');
            $('#slug').val('');
            $('#link_slug').removeClass('is-invalid');
            $('#editLinkModal').modal('hide');
        }

        /*-------------------Смена владельца-----------------------*/
        var email = '';
        var str = '';
        function changeOwnerButtonClick(id, slug){
            $('#company_owner_id').val(id);
            $('#owner_link_slug').text(slug);
        }
        $('#user').on('input', function(){
            email = $(this).val();
            var data = {
                email: email
            };
            if(email.length > 2){
                $.post( "/api/get-users", data, function(req) {
                    $('.test_opt').remove();
                    str = '';
                    // console.log(req);
                    $.each(req, function(key, value) {
                        str += '<option class="test_opt" value="' + value.email + '">';
                    });
                    $('#user_list').append(str);
                    // this.req = '';
                });
                str = '';
            }
        });
        $('#changeOwnerForm').on('submit', function (e) {
            e.preventDefault();
            // console.log($('#changeOwnerForm').serialize())
            $.ajax({
                type: "post",
                url: "/api/change-owner",
                data: $('#changeOwnerForm').serialize(),
                success: function (request) {
                    if(request.error === 0) {
                        CloseChangeOwnerForm();
                        location.reload();
                    } else {
                        console.log(request)
                    }
                }
            });
        });
        function CloseChangeOwnerForm(){
            $('#user').val('');
            str = '';
            $('.test_opt').remove();
            email = '';
        }
        // Скопировать счет
        function copyInvoiceToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#invoice_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }
        /*--------Создание нового авточата по старой ссылке--------*/
        function createOnExist(id, type) {
            var url = 'https://getchat.me/constructor2/';
            console.log(id + ' ' + type);
            $.get("/create-bot-on-exist/" + id + '/' + type,
                function(data) {
                    var target = $(this).prop('target');
                    window.open(url + data.bot_id, target);
                    console.log(url + data.bot_id);
                });
        }


        /*--------Копирование авточата-------*/
        var template_id = null;
        function copyTemplate(id, slug) {
            $('#template_id').val(id);
            $('#user_link').val(slug);
            $('#copyTemplateModal').modal();
            // template_id = id;
        }

        /*----------Активация нового авточата----------*/
        var new_bot_count = parseInt({{ $new_bot_count }});
        var plan_bot_count = parseInt({{ $plan_bot_count }});
        function activate_new_bot(id) {
            var url = '../../manager/bots/activate-bot/' + user_id +'/' + id;

            var active_count_bot = $('.activate_new_bot:checked').length;

            if(plan_bot_count === 0) {
                $.get(url,
                    function(data) {
                        if(data.error === 0) {
                            toastr.success(data.message, 'Ok');
                        } else {
                            $('#activate_new_bot_' + id).prop('checked', false);
                            toastr.error(data.message, 'Внимание!');
                        }
                    }
                );
            } else {
                $.get(url,
                    function(data) {
                    // console.log(data);
                        if(data.error === 0) {
                            toastr.success('Данные сохранены!', 'Ok', {
                                "positionClass": "toast-top-right",
                                "hideDuration": "500",
                                "closeDuration": "500",
                            });
                            if(plan_bot_count > 0) {
                                if(active_count_bot >= plan_bot_count) {
                                    $('.activate_new_bot:not(:checked)').prop('disabled', true);
                                    toastr.warning('Достигнуто максимальное количество!', 'Внимание!');
                                } else {
                                    $('.activate_new_bot:not(:checked)').prop('disabled', false);
                                    toastr.warning('Доступно активирование!', 'Внимание!');
                                }
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

        {{--Редактировать email--}}
        $('#editEmailForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: "{{ route('manager.users.change_email', $user->id) }}",
                data: $('#editEmailForm').serialize(),
                success: function (request) {
                    if (request.error === 0) {
                        toastr.success(request.message, 'Ok');
                        closeEditEmailModal();
                        setTimeout(reLoad, 1000);
                    } else {
                        toastr.error(request.message, 'Внимание!');
                        $('#editEmailInput').addClass('is-invalid');
                    }
                }
            });
        });
        function closeEditEmailModal() {
            // $('#editEmailInput').val('');
            $("#editEmailModal").modal('hide');
        }

        {{--Редактирование телефона--}}
        $('#editPhoneForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: "{{ route('manager.users.change_phone', $user->id) }}",
                data: $('#editPhoneForm').serialize(),
                success: function (request) {
                    // console.log(request);
                    if (request.error === 0) {
                        toastr.success(request.message, 'Ok');
                        closeEditPhoneModal();
                        setTimeout(reLoad, 1000);
                    } else {
                        toastr.error(request.message, 'Внимание!');
                        $('#editPhoneInput').addClass('is-invalid');
                    }
                }
            });
        });
        var closeEditPhoneModal = function () {
            $("#editPhoneModal").modal('hide');
        };

        {{--Редактирование профиля--}}
        $('#editProfileForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: "{{ route('manager.users.change_profile', $user->id) }}",
                data: $('#editProfileForm').serialize(),
                success: function (request) {
                    // console.log(request.message);
                    if (request.error === 0) {
                        toastr.success(request.message, 'Ok');
                        closeEditProfileModal();
                        setTimeout(reLoad, 1000);
                    } else {
                        toastr.error(request.message, 'Внимание!');
                        // $('#editEmailInput').addClass('is-invalid');
                    }
                }
            });
        });
        var closeEditProfileModal = function() {
            $("#editProfileModal").modal('hide');
        };
        /*-----Редактирование пароля-----*/
        $('#editPasswordForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: "{{ route('manager.users.change_password', $user->id) }}",
                data: $('#editPasswordForm').serialize(),
                success: function (request) {
                    if (request.error === 0) {
                        toastr.success(request.message, 'Ok');
                        closeEditPasswordModal();
                    } else {
                        toastr.error(request.message, 'Внимание!');
                        $('#passwordInput').addClass('is-invalid');
                        $('#confirmInput').addClass('is-invalid');
                    }
                }
            });
        });
        var closeEditPasswordModal = function() {
            $("#editPasswordModal").modal('hide');
        };
        /*-----Добавление авточата к ссылке-----*/
        function addBot(company_id, company_slug) {
            $('#addBotId').val(company_id);
            $('#addBotSlug').text(company_slug);
            $('#addBotModal').modal('show');
        }
        $('#addBotForm').on('submit', function (e) {
            e.preventDefault();
            if($('#addBotName').val() !== '') {
                // console.log($('#addBotName').val());
                $.ajax({
                    type: 'post',
                    url: '{{ route('partner::bots::add_bot') }}',
                    data: $('#addBotForm').serialize(),
                    success: function (request) {
                        if (request.error === 0) {
                            addBotModalClose();
                            setTimeout(reLoad, 1000);
                        }
                    }
                });
            } else {
                $('#addBotName').addClass('is-invalid');
                toastr.error('Произошла ошибка!', 'Внимание!');
            }

        });
        function addBotModalClose() {
            $('#addBotId').val('');
            $('#addBotName').val('');
            $('#addBotSlug').text('');
            $('#addBotName').removeClass('is-invalid');
            $('#addBotModal').modal('hide');
        }
        /*-----Перезагрузка страницы-----*/
        var reLoad = function() {
            location.reload();
        }
    </script>
@endsection
