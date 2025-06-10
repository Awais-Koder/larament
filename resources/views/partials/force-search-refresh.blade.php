<script>
    document.addEventListener("DOMContentLoaded", function () {
        let searchInputs = document.querySelectorAll('input[placeholder="Search"]');
        searchInputs.forEach((input) => {
            input.addEventListener("keydown", function (e) {
                if (e.key === 'Enter' && input.value.trim() !== '') {
                    window.location.href = window.location.pathname + '?tableSearch=' + encodeURIComponent(input.value);
                }
            });
        });
    });
</script>
