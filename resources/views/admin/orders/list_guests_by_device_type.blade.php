@if (isset($guests))
  @foreach ($guests as $guest)
    <tr data-user-id="{{ $guest->id }}" id="">
      <td class="select-checkbox">
        <input class="checked-guest"
          type="checkbox"
          value="{{ $guest->id }}"
          name="guests_id[]">
      </td>
      <td>{{ $guest->id }}</td>
      <td>{{ $guest->nickname }}</td>
    </tr>
  @endforeach
@endif
