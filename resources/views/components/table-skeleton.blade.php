@props(['rows' => 5, 'cols' => 4])

@for($i = 0; $i < $rows; $i++)
<tr>
    @for($j = 0; $j < $cols; $j++)
    <td>
        <div class="skeleton skeleton-text" style="width: {{ rand(40, 90) }}%"></div>
    </td>
    @endfor
</tr>
@endfor
