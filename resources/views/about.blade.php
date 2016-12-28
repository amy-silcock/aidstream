<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <title>@lang('title.aidstream')</title>
    <link rel="shortcut icon" type="image/png" sizes="16*16" href="images/favicon.png"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.min.css">
</head>
<body>
@include('includes.header_home')
<section class="main-container">
    <div class="introduction-wrapper about-wrapper">
        <div class="col-md-12 text-center">
            <h1>@lang('about.about_us')</h1>

            <p>@lang('about.we_built_aidstream')</p>
        </div>
        <div class="screenshot text-center">
            <img src="images/screenshot.png" alt="">
        </div>
    </div>
    {{--<div class="col-md-12 text-center heading-title">--}}
    {{--<h2>Who is behind AidStream?</h2>--}}
    {{--</div>--}}
    <div class="information-wrapper bottom-line">
        <div class="information-section about-information-section">
            <div class="col-md-12 text-center">
                <div>
                    <p>
                        {{--<a href="http://www.yipl.com.np">YoungInnovations</a>,--}}
                        {{--a Nepal based software development firm that is engaged in providing open data and technology--}}
                        {{--solutions to a range of development issues including transparency, accountability and civic--}}
                        {{--engagement is behind AidStream. We are thankful to our partners <a href="http://devinit.org/">Development Initiatives</a>,--}}
                        {{--<a href="http://www.aidtransparency.net/governance/secretariat">IATI--}}
                        {{--Secretariat</a> and others who have helped us in various fronts to bring Aidstream to you since--}}
                        {{--2012.--}}
                        @lang('about.and_who_are_we')
                    </p>
                    <p>
                        {{--Although AidStream as a service is available for any organizations around the world to publish--}}
                        {{--their activities into IATI, it has also been developed as an Open Source product that is--}}
                        {{--available in <a href="https://github.com/younginnovations/aidstream-new">GitHub</a>. Please feel free to fork it and use it for your purpose. All of our future--}}
                        {{--enhancements and developments will continue to remain open source.--}}
                        @lang('about.aidstream_is_available')
                    </p>
                    <p>
                        @lang('about.if_you_value_our_contribution')

                        {{--If you see the value of our contribution to the community, we need your support! Please reach out to--}}
                        {{--us at <a href="mailto:info@aidstream.org">info@aidstream.org</a> to see how you can help us.--}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@include('includes.footer')
<script src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        function hamburgerMenu() {
            $('.navbar-toggle.collapsed').click(function () {
                $('.navbar-collapse').toggleClass('out');
                $(this).toggleClass('collapsed');
            });
        }

        hamburgerMenu();
    });
</script>
</body>
</html>
