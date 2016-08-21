<style type="text/css">

.table td.box{
    height:50px !important;
    min-height:50px !important;
    text-align: center;
    border-right: thin solid #eee !important;
}

.table td{
    border-right: thin solid #eee !important;
}

</style>
<h2>HUB-{{ str_pad($doc_number, 5, '0', STR_PAD_LEFT) }}</h2>

<table class="" style="width:100%;">
    <tr>
        <td style="vertical-align:top;">
            <img src="{{ URL::to('/') }}/images/jex_top_logo.png" alt="logo" />
        </td>
        <td style="vertical-align:top;">
            <table class="table table-bordered">
                <tr>
                    <td colspan="2" style="font-weight:bold;font-size:18px;">MDL-{{ str_pad($doc_number, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td>PUBLISH DATE / TGL TERBIT</td>
                    <td style="min-width:100px;">
                        @if(is_null(Input::get('manifest-date')) || Input::get('manifest-date') == '')
                            {{ date('d-m-Y', time() ) }}
                        @else
                            {{ date('d-m-Y', strtotime(Input::get('manifest-date')) ) }}
                        @endif

                    </td>
                </tr>
                <tr>
                    <td>UPLOAD DATE</td>
                    <td>
                        {{ date('d-m-Y' ,strtotime(Input::get('date-from'))) }}
                    </td>
                </tr>
                <tr>
                    <td>DEVICE</td>
                    <td>
                        @if(is_null(Input::get('device')) || Input::get('device') == '')
                            All
                        @else
                            {{
                                Prefs::getDevice('id',Input::get('device'))->identifier
                            }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>COURIER</td>
                    <td>
                        @if(is_null(Input::get('courier')) || Input::get('courier') == '')
                            All
                        @else
                            {{  Prefs::getCourier('id',Input::get('courier'))->name
                            }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align:top;">
            {{--
            <table class="table table-bordered" style="height:100%;min-height:75px;">
                <thead>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <th>Laporan</th>
                        <th>Keuangan</th>
                        <th>Staff Dispatch</th>
                        <th>Staff Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="box" >&nbsp;</td>
                        <td class="box" >&nbsp;</td>
                        <td class="box" >&nbsp;</td>
                        <td class="box" >&nbsp;</td>
                        <td class="box" >&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            --}}

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>COD</th>
                        <th>CCOD</th>
                        <th>DO</th>
                        <th>PS</th>
                        <th>Retur</th>
                        <th>Rata-rata Waktu Pengiriman</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="box" >{{  $report_header_data['cod'] }}</td>
                        <td class="box" >{{  $report_header_data['ccod'] }}</td>
                        <td class="box" >{{  $report_header_data['do'] }}</td>
                        <td class="box" >{{  $report_header_data['ps'] }}</td>
                        <td class="box" >{{  $report_header_data['return'] }}</td>
                        <td class="box" >{{  $report_header_data['avg'] }}</td>
                    </tr>
                </tbody>
            </table>

        </td>
    </tr>

</table>
