@extends('layouts.app')

@section('title', __('users.users'))

@section('content')
    <div>
        <h1>{{ __('Пользователи') }} ({{ count($users) }})</h1>
    </div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('partner::users::create-bot') }}" class="btn btn-outline-blue btn-sm"><i class="fa fa-plus"></i> Создать авточат</a>
            </div>
            <ul class="list-group mb-3">
                @forelse($users as $item)
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <a href="{{ route('partner::users::show', ['id' => $item->id]) }}">
                                @if($item->email)
                                    {{ $item->email }}
                                @else
                                    {{ $item->name }}
                                @endif
                            </a>
                            <small class="text-muted" title="{{ __('users.register_at') }}">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') }}
                            </small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <div class="mt-2">
                                <strong><i class="fa fa-hashtag"></i></strong>{{ $item->id }}
                                <strong class="ml-3">{{ __('Телефон') }}: </strong>
                                <span>
                                    @if($item->phone != null)
                                        +{{ $item->country_code . $item->phone }}
                                    @else
                                        <span>Нет номера</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Записи отсутствуют</li>
                @endforelse
            </ul>
            <div class="px-3">
                {{-- $users->links() --}}
            </div>
        </div>
    </div>
@endsection
