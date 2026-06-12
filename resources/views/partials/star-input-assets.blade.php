<style>
    .star-rating .star { cursor: pointer; }
</style>
<script>
    document.querySelectorAll('.star-rating').forEach(function (widget) {
        var input = widget.querySelector('input');
        var stars = widget.querySelectorAll('.star');

        function paint(value) {
            stars.forEach(function (star) {
                var v = parseInt(star.dataset.value);
                star.classList.toggle('bi-star-fill', v <= value);
                star.classList.toggle('bi-star', v > value);
            });
        }

        stars.forEach(function (star) {
            star.addEventListener('click', function () {
                input.value = star.dataset.value;
                paint(parseInt(input.value));
            });
        });

        paint(parseInt(input.value) || 0);
    });
</script>
