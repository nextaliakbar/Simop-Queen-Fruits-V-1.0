<div class="card-header d-flex justify-content-between gap-10">
    <h5 class="mb-0">Pelanggan Teratas</h5>
    <a href="{{route('admin.customer.list')}}" class="btn-link">Tampilkan Semua</a>
</div>

<div class="card-body">
    <div class="d-flex flex-column gap-3">
        @foreach($top_customer as $key=>$item)
            @if(isset($item->customer))
                <a class="d-flex justify-content-between align-items-center text-dark" href='{{route('admin.customer.view', [$item['user_id']])}}'>
                    <div class="media align-items-center gap-3">
                        <img class="rounded avatar avatar-lg"
                                src="{{ $item->customer->imageFullPath }}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                        <div class="media-body d-flex flex-column custom-media-body">
                            @php($customer_name = $item->customer['f_name'] . ' ' . $item->customer['l_name'])
                            <span class="font-weight-semibold text-capitalize">{{$customer_name ?? '-'}}</span>
                            <span class="text-dark">{{ $item->customer['phone']?? 'Tidak ada' }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-1 badge-soft-c1 font-weight-bold fz-12 rounded lh-1">Pesanan : {{$item['count']}}</span>
                </a>
            @endif
        @endforeach
    </div>
</div>
