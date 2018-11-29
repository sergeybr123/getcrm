@extends('layouts.app')

@section('title', __('users.users'))

@section('content')
    <div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-blue">{{ __('buttons.back') }}</a>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="px-3">
                <h2>{{ $page->slug }}</h2>
            </div>
        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="form-group mb-0">
                        <label for="">Email пользователя: </label>
                        <strong>{{ $page->owner->email }}</strong>
                    </div>
                </div>
                <div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-blue ml-1" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="border-radius:50%;width:30px;height:30px;">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"><i class="far fa-eye"></i> {{ __('buttons.view') }}</a>
                            <a class="dropdown-item" href="#" onclick=""><i class="fa fa-copy"></i> {{ __('buttons.share') }}</a>
                            <a class="dropdown-item text-danger" href="#"><i class="fa fa-trash"></i> {{ __('buttons.remove') }}</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div>
                <ul class="list-group">
                    @forelse($accounts as $account)
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $account->type }}</h5>
                                <small class="text-muted" title="Дата создания">
                                    {{ \Carbon\Carbon::parse($account->created_at)->format('d.m.Y') }}
                                </small>
                            </div>
                            <div class="">
                                @foreach($account->data as $key => $val)
                                    <span class="mr-2">{{ $key }}: {{ $val }}</span>
                                @endforeach
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">Данные отсутствуют</li>
                    @endforelse
                </ul>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>
                    <button type="button" class="btn btn-primary">{{ __('Сохранить') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    function copyToClipboard(key) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#slug_' + key).text()).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.info('Ссылка скопирована');
    }
    function copyPageToClipboard(key) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#page_slug_' + key).text()).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.info('Ссылка скопирована');
    }
</script>
@endsection