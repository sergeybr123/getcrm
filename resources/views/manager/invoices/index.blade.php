@extends('layouts.app')

@section('title', __('Счета и платежи'))

@section('styles')
<link href="{{ asset('js/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<link href="{{ asset('js/jquery-ui/jquery-ui.theme.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <h1>{{ __('Счета и платежи') }}</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="80">#</th>
                    <th>Пользователь</th>
                    <th width="130">Телефон</th>
                    <th width="120">Тип платежа</th>
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
                        @if($invoice[0] != null)
                            <a href="{{ route('manager.users.show', ['id' => $invoice[0]['id']]) }}">
                                {{ $invoice[0]['email'] }}
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($invoice[0]['phone'] != null)
                            {{ '+' . $invoice[0]['phone']['country_code'] . $invoice[0]['phone']['phone'] }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $invoice[1]->types->name }}
                    </td>
                    <td class="text-right">
                        {{ number_format($invoice[1]->amount, 0, '.', '') }} тг.
                    </td>
                    <td class="text-center">
                        @if($invoice[1]->paid == 0)
                            <span class="badge badge-danger">Нет</span>
                        @else
                            <span class="badge badge-success">Да</span>
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
                        @if($invoice[1]->paid == 0)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-info" title="Подтвердить оплату" id="dropdownMenuButton_{{ $invoice[1]->id }}" data-toggle="dropdown" onclick="selectInvoice({{ $invoice[1]->id }})"><i class="fa fa-credit-card"></i></button>
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
            <ul class="pagination justify-content-end">
                <li class="page-item {{ ($all->current_page - 1 <= 0) ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $all->current_page - 1 }}" tabindex="-1">Предыдущая</a>
                </li>
                <li class="page-item {{ ($all->current_page == $all->last_page) ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $all->current_page + 1 }}">Следующая</a>
                </li>
            </ul>
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
    <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
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
    </script>
@endsection