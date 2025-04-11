<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <i class="tio-clear"></i>
</button>
<div class="details">
    <div class="">
        <div class="text-center">
            <div class="modal-header justify-content-center">
                <h4 class="modal-title pb-2">Verifikasi Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="card">
                <div class="modal-body">
                    <p class="text-danger">Harap Periksa & Verifikasi informasi pembayaran apakah benar atau tidak sebelum mengkonfirmasi pesanan.</p>

                    <h5>Informasi Pelanggan</h5>

                    <div class="card-body">
                        <p>Nama : {{ $order->customer ? $order->customer->f_name.' '. $order->customer->l_name: ''}} </p>
                        <p>Kontak : {{ $order->customer ? $order->customer->phone: ''}}</p>
                    </div>

                    <h5>Informasi Pembayaran</h5>
                    @php($payment = json_decode($order->offline_payment?->payment_info, true))
                    <div class="row card-body">
                        <div class="col-md-6">
                            <p>Metode Pembayaran : {{ $payment['payment_name'] }}</p>
                            @foreach($payment['method_fields'] as $fields)
                                @foreach($fields as $fieldKey => $field)
                                    <p>{{ $fieldKey }} : {{ $field }}</p>
                                @endforeach
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <p>Catatan Pembayaran : {{ $payment['payment_note'] }}</p>
                            @foreach($payment['method_information'] as $infos)
                                @foreach($infos as $infoKey => $info)
                                    <p>{{ $infoKey }} : {{ $info }}</p>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container justify-content-center my-2 mx-3" id="button-container">
                @if($order->offline_payment?->status == 0)
                    <button type="button" class="btn btn-secondary verify-offline-payment-btn" data-order-id="{{ $order['id'] }}" data-status="2">Pembayaran Tidak Diterima</button>
                @endif
                <button type="button" class="btn btn-primary verify-offline-payment-btn" data-order-id="{{ $order['id'] }}" data-status="1">Ya, Pembayaran Diterima</button>
            </div>

        </div>

    </div>
</div>

<script>
    "use strict";

    $('.verify-offline-payment-btn').click(function() {
        var orderId = $(this).data('order-id');
        var status = $(this).data('status');
        verify_offline_payment(orderId, status);
    });
</script>
