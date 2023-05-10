<link href="{{ get_asset('admin_assets') }}/libs/summernote/summernote-bs4.css" rel="stylesheet" type="text/css" />
<script src="{{ get_asset('admin_assets') }}/libs/summernote/summernote-bs4.min.js"></script>
<script>
    $(function() {
        $(".editor").summernote({
            height: 250
            , minHeight: null
            , maxHeight: null
            , focus: false
        });
    })
</script>