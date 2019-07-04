@extends('layouts.app')

@section('title', __('Мои данные'))

@section('content')
    <p class="h3">{{ __('Мои данные') }}</p>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-11">
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Email:') }}</div>
                        <div class="col-12 col-md-9">{{ $user->email/*.' '.$user->id*/ }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Телефон:') }}</div>
                        <div class="col-12 col-md-9">{{ '+'.$user->phone->country_code.$user->phone->phone }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Фамилия:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->last_name }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Имя:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->first_name }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Организация:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->company }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Расположение:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->location }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-1">
                    <button class="btn btn-sm btn-outline-blue" title="{{ __('Редактировать данные') }}"><i class="fas fa-cog"></i></button>
                </div>
            </div>
        </div>
    </div>
    <p class="h3">{{ __('Подписка') }}</p>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="row mb-1">
                <div class="col-12 col-md-3">{{ __('Наименование:') }}</div>
                <div class="col-12 col-md-9">{{ $subscribe->plan->name }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-12 col-md-3">{{ __('Состояние:') }}</div>
                <div class="col-12 col-md-9">
                    @if($subscribe->active == 1)
                        <span class="badge badge-success">{{ __('Активна') }}</span>
                    @else
                        <span class="badge badge-danger">{{ __('Завершена') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-12 col-md-3">{{ __('Начало:') }}</div>
                <div class="col-12 col-md-9">
                    @if($subscribe->start_at)
                        {{ \Carbon\Carbon::parse($subscribe->start_at)->format('d.m.Y') }}
                    @endif
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-12 col-md-3">{{ __('Завершение:') }}</div>
                <div class="col-12 col-md-9">
                    @if($subscribe->end_at)
                        {{ \Carbon\Carbon::parse($subscribe->end_at)->format('d.m.Y') }}
                    @endif
                </div>
            </div>
            <div class="mt-2">
                @if($subscribe->active == 1)
                    <button class="btn btn-sm btn-outline-blue" title="{{ __('Продлить подписку') }}">{{ __('Продлить') }}</button>
                @else
                    <button class="btn btn-sm btn-outline-danger" title="{{ __('Подписаться на подписку') }}">{{ __('Подписаться') }}</button>
                @endif
            </div>
        </div>
    </div>
@endsection
