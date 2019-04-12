@extends('layouts.app')

@section('title', __('Счета и платежи'))

@section('styles')
<link href="{{ asset('js/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<link href="{{ asset('js/jquery-ui/jquery-ui.theme.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <h1>{{ __('Счета и платежи') }} (@isset($all->meta){{ $all->meta->total }}@endisset)</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <form>
                <div class="mb-3 row">
                    <div class="col-md-2">
                        <select class="form-control" name="searchFilter">
                            <option value="1" {{ $filter == 1 ? 'selected' : '' }}>{{ __('По номеру счета') }}</option>
                            <option value="2" {{ $filter == 2 ? 'selected' : '' }}>{{ __('По email пользователя') }}</option>
                            <option value="3" {{ $filter == 3 ? 'selected' : '' }}>{{ __('По телефону пользователя') }}</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <input class="form-control" name="searchText" value="{{ $search }}" type="text">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-blue btn-block" type="submit"><i class="fa fa-search"></i> {{ __('Поиск') }}</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="80">#</th>
                    <th>Пользователь</th>
                    {{--<th width="130">Телефон</th>--}}
                    <th>Тип платежа</th>
                    <th width="90">Сумма</th>
                    <th width="50">Оплата</th>
                    <th width="130">Дата создания</th>
                    <th width="120">Дата платежа</th>
                    @permission('confirm-pay')
                        <th width="50"></th>
                    @endpermission
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $key => $invoice)
                <tr>
                    <td>
                        {{ $invoice[1]->id }}
                    </td>
                    <td>
                        <span id="link_{{ $invoice[1]->id }}" style="display:none;">https://getchat.me/order/pay/{{ $invoice[1]->id }}</span>
                        @if($invoice[1]->status == 'active')
                            <button class="float-right btn btn-outline-blue btn-sm" title="{{ __('Скопировать ссылку на оплату') }}"
                                    onclick="copyPageToClipboard({{ $invoice[1]->id }})" type="button"><i class="fa fa-copy"></i>
                            </button>
                        @endif
                        @if($invoice[0] != null)
                            <a href="{{ route('manager.users.show', ['id' => $invoice[0]['id']]) }}">
                                {{ $invoice[0]['email'] }}
                            </a>
                            @if($invoice[1]->description != null)
                            <span><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="{{ $invoice[1]->description }}"></i></span>
                            @endif
                        @endif
                    </td>
                    {{--<td>--}}
                        {{--@if($invoice[0]['phone'] != null)--}}
                            {{--{{ '+' . $invoice[0]['phone']['country_code'] . $invoice[0]['phone']['phone'] }}--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    <td>
                        @if($invoice[1]->plan)
                            <b>{{ $invoice[1]->type->name }}</b> - <i>{{ $invoice[1]->plan->name }}</i>
                        @else
                            {{ $invoice[1]->type->name . ' ' . $invoice[1]->service->name }}
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($invoice[1]->amount, 0, '.', '') }} тг.
                    </td>
                    <td class="text-center">
                        @if($invoice[1]->status == 'active')
                            <span class="badge badge-danger">Нет</span>
                        @elseif($invoice[1]->status == 'paid')
                            <span class="badge badge-success">Да</span>
                        @else
                            <span class="badge badge-warning">Завершен</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($invoice[1]->created_at)->format('d.m.Y') }}
                    </td>
                    <td class="text-center">
                        @if($invoice[1]->paid_at != null)
                        {{ \Carbon\Carbon::parse($invoice[1]->paid_at)->format('d.m.Y') ?? '' }}
                        @endif
                    </td>
                    @permission('confirm-pay')
                    <td>
                        @if($invoice[1]->status == 'active')
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-blue" title="Подтвердить оплату" id="dropdownMenuButton_{{ $invoice[1]->id }}" data-toggle="dropdown" onclick="selectInvoice({{ $invoice[1]->id }})"><i class="fa fa-credit-card"></i></button>
                                <div id="dropdownCalendar_{{ $invoice[1]->id }}" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <div style="height:27px;padding-right:10px;">
                                        <button type="button" class="close" aria-label="Close" onclick="closeDatapicker({{ $invoice[1]->id }})">
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
                @empty
                @endforelse
                </tbody>
            </table>
            @isset($all->meta)
                <ul class="pagination justify-content-end">
                    <li class="page-item {{ ($all->meta->current_page - 1 <= 0) ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $all->meta->current_page - 1 }}" tabindex="-1">Предыдущая</a>
                    </li>
                    <li class="page-item {{ ($all->meta->current_page == $all->meta->last_page) ? 'disabled' : '' }}">
                        <a class="page-link" href="?page={{ $all->meta->current_page + 1 }}">Следующая</a>
                    </li>
                </ul>
            @endisset
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>
                    <button type="button" class="btn btn-primary">{{ __('Сохранить') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip();

        let invoiceId = 0;
        let datePay = '';
        let billing_token = '{{ config('app.billing_token') }}';
        let billing_url = '{{ config('app.billing_url') }}';
        function selectInvoice(id) {
            invoiceId = id;
            $('#dropdownCalendar_' + id).show();
        }
        $( ".datepicker" ).datepicker({
            onSelect: function () {
                datePay = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker('getDate'));
                let data = {
                    id: invoiceId,
                    date: datePay
                };
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
                        // console.log(request);
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

        function copyPageToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#link_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }
    </script>
@endsection