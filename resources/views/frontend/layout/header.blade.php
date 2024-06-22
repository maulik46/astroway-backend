@php

    use Symfony\Component\HttpFoundation\Session\Session;
    use Illuminate\Support\Facades\Artisan;

    if (authcheck()) {
        $session = new Session();
        $token = $session->get('token');
        Artisan::call('cache:clear');
        $getProfile = Http::withoutVerifying()
            ->post(env('APP_URL') . '/api/getProfile', [
                'token' => $token,
            ])
            ->json();

        $getUserNotification = Http::withoutVerifying()
            ->post(env('APP_URL') . '/api/getUserNotification', [
                'token' => $token,
            ])
            ->json();

        $chatrequest = DB::table('chatrequest')
            ->where('userId', authcheck()['id'])
            ->get();
    }
        $logo = DB::table('systemflag')
            ->where('name', 'AdminLogo')
            ->select('value')
            ->first();
        $appName = DB::table('systemflag')
            ->where('name', 'AppName')
            ->select('value')
            ->first();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        $appId = $getsystemflag->where('name', 'firebaseappId')->first();
        $measurementId = $getsystemflag->where('name', 'firebasemeasurementId')->first();
        $messagingSenderId = $getsystemflag->where('name', 'firebasemessagingSenderId')->first();
        $storageBucket = $getsystemflag->where('name', 'firebasestorageBucket')->first();
        $projectId = $getsystemflag->where('name', 'firebaseprojectId')->first();
        $authDomain = $getsystemflag->where('name', 'firebaseauthDomain')->first();
        $databaseURL = $getsystemflag->where('name', 'firebasedatabaseURL')->first();
        $apiKey = $getsystemflag->where('name', 'firebaseapiKey')->first();
        $otplessAppId = $getsystemflag->where('name', 'otplessAppId')->first();




@endphp



<style>
    .scrollable-menu {
        max-height: 450px;
        /* Adjust this value as needed */
        overflow-y: auto;

    }

    .dropdown-menu.show {
        display: block;
    }

    .btn-chataccept {
        border-radius: 30px;
        border: 1px solid #5bbe2a;
        background-color: #5bbe2a !important;
        color: white !important;
    }

    .btn-chatreject {
        border-radius: 30px;
        border: 1px solid #ee4e5e;
        background-color: #ffffff !important;
        color: #ee4e5e !important;
    }

    .btn.clear-notification {
        font-size: 15px !important;
        padding: 8px 30px !important;
    }

    .btn.clear-notification:hover,
    .btn.clear-notification:focus,
    .btn.clear-notification:active {
        color: #fff !important;
        background: #ee4e5e !important;
    }

    @media screen and (max-width: 520px) {
    #notificationList{
        width: 370px !important;
    }
}

