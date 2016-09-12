<table class="table" id="par_{{ $filedata['_id'] }}" >
    <tr>
        <td rowspan="6">
            @if($filedata['is_doc'])
                <span class="fa-stack fa-2x">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-file fa-stack-1x fa-inverse"></i>
                </span><br />
            @else
                <img class="img-circle" style="width:45px;height:45px" src="{{ $filedata['square_url'] }}"><br />
            @endif
            <span class="file_copy" data-clipboard-text="{{ $filedata['url'] }}><i class="icon-copy"></i> copy URL</span>
        </td>
        <td>
            <span class="img-title">{{ $filedata['name'] }}</span><br />
            <input type="hidden" name="fileid[]" value="{!! $filedata['_id'] !!}" class="file-id" />
        </td>

        <td><span class="file_del" ><i class="file_del fa fa-trash-o" id="{{ $filedata['_id'] }}"></i></span></td>
    </tr>
</table>
