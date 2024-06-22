@extends('frontend.layout.master')
@section('content')



    <div class="pt-1 pb-1 bg-red d-none d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">

                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Talk To Astrologer</span>
                        </span>
                    </span>

                </div>
            </div>
        </div>
    </div>


    {{--  Call Intake --}}

    <div class="modal fade rounded mt-2 mt-md-5 " id="callintake" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title font-weight-bold">
                        Intake Form
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body pt-0 pb-0">
                    <div class="bg-white body">
                        <div class="row ">

                            <div class="col-lg-12 col-12 ">
                                <div class="mb-3 ">

                                    <form class="px-3 font-14" method="post" id="callintakeForm">

                                        @if (authcheck())
                                            <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                            <input type="hidden" name="countryCode"
                                                value="{{ authcheck()['countryCode'] }}">
                                        @endif
                                        @if($getAstrologer['recordList'])
                                        <input type="hidden" name="astrologerId"
                                            value="{{ $getAstrologer['recordList'][0]['id'] }}">
                                        @endif
                                        <input type="hidden" name="call_type" id="call_type" value="">
                                        <input type="hidden" name="astrocharge" id="astrocharge" value="">
                                        <div class="row">
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="Name">Name<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="name" placeholder="Enter Name"
                                                        type="text"
                                                        value="{{ $getIntakeForm['recordList'][0]['name'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="PhoneNumber">Phone No<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="PhoneNumber" name="phoneNumber" placeholder="Enter Phone"
                                                        type="text"
                                                        value="{{ $getIntakeForm['recordList'][0]['phoneNumber'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group">
                                                    <label for="Gender">Gender <span class="color-red">*</span></label>
                                                    <select class="form-control" id="Gender" name="gender">
                                                        <option value="Male"
                                                            {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Male' ? 'selected' : '' }}>
                                                            Male</option>
                                                        <option value="Female"
                                                            {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Female' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="Other"
                                                            {{ isset($getIntakeForm['recordList'][0]['gender']) && $getIntakeForm['recordList'][0]['gender'] == 'Other' ? 'selected' : '' }}>
                                                            Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="BirthDate">Birthdate<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="BirthDate" name="birthDate" placeholder="Enter Birthdate"
                                                        type="date"
                                                        value="{{ isset($getIntakeForm['recordList'][0]['birthDate']) ? date('Y-m-d', strtotime($getIntakeForm['recordList'][0]['birthDate'])) : '' }}">
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="BirthTime">Birthtime<span class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="BirthTime" name="birthTime" placeholder="Enter Birthtime"
                                                        type="time"
                                                        value="{{ $getIntakeForm['recordList'][0]['birthTime'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="BirthPlace">Birthplace<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="BirthPlace" name="birthPlace" placeholder="Enter Birthplace"
                                                        type="text"
                                                        value="{{ $getIntakeForm['recordList'][0]['birthPlace'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="MaritalStatus">Marital Status<span
                                                            class="color-red">*</span></label>
                                                    <select class="form-control" id="MaritalStatus" name="maritalStatus">
                                                        <option value="Single"
                                                            {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Single' ? 'selected' : '' }}>
                                                            Single</option>
                                                        <option value="Married"
                                                            {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Married' ? 'selected' : '' }}>
                                                            Married</option>
                                                        <option value="Divorced"
                                                            {{ isset($getIntakeForm['recordList'][0]['maritalStatus']) && $getIntakeForm['recordList'][0]['maritalStatus'] == 'Divorced' ? 'selected' : '' }}>
                                                            Divorced</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="Occupation">Occupation<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Occupation" name="occupation" placeholder="Enter Occupation"
                                                        type="text"
                                                        value="{{ $getIntakeForm['recordList'][0]['occupation'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-2">
                                                <div class="form-group mb-0">
                                                    <label for="TopicOfConcern">Topic Of Concern<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="TopicOfConcern" name="topicOfConcern"
                                                        placeholder="Enter Topic Of Concern" type="text"
                                                        value="{{ $getIntakeForm['recordList'][0]['topicOfConcern'] ?? '' }}">
                                                </div>
                                            </div>

                                            @if (authcheck())
                                                @if ($isFreeAvailable == false)
                                                <input type="hidden" name="isFreeSession"
                                                value="0">
                                                    <div class="col-12 py-3">
                                                        <div class="form-group mb-0">
                                                            <label>Select Time You want to call<span
                                                                    class="color-red">*</span></label><br>
                                                            <div class="btn-group-toggle" data-toggle="buttons">
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration300" value="300"> 5 mins
                                                                </label>
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration600" value="600"> 10 mins
                                                                </label>
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration900" value="900"> 15 mins
                                                                </label>
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration1200" value="1200"> 20 mins
                                                                </label>
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration1500" value="1500"> 25 mins
                                                                </label>
                                                                <label class="btn btn-info btn-sm">
                                                                    <input type="radio" name="call_duration"
                                                                        id="call_duration1800" value="1800"> 30 mins
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <input type="hidden" name="call_duration"
                                                        value="{{ $getIntakeForm['default_time'] }}">
                                                        <input type="hidden" name="isFreeSession"
                                                        value="1">
                                                @endif
                                            @endif



                                        </div>

                                        <div class="col-12 col-md-12 py-3">
                                            <div class="row">

                                                <div class="col-12 pt-md-3 text-center mt-2">
                                                    <button class="font-weight-bold ml-0 w-100 btn btn-chat"
                                                        id="callloaderintakeBtn" type="button" style="display:none;"
                                                        disabled>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span> Loading...
                                                    </button>
                                                    <button type="submit"
                                                        class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                        id="callintakeBtn">Start Call</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- End  Call --}}



    <div class="py-md-3 expert-search-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12" id="experts" style="overflow:hidden;">
                    <div id="expert-search" class="my-3 my-md-0">
                        <!--For Serach Component-->
                        <div class="expert-search-form">
                            <div class="row mx-auto px-2 px-md-0 flex-md-nowrap align-items-center round">
                                <div
                                    class="col-12 col-md-3 col-sm-auto text-left d-flex justify-content-between align-items-center w-100 bg-white px-0">
                                    <h1 class="font-22 font-weight-bold">Talk to Astrologer</h1>
                                    <img src="#" alt="Filter Experts based on Status" width="18"
                                        height="18" class="img-fluid filterIcon float-right d-md-none"
                                        onClick="fnSearch()" />
                                </div>
                                <div class="col-ms-12 col-md-3 d-none d-md-block" id="searchExpert">
                                    <form action="{{ route('front.chatList') }}" method="GET">
                                        <div class="search-box">
                                            <input value="{{ isset($searchTerm) ? $searchTerm : '' }}"
                                                class="form-control rounded" name="s"
                                                placeholder="Search Astrologers" type="search" autocomplete="off">
                                            <button type="submit" class="btn btn-link search-btn" id="search-button">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2 "
                                    id="sortExpert">
                                    <select class="form-control font13 rounded" name="sortBy"
                                        onchange="onSortExpertList()" id="psychicOrderBy">
                                        <option value="1" {{ $sortBy == '1' ? 'selected' : '' }}>Online</option>
                                        <option value="experienceLowToHigh"
                                            {{ $sortBy == 'experienceLowToHigh' ? 'selected' : '' }}>Low Experience
                                        </option>
                                        <option value="experienceHighToLow"
                                            {{ $sortBy == 'experienceHighToLow' ? 'selected' : '' }}>High Experience
                                        </option>
                                        <option value="priceLowToHigh"
                                            {{ $sortBy == 'priceLowToHigh' ? 'selected' : '' }}>
                                            Lowest Price</option>
                                        <option value="priceHighToLow"
                                            {{ $sortBy == 'priceHighToLow' ? 'selected' : '' }}>
                                            Highest Price</option>
                                    </select>

                                </div>

                                <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2"
                                    id="filterExpertCategory">
                                    <select name="astrologerCategoryId" onchange="onFilterExpertCategoryList()"
                                        class="form-control font13 rounded" id="psychicCategories">
                                        <option value="0" {{ $astrologerCategoryId == '0' ? 'selected' : '' }}>All
                                        </option>
                                        @foreach ($getAstrologerCategory['recordList'] as $category)
                                            <option value="{{ $category['id'] }}"
                                                {{ $astrologerCategoryId == $category['id'] ? 'selected' : '' }}>
                                                {{ $category['name'] }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container">
        <div class="row">
            <div class="col-lg-12 expert-search-section-height">
                <div id="expert-list" class="py-4 ">

                    @foreach ($getAstrologer['recordList'] as $astrologer)
                        <div id="ATAAIOfferTile" class="psychic-card overflow-hidden expertOnline ask-guruji">
                            <ul class="list-unstyled d-flex mb-0">
                                <li class="mr-3 position-relative psychic-presence status-online" data-status="online"><a
                                        href="{{ route('front.astrologerDetails', ['id' => $astrologer['id']]) }}">
                                        <div class="psyich-img position-relative">
                                            @if ($astrologer['profileImage'])
                                                <img src="/{{ $astrologer['profileImage'] }}"width="80" height="80"
                                                    style="border-radius:50%;" loading="lazy">
                                            @else
                                                <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                                    width="80" height="80" style="border-radius:50%;">
                                            @endif
                                        </div>
                                    </a>
                                    @if($astrologer['callStatus']=='Busy')
                                    <div class="status-badge specific-Clr-Busy" title="Online"></div>
                                    @else
                                    <div class="status-badge specific-Clr-Online" title="Online"></div>
                                    <div class="status-badge-txt text-center specific-Clr-Online"><span id=""
                                            title="Online"
                                            class="status-badge-txt specific-Clr-Online tooltipex">{{ $astrologer['callStatus'] }}</span>
                                    </div>
                                    @endif
                                </li>
                                <li class="w-100 overflow-hidden"><a
                                        href="{{ route('front.astrologerDetails', ['id' => $astrologer['id']]) }}"
                                        class="colorblack font-weight-semi font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block"
                                        data-toggle="tooltip" title="">{{ $astrologer['name'] }}</a><span
                                        class="font-12 d-block color-red">{{ $astrologer['allSkill'] }}</span><span
                                        class="font-12 d-block exp-language">{{ $astrologer['languageKnown'] }}</span>
                                    <span class="font-12 d-block"> Exp :{{ $astrologer['experienceInYears'] }}
                                        Years</span>

                                    @if ($isFreeAvailable == true)
                                        <span class="font-12 font-weight-semi-bold d-flex"> <span
                                                class="exprt-price"><del>{{ $currency['value'] }}{{ $astrologer['charge'] }}</del>/Min</span>
                                            <span class="free-badge text-uppercase color-red ml-2">Free</span></span>
                                    @else
                                        <span class="font-12 font-weight-semi-bold d-flex"> <span
                                                class="exprt-price mr-2">
                                                <i
                                                    class="fa-solid fa-phone mr-1"></i>{{ $currency['value'] }}{{ $astrologer['charge'] }}</span><i
                                                class="fa-solid fa-video mt-1 mr-1"></i>{{ $currency['value'] }}{{ $astrologer['videoCallRate'] }}</span>
                                    @endif
                                </li>
                            </ul>
                            {{-- <div class="d-flex align-items-center justify-content-between"> --}}

                            <div class="d-flex align-items-end position-relative">
                                <div class="d-block">
                                    <div class="row">
                                        <div class="psy-review-section col-6"><a href="javascript:void(0);">
                                                <span class="colorblack font-12 m-0 p-0 d-block">
                                                    Category:
                                                    <span class="font-12 font-weight-bold m-0 p-0 color-brown">
                                                        <?php
                                                        $category = $astrologer['astrologerCategory'];
                                                        echo strlen($category) > 40 ? substr($category, 0, 40) . '...' : $category;
                                                        ?>
                                                    </span>
                                                </span>
                                                </p>
                                            </a>
                                        </div>
                                        <div class="col-3">
                                            @if($astrologer['callStatus']=='Busy')
                                            <a class="btn-block btn btn-call  align-items-center">Busy</a>
                                            @else
                                            <a class="btn-block btn btn-call btn-audio-call align-items-center " role="button"
                                                data-toggle="modal"
                                                @if (!authcheck()) data-target="#loginSignUp" @else data-target="#callintake" @endif
                                                id="audio-call-btn"><i class="fa-solid fa-phone"></i>
                                            </a>
                                            @endif
                                        </div>
                                        <div class="col-3">
                                            @if($astrologer['callStatus']=='Busy')
                                            <a class="btn-block btn btn-call  align-items-center">Busy</a>
                                            @else
                                            <a class="btn-block btn btn-call btn-video-call align-items-center" role="button"
                                                data-toggle="modal"
                                                @if (!authcheck()) data-target="#loginSignUp" @else data-target="#callintake" @endif
                                                id="video-call-btn"><i class="fa-solid fa-video"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function onFilterExpertCategoryList() {
            var astrologerCategoryId = $('#psychicCategories').val();
            var url = new URL(window.location.href);
            url.searchParams.set('astrologerCategoryId', astrologerCategoryId);
            window.location.href = url.toString();
        }

        function onSortExpertList() {
            var sortBy = $('#psychicOrderBy').val();
            var url = new URL(window.location.href);
            url.searchParams.set('sortBy', sortBy);
            window.location.href = url.toString();
        }
    </script>

    <script>

        $(document).ready(function() {


            $('.btn-audio-call').click(function() {

                $("#call_type").val(10);
                $("#astrocharge").val("{{ $getAstrologer['recordList'][0]['charge'] }}");

            });


            $('.btn-video-call').click(function() {

                $("#call_type").val(11);
                $("#astrocharge").val("{{ $getAstrologer['recordList'][0]['videoCallRate'] }}");

            });



            $('#callintakeBtn').click(function(e) {
                e.preventDefault();


                @if (authcheck())
                    var sessionAvailable = "{{ $isSessionavailable['isAddNewRequest'] }}";
                @endif

                @php
                    use Symfony\Component\HttpFoundation\Session\Session;
                    $session = new Session();
                    $token = $session->get('token');
                @endphp

                if (sessionAvailable == false) {
                    toastr.error('Your request is already there');
                    return false;
                }

                $('#callintakeBtn').hide();
                $('#callloaderintakeBtn').show();
                setTimeout(function() {
                    $('#callintakeBtn').show();
                    $('#callloaderintakeBtn').hide();
                }, 3000);

                astrocharge = $("#astrocharge").val();



                <?php
                $wallet_amount = '';
                if (authcheck()) {
                    $wallet_amount = authcheck()['totalWalletAmount'];
                }
                ?>

                var formData = $('#callintakeForm').serialize();

                // Parse form data as URL parameters
                var urlParams = new URLSearchParams(formData);
                var call_duration = parseInt(urlParams.get('call_duration'));

                var call_duration_minutes = Math.ceil(call_duration / 60);

                var total_charge = astrocharge * call_duration_minutes;

                @if($getAstrologer['recordList'])
                var isFreeAvailable = "{{ $getAstrologer['recordList'][0]['isFreeAvailable'] }}";

                var wallet_amount = "{{ $wallet_amount }}";
                @endif


                // Check if free chat is available and wallet has sufficient balance
                if (isFreeAvailable != true) {
                    if (total_charge <= wallet_amount) {
                        $.ajax({
                            url: "{{ route('api.addCallRequest', ['token' => $token]) }}",
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                // console.log('Call Request Sent');
                            },
                            error: function(xhr, status, error) {
                                toastr.error(xhr.responseText);
                            }
                        });
                        $.ajax({
                            url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                            type: 'POST',
                            data: formData,
                            success: function(response) {

                                setTimeout(function() {
                                    toastr.success(
                                        'Call Request Sent ! you will be notified if astrologer accept your request.'
                                        );
                                    window.location.href = "{{ route('front.home') }}";

                                }, 2000);
                            },
                            error: function(xhr, status, error) {
                                toastr.error(xhr.responseText);
                            }
                        });
                    } else {
                        toastr.error('Insufficient balance. Please recharge your wallet.');
                    }
                } else {

                    $.ajax({
                        url: "{{ route('api.addCallRequest', ['token' => $token]) }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // console.log('Call Request Sent');
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseText);
                        }
                    });

                    $.ajax({
                        url: "{{ route('api.intakeForm', ['token' => $token]) }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {

                            setTimeout(function() {
                                toastr.success(
                                    'Call Request Sent ! you will be notified if astrologer accept your request.'
                                    );
                                window.location.href = "{{ route('front.home') }}";

                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            toastr.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
@endsection
