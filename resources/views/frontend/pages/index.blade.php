@extends('frontend.layout.master')

@section('content')
    <style>
        .modal-content {
            background-clip: border-box !important;
            border: none !important;
            border-radius: 0 !important;
        }
    </style>
    <div class="ds-head-body">

        <div class="container mt-3">
            <!-- Slider container -->
            <div class="slider">
                <!-- slide 1 -->
                @foreach ($home_data['banner'] as $banners)
                    <div class="slide">
                        <img src="/{{ $banners['bannerImage'] }}" alt="" />
                    </div>
                @endforeach

                <!-- Control buttons -->
                <button class="bannerbtn bannerbtn-next" aria-label="Next slide"> > </button>
                <button class="bannerbtn bannerbtn-prev" aria-label="Previous slide">
                    < </button>
            </div>
        </div>


        <div class="astroway-menu bg-pink mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">

                        <ul class="list-unstyled d-flex mb-0 px-lg-4 px-xl-5 mx-xl-5 pt-3 pb-lg-3">

                            <li class="today-panchang">
                                <a href="{{ route('front.getPanchang') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $panchang['value'] }}" alt="" height="55"
                                                width="55">
                                        </div>
                                        <span class="d-block icon-desc">Today&#39;s Panchang</span>
                                    </div>
                                </a>
                            </li>


                            <li class="cheating-and-affairs">
                                <a href="{{ route('front.getkundali') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $freekundali['value'] }}" alt="" height="55"
                                                width="55">
                                        </div>
                                        <span class="d-block icon-desc">Free Janam Kundali</span>
                                    </div>
                                </a>
                            </li>
                            <li class="kundali-matching">
                                <a href="{{ route('front.kundaliMatch') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $kundali_matching['value'] }}" alt="" height="55"
                                                width="55">
                                        </div>
                                        <span class="d-block icon-desc">Kundali Matching</span>
                                    </div>
                                </a>
                            </li>

                            <li class="Online-Puja">
                                <a href="{{ route('front.getproducts') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $shop['value'] }}" alt="" height="55" width="55">
                                        </div>
                                        <span class="d-block icon-desc merital-life ">Products</span>
                                    </div>
                                </a>
                            </li>


                            <li class="daily-horoscope">
                                <a href="{{ route('front.horoScope') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $daily_horoscope['value'] }}" alt="" height="55"
                                                width="55">
                                        </div>
                                        <span class="d-block icon-desc">Free Daily Horoscope</span>
                                    </div>
                                </a>
                            </li>



                            <li class="astroway-blog mr-0">
                                <a href="{{ route('front.getBlog') }}">
                                    <div class="text-center mb-2 mb-md-0">
                                        <div class="icon">
                                            <img src="/{{ $blog['value'] }}" alt="" height="55" width="55">
                                        </div>
                                        <span class="d-block icon-desc">Astrology Blog</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <div class="container mt-5">
            <h2 class="text-md-center heading mb-3">Stories</h2>
            <p class="text-md-center mb-4">See Stories of top-rated Astrologers</p>
            <div class="stories-container">
                @foreach($stories as $story)
                <div class="story {{ $story->allStoriesViewed > 0 ? 'viewed' : '' }}" data-astrologer-id="{{ $story->astrologerId }}" data-astrologer-name="{{ $story->name }}" data-astrologer-profile="{{ $story->profileImage }}">
                    @if($story->profileImage)
                    <img src="/{{$story->profileImage}}" alt="{{$story->name}}">
                    @else
                    <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}" alt="{{$story->name}}">
                    @endif
                    <p>{{$story->name}}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Story Modal -->
        <div class="modal fade" id="storyModal" tabindex="-1" aria-labelledby="storyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img id="astrologerProfileImage" src="" alt="Astrologer Profile Image" class="rounded-circle" style="height: 40px;width:40px">
                        <span class="modal-title mt-2 ml-2" id="astrologerName"></span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators" id="carouselIndicators"></ol>
                            <div class="carousel-inner" id="carouselInner"></div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        {{-- End --}}


        <div class="astroway-live-astrologers slider-bullets py-2 my-md-5 pt-md-5">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading">LIVE SESSIONS</h2>
                        <p class="text-md-center mb-1">Connect with top-rated Astrologers through live sessions for
                            instant solutions</p>
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-sm-12">


                        <div class="owl-carousel owl-theme owl-blur owl-mobile">
                            @foreach ($liveastro['recordList'] as $live)
                                <div class="item gif-animation-enable mb-3"
                                    style="background:url('{{ $live['profileImage'] ? '/' . $live['profileImage'] : asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}')">
                                    <a href="{{ route('front.LiveAstroDetails', ['astrologerId' => $live['astrologerId']]) }}"
                                        class="text-white">
                                        <div class="position-relative live-expert">
                                            <div class="position-absolute top-part">
                                                <span
                                                    class="bg-red px-2 text-white d-inline-flex align-items-center rounded font-12"><i
                                                        class="fa fa-circle font-11 mr-1"></i>Live</span>
                                            </div>
                                            <div class="position-absolute bottom-part w-100 p-2">
                                                <div class="d-flex h-100 align-items-center">
                                                    <div
                                                        class="position-relative profile-pic bg-white d-none d-md-flex align-items-center justify-content-center">
                                                        @if ($live['profileImage'])
                                                            <img src="/{{ $live['profileImage'] }}" width="38"
                                                                height="38" loading="lazy" />
                                                        @else
                                                            <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                                                width="38" height="38" loading="lazy" />
                                                        @endif
                                                    </div>
                                                    <div class=" ml-2">
                                                        <p class="mb-0 pb-0 text-white font-16 text-capitalize">
                                                            {{ $live['name'] }}
                                                        </p>
                                                        <p class="mb-0 pb-0 text-yellow  font-12 text-capitalize">Tarot
                                                            Reading</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>




                        <div class="text-center pt-2">
                            <a href="{{ route('front.getLiveAstro') }}"
                                class="btn view-more colorblack font-weight-semi-bold">
                                View More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="astroway-astrologers py-5 bg-pink-light">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading">OUR ASTROLOGERS</h2>
                        <p class="text-md-center mb-1">Get in touch with the best Online Astrologers, anytime &amp;
                            anywhere!</p>
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-sm-12 ">
                        <div class="owl-carousel owl-theme owl-blur owl-mobile">

                            @foreach ($getAstrologer['recordList'] as $astrologer)
                                <div class="item p-3 mb-3 expertOnline bg-white"
                                    data-psychic-id="{{ $astrologer['id'] }}">
                                    <a href="{{ route('front.astrologerDetails', ['id' => $astrologer['id']]) }}">
                                        <div class="psychic-presence status-{{ $astrologer['id'] }}" data-status="Online"
                                            data-psychic-id="{{ $astrologer['id'] }}">
                                            <div id="psychic-{{ $astrologer['id'] }}-badge"
                                                class="status-badge specific-Clr-Online" title=""></div>
                                        </div>

                                        <div class="astro-profile">
                                            <div>
                                                @if ($astrologer['profileImage'])
                                                    <img src="/{{ $astrologer['profileImage'] }}" class="img-fluid"
                                                        loading="lazy">
                                                @else
                                                    <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                                        class="img-fluid">
                                                @endif
                                            </div>
                                            <p class="astro-name text-center colorblack text-capitalize"
                                                data-toggle="tooltip" title="{{ $astrologer['name'] }}"
                                                style="white-space: nowrap;text-overflow: ellipsis;display: block;overflow:hidden">
                                                {{ $astrologer['name'] }}</p>
                                        </div>
                                        <div>
                                            <p class="mb-0 colorblack text-center">Reviews: <span
                                                    class="color-red">{{ $astrologer['reviews'] }}</span></p>
                                            <p class="mb-0 text-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $astrologer['rating'])
                                                        <i class="fas fa-star filled-star"></i>
                                                    @else
                                                        <i class="far fa-star empty-star"></i>
                                                    @endif
                                                @endfor
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach



                        </div>
                    </div>
                </div>
            </div>

        </div>






        <div class="astroway-customer-stories bg-red-dark py-4 py-md-5">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading text-black">Astrology Videos</h2>

                    </div>
                </div>
                <div class="row pt-md-3">
                    <div class="col-sm-12">
                        <div class="owl-carousel owl-theme owl-youtube">

                            @foreach ($home_data['astrologyVideo'] as $astrologyVideo)
                                <a href="{{ $astrologyVideo['youtubeLink'] }}">
                                    <div class="tile">
                                        <div
                                            class="img-div position-relative d-flex justify-content-center align-item-center">

                                            <img style="cursor: pointer;" class="position-absolute youtube-icon w-auto"
                                                src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/youtube.svg') }}"
                                                alt="">

                                            <img class="youtube-image" src="/{{ $astrologyVideo['coverImage'] }}"
                                                alt="">

                                        </div>
                                        <p class="text-center text-center m-2 fw-bold">{{ $astrologyVideo['videoTitle'] }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach




                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="astroway-customer-stories bg-pink-light py-4 py-md-5">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading">New Products</h2>
                        <p class="text-md-center mb-1">See new products and how Astroway helped them
                            find their path to happiness!</p>
                    </div>
                </div>
                <div class="row pt-md-3">
                    <div class="col-sm-12">
                        <div class="owl-carousel owl-theme">

                            @foreach ($getAstromallProduct['recordList'] as $shop)
                                <div class="item mb-3">
                                    <a href="{{ route('front.getproductDetails', ['id' => $shop['id']]) }}">
                                        <img src="/{{ $shop['productImage'] }}" alt="{{ $shop['name'] }}"
                                            class="img-fluid" width="100" height="100" loading="lazy"
                                            style="height: 190px;width:100%;border-radius: 10px;">
                                    </a>
                                    <span
                                        class="d-block colorblack text-center font-weight-semi-bold pb-1">{{ $shop['name'] }}</span>
                                    {{-- <span class="d-block colorblack text-center font-14">TV Actress</span> --}}
                                    <div class="text-center pb-1">
                                        <p><span class="color-red">{{ $currency['value'] }}{{ $shop['amount'] }}</span>
                                        </p>
                                        <a href="{{ route('front.getproductDetails', ['id' => $shop['id']]) }}"
                                            class="btn view-more color-red font-weight-normal mb-2">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="astroway-publishers py-4 py-md-5 bg-brwon-extra-dark">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading text-white">As Seen On</h2>
                    </div>
                </div>
                <div class="row pt-md-3">
                    <div class="col-sm-12">
                        <div class="d-flex flex-wrap justify-content-center publishers-list">

                            @foreach ($home_data['astrotalkInNews'] as $astrotalkInNews)
                                <a href="{{ $astrotalkInNews['link'] }}" target="_blank">
                                    <div class="publisher row-1"><img src="/{{ $astrotalkInNews['bannerImage'] }}"
                                            alt="abp_live" class="img-fluid" width="173" height="42">
                                    </div>
                                </a>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="astroway-blog py-4 py-md-5">
            <div class="container">
                <div class="row pb-2">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading">
                            OUR BLOG <a href="{{ route('front.getBlog') }}" class="float-right d-md-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16.772" height="16.372"
                                    viewBox="0 0 16.772 16.372">
                                    <path id="Icon_awesome-arrow-right" data-name="Icon awesome-arrow-right"
                                        d="M6.706,3.677,7.487,2.9a.841.841,0,0,1,1.193,0l6.843,6.839a.841.841,0,0,1,0,1.193L8.68,17.771a.841.841,0,0,1-1.193,0l-.781-.781a.846.846,0,0,1,.014-1.207l4.242-4.041H.845A.843.843,0,0,1,0,10.9V9.77a.843.843,0,0,1,.845-.845H10.961L6.72,4.884A.84.84,0,0,1,6.706,3.677Z"
                                        transform="translate(0.5 -2.147)" stroke="#fff" stroke-width="1" />
                                </svg>
                            </a>
                        </h2>
                        <p class="text-md-center mb-1">Delve deeper into the world of Astrology, Psychic Reading
                            &amp; more with insightful articles and latest updates.</p>
                    </div>
                </div>
                <div class="row pt-md-3">
                    <div class="col-sm-12">
                        <div class="owl-carousel owl-theme">
                            @foreach ($home_data['blog'] as $blog)
                                <div class="item mb-3" d-flex>
                                    <a href="{{ route('front.getBlogDetails', ['id' => $blog['id']]) }}"
                                        class="colorblack">
                                        <img src="/{{ $blog['blogImage'] }}" alt="" class="img-fluid"
                                            loading="lazy" width="348" height="170" /></a>
                                    <div class="content p-3 bg-white">
                                        <p class="text-center font-weight-semi font-weight-bold category mb-2"><a
                                                href="{{ route('front.getBlogDetails', ['id' => $blog['id']]) }}"
                                                class="colorblack font-weight-bold">{{ $blog['title'] }}</a>
                                        </p>
                                        <p class="text-center font-14">
                                            <?php
                                            $description = $blog['description'];
                                            $words = explode(' ', $description);
                                            $trimmed_description = implode(' ', array_slice($words, 0, 15));
                                            echo $trimmed_description;
                                            if (count($words) > 15) {
                                                echo '...';
                                            }
                                            ?>
                                        </p>

                                        <div class="text-center pb-1">
                                            <a href="{{ route('front.getBlogDetails', ['id' => $blog['id']]) }}"
                                                class="btn view-more color-red font-weight-normal">
                                                Read More <svg xmlns="http://www.w3.org/2000/svg" width="12.583"
                                                    height="6.874" viewBox="0 0 12.583 6.874">
                                                    <path id="arrow"
                                                        d="M.855,296.24H10.889l-1.88,1.88a.573.573,0,0,0,.81.81l1.712-1.715,1.143-1.145a.571.571,0,0,0,0-.806l-2.855-2.858a.573.573,0,1,0-.81.81l1.884,1.88H.825a.572.572,0,1,0,.03,1.144Z"
                                                        transform="translate(-0.257 -292.234)" fill="#65A9FD" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="py-5 bg-pink-light">
            <div class="container">
                <div class="row ">
                    <div class="col-sm-12">
                        <h2 class="heading text-center">What Is Astrology?<span class="ml-3" data-toggle="collapse"
                                href="#collapse-faq" role="button" aria-expanded="false"
                                aria-controls="collapse-faq"><i class="fa fa-chevron-down color-red"></i></span></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="collapse py-4 font-14" id="collapse-faq">
                            <h3 class="font-weight-bold">Astrology Is The Language Of The Universe</h3>
                            <p>Astrology predictions are based on the position and movements of planets and
                                celestial bodies in the Universe that impact our life quality. This can be studied
                                by creating an offline or online horoscope of individuals. This affects not only the
                                people but also controls the occurrence of certain events happening in the sublunar
                                world.</p>
                            <p>Some may call it pseudo-science, and others call it predictive science. The science
                                that is Astrology inspires people to know the various aspects of their life and take
                                it in the right direction. From making life predictions on the basis of a detailed
                                Kundali or telling you about the near future through daily, weekly and monthly
                                horoscopes, Astrology is the medium through which you can get a glimpse of what the
                                future will bring for you.</p>
                            <p>There is one aspect of offline and online Astrology prediction where the impacts of
                                planetary transition can be seen. And when it is related to the Zodiacs, it happens
                                as various planets cross the sectors of each zodiac in the sky. It impacts the
                                natives of different zodiacs differently. And one more way is by analyzing the
                                planetary position in various houses of one&#39;s Kundli.</p>
                            <p>Astrology reading is quite extensive. It is all about studying the 9 planets placed
                                in the twelve houses of one&#39;s Kundli and their impact on their life. These
                                planets are the Sun, Moon, Mercury, Venus, Mars, Jupiter, Saturn, Rahu, and Ketu.
                                Some of these planets positively impact human life, and others affect it adversely.
                                It depends on their house placement.</p>
                            <p>For example, it is not always a compulsion that Saturn will bring negative impacts or
                                Jupiter will be a positive one.</p>
                            <p>Every house in the Kundli represents a different aspect of one&#39;s life. Similarly,
                                Sun Signs, Moon Signs, Ascendants, and Descendants have their own significance. So
                                it is not a confined subject, and the best way to know your future through the power
                                of Astrology is to talk to an online Astrologer and get a detailed analysis of your
                                online horoscope covering every aspect of your life.</p>

                            <h3 class="font-weight-bold">Astrology Predictions And Its Benefits</h3>
                            <p>Offline and online Astrology predictions have the power to forecast the future by
                                analyzing the positions of the planets as they move and studying their impact on
                                your life.</p>
                            <p>An online horoscope is essentially a blueprint of your life that can help you gain
                                clarity about the different aspects of your life, your personality and your future.
                                Although there are several benefits of Astrological predictions, the best one
                                remains timely guidance, and remedial suggestions to help avoid any unfavorable
                                events coming your way. Or even if not eliminate them altogether, the offline and
                                online Astro remedies can at least minimize their impacts. It is best if the
                                guidance comes from the best Astrologer in India.</p>
                            <p>You can take advantage of staying a step ahead of time in every aspect of your life,
                                be it love, money, career, marriage, family, or anything else. Online Astrology has
                                the power to show you the right path that will lead you towards a successful and
                                happy life.</p>

                            <h3 class="font-weight-bold">How Online Astrology Services Can Benefit You</h3>
                            <p>You know how well you can take your life in the right direction with right Astro
                                guidance, so why not get it from the comfort of your home.</p>

                            <p>Keeping the convenience, comfort and flexibility in mind, Astroway has
                                introduced the best online Astrology consultation services. You can choose from
                                online Astrologers, numerologists, palmists, and <a href="#" target="_blank">tarot
                                    reading experts</a> to get answers for your concerns.
                                This has been done while keeping various factors in mind that can benefit you.</p>
                            <ul class="pl-3">
                                <li>It is the most hassle-free way to connect with the best Astrologers.</li>
                                <li>Online Astrology services are the most time-saving and affordable way to connect
                                    with top Astrologers and get consultations, anytime and anywhere.</li>
                                <li>It makes it convenient for people to talk to an Astrologer openly as your
                                    privacy and confidentiality is strictly maintained.</li>
                                <li>You can choose the best Astrologer online among nearly 100+ Astrologers that you
                                    think matches your requirements perfectly.</li>
                            </ul>


                            <h3 class="font-weight-bold">Online Astrology Consultation Services By Astroway
                            </h3>
                            <p>Astroway has established its footprints in the online Astrology services,
                                helping people get through their life problems. This is done by the best online
                                Astrologers who are experienced and renowned in this domain. Our Astrologers are
                                available 24/7 to help people with their Astro advice on the best website for
                                Astrology.</p>
                            <p>Astroway strives to provide the best Astrology consultation services by the best
                                Astrologers. Our professional Astrologers are not only limited to providing guidance
                                and insights into various aspects of your life. They are also your friend and
                                partner to get you through difficult situations. Another thing is that they are not
                                only traditional Astrologers. There are also tarot reading experts and <a href="#"
                                    target="_blank">numerologists</a> to give you a range of Astrology services.
                            </p>
                            <p>You know that you need an online Astrology reading session at Astroway, so you
                                should understand how it works.</p>
                            <p>Here are the steps you can follow to reach the expert Astrologers on the best
                                Astrologer site.</p>
                            <ul class="pl-3">
                                <li>Download the Astroway app</li>
                                <li>Sign up with your basic details</li>
                                <li>Enjoy your free session of online Astrology consultation</li>
                                <li>Recharge your wallet</li>
                                <li>Choose the best Astrologer online with whom you want to consult</li>
                                <li>Enjoy your live chat/call session with the best online Astrologers</li>
                            </ul>
                            <p>So are you now confused about how you can choose the best Astrologer for your
                                session? The one who can make the most accurate online horoscope? Here are the
                                things to consider.</p>
                            <p>First of all, categorize your query based on various issues like love, finance,
                                family, etc. Then look for the expert Astrologers of that particular aspect and
                                choose them based on the ratings they get from their clients. These ratings are
                                based on the quality of the session. Or you can go a step further and read their
                                descriptions where their experience and expertise are mentioned.</p>
                            <p>That&#39;s how you will get in touch with the expert Astrologer that will provide the
                                guidance you need for all your life problems along with the most effective
                                solutions.</p>


                            <h3 class="font-weight-bold">Online Astrologers Of Astroway</h3>
                            <p>Astroway connects you with India&#39;s top astrologers!</p>
                            <p>We at Astroway consider it our responsibility to connect you with India&#39;s
                                best online astrologers. And to make sure that you get the most satisfactory
                                experience after each session, whether through live chat or call, we are highly
                                particular about choosing our Astrologers.</p>
                            <p>There are a lot of factors that we consider before an Astrologer comes on board with
                                us.</p>
                            <ul class="pl-3">
                                <li>Educational qualifications</li>
                                <li>Area of expertise</li>
                                <li>Years of experience</li>
                                <li>Method of practice (Astrology, numerology, tarot card reading, etc.)</li>
                            </ul>
                            <p>We make sure that our clients get what they expect. So, we ensure that only the best
                                and the most knowledgeable Astrologers are associated with us. Astrologers go
                                through a multi-layer screening process to become a part of our community. And they
                                come from all over the country. All the Astrologers who are associated with us are
                                certified and verified for their area of expertise. We leave no stone unturned to
                                ensure you get the best guidance by the best Astrologers.</p>
                            <p>You can get their guidance regarding <a href="#" target="_blank">your online
                                    horoscope</a>, Kundli matching, general online
                                predictions, etc.</p>
                            <p>Search for the phrase &#39;the best astrologer near me,&#39; and you will get the
                                relevant results wherever you are. But with Astroway, you will still find the
                                best astrologers and get their guidance from the comfort of your home.</p>
                            <p>So whenever you consult with an expert astrologer at Astroway, you get only the
                                best!</p>


                            <h3 class="font-weight-bold">Online Astrology Predictions Categories</h3>
                            <p>You can discuss anything troubling you with a professional Astrologer. Still, in case
                                you need clarity, here are the buckets of specific categories in which you can put
                                your queries.</p>

                            <ul class="pl-3">
                                <li>
                                    <p class="font-weight-bold mb-0">Love and relationships</p>
                                    <p>Here, you can <a href="#" target="_blank">ask an Astrologer any question
                                            related to your
                                            relationship</a>, whether past, present, or future. It also answers the
                                        question about your ex's feelings or maybe issues related to cheating, etc.
                                    </p>
                                </li>
                                <li>
                                    <p class="font-weight-bold mb-0">Marriage and family</p>

                                    <p><a href="#" target="_blank">Ask questions related to your married life</a>.
                                        It
                                        taps the issues related to infidelity, general future, or even second
                                        marriage.</p>
                                </li>
                                <li>
                                    <p class="font-weight-bold mb-0">Career and job</p>
                                    <p>Under this category, all the questions related to your work will be placed.
                                        It can be anything from workplace conflicts to promotions to being confused
                                        between two options.</p>
                                </li>
                                <li>
                                    <p class="font-weight-bold mb-0">Money and finance</p>
                                    <p>This category will have questions that concern money. It may be related to
                                        your current financial position or the future, or maybe the reasons
                                        affecting it or how you can improve.</p>
                                </li>
                            </ul>
                            <p>These are the four primary and basic categories under which almost every question can
                                be put. Then it will be convenient for you to choose the expert Astrologers who will
                                answer your question. It will be done through Vedic Astrology predictions, tarot
                                reading, numerology, and palmistry to give you the best insights.</p>
                            <p>Astroway is your ultimate destination for all your online Astrology consultation
                                needs. Here you can get the best guidance from the top Astrologers who will help you
                                make the best and the most beneficial decisions in life.</p>

                            <h3 class="font-weight-bold pt-4 pb-3">FAQs Related To Astrology &amp; Astroway
                            </h3>
                            <div itemscope itemtype='https://schema.org/FAQPage'>
                                <ol class="pl-3">
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">What are Astrology
                                                predictions based on?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Astrology predictions are basically the
                                                    analysis of the position of planets and stars and how they move
                                                    to impact the world and each individual existing there. So the
                                                    basis of offline and online Astrology predictions is the
                                                    movement and transits of the planets in the Universe.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">What are Astrology
                                                and zodiac?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Zodiac signs are the signs that develop the
                                                    internal and external personality of someone, and Astrology
                                                    defines the changes in that personality concerning the planetary
                                                    movements.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">How do Astrology
                                                predictions help me to deal with my problems?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Astrology predictions can keep you a step ahead
                                                    of time where you can know what is waiting for you in the
                                                    future. And with proper guidance, you can be better prepared to
                                                    deal with the problems and challenges you might face in the
                                                    future.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">How can online
                                                Astrology predictions be so accurate? Is there any scientific reason
                                                behind it?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Astrology services are based on
                                                    pseudo-scientific practice that provides Astrology predictions
                                                    to individuals based on the movements of planets. These offline
                                                    and online Astrology predictions can be general and specific
                                                    depending on the type of reading.</p>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">How reliable are
                                                the Astroway Astrologers?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">The credibility of Astroway&#39;s
                                                    Astrologers can be seen through the reviews and the ratings they
                                                    get from people like you after their session with them. All our
                                                    Astrologers are verified for their experience and expertise.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">Can I ask personal
                                                questions?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Astroway&#39;s Astrologers have expertise
                                                    in every aspect of life. This includes both personal and general
                                                    queries. So you can very well ask an Astrologer online any
                                                    question related to the issue that is troubling you.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">What type of a
                                                question can I ask an Astrologer?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">A good Astrologer is there to solve all your
                                                    queries and concerns regarding life. So you can ask an
                                                    Astrologer any question except those that break the sanctity of
                                                    this spiritual practice. It includes queries related to black
                                                    magic, death, afterlife, etc.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">Can I speak to the
                                                same Astrologer when I call again?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Yes, you can always choose the Astrologer of
                                                    your choice. And if you want to talk to the same Astrologer
                                                    again, you have to select them again for your session through
                                                    the defined process.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">Can I talk to an
                                                Astrologer for free?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">You can connect with the best Astrologers
                                                    without paying anything for your first session. After that, you
                                                    need to recharge your wallet with a basic amount to connect with
                                                    them. You can either chat with an Astrologer or call them.</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">How much does it
                                                cost to see an Astrologer?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <div itemprop="text">
                                                    <p>You can connect with the Astrologers through live chat or
                                                        call. You need to sign up and register absolutely for free
                                                        to get there. After that, you can also avail your first free
                                                        chat session but moving forward, you need to recharge your
                                                        wallet.</p>
                                                    <p>The rates of each Astrologer vary. These are based on their
                                                        expertise, experience, and exposure. So how much you will
                                                        pay will depend on the Astrologer you choose.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <p itemprop="name" class="font-weight-bold mb-0">Who is the best
                                                online Astrologer?</p>
                                            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                <p itemprop="text">Every Astrologer at Astroway is the best.
                                                    Still, the one who can cater to your specific requirements based
                                                    on the area of expertise will be the best for you. At Anytime
                                                    Astro, you can connect with the best Astrologers in India.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="astroway-about d-none d-md-block py-4 py-md-5">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-md-center heading">WHY ASTROWAY?</h2>
                        <p class="text-md-center">One of the best online Astrology platforms to connect with
                            experienced and verified Astrologers</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="accordion" id="faq">
                            <div class="card">
                                <div class="card-header" id="faqhead1">
                                    <h3 class="panel-title mb-0">
                                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse"
                                            data-target="#verified-astrologers" aria-expanded="true"
                                            aria-controls="verified-astrologers">

                                            Verified Astrologers
                                        </a>
                                    </h3>
                                </div>

                                <div id="verified-astrologers" class="collapse" aria-labelledby="faqhead1"
                                    data-parent="#faq">
                                    <div class="card-body">
                                        Astroway helps you connect with the best online Astrologers in India
                                        who will guide you through all the problems of your life and provide answers
                                        to all your queries through accurate Astrology predictions. Be it your love
                                        problems or money problems, our Astrologers can give you guidance on each
                                        and every aspect of your life You can chat with our Astrologers Live or on
                                        call and ask all your concerns. Whether it is Vedic Astrology, Tarot
                                        Reading, Psychic Reading, Horoscope or Numerology, we have certified online
                                        Astrologers who can provide you with the most accurate astro advice for your
                                        concern and give you effective solutions and remedies to resolve your
                                        problems.

                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqhead2">
                                    <h3 class="panel-title mb-0">
                                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse"
                                            data-target="#multiple-ways-to-connect" aria-expanded="true"
                                            aria-controls="multiple-ways-to-connect">Ask An
                                            Astrologer Via Multiple Ways</a>
                                    </h3>
                                </div>

                                <div id="multiple-ways-to-connect" class="collapse" aria-labelledby="faqhead2"
                                    data-parent="#faq">
                                    <div class="card-body">
                                        <p> By offering you multiple ways to connect with online Astrologers, we
                                            make sure that you get the guidance you seek, anytime and anywhere.
                                        </p>
                                        <p>We offer Online Astrology consultation, through which you can connect
                                            with our Astrologers LIVE through a one-on-one chat or a call session.
                                            You can also opt to send a message to your chosen Astrologer and book a
                                            live session with them for online Astrology reading according to your
                                            concern, time and flexibility.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqhead3">
                                    <h3 class="panel-title mb-0">
                                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse"
                                            data-target="#privacy-guaranteed" aria-expanded="true"
                                            aria-controls="privacy-guaranteed">100% Privacy
                                            Guaranteed</a>
                                    </h3>
                                </div>

                                <div id="privacy-guaranteed" class="collapse" aria-labelledby="faqhead3"
                                    data-parent="#faq">
                                    <div class="card-body">
                                        At Astroway, your privacy and security is our top priority. We adopt
                                        the highest security standards to keep your data and information secure. We
                                        ensure complete anonymity of your personal data, and any other information
                                        that you share with our Astrologers. Our platform operates in a 100% secure
                                        setting, so you can connect online with Astrologers without worrying about
                                        anything.

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="astroway-about-mobile d-md-none bg-pink py-4">
            <div class="container">
                <h2 class="heading text-center">WHY ASTROWAY?</h2>
                <div class="row pt-4 pb-2">
                    <div class="col-4 text-center">
                        <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about1.svg') }}"
                            class="img-fluid" />
                        <p class="font-weight-semi-bold pt-3 font-14">Verified Astrologers</p>
                    </div>
                    <div class="col-4 text-center">
                        <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about2.svg') }}"
                            class="img-fluid" />
                        <p class="font-weight-semi-bold pt-3 font-14">Ask An Astrologer Via Multiple Ways</p>
                    </div>
                    <div class="col-4 text-center">
                        <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/about3.svg') }}"
                            class="img-fluid" />
                        <p class="font-weight-semi-bold pt-3 font-14">100% Privacy Guaranteed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var owl = $('.astroway-astrologers .owl-carousel');

            if ($(window).width() > 767) {
                owl.owlCarousel({
                    margin: 0,
                    responsive: {
                        0: {
                            items: 2,
                            slideBy: 2
                        },
                        370: {
                            items: 2.3,
                            slideBy: 2
                        },
                        768: {
                            items: 2.4,
                            slideBy: 2,
                            nav: true
                        },
                        992: {
                            nav: true,
                            items: 3
                        },
                        1199: {
                            nav: true,
                            items: 5
                        }
                    }
                });
            }
            owl.removeClass('owl-blur');

            $('#main_nav').on('shown.bs.collapse', function() {
                $('#navbarDropdown').dropdown('toggle');
            });


            $('.owl-youtube.owl-theme').owlCarousel({
                loop: true,
                nav: true,
                margin: 0,
                responsive: {
                    0: {
                        margin: 5,
                        items: 1
                    },
                    600: {
                        items: 2,
                        margin: 15,
                    },
                    991: {
                        center: true,
                        nav: true,
                    },

                }
            })


            var owl = $('.astroway-live-astrologers .owl-carousel');
            if ($(window).width() > 767) {
                owl.owlCarousel({
                    margin: 0,
                    responsive: {
                        0: {
                            items: 2,
                            slideBy: 2
                        },
                        370: {
                            items: 3.3,
                            slideBy: 2
                        },
                        768: {
                            items: 3,
                            slideBy: 2,
                            nav: true
                        },
                        992: {
                            nav: true,
                            items: 4
                        },
                        1199: {
                            nav: true,
                            items: 5
                        }
                    }
                });
            }
            owl.removeClass('owl-blur');

            $('#main_nav').on('shown.bs.collapse', function() {
                $('#navbarDropdown').dropdown('toggle');
            });


            $('.astroway-blog .owl-carousel').owlCarousel({
                loop: true,
                margin: 0,
                responsive: {
                    0: {
                        items: 1
                    },
                    340: {
                        items: 1.5,
                    },
                    768: {
                        items: 2,
                        nav: true
                    },
                    992: {
                        items: 3,
                        nav: true
                    }
                }
            });


            $('.astroway-customer-stories .owl-carousel').owlCarousel({
                loop: true,
                margin: 0,

                responsive: {
                    0: {
                        items: 1,
                        mouseDrag: true,
                        touchDrag: true
                    },
                    380: {
                        items: 1.5,
                        mouseDrag: true,
                        touchDrag: true
                    },
                    768: {
                        items: 2,
                        nav: true
                    },
                    992: {
                        items: 3,
                        nav: true
                    }
                }
            });



        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let slides = document.querySelectorAll('.slide');
            let currentIndex = 0;

            document.querySelector('.bannerbtn-next').addEventListener('click', function() {
                slides[currentIndex].style.left = '-100%';
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].style.left = '0';
            });

            document.querySelector('.bannerbtn-prev').addEventListener('click', function() {
                slides[currentIndex].style.left = '100%';
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                slides[currentIndex].style.left = '0';
            });

            // Initially position all slides
            slides.forEach((slide, index) => {
                slide.style.left = index === 0 ? '0' : '100%';
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.story').on('click', function() {
                var astrologerId = $(this).data('astrologer-id');
                var astrologerName = $(this).data('astrologer-name');
                var astrologerProfile = $(this).data('astrologer-profile');
                // console.log(astrologerProfile);

                if (!astrologerProfile) {
                    astrologerProfile = 'public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png';
                }
                // Make an AJAX request to get the stories
                $.ajax({
                    url: '/astrologer/' + astrologerId + '/stories',
                    method: 'GET',
                    success: function(response) {
                        openStoryModal(response, astrologerName,astrologerProfile);
                    },
                    error: function(error) {
                        console.error('Error fetching stories:', error);
                    }
                });
            });
        });

        function openStoryModal(stories, name , profileImage) {
            var modal = $('#storyModal');
            var astrologerProfileImage = $('#astrologerProfileImage');
            var astrologerName = $('#astrologerName');
            var carouselIndicators = $('#carouselIndicators');
            var carouselInner = $('#carouselInner');
            var modalTitle=$('#astrologerName');

            // Clear existing slides and indicators
            carouselIndicators.empty();
            carouselInner.empty();

            // Add new slides and indicators
            stories.forEach((story, index) => {
                var indicator = $('<li>')
                    .attr('data-target', '#carouselExampleIndicators')
                    .attr('data-slide-to', index);
                if (index === 0) {
                    indicator.addClass('active');
                }
                carouselIndicators.append(indicator);

                var carouselItem = $('<div>')
                    .addClass('carousel-item');
                if (index === 0) {
                    carouselItem.addClass('active');
                }

                if (story.mediaType === 'image') {
                    var img = $('<img>')
                        .addClass('d-block w-100')
                        .attr('src', story.media);
                    carouselItem.append(img);
                } else if (story.mediaType === 'video') {
                    var video = $('<video>')
                        .addClass('d-block w-100')
                        .attr('controls', true);
                    var source = $('<source>')
                        .attr('src', story.media)
                        .attr('type', 'video/mp4');
                    video.append(source);
                    carouselItem.append(video);
                } else if (story.mediaType === 'text') {
                    var text = $('<div>')
                        .addClass('d-block w-100 text-center')
                        .css({
                            'padding': '20px',
                            'font-size': calculateFontSize(story.media)
                        })
                        .text(story.media);
                    carouselItem.append(text);
                }
                @if(authcheck())
                trackStoryView(story.id);
                @endif
                carouselInner.append(carouselItem);
            });

            modalTitle.text(name);
            astrologerProfileImage.attr('src', profileImage);

            modal.modal('show');

            // Stop auto sliding
                $('.carousel').carousel('pause');


            function calculateFontSize(text) {
                var baseFontSize = 30;
                var maxLength = 200;
                var fontSize = baseFontSize;

                if (text.length > maxLength) {
                    fontSize = baseFontSize - ((text.length - maxLength) / 10);
                }

                return fontSize + 'px';
            }


            function trackStoryView(storyId) {
                $.ajax({
                    url: 'astrologer/viewstory',
                    method: 'POST',
                    data: {
                        storyId: storyId
                    },
                    success: function(response) {
                        console.log(response.message);
                    },
                    error: function(error) {
                        console.error('Error viewing story:', error);
                    }
                });
         }
    }
    </script>
@endsection
