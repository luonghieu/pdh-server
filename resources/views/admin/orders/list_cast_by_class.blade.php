@if (isset($casts))
  @php
  switch (request()->type) {
      case 1:
          $userHide = 'nomination';
          break;
      case 2:
          $userHide = 'candidate';
          break;
      case 3:
          $userHide = 'matching';
          break;
      
      default:
          $userHide = '';
          break;
  }
  @endphp
  @foreach ($casts as $cast)
    <tr data-user-id="{{ $cast->id }}" id="{{ $userHide }}-{{ $cast->id }}">
      <td class="select-checkbox">
        <input class="verify-checkboxs"
          type="checkbox"
          value="{{ $cast->id }}"
          name="casts_id[]">
      </td>
      <td>{{ $cast->id }}</td>
      <td>{{ $cast->nickname }}</td>
    </tr>
  @endforeach
@endif