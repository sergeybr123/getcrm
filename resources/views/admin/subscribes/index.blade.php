@extends('layouts.app')

@section('title', __('Подписки'))

@section('content')
    <h1>{{ __('Подписки') }} ({{ $subscriptions->total() }})</h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            <div class="px-3">
                Подписки
            </div>
            <table class="table table-sm table-bordered table-striped table-responsive-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>User Email</th>
                    <th>User Phone</th>
                    <th>Plan Name</th>
                    <th>Status</th>
                    <th>Start</th>
                    <th>End</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscriptions as $key => $subscription)
                    <tr>
                        <td>{{ $subscription->SubscribeId }}</td>
                        <td><a href="{{ route('manager.users.show', ['id' => $subscription->UserId]) }}">{{ $subscription->email }}</a></td>
                        <td>+{{ $subscription->country_code.$subscription->phone }}</td>
                        <td>{{ $subscription->PlanName }}</td>
                        <td>
                            @if($subscription->active == 1)
                                <span class="badge badge-success">Активна</span>
                            @else
                                <span class="badge badge-danger">Не активна</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($subscription->Start)->format('d.m.Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($subscription->End)->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ __('Подписки отсутствуют') }}</td>
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

    </script>
@endsection