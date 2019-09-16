@extends('layouts.app')

@section('title', __('Подписки'))

@section('content')
    <h1>{{ __('Подписки') }} ({{ $subscriptions->total() }})</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="row mb-3" style="display: none">
                <div class="col-md-3">
                    <form id="active-form">
                        <select id="active-select" class="form-control" name="active">
                            <option value>{{ __('Все') }}</option>
                            <option value="1" {{ $active == 1 ? 'selected' : '' }}>{{ __('Активные') }}</option>
                            <option value="2" {{ $active == 2 ? 'selected' : '' }}>{{ __('Не активные') }}</option>
                        </select>
                    </form>
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="plan" style="display: none">
                        <option>{{ __('Выбрать...') }}</option>
                        <option value="1">{{ __('Активные') }}</option>
                        <option value="2">{{ __('Не активные') }}</option>
                    </select>
                </div>
            </div>
            <table class="table table-bordered table-striped table-responsive-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Email пользователя') }}</th>
                    <th>{{ __('Телефон пользователя') }}</th>
                    <th>{{ __('Тарифный план') }}</th>
                    <th>{{ __('Статус') }}</th>
                    <th>{{ __('Дата начала') }}</th>
                    <th>{{ __('Дата окончания') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscriptions as $key => $subscription)
                    <tr>
                        <td>{{ $subscription->id }}</td>
                        <td>
                            @if($subscription->user)
                                <a href="{{ route('manager.users.show', ['id' => $subscription->user_id]) }}">{{ $subscription->user->email }}</a>
                            @else
                                Неизвестно
                            @endif
                        </td>

                        <td>
                            @if($subscription->user->phone)
                                +{{ $subscription->user->phone->country_code.$subscription->user->phone->phone }}
                            @else
                                Не указан
                            @endif
                        </td>
                        <td class="text-center">{{ $subscription->plan->name }}</td>
                        <td class="text-center">
                            @if($subscription->active == 1)
                                <span class="badge badge-success">Активна</span>
                            @else
                                <span class="badge badge-danger">Не активна</span>
                            @endif
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($subscription->start_at)->format('d.m.Y') }}</td>
                        <td class="text-center">
                            @if($subscription->End)
                                {{ \Carbon\Carbon::parse($subscription->end_at)->format('d.m.Y') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('Подписки отсутствуют') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <ul class="list-group mb-3">

            </ul>
            <div class="px-3">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#active-select').on('change', function() {
            let id_active = $('#active-form').serialize();
            $('#active-form').submit();
            // console.log(id_active)
        });
    </script>
@endsection
