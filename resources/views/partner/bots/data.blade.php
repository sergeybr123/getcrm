@extends('layouts.app')

@section('title', __('Данные авточата'))

@section('styles')

@endsection

@section('content')
    <div class="mb-2">
        <a href="{{ route('partner::bots::index') }}" class="btn btn-outline-blue btn-sm">
            <i class="fa fa-angle-double-left"></i> {{ __('Назад') }}
        </a>
    </div>
    <p class="h3">{{ __('Данные авточата:').' '.$company->slug }}</p>
    <div class="card card-accent-primary mt-3">
        <div class="card-body p-2 pb-0">
            @if($message != "")
                <div class="alert alert-danger mb-0" role="alert">
                    {{ $message }}
                </div>
            @else
                <table class="table table-bordered table-striped table-sm mb-0">
                    @forelse($data as $item)
                        <tr>
                            <td>{!! $item['data']['product'] !!}</td>
                            <td>{!! $item['data']['name'] !!}</td>
                            <td>{!! $item['data']['phone'] !!}</td>
                            <td style="width: 70px;">{{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ __("Данные отсутствуют!") }}</td>
                        </tr>
                    @endforelse
                </table>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
@endsection