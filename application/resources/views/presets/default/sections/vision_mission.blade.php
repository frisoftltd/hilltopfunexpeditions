@php
    $visionMissionContent = getContent('vision_mission.content', true);
    $visionMissionValues = $visionMissionContent->data_values ?? null;
    $visionMissionTitle = $visionMissionValues->title ?? '';
    $visionMissionHeading = $visionMissionValues->heading ?? '';
    $visionMissionSubHeading = $visionMissionValues->sub_heading ?? '';
    $visionText = $visionMissionValues->vision_text ?? '';
    $missionText = $visionMissionValues->mission_text ?? '';
@endphp
<section class="vision-mission--section py-100 position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($visionMissionTitle) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ __($visionMissionHeading) }}</h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($visionMissionSubHeading) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 justify-content-center">
            <div class="col-lg-5">
                <div class="base--card section--bg__two radius--16 h--100 p-4">
                    <h5 class="fw--700 mb-3">@lang('Vision')</h5>
                    <p>{{ __($visionText) }}</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="base--card section--bg__two radius--16 h--100 p-4">
                    <h5 class="fw--700 mb-3">@lang('Mission')</h5>
                    <p>{{ __($missionText) }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
