@php
    $faq = getContent('faq.content', true);
    $faqElements = getContent('faq.element', false, 8);
    $time = 0.1;
@endphp

<section class="faq-section {{ request()->path() == 'about' ? 'py-100 ' : 'py-100 section--bg' }} position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($faq->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ __($faq->data_values->heading) }}</h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($faq->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>

        @php
            $leftColumn = [];
            $rightColumn = [];
            $time = 0;
        @endphp

        @foreach ($faqElements ?? [] as $index => $item)
            @if ($index % 2 == 0)
                @php $leftColumn[] = ['item' => $item, 'delay' => $time += 0.1]; @endphp
            @else
                @php $rightColumn[] = ['item' => $item, 'delay' => $time += 0.1]; @endphp
            @endif
        @endforeach

        <div class="row gy-5 justify-content-between position-relative">
            @foreach (['leftColumn' => $leftColumn, 'rightColumn' => $rightColumn] as $col => $items)
                @php $accordionId = "accordionFlush_$col"; @endphp
                <div class="col-lg-6 mt-4">
                    <div class="accordion custom--accordion1 accordion-flush" id="{{ $accordionId }}">
                        @foreach ($items as $i => $data)
                            @php
                                $item = $data['item'];
                                $delay = $data['delay'];
                                $collapseId = "flush-collapse-{$col}-{$i}";
                            @endphp
                            <div class="accordion-item wow animate__fadeInUp animate__animated" data-wow-delay="{{ $delay }}s">
                                <div class="accordion-header">
                                    <div class="bar"></div>
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                                        aria-controls="{{ $collapseId }}">
                                        {{ __($item->data_values->question) }}
                                    </button>
                                </div>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                    data-bs-parent="#{{ $accordionId }}">
                                    <div class="accordion-body">
                                        {!! $item->data_values->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>


    </div>


</section>
