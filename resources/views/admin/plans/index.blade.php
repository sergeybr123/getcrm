@extends('layouts.app')

@section('title', __('Тарифные планы'))

@section('content')
    <h1>{{ __('Тарифные планы') }} <a href="{{ route('admin::plans::create') }}" class="btn btn-outline-blue"><i class="fa fa-plus"></i></a></h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            {{--<div class="px-3">--}}
            {{--</div>--}}
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                <tr>
                    <th width="30">#</th>
                    <th>{{ __('Наименование') }}</th>
                    <th width="70">{{ __('Цена') }}</th>
                    <th width="70">{{ __('Скидка') }}</th>
                    <th width="70">{{ __('Авточатов') }}</th>
                    <th width="70">{{ __('Действия') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($plans as $key => $plan)
                    <tr>
                        <td>{{ $plan->id }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>{{ $plan->price }}</td>
                        <td>{{ $plan->discount }}</td>
                        <td>{{ $plan->bot_count }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-blue" href="{{ route('admin::plans::update', $plan->id) }}">
                                <i class="fa fa-pencil-alt"></i>
                            </a>
                            <a class="btn btn-sm btn-outline-danger" href="{{ route('admin::plans::delete', $plan->id) }}"
                               onclick="event.preventDefault();document.getElementById('delete-form-{{ $plan->id }}').submit();">
                                <i class="fa fa-trash"></i>
                            </a>
                            <form id="delete-form-{{ $plan->id }}" action="{{ route('admin::plans::delete', $plan->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </td>
                    </tr>
                @empty
                    <td colspan="5">Страницы отсутствуют</td>
                @endforelse


                </tbody>
            </table>
{{--            <ul class="list-group mb-3">--}}
{{--                @forelse($plans as $key => $plan)--}}
{{--                    <li class="list-group-item">--}}
{{--                        <div id="normal_{{ $plan->id }}">--}}
{{--                            <div class="d-flex w-100 justify-content-between">--}}
{{--                                <h5 class="mb-1">{{ $plan->name }}</h5>--}}
{{--                                <small class="text-muted" title="Дата создания">--}}
{{--                                    @if($plan->created_at != null)--}}
{{--                                    {{ \Carbon\Carbon::parse($plan->created_at)->format('d.m.Y') }}--}}
{{--                                    @endif--}}
{{--                                </small>--}}
{{--                            </div>--}}
{{--                            <div class="d-flex w-100 justify-content-between">--}}
{{--                                <p class="mb-1">--}}
{{--                                    <strong>{{ __('Стоимость') }}: </strong>{{ number_format($plan->price, 0, '.', '') }}--}}
{{--                                </p>--}}
{{--                                <div>--}}
{{--                                    <a class="btn btn-sm btn-outline-blue" href="{{ route('admin::plans::update', $plan->id) }}">--}}
{{--                                        <i class="fa fa-pencil-alt"></i>--}}
{{--                                    </a>--}}
{{--                                    <a class="btn btn-sm btn-outline-danger" href="{{ route('admin::plans::delete', $plan->id) }}"--}}
{{--                                       onclick="event.preventDefault();document.getElementById('delete-form-{{ $plan->id }}').submit();">--}}
{{--                                        <i class="fa fa-trash"></i>--}}
{{--                                    </a>--}}
{{--                                    <form id="delete-form-{{ $plan->id }}" action="{{ route('admin::plans::delete', $plan->id) }}" method="POST" style="display: none;">--}}
{{--                                        @csrf--}}
{{--                                    </form>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </li>--}}
{{--                @empty--}}
{{--                    <li class="list-group-item">Страницы отсутствуют</li>--}}
{{--                @endforelse--}}
{{--            </ul>--}}
{{--            <div class="px-3">--}}
{{--                $plan->links() --}}
{{--            </div>--}}
        </div>
    </div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection