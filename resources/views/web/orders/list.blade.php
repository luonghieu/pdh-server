@section('title', 'Cheers')
@section('screen.class', 'gn-1')

@extends('layouts.web')
@section('web.content')
  <div class="list_wrap">
  @if(count($orders['data']))

  @foreach ($orders['data'] as $order)
   <div class="list_item">
     <div class="item_up">
       <ul class="item_left">
         <li class="time-text">
         {{ Carbon\Carbon::parse($order['date'])->format('m月d日(土)') }}
         </li>
         <li class="time-text">{{ $order['address'] }} {{ Carbon\Carbon::parse($order['start_time'])->format('H:i') }}〜</li>
         @if(count($order['tags']))
         <li class="tag-text">
         @foreach($order['tags'] as $tag)
         #{{ $tag['name'] }}
         @endforeach
         </li>
         @endif
       </ul>
       <ul class="item_right">
         <li class=""><span class="icon1 icon-size-w11">{{ $order['duration'] }}時間</span></li>
         <li class=""><span class="icon2 icon-size-w17">{{ number_format($order['temp_point']) }}P〜</span></li>
         <li class=""><span class="icon3 icon-size-w15">{{ $order['total_cast'] }}名</span></li>
       </ul>
     </div>
     <div class="item_down">
       <hr class="border-blue">
       <ul class="face-img">
        @foreach($order['casts'] as $cast)
         <li class=""><img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="顔の写真"></li>
        @endforeach
       </ul>
     </div>
     <div class="btn_wrap">
       <section class="btn-cancel">
        <label for="cancel" data-id ="{{ $order['id'] }}" class="lb-cancel" >キャンセル</label>
       </section>
       <button class="mess-btn" type="button" name="button">メッセージを確認</button>
     </div>
   </div> <!-- /list_item -->
  @endforeach
  <section class="modal-cancel-order">
    <label for="md-cancel" class="lb-modal-cancel" >キャンセル</label>
  </section>
  @endif
 </div>  <!-- /list_wrap -->
@endsection

@section('web.extra')
  @confirm(['triggerId' => 'cancel', 'buttonLeft' =>'はい',
   'buttonRight' =>'いいえ', 'triggerCancel' =>'cf-cancel-order','triggerSuccess' =>''])
    @slot('title')
      この日程をキャンセルしますか？
    @endslot

    @slot('content')
    @endslot
  @endconfirm

  @modal(['triggerId' => 'md-cancel', 'triggerClass' =>'md-cancel-order'])
    @slot('title')
      予約キャンセルが完了しました
    @endslot

    @slot('content')
    @endslot
  @endmodal
@endsection