</style>
<div class="header">
    <nav class="navbar navbar-light fixed-top">
        <div class="container">
            <div class="d-flex align-items-center w-100 justify-content-between">
                <div class="d-flex align-items-center w-50">
                    <button class="navbar-toggler d-inline d-lg-none mr-2" type="button" data-toggle="collapse"
                        data-target="#main_nav" aria-controls="navbarCollapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"
                            style="background-image:url(/{{ $logo->value }});"></span>
                    </button>


                    <a class="navbar-brand" href="{{ route('front.home') }}">
                        <div class="d-flex align-items-center">
                            <img src="/{{ $logo->value }}" alt="{{ $appName->value }}" class="img-fluid"
                                width="53" height="53">
                            <div class="astroway-logo-ntext ml-2">
                                <span class="astroway-logo-text">{{ $appName->value }}</span>
                                <span class="astroway-logo-subtext">Consult Online Astrologers Anytime</span>
                            </div>
                        </div>
                    </a>

                    <div class="collapse navbar-collapse position-absolute" id="main_nav">
                        <div class="container">
                            <div class="w-100">
                                <div class="row my-4">
                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush dropdown ">
                                            <a href="javascript:void(0)"
                                                class="mb-0 border-bottom text-decoration-none border-pink text-uppercase font-weight-semi-bold">
                                                Astrology Online
                                            </a>
                                            <a class="dropdown-caret dropdownmob dropdown-toggle position-absolute px-1"
                                                style="width:20px;right:0!important" role="button" id="navbarDropdown"
                                                data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
                                            <div class="dropdown-menu " aria-labelledby="navbarDropdown">
                                                <a href="{{ route('front.talkList') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Talk To
                                                    Astrologer</a>
                                                <a href="{{ route('front.chatList') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Chat With
                                                    Astrologer</a>


                                                @php
                                                    $getAstrologerCategory = Http::withoutVerifying()
                                                        ->post(env('APP_URL') . '/api/getAstrologerCategory')
                                                        ->json();

                                                @endphp

                                                @foreach ($getAstrologerCategory['recordList'] as $category)
                                                    <a class="dropdown-item list-unstyled py-1 font-14"
                                                        href="{{ route('front.chatList', ['astrologerCategoryId' => $category['id']]) }}">{{ $category['name'] }}</a>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush dropdown mb-3">
                                            <a href="javascript:void(0)"
                                                class="mb-0 border-bottom text-decoration-none border-pink text-uppercase font-weight-semi-bold">
                                                Astrology
                                            </a>
                                            <a class="dropdown-caret dropdownmob dropdown-toggle position-absolute px-1"
                                                style="width:20px;right:0!important" id="navbarDropdown2" role="button"
                                                data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown2">
                                                <a href="{{ route('front.kundaliMatch') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Kundali
                                                    Matching</a>
                                                <a href="{{ route('front.getkundali') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Free Janam
                                                    Kundali</a>
                                            </div>
                                        </div>
                                        <div class="list-group list-group-flush dropdown mb-3">
                                            <a href="javascript:void(0)"
                                                class="mb-0 border-bottom text-decoration-none border-pink text-uppercase font-weight-semi-bold">
                                                Horoscope
                                            </a>
                                            <a class="dropdown-caret dropdownmob dropdown-toggle position-absolute px-1"
                                                style="width:20px;right:0!important" id="navbarDropdown2" role="button"
                                                data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown2">
                                                <a href="{{ route('front.horoScope') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Daily
                                                    Horoscope</a>
                                                <a href="{{ route('front.horoScope') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Weekly
                                                    Horoscope</a>
                                                <a href="{{ route('front.horoScope') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Yearly
                                                    Horoscope</a>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush dropdown mb-3">
                                            <a href="javascript:void(0)"
                                                class="mb-0 border-bottom text-decoration-none border-pink text-uppercase font-weight-semi-bold">
                                                Panchang
                                            </a>
                                            <a class="dropdown-caret dropdownmob dropdown-toggle position-absolute px-1"
                                                style="width:20px;right:0!important" id="navbarDropdown4" role="button"
                                                data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown4">
                                                <a href="{{ route('front.getPanchang') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Today&#39;s
                                                    Panchang</a>
                                            </div>
                                        </div>

                                        <div class="list-group list-group-flush dropdown mb-3">
                                            <a href="javascript:void(0)"
                                                class="mb-0 border-bottom text-decoration-none border-pink text-uppercase font-weight-semi-bold">
                                                Report
                                            </a>
                                            <a class="dropdown-caret dropdownmob dropdown-toggle position-absolute px-1"
                                                style="width:20px;right:0!important" id="navbarDropdown4" role="button"
                                                data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown4">
                                                <a href="{{ route('front.reportList') }}"
                                                    class="dropdown-item list-unstyled py-1 font-14">Get Report</a>
                                            </div>
                                        </div>

                                    </div>


                                    <ul class="navbar-nav pt-3 px-3 w-100 d-lg-none">
                                        <li class="nav-item">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('front.talkList') }}"
                                                    class="btn btn-block btn-chat other-country m-0 w-100 nav-link"><img
                                                        src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/call.svg') }}">
                                                    Talk To Astrologer</a>

                                            </div>

                                        </li>
                                        <li class="nav-item pt-3">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('front.chatList') }}"
                                                    class="btn btn-block btn-chat m-0 w-100 nav-link"><img
                                                        src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/chat.svg')}}">
                                                    Chat With Astrologer</a>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center header-call-chat-btn w-50 justify-content-end">
                    <div class="btn-groups d-none d-lg-flex mr-md-3">
                        <a href="{{ route('front.talkList') }}" id="callPg"
                            class="btn btn-chat other-country"><img
                                src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/call.svg') }}"
                                alt="call"> Talk To Astrologer</a>
                        <a href="{{ route('front.chatList') }}" id="chatPg" class="btn btn-chat"><img
                                src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/chat.svg') }}"
                                alt="chat"> Chat With Astrologer</a>
                    </div>

                    <ul class="list-inline mb-0 d-flex align-items-center userprofileicon">


                        @if (authcheck())

                            <li class="list-inline-item">
                                <div class="dropdown ">
                                    <a class="btn dropdown-toggle p-0" role="button" id="dropdownMenuLink"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">

                                        @if (authcheck()['profile'])
                                            <img src="/{{ authcheck()['profile'] }}" alt="User"
                                                class="img-fluid">
                                        @else
                                            <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img.png') }}"
                                                alt="" class="psychic-img img-fluid">
                                        @endif


                                    </a>
                                    <div class="dropdown-menu user-options fadeInUp5px dropdown-menu-right dropdown-menu-lg-left"
                                        aria-labelledby="dropdownMenuLink">
                                        <ul>
                                            <li class="namedisplay d-block text-center">


                                                @if (authcheck()['profile'])
                                                    <img src="/{{ authcheck()['profile'] }}" alt="User"
                                                        class="img-fluid">
                                                @else
                                                    <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/user-img-new.png') }}"
                                                        alt="User" class="img-fluid">
                                                @endif

                                                <div>
                                                    <h2 class="pt-3">{{ str_repeat('X', 6) . substr(authcheck()['contactNo'], -4) }}

                                                    </h2>
                                                    <h3></h3>
                                                </div>
                                            </li>
                                            <li class="d-lg-block">
                                                <div>
                                                    <a class="dropdown-item "
                                                        href="{{ route('front.getMyAccount') }}">
                                                        <span class="mr-2 accSet accSettingWeb">
                                                            <i class="fa-solid fa-user"></i>

                                                        </span>
                                                        <span>My Account</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center pr-2"
                                                        href="{{ route('front.getMyWallet') }}">
                                                        <span>
                                                            <span class="mr-2">
                                                                <i class="fa-solid fa-wallet"></i>
                                                            </span>

                                                            <span>My Wallet</span>
                                                        </span>
                                                        <span class="gWalletbalance color-red bg-pink"
                                                            style="border-radius:20px; padding:2px 10px; font-size:12px;">{{$currency['value']}}{{ $getProfile['data']['totalWalletAmount'] }}</span>

                                                    </a>
                                                </div>
                                            </li>



                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" href="{{route('front.getMyChat')}}">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-comment-dots"></i>
                                                        </span>
                                                        <span>My Chats</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" href="{{route('front.getMyCall')}}">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-phone"></i>
                                                        </span>
                                                        <span>My Calls</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" href="{{ route('front.myOrders') }}">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-cart-shopping"></i>
                                                        </span>
                                                        <span>My Orders</span>
                                                    </a>
                                                </div>
                                            </li>
                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" href="{{ route('front.getMyReport') }}">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-file"></i>
                                                        </span>
                                                        <span>My Reports</span>
                                                    </a>
                                                </div>
                                            </li>
                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" href="{{ route('front.getMyFollowing') }}">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-circle-user"></i>
                                                        </span>
                                                        <span>My Following</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item"  href="{{route('front.deleteAccount')}}"
                                                        >
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </span>
                                                        <span>Delete Account</span>
                                                    </a>
                                                </div>
                                            </li>

                                            <li class="d-block">
                                                <div>
                                                    <a class="dropdown-item" id="logout" href="javascript:void()"
                                                        onclick="logout()">
                                                        <span class="mr-2">
                                                            <i class="fa-solid fa-right-from-bracket"></i>
                                                        </span>
                                                        <span>Sign Out</span>
                                                    </a>
                                                </div>
                                            </li>



                                        </ul>
                                    </div>
                                </div>
                            </li>

                            {{-- For Notification --}}
                            <li class="list-inline-item ml-4">
                                <div class="dropdown">
                                    <a class="btn  p-0" style="width: 30px" role="button" id="dropdownMenuLink"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <i class="fa-solid fa-bell"></i>
                                        <span class="badge badge-danger badge-counter" id="notificationCount">0</span>
                                    </a>
                                    <div class="dropdown-menu user-options fadeInUp5px dropdown-menu-right dropdown-menu-lg-left scrollable-menu"
                                        aria-labelledby="dropdownMenuLink" id="notificationDropdown">
                                        <ul id="notificationList">
                                            @foreach ($getUserNotification['recordList'] as $notification)
                                                <li
                                                    class="d-lg-block @if ($notification['chatStatus'] == 'Accepted' || $notification['callStatus'] == 'Accepted') bg-pink @endif">
                                                    <div>
                                                        <a class="dropdown-item"
                                                            @if ($notification['chatStatus'] == 'Accepted') onclick="setIds('{{ $notification['chatId'] }}', '{{ $notification['astrologerId'] }}')" data-toggle="modal" data-target="#chatinfomodal"
                                                            @elseif($notification['callStatus'] == 'Accepted') onclick="setCallIds('{{ $notification['callId'] }}', '{{ $notification['astrologerId'] }}')" data-toggle="modal" data-target="#callinfomodal" @endif>
                                                            <span class="mr-2 accSet accSettingWeb">
                                                                <i class="fa-solid fa-bell"></i>
                                                            </span>
                                                            <span>{{ $notification['title'] }}</span>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if (count($getUserNotification['recordList']) > 0)
                                            <a class="dropdown-item text-center btn clear-notification"
                                                id="clearNotifications">Clear Notifications</a>
                                        @else
                                            <ul id="notificationList">
                                                <li class="d-lg-block">
                                                    <span class="dropdown-item text-center ">No Notification Yet</span>
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </li>



                            {{-- End --}}
                        @else
                            <li class="list-inline-item usericon"><a style="cursor:pointer;"
                                    class="colorblack font-weight-semi loginSignUp d-flex align-items-center"
                                    data-toggle="modal" data-target="#loginSignUp">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31.001"
                                        viewBox="0 0 31 31.001">
                                        <path id="Path_22197" data-name="Path 22197"
                                            d="M-1542.569-660.735a15.4,15.4,0,0,0-10.96-4.54,15.4,15.4,0,0,0-10.96,4.54,15.4,15.4,0,0,0-4.54,10.96,15.4,15.4,0,0,0,4.54,10.96,15.4,15.4,0,0,0,10.96,4.54,15.4,15.4,0,0,0,10.96-4.54,15.4,15.4,0,0,0,4.54-10.96A15.4,15.4,0,0,0-1542.569-660.735Zm-18.529,22.2a1.407,1.407,0,0,1,.058-.37,7.822,7.822,0,0,1,8.253-6.134,7.787,7.787,0,0,1,7.043,6.061.694.694,0,0,1,.021.279,13.477,13.477,0,0,1-7.806,2.48A13.475,13.475,0,0,1-1561.1-638.538Zm2.805-13.283a4.915,4.915,0,0,1,4.932-4.9,4.914,4.914,0,0,1,4.89,4.938,4.9,4.9,0,0,1-4.932,4.89A4.9,4.9,0,0,1-1558.293-651.821Zm14.155,11.807c-.047-.121-.1-.26-.148-.425a9.72,9.72,0,0,0-5.4-5.721,6.706,6.706,0,0,0,3.021-5.721,6.469,6.469,0,0,0-2-4.705,6.7,6.7,0,0,0-9.414-.021,6.5,6.5,0,0,0-1.994,5.449,6.659,6.659,0,0,0,3,4.994,10.164,10.164,0,0,0-5.644,6.344,13.518,13.518,0,0,1-4.367-9.956,13.568,13.568,0,0,1,13.552-13.552,13.568,13.568,0,0,1,13.552,13.552A13.514,13.514,0,0,1-1544.138-640.014Z"
                                            transform="translate(1569.03 665.275)" />
                                    </svg>
                                    <span class="d-none d-lg-inline ml-2 text-nowrap">Sign In </span></a>
                            </li>
                        @endif

                        {{-- End Profile Section --}}

                    </ul>
                    <button class="navbar-toggler collapsed d-lg-inline position-relative border-0 d-none ml-2"
                        type="button" data-toggle="collapse" data-target="#main_nav" aria-controls="navbarCollapse"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span style="color: #000">
                            <i class="fa-solid fa-list"></i>
                        </span>
                    </button>

                </div>
            </div>
        </div>
    </nav>
