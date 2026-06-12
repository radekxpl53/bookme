<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close bg-white rounded-circle p-2 position-absolute top-0 end-0 m-2"
                        data-bs-dismiss="modal" aria-label="Zamknij"></button>
                <img src="" alt="podgląd zdjęcia" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var imageModal = document.getElementById('imageModal');
        if (!imageModal) return;

        imageModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;
            imageModal.querySelector('img').src = trigger.getAttribute('data-img');
        });
    })();
</script>
