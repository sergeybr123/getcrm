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
                                <a href="https://getchat.me/constructor2/{{ $page->BotId }}" target="_blank" class="btn btn-sm btn-outline-blue ml-1" style="border-radius:50%;">
                                    <i class="fa fa-wrench"></i>
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
@endsection
@section('scripts')
    <script>

    </script>
@endsection