</div>

{{-- Chat Accept Reject Model --}}
<div id="chatinfomodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm h-100 d-flex align-items-center">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title font-weight-bold">
                    Accept Chat Request
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <form id="chatForm">
                    <input type="hidden" name="chatId" id="chatIdInput" value="">
                    <input type="hidden" id="astrologerIdInput" name="astrologerId" value="">
                    <div class="text-center">
                        <a class="btn btn-chataccept  active d-inline-block m-2" id="startchat" role="button"
                            data-toggle="modal">
                            Start Chat
                        </a>
                        <a class="btn btn-chatreject active d-inline-block m-2" id="rejectchat" role="button"
                            data-toggle="modal">
                            Reject Chat
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

{{-- Call Accept Reject Modal --}}

<div id="callinfomodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm h-100 d-flex align-items-center">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title font-weight-bold">
                    Accept Call Request
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <form id="callForm">
                    <input type="hidden" name="callId" id="callIdInput" value="">
                    <input type="hidden" id="astrologerIdInput" name="astrologerId" value="">
                    <input type="hidden" id="calltypeInput" name="call_type" value="">
                    <div class="text-center">
                        <a class="btn btn-chataccept  active d-inline-block m-2" id="startcall" role="button"
                            data-toggle="modal">
                            Start Call
                        </a>
                        <a class="btn btn-chatreject active d-inline-block m-2" id="rejectcall" role="button"
                            data-toggle="modal">
                            Reject Call
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

