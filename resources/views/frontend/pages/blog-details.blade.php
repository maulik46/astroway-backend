@extends('frontend.layout.master')
@section('content')
    <div class="pt-1 pb-1 bg-red d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="/" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="#"
                                style="color:white;text-decoration:none">Blogs </a>
                            <i class="fa fa-chevron-right"></i> Blog Details

                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="left-side">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class=" inner-content">
                                    <div class="post-header-section">
                                        <h1 class="page-title">{{ $getblogdetails['recordList']['title'] }}
                                        </h1>
                                        <p class="author">
                                            <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $getblogdetails['recordList']['postedOn'])->format('d M, Y') }}
                                                by <span style="color:#B0121B">{{ $getblogdetails['recordList']['author'] }}
                                                </span></span>
                                        </p>
                                    </div>
                                    <div class="post-featured-image-section">
                                        <div class="row bg align-items-center">
                                            <div class="col-md-8 px-0">
                                                <img style="width:100%"
                                                    src="/{{ $getblogdetails['recordList']['blogImage'] }}"
                                                    class="img-responsive post-img" alt="Nakshatra Porutham" width="750"
                                                    height="376">
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <p class="h2">Need Guidance On Your Problems?</p>
                                                <p class="pb-4" style="font-weight:600">Consult With The Best Online
                                                    Astrologers</p>
                                                <div class="position-relative astroway-call-action">
                                                    <div class="d-m-flex-in-mobile">


                                                        <a href="{{ route('front.talkList') }}" class="other-country">
                                                            <img
                                                                src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/call-btn.svg') }}">
                                                            <p class="ml-3 text-left mb-0">
                                                                <span
                                                                    class="text-nowrap mb-0 font-weight-semi-bold colorblack">Talk
                                                                    To <span class="color-red">Astrologer</span></span>
                                                            </p>
                                                        </a>
                                                        <a href="{{ route('front.chatList') }}"
                                                            class="other-country-show-img">
                                                            <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/astroway/images/chat-btn.svg') }}"
                                                                alt="chat-btn">
                                                            <p class="ml-3 text-left mb-0 w-75">
                                                                <span
                                                                    class="text-nowrap mb-0 font-weight-semi-bold colorblack">Chat
                                                                    With <span class="color-red">Astrologer</span></span>
                                                                <span
                                                                    class=" font-16 colorblack text-left d-none other-country-show">Connect
                                                                    with the best Indian Astrologers via Live chat for all
                                                                    your life’s problems</span>
                                                            </p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="mt-5">{!! $getblogdetails['recordList']['description'] !!}</p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
