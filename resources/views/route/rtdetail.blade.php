    <div class="panel panel-body rt-item" id="{{ $order->delivery_id }}"
        data-lat="{{ $order->latitude }}"
        data-lon="{{ $order->longitude }}"
        data-address="{{ '<b>'.$order->buyer_name.'</b><br />'.$order->shipping_address.'<br />'.$order->buyerdeliverycity.' '.$order->shipping_zip }}"
         >
        <div class="media">
            <div class="media-left rt-handle">
                <button class="btn btn-transparent btn-sm bt-up"  data-pid="{{ $order->delivery_id }}">
                    <i class="fa fa-2x fa-caret-up" data-pid="{{ $order->delivery_id }}"></i>
                </button>
                <br/>
                <button class="btn btn-transparent btn-sm bt-down" data-pid="{{ $order->delivery_id }}">
                    <i class="fa fa-2x fa-caret-down" data-pid="{{ $order->delivery_id }}"></i>
                </button>
            </div>

            <div class="media-body">
                <h6 class="media-heading">{{ $order->buyer_name }}</h6>
                <p class="text-muted">
                    {{ $order->shipping_address }}<br />
                    {{ $order->buyerdeliverycity}}
                    {{ $order->shipping_zip }}
                </p>
                <ul class="icons-list" style="color:black;">
                    <li><i class="icon-location3"></i></li>
                    <li><span class="rt-lat">{{ $order->latitude }}</span></li>
                    <li><span class="rt-lon">{{ $order->longitude }}</span></li>
                    <li><span class="btn btn-sm btn pick-location"         
                            data-lat="{{ $order->latitude }}"
                            data-lon="{{ $order->longitude }}"
                            data-id="{{ $order->delivery_id }}"
                         >Set Loc</span></li>
                </ul>
            </div>
        </div>
    </div>


