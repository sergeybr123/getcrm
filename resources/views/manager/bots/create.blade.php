@extends('layouts.app')
@section('styles')
@endsection
@section('title', __('Добавление нового мультилинка'))
@section('content')
    <div>
        <a href="{{ route('manager.users.show', $user->id) }}" class="btn btn-outline-blue"><i
                class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-6 offset-3">
            <div class="card card-accent-primary mt-3">
                <div class="card-body justify-content-center w-100">
                    <p class="h4 text-center mb-3">{{ __('Добавление нового мультилинка') }}</p>
                    <form method="post">
                        @csrf
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
                        <div class="mb-4">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">Пользователь</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control-plaintext font-weight-bold" id="staticEmail" value="{{ $user->email }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="link" class="col-sm-3 col-form-label">Ссылка</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="link" name="link">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Наименование</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name">
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
