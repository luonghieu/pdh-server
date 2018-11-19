<footer>
  <small>Copyright &copy; 2018 Cheers</small>
</footer>
<script src="{{ asset('assets/web/js/jquery.js') }}"></script>
<script src="{{ asset('assets/web/js/iscroll.js') }}"></script>
<script src="{{ asset('assets/web/js/mmenu/jquery.mhead.js') }}"></script>
<script src="{{ asset('assets/web/js/mmenu/jquery.mmenu.all.js') }}"></script>
<script>
    jQuery(document).ready(function( $ ) {
        $("#menu").mmenu({
            "extensions": [
                "position-right"
            ]
        });
    });
</script>
<script src="{{ mix('assets/web/js/common.min.js') }}"></script>
<script src="{{ asset('assets/web/js/slick/slick.min.js') }}"></script>
<script src="{{ mix('assets/web/js/gf-2.min.js') }}"></script>
<script src="{{ mix('assets/web/js/ge-2-1-a.min.js') }}"></script>
<script src="{{ asset('assets/web/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/web/js/moment.min.js') }}"></script>
<!-- Improve load list image -->
<script src="{{ asset('assets/web/js/lazy/jquery.lazy.min.js') }}"></script>
<script src="{{ mix('assets/web/js/lazy/loading_image.min.js') }}"></script>
