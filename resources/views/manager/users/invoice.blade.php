@extends('layouts.app')

@section('title', __('Выставление счета'))

@section('styles')
    <link href="{{ asset('js/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui.theme.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="row mt-5">
        <div class="col-md-6 col-sm-12 offset-md-3">
            <div class="card card-accent-primary mt-3">
                <p class="h3 card-title pt-3 pl-3">{{ __('Пользователь:') }} <strong>{{ $user->email }}</strong></p>
                <div class="card-body">
                    <div id="block_plan">
                        @foreach($plans as $item)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" onclick="ChoiseType({{ $item->id }})" name="plan_id" id="planRadios_{{ $item->id }}" value="{{ $item->id }}" {{ ($item->id == $user->subscribe->plan_id) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="planRadios_{{ $item->id }}">{{ $item->name }}</label>
                            </div>
                        @endforeach
                            <p class="mb-0">Период</p>
                            <input id="plan_quantity" class="form-control" name="plan_quantity" type="number" min="0" max="12" value="0">
                            <hr>
                    </div>
                    <div id="block_service">
                        @if($services_service)
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="serviceCheck" onchange="GetRef()">
                                <label class="custom-control-label" for="serviceCheck">{{ $services_service->name }}</label>
                            </div>
                            <hr>
                        @endif
                    </div>
                    <div id="block_bot">
                        @if($services_bot)
                            <div class="form-inline">
                                <label for="bot_service" class="mr-2">{{ __('Дополнительный авточат') }}</label>
                                <input class="form-control" type="number" id="botCheck" onchange="GetRef()" min="{{ $user->subscribe->plan->bot_count }}" value="{{ $user->subscribe->bot_count }}">
                            </div>
                            <hr>
                        @endif
                    </div>
                    <div id="block_bonus">
                        @if($services_bonus)
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="bonusCheck" onclick="ChoiseBonus()">
                                <label class="custom-control-label" for="bonusCheck">{{ $services_bonus->name }}</label>
                            </div>
                            <hr>
                        @endif
                    </div>
                    <div>
                        <div class="dropdown">
                            <p class="mb-0">Дата начала подписки</p>
                            <input class="form-control" type="text" name="start_subscribe" id="datepickerStartSubscribe">
                            <div id="dropdownCalendar" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <div class="datepicker"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3" id="amount_place">
                        <strong>Итого: </strong><span id="amount">0</span>
                    </div>
                    <textarea id="description" class="form-control mt-2" rows="3" placeholder="Описание(не обязательно)"></textarea>
                </div>
                <div class="card-footer">
                    <a href="{{ route('manager.users.show', $user->id) }}" class="btn btn-secondary">{{ __('Отмена') }}</a>
                    <button id="send_invoice" type="button" class="btn btn-primary" onclick="PostForm()" disabled>{{ __('Сохранить') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    var ref = null;
    var billing_token = '{{ config('app.billing_token') }}';
    var billing_url = '{{ config('app.billing_url') }}';
    var manager_id = {!! $manager_id !!};
    var user = {!! json_encode($user) !!};

    console.log(manager_id);

    $(document).ready(function(){
        // Load();
    });

    function ChoiseBonus(){

    }

    $( function() {
        $( "#datepickerStartSubscribe" ).datepicker({ dateFormat: 'dd.mm.yyyy' });
    });

    {{--var ref = {!! json_encode($ref->data) !!};--}}

    function GetRef() {
        var serviceDevelop = $('#serviceCheck').prop('checked') || null;
        var botCheck = $("#botCheck").val() || null;
        var serviceDevelop = $("#bonusCheck").prop("checked") ? 1 : 0;
        var data = {
            manager_id: manager_id,
            plan: $('#period').val(),
            plan_quantity: plan_quantity,
            serviceCheck: serviceDevelop,
            bots: botCheck,
            start_subscribe: $('#datepickerStartSubscribe').val(),
            // ref_id: ref.id,
        };

        // if

        $.ajax({
            type: "POST",
            url: billing_url + "/ref/get-ref-inv",
            dataType: 'json',
            async: false,
            data: data,
            processData: false,
            headers: {
                "Authorization": "Basic " + billing_token
            },
            success: function (request) {
                console.log(request);
                // if(request.error === 0) {
                //     $('#dropdownCalendar_' + invoiceId).hide();
                //     location.reload();
                // }
            }
        });
        // console.log(data);
    }
    // setTimeout(GetRef, 2000);

    // function selectDateSubscribe() {
    //     $('#dropdownCalendar').show();
    // }
    // $( ".datepicker" ).datepicker({
    //     onSelect: function () {
    //         datePay = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker('getDate'));
    //         // console.log(datePay);
    //         var data = {
    //             id: invoiceId,
    //             date: datePay
    //         };
    //         // console.log(data);
    //         $.ajax({
    //             type: "POST",
    //             url: billing_url + "/pay-with-day",
    //             dataType: 'json',
    //             async: false,
    //             data: data,
    //             headers: {
    //                 "Authorization": "Basic " + billing_token
    //             },
    //             success: function (request) {
    //                 console.log(request);
    //                 if(request.error === 0) {
    //                     $('#dropdownCalendar_' + invoiceId).hide();
    //                     location.reload();
    //                 }
    //             }
    //         });
    //     }
    // });
    // function closeDatapicker(id) {
    //     $('#dropdownCalendar_' + id).hide();
    // }

    // function Load() {
{{--        var ref = {!! json_encode($ref->data) !!};--}}
{{--        --}}{{--var types = {!! $invoice_types !!} --}}{{----}}{{--{!! $invoice_types !!}--}}{{----}}{{--;--}}
{{--        var plans = {!! $plans !!};--}}
{{--        var services = {!! $services !!};--}}
{{--        var user = {!! $user !!};--}}
{{--        var subscribe = {!! $user->subscribe !!};--}}
{{--        var sub_plan = {!! $user->subscribe->plan !!};--}}
{{--        --}}{{--var last_inv = {!! $user->subscribe->last_inv->ref_invoice !!};--}}
{{--        // console.log(ref);--}}
{{--        var t_str = '';--}}
{{--        var p_str = '';--}}
{{--        var s_str = '';--}}
{{--        var plan_arr = [];--}}
{{--        // $.each(types, function (key, value) {--}}
{{--        //     t_str += '<div class="custom-control custom-radio">';--}}
{{--        //     t_str += '    <input class="custom-control-input" type="radio" onclick="ChoiseType(' + value.id + ')" name="type_id" id="typeRadios' + key + '" value="' + value.id + '">';--}}
{{--        //     t_str += '    <label class="custom-control-label" for="typeRadios' + key + '">' + value.name + '</label>';--}}
{{--        //     t_str += '</div>';--}}
{{--        // });--}}
{{--        // $('#types_invoice_block').append(t_str + '<hr>');--}}
{{--        // t_str = '';--}}
{{--        p_str += '<p class="mb-0">Тарифный план</p>';--}}
{{--        $.each(plans, function (key, value) {--}}
{{--            plan_arr.push(value.id);--}}
{{--            // console.log(value);--}}
{{--            p_str += '  <div class="custom-control custom-radio custom-control-inline">';--}}
{{--            p_str += '    <input class="custom-control-input" type="radio" onclick="ChoisePlan(' + value.id + ')" name="plan_id" id="planRadios' + key + '" value="' + value.id + '">';--}}
{{--            p_str += '    <label class="custom-control-label" for="planRadios' + key + '">' + value.name + '</label>';--}}
{{--            p_str += '  </div>';--}}
{{--            // p_str += '</div>';--}}
{{--        });--}}
{{--        p_str += '<p class="mb-0">Период</p>';--}}
{{--        p_str += '<input class="form-control" type="number" min="1" max="12" value="1">';--}}
{{--        $('#plan_block').append(p_str + '<hr>');--}}
{{--        p_str = '';--}}
{{--        $.each(services, function (key, value) {--}}
{{--            // console.log(value);--}}
{{--            if(value.type === 'service') {--}}
{{--                s_str += '<div class="custom-control custom-checkbox">';--}}
{{--                s_str += '  <input class="custom-control-input" type="checkbox" id="serviceCheck' + key + '" onclick="ChoiseService(' + value.id + ')">';--}}
{{--                s_str += '  <label class="custom-control-label" for="serviceCheck' + key + '">' + value.name + '</label>';--}}
{{--                s_str += '</div><hr>'--}}
{{--            } else if(value.type === 'bot') {--}}
{{--                s_str += '<div class="custom-control custom-checkbox">';--}}
{{--                s_str += '  <input class="custom-control-input" type="checkbox" onclick="ChoiseServiceBonus(' + value.id + ')" name="plan_id" id="serviceRadios' + key + '" value="' + value.id + '" {{ $user->subscribe->active == 0 ? "disabled" : "" }}>';--}}
{{--                s_str += '  <label class="custom-control-label" for="serviceRadios' + key + '">' + value.name + '</label>';--}}
{{--                s_str += '</div><hr>';--}}
{{--            } else if(value.type === 'bonus') {--}}
{{--                s_str += '<div class="custom-control custom-checkbox">';--}}
{{--                s_str += '    <input class="custom-control-input" type="checkbox" onclick="ChoiseServiceBonus(' + value.id + ')" name="plan_id" id="serviceRadios' + key + '" value="' + value.id + '" {{ $user->subscribe->active == 0 ? "disabled" : "" }}>';--}}
{{--                s_str += '    <label class="custom-control-label" for="serviceRadios' + key + '">' + value.name + '</label>';--}}
{{--                s_str += '</div><hr>';--}}
{{--            }--}}
{{--        });--}}
{{--        $('#services_block').append(s_str);--}}
{{--        s_str = '';--}}
{{--    }--}}

{{--    function ChoiseType(id) {--}}
{{--        if(id === 1) {--}}

{{--        } else if (id === 2) {--}}

{{--        } else if (id === 3) {--}}

{{--        }--}}
{{--    }--}}

    {{--var typeId = null;--}}
    {{--var planId = null;--}}
    {{--var serviceId = null;--}}
    {{--var amount = 0;--}}
    {{--var planAmount = 0;--}}
    {{--var serviceAmount = 0;--}}
    {{--var subscribeAmount = 0;--}}
    {{--var url = '';--}}
    {{--var strPlan = '';--}}
    {{--var strService = '';--}}
    {{--var period = $('#periodMonth').val();--}}
    {{--var plan_discount = 0;--}}

    {{--/*--------Дополнения к подписке--------*/--}}
    {{--var additional_quantity = 0;--}}
    {{--var additional_price = 0;--}}
    {{--var additional_total = 0;--}}
    {{--var additional_str = '';--}}

    {{--$('#periodMonth').bind('keyup mouseup', function() {--}}
    {{--    period = this.value;--}}
    {{--    Itogo();--}}
    {{--});--}}

    {{--$(document).ready(function(){--}}

    {{--    Load();--}}
    {{--});--}}

    {{--function Load()--}}
    {{--{--}}
    {{--    var str = '';--}}
    {{--    $('#plan_place').empty();--}}
    {{--    $.ajax({--}}
    {{--        type: "GET",--}}
    {{--        url: billing_url + "/type-invoice",--}}
    {{--        dataType: 'json',--}}
    {{--        async: false,--}}
    {{--        headers: {--}}
    {{--            "Authorization": "Basic " + billing_token--}}
    {{--        },--}}
    {{--        success: function (request) {--}}
    {{--            str += '<p class="h4">Тип счета:</p>';--}}
    {{--            $.each(request, function (key, value) {--}}
    //                 str += '<div class="custom-control custom-radio">';
    //                 str += '    <input class="custom-control-input" type="radio" onclick="ChoiseType(' + value.id + ')" name="type_id" id="typeRadios' + key + '" value="' + value.id + '">';
    //                 str += '    <label class="custom-control-label" for="typeRadios' + key + '">' + value.name + '</label>';
    //                 str += '</div>';
    {{--            });--}}
    {{--            $('#plan_place').append(str);--}}
    {{--            // $("input:radio[name=type_id]:first").attr('checked', true);--}}
    {{--            str = '';--}}
    {{--        }--}}
    {{--    });--}}
    {{--}--}}

    {{--function ChoiseType(id)--}}
    {{--{--}}
    {{--    typeId = id;--}}
    {{--    strPlan = '';--}}
    {{--    strService = '';--}}
    {{--    $('#additional_place').remove(); // Удаляем если имеется--}}
    {{--    $('#planPlace').remove(); // Удаляем если имеется--}}
    {{--    $('#servicePlace').remove();--}}
    {{--    planAmount = 0;--}}
    {{--    serviceAmount = 0;--}}
    {{--    subscribeAmount = 0;--}}

    {{--    if(id === 1) {--}}
    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            url: billing_url + "/subscribe/" + '{{ $user->id }}',--}}
    {{--            dataType: 'json',--}}
    {{--            async: false,--}}
    {{--            headers: {--}}
    {{--                "Authorization": "Basic " + billing_token--}}
    {{--            },--}}
    {{--            success: function (request) {--}}
    {{--                // console.log(request.data/*.plan.name + ', ' +request.data.plan.price*/);--}}
    {{--                planId = request.data.plan.id;--}}
    {{--                plan_discount = request.data.plan.discount;--}}
    {{--                // if(planId > 3) {--}}
    {{--                //     $('#periodDiv').css('display', 'block');--}}
    {{--                // }--}}
    {{--                // console.log(request.data.id);--}}
    {{--                var date_now = Date.now();--}}
    {{--                var date_end = new Date(request.data.end_at);--}}
    {{--                var millisecondsPerDay = 1000 * 60 * 60 * 24;--}}

    {{--                var millisBetween = date_end.getTime() - date_now;--}}
    {{--                var days = millisBetween / millisecondsPerDay;--}}

    {{--                // console.log(Math.floor(days));--}}
    {{--                // console.log(request.data.plan_id);--}}
    {{--                if(request.data.plan_id === 7) {--}}
    {{--                    alert("Вы не можете поставить продление!!!");--}}
    {{--                    $('#send_invoice').attr("disabled", true);--}}
    {{--                } else {--}}
    {{--                    $('#send_invoice').removeAttr('disabled');--}}
    {{--                }--}}

    {{--                // if(request.data.additional != null){--}}
    {{--                //     $.ajax({--}}
    {{--                //         type: "GET",--}}
    {{--                //         url: billing_url + "/additional/" + request.data.id,--}}
    {{--                //         dataType: 'json',--}}
    {{--                //         async: false,--}}
    {{--                //         headers: {--}}
    {{--                //             "Authorization": "Basic " + billing_token--}}
    {{--                //         },--}}
    {{--                //         success: function (request_addit) {--}}
    {{--                //             additional_str += '<div id="additional_place">';--}}
    {{--                //             additional_str += '<strong>Дополнительно к подписке:</strong><br>';--}}
    {{--                //             $.each(request_addit.data, function (key, value) {--}}
    {{--                //                 additional_str += '<label class="mb-0">Наименование: ' + value.additional_type.name + '; Кол-во: ' + value.quantity + '; Стоимость: ' + parseInt(value.additional_type.price) + ' &#8376;/мес.</label><br>';--}}
    {{--                //                 additional_total += parseInt(value.quantity) * parseInt(value.additional_type.price);--}}
    {{--                //             });--}}
    {{--                //             additional_str += '<b>Всего: ' + additional_total + '</b>';--}}
    {{--                //             additional_str += '</div>';--}}
    {{--                //             $('#plan_place').append(additional_str);--}}
    {{--                //             additional_str = '';--}}
    {{--                //         }--}}
    {{--                //     });--}}
    {{--                // }--}}
    {{--                subscribeAmount = parseInt(parseFloat(request.data.plan.price).toFixed(0)) || 0;--}}
    {{--                $('#periodDiv').css('display', 'block');--}}
    {{--            }--}}
    {{--        });--}}
    {{--    }--}}
    {{--    if(id === 2) {--}}
    {{--        $('#periodMonth').val(1);--}}
    {{--        period = 1;--}}
    {{--        $('#periodDiv').css('display', 'none');--}}
    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            url: billing_url + "/plans",--}}
    {{--            dataType: 'json',--}}
    {{--            async: false,--}}
    {{--            headers: {--}}
    {{--                "Authorization": "Basic " + billing_token--}}
    {{--            },--}}
    {{--            success: function (request) {--}}
    {{--                // console.log(request);--}}
    {{--                strPlan += '<div class="mt-2" id="planPlace">';--}}
    {{--                strPlan += '<strong>Тарифный план:</strong>';--}}
    {{--                $.each(request.data, function (key, value) {--}}
    {{--                    if(value.on_show === 1){--}}
    {{--                        strPlan += '<div class="custom-control custom-radio">';--}}
    {{--                        strPlan += '    <input class="custom-control-input" type="radio" name="plan_id" onclick="ChoisePlan('+value.id+', \''+value.code+'\', '+value.price+', ' +value.discount+ ')" id="plansRadios' + key + '" value="' + value.id + '">';--}}
    {{--                        strPlan += '    <label class="custom-control-label" for="plansRadios' + key + '">' + value.name + '</label>';--}}
    {{--                        strPlan += '</div>';--}}
    {{--                    }--}}
    {{--                });--}}
    {{--                strPlan += '<div class="custom-control custom-checkbox mt-3">' +--}}
    {{--                    '    <input class="custom-control-input" type="checkbox" id="developChat" onclick="ChoiseDevelop()">' +--}}
    {{--                    '    <label class="custom-control-label" for="developChat">Разработка авточата</label>' +--}}
    {{--                    '</div>';--}}
    {{--                strPlan += '</div>';--}}
    {{--                $('#plan_place').append(strPlan);--}}
    {{--                strPlan = '';--}}
    {{--            }--}}
    {{--        });--}}
    {{--        $('#plan_place').append(strService);--}}
    {{--        $('#send_invoice').removeAttr('disabled');--}}
    {{--    }--}}
    {{--    if(id === 3) {--}}
    {{--        $('#periodMonth').val(1);--}}
    {{--        period = 1;--}}
    {{--        $('#periodDiv').css('display', 'none');--}}
    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            url: billing_url + "/services",--}}
    {{--            dataType: 'json',--}}
    {{--            async: false,--}}
    {{--            headers: {--}}
    {{--                "Authorization": "Basic " + billing_token--}}
    {{--            },--}}
    {{--            success: function (request) {--}}
    {{--                // console.log(request)--}}
    {{--                strService += '<div class="mt-2" id="servicePlace">';--}}
    {{--                strService += '<strong>Услуги:</strong>';--}}
    {{--                $.each(request.data, function (key, value) {--}}
    {{--                    strService += '<div class="custom-control custom-radio">';--}}
    {{--                    strService += '    <input class="custom-control-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';--}}
    {{--                    strService += '    <label class="custom-control-label" for="serviceRadios'+key+'">'+value.name+'</label>';--}}
    {{--                    strService += '</div>';--}}
    {{--                });--}}
    {{--                strService += '</div>';--}}
    {{--                $('#plan_place').append(strService);--}}
    {{--                strService = '';--}}
    {{--            }--}}
    {{--        });--}}
    {{--        $('#send_invoice').removeAttr('disabled');--}}
    {{--    }--}}
    {{--    Itogo();--}}
    {{--}--}}

    {{--function ChoisePlan(id, code, price, discount)--}}
    {{--{--}}
    {{--    plan_discount = discount;--}}
    {{--    planAmount = 0;--}}
    {{--    // serviceAmount = 0;--}}
    {{--    planId = id;--}}
    {{--    if(planId > 3) {--}}
    {{--        $('#periodDiv').css('display', 'block');--}}
    {{--    } else {--}}
    {{--        $('#periodMonth').val(1);--}}
    {{--        period = 1;--}}
    {{--        $('#periodDiv').css('display', 'none');--}}
    {{--    }--}}
    {{--    planAmount +=  price;--}}
    {{--    Itogo();--}}
    {{--    // LoadServices()--}}
    {{--}--}}

    {{--function ChoiseDevelop()--}}
    {{--{--}}
    {{--    // LoadServices();--}}
    {{--    if($('#developChat').prop('checked')) {--}}
    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            url: billing_url + "/services/1", //url,--}}
    {{--            dataType: 'json',--}}
    {{--            async: false,--}}
    {{--            headers: {--}}
    {{--                "Authorization": "Basic " + billing_token--}}
    {{--            },--}}
    {{--            success: function (request) {--}}
    {{--                serviceId = request.data.id;--}}
    {{--                serviceAmount = parseInt(request.data.price) || 0;--}}
    {{--            }--}}
    {{--        });--}}
    {{--    } else {--}}
    {{--        serviceId = null;--}}
    {{--        serviceAmount = 0;--}}
    {{--    }--}}
    {{--    Itogo();--}}
    {{--    // console.log(serviceAmount);--}}
    {{--}--}}

    {{--// function LoadServices()--}}
    {{--// {--}}
    {{--//     strService = '';--}}
    {{--//     url = '';--}}
    {{--//     $('#servicePlace').remove();--}}
    {{--//     if($('#developChat').is(':checked')) {--}}
    {{--//         $.ajax({--}}
    {{--//             type: "GET",--}}
    {{--//             url: billing_url + "/services", //url,--}}
    {{--//             dataType: 'json',--}}
    {{--//             async: false,--}}
    {{--//             headers: {--}}
    {{--//                 "Authorization": "Basic " + billing_token--}}
    {{--//             },--}}
    {{--//             success: function (request) {--}}
    {{--//                 strService += '<div class="mt-2" id="servicePlace">';--}}
    {{--//                 strService += '<strong>Услуги:</strong>';--}}
    {{--//                 $.each(request, function (key, value) {--}}
    {{--//                     strService += '<div class="custom-control custom-radio">';--}}
    {{--//                     strService += '    <input class="custom-control-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';--}}
    {{--//                     strService += '    <label class="custom-control-label" for="serviceRadios'+key+'">'+value.name+'</label>';--}}
    {{--//                     strService += '</div>';--}}
    {{--//                 });--}}
    {{--//                 strService += '</div>';--}}
    {{--//                 $('#plan_place').append(strService);--}}
    {{--//                 strService = '';--}}
    {{--//             }--}}
    {{--//         });--}}
    {{--//     } else {--}}
    {{--//         $('#servicePlace').remove();--}}
    {{--//         serviceAmount = 0;--}}
    {{--//         Itogo();--}}
    {{--//     }--}}
    {{--// }--}}

    {{--function ChoiseService(id, price)--}}
    {{--{--}}
    {{--    serviceAmount = 0;--}}
    {{--    serviceId = id;--}}
    {{--    serviceAmount += price;--}}
    {{--    Itogo();--}}
    {{--}--}}

    {{--function Itogo()--}}
    {{--{--}}
    {{--    if(plan_discount === 0 || plan_discount === undefined) {--}}
    {{--        plan_discount = 1;--}}
    {{--    }--}}
    {{--    if(period >= 12) {--}}
    {{--        amount = (subscribeAmount * period - ((subscribeAmount * period) * (plan_discount / 100))) + (planAmount * period - ((planAmount * period) * (plan_discount / 100))) + (serviceAmount - (serviceAmount * (plan_discount / 100)))/* + (additional_total * period)*/;--}}
    {{--    } else {--}}
    {{--        amount = (subscribeAmount * period) + (planAmount * period) + serviceAmount/* + (additional_total * period)*/;--}}
    {{--    }--}}
    {{--    $('#amount').text(amount);--}}
    {{--    // $('#send_invoice').removeAttr('disabled');--}}
    {{--}--}}

    {{--// Отправка данных на сервер--}}
    {{--function PostForm()--}}
    {{--{--}}
    {{--    var data = null;--}}
    {{--    if(typeId < 3) {--}}
    {{--        period = parseInt($('#periodMonth').val());--}}
    {{--        data = {--}}
    {{--            manager_id: '{{ Auth::user()->id }}',--}}
    {{--            user_id: '{{ $user->id }}',--}}
    {{--            amount: amount,--}}
    {{--            type_id: typeId,--}}
    {{--            plan_id: planId,--}}
    {{--            service_id: serviceId,--}}
    {{--            period: period,--}}
    {{--            description: $('#description').val()--}}
    {{--        };--}}
    {{--    } else if (typeId === 3) {--}}
    {{--        // period = null;--}}
    {{--        // planId = null;--}}
    {{--        data = {--}}
    {{--            manager_id: '{{ Auth::user()->id }}',--}}
    {{--            user_id: '{{ $user->id }}',--}}
    {{--            amount: amount,--}}
    {{--            type_id: typeId,--}}
    {{--            // plan_id: planId,--}}
    {{--            service_id: serviceId,--}}
    {{--            // period: period,--}}
    {{--            description: $('#description').val()--}}
    {{--        };--}}
    {{--    }--}}

    {{--    // console.log(data);--}}
    {{--    $.ajax({--}}
    {{--        type: "POST",--}}
    {{--        url: billing_url + "/invoice",--}}
    {{--        data: data,--}}
    {{--        dataType: 'json',--}}
    {{--        headers: {--}}
    {{--            "Authorization": "Basic " + billing_token--}}
    {{--        },--}}
    {{--        success: function (request) {--}}
    {{--            console.log(request);--}}
    {{--            if(request.error === undefined) {--}}
    {{--                console.log(request);--}}
    {{--                // CloseForm();--}}
    {{--                window.location.replace("{{ route('manager.users.show', $user->id) }}");--}}
    {{--            }--}}
    {{--        }--}}
    {{--    });--}}
    {{--}--}}
</script>
@endsection
