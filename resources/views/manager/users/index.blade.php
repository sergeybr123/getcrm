@extends('layouts.app')

@section('title', __('users.users'))

@section('content')
<h1>{{ __('users.users') }}</h1>
<div class="card card-accent-primary mt-3">
    <div class="card-body">
        <div class="px-3 mb-3">
            <form method="post" action="{{ route('manager.user.search') }}">
                @csrf
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label flex-nowrap px-0">Поиск</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="type">
                                            <option value="1">по email</option>
                                            <option value="2">по номеру телефона</option>
                                            {{--<option value="3">по дате регистрации</option>--}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" name="text" type="text" placeholder="{{ __('Введите для поиска') }}">
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
            @forelse($users as $item)
                <li class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <a href="{{ route('manager.users.show', ['id' => $item->id]) }}">{{ $item->email }}</a>
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
                                    +{{ $item->phone->country_code . $item->phone->phone }}
                                @else
                                    <span>Нет номера</span>
                                @endif
                            </span>
                        </div>
                        {{--<div class="form-inline">--}}
                            {{--<a href="#" class="btn btn-circle btn-sm btn-outline-blue">--}}
                                {{--<i class="fa fa-pencil-alt"></i>--}}
                            {{--</a>--}}
                            {{--<div class="dropdown">--}}
                                {{--<button class="btn btn-circle btn-sm btn-outline-blue ml-1" type="button"--}}
                                        {{--id="dropdownMenuButton"--}}
                                        {{--data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
                                        {{--style="width:30px;height:30px;">--}}
                                    {{--<i class="fa fa-ellipsis-v"></i>--}}
                                {{--</button>--}}
                                {{--<div class="dropdown-menu dropdown-menu-right mt-1" aria-labelledby="dropdownMenuButton">--}}
                                    {{--<a class="dropdown-item" href="{{ route('manager.users.show', ['id' => $item->id]) }}">--}}
                                        {{--<i class="far fa-eye"></i> {{ __('buttons.view') }}--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </li>
            @empty
                <li class="list-group-item">Страницы отсутствуют</li>
            @endforelse
        </ul>
        <div class="px-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
