<script>
    (function() {
        let url =  "<?= $filename ?>";
        location.replace(url);
        setTimeout(function() {
            window.close()
        }, 500);
    })();
</script>