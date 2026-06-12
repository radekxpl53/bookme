<div class="star-rating d-inline-flex gap-1 fs-3" data-name="{{ $name }}">
    <input type="hidden" name="{{ $name }}" value="{{ old($name, $value ?? '') }}">
    @for($i = 1; $i <= 5; $i++)
        <i class="bi bi-star text-warning star" data-value="{{ $i }}" role="button"></i>
    @endfor
</div>
