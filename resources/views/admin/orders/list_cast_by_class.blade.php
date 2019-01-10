@if (isset($casts))
  @foreach ($casts as $cast)
  <tr>
    <td class="select-checkbox">
      <input class="verify-checkboxs"
        type="checkbox"
        value=""
        name="casts_id[]">
    </td>
    <td>{{ $cast->id }}</td>
    <td>{{ $cast->nickname }}</td>
  </tr>
  @endforeach
@endif
