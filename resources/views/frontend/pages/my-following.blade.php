@extends('frontend.layout.master')
@section('content')
<style>
    .psychic-card .btn {
    min-width: 130px;
    height: 35px;
    font-size: 20px;
}
</style>
    <div class="ds-head-body">

        <div class="container">
            <div class="row">
                <div class="col-sm-12 expert-search-section-height-favourites">
                    <h1 class="h2 font-weight-bold colorblack mb-1 mb-md-4 mt-sm-3">
                        My Followings
                    </h1>
                    <div class="list py-4 " id="expert-list">

                        @foreach ($getfollowing['recordList'] as $getfollowing)
                            <div id="psychic-card-618160" class="psychic-card overflow-hidden  expertOnline  "
                                data-psychic-id="618160">
                                <ul class="list-unstyled d-flex mb-0">
                                    <li class="mr-3 position-relative psychic-presence status-618160"
                                        data-status="Available" data-chata="₹0" data-calla="₹ 0"><a
                                            href="{{ route('front.astrologerDetails', ['id' => $getfollowing['id']]) }}">
                                            <div class="psyich-img position-relative"><img
                                                    src="/{{ $getfollowing['profileImage'] }}" width="80" height="80"
                                                    style="border-radius:50%;" loading="lazy">
                                            </div>
                                        </a>
                                        <div id="psychic-618160-badge" class="status-badge specific-Clr-Online"
                                            title="Online">
                                        </div>
                                        <div class="status-badge-txt text-center specific-Clr-Online"><span
                                                id="psychic-618160-badge-txt"></span></div>
                                    </li>
                                    <li class="w-100 overflow-hidden"><a
                                            href="{{ route('front.astrologerDetails', ['id' => $getfollowing['id']]) }}"
                                            class="colorblack font-weight-semi font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block"
                                            data-toggle="tooltip">{{ $getfollowing['name'] }}</a><span
                                            class="font-12 d-block color-red">{{ $getfollowing['allSkill'] }}</span><span
                                            class="font-12 d-block exp-language">{{ $getfollowing['languageKnown'] }}</span><span
                                            class="font-12 d-block"> Exp :{{ $getfollowing['experienceInYears'] }}</span>
                                    </li>
                                </ul>
                                <div class="">
                                    <div class="d-block">
                                        <div class="d-flex justify-content-end"><a
                                                href="{{ route('front.astrologerDetails', ['id' => $getfollowing['id']]) }}"
                                                class="btn-block btn btn-primary" role="button"
                                               >View</a></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>



    </div>
@endsection
