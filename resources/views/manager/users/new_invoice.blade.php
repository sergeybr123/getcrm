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
                    <div id="block_bot">
                        @if($services_bot)
                            <div class="form-inline">
                                <label for="bot_service" class="mr-2">{{ __('Количество авточатов') }}</label>
                                <input class="form-control" type="number" id="botCheck" onchange="GetRef()" min="{{ $user->subscribe->plan->bot_count }}" value="{{ $user->subscribe->bot_count }}">
                            </div>
                            <hr>
                        @endif
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
    $( function() {
        $( "#datepickerStartSubscribe" ).datepicker({ dateFormat: 'dd.mm.yyyy' });
    });
</script>
@endsection
