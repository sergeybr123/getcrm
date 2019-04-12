@extends('layouts.app')

@section('title', __('Шаблоны'))

@section('content')
    <h1>{{ __('Шаблоны') }} (@if($templates){{ $templates->total() }}@endif)</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <ul class="list-group mb-3">
                @forelse($templates as $key => $page)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 id="page_slug_{{ $key }}" class="mb-1">{{ $page->BotName }}</h5>
                            <small class="text-muted" title="Дата создания">
                                {{ \Carbon\Carbon::parse($page->CompanyCreated)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">
                                <span>https://getchat.me/{{ $page->Slug }}</span>
                            </p>
                            <div class="form-inline">
                                <a href="https://getchat.me/constructor2/{{ $page->BotId }}" target="_blank" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;" title="Открыть в конструкторе">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <a href="#" target="_blank" onclick="copyTemplate({{ $page->BotId }})" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;" title="Копировать шаблон">
                                    <i class="fa fa-copy"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Страницы отсутствуют</li>
                @endforelse
            </ul>
            <div class="px-3">
                @if($templates)
                {{ $templates->links() }}
                @endif
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="copyTemplateModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="copyBotForm" method="post" action="{{ route('copy_templates') }}">
                    @csrf
                    <input type="hidden" id="template_id" name="template_id">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Копирование авточата') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Ссылка:') }}</strong>
                        <input id="link" class="form-control" type="text" name="link">
                    </div>
                    <div class="modal-body">
                        <strong>{{ __('Пользователь:') }}</strong>
                        <input id="user_email" class="form-control" type="text" name="user_email">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Закрыть') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Копировать') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script> // onclick="createCopy()"
        var template_id = null;
        function copyTemplate(id) {
            $('#template_id').val(id);
            $('#copyTemplateModal').modal();
        }
        // function createCopy() {
        //     var url = 'http://getchat/create-new-bot';
        //     $.ajax({
        //         type: "GET",
        //         url: url,
        //         data: $('#copyBotForm').serialize(),
        //         success: function(request) {
        //             $('#copyTemplateModal').modal('hide');
        //         }
        //     });
        // }
    </script>
@endsection