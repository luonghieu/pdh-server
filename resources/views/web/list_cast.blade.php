<!-- schedule -->
<div id="list-cast-wrapper">
{{--    @php $casts = $casts->toArray() @endphp--}}
{{--    {{ dd($casts->collection->toArray()) }}--}}
    @php $renderListCast = true; $casts = $casts->toArray(); @endphp
    @if (!count($casts))
        <div class="no-cast">
            <figure><img src="{{ asset('assets/web/images/common/woman2.svg') }}"></figure>
            <figcaption>キャストが見つかりません</figcaption>
        </div>
    @else
        <div class="cast-list">
            @include('web.users.load_more_list_casts', compact('casts', 'renderListCast'))
            <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] }}">
            <!-- loading_page -->
            @include('web.partials.loading_icon')
        </div> <!-- /list_wrap -->
@endif
<!-- Change favorite -->
</div>

