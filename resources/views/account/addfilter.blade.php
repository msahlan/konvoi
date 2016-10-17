<table class="table">
    <tr>
        @for($i = 1; $i < 32;$i++)
            <td>
                {{ $i }}<hr>{{ $day_load[$i] }}
            </td>
        @endfor
    </tr>
</table>