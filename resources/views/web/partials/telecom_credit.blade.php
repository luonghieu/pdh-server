@if (auth()->check() && auth()->user()->is_guest)
<form action="{{ env('TELECOM_CREDIT_VERIFICATION_URL') }}" method="POST" id="telecom-credit-form">
  <input type="hidden" name="clientip" value="{{ env('TELECOM_CREDIT_CLIENT_IP') }}">
  <input type="hidden" name="usrtel" value="{{ auth()->user()->phone }}">
  <input type="hidden" name="usrmail" value="{{ auth()->user()->email }}">
  <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
  <input type="hidden" name="redirect_url" value="{{ url()->full() }}">
</form>
@endif
