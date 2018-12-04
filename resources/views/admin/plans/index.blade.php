@extends('layouts.app')

@section('title', __('Тарифные планы'))

@section('content')
    <h1>{{ __('Тарифные планы') }} <a href="{{ route('admin::plans::create') }}" class="btn btn-outline-blue"><i class="fa fa-plus"></i></a></h1>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            {{--<div class="px-3">--}}
            {{--</div>--}}
            <ul class="list-group mb-3">
                @forelse($plans as $key => $plan)
                    <li class="list-group-item">
                        <div id="normal_{{ $plan->id }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $plan->name }}</h5>
                                <small class="text-muted" title="Дата создания">
                                    @if($plan->created_at != null)
                                    {{ \Carbon\Carbon::parse($plan->created_at)->format('d.m.Y') }}
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex w-100 justify-content-between">
                                <p class="mb-1">
                                    <strong>{{ __('Стоимость') }}: </strong>{{ number_format($plan->price, 0, '.', '') }}
                                </p>
                                <div>
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
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">Страницы отсутствуют</li>
                @endforelse
            </ul>
            <div class="px-3">
                {{-- $pages->links() --}}
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
    </script>
@endsection