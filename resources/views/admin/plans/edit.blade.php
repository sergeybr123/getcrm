@extends('layouts.app')
@section('title', __('Редактирование тарифного плана'))
@section('styles')
    <link href="{{ asset('vendors/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div>
        <a href="{{ route('admin::plans::index') }}" class="btn btn-outline-blue"><i
                    class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card card-accent-primary mt-3">
                <div class="card-header">
                    <p class="h4 text-center mb-1">{{ __('Редактирование плана') }}</p>
                </div>
                <div class="card-body justify-content-center w-100">
                    <form method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $plan->id }}">
                        @if ($errors->any())
                            <div class="text-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="code" class="col-sm-3 col-form-label">{{ __('Код') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" id="code" name="code" value="{{ old('code') ?? $plan->code }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">{{ __('Наименование') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" value="{{ old('name') ?? $plan->name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="price" class="col-sm-3 col-form-label">{{ __('Стоимость') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" id="price" name="price" value="{{ old('price') ?? $plan->price }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="discount" class="col-sm-3 col-form-label">{{ __('Скидка за год, %') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('discount') ? ' is-invalid' : '' }}" id="discount" name="discount" value="{{ old('discount') ?? $plan->discount }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bot_count" class="col-sm-3 col-form-label">{{ __('Количество авточатов') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('bot_count') ? ' is-invalid' : '' }}" id="bot_count" name="bot_count" value="{{ old('bot_count') ?? $plan->bot_count }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="interval" class="col-sm-3 col-form-label">{{ __('Интервал') }}</label>
                            <div class="col-sm-9">
                                <select id="interval" name="interval" class="form-control{{ $errors->has('discount') ? ' is-invalid' : '' }}">
                                    <option value="unlimited" {{ $plan->interval == 'unlimited' ? 'selected' : '' }}>{{ __('Неограниченно') }}</option>
                                    <option value="month" {{ $plan->interval == 'month' ? 'selected' : '' }}>{{ __('Месяц') }}</option>
                                    <option value="year" {{ $plan->interval == 'year' ? 'selected' : '' }}>{{ __('Год') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sort_order" class="col-sm-3 col-form-label">{{ __('Сортировка') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('sort_order') ? ' is-invalid' : '' }}" id="sort_order" name="sort_order" value="{{ old('sort_order') ?? $plan->sort_order }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="on_show" class="col-sm-3 col-form-label">{{ __('Видимость') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control{{ $errors->has('on_show') ? ' is-invalid' : '' }}" id="sort_order" name="on_show" value="{{ old('on_show') ?? $plan->on_show }}">
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-outline-blue" type="submit">{{ __('Сохранить') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('vendors/js/select2.min.js') }}"></script>
    <script>
        $('#select2-1').select2({
            theme: "bootstrap"
        });
    </script>
@endsection