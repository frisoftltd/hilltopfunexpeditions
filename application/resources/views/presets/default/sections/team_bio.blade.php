@php
    $teamBioContent = getContent('team_bio.content', true);
@endphp
<section class="team-bio--section py-100 position-relative section--bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($teamBioContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ __($teamBioContent->data_values->heading) }}</h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($teamBioContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center align-items-center gy-4">
            <div class="col-lg-4">
                <div class="radius--20 overflow-hidden">
                    <img class="fit--img w--100"
                        src="{{ getImage(getFilePath('teamBio') . '/' . $teamBioContent->data_values->founder_image) }}"
                        alt="{{ __($teamBioContent->data_values->founder_name) }}">
                </div>
            </div>
            <div class="col-lg-7">
                <h4 class="fw--700 mb-1">{{ __($teamBioContent->data_values->founder_name) }}</h4>
                <p class="text--base fw--600 mb-3">{{ __($teamBioContent->data_values->founder_role) }}</p>
                <p>{{ __($teamBioContent->data_values->founder_bio) }}</p>
            </div>
        </div>
    </div>
</section>
