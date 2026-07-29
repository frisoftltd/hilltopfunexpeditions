@props(['review'])

<div class="review-card">
    <div class="user-info">
        <div class="thumb-wrap">
            <img class="fit--img"
                src="{{ getImage(getFilePath('userProfile') . '/' . $review->user->image, getFileSize('userProfile')) }}"
                alt="..">
        </div>
        <div class="user-name">
            <div class="d-flex align-items-center gap--8">
                <h1 class="name fs--20 fw--600 mb-0">
                    {{ $review->user->fullname }}
                </h1>
                <p class="fs--14">
                    {{ showDateTime($review->created_at, 'd M') }}
                </p>
            </div>
            <ul class="rating-wrap">
                @php echo calculateIndividualRating($review->star) @endphp
            </ul>
        </div>
    </div>
    <div class="content">
        <div class="discription">@php echo $review->review; @endphp</div>

        @if ($review->images->isNotEmpty())
            <div class="d-flex flex-wrap gap--8 mt-2">
                @foreach ($review->images as $image)
                    <a href="{{ getImage(getFilePath('reviewImage') . '/' . $image->image) }}" target="_blank">
                        <img src="{{ getImage(getFilePath('reviewImage') . '/' . $image->image, getFileSize('reviewImage')) }}"
                            alt="@lang('Review Image')" class="rounded" style="width: 70px; height: 70px; object-fit: cover;">
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
