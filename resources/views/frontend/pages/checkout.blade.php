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
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.checkout') }}"
                                style="color:white;text-decoration:none">Checkout </a>

                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade rounded mt-2 mt-md-5 login-offer" id="checkout" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title font-weight-bold">
                        SHIPPING DETAILS
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body pt-0 pb-0">

                    <div class="bg-white body">
                        <div class="row ">

                            <div class="col-lg-12 col-12 ">
                                <div class="mb-3 ">

                                    <form class="px-3 font-14" method="post" id="orderAddress" autocomplete="off">

                                        <input type="hidden" name="userId" value="{{ authcheck()['id'] }}">
                                        <div class="row">
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Name&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="name" placeholder="Enter Name"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Phone No&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="phoneNumber" placeholder="Enter Phone"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Flat No&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="flatNo" placeholder="Enter Flat"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Locality&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="locality" placeholder="Enter Locality"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Landmark&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="landmark" placeholder="Enter Landmark"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">City&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="city" placeholder="Enter City"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">State&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="state" placeholder="Enter State"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Country&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="country" placeholder="Enter Country"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 py-3">
                                                <div class="form-group mb-0">
                                                    <span
                                                        class="field-validation-valid control-label commonerror float-right color-red"
                                                        data-valmsg-for="Name" data-valmsg-replace="false"> </span>
                                                    <label for="BoyName" class="">Pincode&nbsp;<span
                                                            class="color-red">*</span></label>
                                                    <input class="form-control border-pink matchInTxt shadow-none"
                                                        id="Name" name="pincode" placeholder="Enter Pincode"
                                                        type="text" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12 py-3">
                                            <div class="row">

                                                <div class="col-12 pt-md-3 text-center mt-2">
                                                    <button type="submit"
                                                        class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                                        id="addressBtn">Add Address</button>
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


    <div class="ds-head-populararticle bg-white cat-pages">
        <div class="container">
            <div class="row py-3">
                <div class="col-sm-12 mt-4">
                    <div class="row">
                        <div class="col-12 mb-5">
                            <h2 class="cat-heading font-24 font-weight-bold">Checkout <span class="color-red">Form</span>
                            </h2>

                        </div>
                        <div class="col-lg-8 col-12 ">
                            <div class="mb-3 shadow-pink">
                                <div class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3">
                                    SELECT ADDRESS
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-6 ml-auto">
                                        <a role="button" data-toggle="modal" data-target="#checkout"
                                            class="mt-3 btn view-more color-red font-weight-normal mb-2">
                                            Add Address
                                        </a>
                                    </div>

                                </div>


                                <form class="px-3 font-14" method="post" id="orderForm" autocomplete="off">

                                    <div class="table-responsive  mt-4 mb-4 ">
                                        <table class="table  border-pink font-14 mb-0 text-center">
                                            <tbody>
                                                <tr class="bg-pink color-red">
                                                    <td>#</td>
                                                    <td>Name</td>
                                                    <td>Phone</td>
                                                    <td>Address</td>
                                                </tr>

                                                @foreach ($getOrderAddress['recordList'] as $getOrderAddress)
                                                    <tr>
                                                        <td> <input type="radio" name="orderAddressId"
                                                                value="{{ $getOrderAddress['id'] }}"></td>
                                                        <td>{{ $getOrderAddress['name'] }}</td>
                                                        <td>{{ $getOrderAddress['phoneNumber'] }}</td>
                                                        <td>{{ $getOrderAddress['flatNo'] }},{{ $getOrderAddress['locality'] }},{{ $getOrderAddress['landmark'] }},{{ $getOrderAddress['city'] }},{{ $getOrderAddress['state'] }},{{ $getOrderAddress['country'] }},{{ $getOrderAddress['pincode'] }}
                                                        </td>

                                                    </tr>
                                                @endforeach



                                            </tbody>
                                        </table>
                                    </div>


                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="shadow-pink p-3">
                                <div class="bg-pink color-red text-center font-weight-semi-bold py-1 px-3">
                                    Product Detail
                                </div>
                                <div class="card border-0 mt-2">
                                    <div class="card-body pt-0">
                                        <div class="row justify-content-between mb-3">
                                            <div class="col-auto">
                                                <div class="media">
                                                    <img class="img-fluid mr-3"
                                                        src="/{{ $getproductdetails['recordList'][0]['productImage'] }}"
                                                        width="62" height="62">
                                                    <div class="media-body">
                                                        <p class="mb-0">
                                                            <b>{{ $getproductdetails['recordList'][0]['name'] }}</b></p>
                                                        <small
                                                            class="text-muted">{{ $getproductdetails['recordList'][0]['features'] }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="productCategoryId"
                                                value="{{ $getproductdetails['recordList'][0]['productCategoryId'] }}">
                                            <input type="hidden" name="productId"
                                                value="{{ $getproductdetails['recordList'][0]['id'] }}">

                                        </div>
                                        <div class="row justify-content-between ">
                                            <div class="col-auto">
                                                <p><span>Price:</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                <p><span>{{ $currency['value'] }}{{ number_format($getproductdetails['recordList'][0]['amount'], 2) }}</span>
                                                </p>
                                                <input type="hidden"
                                                    value="{{ number_format($getproductdetails['recordList'][0]['amount'], 2) }}"
                                                    name="payableAmount">
                                            </div>
                                        </div>

                                        <div class="row justify-content-between">
                                            <div class="col-auto">
                                                <p><span>Gst( {{ $gstvalue['value'] }}%):</span></p>
                                            </div>
                                            <div class="col-auto my-auto">
                                                <p><span>{{ $currency['value'] }}{{ number_format($getproductdetails['recordList'][0]['amount'] * ($gstvalue['value'] / 100), 2) }}</span>
                                                </p>
                                                <input type="hidden" value="{{ $gstvalue['value'] }}"
                                                    name="gstPercent">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row justify-content-between mb-2">
                                            <div class="col-auto">
                                                <p><b>Total Price:</b></p>
                                            </div>
                                            <div class="col-auto my-auto color-red">
                                                <p><b>{{ $currency['value'] }}{{ number_format($getproductdetails['recordList'][0]['amount'] + $getproductdetails['recordList'][0]['amount'] * ($gstvalue['value'] / 100), 2) }}</b>
                                                </p>
                                                <input type="hidden"
                                                    value="{{ number_format($getproductdetails['recordList'][0]['amount'] + $getproductdetails['recordList'][0]['amount'] * ($gstvalue['value'] / 100), 2) }}"
                                                    name="totalPayable">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 py-3">
                                <div class="row">

                                    <div class="col-12 pt-md-3 text-center mt-2">
                                        <button type="submit" class="btn btn-block btn-chat px-4 px-md-5 mb-2"
                                            id="orderBtn">Buy Now</button>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#addressBtn').click(function(e) {
                e.preventDefault();

                @php
                    use Symfony\Component\HttpFoundation\Session\Session;
                    $session = new Session();
                    $token = $session->get('token');

                @endphp

                var formData = $('#orderAddress').serialize();
                // console.log(formData);

                $.ajax({
                    url: '{{ route('api.addOrderAddress', ['token' => $token]) }}',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        toastr.success('Address Added Successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#orderBtn').click(function(e) {
                e.preventDefault();

                @php
                    $token = $session->get('token');
                @endphp

                var paymentMethod = 'wallet';

                if (!$("input[name='orderAddressId']:checked").val()) {
                    toastr.error('Please select an address.');
                    return;
                }

                var formData = $('#orderForm').serialize();
                formData += '&paymentMethod=' + encodeURIComponent(paymentMethod);
                // console.log(formData);


                $.ajax({
                    url: '{{ route('api.addUserOrder', ['token' => $token]) }}',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        toastr.success('Product Ordered Successfully');
                        setTimeout(function() {
                            window.location.href = '{{ route('front.home') }}';
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText).error.paymentMethod[0];
                        toastr.error(errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
