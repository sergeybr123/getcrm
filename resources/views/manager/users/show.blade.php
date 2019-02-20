@extends('layouts.app')

@section('title', __('users.users'))

@section('styles')
    <link href="{{ asset('vendors/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <style>
        #DataTables_Table_0_wrapper, #DataTables_Table_1_wrapper, #DataTables_Table_2_wrapper {
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
                    <p class="h2">{{ $user->email }}</p>
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
                    <a href="{{ route('manager.users.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-outline-blue" style="border-radius:50%;width:30px;height:30px;"
                       title="{{ __('Редактировать') }}">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a href="{{ route('manager.users.create_invoice', $user->id) }}" class="btn btn-sm btn-outline-blue ml-1"
                       style="border-radius:50%;width:30px;height:30px;" title="Выставить счет">
                        <i class="fa fa-file-invoice"></i>
                    </a>
                    {{--<a href="#" class="btn btn-sm btn-outline-blue ml-1"--}}
                       {{--style="border-radius:50%;width:30px;height:30px;" data-toggle="modal" data-target="#invoiceModal"--}}
                       {{--title="Выставить счет" onclick="Load();">--}}
                        {{--<i class="fa fa-file-invoice"></i>--}}
                    {{--</a>--}}
                    <a href="#" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;width:30px;height:30px;"
                       title="Добавить авточат" data-toggle="modal" data-target="#createBotModal">
                        <i class="fa fa-comments"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="d-flex w-100 justify-content-between">
                @if($subscribe == null)
                    <div>
                        <p class="h4">{{ __("Тарифный план: ") . __('Free') }}</p>
                    </div>
                    {{--<button class="btn btn-outline-blue" type="button" data-toggle="modal" data-target="#payActivateModal" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">--}}
                        {{--Активировать--}}
                    {{--</button>--}}
                @endif
                @if($subscribe != null)
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
                    @if($subscribe->plan->id == 2 || $subscribe->plan->id == 3)
                    <div>
                        <form id="change-plan">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <p class="mb-1">{{ __('Выберите тарифный план') }}:</p>
                            @foreach($plans as $plan)
                                @if($plan->price != null && $plan->on_show == 1)
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="customRadio-{{ $plan->id }}" name="plan_id" class="custom-control-input" value="{{ $plan->id }}">
                                    <label class="custom-control-label" for="customRadio-{{ $plan->id }}">{{ $plan->name }}</label>
                                </div>
                                @endif
                            @endforeach
                            <div class="text-right mt-1">
                                <button class="btn btn-sm btn-outline-blue" type="submit" id="sendFormPlans" disabled>{{ __('Сохранить') }}</button>
                            </div>
                        </form>
                    </div>
                    @endif
                    {{--<div>--}}
                        {{--@if($subscribe->active == 0)--}}
                            {{--<button class="btn btn-outline-blue" type="button" data-toggle="modal" data-target="#payActivateModal" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">--}}
                                {{--Активировать--}}
                            {{--</button>--}}
                        {{--@endif--}}
                        {{--@if($subscribe->active == 1 && $subscribe->end_at > \Carbon\Carbon::today())--}}
                            {{--<button class="btn btn-outline-blue" type="button" data-toggle="modal" data-target="#subscribeModal" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">--}}
                                {{--{{ __('Продлить') }}--}}
                            {{--</button>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                @endif
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab1" role="tablist">
                <li class="nav-item"><a class="nav-link active show" id="bot-tab" data-toggle="tab" href="#bot-tab-content" role="tab" aria-controls="home" aria-selected="true">Авточаты</a></li>
                <li class="nav-item"><a class="nav-link" id="page-tab" data-toggle="tab" href="#page-tab-content" role="tab" aria-controls="profile" aria-selected="false">Страницы</a></li>
                <li class="nav-item"><a class="nav-link" id="invoice-tab" data-toggle="tab" href="#invoice-tab-content" role="tab" aria-controls="contact" aria-selected="false">Счета</a></li>
            </ul>
            <div class="tab-content" id="myTab1Content">
                <div class="tab-pane fade active show" id="bot-tab-content" role="tabpanel" aria-labelledby="bot-tab">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                        <tr>
                            <th width="120">Наименование</th>
                            <th>Ссылка</th>
                            <th width="100">Дата создания</th>
                            <th width="90"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bots as $bot)
                            <tr>
                                <td>{{ $bot->name }}</td>
                                <td>
                                    <span id="page_slug_{{ $bot->id }}">https://getchat.me/{{ $bot->slug }}</span>
                                    <button class="btn float-right btn-sm btn-outline-blue ml-2" type="button"
                                            title="Копировать ссылку"
                                            onclick="copyPageToClipboard({{ $bot->id }})" style="border-radius:50%;">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($bot->created_at)->format('d.m.Y') ?? '' }}</td>
                                <td>
                                    <div class="form-inline">
                                        <a href="https://getchat.me/constructor/{{ $bot->id }}" target="_blank" class="btn btn-sm btn-outline-blue mr-1" style="border-radius:50%;">
                                            <i class="fa fa-wrench"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-blue" style="border-radius:50%;" data-toggle="modal" data-target="#editLinkModal" onclick="EditLink({{ $bot->id }}, '{{ $bot->slug }}')" title="{{ __('Редактирование ссылки') }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-blue ml-1" type="button"
                                                    id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    style="border-radius:50%;width:30px;height:30px;">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="dropdownMenuButton">
                                                @if($bot->temp_bot != null)
                                                <button class="dropdown-item" onclick="botConfirm({{ $bot->id }})"><i class="fa fa-check"></i> {{ __('Подтвердить')  }}</button>
                                                <button class="dropdown-item" onclick="botReset({{ $bot->id }})"><i class="fa fa-times"></i> {{ __('Сбросить')  }}</button>
                                                @endif
                                                <a class="dropdown-item" href="https://getchat.me/{{ $bot->slug }}" target="_blank">
                                                    <i class="far fa-eye"></i> {{ __('buttons.view') }}
                                                </a>
                                                <button class="dropdown-item" onclick="changeOwnerButtonClick({{ $bot->id }}, '{{ $bot->slug }}')" data-toggle="modal" data-target="#changeOwnerModal"><i class="fa fa-user"></i> {{ __('Изменить владельца') }}</button>
                                                <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $bot->id }})">
                                                    <i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="page-tab-content" role="tabpanel" aria-labelledby="page-tab">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                        <tr>
                            <th width="120">Наименование</th>
                            <th class="d-none d-md-table-cell">Ссылка</th>
                            <th width="100">Дата создания</th>
                            <th width="90"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pages as $key => $page)
                            <tr>
                                <td>{{ $page->name }}</td>
                                <td class="d-none d-md-table-cell">
                                    <span id="page_slug_{{ $key }}">https://getchat.me/{{ $page->slug }}</span>
                                    <button class="btn float-right btn-sm btn-outline-blue ml-2" type="button"
                                            title="Копировать ссылку"
                                            onclick="copyPageToClipboard({{ $key }})" style="border-radius:50%;">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($page->created_at)->format('d.m.Y') ?? '' }}</td>
                                <td>
                                    <div class="form-inline">
                                        <a href="https://getchat.me/constructor/{{ $page->id }}" target="_blank" class="btn btn-sm btn-outline-blue mr-1" style="border-radius:50%;">
                                            <i class="fa fa-wrench"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-blue" style="border-radius:50%;" data-toggle="modal" data-target="#editLinkModal" onclick="EditLink({{ $page->id }}, '{{ $page->slug }}')" title="{{ __('Редактирование ссылки') }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-blue ml-1" type="button"
                                                    id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    style="border-radius:50%;width:30px;height:30px;">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="dropdownMenuButton">
                                                @if($page->temp_bot != null)
                                                    <button class="dropdown-item" onclick="botConfirm({{ $page->id }})"><i class="fa fa-check"></i> {{ __('Подтвердить')  }}</button>
                                                    <button class="dropdown-item" onclick="botReset({{ $page->id }})"><i class="fa fa-times"></i> {{ __('Сбросить')  }}</button>
                                                @endif
                                                <a class="dropdown-item" href="https://getchat.me/{{ $page->slug }}" target="_blank">
                                                    <i class="far fa-eye"></i> {{ __('buttons.view') }}
                                                </a>
                                                <button class="dropdown-item" onclick="changeOwnerButtonClick({{ $page->id }}, '{{ $page->slug }}')" data-toggle="modal" data-target="#changeOwnerModal"><i class="fa fa-user"></i> {{ __('Изменить владельца') }}</button>
                                                <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $key }})">
                                                    <i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}
                                                </a>
                                                <a class="dropdown-item text-danger" href="#"  onclick="event.preventDefault();document.getElementById('delete-chat').submit();">
                                                    <i class="fa fa-trash"></i> {{ __('Удалить') }}
                                                </a>
                                                <form id="delete-chat" action="{{ route('manager.users.delete_chat', ['id' => $page->id, 'user_id' => $user->id]) }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="invoice-tab-content" role="tabpanel" aria-labelledby="invoice-tab">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                        <tr>
                            <th width="70">#</th>
                            <th width="120">Вид</th>
                            <th>Наименование</th>
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
                                <td>{{ $invoice->type->name }}</td>
                                <td>
                                    @if($invoice->plan != null)
                                        {{ $invoice->plan->name }}
                                    @else
                                        @if($invoice->service != null)
                                            {{ $invoice->service->name }}
                                        @endif
                                    @endif
                                </td>
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

    {{--<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"--}}
         {{--aria-hidden="true">--}}
        {{--<div class="modal-dialog modal-lg" role="document">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<h5 class="modal-title" id="exampleModalLabel">Выставить счет</h5>--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                        {{--<span aria-hidden="true">&times;</span>--}}
                    {{--</button>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<div id="plan_place"></div>--}}
                    {{--<div id="periodDiv" class="mt-2" style="display: none">--}}
                        {{--<strong>Период</strong>--}}
                        {{--<input class="form-control" type="number" id="periodMonth" min="1" max="12" value="1">--}}
                    {{--</div>--}}
                    {{--<div class="mt-3" id="amount_place">--}}
                        {{--<strong>Итого: </strong><span id="amount">0</span>--}}
                    {{--</div>--}}
                    {{--<textarea id="description" class="form-control mt-2" rows="3" placeholder="Описание(не обязательно)"></textarea>--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="CloseForm()">{{ __('Закрыть') }}</button>--}}
                    {{--<button type="button" class="btn btn-primary" onclick="PostForm()">{{ __('Сохранить') }}</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="modal fade" id="payActivateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
        {{--<div class="modal-dialog" role="document">--}}
            {{--<div class="modal-content">--}}
                {{--<form action="{{ route('manager.pay.activate') }}" method="post">--}}
                    {{--@csrf--}}
                    {{--<input type="hidden" name="user_id" value="{{ $user->id }}">--}}
                    {{--<div class="modal-header">--}}
                        {{--<h5 class="modal-title" id="exampleModalLabel">Выберите счет</h5>--}}
                        {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                            {{--<span aria-hidden="true">&times;</span>--}}
                        {{--</button>--}}
                    {{--</div>--}}
                    {{--<div class="modal-body">--}}
                        {{--@forelse($invoices as $invoice)--}}
                            {{--@if($invoice->paid == 1)--}}
                            {{--<div class="form-check">--}}
                                {{--<input class="form-check-input" type="radio" name="invoice_id" id="inv_{{ $invoice->id }}" value="{{ $invoice->id }}">--}}
                                {{--<label class="form-check-label" for="inv_{{ $invoice->id }}">--}}
                                    {{--{{ '#' . $invoice->id . ' Дата оплаты: ' . \Carbon\Carbon::parse($invoice->paid_at)->format('d.m.Y') }}--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        {{--@empty--}}
                            {{--<span>Данные отсутствуют</span>--}}
                        {{--@endforelse--}}
                        {{--<input class="form-control mt-3" name="date" type="date">--}}
                    {{--</div>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>--}}
                        {{--<button type="submit" class="btn btn-primary">{{ __('Активировать') }}</button>--}}
                    {{--</div>--}}
                {{--</form>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<div class="modal fade" id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="subscribeModalLabel" aria-hidden="true">--}}
        {{--<div class="modal-dialog" role="document">--}}
            {{--<div class="modal-content">--}}
                {{--<form action="{{ route('manager.pay.activate') }}" method="post">--}}
                    {{--@csrf--}}
                    {{--<input type="hidden" name="user_id" value="{{ $user->id }}">--}}
                    {{--<div class="modal-header">--}}
                        {{--<h5 class="modal-title" id="exampleModalLabel">{{ __('Продление подписки пользователя') }}</h5>--}}
                        {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                            {{--<span aria-hidden="true">&times;</span>--}}
                        {{--</button>--}}
                    {{--</div>--}}
                    {{--<div class="modal-body">--}}
                        {{--@forelse($invoices as $invoice)--}}
                            {{--@if($invoice->type->id == 1 && $invoice->paid == 1)--}}
                                {{--<div class="form-check">--}}
                                    {{--<input class="form-check-input" type="radio" name="invoice_id" id="inv{{ $invoice->id }}" value="{{ $invoice->id }}">--}}
                                    {{--<label class="form-check-label" for="inv{{ $invoice->id }}">--}}
                                        {{--{{ '#' . $invoice->id . ' Дата оплаты: ' . \Carbon\Carbon::parse($invoice->paid_at)->format('d.m.Y') }}--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                        {{--@empty--}}
                            {{--<span>Данные отсутствуют</span>--}}
                        {{--@endforelse--}}
                        {{--<input class="form-control mt-3" name="date" type="date">--}}
                    {{--</div>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>--}}
                        {{--<button type="submit" class="btn btn-primary">{{ __('Активировать') }}</button>--}}
                    {{--</div>--}}
                {{--</form>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--Изменение ссылки--}}
    <div class="modal fade" id="editLinkModal" tabindex="-1" role="dialog" aria-labelledby="editLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form" action="#">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Редактирование ссылки') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <input class="form-control" type="text" name="slug" id="slug">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="CloseForm()" data-dismiss="modal">{{ __('Отмена') }}</button>
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

    {{--Создание нового авточата--}}
    <div class="modal fade" id="createBotModal" tabindex="-1" role="dialog" aria-labelledby="createBotModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="createBotForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Создание нового авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Ссылка:') }}</strong>
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input id="link" class="form-control" type="text" name="link">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>
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
        /*-----------------------Выставление счета------------------------------*/
        let billing_token = '{{ config('app.billing_token') }}';
        let billing_url = '{{ config('app.billing_url') }}';
        let typeId = null;
        let planId = null;
        let serviceId = null;
        let amount = 0;
        let planAmount = 0;
        let serviceAmount = 0;
        let subscribeAmount = 0;
        let url = '';
        let strPlan = '';
        let strService = '';
        let period = $('#periodMonth').val();
        let plan_discount = 0;

        $('#periodMonth').bind('keyup mouseup', function() {
            period = this.value;
            Itogo();
        });

        function Load()
        {
            let str = '';
            $('#plan_place').empty();
            $.ajax({
                type: "GET",
                url: billing_url + "/type-invoice",
                dataType: 'json',
                async: false,
                headers: {
                    "Authorization": "Basic " + billing_token
                },
                success: function (request) {
                    str += '<strong>Тип счета:</strong>';
                    $.each(request, function (key, value) {
                        str += '<div class="form-check">';
                        str += '    <input class="form-check-input" type="radio" onclick="ChoiseType(' + value.id + ')" name="type_id" id="typeRadios' + key + '" value="' + value.id + '">';
                        str += '    <label class="form-check-label" for="typeRadios' + key + '">' + value.name + '</label>';
                        str += '</div>';
                    });
                    $('#plan_place').append(str);
                    str = '';
                }
            });
        }

        function ChoiseType(id)
        {
            typeId = id;
            strPlan = '';
            strService = '';
            $('#planPlace').remove(); // Удаляем если имеется
            $('#servicePlace').remove();
            planAmount = 0;
            serviceAmount = 0;
            subscribeAmount = 0;

            if(id === 1) {
                $.ajax({
                    type: "GET",
                    url: billing_url + "/subscribe/" + '{{ $user->id }}',
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        // console.log(request.data/*.plan.name + ', ' +request.data.plan.price*/);
                        planId = request.data.plan.id;
                        plan_discount = request.data.plan.discount;
                        // if(planId > 3) {
                        //     $('#periodDiv').css('display', 'block');
                        // }
                        // console.log(planId);
                        subscribeAmount = parseInt(parseFloat(request.data.plan.price).toFixed(0)) || 0;
                        $('#periodDiv').css('display', 'block');
                    }
                });
            }
            if(id === 2) {
                $('#periodMonth').val(1);
                period = 1;
                $('#periodDiv').css('display', 'none');
                $.ajax({
                    type: "GET",
                    url: billing_url + "/plans",
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        // console.log(request);
                        strPlan += '<div class="mt-2" id="planPlace">';
                        strPlan += '<strong>Тарифный план:</strong>';
                        $.each(request.data, function (key, value) {
                            if(value.id > 3){
                                strPlan += '<div class="form-check">';
                                strPlan += '    <input class="form-check-input" type="radio" name="plan_id" onclick="ChoisePlan('+value.id+', \''+value.code+'\', '+value.price+', ' +value.discount+ ')" id="plansRadios' + key + '" value="' + value.id + '">';
                                strPlan += '    <label class="form-check-label" for="plansRadios' + key + '">' + value.name + '</label>';
                                strPlan += '</div>';
                            }
                        });
                        strPlan += '<div class="form-check mt-3">' +
                                  '    <input class="form-check-input" type="checkbox" id="developChat" onclick="ChoiseDevelop()">' +
                                  '    <label class="form-check-label" for="developChat">Разработка авточата</label>' +
                                  '</div>';
                        strPlan += '</div>';
                        $('#plan_place').append(strPlan);
                        strPlan = '';
                    }
                });
                $('#plan_place').append(strService);
            }
            if(id === 3) {
                $('#periodMonth').val(1);
                period = 1;
                $('#periodDiv').css('display', 'none');
                $.ajax({
                    type: "GET",
                    url: billing_url + "/services",
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        strService += '<div class="mt-2" id="servicePlace">';
                        strService += '<strong>Услуги:</strong>';
                        $.each(request, function (key, value) {
                            strService += '<div class="form-check">';
                            strService += '    <input class="form-check-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';
                            strService += '    <label class="form-check-label" for="serviceRadios'+key+'">'+value.name+'</label>';
                            strService += '</div>';
                        });
                        strService += '</div>';
                        $('#plan_place').append(strService);
                        strService = '';
                    }
                });
            }
            Itogo();
        }

        function ChoisePlan(id, code, price, discount)
        {
            plan_discount = discount;
            // console.log(discount);
            planAmount = 0;
            serviceAmount = 0;
            planId = id;
            if(planId > 3) {
                $('#periodDiv').css('display', 'block');
            } else {
                $('#periodMonth').val(1);
                period = 1;
                $('#periodDiv').css('display', 'none');
            }
            planAmount +=  price;
            Itogo();
            LoadServices()
        }

        function ChoiseDevelop()
        {
            LoadServices();
        }

        function LoadServices()
        {
            strService = '';
            url = '';
            $('#servicePlace').remove();
            if($('#developChat').is(':checked')) {
                $.ajax({
                    type: "GET",
                    url: billing_url + "/service/plan-not-null", //url,
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        strService += '<div class="mt-2" id="servicePlace">';
                        strService += '<strong>Услуги:</strong>';
                        $.each(request, function (key, value) {
                            strService += '<div class="form-check">';
                            strService += '    <input class="form-check-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';
                            strService += '    <label class="form-check-label" for="serviceRadios'+key+'">'+value.name+'</label>';
                            strService += '</div>';
                        });
                        strService += '</div>';
                        $('#plan_place').append(strService);
                        strService = '';
                    }
                });
            } else {
                $('#servicePlace').remove();
                serviceAmount = 0;
                Itogo();
            }
        }

        function ChoiseService(id, price)
        {
            serviceAmount = 0;
            serviceId = id;
            serviceAmount += price;
            Itogo();
        }

        function Itogo()
        {
            if(plan_discount === 0 || plan_discount === undefined) {
                plan_discount = 1;
            }
            if(period >= 12) {
                amount = (subscribeAmount * period - ((subscribeAmount * period) * (plan_discount / 100))) + (planAmount * period - ((planAmount * period) * (plan_discount / 100))) + (serviceAmount - (serviceAmount * (plan_discount / 100)));
            } else {
                amount = (subscribeAmount * period) + (planAmount * period) + serviceAmount;
            }
            $('#amount').text(amount);
        }

        // Отправка данных на сервер
        function PostForm()
        {
            period = parseInt($('#periodMonth').val());
            let data = {
                manager_id: '{{ Auth::user()->id }}',
                user_id: '{{ $user->id }}',
                amount: amount,
                type_id: typeId,
                plan_id: planId,
                service_id: serviceId,
                period: period,
                description: $('#description').val()
            };
            // console.log(data);
            $.ajax({
                type: "POST",
                url: billing_url + "/invoice",
                data: data,
                dataType: 'json',
                headers: {
                    "Authorization": "Basic " + billing_token
                },
                success: function (request) {

                    if(request.error === undefined) {
                        // console.log(request);
                        CloseForm();
                    }
                }
            });
        }

        // Закрытие формы
        function CloseForm()
        {
            typeId = 0;
            planId = 0;
            serviceId = 0;
            amount = 0;
            planAmount = 0;
            serviceAmount = 0;
            period = 1;
            $('#periodMonth').val(1);
            $('#periodDiv').css('display', 'none');
            url = '';
            strPlan = '';
            strService = '';
            $('#planPlace').remove();
            $('#servicePlace').remove();
            Itogo();
            $('#amount').text(amount);
            $('#invoiceModal').modal('hide');
            location.reload();
        }
        /*-----------------------Выставление счета------------------------------*/


        $('.dataTable').DataTable({
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[30, 50, 100, -1], [30, 50, 100, "Все"]],
            // "pagingType": "full_numbers",
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
            }
        });
        $('.dataTable').attr('style','border-collapse: collapse !important');



        /*-------------*/
        $('.custom-control-input').on('click', function() {
            $('#sendFormPlans').removeAttr('disabled');
        });




        $('#change-plan').submit(function ( e ) {
            let data;

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
        let invoiceId = 0;
        let datePay = '';
        function selectInvoice(id) {
            invoiceId = id;
            $('#dropdownCalendar_' + id).show();
        }
        $( ".datepicker" ).datepicker({
            onSelect: function () {
                datePay = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker('getDate'));
                // console.log(datePay);
                let data = {
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

        /*------------------------Редактирование ссылки на страницу----------------------------*/
        function EditLink(id, slug) {
            $('#id').val(id);
            $('#slug').val(slug);
            $('#editLinkModal').show();
        }

        $('#form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: '{{ route('edit_link') }}',
                data: $('#form').serialize(),
                success: function (request) {
                    if(request.error === 0) {
                        editCloseForm();
                        location.reload();
                    }
                }
            });
        });

        function editCloseForm() {
            $('#id').val();
            $('#slug').val();
            $('#editLinkModal').hide();
        }



        /*-------------------Смена владельца-----------------------*/
        let email = '';
        let str = '';
        function changeOwnerButtonClick(id, slug){
            $('#company_owner_id').val(id);
            $('#owner_link_slug').text(slug);
        }
        $('#user').on('input', function(){
            email = $(this).val();
            let data = {
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

        /*-------------------------создание нового авточата----------------------------*/
        $('#createBotForm').on('submit', function (e) {
            e.preventDefault();
            console.log($('#createBotForm').serialize());
            $.ajax({
                type: "post",
                url: "{{ route('create_bot') }}",
                data: $('#createBotForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (request) {
                    if(request.error === 0) {
                        // CloseChangeOwnerForm();
                        $('#createBotModal').hide();
                        $('#link').val('');
                        location.reload();
                        // let url = "https://getchat.me/constructor/" + request.company.slug;
                        // window.open(url, '_blank');
                    } else {
                        console.log(request)
                    }
                }
            });
        });

        // Скопировать счет
        function copyInvoiceToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#invoice_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }

        function botConfirm(id) {
            let data = {
                id: id
            };

            $.ajax({
                type: "get",
                url: "{{ route('manager.bots.confirmBot') }}",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (request) {
                    if(request.error === 0) {
                        location.reload();
                    } else {
                        console.log(request)
                    }
                }
            });
        }

        function botReset(id) {
            let data = {
                id: id
            };
            $.ajax({
                type: "get",
                url: "{{ route('manager.bots.resetBot') }}",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (request) {
                    if(request.error === 0) {
                        location.reload();
                    } else {
                        console.log(request)
                    }
                }
            });
        }
    </script>
@endsection
