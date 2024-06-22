@extends('frontend.layout.master')
@section('content')
    <style>
    .text-bold {
            font-weight: 800;
        }

        text-color {
            color: #0093c4;
        }



        .main-description .category {
            text-transform: uppercase;
            color: #0093c4;
        }

        .main-description .product-title {
            font-size: 2.5rem;
        }

        .old-price-discount {
            font-weight: 600;
        }

        .new-price {
            font-size: 2rem;
        }

        .details-title {
            text-transform: uppercase;
            font-weight: 600;
            font-size: 1.2rem;
            color: #757575;
        }



        .astroway-productdetails .item {
            box-shadow: 0 3px 6px #65a9fd24;
            border: 1px solid #65A9FD;
            margin-right: 10px;
            margin-left: 10px;
        }

        /* Small devices (landscape phones, less than 768px) */
        @media (max-width: 767.98px) {

            /* Make preview images responsive  */
            .previews img {
                width: 100%;
                height: auto;
            }

        }
</style>
    <div class="pt-1 pb-1 bg-red d-md-block astroway-breadcrumb">
        <div class="container">
            <div class="row afterLoginDisplay">
                <div class="col-md-12 d-flex align-items-center">
                    <span style="text-transform: capitalize; ">
                        <span class="text-white breadcrumbs">
                            <a href="{{route('front.home')}}" style="color:white;text-decoration:none">
                                <i class="fa fa-home font-18"></i>
                            </a>
                            <i class="fa fa-chevron-right"></i> <a href="{{route('front.getproducts')}}"
                                style="color:white;text-decoration:none">Product </a>
                            <i class="fa fa-chevron-right"></i> Product Details

                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-5">
                <div class="ds-head-body">
                    <div class="astroway-productdetails ">
                        <div class="container">

                            <div class="row pt-md-3">
                                <div class="col-sm-12">
                                    <div class="owl-carousel owl-theme">
                                            <div class="item mb-3" d-flex>
                                                <a href="" class="colorblack">
                                                    <img style="max-height: 300px" src="/{{ $getproductdetails['recordList'][0]['productImage'] }}" alt="" class="img-fluid" loading="lazy"
                                                      /></a>
                                                <div class="content p-3 bg-white">
                                                    <p class="text-center font-weight-semi font-weight-bold category mb-2"><a
                                                            href="" class="colorblack font-weight-bold">{{ $getproductdetails['recordList'][0]['name'] }}</a>
                                                    </p>


                                                    <div class="text-center pb-1">
                                                        <p><span class="color-red">{{$currency['value']}}{{ $getproductdetails['recordList'][0]['amount'] }}</span></p>
                                                        @if(!authcheck())
                                                        <a  role="button" data-toggle="modal"  data-target="#loginSignUp"  class="btn view-more color-red font-weight-normal mb-2">
                                                           Buy Now
                                                        </a>
                                                        @else
                                                        <a href="{{ route('front.checkout', ['id' => $getproductdetails['recordList'][0]['id']]) }}" role="button"   class="btn view-more color-red font-weight-normal mb-2">
                                                            Buy Now
                                                         </a>
                                                        @endif

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
            <div class="col-md-7">
                <div class="main-description px-2">
                    <div class="category text-bold">
                        Category: {{ $getproductdetails['recordList'][0]['productCategory'] }}
                    </div>
                    <div class="product-title text-bold my-3">
                        {{ $getproductdetails['recordList'][0]['name'] }}
                    </div>

                    <div class="price-area my-4">
                        <div class="category text-bold">
                            Price: {{$currency['value']}}{{ $getproductdetails['recordList'][0]['amount'] }}
                        </div>

                    </div>

                    <div class="product-details my-4">
                        <p class="details-title text-color mb-1">Product Details</p>
                        <p class="description">{{ $getproductdetails['recordList'][0]['features'] }} </p>
                    </div>

                    <div class="delivery my-4">
                        <p class="font-weight-bold mb-0"><span><i class="fa-solid fa-truck"></i></span> <b>Delivery done in 3 days from date of purchase</b> </p>
                        <p class="text-secondary">Order now to get this product delivery</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="astroway-customer-stories bg-pink-light py-4 py-md-5">
        <div class="container">
            <div class="row pb-2">
                <div class="col-sm-12">
                    <h2 class="text-md-center heading">Recent Products</h2>
                    <p class="text-md-center mb-1">See new products and how Astroway helped them
                        find their path to happiness!</p>
                </div>
            </div>
            <div class="row pt-md-3">
                <div class="col-sm-12">
                    <div class="owl-carousel owl-theme">

                        @foreach ($getAstromallProduct['recordList'] as $shop)
                            <div class="item mb-3">
                                <a href="{{route('front.getproductDetails',['id'=>$shop['id']])}}">
                                <img src="/{{ $shop['productImage'] }}" alt="{{ $shop['name'] }}" class="img-fluid"
                                    width="100" height="100" loading="lazy" style="height: 190px;width:100%">
                                </a>
                                <span
                                    class="d-block colorblack text-center font-weight-semi-bold pb-1">{{ $shop['name'] }}</span>


                                <div class="text-center pb-1">
                                    <p><span class="color-red">{{$currency['value']}}{{ $shop['amount'] }}</span></p>
                                    <a href="{{route('front.getproductDetails',['id'=>$shop['id']])}}" class="btn view-more color-red font-weight-normal mb-2">
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

    <script>
             $(document).ready(function() {


            $('.astroway-productdetails .owl-carousel').owlCarousel({
                loop: false,
                margin: 0,
                responsive: {
                    0: {
                        items: 1
                    },
                    340: {
                        items: 1,
                    },
                    768: {
                        items: 1,
                        nav: true
                    },
                    992: {
                        items: 1,
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
@endsection
