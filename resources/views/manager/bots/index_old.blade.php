@extends('layouts.app')

@section('title', __('Авточаты'))

@section('content')
    <h1>{{ __('Авточаты старые') }} ({{ $bots_old->total() }})</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="px-3 mb-3">
                <form>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3 pl-0">
                                    <select class="form-control" name="type">
                                        <option value="1" {{ $type == 1 ? 'selected' : '' }}>по ссылке</option>
                                        <option value="2" {{ $type == 2 ? 'selected' : '' }}>по email пользователя</option>
                                        {{--<option value="3">по дате регистрации</option>--}}
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control" name="text" type="text" placeholder="{{ __('Введите для поиска') }}" value="{{ $text }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 pr-0">
                            <button class="btn btn-outline-info btn-block" type="submit">{{ __('Поиск') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group mb-3">
                @forelse($bots_old as $key => $boto)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $boto->slug }}</h5>
                            <span id="page_slug_{{ $boto->id }}" style="display: none;">https://getchat.me/{{ $boto->slug }}</span>
                            <small class="text-muted" title="Дата создания">
                                {{ \Carbon\Carbon::parse($boto->created_at)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                <strong>{{ __('pages.owner') }}: </strong>
                                <a href="{{ route('manager.users.show', ['id' => $boto->owner->id]) }}">{{ $boto->owner->email }}</a>
                            </p>
                            <div class="form-inline">
                                <a href="https://getchat.me/constructor/{{ $boto->id }}" class="btn btn-circle btn-sm btn-outline-blue" title="{{ __('Перейти в конструктор') }}" target="_blank">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <a href="#" class="btn btn-circle btn-sm btn-outline-blue ml-1" data-toggle="modal" data-target="#editLinkModal" title="{{ __('Редактировать ссылку') }}" onclick="EditLink({{ $boto->id }}, '{{ $boto->slug }}')">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <div class="dropdown ml-1">
                                    <button class="btn btn-circle btn-sm btn-outline-blue" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="width:30px;height:30px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="https://getchat.me/{{ $boto->slug }}" target="_blank"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                                        <button class="dropdown-item" onclick="changeOwnerButtonClick({{ $boto->id }}, '{{ $boto->slug }}')" data-toggle="modal" data-target="#changeOwnerModal"><i class="fa fa-user"></i> {{ __('Изменить владельца') }}</button>
                                        <a class="dropdown-item" href="#" onclick="copyPageToClipboard({{ $boto->id }})"><i class="fa fa-copy"></i> {{ __('buttons.copy_link') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Авточаты отсутствуют</li>
                @endforelse
            </ul>
            <div class="px-3">
                {{ $bots_old->links() }}
            </div>
        </div>
    </div>



    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('pages.change_owner') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

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
@endsection
@section('scripts')
    <script>
        function copyPageToClipboard(key) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#page_slug_' + key).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.info('Ссылка скопирована');
        }

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

        /*-------------------Смена владельца-----------------------*/
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