@extends('frontend.layout.master')

<link rel="stylesheet" href="{{ asset('public/frontend/agora/index.css') }}">

@section('content')
    @if (authcheck())
        @php
            $userId = authcheck()['id'];
            $astrologerId = request()->query('astrologerId');
            $callId = request()->query('callId');
        @endphp
    @endif

    <div class="pt-1 pb-1 bg-red d-none d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">

                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{ route('front.home') }}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <span class="breadcrumbtext">Call</span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>



    <input id="appid" type="hidden" placeholder="enter appid" value="{{ $agoraAppIdValue }}">
    <input id="token" type="hidden" placeholder="enter token" value="{{ $callrequest->token }}">
    <input id="channel" type="hidden" placeholder="enter channel name" value="{{ $callrequest->channelName }}">

    <section class="container">
        <div class=" row">
            <div class="col-md-2 col-sm-12 order-md-0 order-2 bottom-sm-0 bottom-buttons">
                <div class="navigation flex-sm-column h-100">
                    <span id="remainingTime" class="color-red">{{ $callrequest->call_duration }}</span>
                    <button class="video-action-button mic" onclick="toggleMic()" id="mic-icon">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="video-action-button camera" onclick="toggleVideo()" id="video-icon">
                        <i class="fas fa-video"></i>
                    </button>

                    <button class="video-action-button maximize">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                    <form id="endCallForm" class="d-inline-block">
                        <input type="hidden" name="callId" value="{{ $callId }}">
                        <input type="hidden" name="totalMin" id="totalMin" value="">
                        <button class="video-action-button endcall" id="leave">Leave</button>
                    </form>

                    <div class="video-call-actions ">
                    </div>
                </div>
            </div>
            <div class="app-main col-md-9 col-sm-12 order-sm-0">
                <div class="video-call-wrapper shadow">
                    <div class="video-participant">
                        <div class="participant-actions">
                            <button class="btn-mute"></button>
                            <button class="btn-camera"></button>
                        </div>

                        <a href="javascript:void(0);" class="name-tag" id="local-player-name">Mayank Sharma</a>
                        <div id="local-player" class="player"></div>
                        @if(authcheck()['profile'])
                        <img src="/{{ authcheck()['profile'] }}" alt="participant">
                        @else
                        <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/blank-profile.png') }}" alt="participant">
                        @endif
                    </div>
                    <div class="video-participant">
                        <div class="participant-actions">
                            <button class="btn-mute"></button>
                            <button class="btn-camera"></button>
                        </div>
                        <a href="javascript:void(0);" class="name-tag" id="remote-player-name">Guru Parmanand</a>
                        <div id="remote-playerlist"></div>
                        @if($getAstrologer['recordList'][0]['profileImage'])
                        <img src="/{{ $getAstrologer['recordList'][0]['profileImage'] }}" alt="participant">
                        @else
                        <img src="{{ asset('public/frontend/astrowaycdn/dashaspeaks/web/content/images/blank-profile.png') }}" alt="participant">
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            $('button.mode-switch').click(function() {
                $('body').toggleClass('dark');
            });

            $(".btn-close-right").click(function() {
                $(".right-side").removeClass("show");
                $(".expand-btn").addClass("show");
            });

            $(".expand-btn").click(function() {
                $(".right-side").addClass("show");
                $(this).removeClass("show");
            });
        });
    </script>
    <script src="{{ asset('public/frontend/agora/AgoraRTC_N-4.20.2.js') }}"></script>
    <script src="{{ asset('public/frontend/agora/index.js') }}"></script>

    <script>
        var updateTime = new Date("{{ $callrequest->updated_at }}").getTime();
        var callDuration = {{ $callrequest->call_duration }};

        var currentTime = new Date().getTime();
        var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
        var remainingTime = callDuration - elapsedTime;

        $("#local-player-name").text("{{ authcheck()['name'] }}");
        $("#remote-player-name").text("{{ $getAstrologer['recordList'][0]['name'] }}");

        function updateTimer() {
            var minutes = Math.floor(remainingTime / 60);
            var seconds = remainingTime % 60;

            var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

            document.getElementById('remainingTime').innerHTML = formattedTime;
        }

        // Update the timer every second
        updateTimer();
        var timerInterval = setInterval(function() {
            remainingTime--;
            if (remainingTime < 0) {
                remainingTime = 0;

            }
            updateTimer();

            if (remainingTime <= 0) {
                endCall();
                clearInterval(timerInterval);

            }

            var totalSeconds = callDuration - remainingTime;


            $("#leave").prop("disabled", true);
            if (totalSeconds >= 60) {
                $("#leave").prop("disabled", false);
            }

        }, 1000);




        // End call
        function endCall() {
            @php
                use Symfony\Component\HttpFoundation\Session\Session;
                $session = new Session();
                $token = $session->get('token');
            @endphp


            var totalSeconds = callDuration - remainingTime;
            $("#totalMin").val(totalSeconds);





            var formData = $('#endCallForm').serialize();

            $.ajax({
                url: "{{ route('api.endCall', ['token' => $token]) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Call Ended Successfully');

                    window.location.href = "{{ route('front.home') }}";
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseText);
                }
            });

        }
    </script>
@endsection
