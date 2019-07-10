@extends('layouts.app')

@section('title', __('Выставление счета'))

@section('styles')
@endsection

@section('content')
    <div class="row mt-5">
        <div class="col-md-6 col-sm-12 offset-md-3">
            <div class="card card-accent-primary mt-3">
                <form>
                    <div class="card-body">
                        <p class="mb-1">{{ __('Пользователь:') }} <strong>{{ $user->email }}</strong></p>
                        <p class="mb-1">{{ __('Подписка:') }} <strong>{{ $subscribe->plan->name }}</strong></p>
                        <p class="mb-1">{{ __('Статус подписки:') }} <strong>{{ $subscribe->active == 1 ? 'Активная' : 'Не активная' }}</strong></p>
                    </div>
                    <div class="card-body pt-1">
                        <div>
                            <p class="mb-1">{{ __('Действия:') }}</p>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio1" name="type_id" class="custom-control-input" value="1" {{ $subscribe->active == 1 ? 'disabled' : 'checked' }}>
                                <label class="custom-control-label" for="customRadio1">{{ __('Приобрести') }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio2" name="type_id" class="custom-control-input" value="2" {{ $subscribe->active == 1 ? 'checked' : 'disabled' }}>
                                <label class="custom-control-label" for="customRadio2">{{ __('Продлить') }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio3" name="type_id" class="custom-control-input" value="4">
                                <label class="custom-control-label" for="customRadio3">{{ __('Дополнения к подписке') }}</label>
                            </div>
                        </div>
                        <div id="periodDiv" class="mt-2">
                            <strong>Период</strong>
                            <input class="form-control" type="number" id="periodMonth" min="1" max="12" value="1">
                        </div>
                        <div class="mt-3" id="amount_place">
                            <strong>Итого: </strong><span id="amount">0</span>
                        </div>
                        <textarea id="description" class="form-control mt-2" rows="3" placeholder="Описание(не обязательно)"></textarea>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('partner::invoices::index') }}" class="btn btn-secondary">{{ __('Отмена') }}</a>
                        <button id="send_invoice" type="button" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var billing_token = '{{ config('app.billing_token') }}';
        var billing_url = '{{ config('app.billing_url') }}';
        $(document).load(function() {

        })
    </script>
@endsection