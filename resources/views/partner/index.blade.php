@extends('layouts.app')

@section('title', __('Мои данные'))

@section('styles')
    <link href="{{ asset('vendors/css/toastr.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <p class="h3">{{ __('Мои данные') }}</p>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-11">
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Email:') }}</div>
                        <div class="col-12 col-md-9">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Телефон:') }}</div>
                        <div class="col-12 col-md-9">{{ '+'.$user->phone->country_code.$user->phone->phone }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Фамилия:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->last_name ?? '' }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Имя:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->first_name ?? '' }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Организация:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->company ?? '' }}</div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-12 col-md-3">{{ __('Расположение:') }}</div>
                        <div class="col-12 col-md-9">{{ $profile->location ?? '' }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-1">
                    {{--<button class="btn btn-sm btn-outline-blue" title="{{ __('Редактировать данные') }}"><i class="fas fa-cog"></i></button>--}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-blue dropdown-toggle" title="{{ __('Редактировать данные') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editEmailModal">{{ __('Изменить email') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editPhoneModal">{{ __('Изменить телефон') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editProfileModal">{{ __('Редактировать профиль') }}</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editPasswordModal">{{ __('Изменить пароль') }}</a>
                        </div>
                    </div>
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
            {{--Продление подписки, раскоментировать как сделаю биллинг--}}
            {{--<div class="mt-2">--}}
                {{--@if($subscribe->active == 1)--}}
                    {{--<a href="{{ route('partner::invoices::create') }}" class="btn btn-sm btn-outline-blue" title="{{ __('Продлить подписку') }}">{{ __('Продлить') }}</a>--}}
                {{--@else--}}
                    {{--<button class="btn btn-sm btn-outline-danger" title="{{ __('Подписаться на подписку') }}">{{ __('Подписаться') }}</button>--}}
                {{--@endif--}}
            {{--</div>--}}
        </div>
    </div>

    {{--Редактировать email--}}
    <div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editEmailForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmailModalLabel">{{ __('Редактрование email') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{--<div class="row">--}}
                            {{--<div class="col-4 col-md-3">--}}
                                {{--{{ __('Ваш email:') }}--}}
                            {{--</div>--}}
                            {{--<div class="col-8 col-md-9">--}}
                                {{--{{ $user->email }}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            <div class="col-2 col-md-1">
                                {{ __('Email:') }}
                            </div>
                            <div class="col-10 col-md-11">
                                <input type="email" class="form-control" id="editEmailInput" name="email" value="{{ $user->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditEmailModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Редактировать email--}}
    <div class="modal fade" id="editPhoneModal" tabindex="-1" role="dialog" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editPhoneForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPhoneModalLabel">{{ __('Редактирование номера телефона') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                            {{ __('Код страны:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <select id="select2-1" class="form-control select2-single" name="country_code">
                                    @foreach($phones as $phone)
                                        <option value="{{ str_replace('+', '', $phone['dial_code']) }}" {{ str_replace('+', '', $phone['dial_code']) == $user_phone->country_code ? 'selected' : '' }}>{{ $phone['name'] . ' ' . $phone['dial_code'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Телефон:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editPhoneInput" name="phone" value="{{ $user_phone->phone }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditPhoneModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Редактировать профиль пользователя--}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editProfileForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">{{ __('Редактрование профиля пользователя') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                                {{ __('Фамилия:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editLastNameInput" name="last_name" value="{{ $profile->last_name }}" autofocus>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Имя:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editFirstNameInput" name="first_name" value="{{ $profile->first_name }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Организация:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editCompanyInput" name="company" value="{{ $profile->company }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Расположение:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="text" class="form-control" id="editLocationInput" name="location" value="{{ $profile->location }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Редактировать профиль пользователя--}}
    <div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editPasswordForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPasswordModalLabel">{{ __('Изменение пароля пользователя') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 col-md-3">
                                {{ __('Пароль:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="password" class="form-control" id="passwordInput" name="password" autofocus>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-4 col-md-3">
                                {{ __('Повторить:') }}
                            </div>
                            <div class="col-8 col-md-9">
                                <input type="password" class="form-control" id="confirmInput" name="confirm_password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditPasswordModal()">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    {{--Редактировать email--}}
    $('#editEmailForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: "{{ route('partner::change_email') }}",
            data: $('#editEmailForm').serialize(),
            success: function (request) {
                if (request.error === 0) {
                    toastr.success(request.message, 'Ok');
                    closeEditEmailModal();
                    setTimeout(reLoad, 1000);
                } else {
                    toastr.error(request.message, 'Внимание!');
                    $('#editEmailInput').addClass('is-invalid');
                }
            }
        });
    });
    function closeEditEmailModal() {
        // $('#editEmailInput').val('');
        $("#editEmailModal").modal('hide');
    }

    {{--Редактирование телефона--}}
    $('#editPhoneForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: "{{ route('partner::change_phone') }}",
            data: $('#editPhoneForm').serialize(),
            success: function (request) {
                // console.log(request);
                if (request.error === 0) {
                    toastr.success(request.message, 'Ok');
                    closeEditPhoneModal();
                    setTimeout(reLoad, 1000);
                } else {
                    toastr.error(request.message, 'Внимание!');
                    $('#editPhoneInput').addClass('is-invalid');
                }
            }
        });
    });
    var closeEditPhoneModal = function () {
            $("#editPhoneModal").modal('hide');
        };

    {{--Редактирование профиля--}}
    $('#editProfileForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: "{{ route('partner::change_profile') }}",
            data: $('#editProfileForm').serialize(),
            success: function (request) {
                // console.log(request.message);
                if (request.error === 0) {
                    toastr.success(request.message, 'Ok');
                    closeEditProfileModal();
                    setTimeout(reLoad, 1000);
                } else {
                    toastr.error(request.message, 'Внимание!');
                    // $('#editEmailInput').addClass('is-invalid');
                }
            }
        });
    });
    var closeEditProfileModal = function() {
            $("#editProfileModal").modal('hide');
        };
    /*-----Редактирование пароля-----*/
    $('#editPasswordForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: "{{ route('partner::change_password') }}",
            data: $('#editPasswordForm').serialize(),
            success: function (request) {
                if (request.error === 0) {
                    toastr.success(request.message, 'Ok');
                    closeEditPasswordModal();
                } else {
                    toastr.error(request.message, 'Внимание!');
                    $('#passwordInput').addClass('is-invalid');
                    $('#confirmInput').addClass('is-invalid');
                }
            }
        });
    });
    var closeEditPasswordModal = function() {
        $("#editPasswordModal").modal('hide');
    };
    /*-----Перезагрузка страницы-----*/
    var reLoad = function() {
        location.reload();
    }
</script>
@endsection