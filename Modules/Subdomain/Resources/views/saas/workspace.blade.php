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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Patua+One&display=swap" rel="stylesheet">

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
            <p id="alert"></p>

            <div class="form-group @if($errors->has('sub_domain')) has-error @endif">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="subdomain" name="sub_domain" id="sub_domain">
                    <div class="input-group-append">
                        @if(function_exists('get_domain'))
                            <span class="input-group-text" id="basic-addon2">.{{ get_domain() }}</span>
                        @else
                            <span class="input-group-text" id="basic-addon2">.{{ $_SERVER['SERVER_NAME'] }}</span>
                        @endif
                    </div>

                </div>
                @if ($errors->has('sub_domain'))
                    <div class="help-block">{{ $errors->first('sub_domain') }}</div>
                @endif
            </div>

            <div class="form-group text-center">
                <div class="col-xs-6 text-center">
                    <button class="btn btn-info btn-sm b btn-round text-uppercase waves-effect waves-light"
                            type="submit" id="save-form">@lang('subdomain::app.core.continue')</button>
                </div>
            </div>

            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center">
                    <p>{{__('subdomain::app.core.signInTitle')}}
                        <a href="{{ route('front.forgot-company') }}"
                           class="text-primary m-l-5">
                            <b>
                                {{__('subdomain::app.messages.findCompanyUrl')}}

                            </b></a></p>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                    <script>
                        var facebook = "{{ route('social.login', 'facebook') }}";
                        var google = "{{ route('social.login', 'google') }}";
                        var twitter = "{{ route('social.login', 'twitter') }}";
                        var linkedin = "{{ route('social.login', 'linkedin') }}";
                    </script>
                    @if(isset($socialAuthSettings))
                        <div class="social mb-1">
                            @if($socialAuthSettings->facebook_status == 'enable')
                                <a href="javascript:;" class="btn btn-primary btn-facebook" data-toggle="tooltip" title="Login with Facebook" onclick="window.location.href = facebook;" data-original-title="Login with Facebook"> <i aria-hidden="true" class="zmdi zmdi-facebook"></i> </a>
                            @endif
                            @if($socialAuthSettings->google_status == 'enable')
                                <a href="javascript:;" class="btn btn-primary btn-google" data-toggle="tooltip" title="Login with Google" onclick="window.location.href = google;" data-original-title="Login with Google"> <i aria-hidden="true" class="zmdi zmdi-google"></i> </a>
                            @endif
                            @if($socialAuthSettings->twitter_status == 'enable')
                                <a href="javascript:;" class="btn btn-primary btn-twitter" data-toggle="tooltip" title="Login with twitter" onclick="window.location.href = twitter;" data-original-title="Login with Google"> <i aria-hidden="true" class="zmdi zmdi-twitter"></i> </a>
                            @endif
                            @if($socialAuthSettings->linkedin_status == 'enable')
                                <a href="javascript:;" class="btn btn-primary btn-linkedin" data-toggle="tooltip" title="Login with linkedin" onclick="window.location.href = linkedin;" data-original-title="Login with Linkedin"> <i aria-hidden="true" class="zmdi zmdi-linkedin"></i> </a>
                            @endif
                        </div>
                    @endif
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
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->

<script>

    $('#save-form').on('click', function (e) {
        e.preventDefault();
        $.easyAjax({
            url: '{{route('front.check-domain')}}',
            container: '#register',
            type: "POST",
            messagePosition: "inline",
            data: $('#register').serialize(),

        })
    });

</script>
</body>
</html>
