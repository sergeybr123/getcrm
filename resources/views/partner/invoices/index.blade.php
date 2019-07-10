@extends('layouts.app')

@section('title', __('Мои счета'))

@section('content')
    {{--<button class="btn btn-sm btn-outline-blue float-right"><i class="fas fa-plus"></i> {{ __('Добавить счет') }}</button>--}}
    <p class="h3">{{ __('Мои счета') }}</p>
    <div id="errorPay"></div>
    <div class="card card-accent-primary mt-3">
        <div class="card-body">
            @forelse($invoices->data as $invoice)
                <hr class="my-1">
                <div class="d-flex w-100 justify-content-between px-1">
                    <div>
                        <span class="mr-1">{{ $invoice->id }}</span>|
                        <span class="mr-1">{{ $invoice->type->name.' - '.$invoice->plan->name }}</span>|
                        <span>{{ $invoice->amount }}</span>
                    </div>
                    @if($invoice->paid == 0 && $invoice->status == 'active')
                    <div>
                        <button class="btn btn-link text-blue mr-1 p-0" title="{{ __('Оплатить счет') }}" onclick="pay({{ $invoice->amount }}, {{ $invoice->id }}, {{ $invoice->user_id }})"><i class="fas fa-wallet"></i></button>
                        <button class="btn btn-link text-muted mx-0 p-0" onclick="event.preventDefault();document.getElementById('completedInvoice{{ $invoice->id }}').submit();"><i class="fas fa-times" title="{{ __('Отменить счет') }}"></i></button>
                        <form id="completedInvoice{{ $invoice->id }}" action="{{ route('partner::invoices::completed', $invoice->id) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                    @elseif($invoice->status == 'paid')
                        <span class="badge badge-success">Оплочено</span>
                    @elseif($invoice->status == 'completed')
                        <span class="badge badge-warning">Завершен</span>
                    @endif
                </div>
            @empty
                <div class="alert alert-secondary" role="alert">
                    {{ __('Данные отсутствуют') }}
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://widget.cloudpayments.kz/bundles/cloudpayments"></script>
<script>
    function pay(amount, invoice_id, user_id) {
        var widget = new cp.CloudPayments();
        widget.charge({
                publicId: 'pk_a7d751f2e03eec1954e376423fe54',
                description: 'Оплата подписки',
                amount: amount,
                currency: 'KZT',
                invoiceId: invoice_id, //номер заказа
                accountId: user_id, //плательщик
            },
            function (options) { // success
                //действие при успешном платеже
                window.location.reload();
                // console.log('Done payment', options);
                {{--window.location.href = '--}}{{-- route('payments') --}}{{--';--}}
            },
            function (reason, options) { // fail
                //действие при неуспешном платеже
                {{--window.location.href = "{{ route('partner::invoices::index') }}";--}}
                console.log('Fail payment', reason, options);
            });
    }

</script>
@endsection