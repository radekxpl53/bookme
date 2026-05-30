<span class="text-warning">
    @for($i = 1; $i <= 5; $i++)
        <i class="bi {{ $i <= $rating ? 'bi-star-fill' : 'bi-star' }}"></i>
    @endfor
</span>
