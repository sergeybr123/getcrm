@extends('layouts.app')
@section('styles')
@endsection
@section('title', __('Изменение владельца авточата'))
@section('content')
    <div>
        <a href="{{ route('manager.users.show', $old_owner->id) }}" class="btn btn-outline-blue"><i
                    class="fa fa-angle-double-left"></i> {{ __('Назад') }}</a>
    </div>
    <div class="row">
        <div class="col-md-6 offset-3">
            <div class="card card-accent-primary mt-3">
                <div class="card-body justify-content-center w-100">
                    <p class="h4 text-center mb-3">{{ __('Изменение владельца авточата') }}</p>
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
                                <label for="staticEmail" class="col-sm-3 col-form-label">{{ __('Владелец:') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control-plaintext font-weight-bold" id="staticEmail" value="{{ $old_owner->email }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">{{ __('Ссылка:') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control-plaintext font-weight-bold" id="staticEmail" value="{{ $bot->slug }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">{{ __('Новый владелец:') }}</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="new_owner" name="new_owner">
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('manager.users.show', $old_owner->id) }}" class="btn btn-outline-secondary">{{ __('Отмена') }}</a>
                            <button class="btn btn-outline-blue" type="submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection
