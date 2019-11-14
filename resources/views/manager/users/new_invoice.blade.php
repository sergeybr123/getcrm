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

                    <div class="custom-control custom-radio">
                        <input type="radio" id="selPaid" name="selectPaidStatus" class="custom-control-input" checked>
                        <label class="custom-control-label" for="selPaid">Платные услуги</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="selNoPaid" name="selectPaidStatus" class="custom-control-input">
                        <label class="custom-control-label" for="selNoPaid">Бесплатные услуги</label>
                    </div>

                    <div class="paid_div my-1 p-2" style="display: none;border: 1px solid #5d5d5d;">
                        <div id="block_plan" class="mb-1">
                            @foreach($plans as $item)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="plan_id" id="planRadios_{{ $item->id }}" value="{{ $item->id }}" {{ ($item->id == $user->subscribe->plan_id) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="planRadios_{{ $item->id }}">{{ $item->name }}</label>
                                </div>
                            @endforeach
                            <p class="mb-0">Период</p>
                            <input id="plan_quantity" class="form-control" name="plan_quantity" type="number" onclick="paymentRef()" min="0" max="12" value="0">
                        </div>
                        <div id="block_bot" class="mb-1">
                            @if($services_bot)
                                <div class="form-inline">
                                    <label for="bot_service" class="mr-2">{{ __('Количество авточатов') }}</label>
                                    <input class="form-control" type="number" id="botCheck" min="{{ $user->subscribe->plan->bot_count }}" value="{{ $user->subscribe->bot_count }}">
                                </div>
                            @endif
                        </div>
                        <div id="block_service" class="mb-1">
                            @if($services_service)
                                <div class="form-inline">
                                    <label for="serviceCheck" class="mr-2">{{ $services_service->name }}</label>
                                    <input class="form-control" type="number" id="serviceCheck" min="0" value="0">
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="datepickerStartSubscribe" class="mt-2">Дата начала подписки:</label>
                            <div class="form-inline">
                                <div class="custom-control custom-radio mr-2">
                                    <input type="radio" id="selNoStart" name="selectStart" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="selNoStart">С момента оплаты</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="selStart" name="selectStart" class="custom-control-input">
                                    <label class="custom-control-label" for="selStart">Установить дату</label>
                                </div>
                            </div>
                            <div class="selectDateStart mt-1" style="display: none">
                                <input class="form-control" type="text" name="start_subscribe" id="datepickerStartSubscribe">
                                <div id="dropdownCalendar" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <div class="datepicker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="no_paid_div my-1 p-2" style="display: none;border: 1px solid #5d5d5d;">
                        <div id="block_bonus">
                            @if($services_bonus)
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="bonusCheck">
                                    <label class="custom-control-label" for="bonusCheck">{{ $services_bonus->name }}</label>
                                </div>
                            @endif
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
    $( function() {
        $( "#datepickerStartSubscribe" ).datepicker({ dateFormat: 'dd.mm.yy' });
    });

    var plans = {!! $plans !!};
    var subscribe = {!! $subscribe !!};
    var last_invoice = {!! $last_invoice !!};
    var ref_options = {!! $last_invoice->ref_options !!};
    // Даты
    var today = new Date();
    var start = new Date(subscribe.start_at);
    var end = new Date(subscribe.end_at);
    var cost_pay = null;
    var day_to_end = null;
    var all_period = null;
    var ref = null;

    $(document).ready(function () {
        if($('#selPaid').prop("checked", true)) {
            $('.paid_div').css('display', 'block');
            $('.no_paid_div').css('display', 'none');
        } else {
            $('.paid_div').css('display', 'none');
            $('.no_paid_div').css('display', 'block');
        }
    });

    // var sel_plan = $('input[name=plan_id]:checked').val();
    // var period = $('#plan_quantity').val();
    // console.log(period);

    $('#selPaid').on('click', function() {
        $('.paid_div').css('display', 'block');
        $('.no_paid_div').css('display', 'none');
    });
    $('#selNoPaid').on('click', function() {
        $('.paid_div').css('display', 'none');
        $('.no_paid_div').css('display', 'block');
    });

    $('#selNoStart').on('click', function() {
        $('.selectDateStart').css('display', 'none');
    });
    $('#selStart').on('click', function() {
        $('.selectDateStart').css('display', 'block');
    });

    function paymentRef() {
        //Считаем сумму которую должны вернуть
        cost_pay = parseInt(ref_options.plan.price) + parseInt(-(ref_options.plan.discount)) + parseInt(ref_options.plan.x);
        // Расчет оставшихся дней подписки
        var diffDateToEnd = (end - today) / (1000 * 60 * 60 * 24);
        day_to_end = Math.ceil(diffDateToEnd);
        // Расчет всего периода подписки
        var diffDateAll = (end - start) / (1000 * 60 * 60 * 24);
        all_period = Math.ceil(diffDateAll);
        // Считаем ref
        ref = Math.floor(cost_pay * (day_to_end / all_period));
        //!Считаем сумму которую должны вернуть
        console.log(ref);
        console.log(plans);

        var sel_plan = $('input[name=plan_id]:checked').val();
        var period = $('#plan_quantity').val();
        console.log(period + " | " + sel_plan + " | " + subscribe.plan_id);

        $.each(plans, function(key, value) {
            // console.log(value.id);
            if(sel_plan == value.id) {
                console.log(value);
            }
        });


        if(subscribe.active == 1 && (sel_plan == subscribe.plan_id)) {


            console.log('check');

        }
    }

</script>
@endsection
