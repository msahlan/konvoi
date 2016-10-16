    <div class="panel panel-body rt-item" id="{{ $order->transactionId }}"
        data-lat="{{ $order->latitude }}"
        data-lon="{{ $order->longitude }}"
        data-address="{{ '<b>'.$order->contractName.'</b><br />'.$order->pickupAddress.'<br />'.$order->pickupDistrict.'<br />'.$order->pickupCity.' '.$order->pickupZIP }}"
         >
        <div class="media">
            <div class="media-left rt-handle">
                <button class="btn btn-transparent btn-sm bt-viewmap"  data-pid="{{ $order->transactionId }}"  
                    data-lat="{{ $order->latitude }}"
                    data-lon="{{ $order->longitude }}"
                >view on map</button>
            </div>

            <div class="media-body">
                <h6 class="media-heading">{{ $order->contractName }}</h6>
                <p class="text-muted">
                    {{ $order->pickupAddress }}<br />
                    {{ $order->pickupDistrict }}<br />
                    {{ $order->pickupCity.' '.$order->pickupZIP }}
                </p>
                <ul class="icons-list" style="color:black;">
                    <li><i class="icon-location3"></i></li>
                    <li><span class="rt-lat">{{ $order->latitude }}</span></li>
                    <li><span class="rt-lon">{{ $order->longitude }}</span></li>
                </ul>
            </div>
        </div>
    </div>


