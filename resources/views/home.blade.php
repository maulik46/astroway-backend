@extends('layout.app')

@section('content')
    <section id="home"class="blog-page home" style="padding-top: 120px !important;">
        <div class="animation-circle">
            <i></i>
            <i></i>
            <i></i>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8 homeleft">
                    <div class="d-flex-1">
                        <div class="slide-text">
                            <div>
                                <h1>The best way to connect with Astrologer is with
                                    <span class="main-title">Astroway!</span>
                                </h1>
                                <h4>Astrology is a predictive science with its own sets of methods, claims and findings that
                                    have
                                    forever inspired and allowed people with insights into different aspects of their life.
                                </h4>
                                <div class="slid-btn">
                                    <a [routerLink]="'/home'"><img src="/build/assets/images/app1.png" alt="app1"
                                            class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                            data-tilt-max="1.8"></a>
                                    <a [routerLink]="'/home'"><img src="/build/assets/images/app2.png" alt="app2"
                                            class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                            data-tilt-max="1.8"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="home-right">
                        <div class="mobile-slid">
                            <img src="/build/assets/images/userapp-6.png" alt="top1" class="img-fluid lefthomeimg">
                        </div>
                                          </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about section-->
    <section id="about" class="pb-0 about" style="padding-top: 100px !important;">
        <div class="about-chat">
            <div class="container ">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title">
                            <h2>About Astroway</h2>
                            <img src="/build/assets/images/title-line.png" alt="title-line" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-md-12 about-box">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/stay-connected.png" alt="stay-connected">
                                    <h3 class="sub-title"> Stay Connected</h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/get-notified.png" alt="get-notified">
                                    <h3 class="sub-title"> Get Notified </h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/stay-updated.png" alt="stay-updated">
                                    <h3 class="sub-title">Stay Updated</h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 text-center">
                                <div class="chat-box">
                                    <img src="/build/assets/images/mini-world.png" alt="mini-world">
                                    <h3 class="sub-title">Mini World</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chat-slide text-center">
                            <h3>Connect with Astrologer all over the world , with your small device</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid text-center">
            <img src="/build/assets/images/userapp-7.png"
                style="z-index: -1;
                position: relative;
                bottom: -80px;
                height: 630px;
                border-radius: 50px;
                border: 15px solid #383838;"
                alt="banner.png" class="img-fluid full-banner">
        </div>
    </section>
    <!-- Feature section -->
    <section class="theme-bg feature" id="feature"
        style="padding-top: 100px !important;padding-bottom: 100px !important;">
        <div class="container">
            <div class="animation-circle-inverse">
                <i></i>
                <i></i>
                <i></i>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="text-white">Astroway Feature</h2>
                        <img src="/build/assets/images/white-line.png" alt="white-line" class="img-fluid">
                    </div>
                </div>
               
                <div class="col-lg-4 col-sm-6">
                    <div class="future-box">
                        <div class="future-timeline">
                            <ul>
                                <li class="timeline">
                                    <h4 class="sub-title">Easy Installation</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Daily Horoscope</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Free Kundli</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Kundli Matching</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Live Astrologers</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 future-mobile text-center"><img src="/build/assets/images/userapp-4.png"
                        style="height: 600px;
                    border-radius: 50px;
                    border: 15px solid #383838;"alt="feature-mob"
                        class="img-fluid">
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="future-box">
                        <div class="future-timeline-right">
                            <ul class="rightcontent">
                                <li class="timeline-right">
                                    <h4>Call and Chat with Astrologer</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Astromall</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Astro Blogs</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Daily Panchang</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Connect with Counsellor</h4>
                                    <p>Lorem Ipsum has been the industry's</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="work" id="work" style="padding-top: 100px !important;padding-bottom: 100px !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="section-title">
                        <h2>How Astroway Work</h2>
                        <img src="/build/assets/images/title-line.png" alt="white-line" class="img-fluid">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem autem voluptatem obcaecati!
                            ipsum dolor sit Rem autem voluptatem obcaecati</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/1.png" alt="" />
                        <h3>download app</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias non nulla placeat, odio</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/2.png" alt="" />
                        <h3>connect Astrologer</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias non nulla placeat, odio</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/3.png" alt="" />
                        <h3>enjoy app</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias non nulla placeat, odio</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="screenshots" class="theme-bg screenshots" style="padding-top: 100px !important;">
    </section>

    <!-- news section-->
    <section class="team-bg" id="testimonial" style="padding-top: 100px !important;padding-bottom: 70px !important;">
        <div class="animation-circle-inverse">
            <i></i>
            <i></i>
            <i></i>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">John Shipment</h4>
                                    <h6>Excellent, Deep, Easy. This could be a Astroway app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>

                            </div>
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">John Shipmen</h4>
                                    <h6>Excellent, Deep, Easy. This could be a Astroway app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">John Shipmen</h4>
                                    <h6>Excellent, Deep, Easy. This could be a Astroway app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </section>
    <!-- end news section-->
    <!-- download section -->
    <section class="download-bg" style="padding-top: 40px !important;padding-bottom: 40px !important;">
        <div class="container">
            <div class="row download">
                <div class="col-xl-3 display-flex">
                    <div class="footer-logo">
                        <img src="/build/assets/images/astroTalk.png" style="width:100px;height:100px" alt="footer-logo"
                            class="img-fluid">
                    </div>
                </div>
                <div class="col-xl-5 downloadapp">
                    <div class="download-text">
                        <h3>Download the astroway app and enjoy it</h3>
                    </div>
                </div>
                <div class="col-xl-4 downloadapp">
                    <div class="download-img">
                        <ul>
                            <li><img style="width:120px" src="/build/assets/images/app1.png" alt="foot-app"
                                    class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                    data-tilt-max="1.8"></li>
                            <li><img style="width:120px" src="/build/assets/images/app2.png" alt="foot-app1"
                                    class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                    data-tilt-max="1.8"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact-section -->
    <section class="contact" id="contact" style="padding-top: 100px !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title">
                        <h2>Contact Info</h2>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consecte adipisicing elit sed do eiusmod tempor incididunt.</p>
                    <ul class="contact-box">
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Location</h3>
                                <p>Ring Road, New Delhi, India</p>
                            </div>
                        </li>
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Call US</h3>
                                <p>+91 9310797700</p>
                            </div>
                        </li>
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Email Us</h3>
                                <p>start@diploy.in</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end contact section -->
    <!-- end download section -->
    <!-- end Feature section -->
    <!-- end about section  -->
@endsection

<!-- end slider section -->