{{-- End Model --}}

<div class="modal fade rounded mt-2 mt-md-5 login-offer" id="loginSignUp" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt-0 pb-0">
                <div class="login-offer-bg d-none">
                    <p class="text-white font-22 text-center font-weight-bold p-0 m-0 offertxt1">Get
                        Consultation from Experts</p>
                    <p class="text-center p-0 m-0 offertxt2 ">First Chat Free</p>

                </div>
                <button type="button" class="close login-sig-close-btn loginCloseBut" data-dismiss="modal"
                    aria-hidden="true">
                    ×
                </button>
                <div class="bg-white body">
                    <div class="row ">
                        <div class="col-md-12 px-4 py-5">

                            <ul class="nav nav-tabs">

                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="LoginRegisterWithOTP">
                                    <div id="otpless-login-page"></div>

                                    <form method="post" action="{{route('front.verifyOTL')}}" id="OtpLesslogin">
                                        @csrf
                                        <input type="hidden" name="otl_token" id="otl_token">
                                        <input id="contactNo" name="contactNo" type="hidden" value="" />
                                        <input id="countryCode" name="countryCode" type="hidden" value="" />
                                    </form>


                                </div>

                                <div class="tab-pane " id="Login">


                                    <div class="col-md-12 text-center">
                                        <h3><b>Welcome Back!</b></h3>
                                    </div>
                                    <div>
                                        <p class="colorblack mt-md-2 mt-2 text-center pb-md-0 pb-2 mb-0"> Sign
                                            in to your account</p>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script id="otpless-sdk" type="text/javascript" src="https://otpless.com/v2/auth.js" data-appid="{{$otplessAppId['value']}}"></script>
