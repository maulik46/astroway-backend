@extends('frontend.layout.master')
@section('content')

    <style>
        .astroway-customer-stories .item img {
            width: 100% !important;
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
                            <i class="fa fa-chevron-right"></i> <a href="#"
                                style="color:white;text-decoration:none">Product </a>


                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


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
                                    <h1 class="font-22 font-weight-bold">Search Products</h1>
                                    <img src="#" alt="Filter Experts based on Status" width="18" height="18"
                                        class="img-fluid filterIcon float-right d-md-none" onclick="fnSearch()">
                                </div>
                                <div class="col-ms-12 col-md-3 d-none d-md-block" id="searchProduct">
                                    <form action="{{ route('front.getproducts') }}" method="GET">
                                        <div class="search-box">
                                            <input value="{{ isset($searchTerm) ? $searchTerm : '' }}" class="form-control rounded" name="s" placeholder="Search Products" type="search" autocomplete="off">
                                            <button type="submit" class="btn btn-link search-btn" id="search-button">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>


                                <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2 "
                                    id="filterproductCategory">

                                    <select name="productCategoryId" onchange="onFilterProductCategoryList()"
                                    class="form-control font13 rounded" id="psychicCategories">
                                    <option value="0" {{ $productCategoryId == '0' ? 'selected' : '' }}>All
                                    </option>
                                    @foreach ($getproductCategory['recordList'] as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ $productCategoryId == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                                </div>



                                {{-- <div class="col-ms-12 col-md-3 d-none d-md-flex nowrap align-items-center pl-md-0 pt-2 pb-2" id="filterExpertCategory">
                                    <select class="form-control font13 rounded" id="psychicCategories" onchange="onFilterExpertCategoryList()">
                                        <option value="0">All</option>
                                    </select>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="astroway-customer-stories  py-4 py-md-5">
        <div class="container">
            <div class="row pb-2">
                <div class="col-sm-12">
                    <h2 class="text-md-center heading">Products</h2>
                    <p class="text-md-center mb-1">See new products and how Astroway helped them
                        find their path to happiness!</p>
                </div>
            </div>
            <div class="row pt-md-3">

                {{-- <div class="owl-carousel owl-theme"> --}}

                @foreach ($getAstromallProduct['recordList'] as $shop)
                    <div class="col-sm-12 col-md-4">
                        <div class="item mb-3">
                            <a href="{{ route('front.getproductDetails', ['id' => $shop['id']]) }}">
                                <img src="/{{ $shop['productImage'] }}" alt="{{ $shop['name'] }}" class="img-fluid"
                                    width="100" height="100" loading="lazy" style="height: 190px;width:100%">
                            </a>
                            <span
                                class="d-block colorblack text-center font-weight-semi-bold pb-1">{{ $shop['name'] }}</span>


                            <div class="text-center pb-1">
                                <p><span class="color-red">{{$currency['value']}}{{ $shop['amount'] }}</span></p>
                                <a href="{{ route('front.getproductDetails', ['id' => $shop['id']]) }}"
                                    class="btn view-more color-red font-weight-normal mb-2">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Display pagination links -->

            </div>
            @if ($totalRecords > 0)
                <div class="text-center mt-5">
                    <span>Page {{ $currentPage }} of {{ ceil($totalRecords / $perPage) }}</span>
                    @if ($currentPage > 1)
                        <a class="btn btn-chat px-5 my-2"
                            href="{{ route('front.getproducts', ['page' => $currentPage - 1]) }}"><i
                                class="fa fa-angle-double-left" aria-hidden="true"></i>Previous</a>
                    @endif
                    @if ($currentPage < ceil($totalRecords / $perPage))
                        <a class="btn btn-chat px-5 my-2"
                            href="{{ route('front.getproducts', ['page' => $currentPage + 1]) }}">Next<i
                                class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    @endif
                </div>
            @endif
        </div>
    </div>


    <script>

        function onFilterProductCategoryList() {
            var productCategoryId = $('#psychicCategories').val();
            var url = new URL(window.location.href);
            url.searchParams.set('productCategoryId', productCategoryId);
            window.location.href = url.toString();
        }

        $(document).ready(function() {



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
