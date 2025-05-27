{{-- css loading --}}
<style>
    #loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
{{-- html loading --}}
<div id="loading" style="display: none;">
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <i class="fa fa-spinner fa-spin fa-3x"></i> <!-- Font Awesome spinner -->
        <p>Loading...</p>
    </div>
</div>
{{-- js loading --}}
<script>
    $(document).ajaxStart(function() {
        $('#loading').show();
    });

    $(document).ajaxStop(function() {
        $('#loading').hide();
    });
</script>
