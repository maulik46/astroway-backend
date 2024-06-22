@extends('layout.app')
@section('content')
<section class="blog-page" style="padding-top: 160px !important;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="blog-details news-slid">
                    <div class="news-text">
                        <div class="blog-hover">
                            <h4>{{$terms->title}}</h4>
                        </div>
                        {!!$terms->description!!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
