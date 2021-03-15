<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title> {{ __($pageTitle) }} | {{ ucwords($setting->company_name)}}</title>

    <!-- Bootstrap CSS -->
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/animate-css/animate.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick-theme.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/fonts/flaticon/flaticon.css') }}">
    <link href="{{ asset('front/plugin/froiden-helper/helper.css') }}" rel="stylesheet">
    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/css/main.css') }}">
    <!-- Template Font Family  -->
    <link type="text/css" rel="stylesheet" media="all"
          href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap">
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        :root {
            --main-color: {{ $frontDetail->primary_color }};
        }

    </style>
</head>

<body id="home">


<!-- Topbar -->
@include('sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100 login-section" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 shadow bg-white form-section">
            <h4 class="mb-0">
                {{$pageTitle}}
            </h4>

            {!! Form::open(['id'=>'register','class'=>'form-horizontal form-material', 'method'=>'POST']) !!}
            <p>
                {{__('subdomain::app.messages.forgotPageMessage')}}

            </p>
            <p id="alert"></p>
            <div class="form-group removeable {{ $errors->has('email') ? 'has-error' : '' }}">
                <div class="col-xs-12">
                    <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}"
                           autofocus required="" placeholder="@lang('app.email')">
                    @if ($errors->has('email'))
                        <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                    @endif

                </div>
            </div>

            <div class="form-group removeable text-center m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light"
                            type="submit" id="submit-form"
                            onclick="confirm();return false;"
                    >@lang('app.submit')</button>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->
{{--@include('saas.sections.cta')--}}
<!-- End Cta -->

<!-- Footer -->
@include('sections.saas.saas_footer')
<!-- END Footer -->


<!-- Scripts -->
<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('saas/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('saas/vendor/wowjs/wow.min.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->

<script>
    function confirm() {
        $.easyAjax({
            url: '{{route('front.submit-forgot-password')}}',
            container: '.form-section',
            type: "POST",
            data: $('#register').serialize(),
            messagePosition: "inline",
            success: function (response) {
                if (response.status == 'success') {
                    $('.removeable').remove();
                }
            }
        })
    }

</script>
</body>
</html>
