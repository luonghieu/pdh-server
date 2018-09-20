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
<script src="{{ asset('assets/web/js/common.js') }}"></script>