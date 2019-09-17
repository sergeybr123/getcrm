@extends('layouts.app')

@section('title', __('Выставление счета'))

@section('styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card card-accent-primary mt-3">
                <p class="h3 card-title pt-3 pl-3">{{ __('Пользователь:') }} <strong>{{ $user->email }}</strong></p>
                <div class="card-body">
                    <div id="plan_place"></div>
                    {{--<div id="planPlace"></div>--}}
                    {{--<div id="servicePlace"></div>--}}
                    <div id="periodDiv" class="mt-2" style="display: none">
                        <strong>Период</strong>
                        <input class="form-control" type="number" id="periodMonth" min="1" max="12" value="1">
                    </div>
                    <div class="mt-3" id="amount_place">
                        <strong>Итого: </strong><span id="amount">0</span>
                    </div>
                    <textarea id="description" class="form-control mt-2" rows="3" placeholder="Описание(не обязательно)"></textarea>


                </div>
                <div class="card-footer">
                    <a href="{{ route('manager.users.show', $user->id) }}" class="btn btn-secondary">{{ __('Отмена') }}</a>
                    <button type="button" class="btn btn-primary" onclick="PostForm()">{{ __('Сохранить') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
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

    /*--------Дополнения к подписке--------*/
    let additional_quantity = 0;
    let additional_price = 0;
    let additional_total = 0;
    let additional_str = '';

    $('#periodMonth').bind('keyup mouseup', function() {
        period = this.value;
        Itogo();
    });

    $(document).ready(function(){

        Load();
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
                str += '<p class="h4">Тип счета:</p>';
                $.each(request, function (key, value) {
                    str += '<div class="custom-control custom-radio">';
                    str += '    <input class="custom-control-input" type="radio" onclick="ChoiseType(' + value.id + ')" name="type_id" id="typeRadios' + key + '" value="' + value.id + '">';
                    str += '    <label class="custom-control-label" for="typeRadios' + key + '">' + value.name + '</label>';
                    str += '</div>';
                });
                $('#plan_place').append(str);
                // $("input:radio[name=type_id]:first").attr('checked', true);
                str = '';
            }
        });
    }

    function ChoiseType(id)
    {
        typeId = id;
        strPlan = '';
        strService = '';
        $('#additional_place').remove(); // Удаляем если имеется
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
                    // console.log(request.data.id);
                    let date_now = Date.now();
                    let date_end = new Date(request.data.end_at);
                    let millisecondsPerDay = 1000 * 60 * 60 * 24;

                    let millisBetween = date_end.getTime() - date_now;
                    let days = millisBetween / millisecondsPerDay;

                    console.log(Math.floor(days));

                    // if(request.data.additional != null){
                    //     $.ajax({
                    //         type: "GET",
                    //         url: billing_url + "/additional/" + request.data.id,
                    //         dataType: 'json',
                    //         async: false,
                    //         headers: {
                    //             "Authorization": "Basic " + billing_token
                    //         },
                    //         success: function (request_addit) {
                    //             additional_str += '<div id="additional_place">';
                    //             additional_str += '<strong>Дополнительно к подписке:</strong><br>';
                    //             $.each(request_addit.data, function (key, value) {
                    //                 additional_str += '<label class="mb-0">Наименование: ' + value.additional_type.name + '; Кол-во: ' + value.quantity + '; Стоимость: ' + parseInt(value.additional_type.price) + ' &#8376;/мес.</label><br>';
                    //                 additional_total += parseInt(value.quantity) * parseInt(value.additional_type.price);
                    //             });
                    //             additional_str += '<b>Всего: ' + additional_total + '</b>';
                    //             additional_str += '</div>';
                    //             $('#plan_place').append(additional_str);
                    //             additional_str = '';
                    //         }
                    //     });
                    // }
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
                        // console.log(value)
                        if(value.on_show === 1){
                            strPlan += '<div class="custom-control custom-radio">';
                            strPlan += '    <input class="custom-control-input" type="radio" name="plan_id" onclick="ChoisePlan('+value.id+', \''+value.code+'\', '+value.price+', ' +value.discount+ ')" id="plansRadios' + key + '" value="' + value.id + '">';
                            strPlan += '    <label class="custom-control-label" for="plansRadios' + key + '">' + value.name + '</label>';
                            strPlan += '</div>';
                        }
                    });
                    strPlan += '<div class="custom-control custom-checkbox mt-3">' +
                        '    <input class="custom-control-input" type="checkbox" id="developChat" onclick="ChoiseDevelop()">' +
                        '    <label class="custom-control-label" for="developChat">Разработка авточата</label>' +
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
                    // console.log(request)
                    strService += '<div class="mt-2" id="servicePlace">';
                    strService += '<strong>Услуги:</strong>';
                    $.each(request.data, function (key, value) {
                        strService += '<div class="custom-control custom-radio">';
                        strService += '    <input class="custom-control-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';
                        strService += '    <label class="custom-control-label" for="serviceRadios'+key+'">'+value.name+'</label>';
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
        planAmount = 0;
        // serviceAmount = 0;
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
        // LoadServices()
    }

    function ChoiseDevelop()
    {
        // LoadServices();
        if($('#developChat').prop('checked')) {
            $.ajax({
                type: "GET",
                url: billing_url + "/services/1", //url,
                dataType: 'json',
                async: false,
                headers: {
                    "Authorization": "Basic " + billing_token
                },
                success: function (request) {
                    serviceId = request.data.id;
                    serviceAmount = parseInt(request.data.price) || 0;
                }
            });
        } else {
            serviceId = null;
            serviceAmount = 0;
        }
        Itogo();
        // console.log(serviceAmount);
    }

    // function LoadServices()
    // {
    //     strService = '';
    //     url = '';
    //     $('#servicePlace').remove();
    //     if($('#developChat').is(':checked')) {
    //         $.ajax({
    //             type: "GET",
    //             url: billing_url + "/services", //url,
    //             dataType: 'json',
    //             async: false,
    //             headers: {
    //                 "Authorization": "Basic " + billing_token
    //             },
    //             success: function (request) {
    //                 strService += '<div class="mt-2" id="servicePlace">';
    //                 strService += '<strong>Услуги:</strong>';
    //                 $.each(request, function (key, value) {
    //                     strService += '<div class="custom-control custom-radio">';
    //                     strService += '    <input class="custom-control-input" type="radio" name="service_id" onclick="ChoiseService('+value.id+', '+value.price+')" id="serviceRadios'+key+'" value="'+value.id+'">';
    //                     strService += '    <label class="custom-control-label" for="serviceRadios'+key+'">'+value.name+'</label>';
    //                     strService += '</div>';
    //                 });
    //                 strService += '</div>';
    //                 $('#plan_place').append(strService);
    //                 strService = '';
    //             }
    //         });
    //     } else {
    //         $('#servicePlace').remove();
    //         serviceAmount = 0;
    //         Itogo();
    //     }
    // }

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
            amount = (subscribeAmount * period - ((subscribeAmount * period) * (plan_discount / 100))) + (planAmount * period - ((planAmount * period) * (plan_discount / 100))) + (serviceAmount - (serviceAmount * (plan_discount / 100)))/* + (additional_total * period)*/;
        } else {
            amount = (subscribeAmount * period) + (planAmount * period) + serviceAmount/* + (additional_total * period)*/;
        }
        $('#amount').text(amount);
    }

    // Отправка данных на сервер
    function PostForm()
    {
        let data = null;
        if(typeId < 3) {
            period = parseInt($('#periodMonth').val());
            data = {
                manager_id: '{{ Auth::user()->id }}',
                user_id: '{{ $user->id }}',
                amount: amount,
                type_id: typeId,
                plan_id: planId,
                service_id: serviceId,
                period: period,
                description: $('#description').val()
            };
        } else if (typeId === 3) {
            // period = null;
            // planId = null;
            data = {
                manager_id: '{{ Auth::user()->id }}',
                user_id: '{{ $user->id }}',
                amount: amount,
                type_id: typeId,
                // plan_id: planId,
                service_id: serviceId,
                // period: period,
                description: $('#description').val()
            };
        }

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
                console.log(request);
                if(request.error === undefined) {
                    console.log(request);
                    // CloseForm();
                    window.location.replace("{{ route('manager.users.show', $user->id) }}");
                }
            }
        });
    }
</script>
@endsection
