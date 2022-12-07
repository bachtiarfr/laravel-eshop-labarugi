@extends('order.layouts.app')
@section('content')

<div class="d-flex flex-column flex-md-row justify-content-end">
    <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal-new-order" style="margin-right:200px;margin-top:20px;margin-bottom:20px">
        Create New Order
    </a>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead class="thead-light">
                            <th scope="col">Invoice</th>
                            <th scope="col">Total Harga</th>
                            <th scope="col">Status Pembayaran</th>
                            <th scope="col"></th>
                        </thead>
                        <tbody>
                            @if(count($orders) > 0)
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>#{{ $order->invoice }}</td>
                                        <td>Rp. {{ number_format($order->gross_amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->transaction_status == 'pending')
                                                <span class="badge badge-pill badge-danger">
                                                    Menunggu pembayaran
                                                </span>
                                            @elseif($order->transaction_status == 'settlement' || $order->transaction_status == 'capture')
                                                <span class="badge badge-pill badge-success">
                                                    Pembayaran telah diterima
                                                </span>
                                            @elseif($order->transaction_status == 'expire')
                                                <span class="badge badge-pill badge-warning">
                                                    Kadaluarsa
                                                </span>
                                            @else
                                                <span class="badge badge-pill badge-info">
                                                    {{ $order->transaction_status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">
                                        <center>Transactions not available yet</center>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-new-order">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Order</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label>Product</label>
            <select name="product_id" class="form-control">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ (int) $product->price }}">
                        {{ $product->title }}
                    </option>
                @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" placeholder="Input Product Quantity" required>
          </div>
          <div class="form-group">
            <label>Total</label>
            <input type="number" name="total" class="form-control" placeholder="Total" readonly="">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary process-order">Process Order</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>

$('body').on('change', 'select[name=product_id]', function(){
    const productPrice = $(this).find(":selected").attr('data-price');
    const qty = $('input[name=quantity]').val()

    if(qty != '')
    {
        $('input[name=total]').val(productPrice * qty)
    }
})

$('body').on('input', 'input[name=quantity]', function(){
    const productPrice = $('select[name=product_id]').find(":selected").attr('data-price');
    const qty = this.value
    const total = productPrice*qty;

    $('input[name=total]').val(total)
})

$('body').on('click', '.process-order', function(e){
    e.preventDefault()
    snap.show();
    const data = {
        productId: $('select[name=product_id]').val(),
        productName: $('select[name=product_id]').find(":selected").text(),
        productPrice: $('select[name=product_id]').find(":selected").attr('data-price'),
        qty: $('input[name=quantity]').val(),
        total: $('input[name=total]').val()
    }

    $.ajax({
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: "{{ route('orders.generateSnapToken') }}",
      data: data,
      success: function (orderDetail) {
        redirectToSnap(orderDetail)
      },
      error: function (err) {
        snap.hide();
        Swal.fire({
            title: 'An error occured...',
            icon: 'warning'
        })
      },
    });
})

function redirectToSnap({ snap_token: snapToken, order_detail: orderDetail }) {
    $('#modal-new-order').modal('toggle')
    snap.pay(snapToken, {
        // onSuccess for pay direct through snap (credit card)
        onSuccess: function(resMidtrans) {
            saveOrder({ resMidtrans, orderDetail})
        },
        // all payment besides credit card will go through onPending
        onPending: function(resMidtrans) {
            saveOrder({ resMidtrans, orderDetail})
        },
        // Optional
        onError: function(err) {
            Swal.fire('An error occured during payment process, please try again later', '', 'error')
        }
    });
}

function saveOrder(order) {
    console.log(order)
    $.ajax({
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: "",
      data: order,
      success: function (res) {
        Swal.fire({
            title: 'Thank you for your order',
            text: 'The order will be processed after your payment completed',
            icon: 'success'
        }).then((nextResult) => {
          if (nextResult) window.location.reload(true);
        })
      },
      error: function (err) {
        console.log(err)
        Swal.fire('An error occured during payment process, please try again later', '','error')
      },
    });
}
</script>
@endsection