<script>
    function otpless(otplessUser) {
            console.log(JSON.stringify(otplessUser));
            $("#otl_token").val(otplessUser.token);
            $("#OtpLesslogin").submit();
        }
</script>


<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-storage.js"></script>



<script>
    var firebaseConfig = {
        apiKey: "{{$apiKey['value']}}",
        databaseURL: "{{$databaseURL['value']}}",
        authDomain: "{{$authDomain['value']}}",
        projectId: "{{$projectId['value']}}",
        storageBucket: "{{$storageBucket['value']}}",
        messagingSenderId: "{{$messagingSenderId['value']}}",
        appId: "{{$appId['value']}}",
        measurementId: "{{$measurementId['value']}}"
    };

    firebase.initializeApp(firebaseConfig);

</script>



<script>
    function logout() {
        $.ajax({
            url: "{{ route('front.logout') }}", // URL of your logout route
            type: 'GET',
            success: function(response) {

                toastr.success('Logged out successfully');

                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function(xhr, status, error) {
                toastr.error(error);
            }
        });
    }



</script>

@if (authcheck())
    <script>
       // Store the IDs of notifications that have already triggered a modal
let processedNotifications = new Set();

function setIds(chatId, astrologerId) {
    document.getElementById('chatIdInput').value = chatId;
    document.getElementById('astrologerIdInput').value = astrologerId;
}

function setCallIds(callId, astrologerId,call_type) {
    document.getElementById('callIdInput').value = callId;
    document.getElementById('astrologerIdInput').value = astrologerId;
    document.getElementById('calltypeInput').value = call_type;
}

setInterval(function() {
    fetch("{{ route('api.getUserNotification', ['token' => $token]) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            const notificationCount = document.getElementById('notificationCount');

            notificationList.innerHTML = '';
            notificationCount.innerText = data.recordList.length; // Update notification count

            data.recordList.forEach(notification => {
                const isChatAccepted = notification.chatStatus === 'Accepted';
                const isCallAccepted = notification.callStatus === 'Accepted';

                notificationList.innerHTML += `
                    <li class="d-lg-block ${isChatAccepted || isCallAccepted ? 'bg-pink' : ''}">
                        <div>
                            <a class="dropdown-item" ${isChatAccepted ? `onclick="setIds('${notification.chatId}', '${notification.astrologerId}')" data-toggle="modal" data-target="#chatinfomodal"` : (isCallAccepted ? `onclick="setCallIds('${notification.callId}', '${notification.astrologerId}','${notification.call_type}')" data-toggle="modal" data-target="#callinfomodal"` : '')}>
                                <span class="mr-2 accSet accSettingWeb">
                                    <i class="fa-solid fa-bell"></i>
                                </span>
                                <span>${notification.title}</span>
                            </a>
                        </div>
                    </li>
                `;

                // Check if the notification has already been processed
                if ((isChatAccepted || isCallAccepted) && !processedNotifications.has(notification.id)) {
                    // Add the notification ID to the set of processed notifications
                    processedNotifications.add(notification.id);

                    // Open the respective modal if the condition matches
                    if (isChatAccepted) {
                        setIds(notification.chatId, notification.astrologerId);
                        $('#chatinfomodal').modal('show');
                    } else if (isCallAccepted) {
                        setCallIds(notification.callId, notification.astrologerId,notification.call_type);

                        $('#callinfomodal').modal('show');
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching notifications:', error));
}, 4000);




        // Start Chat

        $('#startchat').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');

            @endphp

            var formData = $('#chatForm').serialize();
            var astrologerId = $("#astrologerIdInput").val();
            var chatId = $("#chatIdInput").val();


            $.ajax({
                url: "{{ route('api.acceptChatRequestFromCustomer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Chat Started Successfully..Wait');
                    window.location.href = "{{ route('front.chat') }}" + "?astrologerId=" +
                        astrologerId + "&chatId=" + chatId;
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });

        // Reject Chat

        $('#rejectchat').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');
            @endphp

            var formData = $('#chatForm').serialize();
            var astrologerId = $("#astrologerIdInput").val();

            $.ajax({
                url: "{{ route('api.rejectChatRequestFromCustomer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Chat Rejected Successfully.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });


        // Start Call

        $('#startcall').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');

            @endphp

            var formData = $('#callForm').serialize();
            var astrologerId = $("#astrologerIdInput").val();
            var callId = $("#callIdInput").val();
            var call_type = $("#calltypeInput").val();


            $.ajax({
                url: "{{ route('api.acceptCallRequestFromCustomer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Call Started Successfully..Wait');
                    window.location.href = "{{ route('front.call') }}" + "?astrologerId=" +
                        astrologerId + "&callId=" + callId + "&call_type=" + call_type;
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });


        // Reject Call

        $('#rejectcall').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');
            @endphp

            var formData = $('#callForm').serialize();
            var astrologerId = $("#astrologerIdInput").val();

            $.ajax({
                url: "{{ route('api.rejectCallRequestFromCustomer', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Call Rejected Successfully.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });




        // Notification Clear


        $('#clearNotifications').click(function(e) {
            e.preventDefault();

            @php
                $token = $session->get('token');
            @endphp


            $.ajax({
                url: "{{ route('api.deleteAllUserNotification', ['token' => $token]) }}",
                type: 'POST',
                success: function(response) {
                    toastr.success('Notification Cleared Successfully');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });
        });
    </script>
@endif
