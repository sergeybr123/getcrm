@extends('layouts.app')

@section('title', __('users.users'))

@section('styles')
    <link href="{{ asset('vendors/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        #DataTables_Table_0_wrapper {
            padding: 8px 0 0 0;
        }
    </style>
<style>
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
                        <strong>Дата
                            регистрации: </strong>{{ \Carbon\Carbon::parse($user->created_at)->format('d.m.Y') }}
                    </div>
                </div>
                <div class="form-inline" style="align-items: normal;">
                    <a href="{{ route('manager.users.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-outline-blue" style="border-radius:50%;width:30px;height:30px;"
                       title="{{ __('Редактировать') }}">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-blue ml-1"
                       style="border-radius:50%;width:30px;height:30px;" data-toggle="modal" data-target="#invoiceModal"
                       title="Выставить счет" onclick="Load();">
                        <i class="fa fa-file-invoice"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;width:30px;height:30px;"
                       title="Добавить авточат">
                        <i class="fa fa-comments"></i>
                    </a>
                    {{--<div class="dropdown">--}}
                        {{--<button class="btn btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"--}}
                                {{--data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
                                {{--style="border-radius:50%;width:30px;height:30px;">--}}
                            {{--<i class="fa fa-ellipsis-v"></i>--}}
                        {{--</button>--}}
                        {{--<div class="dropdown-menu dropdown-menu-right mt-1" aria-labelledby="dropdownMenuButton">--}}
                            {{--<a class="dropdown-item" href="#" target="_blank">{{ __('Создать авточат') }}</a>--}}
                            {{--<button class="dropdown-item" data-toggle="modal"--}}
                                    {{--data-target="#invoiceModal">{{ __('Выставить счет') }}</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </div>


    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="d-flex w-100 justify-content-between">
                <div>
                    <p class="h4">{{ __("Тарифный план: ") . $plan->plan->name }}</p>
                    <span>{{ __('Дата регистрации: ') . \Carbon\Carbon::parse($plan->created_at->date)->format('d.m.Y') }}</span><br>
                    <strong>Подписка с:</strong> {{ \Carbon\Carbon::parse($plan->start_at)->format('d.m.Y') }}
                    <strong>по:</strong>
                    @if($plan->end_at != null)
                        {{ \Carbon\Carbon::parse($plan->end_at)->format('d.m.Y') }}
                    @else
                        {{ __('Бессрочная') }}
                    @endif
                    <br><strong>Статус:</strong>
                    @if($plan->active == 1)
                        <span class="badge badge-success">Активная</span>
                    @else
                        <span class="badge badge-danger">Не активная</span>
                    @endif
                </div>
                <div>
                    @if($plan->plan_id == 4 || $plan->active == 0)
                        <button class="btn btn-outline-blue" type="button" data-toggle="modal" data-target="#payActivateModal" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">
                            Активировать
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <ul class="nav nav-tabs nav-justified" id="myTab1" role="tablist">
                <li class="nav-item"><a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Авточаты</a></li>
                <li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Страницы</a></li>
                <li class="nav-item"><a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Счета</a></li>
            </ul>
            <div class="tab-content" id="myTab1Content">
                <div class="tab-pane p-0 fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <ul class="list-group">
                        @forelse($bots as $key => $bot)
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $bot->name ?? $bot->slug }}</h5>
                                    <small class="text-muted" title="Дата создания">
                                        {{ \Carbon\Carbon::parse($bot->created_at)->format('d.m.Y') }}
                                    </small>
                                </div>
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1">
                                        <span id="slug_{{ $key }}">https://getchat.me/{{ $bot->slug }}</span>
                                        <button class="btn btn-sm btn-outline-blue ml-2" type="button"
                                                title="Копировать ссылку"
                                                onclick="copyToClipboard({{ $key }})" style="border-radius:50%;">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </p>
                                    <div class="form-inline">
                                        <a href="#" class="btn btn-sm btn-outline-blue" style="border-radius:50%;">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        {{--<a href="#" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;">--}}
                                        {{--<i class="fa fa-chart-line"></i>--}}
                                        {{--</a>--}}
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-blue ml-1" type="button"
                                                    id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    style="border-radius:50%;width:30px;height:30px;">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="https://getchat.me/{{ $bot->slug }}"
                                                   target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                                <a class="dropdown-item" href="#" onclick="copyToClipboard({{ $key }})"><i
                                                            class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                                <a class="dropdown-item text-danger" href="#"><i
                                                            class="fa fa-trash"></i> {{ __('buttons.remove') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">Авточаты отсутствуют</li>
                        @endforelse
                    </ul>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                        @foreach($pages as $key => $page)
                            <tr>
                                <td>{{ $page->slug }}</td>
                                <td>
                                    <span id="page_slug_{{ $key }}">https://getchat.me/{{ $page->slug }}</span>
                                    <button class="btn btn-sm btn-outline-blue ml-2" type="button"
                                            title="Копировать ссылку"
                                            onclick="copyPageToClipboard({{ $key }})" style="border-radius:50%;">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($page->created_at)->format('d.m.Y') ?? '' }}</td>
                                <td>
                                    <div class="form-inline">
                                        <a href="#" class="btn btn-sm btn-outline-blue" style="border-radius:50%;">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;">
                                            <i class="fa fa-chart-line"></i>
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
                                                <a class="dropdown-item" href="https://getchat.me/{{ $page->slug }}" target="_blank">
                                                    <i class="far fa-eye"></i> {{ __('buttons.view') }}
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $key }})">
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
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
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
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $key => $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->types->name }}</td>
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
                                    @if($invoice->paid == 0)
                                        <span class="badge badge-secondary">Не оплачен</span>
                                    @else
                                        <span class="badge badge-success">Оплачен</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d.m.Y') ?? '' }}</td>
                                <td class="text-center">
                                    @if($invoice->paid_at != null)
                                        {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d.m.Y') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выставить счет</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="plan_place"></div>
                    <div class="mt-3" id="amount_place">
                        <strong>Итого: </strong><span id="amount">0</span>
                    </div>
                    <textarea id="description" class="form-control mt-2" rows="3" placeholder="Описание(не обязательно)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="CloseForm()">Отмена</button>
                    <button type="button" class="btn btn-primary" onclick="PostForm()">Сохранить</button>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="payActivateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('manager.pay.activate') }}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Выберите счет</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @forelse($invoices as $item)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invoice_id" id="inv{{ $invoice->id }}" value="{{ $invoice->id }}">
                                <label class="form-check-label" for="inv{{ $invoice->id }}">
                                    {{ $invoice->id }}
                                </label>
                            </div>
                        @empty
                            <span>Данные отсутствуют</span>
                        @endforelse
                        <input class="form-control mt-3" name="date" type="date">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Активировать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





@endsection
@section('scripts')
    <script src="{{ asset('vendors/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        function copyToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#slug_' + key).text()).select();
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


        /*-----------------------Выставление счета------------------------------*/
        var billing_token = '{{ config('app.billing_token') }}';
        var billing_url = '{{ config('app.billing_url') }}';
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

            if(id == 1) {
                $.ajax({
                    type: "GET",
                    url: billing_url + "/subscribe/" + '{{ $user->id }}',
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        // strService += '<div class="mt-2" id="servicePlace">';
                        // strService += '<strong>Услуги:</strong>';
                        // $.each(request, function (key, value) {
                        //     strService += '<div class="form-check">';
                        //     strService += '    <input class="form-check-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';
                        //     strService += '    <label class="form-check-label" for="serviceRadios'+key+'">'+value.name+'</label>';
                        //     strService += '</div>';
                        // });
                        // strService += '</div>';
                        // $('#plan_place').append(strService);
                        // strService = '';
                        console.log(request.data/*.plan.name + ', ' +request.data.plan.price*/)
                        subscribeAmount = parseInt(parseFloat(request.data.plan.price).toFixed(0)) || 0;
                    }
                });
            }

            if(id == 2) {
                $.ajax({
                    type: "GET",
                    url: billing_url + "/plans",
                    dataType: 'json',
                    async: false,
                    headers: {
                        "Authorization": "Basic " + billing_token
                    },
                    success: function (request) {
                        strPlan += '<div class="mt-2" id="planPlace">';
                        strPlan += '<strong>Тарифный план:</strong>';
                        $.each(request.data, function (key, value) {
                            strPlan += '<div class="form-check">';
                            strPlan += '    <input class="form-check-input" type="radio" name="plan_id" onclick="ChoisePlan('+value.id+', \''+value.code+'\', '+value.price+')" id="plansRadios' + key + '" value="' + value.id + '">';
                            strPlan += '    <label class="form-check-label" for="plansRadios' + key + '">' + value.name + '</label>';
                            strPlan += '</div>';
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
            if(id == 3) {
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

        function ChoisePlan(id, code, price)
        {
            planAmount = 0;
            serviceAmount = 0;
            planId = id;
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
                // if(planId === 1) {
                //     url = billing_url + "/service/plan-not-null";
                // } else {
                //     url = billing_url + "/service/" + planId + "/plan";
                // }
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
            amount = subscribeAmount + planAmount + serviceAmount;
            $('#amount').text(amount);
        }

        // Отправка данных на сервер
        function PostForm()
        {
            let data = {
                manager_id: '{{ Auth::user()->id }}',
                user_id: '{{ $user->id }}',
                amount: amount,
                type_id: typeId,
                plan_id: planId,
                service_id: serviceId,
                description: $('#description').val()
            };
            $.ajax({
                type: "POST",
                url: billing_url + "/invoice",
                data: data,
                dataType: 'json',
                headers: {
                    "Authorization": "Basic " + billing_token
                },
                success: function (request) {
                    if(request.error == 0) {
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
            url = '';
            strPlan = '';
            strService = '';
            $('#planPlace').remove();
            $('#servicePlace').remove();
            Itogo();
            $('#invoiceModal').modal('hide')
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
    </script>
@endsection
