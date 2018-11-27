@extends('layouts.app')

@section('title', __('Мои страницы'))

@section('content')
    <div class="card card-accent-primary mt-3">
        <div class="card-header">
            <p class="h3 mb-0">{{ __('Старые авточаты') }}</p>
        </div>
        <div class="card-body">
            <ul class="list-group mb-3">
                @forelse($bot_old as $key => $bo)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 id="page_slug_{{ $key }}" class="mb-1"><span class="text-muted">https://getchat.me/</span>{{ $bo->slug }}</h5>
                            <small class="text-muted" title="Дата создания">
                                {{ \Carbon\Carbon::parse($bo->created_at)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                <strong>{{ __('pages.owner') }}: </strong>
                                <a href="{{ route('manager.users.show', ['id' => $bo->owner->id]) }}">{{ $bo->owner->email }}</a>
                            </p>
                            <div class="form-inline">
                                <a href="#" class="btn btn-circle btn-sm btn-outline-blue mr-1" data-toggle="modal" data-target="#editLinkModal" onclick="EditLink({{ $bo->id }}, '{{ $bo->slug }}')">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <a href="https://getchat.me/constructor/{{ $bo->id }}" target="_blank" class="btn btn-circle btn-sm btn-outline-blue">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="width:30px;height:30px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="https://getchat.me/{{ $bo->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                        {{--<button class="dropdown-item" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-exchange-alt"></i> {{ __('buttons.change_owner') }}</button>--}}
                                        <button class="dropdown-item" onclick="changeOwnerButtonClick({{ $bo->id }}, '{{ $bo->slug }}')" data-toggle="modal" data-target="#changeOwnerModal"><i class="fa fa-user"></i> {{ __('Изменить владельца') }}</button>
                                        <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $key }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Страницы отсутствуют</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{--<div class="card card-accent-primary mt-3">--}}
        {{--<div class="card-header">--}}
            {{--<p class="h3 mb-0">{{ __('Новые авточаты') }}</p>--}}
        {{--</div>--}}
        {{--<div class="card-body">--}}
            {{--<ul class="list-group mb-3">--}}
                {{--@forelse($bot_new as $key => $bn)--}}
                    {{--@if($bn->bots != null)--}}
                    {{--<li class="list-group-item">--}}
                    {{--@foreach($bn->bots as $bot)--}}

                            {{--<div class="d-flex w-100 justify-content-between">--}}
                                {{--<h5 id="page_slug_{{ $key }}" class="mb-1"><span class="text-muted">https://getchat.me/</span>{{ $bn->slug }}</h5>--}}
                                {{--<small class="text-muted" title="Дата создания">--}}
                                    {{--{{ \Carbon\Carbon::parse($bn->created_at)->format('d.m.Y') }}--}}
                                {{--</small>--}}
                            {{--</div>--}}

                        {{--{{ $bot->id }}--}}
                    {{--@endforeach--}}
                    {{--</li>--}}
                    {{--@endif--}}
                    {{--@if($bn->bots != '')--}}
                        {{--<li class="list-group-item">--}}
                            {{--<div class="d-flex w-100 justify-content-between">--}}
                                {{--<h5 id="page_slug_{{ $key }}" class="mb-1"><span class="text-muted">https://getchat.me/</span>{{ $bn->slug }}</h5>--}}
                                {{--<small class="text-muted" title="Дата создания">--}}
                                    {{--{{ \Carbon\Carbon::parse($bn->created_at)->format('d.m.Y') }}--}}
                                {{--</small>--}}
                            {{--</div>--}}
                            {{--<div class="d-flex w-100 justify-content-between">--}}
                                {{--<p class="mb-1">--}}
                                    {{--<strong>{{ __('pages.owner') }}: </strong>--}}
                                    {{--<a href="{{ route('manager.users.show', ['id' => $bn->owner->id]) }}">{{ $bn->owner->email }}</a>--}}
                                {{--</p>--}}
                                {{--<div class="form-inline">--}}
                                    {{--<a href="#" class="btn btn-circle btn-sm btn-outline-blue" data-toggle="modal" data-target="#editLinkModal" onclick="EditLink({{ $bn->id }}, '{{ $bn->slug }}')">--}}
                                        {{--<i class="fa fa-pencil-alt"></i>--}}
                                    {{--</a>--}}
                                    {{--<div class="dropdown">--}}
                                        {{--<button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"--}}
                                                {{--data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
                                                {{--style="width:30px;height:30px;">--}}
                                            {{--<i class="fa fa-ellipsis-v"></i>--}}
                                        {{--</button>--}}
                                        {{--<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">--}}
                                            {{--<a class="dropdown-item" href="https://getchat.me/{{ $bn->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>--}}
                                            {{--<button class="dropdown-item" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-exchange-alt"></i> {{ __('buttons.change_owner') }}</button>--}}
                                            {{--<a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $key }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    {{--@endif--}}
                {{--@empty--}}
                    {{--<li class="list-group-item">Страницы отсутствуют</li>--}}
                {{--@endforelse--}}
            {{--</ul>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="card card-accent-primary mt-3">
        <div class="card-header">
            <p class="h3 mb-0">{{ __('Мои страницы') }}</p>
        </div>
        <div class="card-body">
            <ul class="list-group mb-3">
                @forelse($pages as $key => $page)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 id="page_slug_{{ $key }}" class="mb-1"><span class="text-muted">https://getchat.me/</span>{{ $page->slug }}</h5>
                            <small class="text-muted" title="Дата создания">
                                {{ \Carbon\Carbon::parse($page->created_at)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                <strong>{{ __('pages.owner') }}: </strong>
                                <a href="{{ route('manager.users.show', ['id' => $page->owner->id]) }}">{{ $page->owner->email }}</a>
                            </p>
                            <div class="form-inline">
                                <a href="#" class="btn btn-circle btn-sm btn-outline-blue" data-toggle="modal" data-target="#editLinkModal" onclick="EditLink({{ $page->id }}, '{{ $page->slug }}')">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="width:30px;height:30px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="https://getchat.me/{{ $page->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                        {{--<button class="dropdown-item" data-toggle="modal" data-target="#changeLinkModal"><i class="fa fa-exchange-alt"></i> {{ __('Изменить ссылку') }}</button>--}}
                                        <button class="dropdown-item" onclick="changeOwnerButtonClick({{ $page->id }}, '{{ $page->slug }}')" data-toggle="modal" data-target="#changeOwnerModal"><i class="fa fa-user"></i> {{ __('Изменить владельца') }}</button>
                                        <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $key }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Страницы отсутствуют</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{--Изменение владельца--}}
    <div class="modal fade" id="changeOwnerModal" tabindex="-1" role="dialog" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="changeOwnerForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Изменить владельца') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ __('Ссылка:') }}</strong> https://getchat.me/<strong id="owner_link_slug"></strong></p>
                        <input type="hidden" id="company_owner_id" name="company_id">
                        <input id="user" class="form-control" type="text" name="user" list="user_list">
                        <datalist id="user_list">
                        </datalist>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="CloseChangeOwnerForm()">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Заменить ссылки--}}
    <div class="modal fade" id="changeLinkModal" tabindex="-1" role="dialog" aria-labelledby="changeLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Изменить ссылку') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    {{--Изменение ссылки--}}
    <div class="modal fade" id="editLinkModal" tabindex="-1" role="dialog" aria-labelledby="editLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form" action="#">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Редактирование ссылки') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <input class="form-control" type="text" name="slug" id="slug">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="CloseForm()" data-dismiss="modal">{{ __('Отмена') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function EditLink(id, slug) {
            $('#id').val(id);
            $('#slug').val(slug);
            $('#editLinkModal').show();
        }

        $('#form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: '{{ route('edit_link') }}',
                data: $('#form').serialize(),
                success: function (request) {
                    if(request.error === 0) {
                        CloseForm();
                        location.reload();
                    }
                }
            });
        });

        function CloseForm() {
            $('#id').val();
            $('#slug').val();
            $('#editLinkModal').hide();
        }

        let email = '';
        let str = '';
        function changeOwnerButtonClick(id, slug){
            $('#company_owner_id').val(id);
            $('#owner_link_slug').text(slug);
        }
        $('#user').on('input', function(){
            email = $(this).val();
            let data = {
                email: email
            };
            if(email.length > 2){
                $.post( "/api/get-users", data, function(req) {
                    $('.test_opt').remove();
                    str = '';
                    // console.log(req);
                    $.each(req, function(key, value) {
                        str += '<option class="test_opt" value="' + value.email + '">';
                    });
                    $('#user_list').append(str);
                    // this.req = '';
                });
                str = '';
            }
        });
        $('#changeOwnerForm').on('submit', function (e) {
            e.preventDefault();
            // console.log($('#changeOwnerForm').serialize())
            $.ajax({
                type: "post",
                url: "/api/change-owner",
                data: $('#changeOwnerForm').serialize(),
                success: function (request) {
                    if(request.error === 0) {
                        CloseChangeOwnerForm();
                        location.reload();
                    } else {
                        console.log(request)
                    }
                }
            });
        });
        function CloseChangeOwnerForm(){
            $('#user').val('');
            str = '';
            $('.test_opt').remove();
            email = '';
        }
    </script>
@endsection