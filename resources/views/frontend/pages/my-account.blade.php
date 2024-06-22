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
                            <i class="fa fa-chevron-right"></i> <a href="{{ route('front.getLiveAstro') }}"
                                style="color:white;text-decoration:none">My Account</a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid container-xl mt-3 email-prefrences" data-select2-id="select2-data-9-7lve">
        <div class="inpage" data-select2-id="select2-data-8-zrys">
            <div class="tab-content py-3" data-select2-id="select2-data-7-dgci">
                <div data-select2-id="select2-data-6-lle8">
                    <div class="text-left">
                        <h1 class="h2 font-weight-bold colorblack">My Account</h1>
                        <p>
                            View and update your profile, change password in your Astroway account.
                        </p>
                    </div>
                    <div class="form-group  mb-o mt-0 mt-lg-4 p-0">
                        <div class="d-flex flex-nowrap">
                            <a
                                class="text-decoration-none colorbrown weight500 mb-4 mt-1 py-2 py-sm-3 px-2 px-sm-3 d-inline-block border-bottom borderbrown">
                                Update Profile</a>

                    </div>

                    <form  id="frmUpdateProfile" enctype="multipart/form-data">
                       @csrf
                        <div class="container" data-select2-id="select2-data-5-q5pn">

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Name <b class="req">*</b></label>
                                        <input autocomplete="off" class="form-control inputtext" data-val="true"

                                            id="FirstName" maxlength="30" name="name" placeholder="Enter First Name"
                                            type="text" value="{{$getuserdetails['userDetails']['name']}}">
                                        <span class="field-validation-valid text-danger" data-valmsg-for="FirstName"
                                            data-valmsg-replace="true"></span>
                                    </div>


                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Email <b class="req">*</b></label>
                                        <input autocomplete="off" class="form-control inputtext" id="EmailAddress"
                                            maxlength="50" name="email"
                                            placeholder="Enter Email" type="text" value="{{$getuserdetails['userDetails']['email']}}">

                                    </div>
                                </div>



                            </div>
                            <div class="row">

                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Mobile <b class="req">*</b></label>
                                        <div class="input-group">

                                            <input autocomplete="off" class="form-control inputtext" data-val="true"
                                                 id="ContactMobile"
                                                maxlength="12" value="{{$getuserdetails['userDetails']['contactNo']}}" name="contactNo"
                                               type="number"
                                                >
                                            <span class="field-validation-valid text-danger"
                                                data-valmsg-for="ContactMobile" data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Gender</label>
                                        <select class="form-control" data-val="true" id="Gender" name="gender">
                                            <option value="" {{ $getuserdetails['userDetails']['gender'] == 0 ? 'selected' : '' }}>--Select--</option>
                                            <option value="Male" {{ $getuserdetails['userDetails']['gender'] == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $getuserdetails['userDetails']['gender'] == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ $getuserdetails['userDetails']['gender'] == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <span class="field-validation-valid text-danger" data-valmsg-for="Gender" data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <label class="pb-1 pb-md-0 form-label">Birth Date</label>
                                    <input type="date" name="birthDate" class="form-control" value="{{date("Y-m-d", strtotime($getuserdetails['userDetails']['birthDate']));}}">
                                    <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                    data-valmsg-replace="true"></span>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Birth Time</label>

                                        <div class="md-form md-outline input-with-post-icon timepicker position-relative"
                                            default="now">
                                            <input type="time" name="birthTime" class="form-control" value="{{$getuserdetails['userDetails']['birthTime']}}">
                                            <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                            data-valmsg-replace="true"></span>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Place Of Birth</label>
                                        <input autocomplete="off" name="birthPlace" class="form-control inputtext ui-autocomplete-input"
                                            id="POB"  placeholder="Enter Place Of Birth"
                                            type="text" value="{{$getuserdetails['userDetails']['birthPlace']}}">
                                        <span class="field-validation-valid text-danger" data-valmsg-for="POB"
                                            data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Current Address</label>
                                        <input class="form-control inputtext"  id="CurrentAddress" value="{{$getuserdetails['userDetails']['addressLine1']}}" maxlength="300"
                                            name="addressLine1" placeholder="Enter Current Address" type="text"
                                            value="">
                                        <span class="field-validation-valid text-danger"
                                            data-valmsg-for="CurrentAddress" data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Location (City/State/Country)</label>
                                        <input autocomplete="off" class="form-control inputtext ui-autocomplete-input"
                                            id="CurrentPlace" name="location"
                                            placeholder="Enter Current City" type="text" value="{{$getuserdetails['userDetails']['location']}}">
                                        <span class="field-validation-valid text-danger" data-valmsg-for="CurrentPlace"
                                            data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Pin Code</label>
                                        <input autocomplete="off" class="form-control inputtext" id="PinCode"
                                            maxlength="12" name="pincode"
                                            placeholder="Enter Pin Code" type="number" value="{{$getuserdetails['userDetails']['pincode']}}">
                                        <span class="field-validation-valid text-danger" data-valmsg-for="PinCode"
                                            data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="pb-1 pb-md-0 form-label">Profile <b class="req">*</b></label>
                                        <input class="form-control" id="profilepic" name="profilepic" style="height:44px;" type="file" value="">
                                        <span class="field-validation-valid text-danger" data-valmsg-for="FirstName"
                                            data-valmsg-replace="true"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-12 text-center pt-4">
                                    <div class="form-group text-right">
                                        <input type="button" id="btnSave" value="Update Profile"
                                            class="btn btn-chat font-weight-semi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
        function isNumberKey(evt) {
        var e = event || evt;
        var CharCode = e.which || e.keyCode;
        if (CharCode == 13) {
            $("#btnVerify").click();
            return false;
        }
        if (CharCode > 31 && (CharCode < 48 || CharCode > 57))
            return false;
    }
</script>
<script>
 $(document).ready(function() {
    $('#btnSave').click(function(e) {
        e.preventDefault();

        @php
        $id=authcheck()['id']
        @endphp
        var formData = new FormData($('#frmUpdateProfile')[0]);
        formData.append('profilepic', $('#profilepic')[0].files[0]);

        $.ajax({
            url: '{{ route("user.update", ['id' => $id]) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success('Profile Updated Successfully');
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
@endsection
