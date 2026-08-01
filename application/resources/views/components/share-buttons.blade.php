@props(['shareTitle' => null])
@php
    $shareUrl = Request::url();
    $shareText = $shareTitle ? slug($shareTitle) : '';
@endphp
<div class="base--card radius--20 mb-4">
    <div class="auth--badge d-flex align-items-center gap--8 flex-wrap">
        <h5 class="social-share--title mb-0 me-sm-3 me-1 d-inline-block">@lang('Share This'):</h5>
        <ul class="social-list d-flex gap--12 mb-0">
            <li class="social-list--item">
                <a href="https://www.facebook.com/share.php?u={{ urlencode($shareUrl) }}&title={{ $shareText }}"
                    class="social-list__link d-flex justify-content-center align-items-center" target="_blank" rel="noopener">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </li>
            <li class="social-list--item">
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($shareUrl) }}&title={{ $shareText }}&source=behands"
                    class="social-list__link d-flex justify-content-center align-items-center" target="_blank" rel="noopener">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </li>
            <li class="social-list--item">
                <a href="https://twitter.com/intent/tweet?status={{ $shareText }}+{{ urlencode($shareUrl) }}"
                    class="social-list__link d-flex justify-content-center align-items-center" target="_blank" rel="noopener">
                    <i class="fab fa-twitter"></i>
                </a>
            </li>
            <li class="social-list--item">
                <button type="button"
                    class="social-list__link copy-link-btn d-flex justify-content-center align-items-center border-0"
                    title="@lang('Copy Link')">
                    <i class="fa-solid fa-link"></i>
                </button>
            </li>
        </ul>
    </div>
</div>

@once
    @push('script')
        <script>
            (function() {
                "use strict";
                // Delegated on document rather than bound per-button, so this
                // works regardless of how many .copy-link-btn instances exist
                // on the page and needs no per-instance id.
                document.addEventListener('click', function(e) {
                    var btn = e.target.closest('.copy-link-btn');
                    if (!btn) return;
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        var icon = btn.querySelector('i');
                        icon.classList.remove('fa-link');
                        icon.classList.add('fa-check');
                        setTimeout(function() {
                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-link');
                        }, 2000);
                    });
                });
            })();
        </script>
    @endpush
@endonce
