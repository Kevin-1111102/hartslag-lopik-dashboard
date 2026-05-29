@props(['aedPhotoPath'])

<div class="col-6 col-md-4">
    <div class="position-relative border rounded p-1 bg-light h-100">
        <img
            src="{{ asset('storage/' . $aedPhotoPath) }}"
            alt="AED foto"
            class="img-fluid rounded"
            style="height: 120px; object-fit: cover; width: 100%;">

        <div class="position-absolute top-0 end-0 m-2">
            <button type="button" class="btn btn-sm btn-danger rounded-circle photo-remove-btn" data-photo-remove="1" aria-label="Verwijderen">
                ×
            </button>
        </div>
    </div>
</div>

