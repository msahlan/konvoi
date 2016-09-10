<table class="table" id="par_{{ $file_id }}" >
    <tr>
        <td rowspan="6">
            <img style="width:100px;" src="{{ $thumbnail_url }}"><br />
            <span class="file_copy" data-clipboard-text="{{ $full_url }} ><i class="icon-copy"></i> copy URL</span>
            {{ Former::hidden('fileid')->value( $file_id ) }}
        </td>
        <td>
            <span class="img-title">{{ $filename }}</span>
            {{ Former::text('caption')->class('caption')->id('cap_'.$file_id ) }}
            {{ Former::text('keyword')->class('keyword')->id('key_'.$file_id ) }}
        </td>
        <td>
            <span class="file_del" ><i class="file_del fa fa-trash-o" id="{{ $file_id }}"></i>
            </span>
        </td>
    </tr>
</table>
