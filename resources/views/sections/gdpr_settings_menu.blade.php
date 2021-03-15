@section('other-section')
<ul class="nav tabs-vertical">
    <li class="tab">
        <a href="{{ route('admin.settings.index') }}" class="text-danger"><i class="ti-arrow-left"></i> {{ mb_convert_case(__('app.back'),MB_CASE_TITLE,"UTF-8") }}</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.index') active @endif">
        <a href="{{ route('admin.gdpr.index') }}">{{ ucwords(__('app.General')) }}</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-data-portability') active @endif">
        <a href="{{ route('admin.gdpr.right-to-data-portability') }}">{{ mb_convert_case(__('app.Righttodataportability'),MB_CASE_TITLE).PHP_EOL }}</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-erasure') active @endif">
        <a href="{{ route('admin.gdpr.right-to-erasure') }}">{{ mb_convert_case(__('app.RighttoErasure'),MB_CASE_TITLE).PHP_EOL }}</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-informed') active @endif">
        <a href="{{ route('admin.gdpr.right-to-informed') }}">{{ mb_convert_case(__('app.Righttobeinformed'),MB_CASE_TITLE).PHP_EOL }}</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-of-access') active @endif">
        <a href="{{ route('admin.gdpr.right-of-access') }}">{{ mb_convert_case(__('app.Rightofaccess'),MB_CASE_TITLE).PHP_EOL }}/{{ mb_convert_case(__('app.Righttorectification'),MB_CASE_TITLE).PHP_EOL }}</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.consent') active @endif">
        <a href="{{ route('admin.gdpr.consent') }}">{{ mb_convert_case(__('app.Consent'),MB_CASE_TITLE).PHP_EOL }}</a></li>
</ul>

<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script>
    var screenWidth = $(window).width();
    if(screenWidth <= 768){

        $('.tabs-vertical').each(function() {
            var list = $(this), select = $(document.createElement('select')).insertBefore($(this).hide()).addClass('settings_dropdown form-control');

            $('>li a', this).each(function() {
                var target = $(this).attr('target'),
                    option = $(document.createElement('option'))
                        .appendTo(select)
                        .val(this.href)
                        .html($(this).html())
                        .click(function(){
                            if(target==='_blank') {
                                window.open($(this).val());
                            }
                            else {
                                window.location.href = $(this).val();
                            }
                        });

                if(window.location.href == option.val()){
                    option.attr('selected', 'selected');
                }
            });
            list.remove();
        });

        $('.settings_dropdown').change(function () {
            window.location.href = $(this).val();
        })

    }
</script>
@endsection