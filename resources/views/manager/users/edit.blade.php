@extends('layouts.app')
@section('title', __('Редактирование пользователя'))
@section('styles')
    <link href="{{ asset('vendors/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div>
        <a href="{{ route('manager.users.show', $user->id) }}" class="btn btn-outline-blue"><i
                    class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card card-accent-primary mt-3">
                <div class="card-header">
                    <p class="h3 text-center">{{ $user->email }}</p>
                </div>
                <div class="card-body justify-content-center w-100">
                    <form action="{{ route('manager.users.update', $user->id) }}" method="post">
                        @csrf
                        {{--<input type="hidden" class="form-control" name="id" value="{{ $user->id }}">--}}
                        @if (session('success'))
                            <p class="text-success">
                                {{ session('success') }}
                            </p>
                        @endif

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
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="country_code" class="col-sm-2 col-form-label">Код</label>
                            <div class="col-sm-10">
                                <select id="select2-1" class="form-control select2-single" name="country_code">
                                    @foreach($phones as $phone)
                                        <option value="{{ $phone['code'] }}" {{ str_replace('+', '', $phone['dial_code']) == $user->phone->country_code ? 'selected' : '' }}>{{ $phone['name'] . ' ' . $phone['dial_code'] }}</option>
                                    @endforeach
                                </select>
                                {{--<input type="text" class="form-control" id="country_code" name="country_code" placeholder="country_code" value="{{ $user->phone->country_code }}">--}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Email" value="{{ $user->phone->phone }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Имя</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="first_name" placeholder="Имя" value="{{ $user->profile != null ? $user->profile->first_name : '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="surname" class="col-sm-2 col-form-label">Фамилия</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="surname" name="last_name" placeholder="Фамилия" value="{{ $user->profile != null ? $user->profile->last_name : '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="company" class="col-sm-2 col-form-label">Компания</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="company" name="company" placeholder="Компания" value="{{ $user->profile != null ? $user->profile->company : '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location" class="col-sm-2 col-form-label">Город, стр</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="location" name="location" placeholder="Город, стр" value="{{ $user->profile != null ? $user->profile->location : '' }}">
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-outline-blue" type="submit">Сохранить</button>
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