<!--
Optimumbrew Technology

Project         :  Photoadking
File            :  design_page.blade.php

File Created    :  Saturday, 23th May 2022 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Saturday, 23th May 2022 05:22:26 pm
-----
Purpose          :  This file create dynamic html for all design page.
-----
Copyright 2018 - 2022 Optimumbrew Technology

-->
@php
    $activation_link_path = config('constant.ACTIVATION_LINK_PATH');
    $cloudfront_link_path = config('constant.CDN_STATIC_WEB_ASSETS_PATH');
@endphp
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{!! $json_data->seo_section->page_detail->page_title !!}</title>
    <meta name="theme-color" content="#317EFB">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" content="{!! $json_data->seo_section->page_detail->meta !!}">
    <meta name="COPYRIGHT" content="PhotoADKing">
    <meta name="AUTHOR" content="PhotoADKing">
    <link rel="canonical" href="{!! $json_data->seo_section->page_detail->canonical !!}">

    <link async="" rel="preload" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          as="style">
          <link async="" rel="preload" href="{!! $activation_link_path !!}/css/new_style.css?v4.86" as="style">
          <link async="" rel="preload" href="{!! $activation_link_path !!}/css/new_css.css?v4.86" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">

    <link async rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
      type="text/css" media="all" />

    <link async="" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          type="text/css" media="all">
    <script>
        if (window.screen.width > 767) {
            document.currentScript.insertAdjacentHTML('beforebegin',
                '<link async rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" as = "style" />'
                + '<link async rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" type="text/css" media="all" />'
            )
        }
    </script>
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Regular.otf" as="font" crossorigin="">
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Bold.otf" as="font" crossorigin="">
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Regular.otf" as="font" crossorigin="">
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Bold.otf" as="font" crossorigin="">
    <link async="" rel="stylesheet" href="{!! $activation_link_path !!}/css/new_css.css?v4.86" type="text/css"
          media="all">
    <link rel="stylesheet" href="{!! $activation_link_path !!}/css/new_style.css?v4.86" type="text/css" media="all">

    <meta property="og:image:height" content="462">
    <meta property="og:image:width" content="883">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{!! $json_data->seo_section->page_detail->page_title !!}">
    <meta property="og:description"
          content="{!! $json_data->seo_section->page_detail->meta !!}">
    <meta property="og:image" content="{!! $activation_link_path !!}/photoadking.png?v1.6">
    <meta property="og:url" content="{!! $json_data->seo_section->page_detail->canonical !!}">

    <meta name="twitter:title" content="{!! $json_data->seo_section->page_detail->page_title !!}">
    <meta name="twitter:description"
          content="{!! $json_data->seo_section->page_detail->meta !!}">
    <meta name="twitter:image" content="{!! $activation_link_path !!}/photoadking.png?v1.6">
    <meta name="twitter:url" content="{!! $activation_link_path !!}">
    <meta http-equiv="Expires" content="1">

    <link rel="apple-touch-icon" sizes="57x57"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-57x57.png?v1.3">
    <link rel="apple-touch-icon" sizes="60x60"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-60x60.png?v1.3">
    <link rel="apple-touch-icon" sizes="72x72"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-72x72.png?v1.3">
    <link rel="apple-touch-icon" sizes="76x76"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-76x76.png?v1.3">
    <link rel="apple-touch-icon" sizes="114x114"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-114x114.png?v1.3">
    <link rel="apple-touch-icon" sizes="120x120"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-120x120.png?v1.3">
    <link rel="apple-touch-icon" sizes="144x144"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-144x144.png?v1.3">
    <link rel="apple-touch-icon" sizes="152x152"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-152x152.png?v1.3">
    <link rel="apple-touch-icon" sizes="180x180"
          href="{!! $cloudfront_link_path !!}/favicon/apple-icon-180x180.png?v1.3">
    <link rel="icon" type="image/png" sizes="512x512"
          href="{!! $cloudfront_link_path !!}/favicon/android-icon-512x512.png?v1.3">
    <link rel="icon" type="image/png" sizes="192x192"
          href="{!! $cloudfront_link_path !!}/favicon/android-icon-192x192.png?v1.3">
    <link rel="icon" type="image/png" sizes="96x96"
          href="{!! $cloudfront_link_path !!}/favicon/favicon-96x96.png?v1.3">
    <link rel="icon" type="image/png" sizes="48x48"
          href="{!! $cloudfront_link_path !!}/favicon/android-icon-48x48.png?v1.3">
    <link rel="icon" type="image/png" sizes="32x32"
          href="{!! $cloudfront_link_path !!}/favicon/favicon-32x32.png?v1.3">
    <link rel="icon" type="image/png" sizes="16x16"
          href="{!! $cloudfront_link_path !!}/favicon/favicon-16x16.png?v1.3">
    <link rel="shortcut icon" type="image/icon" href="{!! $cloudfront_link_path !!}/favicon/favicon.ico?v1.3">
    <link rel="icon" type="image/icon" href="{!! $cloudfront_link_path !!}/favicon/favicon.ico?v1.3">
    <link rel="mask-icon" href="{!! $cloudfront_link_path !!}/favicon/safari-pinned-tab.svg" color="#1b94df">
    <link rel="manifest" href="{!! $cloudfront_link_path !!}/favicon/manifest.json?v1.3">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage"
          content="{!! $cloudfront_link_path !!}/favicon/ms-icon-144x144.png?v1.3">
    <meta name="msapplication-config" content="{!! $cloudfront_link_path !!}/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <script src="https://apis.google.com/js/platform.js" async=""></script>
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-128186520-1"></script>
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=AW-859101740"></script>


    <script>
        if (window.location.host == 'photoadking.com') {
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', 'AW-859101740');
            gtag('config', 'UA-128186520-1');
        }

    </script>
    <script>
        if (window.location.host == 'photoadking.com') {
            try {
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-128186520-1', 'auto');
                ga('send', 'pageview');
            } catch (error) {
                console.log(error);
            }
        }

    </script>
    <!-- Facebook Pixel Code -->
    <!-- <script>
      if (window.location.host == 'photoadking.com') {
        ! function (f, b, e, v, n, t, s) {
          if (f.fbq) return;
          n = f.fbq = function () {
            n.callMethod ?
              n.callMethod.apply(n, arguments) : n.queue.push(arguments)
          };
          if (!f._fbq) f._fbq = n;
          n.push = n;
          n.loaded = !0;
          n.version = '2.0';
          n.queue = [];
          t = b.createElement(e);
          t.async = !0;
          t.src = v;
          s = b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
          'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '245438776057897');
        fbq('track', 'PageView');
      }

    </script> -->
    <!-- <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=245438776057897&ev=PageView&noscript=1" /></noscript> -->
    <!-- Facebook Pixel Code -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      if (window.location.host == 'photoadking.com') {
        (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-7573840847608910",
          enable_page_level_ads: true
        });
      }

    </script> -->

  @if(isset($json_data->seo_section->faqs) && $json_data->seo_section->faqs)
    <script xmlns="http://www.w3.org/1999/xhtml" type="application/ld+json">
      {!! $json_data->seo_section->faqs !!}
    </script>
  @endif

  @if(isset($json_data->seo_section->guide_schema) && $json_data->seo_section->guide_schema)
    <script xmlns="http://www.w3.org/1999/xhtml" type="application/ld+json">
      {!! $json_data->seo_section->guide_schema !!}
    </script>
  @endif

  @if(isset($json_data->seo_section->rating_schema) && $json_data->seo_section->rating_schema)
    <script xmlns="http://www.w3.org/1999/xhtml" type="application/ld+json">
      {!! $json_data->seo_section->rating_schema !!}
    </script>
  @endif

</head>


<body class="position-relative">
<div class="w-100">
    <div class="fchat-wrapper-div">
        <img src="{!! $cloudfront_link_path !!}/BG_shadow.png" id="chat_icon" alt="freshchat icon" width="90px"
             height="90px" onclick="initializeFeshchat()" class="fchat-bg-style">
        <img src="{!! $cloudfront_link_path !!}/chat.svg" id="chat_ic" class="fchat-chat-icon" alt="chat icon"
             height="24px" width="24px" onclick="initializeFeshchat()">

        <img src="{!! $cloudfront_link_path !!}/rolling.svg" id="loader_ic" alt="loading icon"
             class="disply-none fchat-loder-icon" height="24px" width="24px">
    </div>

</div>

<div class="l-body-container bg-white" id="mainbody">
    <div class="c-con-pedding bg-white">
        <div class="w-100 privacy-header-bg l-blue-bg position-relative sec-first c-header-bg" id="Home">
            <!-- <div id="header"></div> -->
            <div class="w-100">
                <ul class="l-mob-menu-container " id="mobmenu">
                    <li class="px-4 pb-1 pt-cust-18"><a href="{!! $activation_link_path !!}/app/#/"
                                                        class="btn btn-sm w-100 loginbtn_link" target="_blank" href="#">Log
                            In</a>
                    </li>
                    <li class="px-4 py-2 mb-2"><a href="{!! $activation_link_path !!}/app/#/sign-up"
                                                  class="btn btn-sm w-100 signupbtn_link" target="_blank" href="#">Sign
                            Up
                            Free</a></li>
                    <li>
                        <a onmousedown="openExpansion('#mob_create')" data-parent="#Home" class="position-relative"
                           href="#mob_create" data-toggle="collapse" aria-expanded="false">Create
                            <svg class="ul-header-list-img"
                                 xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000"
                                 preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                   stroke="none">
                                    <path
                                            d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                        <div id="mob_create" class="collapse in">
                            <ul style="padding-left: 15px;">
                                <li><a href="{!! $activation_link_path !!}/create/online-graphic-maker/"
                                       class="remove-style" onclick="hideOverlay()">Graphic
                                        Maker</a></li>
                                <li><a href="{!! $activation_link_path !!}/create/video-templates/" class="remove-style"
                                       onclick="hideOverlay()">Promo Video
                                        Maker</a></li>
                                <li><a href="{!! $activation_link_path !!}/create/youtube-intro-maker/"
                                       class="remove-style" onclick="hideOverlay()">Intro
                                        Maker</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a onclick="openExpansion('#mob_templates')" data-parent="#Home" class="position-relative"
                           href="#mob_templates" data-toggle="collapse" aria-expanded="false">templates
                            <svg
                                    class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0"
                                    viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                   stroke="none">
                                    <path
                                            d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                        <div id="mob_templates" class="collapse">
                            <ul style="padding-left: 0px;">
                                <li><a onclick="openSubcatExpansion('#social')" class="position-relative" href="#social"
                                       data-toggle="collapse" aria-expanded="false"><span
                                                class="pl-3">Social Media</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="social" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/facebook-cover/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Facebook
                                                    Cover </a></li>
                                            <li>
                                                <a href="{!! $activation_link_path !!}/templates/social-media-post/facebook-post/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Facebook
                                                    Post</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/instagram-post/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Instagram
                                                    Post</a></li>
                                            <li>
                                                <a href="{!! $activation_link_path !!}/templates/social-story/instagram-story/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Instagram
                                                    Story</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/linkedin-banner/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">LinkedIn
                                                    Cover</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/pinterest-post/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Pinterest
                                                    Graphic</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/youtube-channel-art/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">YouTube
                                                    Channel Art</a>
                                            <li><a href="{!! $activation_link_path !!}/templates/youtube-thumbnail/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">YouTube
                                                    Thumbnail</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a onclick="openSubcatExpansion('#Marketing')" class="position-relative"
                                       href="#Marketing"
                                       data-toggle="collapse" aria-expanded="false"><span class="pl-3">Marketing</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="Marketing" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/flyers/"
                                                   class="remove-style" onclick="hideOverlay()">Flyer</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/posters/"
                                                   class="remove-style" onclick="hideOverlay()">Poster</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/brochures/"
                                                   class="remove-style" onclick="hideOverlay()">Brochure</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/business-card/"
                                                   class="remove-style" onclick="hideOverlay()">Business
                                                    Card</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/gift-certificate/"
                                                   class="remove-style" onclick="hideOverlay()">Gift
                                                    Certificate</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a onclick="openSubcatExpansion('#Blogging')" class="position-relative"
                                       href="#Blogging"
                                       data-toggle="collapse" aria-expanded="false"><span class="pl-3">Blogging</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="Blogging" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/infographics/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Infographic</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/book-cover/e-book/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">E-Book</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/blog-image/"
                                                   class="remove-style" onclick="hideOverlay()">Blog
                                                    Graphic</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/storyboard/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Storyboard</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/album-covers/"
                                                   class="remove-style" onclick="hideOverlay()">Album
                                                    Cover </a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/email-header/"
                                                   class="remove-style" onclick="hideOverlay()">Email
                                                    Header</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a onclick="openSubcatExpansion('#personal')" class="position-relative"
                                       href="#personal"
                                       data-toggle="collapse" aria-expanded="false"><span class="pl-3">Personal</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="personal" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/invitations/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Invitation</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/resume/"
                                                   class="remove-style" onclick="hideOverlay()">Resume</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/biodata/"
                                                   class="remove-style" onclick="hideOverlay()">Bio-Data</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/planners/"
                                                   class="remove-style" onclick="hideOverlay()">Planner</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/postcard-templates/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Postcard</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/greeting-cards/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Greeting</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a onclick="openSubcatExpansion('#Business')" class="position-relative"
                                       href="#Business"
                                       data-toggle="collapse" aria-expanded="false"><span class="pl-3">Business</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="Business" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/logo/"
                                                   class="remove-style" onclick="hideOverlay()">Logo</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/invoice-templates/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Invoice</a></li>
                                            <li><a href="{!! $activation_link_path !!}/letterhead-maker/ "
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Letterhead</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/presentation-maker/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Presentation</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/certificate/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Certificate</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/restaurant-menu/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Menu</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li><a onclick="openSubcatExpansion('#Trending')" class="position-relative"
                                       href="#Trending"
                                       data-toggle="collapse" aria-expanded="false"><span class="pl-3">Trending</span>
                                        <svg
                                                class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                               fill="#424d5c"
                                               stroke="none">
                                                <path
                                                        d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                                </path>
                                            </g>
                                        </svg>
                                    </a>
                                    <div id="Trending" class="sub-opt collapse">
                                        <ul style="padding-left: 30px;">
                                            <li><a href="{!! $activation_link_path !!}/templates/animated-video-maker/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Animated Video</a></li>
                                            <li>
                                                <a href="{!! $activation_link_path !!}/templates/social-story/whatsapp-status/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Status Video</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/youtube-intro-maker/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Intro
                                                    Video</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/outro-video-maker/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Outro
                                                    Video </a></li>
                                            <li><a href="{!! $activation_link_path !!}/design/video-flyer-maker/"
                                                   class="remove-style" onclick="hideOverlay()">Video
                                                    Flyer</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li><a onclick="openExpansion('#mob_features')" class="position-relative" data-parent="#Home"
                           href="#mob_features" data-toggle="collapse" aria-expanded="false">Features
                            <svg class="ul-header-list-img"
                                 xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000"
                                 preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                   stroke="none">
                                    <path
                                            d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                        <div id="mob_features" class="collapse">
                            <ul style="padding-left: 15px;">
                                <li><a href="{!! $activation_link_path !!}/features/online-graphic-editor/"
                                       class="remove-style" onclick="hideOverlay()">Graphic
                                        Editor</a></li>
                                <li><a href="{!! $activation_link_path !!}/features/online-video-maker/"
                                       class="remove-style" onclick="hideOverlay()">Video
                                        Maker</a></li>
                                <li><a href="{!! $activation_link_path !!}/features/free-intro-maker/"
                                       class="remove-style" onclick="hideOverlay()">Intro
                                        Editor</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a onclick="openExpansion('#mob_learn')" class="position-relative" href="#mob_learn"
                           data-parent="#Home"
                           data-toggle="collapse" aria-expanded="false">Learn
                            <svg class="ul-header-list-img"
                                 xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000"
                                 preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                   stroke="none">
                                    <path
                                            d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                        <div id="mob_learn" class="collapse">
                            <ul style="padding-left: 15px;">
                                <li><a href="https://blog.photoadking.com/" class="remove-style"
                                       onclick="hideOverlay()">Getting
                                        Started</a></li>
                                <li><a href="https://blog.photoadking.com/ideas-and-inspirations/" class="remove-style"
                                       onclick="hideOverlay()">Design
                                        Inspiration</a></li>
                                <li><a href="https://blog.photoadking.com/video-marketing/" class="remove-style"
                                       onclick="hideOverlay()">Marketing Mania</a>
                                </li>
                                <li><a href="https://blog.photoadking.com/how-to-make/" class="remove-style"
                                       onclick="hideOverlay()">Tutorials</a></li>
                                <li><a href="{!! $activation_link_path !!}/whats-new/" class="remove-style" onclick="hideOverlay()">What's New</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a onclick="openExpansion('#mob_pricing')" class="position-relative" data-parent="#Home"
                           href="#mob_pricing" data-toggle="collapse" aria-expanded="false">Pricing
                            <svg class="ul-header-list-img"
                                 xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000"
                                 preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                   stroke="none">
                                    <path
                                            d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                        <div id="mob_pricing" class="collapse">
                            <ul style="padding-left: 15px;">
                                <li><a href="{!! $activation_link_path !!}/go-premium/" class="remove-style"
                                       onclick="hideOverlay()">Free</a></li>
                                <li><a href="{!! $activation_link_path !!}/go-premium/" class="remove-style"
                                       onclick="hideOverlay()">Pro</a></li>
                                <!--
                               if you want to add other option in pricing then uncomment below code
                                <li><a href="" class="remove-style">Enterprise</a></li>
                              <li><a href="" class="remove-style">Education</a></li>
                              <li><a href="" class="remove-style">Nonprofits</a></li> -->
                            </ul>
                        </div>
                    </li>
                    <!--  <li id="hd-login"><a href="app/#/login">Login</a></li>
                    <li><a href="app/#/sign-up" id="rlp-text-mob">Signup</a></li> -->
                </ul>
                <div class="overlay" style="display: none;"></div>
                <div class="l-transition-5 l-header-big bg-white" id="docHeader">
                    <div class="col-12 col-lg-12 col-md-12 col-sm-12 l-min-md-pd px-0 m-auto"><a href="{!! $activation_link_path !!}">

                            <div class="l-logo-div float-left ml-0 pl-0"><img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        data-src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        alt="PhotoADKing Logo" width="180px" height="39px"
                                        id="brand_logo1" name="brand-img1" class="l-blue-logo"> <img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        width="180px" height="39px"
                                        data-src="{!! $cloudfront_link_path !!}/photoadking.svg" id="brand_logo2"
                                        name="brand-img2"
                                        alt="PhotoADKing Logo" class="l-wht-logo"> <img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg" width="180px"
                                        height="39px" id="brand_logo3" name="brand-img3"
                                        data-src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        alt="PhotoADKing Logo" class="l-white-logo"></div>
                        </a>
                        <div class="float-left l-menu align-items-center">
                            <ul class="l-menu-container  mt-10px">
                                <li><a id="home_lnk" href="{!! $activation_link_path !!}/create/online-graphic-maker/"
                                       name="home-btn" role="button"
                                       data-html="true" data-content='<div class="row m-0 p-8px mt-1 createbtn">
                  <div class="col-4 m-0 pl-20">
                    <a href="{!! $activation_link_path !!}/create/online-graphic-maker/"><div class="popover-image"><img src="{!! $cloudfront_link_path !!}/resources/Create_graphic_design.png" width="160px" height="111px" alt="" ></div></a>
                    <a class="remove-style" href="{!! $activation_link_path !!}/create/online-graphic-maker/"><h4 class="header-popover-heading">Graphic Maker</h4></a>
                    <p class="header-popover-desc">Create stunning graphics with graphic design templates. </p>
                  </div>
                  <div class="col-4 m-0 pl-20">
                    <div class="text-center popover-image">
                      <a href="{!! $activation_link_path !!}/create/video-templates/"><img src="{!! $cloudfront_link_path !!}/resources/Create_promo_video.png" width="160px" height="111px" alt="" class="popover-image"></a>
                    </div>
                    <a class="remove-style" href="{!! $activation_link_path !!}/create/video-templates/"><h4 class="header-popover-heading">Promo Video</h4></a>
                    <p class="header-popover-desc">Create short video ads with promo video maker. </p>
                  </div>
                  <div class="col-4 m-0 pl-20">
                    <div class="popover-image text-right ">
                    <a href="{!! $activation_link_path !!}/create/youtube-intro-maker/"><img src="{!! $cloudfront_link_path !!}/resources/Create_intro_maker.png" width="160px" height="111px" alt="" class="popover-image text-right" >
                    </div>
                    <a class="remove-style" href="{!! $activation_link_path !!}/create/youtube-intro-maker/"><h4 class="header-popover-heading">Intro Maker</h4></a>
                    <p class="header-popover-desc">Create stunning YouTube video intro with templates.</p>
                  </div>
                </div>'>Create </a></li>
                                <li><a id="template_lnk" name="template-btn"
                                       href="{!! $activation_link_path !!}/templates/" role="button" data-html="true"
                                       data-content='<div class="row m-0 templatebtn p-8px mt-1">
                    <div class="col-2 m-0 pl-20">
                      <a ><div class="popover-image"><img src="{!! $cloudfront_link_path !!}/resources/Template_Social_media.png" width="160px" height="111px" alt="" ></div></a>
                      <a  class="remove-style"><h4 class="header-popover-heading">Social Media</h4></a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/facebook-cover/">Facebook Cover </a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/social-media-post/facebook-post/">Facebook Post</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/instagram-post/">Instagram Post</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/social-story/instagram-story/">Instagram Story</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/linkedin-banner/">LinkedIn Cover</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/pinterest-post/">Pinterest Graphic</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/youtube-channel-art/">YouTube Channel Art</a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/youtube-thumbnail/">YouTube Thumbnail</a>
                      </div>

                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! $cloudfront_link_path !!}/resources/Template_marketing.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Marketing</h4></a>
                      <a class="template-link" href="{!! $activation_link_path !!}/templates/flyers/">Flyer</a><a class="template-link" href="{!! $activation_link_path !!}/templates/posters/">Poster</a><a class="template-link" href="{!! $activation_link_path !!}/templates/brochures/">Brochure</a><a class="template-link" href="{!! $activation_link_path !!}/templates/business-card/">Business Card</a><a class="template-link" href="{!! $activation_link_path !!}/templates/gift-certificate/">Gift Certificate</a>
                    </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! $cloudfront_link_path !!}/resources/Template_blogging.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Blogging</h4></a>
                     <a class="template-link" href="{!! $activation_link_path !!}/templates/infographics/">Infographic</a><a class="template-link" href="{!! $activation_link_path !!}/templates/book-cover/e-book/">E-Book</a><a class="template-link" href="{!! $activation_link_path !!}/templates/blog-image/">Blog Graphic</a><a class="template-link" href="{!! $activation_link_path !!}/templates/storyboard/">Storyboard</a><a class="template-link" href="{!! $activation_link_path !!}/templates/album-covers/">Album Cover</a><a class="template-link" href="{!! $activation_link_path !!}/templates/email-header/">Email Header</a> </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! $cloudfront_link_path !!}/resources/Template_Personal.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Personal</h4></a>
                     <a class="template-link" href="{!! $activation_link_path !!}/templates/invitations/">Invitation</a><a class="template-link" href="{!! $activation_link_path !!}/templates/resume/">Resume</a><a class="template-link" href="{!! $activation_link_path !!}/templates/biodata/">Bio-Data</a><a class="template-link" href="{!! $activation_link_path !!}/templates/planners/">Planner</a><a class="template-link" href="{!! $activation_link_path !!}/templates/postcard-templates/">Postcard</a><a class="template-link" href="{!! $activation_link_path !!}/templates/greeting-cards/">Greeting</a> </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! $cloudfront_link_path !!}/resources/Template_business.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Business</h4></a>
                     <a class="template-link" href="{!! $activation_link_path !!}/templates/logo/">Logo</a><a class="template-link" href="{!! $activation_link_path !!}/templates/invoice-templates/">Invoice</a><a class="template-link" href="{!! $activation_link_path !!}/letterhead-maker/ ">Letterhead</a><a class="template-link" href="{!! $activation_link_path !!}/presentation-maker/">Presentation</a><a class="template-link" href="{!! $activation_link_path !!}/templates/certificate/">Certificate</a><a class="template-link" href="{!! $activation_link_path !!}/templates/restaurant-menu/">Menu</a>
                    </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-right popover-image">
                        <a ><img src="{!! $cloudfront_link_path !!}/resources/Template_Trending.png" width="160px" height="111px" alt="" class=" text-right">
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Trending</h4></a>
                     <a class="template-link" href="{!! $activation_link_path !!}/templates/animated-video-maker/">Animated Video</a><a class="template-link" href="{!! $activation_link_path !!}/templates/social-story/whatsapp-status/">Status Video</a><a class="template-link" href="{!! $activation_link_path !!}/templates/youtube-intro-maker/  ">Intro Video</a><a class="template-link" href="{!! $activation_link_path !!}/templates/outro-video-maker/">Outro Video</a><a class="template-link" href="{!! $activation_link_path !!}/design/video-flyer-maker/">Video Flyer</a>
                    </div>
                  </div>'> Templates</a></li>
                                <li><a id="goFeatures"
                                       href="{!! $activation_link_path !!}/features/online-graphic-editor/"
                                       name="feature-btn" role="button"
                                       data-html="true" data-content='<div class="row m-0 p-8px mt-1 createbtn">
                <div class="col-4 m-0 pl-20">
                  <a href="{!! $activation_link_path !!}/features/online-graphic-editor/"><div class="popover-image"><img src="{!! $cloudfront_link_path !!}/resources/Feature_graphic_editor.png" width="160px" height="111px" alt="" ></div></a>
                  <a href="{!! $activation_link_path !!}/features/online-graphic-editor/"  class="remove-style"><h4 class="header-popover-heading">Graphic Editor</h4></a>
                  <p class="header-popover-desc">Designing made easy with PhotoADKing&apos;s online graphic editor.</p>
                </div>
                <div class="col-4 m-0 pl-20">
                  <div class="text-center popover-image">
                    <a href="{!! $activation_link_path !!}/features/online-video-maker/"><img src="{!! $cloudfront_link_path !!}/resources/Feature_video_maker.png" width="160px" height="111px" alt="" ></a>
                  </div>
                  <a href="{!! $activation_link_path !!}/features/online-video-maker/"  class="remove-style"><h4 class="header-popover-heading">Video Maker</h4></a>
                  <p class="header-popover-desc">Easy to use short video maker for your business.</p>
                </div>
                <div class="col-4 m-0 pl-20">
                  <div class="text-right popover-image">
                  <a href="{!! $activation_link_path !!}/features/free-intro-maker/"><img src="{!! $cloudfront_link_path !!}/resources/Feature_Intro_editor.png" width="160px" height="111px" alt="" class=" text-right" >
                  </div>
                  <a href="{!! $activation_link_path !!}/features/free-intro-maker/" class="remove-style"><h4 class="header-popover-heading">Intro Editor</h4></a>
                  <p class="header-popover-desc">Customize intro templates with ease.</p>
                </div>
              </div>'>Features</a></li>
                                <li><a id="learn_lnk" href="https://blog.photoadking.com/" name="learn-btn"
                                       role="button"
                                       data-html="true" data-content='<div class="row m-0 p-8px mt-1 featurebtn">
              <div class="col m-0 pl-20">
                <a href="https://blog.photoadking.com/"><div class="popover-image"><img src="{!! $cloudfront_link_path !!}/resources/Learn_getting_started.png" width="160px" height="111px" alt="" ></div></a>
                <a href="https://blog.photoadking.com/" class="remove-style"><h4 class="header-popover-heading">Getting Started</h4></a>
                <p class="header-popover-desc">Get started with your next design ideas.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/ideas-and-inspirations/"><img src="{!! $cloudfront_link_path !!}/resources/Learn_design_inspiration.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/ideas-and-inspirations/"  class="remove-style"><h4 class="header-popover-heading">Design Inspiration</h4></a>
                <p class="header-popover-desc">Get ideas & inspiration for your next design project.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/video-marketing/"><img src="{!! $cloudfront_link_path !!}/resources/Learn_marketing_mania.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/video-marketing/"  class="remove-style"><h4 class="header-popover-heading">Marketing Mania</h4></a>
                <p class="header-popover-desc">Creative marketing ideas to boost your business.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/how-to-make/"><img src="{!! $cloudfront_link_path !!}/resources/Learn_tutorials.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/how-to-make/"  class="remove-style"><h4 class="header-popover-heading">Tutorials</h4></a>
                <p class="header-popover-desc">Step-by-step tutorial for designing from scratch in PhotoADKing.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-right popover-image">
                <a href="{!! $activation_link_path !!}/whats-new/"><img src="{!! $cloudfront_link_path !!}/resources/Learn_whats_new.png" width="160px" height="111px" alt="" class=" text-right" >
                </div>
                <a href="{!! $activation_link_path !!}/whats-new/"  class="remove-style"><h4 class="header-popover-heading">What&apos;s New</h4></a>
                <p class="header-popover-desc">It&apos;s a time to take advantage of our new updates.</p>
              </div>
            </div>'>Learn</a></li>
                                <li><a id="primium_lnk" href="{!! $activation_link_path !!}/go-premium/"
                                       name="pricing-btn" role="button" data-html="true"
                                       data-content='<div class="row m-0 p-8px mt-1 pricingbtn">
            <div class="col-6 m-0 pl-20">
              <a href="{!! $activation_link_path !!}/go-premium/"><div class="popover-image"><img src="{!! $cloudfront_link_path !!}/resources/Pricing_free.png" width="160px" height="111px" alt="" ></div></a>
              <a href="{!! $activation_link_path !!}/go-premium/" class="remove-style"><h4 class="header-popover-heading">Free</h4></a>
              <p class="header-popover-desc">Quickly create and download designs for any occasion. Use it as long as you want.</p>
            </div>
            <div class="col-6 m-0 pl-20">
              <div class="text-left popover-image">
                <a href="{!! $activation_link_path !!}/go-premium/" ><img src="{!! $cloudfront_link_path !!}/resources/Pricing_pro.png" width="160px" height="111px" alt="" ></a>
              </div>
              <a href="{!! $activation_link_path !!}/go-premium/" class="remove-style"><h4 class="header-popover-heading">Pro</h4></a>
              <p class="header-popover-desc">Easily create unlimited branded videos and get unlimited templates and more content.</p>
            </div>

          </div>'>Pricing</a></li>
                            </ul>

                        </div>

                        <div class="float-right l-menu align-items-center">
                            <ul class="l-menu-container mr-0">
                                <li class="mr-cust-15" id="hd-logn" target="_blank" name="login-btn"><a
                                            href="{!! $activation_link_path !!}/app/#/login">Log In</a>
                                </li>
                            </ul>
                            <a href="{!! $activation_link_path !!}/app/#/sign-up" target="_blank" id="rlp-link">
                                <button class="l-signup-btn my-1" name="signup-btn"
                                        id="rlp-btn-txt"><span>Sign Up Free</span></button>
                            </a>
                        </div>
                        <div class="float-right l-mob-menu" id="mob-menu">
                            <button class="l-transparent-button"> <span>
                  <svg class="l-sm-white" width="20" height="16" viewBox="0 0 20 16" fill="none"
                       xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M18.75 0.499969H1.25001C0.559624 0.499969 0 1.05964 0 1.74998C0 2.44033 0.559624 3 1.25001 3H18.75C19.4404 3 20 2.44033 20 1.74998C20 1.05964 19.4404 0.499969 18.75 0.499969Z"
                            fill="#151515"/>
                    <path
                            d="M18.75 6.74996H1.25001C0.559624 6.74996 0 7.30959 0 7.99993C0 8.69027 0.559624 9.24994 1.25001 9.24994H18.75C19.4404 9.24994 20 8.69031 20 7.99993C20 7.30954 19.4404 6.74996 18.75 6.74996Z"
                            fill="#151515"/>
                    <path
                            d="M18.75 13H1.25001C0.559624 13 0 13.5596 0 14.25C0 14.9404 0.559624 15.5 1.25001 15.5H18.75C19.4404 15.5 20 14.9404 20 14.25C20 13.5596 19.4404 13 18.75 13Z"
                            fill="#151515"/>
                  </svg>
                </span> <span> <svg class="l-sm-blue" width="20" height="16" viewBox="0 0 20 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M18.75 0.499969H1.25001C0.559624 0.499969 0 1.05964 0 1.74998C0 2.44033 0.559624 3 1.25001 3H18.75C19.4404 3 20 2.44033 20 1.74998C20 1.05964 19.4404 0.499969 18.75 0.499969Z"
                            fill="#151515"/>
                    <path
                            d="M18.75 6.74996H1.25001C0.559624 6.74996 0 7.30959 0 7.99993C0 8.69027 0.559624 9.24994 1.25001 9.24994H18.75C19.4404 9.24994 20 8.69031 20 7.99993C20 7.30954 19.4404 6.74996 18.75 6.74996Z"
                            fill="#151515"/>
                    <path
                            d="M18.75 13H1.25001C0.559624 13 0 13.5596 0 14.25C0 14.9404 0.559624 15.5 1.25001 15.5H18.75C19.4404 15.5 20 14.9404 20 14.25C20 13.5596 19.4404 13 18.75 13Z"
                            fill="#151515"/>
                  </svg> </span></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 l-min-md-pd p-0">
                <div class="header-content-padding sec-first-content-wrapper c-container-pedding">
                    <div class="s-header-content-container sec-first-heading-container">
                        <h1 class="f-heading font-72px"
                            spellcheck="false">{!! $json_data->header_section->heading !!}</h1>
                        <p class="s-sub-header c-font-size-subheading margin-0">{!! $json_data->header_section->subheading !!}</p>
                        <!-- 37 -->
                        <a href="{!! $json_data->header_section->button->href !!}" target="_blank" style="overflow: auto;"
                           id="hdrnavbtn"
                           class="btn sec-first-button m-bottom-30 c-bg-text" onclick="setUserkeyword('','','')">{!! $json_data->header_section->button->text !!}
                            <svg viewBox="0 0 22 20" class="sec-first-button-svg mb-1"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill="#0069FF"
                                      d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="slider-wrapper sm-hd" style="min-height: 320px;">
                <ul class="slider-container slider-container-cstm" id="slider">
                </ul>
            </div>
            <div class="review-wrapper pt-1 pb-1 c-custom-bg">
                <span class="desc">{!! $json_data->review_section->review !!}</span>
                <br>
                <span class="Author whitespace-nowrap d-inline-block" style="overflow: auto;">
                    {!! $json_data->review_section->review_by !!}
                 </span>
                <span class="Author whitespace-nowrap d-inline-block"
                      style="overflow: auto;">{!! $json_data->review_section->rating->text !!}</span>
                @if($json_data->review_section->rating->is_star_symbol_applied)
                    <span class="d-inline-block" style="vertical-align: super;">
                    @php $floor_rating = floor($json_data->review_section->rating->rating_recieved); @endphp
                    @if($floor_rating != $json_data->review_section->rating->rating_recieved)
                        @for ($i = 1; $i <= $json_data->review_section->rating->total_rating; $i++)

                            @if($i <= $floor_rating)
                                <!-- Filled SVG -->
                                    <svg width="21" height="21" viewBox="0 0 25 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_590_9774{!! $i !!})">
                              <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z"
                                    fill="#FFD923"></path>
                            </g>
                            <defs>
                              <filter id="filter0_d_590_9774{!! $i !!}" x="0.589722" y="0.236328" width="23.8206"
                                      height="23.1445"
                                      filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                               values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                               result="hardAlpha"></feColorMatrix>
                                <feOffset></feOffset>
                                <feGaussianBlur stdDeviation="2"></feGaussianBlur>
                                <feComposite in2="hardAlpha" operator="out"></feComposite>
                                <feColorMatrix type="matrix"
                                               values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0"></feColorMatrix>
                                <feBlend mode="normal" in2="BackgroundImageFix"
                                         result="effect1_dropShadow_590_9774"></feBlend>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774"
                                         result="shape"></feBlend>
                              </filter>
                            </defs>
                          </svg>
                            @elseif($i-1 == $floor_rating)
                                <!-- Half Filled SVG -->
                                    <svg width="21" height="21" viewBox="0 0 25 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_d_4482_4765{!! $i !!})">
                                    <path d="M12.9755 5.08156L14.1574 8.71885C14.3582 9.33688 14.9341 9.75532 15.5839 9.75532H19.4084C19.8928 9.75532 20.0942 10.3751 19.7023 10.6598L16.6082 12.9078C16.0825 13.2898 15.8625 13.9668 16.0633 14.5848L17.2452 18.2221C17.3948 18.6828 16.8676 19.0659 16.4757 18.7812L13.3817 16.5332C12.8559 16.1512 12.1441 16.1512 11.6183 16.5332L8.52426 18.7812C8.1324 19.0659 7.60516 18.6828 7.75484 18.2221L8.93667 14.5848C9.13748 13.9668 8.91749 13.2898 8.39176 12.9078L5.2977 10.6598C4.90584 10.3751 5.10723 9.75532 5.59159 9.75532H9.41606C10.0659 9.75532 10.6418 9.33688 10.8426 8.71885L12.0245 5.08156C12.1741 4.6209 12.8259 4.62091 12.9755 5.08156Z"
                                          stroke="#FFD923" shape-rendering="crispEdges"/>
                                    </g>
                                    <g clip-path="url(#clip0_4482_4765)">
                                    <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.683 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.567 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z"
                                          fill="#FFD923"/>
                                    </g>
                                    <defs>
                                    <filter id="filter0_d_4482_4765{!! $i !!}" x="0.589844" y="0.236084" width="23.8203"
                                            height="23.145"
                                            filterUnits="userSpaceOnUse"
                                            color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                   values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                   result="hardAlpha"/>
                                    <feOffset/>
                                    <feGaussianBlur stdDeviation="2"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                             result="effect1_dropShadow_4482_4765"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4482_4765"
                                             result="shape"/>
                                    </filter>
                                    <clipPath id="clip0_4482_4765">
                                    <rect width="11" height="21" fill="white" transform="translate(2 2)"/>
                                    </clipPath>
                                    </defs>
                                    </svg>
                            @else
                                <!-- Blank SVG -->
                                    <svg width="21" height="21" viewBox="0 0 25 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_d_4482_4766{!! $i !!})">
                                    <path d="M12.0245 5.08156C12.1741 4.6209 12.8259 4.62091 12.9755 5.08156L14.1574 8.71885C14.3582 9.33688 14.9341 9.75532 15.5839 9.75532H19.4084C19.8928 9.75532 20.0942 10.3751 19.7023 10.6598L16.6082 12.9078C16.0825 13.2898 15.8625 13.9668 16.0633 14.5848L17.2452 18.2221C17.3948 18.6828 16.8676 19.0659 16.4757 18.7812L13.3817 16.5332C12.8559 16.1512 12.1441 16.1512 11.6183 16.5332L8.52426 18.7812C8.1324 19.0659 7.60516 18.6828 7.75484 18.2221L8.93667 14.5848C9.13748 13.9668 8.91749 13.2898 8.39176 12.9078L5.2977 10.6598C4.90584 10.3751 5.10723 9.75532 5.59159 9.75532H9.41606C10.0659 9.75532 10.6418 9.33688 10.8426 8.71885L12.0245 5.08156Z"
                                          stroke="#FFD923" shape-rendering="crispEdges"/>
                                    </g>
                                    <defs>
                                    <filter id="filter0_d_4482_4766{!! $i !!}" x="0.589844" y="0.236084" width="23.8203"
                                            height="23.145" filterUnits="userSpaceOnUse"
                                            color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                   values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                   result="hardAlpha"/>
                                    <feOffset/>
                                    <feGaussianBlur stdDeviation="2"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                             result="effect1_dropShadow_4482_4766"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4482_4766"
                                             result="shape"/>
                                    </filter>
                                    </defs>
                                    </svg>
                            @endif
                        @endfor
                    @else
                        @for ($i = 1; $i <= $json_data->review_section->rating->total_rating; $i++)
                            @if($i <= $json_data->review_section->rating->rating_recieved)
                                <!-- Filled SVG -->
                                    <svg width="21" height="21" viewBox="0 0 25 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                        <g filter="url(#filter0_d_590_9774{!! $i !!})">
                          <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z"
                                fill="#FFD923"></path>
                        </g>
                        <defs>
                          <filter id="filter0_d_590_9774{!! $i !!}" x="0.589722" y="0.236328" width="23.8206"
                                  height="23.1445"
                                  filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                           values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                           result="hardAlpha"></feColorMatrix>
                            <feOffset></feOffset>
                            <feGaussianBlur stdDeviation="2"></feGaussianBlur>
                            <feComposite in2="hardAlpha" operator="out"></feComposite>
                            <feColorMatrix type="matrix"
                                           values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0"></feColorMatrix>
                            <feBlend mode="normal" in2="BackgroundImageFix"
                                     result="effect1_dropShadow_590_9774"></feBlend>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774"
                                     result="shape"></feBlend>
                          </filter>
                        </defs>
                      </svg>
                            @else
                                <!-- Blank SVG -->
                                    <svg width="21" height="21" viewBox="0 0 25 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                <g filter="url(#filter0_d_4482_4766{!! $i !!})">
                                <path d="M12.0245 5.08156C12.1741 4.6209 12.8259 4.62091 12.9755 5.08156L14.1574 8.71885C14.3582 9.33688 14.9341 9.75532 15.5839 9.75532H19.4084C19.8928 9.75532 20.0942 10.3751 19.7023 10.6598L16.6082 12.9078C16.0825 13.2898 15.8625 13.9668 16.0633 14.5848L17.2452 18.2221C17.3948 18.6828 16.8676 19.0659 16.4757 18.7812L13.3817 16.5332C12.8559 16.1512 12.1441 16.1512 11.6183 16.5332L8.52426 18.7812C8.1324 19.0659 7.60516 18.6828 7.75484 18.2221L8.93667 14.5848C9.13748 13.9668 8.91749 13.2898 8.39176 12.9078L5.2977 10.6598C4.90584 10.3751 5.10723 9.75532 5.59159 9.75532H9.41606C10.0659 9.75532 10.6418 9.33688 10.8426 8.71885L12.0245 5.08156Z"
                                      stroke="#FFD923" shape-rendering="crispEdges"/>
                                </g>
                                <defs>
                                <filter id="filter0_d_4482_4766{!! $i !!}" x="0.589844" y="0.236084" width="23.8203"
                                        height="23.145" filterUnits="userSpaceOnUse"
                                        color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                               values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset/>
                                <feGaussianBlur stdDeviation="2"/>
                                <feComposite in2="hardAlpha" operator="out"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0"/>
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_4482_4766"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4482_4766"
                                         result="shape"/>
                                </filter>
                                </defs>
                                </svg>
                                @endif
                            @endfor
                        @endif

                    </span>
                @endif

            </div>
        </div>
    </div>
    <div class="row no-gutters pb-4 bg-white">
        <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 pb-5 m-auto w-100 collection-container text-center px-3 pt-0 px-3 px-sm-0">

            <h2 class="c-section-main-heading pt-0 pt-sm-3" spellcheck="false">
                {!! $json_data->intro_section->heading !!}</h2>

            <p class="sub-heading mb-5 sec-three-content gray-text"
               spellcheck="false">{!! $json_data->intro_section->subheading !!}<br></p>

            <div class="text-center">
                @if(isset($json_data->intro_section->video) && $json_data->intro_section->video)
                    <video autoplay muted loop playsinline class="video-player-css s-video" loading="lazy" preload="none"
                           poster="{!! $json_data->intro_section->webp_image !!}"
                           onerror=" this.poster='{!! $json_data->intro_section->image !!}'"
                           controlsList="nodownload">
                        <source src="{!! $json_data->intro_section->video !!}"
                                type="video/mp4"/>
                        Your browser does not support the video.
                    </video>
                @else
                    <img src="{!! $json_data->intro_section->webp_image !!}"
                         onerror=" this.src='{!! $json_data->intro_section->image !!}'"
                         class="video-player-css s-video dp-lrg-imge" loading="lazy"nbvc alt="{!! $json_data->intro_section->alt!!}">
                @endif
                <div class="text-center mt-5">
                    <a href="{!! $json_data->intro_section->button->href !!}"
                    target="_blank"   class="Click to sign-up and get started" id="try_now_btn" onclick="setUserkeyword('','','')"
                       title="Click to try now!">
                        <button class="c-cta-btn">
                            <span>{!! $json_data->intro_section->button->text !!}</span>
                            <svg viewBox="0 0 22 20" class="sec-first-button-svg"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill="#ffffff"
                                      d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                                </path>
                            </svg>
                        </button>
                    </a>
                </div>

                <div class="mt-5 position-relative">
                  <div class="feature-name"><span class="bg-white position-relative px-3">FEATURED ON</span></div>
                  <div class="slider-show">
                    <div class="d-flex" id="ttcw1" style="overflow-x: scroll;align-items: center;">
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4" style="display:block !important;">
                        <!-- <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo1.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo1.png" alt="G2 logo" loading="lazy">
                        </picture> -->
                        <svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_7765_12085)">
                            <path d="M22.5 45C34.9264 45 45 34.9264 45 22.5C45 10.0736 34.9264 0 22.5 0C10.0736 0 0 10.0736 0 22.5C0 34.9264 10.0736 45 22.5 45Z" fill="#898D9A" />
                            <path d="M32.25 17.2499H28.425C28.5 16.6499 28.875 16.2749 29.625 15.8999L30.3 15.5249C31.575 14.9249 32.25 14.1749 32.25 12.9749C32.2589 12.6319 32.1891 12.2913 32.0461 11.9793C31.9032 11.6673 31.6907 11.3922 31.425 11.1749C30.7982 10.7751 30.0684 10.5666 29.325 10.5749C28.7205 10.5712 28.1256 10.7264 27.6 11.0249C27.1085 11.3535 26.6982 11.7895 26.4 12.2999L27.525 13.4249C27.6828 13.0553 27.9433 12.7386 28.2756 12.5126C28.608 12.2866 28.9982 12.1608 29.4 12.1499C30.15 12.1499 30.525 12.5249 30.525 12.9749C30.525 13.4249 30.3 13.7999 29.55 14.1749L29.1 14.3999C28.2548 14.7712 27.5283 15.368 27 16.1249C26.5834 16.8547 26.3758 17.685 26.4 18.5249V18.8249H32.25V17.2499Z" fill="white" />
                            <path d="M31.725 20.7H25.35L22.2 26.175H28.575L31.725 31.65L34.875 26.175L31.725 20.7Z" fill="white" />
                            <path d="M22.725 29.8499C20.7757 29.8499 18.9062 29.0755 17.5278 27.6971C16.1494 26.3187 15.375 24.4492 15.375 22.4999C15.375 20.5505 16.1494 18.681 17.5278 17.3027C18.9062 15.9243 20.7757 15.1499 22.725 15.1499L25.275 9.89989C24.4336 9.74731 23.5801 9.67199 22.725 9.67488C19.3236 9.67488 16.0615 11.0261 13.6563 13.4312C11.2512 15.8364 9.89999 19.0985 9.89999 22.4999C9.89999 25.9013 11.2512 29.1634 13.6563 31.5685C16.0615 33.9737 19.3236 35.3249 22.725 35.3249C25.4389 35.3368 28.0881 34.4974 30.3 32.9249L27.525 28.0499C26.1964 29.2124 24.4904 29.8522 22.725 29.8499V29.8499Z" fill="white" />
                          </g>
                          <defs>
                            <clipPath id="clip0_7765_12085">
                              <rect width="45" height="45" fill="white" />
                            </clipPath>
                          </defs>
                        </svg>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo2.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo2.png" alt="colibri logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo3.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo3.png" alt="Filmora logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo4.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo4.png" alt="GEEKFLARE logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo5.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo5.png" alt="engagebay logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo6.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo6.png" alt="twine logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo7.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo7.png" alt="FOUNDERJAR logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo8.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo8.png" alt="starter story logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo9.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo9.png" alt="unthinkble.fm logo" loading="lazy">
                        </picture>
                      </div>
                      <div class="col-lg-2 col-md-3 col-sm-3 col-4">
                      <picture>
                          <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo10.webp" type="image/webp">
                          <img class="c-img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/logo10.png" alt="techviral logo" loading="lazy">
                        </picture>
                      </div>
                    </div>
                    <button style="display: none !important;" class="c-slick-prev button-back" id="ttcbleft" aria-label="photoadking" onclick="scrollLeftSlider()" style="display: inline-block;"></button>
                    <button class="c-slick-next button-back" id="ttcbright" aria-label="photoadking" onclick="scrollRightSlider()"></button>
                  </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row mx-0 bkg-white">
        <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container text-center pb-0 pt-3 px-3 px-sm-0">
            <h2 class="c-section-main-heading"
                spellcheck="false">{!! $json_data->template_section->heading !!}</h2>
            <p class="sub-heading sec-four-content mb-5"
               spellcheck="false">{!! $json_data->template_section->subheading !!}</p>

            <div class="card-wrapper col-four col-three-md col-two-sm" id="card-wrapper">
                <!-- keep for card fluid added using javascript -->
            </div>
            <div class="see-more-btn-wrapper"><a href="{!! $json_data->template_section->button->href !!}" target="_blank"
                                                 class="m-auto c-background  see-more-btn d-inline-block sec-four-button" onclick="setUserkeyword('','','')">
                    {!! $json_data->template_section->button->text !!}

                    <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                              fill="white"></path>
                    </svg>
                </a>

            </div>


        </div>

    </div>
    <div class="row mx-0 sec-three-wrapper bg-white">
        <div class="row col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 pt-0 collection-container px-3 px-sm-0 pt-0 pt-sm-2">
            <h2 class="c-section-main-heading text-center w-100">{!! $json_data->key_feature_section->heading !!}</h2>
            <p class="sec-four-static-content w-100 gray-text px-2 px-lg-0">{!! $json_data->key_feature_section->subheading !!}</p>

            @foreach($json_data->key_feature_section->data as $i => $data)

                @if($i == 0)
                    <div class="col-lg-6 col-md-12 p-0 pl-lg-2 pr-lg-5 m-0 pt-lg-5 pb-lg-4 text-center text-lg-left">
                        <img loading="lazy" src="{!! $data->webp_image !!}"
                             onerror=" this.src='{!! $data->image !!}'"
                             class="w-100img h-auto" alt="{!! $data->alt !!}" height="100px" width="100px">
                    </div>
                    <div class="col-lg-6 col-md-12 m-auto pt-3 pb-5 sm-pb-3 px-0 text-center text-lg-left pl-lg-5 pr-lg-3 py-lg-0">
                        <h2 class="pb-3 pb-lg-4 sec-three-font-style text-blck c-heading-font">{!! $data->heading !!}
                        </h2>
                        <p class="sec-four-static-content gray-text pb-3 pb-lg-4">{!! $data->subheading !!}</p>
                        <a href="{!! $data->button->href !!}" target="_blank"
                           onclick="categoryRedirect('{!! $json_data->slider_template_section[0]->sub_category_id !!}')"
                           class="m-auto  see-more-btn d-inline-block sec-four-button c-background">{!! $data->button->text !!}

                            <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                                      fill="white"></path>
                            </svg>
                        </a>
                    </div>

                @elseif($i % 2 != 0)
                    <div class="col-lg-6 col-md-12 px-0 pt-0 pl-lg-4 pr-lg-2 m-0 py-lg-4 text-center text-lg-right d-block d-lg-none">
                        <img loading="lazy" src="{!! $data->webp_image !!}"
                             onerror=" this.src='{!! $data->image !!}'"
                             class="w-100img pl-2 h-auto" alt="{!! $data->alt !!}" height="100px" width="100px">
                    </div>
                    <div class="col-lg-6 col-md-12 m-auto text-center text-lg-left px-0 pt-3 pb-5 sm-pb-3 py-lg-0 pl-lg-2 pr-lg-5">
                        <h2 class="pb-3 pb-lg-4 sec-three-font-style text-black c-heading-font">{!! $data->heading !!}</h2>
                        <p class="sec-four-static-content gray-text pb-3 pb-lg-4">{!! $data->subheading !!}</p>
                        <a href="{!! $data->button->href !!}" target="_blank"
                           onclick="categoryRedirect('{!! $json_data->slider_template_section[0]->sub_category_id !!}')"
                           class="m-auto  see-more-btn d-inline-block sec-four-button c-background">
                            {!! $data->button->text !!}
                            <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                                      fill="white"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-12 px-0 pt-0 pb-4 pl-lg-4 pr-lg-2 m-0 py-lg-4 text-center text-lg-right d-none d-lg-block">
                        <img loading="lazy" src="{!! $data->webp_image !!}"
                             onerror=" this.src='{!! $data->image !!}'"
                             class="w-100img pl-2 h-auto" alt="{!! $data->alt !!}" height="100px" width="100px">
                    </div>

                @elseif($i % 2 == 0)
                    <div class="col-lg-6 col-md-12 px-0 pb-4 pt-0 pl-lg-2 pr-lg-5 m-0 pt-lg-4 pb-lg-5 text-center text-lg-left">
                        <img loading="lazy" src="{!! $data->webp_image !!}"
                             onerror=" this.src='{!! $data->image !!}'"
                             class="w-100img h-auto" alt="{!! $data->alt !!}" height="100px" width="100px">
                    </div>
                    <div class="col-lg-6 col-md-12 px-0 pt-0 pb-4 m-auto text-center text-lg-left pl-lg-5 pr-lg-3 py-lg-0">
                        <h2 class="pb-3 pb-lg-4 sec-three-font-style text-blck c-heading-font">{!! $data->heading !!}
                        </h2>
                        <p class="sec-four-static-content gray-text pb-3 pb-lg-4">{!! $data->subheading !!}</p>
                        <a href="{!! $data->button->href !!}" target="_blank"
                           onclick="categoryRedirect('{!! $json_data->slider_template_section[0]->sub_category_id !!}')"
                           class="m-auto  see-more-btn d-inline-block sec-four-button c-background">
                            {!! $data->button->text !!}

                            <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                                      fill="white"></path>
                            </svg>
                        </a>
                    </div>

                @endif

            @endforeach

        </div>

    </div>
    <div class="row mx-0 how-to-sec-bg pb-3">
        <div class="col-12 col-lg-9 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container pb-0">
            <h2 class="heading my-4 pb-1 sec-three-heading new-sec-three-heading  sec-three-special"> {!! $json_data->how_to_section->heading !!} </h2>
            <!-- below is two column list and display in large screen -->

            <div class="col-12 justify-content-center d-flex mx-0 pb-5" style="flex-wrap: wrap;">

                @foreach($json_data->how_to_section->data as $i => $how_to_data)
                    <div class="w-47p how-to-step-wrapper m-3">
                        <div class="">
                            <div class="position-relative d-flex">
                              <div style="margin-top: 12px !important;" class="how-to-step-number extra-back" >{!! $i+1 !!}</div>
                                <h3 class="how-to-step-title step-title ml-lg-3">{!! $how_to_data->title !!}</h3>
                            </div>
                            <p class="how-to-step-content step-content c-line-height ml-4 pl-4">{!! $how_to_data->content !!}</p>
                        </div>
                    </div>
                @endforeach

            </div>
            <!-- below list is one column list and display in smalll devices -->

            <div class="see-more-btn-wrapper sec-five-btn-wrapper">

                <a href="{!! $json_data->how_to_section->button->href !!}" target="_blank"
                   class="m-auto  see-more-btn d-inline-block sec-four-button c-background" onclick="setUserkeyword('','','')">
                    {!! $json_data->how_to_section->button->text !!}
                    <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                              fill="white"></path>
                    </svg>
                </a>

            </div>
        </div>
    </div>
    @if(isset($json_data->benefit_section) && isset($json_data->benefit_section->data))
      <div class="">
        <div class="row mx-0 pb-3 mb-5 mt-5">
          <div class="meggi-girls d-flex">
            <!-- <img class="img-fluid" src="http://192.168.0.108/photoadking/images/design-images/meggi.png" alt=""> -->
            <picture>
              <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/usage_model.webp" type="image/webp">
              <img class="img-fluid" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/resources/usage_model.png" loading="lazy" alt="An image showing more PhotoADKing features">
            </picture>
          </div>
          <div class="girls-side">
            @foreach($json_data->benefit_section->data as $i => $benefit_data)
              <h2 class="how-to-step-title step-title ml-lg-3">{!! $benefit_data->heading !!}</h2>
              @foreach($benefit_data->subheading as $i => $subheading)
                <p class="how-to-step-content step-content c-line-height pl-lg-3 pb-2">{!! $subheading->content !!}</p>
              @endforeach
            @endforeach
          </div>
        </div>
      </div>
    @endif

    <div class="row l-blue-bg w-100 m-0 c-background">
        <div class="col-12 col-xl-10 col-lg-12 col-md-12 col-sm-12 w-100 ml-auto collection-container py-0 pr-0 c-flyer-maker-teb pl-0">

            <div class="intrnl-btn-block">

                <h2 class="heading color-white intrnl-heading py-3 py-sm-0 py c-flyer-maker-font c-line-height-extra"
                    spellcheck="false">{!! $json_data->tag_section->heading !!}</h2>

                @foreach($json_data->tag_section->data as $i => $tag_data)
                    <a class="btn btn-default btm-link-btns" href="{!! $tag_data->href !!}">{!! $tag_data->text !!}</a>
                @endforeach

            </div>

            <div class="intrnl-img-block c-custom-imgset"
                 style="overflow: hidden;">
            <!-- style="background-image: url('{!! $activation_link_path !!}images/mask_group.png');height: match;" -->
            <img src="{!! $json_data->tag_section->bg_image->webp_image !!}" style="max-width: fit-content;" loading="lazy"
                     alt="{!! $json_data->tag_section->bg_image->alt !!}"
                     onerror="this.src='{!! $json_data->tag_section->bg_image->image !!}'">
            </div>

        </div>
    </div>

    <section class="loved-by-section bg-white">
  <div class="text-center">
    <h2 class="c-section-main-heading">Loved by the people around the world</h2>
  </div>
  <div class="loved-section-container w-100 mob-loved-review pb-5">
    <div class="loved-review-container w-100">
      <div class="loved-review-section-1">
        <div class="loved-review-box">
          <a target="_blank" href="https://www.capterra.com/p/187414/PhotoADKing/reviews/2827158/" rel="noreferrer nofollow">
            <div class="user-review-box">
              <div class="c-rev-div d-flex">
                <div class="c-review-top-quote">
                  <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                    <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                  </svg>
                </div>
                <p class="c-review-text"> This App has helped me to create amazing and beautiful designs for my online business and colleagues as well which has also given job opportunities as well as it's ... </p>
              </div>
              <div class="c-rev-div d-flex">
                <div class="d-flex w-65">
                  <div class="review-user-avtar">
                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_first.png"  alt="Emmanuel R. profile image" loading="lazy">
                  </div>
                  <div class="review-user-details">
                    <p>Emmanuel R.</p>
                    <p>Business Owner</p>
                    <p>(USA)</p>
                  </div>
                </div>
                <div class="w-35 position-relative c-rating-box">
                  <div class="d-block text-right">
                    <svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 11.2335L12.6714 11.236L20.3765 11.2374V3.604L0 11.2335Z" fill="#FF9D28"></path>
                      <path d="M20.3765 3.60446V30.5541L29.9999 0L20.3765 3.60446Z" fill="#68C5ED"></path>
                      <path d="M20.3765 11.2376L12.6714 11.2363L20.3765 30.5539V11.2376Z" fill="#044D80"></path>
                      <path d="M0 11.2339L14.6473 16.193L12.6714 11.2364L0 11.2339Z" fill="#E54747"></path>
                    </svg>
                  </div>
                  <div class="d-block text-right">
                    <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                      <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                      <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                      <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                      <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
        <div class="loved-review-box">
          <div class="user-review-box r-box-diff">
            <div class="review-feather">
              <svg viewBox="0 0 123 173" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M49.7327 160.198C56.4853 166.367 69.584 173.239 78.9854 168.496C71.7022 161.605 59.5402 157.828 49.7327 160.198Z" fill="#B6B6B6"></path>
                <path d="M39.0543 144.407C39.4653 144.906 39.8763 145.405 40.2873 145.905C40.4329 146.043 40.4928 146.154 40.6384 146.292C40.7841 146.43 40.8439 146.542 40.9896 146.68C41.1951 146.929 41.4864 147.205 41.6919 147.455C42.1888 147.98 42.7116 148.419 43.2084 148.945C43.4998 149.22 43.7312 149.384 44.0226 149.66C44.3139 149.936 44.5454 150.099 44.8367 150.375C45.3596 150.814 45.9682 151.28 46.517 151.634C47.0399 152.073 47.6745 152.453 48.2233 152.806C49.4068 153.54 50.6163 154.187 51.8258 154.835C54.2967 155.958 56.7598 156.798 59.2072 157.07C60.375 157.237 61.5948 157.231 62.7288 157.2C63.8888 157.083 64.9889 156.854 65.9433 156.487C65.5922 156.099 65.3269 155.738 65.0355 155.462C64.7702 155.101 64.4789 154.825 64.1875 154.549C63.7167 153.938 63.134 153.387 62.6112 152.948C62.3199 152.672 62.0026 152.482 61.7112 152.206C61.4199 151.931 61.0427 151.629 60.7254 151.439C60.0309 150.948 59.1908 150.319 58.0931 149.611C57.5443 149.258 56.8238 148.852 56.0773 148.532C55.76 148.342 55.3309 148.212 54.9017 148.082C54.4725 147.952 54.0694 147.737 53.6402 147.607C52.7819 147.347 51.9496 147.001 50.9795 146.801C50.5503 146.671 50.0353 146.515 49.5802 146.471L48.8935 146.263L48.2069 146.055C46.3784 145.594 44.524 145.22 42.901 145.01C41.4679 144.482 40.0166 144.323 39.0543 144.407Z" fill="#B6B6B6"></path>
                <path d="M30.74 127.927C31.3384 129.045 32.0225 130.189 32.8185 131.274C33.6145 132.358 34.4105 133.443 35.3183 134.467C35.8152 134.993 36.2262 135.492 36.749 135.931C36.9545 136.181 37.2458 136.457 37.4773 136.62C37.6828 136.87 38.0001 137.06 38.2056 137.31C38.7285 137.749 39.2513 138.189 39.8001 138.542C40.0316 138.706 40.3229 138.982 40.5544 139.145L40.9316 139.447L41.3088 139.749C42.4064 140.456 43.5301 141.078 44.6797 141.613C45.2545 141.881 45.8293 142.149 46.3443 142.305C46.9451 142.487 47.4601 142.643 48.0609 142.825C48.5759 142.981 49.2027 143.077 49.7177 143.233C49.9752 143.311 50.2587 143.303 50.5162 143.381C50.7997 143.373 51.0572 143.451 51.3407 143.443C52.4486 143.498 53.4109 143.414 54.3992 143.245C54.1339 142.884 53.9804 142.462 53.6891 142.187C53.5096 141.851 53.2443 141.49 53.0388 141.24C52.5679 140.629 52.1829 140.044 51.7121 139.433C51.4208 139.157 51.2413 138.822 50.9499 138.546C50.6586 138.27 50.3933 137.909 50.1019 137.633C49.4595 136.97 48.7051 136.367 47.8311 135.54C46.8973 134.601 45.5422 133.816 44.0154 132.978C43.2949 132.573 42.4886 132.141 41.6563 131.795C41.2531 131.58 40.8239 131.45 40.4208 131.234C40.0176 131.018 39.5885 130.888 39.1853 130.672C37.5207 129.981 35.8301 129.375 34.3709 128.933C32.9378 128.405 31.6503 128.015 30.74 127.927Z" fill="#B6B6B6"></path>
                <path d="M25.1958 110.036C25.5965 111.188 26.083 112.366 26.6553 113.571C27.2277 114.775 27.826 115.893 28.5362 116.951C29.2464 118.01 30.0424 119.094 30.8643 120.093C31.0698 120.343 31.2753 120.592 31.4808 120.842C31.6863 121.092 31.8918 121.341 32.1233 121.505C32.5343 122.004 32.9713 122.418 33.4942 122.857C33.6996 123.107 33.9311 123.271 34.1626 123.435C34.3941 123.598 34.5996 123.848 34.917 124.038C35.3799 124.366 35.9028 124.805 36.3658 125.133C36.8288 125.46 37.2918 125.788 37.8666 126.056C38.3556 126.298 38.8446 126.539 39.3335 126.781C41.3155 127.663 43.2298 128.149 45.1284 128.068C44.9749 127.647 44.7954 127.311 44.7017 127.002C44.5222 126.666 44.4545 126.271 44.275 125.935C43.916 125.264 43.6429 124.619 43.172 124.008C42.813 123.337 42.3422 122.726 41.8115 122.003C41.5462 121.642 41.2809 121.28 40.9297 120.893C40.6644 120.531 40.3392 120.058 39.988 119.67C39.577 119.171 39.166 118.672 38.5574 118.206C38.0606 117.681 37.4519 117.216 36.8432 116.75C36.2346 116.285 35.5401 115.793 34.8456 115.302C34.1511 114.81 33.4307 114.405 32.7362 113.913C32.0157 113.507 31.3212 113.016 30.6007 112.61C29.8802 112.205 29.2456 111.825 28.5849 111.531C27.2896 110.858 26.0541 110.296 25.1958 110.036Z" fill="#B6B6B6"></path>
                <path d="M25.3031 72.8676C25.233 73.4085 25.2226 74.0614 25.2383 74.6283C25.254 75.1953 25.2437 75.8481 25.2594 76.4151C25.275 76.9821 25.3765 77.5751 25.478 78.1681C25.5796 78.761 25.6811 79.354 25.7826 79.947C25.8841 80.54 26.0116 81.0471 26.1989 81.6661C26.3264 82.1733 26.5138 82.7922 26.7271 83.3254C26.9404 83.8585 27.0679 84.3657 27.2813 84.8988C27.4946 85.432 27.734 85.8793 27.9473 86.4124C28.4 87.3929 28.9645 88.3135 29.555 89.1483C30.1455 89.9831 30.762 90.7321 31.4044 91.3952C32.6894 92.7215 34.0784 93.7044 35.5375 94.1464C35.5739 93.4077 35.5842 92.7549 35.6205 92.0163C35.6308 91.3635 35.5813 90.5988 35.5058 89.92C35.4563 89.1554 35.3808 88.4765 35.1596 87.6599C35.0242 86.8693 34.8291 85.9668 34.5481 85.0383C34.4544 84.7288 34.3347 84.5052 34.2411 84.1957C34.1474 83.8862 34.0277 83.6626 33.8482 83.3271C33.549 82.7679 33.2499 82.2088 32.9507 81.6496C32.5657 81.0645 32.1807 80.4794 31.7957 79.8942C31.4107 79.3091 30.9139 78.7838 30.5289 78.1986C30.3234 77.949 30.0321 77.6733 29.8526 77.3378C29.6471 77.0882 29.4416 76.8385 29.1502 76.5629C28.7392 76.0636 28.2424 75.5383 27.8054 75.1248C27.3684 74.7113 26.8716 74.186 26.4944 73.8843C26.0574 73.4709 25.6803 73.1692 25.3031 72.8676Z" fill="#B6B6B6"></path>
                <path d="M72.6208 142.486C76.7464 148.046 81.4893 160.54 79.0716 168.522C70.7637 162.539 66.7986 151.499 72.6208 142.486Z" fill="#B6B6B6"></path>
                <path d="M61.8105 131.153C62.0498 131.6 62.2033 132.021 62.4427 132.469C62.5025 132.581 62.5623 132.692 62.6222 132.804L62.8017 133.14C62.9213 133.363 63.041 133.587 63.1347 133.897C63.374 134.344 63.5015 134.851 63.7149 135.384C63.8345 135.608 63.9282 135.917 63.962 136.115C64.0817 136.339 64.1754 136.648 64.295 136.872C64.5084 137.405 64.6359 137.912 64.8492 138.445C65.0625 138.978 65.164 139.571 65.3774 140.104C65.7182 141.145 66.0331 142.271 66.2361 143.457C66.7799 145.683 67.0403 147.917 67.129 150.099C67.1604 151.233 67.1319 152.255 66.9058 153.311C66.7655 154.393 66.4795 155.337 66.1675 156.367C65.7904 156.066 65.4132 155.764 65.1219 155.488C64.8305 155.213 64.4534 154.911 64.162 154.635C63.5794 154.084 63.0826 153.559 62.5857 153.034C62.2944 152.758 62.1149 152.422 61.8496 152.061C61.5842 151.699 61.4047 151.364 61.1394 151.003C60.7206 150.22 60.2159 149.411 59.7034 148.319C59.4302 147.674 59.2429 147.055 59.0217 146.238C58.954 145.843 58.8864 145.447 58.8187 145.052C58.751 144.657 58.6833 144.261 58.7275 143.806C58.704 142.956 58.6805 142.105 58.7428 141.281C58.7869 140.826 58.8051 140.456 58.8493 140.001C58.9012 139.83 58.8934 139.546 58.9454 139.374C58.9974 139.203 58.9896 138.919 59.0416 138.748C59.3638 137.065 59.8318 135.52 60.3336 134.173C60.6377 132.859 61.2332 131.821 61.8105 131.153Z" fill="#B6B6B6"></path>
                <path d="M54.4895 118.816C54.7705 119.744 55.0515 120.673 55.3065 121.687C55.5616 122.701 55.7307 123.689 55.9857 124.704C56.1133 125.211 56.1289 125.778 56.2564 126.285C56.2643 126.568 56.2981 126.766 56.3918 127.076C56.3996 127.359 56.5193 127.583 56.5271 127.866C56.5688 128.347 56.6703 128.94 56.686 129.507C56.6938 129.791 56.7017 130.074 56.7095 130.358L56.7772 130.753L56.8448 131.148C56.9022 132.197 56.9336 133.331 56.9051 134.353C56.8349 134.894 56.8506 135.461 56.8064 135.916C56.7363 136.457 56.6921 136.912 56.622 137.453C56.5518 137.994 56.3958 138.509 56.3517 138.964C56.2737 139.222 56.1957 139.479 56.1177 139.737C56.0397 139.994 55.9617 140.252 55.9097 140.423C55.6238 141.367 55.252 142.286 54.7085 143.152C54.4172 142.876 54.066 142.489 53.7747 142.213C53.4833 141.937 53.218 141.576 53.0125 141.326C52.5417 140.715 52.1567 140.13 51.7118 139.433C51.5323 139.097 51.3528 138.762 51.1733 138.426C50.9938 138.091 50.8403 137.67 50.6868 137.248C50.3798 136.406 50.0728 135.563 49.758 134.437C49.4691 133.225 49.3701 131.696 49.5546 130.159C49.6169 129.334 49.8249 128.647 49.973 127.849C50.077 127.506 50.207 127.076 50.311 126.733C50.363 126.561 50.415 126.39 50.493 126.132C50.5449 125.961 50.6229 125.703 50.6749 125.531C51.2287 124.012 51.9282 122.631 52.6615 121.448C53.0853 120.358 53.8524 119.372 54.4895 118.816Z" fill="#B6B6B6"></path>
                <path d="M49.2605 105.144C49.4557 106.046 49.5391 107.008 49.5964 108.057C49.6798 109.019 49.7371 110.067 49.7086 111.089C49.6802 112.111 49.6517 113.133 49.6232 114.156C49.631 114.439 49.553 114.697 49.5869 114.894C49.5947 115.178 49.5167 115.435 49.5246 115.719C49.4544 116.26 49.4103 116.715 49.3401 117.256C49.2699 117.797 49.114 118.312 49.0698 118.767C48.9997 119.308 48.8697 119.737 48.7137 120.252C48.5577 120.767 48.5135 121.222 48.3576 121.737C48.2016 122.252 48.0716 122.681 47.9416 123.11C47.2838 124.973 46.3945 126.671 45.2399 128.008C44.9746 127.647 44.7951 127.311 44.5298 126.95C44.3503 126.614 44.1708 126.279 43.9913 125.943C43.6323 125.272 43.3591 124.627 43.1119 123.897C42.8648 123.166 42.7034 122.461 42.5082 121.558C42.4406 121.163 42.3989 120.682 42.2454 120.261C42.2037 119.78 42.162 119.298 42.1464 118.731C42.1307 118.164 42.141 117.512 42.1773 116.773C42.2735 116.146 42.3956 115.434 42.6036 114.747C42.8116 114.06 43.0196 113.374 43.3394 112.627C43.6332 111.966 44.0388 111.246 44.4185 110.611C44.7981 109.977 45.1777 109.342 45.6432 108.733C46.1087 108.125 46.5482 107.602 46.9877 107.079C47.6508 106.437 48.5636 105.589 49.2605 105.144Z" fill="#B6B6B6"></path>
                <path d="M23.9599 89.9837C23.9756 90.5507 24.0511 91.2295 24.1526 91.8225C24.2541 92.4154 24.3556 93.0084 24.4571 93.6014C24.5587 94.1944 24.746 94.8133 24.8475 95.4063C25.0348 96.0253 25.2222 96.6443 25.4355 97.1774C25.6229 97.7964 25.8362 98.3295 26.1354 98.8887C26.3487 99.4218 26.6479 99.981 26.947 100.54C27.4595 101.632 28.0839 102.665 28.7342 103.611C29.0594 104.085 29.3845 104.558 29.7955 105.057C30.1207 105.531 30.4719 105.918 30.9089 106.332C31.697 107.133 32.4253 107.822 33.2655 108.451C34.0198 109.054 34.886 109.598 35.7183 109.944C36.5246 110.375 37.3829 110.635 38.2672 110.809C38.2177 110.045 38.0564 109.34 37.9808 108.661C37.8195 107.956 37.6582 107.252 37.4968 106.547C37.0025 105.085 36.4223 103.597 35.5768 101.748C35.1501 100.682 34.3801 99.5118 33.4125 98.3754C32.9416 97.7643 32.4448 97.2389 31.974 96.6278C31.4771 96.1025 30.9205 95.4654 30.3378 94.9141C29.2583 93.8374 28.093 92.7348 27.0474 91.8559C25.7261 91.2683 24.7402 90.5011 23.9599 89.9837Z" fill="#B6B6B6"></path>
                <path d="M47.6404 90.6911C47.5962 91.1462 47.5521 91.6014 47.4819 92.1424C47.4378 92.5975 47.3676 93.1385 47.2376 93.5676C47.1935 94.0228 47.0375 94.5378 46.8815 95.0528C46.7255 95.5678 46.6814 96.0229 46.5254 96.5379C46.3694 97.0529 46.2394 97.482 46.0834 97.997C45.9274 98.512 45.7974 98.9412 45.6415 99.4562C45.3555 100.4 44.8719 101.378 44.5001 102.296C44.2582 102.785 44.1283 103.215 43.8864 103.704C43.6706 104.107 43.4288 104.596 43.213 104.999C42.7553 105.891 42.2379 106.671 41.7204 107.452C41.2029 108.232 40.6256 108.9 39.9625 109.543C39.3852 110.211 38.6622 110.742 38.0511 111.213C37.8039 110.482 37.5568 109.751 37.3954 109.047C37.2341 108.342 37.1586 107.663 37.109 106.898C36.984 105.455 37.1945 103.832 37.5089 101.866C37.7611 100.724 38.2109 99.5481 38.9442 98.3647C39.2978 97.8158 39.7633 97.2072 40.1169 96.6584C40.5564 96.1355 40.9959 95.6127 41.5472 95.03C42.512 94.0104 43.6224 93.1286 44.6809 92.4184C45.8511 91.6484 46.8576 91.1099 47.6404 90.6911Z" fill="#B6B6B6"></path>
                <path d="M46.3572 76.0588C45.6685 79.8795 44.1293 83.7238 42.2051 86.9829C41.6876 87.7632 41.256 88.5695 40.6527 89.3238C40.1352 90.1041 39.5579 90.7726 38.9806 91.4411C37.852 92.6923 36.4919 93.7796 35.1759 94.4118C35.0146 93.7069 34.9391 93.0281 34.7778 92.3233C34.7023 91.6445 34.6267 90.9657 34.6631 90.2271C34.6994 89.4884 34.7357 88.7498 34.8838 87.9513C35.032 87.1528 35.292 86.2945 35.5519 85.4362C35.6299 85.1787 35.7079 84.9212 35.8717 84.6897C35.9497 84.4322 36.1136 84.2007 36.3034 83.8834C36.657 83.3346 36.9847 82.8716 37.3383 82.3228C37.7778 81.7999 38.1913 81.3629 38.6307 80.8401C39.1301 80.4291 39.5695 79.9063 40.1547 79.5213C40.4043 79.3158 40.654 79.1103 40.9036 78.9048C41.1533 78.6993 41.4888 78.5198 41.7384 78.3143C42.3236 77.9293 42.7969 77.6041 43.356 77.305C43.9152 77.0058 44.3625 76.7665 44.8956 76.5531C45.5146 76.3658 45.9619 76.1265 46.3572 76.0588Z" fill="#B6B6B6"></path>
                <path d="M28.4238 55.4464C28.2679 55.9614 28.1717 56.5882 28.1015 57.1292C28.0314 57.6702 27.9352 58.297 27.9509 58.864C27.8964 59.972 27.9018 61.1918 28.045 62.2659C28.1622 63.4259 28.3054 64.5 28.5604 65.5143C28.7894 66.6144 29.1302 67.6547 29.4971 68.6092C29.8639 69.5636 30.3166 70.5441 30.7354 71.3269C31.2141 72.2215 31.6589 72.9185 32.2754 73.6675C33.3627 75.0276 34.5281 76.1302 35.7375 76.7776C35.9195 76.1768 36.0157 75.55 36.052 74.8113C36.0701 74.442 36.0623 74.1585 36.0805 73.7892C36.0986 73.4199 36.1168 73.0506 36.0231 72.7411C35.9736 71.9764 35.9241 71.2118 35.7887 70.4211C35.6534 69.6305 35.544 68.754 35.4607 67.7917C35.3332 67.2846 35.2317 66.6916 35.1042 66.1845C34.8908 65.6513 34.7035 65.0323 34.4902 64.4992C34.0036 63.3211 33.4053 62.2028 32.7211 61.0585C32.422 60.4994 32.037 59.9142 31.7118 59.4409C31.3008 58.9416 31.0016 58.3825 30.5906 57.8831C30.2655 57.4098 29.8545 56.9105 29.5033 56.5231C29.1522 56.1356 28.801 55.7481 28.4238 55.4464Z" fill="#B6B6B6"></path>
                <path d="M49.4551 60.8799C48.3112 64.6565 46.1633 68.0352 43.6903 70.9407C43.0012 71.669 42.4239 72.3375 41.7867 72.8942C41.1236 73.5366 40.4864 74.0933 39.7895 74.5381C38.4554 75.5396 37.1134 76.2576 35.7637 76.6922C35.6881 76.0133 35.7843 75.3865 35.7946 74.7337C35.8128 74.3644 35.8908 74.1069 35.9948 73.7636C36.0988 73.4202 36.2027 73.0769 36.3067 72.7336C36.5147 72.047 36.8345 71.3005 37.1283 70.6398C37.4481 69.8933 37.8538 69.1728 38.2854 68.3665C38.769 67.3886 39.4842 66.5744 40.397 65.7264C41.1979 64.9382 42.2824 64.1423 43.3148 63.5179C43.7881 63.1928 44.3473 62.8936 44.9064 62.5944C45.4656 62.2953 45.9987 62.0819 46.5319 61.8686C47.065 61.6552 47.5981 61.4419 48.1053 61.3144C48.5266 61.1609 49.0338 61.0334 49.4551 60.8799Z" fill="#B6B6B6"></path>
                <path d="M34.885 38.7564C33.0701 42.892 32.8002 47.4955 33.5289 51.277C33.6981 52.2653 33.9791 53.1938 34.2003 54.0104C34.5073 54.853 34.8143 55.6957 35.1733 56.3667C35.9511 57.8204 36.833 58.9309 37.7329 59.672C38.838 57.5704 39.0691 54.6418 39.1987 51.1202C39.2272 50.0981 39.084 49.024 38.855 47.9238C38.626 46.8237 38.3112 45.6976 37.8845 44.6313C37.4578 43.565 37.0311 42.4987 36.4926 41.4923C35.9541 40.4858 35.4755 39.5912 34.885 38.7564Z" fill="#B6B6B6"></path>
                <path d="M53.6484 47.3447C51.8439 50.8275 49.0874 53.7408 46.1695 55.9493C45.4465 56.4799 44.7236 57.0106 43.9408 57.4294C43.2438 57.8743 42.461 58.2931 41.7042 58.6261C40.2764 59.3181 38.9266 59.7526 37.7069 59.758C38.0395 57.4224 39.6699 54.8239 41.8672 52.2098C42.5564 51.4815 43.3313 50.7792 44.278 50.1288C45.1986 49.5643 46.2909 49.0518 47.2713 48.5991C48.3376 48.1725 49.4637 47.8576 50.5639 47.6286C51.638 47.4854 52.7121 47.3422 53.6484 47.3447Z" fill="#B6B6B6"></path>
                <path d="M42.5741 25.4361C40.3821 29.27 39.709 33.6577 40.1204 37.2494C40.5058 40.9269 41.778 43.8423 43.2944 45.3324C44.4932 43.5403 45.0598 40.4322 45.4807 37.1863C45.5249 36.7311 45.595 36.1902 45.5534 35.709C45.5975 35.2539 45.5818 34.6869 45.4543 34.1797C45.4126 33.6986 45.2851 33.1914 45.2695 32.6244C45.142 32.1173 45.0145 31.6101 44.887 31.103C44.3509 29.1602 43.4716 27.1135 42.5741 25.4361Z" fill="#B6B6B6"></path>
                <path d="M59.6084 35.0938C57.5464 38.4985 54.5245 41.0503 51.4791 42.7517C48.4078 44.5388 45.3129 45.4755 43.2946 45.3327C43.7469 43.2208 45.8923 40.7783 48.329 38.6115C48.7165 38.2604 49.078 37.995 49.4394 37.7297C49.8009 37.4644 50.2482 37.225 50.6955 36.9857C51.1429 36.7464 51.5902 36.507 52.0973 36.3795C52.6305 36.1662 53.0518 36.0127 53.5589 35.8852C55.5277 35.2633 57.65 35.0628 59.6084 35.0938Z" fill="#B6B6B6"></path>
                <path d="M51.3945 13.0215C48.7993 16.6396 47.9468 20.6918 48.0148 24.1796C48.0619 25.8805 48.3325 27.4618 48.7671 28.8116C49.2016 30.1614 49.7401 31.1678 50.2967 31.8049C51.5632 30.4081 52.3794 27.0946 53.1177 24.0385C53.8402 20.4154 52.9399 16.5819 51.3945 13.0215Z" fill="#B6B6B6"></path>
                <path d="M66.6318 23.3525C64.3383 26.5935 61.0771 28.698 58.0499 30.03C56.5363 30.696 54.9889 31.1644 53.6052 31.4012C52.3074 31.6641 51.0617 31.7552 50.2632 31.6071C50.8091 29.8046 53.4097 27.4063 55.9141 25.6348C58.8059 23.5122 62.793 23.0331 66.6318 23.3525Z" fill="#B6B6B6"></path>
                <path d="M68.3072 6.99349C65.0407 7.8782 61.6858 9.6732 59.1946 12.948C56.7034 16.2228 56.5503 18.8938 57.3257 21.2839C59.8433 21.0157 62.2255 19.9569 64.5789 16.8278C66.9582 13.6128 67.9123 10.1536 68.3072 6.99349Z" fill="#B6B6B6"></path>
              </svg>
            </div>
            <div class="review-feather-text">
              <p>Social Review</p>
              <p>Boost Conversion</p>
              <div class="r-loved-line"></div>
              <p>Use PhotoADKing! Create your first design now!</p>
            </div>
            <div class="review-feather">
              <svg viewBox="0 0 124 173" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M73.4509 160.198C66.6983 166.367 53.5996 173.239 44.1981 168.496C51.4814 161.605 63.6434 157.828 73.4509 160.198Z" fill="#B6B6B6"></path>
                <path d="M84.1293 144.407C83.7183 144.906 83.3073 145.405 82.8963 145.905C82.7506 146.043 82.6908 146.154 82.5451 146.292C82.3995 146.43 82.3396 146.542 82.194 146.68C81.9885 146.929 81.6972 147.205 81.4917 147.455C80.9948 147.98 80.472 148.419 79.9752 148.945C79.6838 149.22 79.4524 149.384 79.161 149.66C78.8697 149.936 78.6382 150.099 78.3469 150.375C77.824 150.814 77.2154 151.28 76.6666 151.634C76.1437 152.073 75.5091 152.453 74.9603 152.806C73.7768 153.54 72.5673 154.187 71.3578 154.835C68.8869 155.958 66.4238 156.798 63.9764 157.07C62.8086 157.237 61.5888 157.231 60.4548 157.2C59.2948 157.083 58.1947 156.854 57.2402 156.487C57.5914 156.099 57.8567 155.738 58.1481 155.462C58.4134 155.101 58.7047 154.825 58.9961 154.549C59.4669 153.938 60.0495 153.387 60.5724 152.948C60.8637 152.672 61.181 152.482 61.4724 152.206C61.7637 151.931 62.1408 151.629 62.4582 151.439C63.1527 150.948 63.9928 150.319 65.0905 149.611C65.6393 149.258 66.3598 148.852 67.1063 148.532C67.4236 148.342 67.8527 148.212 68.2819 148.082C68.711 147.952 69.1142 147.737 69.5434 147.607C70.4017 147.347 71.234 147.001 72.2041 146.801C72.6333 146.671 73.1483 146.515 73.6034 146.471L74.2901 146.263L74.9767 146.055C76.8052 145.594 78.6596 145.22 80.2826 145.01C81.7157 144.482 83.167 144.323 84.1293 144.407Z" fill="#B6B6B6"></path>
                <path d="M92.4436 127.927C91.8452 129.045 91.1611 130.189 90.3651 131.274C89.5691 132.358 88.7731 133.443 87.8652 134.467C87.3684 134.993 86.9574 135.492 86.4346 135.931C86.2291 136.181 85.9378 136.457 85.7063 136.62C85.5008 136.87 85.1834 137.06 84.978 137.31C84.4551 137.749 83.9323 138.189 83.3835 138.542C83.152 138.706 82.8606 138.982 82.6291 139.145L82.252 139.447L81.8748 139.749C80.7772 140.456 79.6535 141.078 78.5039 141.613C77.9291 141.881 77.3543 142.149 76.8393 142.305C76.2385 142.487 75.7235 142.643 75.1227 142.825C74.6077 142.981 73.9809 143.077 73.4659 143.233C73.2084 143.311 72.9249 143.303 72.6674 143.381C72.3839 143.373 72.1264 143.451 71.8429 143.443C70.7349 143.498 69.7726 143.414 68.7843 143.245C69.0497 142.884 69.2032 142.462 69.4945 142.187C69.674 141.851 69.9393 141.49 70.1448 141.24C70.6157 140.629 71.0007 140.044 71.4715 139.433C71.7628 139.157 71.9423 138.822 72.2337 138.546C72.525 138.27 72.7903 137.909 73.0817 137.633C73.7241 136.97 74.4785 136.367 75.3525 135.54C76.2863 134.601 77.6414 133.816 79.1682 132.978C79.8887 132.573 80.695 132.141 81.5273 131.795C81.9305 131.58 82.3597 131.45 82.7628 131.234C83.166 131.018 83.5951 130.888 83.9983 130.672C85.6629 129.981 87.3535 129.375 88.8127 128.933C90.2458 128.405 91.5333 128.015 92.4436 127.927Z" fill="#B6B6B6"></path>
                <path d="M97.9878 110.036C97.5871 111.188 97.1006 112.366 96.5282 113.571C95.9559 114.775 95.3576 115.893 94.6474 116.951C93.9372 118.01 93.1412 119.094 92.3192 120.093C92.1137 120.343 91.9083 120.592 91.7028 120.842C91.4973 121.092 91.2918 121.341 91.0603 121.505C90.6493 122.004 90.2123 122.418 89.6894 122.857C89.4839 123.107 89.2525 123.271 89.021 123.435C88.7895 123.598 88.584 123.848 88.2666 124.038C87.8036 124.366 87.2808 124.805 86.8178 125.133C86.3548 125.46 85.8918 125.788 85.317 126.056C84.828 126.298 84.339 126.539 83.8501 126.781C81.8681 127.663 79.9538 128.149 78.0552 128.068C78.2087 127.647 78.3882 127.311 78.4819 127.002C78.6614 126.666 78.7291 126.271 78.9086 125.935C79.2676 125.264 79.5407 124.619 80.0116 124.008C80.3706 123.337 80.8414 122.726 81.3721 122.003C81.6374 121.642 81.9027 121.28 82.2539 120.893C82.5192 120.531 82.8444 120.058 83.1955 119.67C83.6065 119.171 84.0175 118.672 84.6262 118.206C85.123 117.681 85.7317 117.216 86.3403 116.75C86.949 116.285 87.6435 115.793 88.338 115.302C89.0325 114.81 89.7529 114.405 90.4474 113.913C91.1679 113.507 91.8624 113.016 92.5829 112.61C93.3034 112.205 93.938 111.825 94.5987 111.531C95.894 110.858 97.1295 110.296 97.9878 110.036Z" fill="#B6B6B6"></path>
                <path d="M97.8805 72.8676C97.9506 73.4085 97.961 74.0614 97.9453 74.6283C97.9296 75.1953 97.9399 75.8481 97.9242 76.4151C97.9086 76.9821 97.8071 77.5751 97.7055 78.1681C97.604 78.761 97.5025 79.354 97.401 79.947C97.2995 80.54 97.172 81.0471 96.9847 81.6661C96.8572 82.1733 96.6698 82.7922 96.4565 83.3254C96.2431 83.8585 96.1156 84.3657 95.9023 84.8988C95.689 85.432 95.4496 85.8793 95.2363 86.4124C94.7836 87.3929 94.2191 88.3135 93.6286 89.1483C93.0381 89.9831 92.4216 90.7321 91.7791 91.3952C90.4942 92.7215 89.1052 93.7044 87.6461 94.1464C87.6097 93.4077 87.5994 92.7549 87.5631 92.0163C87.5528 91.3635 87.6023 90.5988 87.6778 89.92C87.7273 89.1554 87.8028 88.4765 88.024 87.6599C88.1593 86.8693 88.3545 85.9668 88.6355 85.0383C88.7292 84.7288 88.8489 84.5052 88.9425 84.1957C89.0362 83.8862 89.1559 83.6626 89.3354 83.3271C89.6345 82.7679 89.9337 82.2088 90.2329 81.6496C90.6179 81.0645 91.0029 80.4794 91.3879 79.8942C91.7729 79.3091 92.2697 78.7838 92.6547 78.1986C92.8602 77.949 93.1515 77.6733 93.331 77.3378C93.5365 77.0882 93.742 76.8385 94.0334 76.5629C94.4444 76.0636 94.9412 75.5383 95.3782 75.1248C95.8152 74.7113 96.312 74.186 96.6892 73.8843C97.1262 73.4709 97.5033 73.1692 97.8805 72.8676Z" fill="#B6B6B6"></path>
                <path d="M50.5628 142.486C46.4372 148.046 41.6943 160.54 44.112 168.522C52.4199 162.539 56.385 151.499 50.5628 142.486Z" fill="#B6B6B6"></path>
                <path d="M61.3731 131.153C61.1338 131.6 60.9803 132.021 60.7409 132.469C60.6811 132.581 60.6213 132.692 60.5614 132.804L60.3819 133.14C60.2623 133.363 60.1426 133.587 60.0489 133.897C59.8096 134.344 59.6821 134.851 59.4687 135.384C59.3491 135.608 59.2554 135.917 59.2216 136.115C59.1019 136.339 59.0082 136.648 58.8886 136.872C58.6752 137.405 58.5477 137.912 58.3344 138.445C58.121 138.978 58.0195 139.571 57.8062 140.104C57.4654 141.145 57.1505 142.271 56.9475 143.457C56.4036 145.683 56.1433 147.917 56.0546 150.099C56.0232 151.233 56.0517 152.255 56.2778 153.311C56.4181 154.393 56.7041 155.337 57.0161 156.367C57.3932 156.066 57.7704 155.764 58.0617 155.488C58.3531 155.213 58.7302 154.911 59.0215 154.635C59.6042 154.084 60.101 153.559 60.5979 153.034C60.8892 152.758 61.0687 152.422 61.334 152.061C61.5994 151.699 61.7789 151.364 62.0442 151.003C62.463 150.22 62.9677 149.411 63.4802 148.319C63.7534 147.674 63.9407 147.055 64.1619 146.238C64.2296 145.843 64.2972 145.447 64.3649 145.052C64.4326 144.657 64.5002 144.261 64.4561 143.806C64.4796 142.956 64.5031 142.105 64.4408 141.281C64.3967 140.826 64.3785 140.456 64.3343 140.001C64.2823 139.83 64.2902 139.546 64.2382 139.374C64.1862 139.203 64.194 138.919 64.142 138.748C63.8197 137.065 63.3518 135.52 62.85 134.173C62.5459 132.859 61.9504 131.821 61.3731 131.153Z" fill="#B6B6B6"></path>
                <path d="M68.6941 118.816C68.4131 119.744 68.1321 120.673 67.877 121.687C67.622 122.701 67.4529 123.689 67.1978 124.704C67.0703 125.211 67.0547 125.778 66.9272 126.285C66.9193 126.568 66.8855 126.766 66.7918 127.076C66.784 127.359 66.6643 127.583 66.6565 127.866C66.6148 128.347 66.5133 128.94 66.4976 129.507C66.4898 129.791 66.4819 130.074 66.4741 130.358L66.4064 130.753L66.3387 131.148C66.2814 132.197 66.25 133.331 66.2785 134.353C66.3487 134.894 66.333 135.461 66.3772 135.916C66.4473 136.457 66.4915 136.912 66.5616 137.453C66.6318 137.994 66.7878 138.509 66.8319 138.964C66.9099 139.222 66.9879 139.479 67.0659 139.737C67.1439 139.994 67.2219 140.252 67.2739 140.423C67.5598 141.367 67.9316 142.286 68.4751 143.152C68.7664 142.876 69.1176 142.489 69.4089 142.213C69.7003 141.937 69.9656 141.576 70.1711 141.326C70.6419 140.715 71.0269 140.13 71.4717 139.433C71.6512 139.097 71.8308 138.762 72.0103 138.426C72.1898 138.091 72.3433 137.67 72.4968 137.248C72.8038 136.406 73.1108 135.563 73.4256 134.437C73.7145 133.225 73.8135 131.696 73.629 130.159C73.5667 129.334 73.3587 128.647 73.2106 127.849C73.1066 127.506 72.9766 127.076 72.8726 126.733C72.8206 126.561 72.7686 126.39 72.6906 126.132C72.6386 125.961 72.5607 125.703 72.5087 125.531C71.9549 124.012 71.2554 122.631 70.5221 121.448C70.0983 120.358 69.3312 119.372 68.6941 118.816Z" fill="#B6B6B6"></path>
                <path d="M73.9231 105.144C73.7279 106.046 73.6445 107.008 73.5872 108.057C73.5038 109.019 73.4465 110.067 73.475 111.089C73.5034 112.111 73.5319 113.133 73.5604 114.156C73.5526 114.439 73.6305 114.697 73.5967 114.894C73.5889 115.178 73.6669 115.435 73.659 115.719C73.7292 116.26 73.7733 116.715 73.8435 117.256C73.9136 117.797 74.0696 118.312 74.1138 118.767C74.1839 119.308 74.3139 119.737 74.4699 120.252C74.6259 120.767 74.67 121.222 74.826 121.737C74.982 122.252 75.112 122.681 75.242 123.11C75.8998 124.973 76.789 126.671 77.9436 128.008C78.209 127.647 78.3885 127.311 78.6538 126.95C78.8333 126.614 79.0128 126.279 79.1923 125.943C79.5513 125.272 79.8245 124.627 80.0717 123.897C80.3188 123.166 80.4802 122.461 80.6754 121.558C80.743 121.163 80.7847 120.682 80.9382 120.261C80.9799 119.78 81.0216 119.298 81.0372 118.731C81.0529 118.164 81.0426 117.512 81.0063 116.773C80.9101 116.146 80.788 115.434 80.58 114.747C80.372 114.06 80.164 113.374 79.8442 112.627C79.5504 111.966 79.1448 111.246 78.7651 110.611C78.3855 109.977 78.0058 109.342 77.5404 108.733C77.0749 108.125 76.6354 107.602 76.1959 107.079C75.5328 106.437 74.62 105.589 73.9231 105.144Z" fill="#B6B6B6"></path>
                <path d="M99.2237 89.9837C99.208 90.5507 99.1325 91.2295 99.031 91.8225C98.9295 92.4154 98.828 93.0084 98.7264 93.6014C98.6249 94.1944 98.4376 94.8133 98.3361 95.4063C98.1488 96.0253 97.9614 96.6443 97.7481 97.1774C97.5607 97.7964 97.3474 98.3295 97.0482 98.8887C96.8349 99.4218 96.5357 99.981 96.2365 100.54C95.724 101.632 95.0997 102.665 94.4494 103.611C94.1242 104.085 93.799 104.558 93.3881 105.057C93.0629 105.531 92.7117 105.918 92.2747 106.332C91.4866 107.133 90.7583 107.822 89.9181 108.451C89.1638 109.054 88.2976 109.598 87.4653 109.944C86.659 110.375 85.8007 110.635 84.9164 110.809C84.9659 110.045 85.1272 109.34 85.2027 108.661C85.3641 107.956 85.5254 107.252 85.6868 106.547C86.1811 105.085 86.7613 103.597 87.6068 101.748C88.0335 100.682 88.8035 99.5118 89.7711 98.3754C90.242 97.7643 90.7388 97.2389 91.2096 96.6278C91.7065 96.1025 92.2631 95.4654 92.8458 94.9141C93.9253 93.8374 95.0906 92.7348 96.1362 91.8559C97.4575 91.2683 98.4434 90.5011 99.2237 89.9837Z" fill="#B6B6B6"></path>
                <path d="M75.5432 90.6911C75.5874 91.1462 75.6315 91.6014 75.7017 92.1424C75.7458 92.5975 75.816 93.1385 75.946 93.5676C75.9901 94.0228 76.1461 94.5378 76.3021 95.0528C76.4581 95.5678 76.5022 96.0229 76.6582 96.5379C76.8142 97.0529 76.9442 97.482 77.1002 97.997C77.2562 98.512 77.3861 98.9412 77.5421 99.4562C77.8281 100.4 78.3117 101.378 78.6835 102.296C78.9254 102.785 79.0553 103.215 79.2972 103.704C79.513 104.107 79.7548 104.596 79.9706 104.999C80.4282 105.891 80.9457 106.671 81.4632 107.452C81.9806 108.232 82.558 108.9 83.2211 109.543C83.7984 110.211 84.5214 110.742 85.1325 111.213C85.3797 110.482 85.6268 109.751 85.7882 109.047C85.9495 108.342 86.025 107.663 86.0745 106.898C86.1996 105.455 85.9891 103.832 85.6747 101.866C85.4225 100.724 84.9727 99.5481 84.2394 98.3647C83.8858 97.8158 83.4203 97.2072 83.0667 96.6584C82.6272 96.1355 82.1877 95.6127 81.6364 95.03C80.6716 94.0104 79.5612 93.1286 78.5027 92.4184C77.3325 91.6484 76.326 91.1099 75.5432 90.6911Z" fill="#B6B6B6"></path>
                <path d="M76.8264 76.0588C77.5151 79.8795 79.0543 83.7238 80.9785 86.9829C81.496 87.7632 81.9276 88.5695 82.5309 89.3238C83.0484 90.1041 83.6257 90.7726 84.203 91.4411C85.3316 92.6923 86.6917 93.7796 88.0076 94.4118C88.169 93.7069 88.2445 93.0281 88.4058 92.3233C88.4813 91.6445 88.5569 90.9657 88.5205 90.2271C88.4842 89.4884 88.4479 88.7498 88.2998 87.9513C88.1516 87.1528 87.8916 86.2945 87.6317 85.4362C87.5537 85.1787 87.4757 84.9212 87.3119 84.6897C87.2339 84.4322 87.07 84.2007 86.8802 83.8834C86.5266 83.3346 86.1989 82.8716 85.8453 82.3228C85.4058 81.7999 84.9923 81.3629 84.5529 80.8401C84.0535 80.4291 83.6141 79.9063 83.0289 79.5213C82.7793 79.3158 82.5296 79.1103 82.28 78.9048C82.0303 78.6993 81.6948 78.5198 81.4452 78.3143C80.86 77.9293 80.3867 77.6041 79.8276 77.305C79.2684 77.0058 78.8211 76.7665 78.288 76.5531C77.669 76.3658 77.2217 76.1265 76.8264 76.0588Z" fill="#B6B6B6"></path>
                <path d="M94.7598 55.4464C94.9157 55.9614 95.0119 56.5882 95.082 57.1292C95.1522 57.6702 95.2484 58.297 95.2327 58.864C95.2872 59.972 95.2818 61.1918 95.1386 62.2659C95.0214 63.4259 94.8782 64.5 94.6232 65.5143C94.3942 66.6144 94.0534 67.6547 93.6865 68.6092C93.3197 69.5636 92.867 70.5441 92.4482 71.3269C91.9695 72.2215 91.5247 72.9185 90.9082 73.6675C89.8209 75.0276 88.6555 76.1302 87.4461 76.7776C87.2641 76.1768 87.1679 75.55 87.1316 74.8113C87.1135 74.442 87.1213 74.1585 87.1031 73.7892C87.085 73.4199 87.0668 73.0506 87.1605 72.7411C87.21 71.9764 87.2595 71.2118 87.3949 70.4211C87.5302 69.6305 87.6395 68.754 87.7229 67.7917C87.8504 67.2846 87.9519 66.6916 88.0794 66.1845C88.2928 65.6513 88.4801 65.0323 88.6934 64.4992C89.1799 63.3211 89.7783 62.2028 90.4624 61.0585C90.7616 60.4994 91.1466 59.9142 91.4718 59.4409C91.8828 58.9416 92.1819 58.3825 92.5929 57.8831C92.9181 57.4098 93.3291 56.9105 93.6803 56.5231C94.0314 56.1356 94.3826 55.7481 94.7598 55.4464Z" fill="#B6B6B6"></path>
                <path d="M73.7285 60.8799C74.8724 64.6565 77.0203 68.0352 79.4933 70.9407C80.1824 71.669 80.7597 72.3375 81.3969 72.8942C82.06 73.5366 82.6971 74.0933 83.3941 74.5381C84.7282 75.5396 86.0702 76.2576 87.4199 76.6922C87.4955 76.0133 87.3993 75.3865 87.389 74.7337C87.3708 74.3644 87.2928 74.1069 87.1888 73.7636C87.0848 73.4202 86.9809 73.0769 86.8769 72.7336C86.6689 72.047 86.3491 71.3005 86.0553 70.6398C85.7355 69.8933 85.3298 69.1728 84.8982 68.3665C84.4145 67.3886 83.6994 66.5744 82.7866 65.7264C81.9857 64.9382 80.9012 64.1423 79.8688 63.5179C79.3955 63.1928 78.8363 62.8936 78.2772 62.5944C77.718 62.2953 77.1849 62.0819 76.6517 61.8686C76.1186 61.6552 75.5854 61.4419 75.0783 61.3144C74.657 61.1609 74.1498 61.0334 73.7285 60.8799Z" fill="#B6B6B6"></path>
                <path d="M88.2986 38.7564C90.1135 42.892 90.3834 47.4955 89.6547 51.277C89.4855 52.2653 89.2045 53.1938 88.9833 54.0104C88.6763 54.853 88.3693 55.6957 88.0103 56.3667C87.2325 57.8204 86.3506 58.9309 85.4507 59.672C84.3456 57.5704 84.1145 54.6418 83.9849 51.1202C83.9564 50.0981 84.0996 49.024 84.3286 47.9238C84.5576 46.8237 84.8724 45.6976 85.2991 44.6313C85.7258 43.565 86.1525 42.4987 86.691 41.4923C87.2295 40.4858 87.7081 39.5912 88.2986 38.7564Z" fill="#B6B6B6"></path>
                <path d="M69.5352 47.3447C71.3397 50.8275 74.0962 53.7408 77.0141 55.9493C77.737 56.4799 78.46 57.0106 79.2428 57.4294C79.9398 57.8743 80.7226 58.2931 81.4794 58.6261C82.9072 59.3181 84.2569 59.7526 85.4767 59.758C85.1441 57.4224 83.5137 54.8239 81.3164 52.2098C80.6272 51.4815 79.8523 50.7792 78.9056 50.1288C77.985 49.5643 76.8927 49.0518 75.9123 48.5991C74.846 48.1725 73.7198 47.8576 72.6197 47.6286C71.5456 47.4854 70.4715 47.3422 69.5352 47.3447Z" fill="#B6B6B6"></path>
                <path d="M80.6095 25.4361C82.8015 29.27 83.4746 33.6577 83.0632 37.2494C82.6778 40.9269 81.4056 43.8423 79.8891 45.3324C78.6904 43.5403 78.1238 40.4322 77.7029 37.1863C77.6587 36.7311 77.5886 36.1902 77.6302 35.709C77.5861 35.2539 77.6018 34.6869 77.7293 34.1797C77.7709 33.6986 77.8985 33.1914 77.9141 32.6244C78.0416 32.1173 78.1691 31.6101 78.2966 31.103C78.8327 29.1602 79.712 27.1135 80.6095 25.4361Z" fill="#B6B6B6"></path>
                <path d="M63.5752 35.0938C65.6372 38.4985 68.6591 41.0503 71.7045 42.7517C74.7758 44.5388 77.8707 45.4755 79.889 45.3327C79.4367 43.2208 77.2913 40.7783 74.8546 38.6115C74.4671 38.2604 74.1056 37.995 73.7442 37.7297C73.3827 37.4644 72.9354 37.225 72.4881 36.9857C72.0407 36.7464 71.5934 36.507 71.0863 36.3795C70.5531 36.1662 70.1318 36.0127 69.6247 35.8852C67.6559 35.2633 65.5336 35.0628 63.5752 35.0938Z" fill="#B6B6B6"></path>
                <path d="M71.7891 13.0215C74.3843 16.6396 75.2368 20.6918 75.1688 24.1796C75.1217 25.8805 74.851 27.4618 74.4165 28.8116C73.982 30.1614 73.4435 31.1678 72.8869 31.8049C71.6204 30.4081 70.8042 27.0946 70.0659 24.0385C69.3434 20.4154 70.2437 16.5819 71.7891 13.0215Z" fill="#B6B6B6"></path>
                <path d="M56.5518 23.3525C58.8453 26.5935 62.1065 28.698 65.1337 30.03C66.6473 30.696 68.1947 31.1644 69.5784 31.4012C70.8761 31.6641 72.1219 31.7552 72.9204 31.6071C72.3745 29.8046 69.7739 27.4063 67.2695 25.6348C64.3777 23.5122 60.3906 23.0331 56.5518 23.3525Z" fill="#B6B6B6"></path>
                <path d="M54.8764 6.99349C58.1429 7.8782 61.4978 9.6732 63.989 12.948C66.4802 16.2228 66.6333 18.8938 65.8579 21.2839C63.3403 21.0157 60.9581 19.9569 58.6047 16.8278C56.2254 13.6128 55.2713 10.1536 54.8764 6.99349Z" fill="#B6B6B6"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="loved-review-box">
          <a target="_blank" href="https://www.trustpilot.com/reviews/5f844e54798e6f0bcc428385" rel="noreferrer nofollow">
            <div class="user-review-box">
              <div class="c-rev-div d-flex">
                <div class="c-review-top-quote">
                  <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                    <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                  </svg>
                </div>
                <p class="c-review-text"> PhotoADKing is great. It permits you to be so inventive and gives you the instruments to do as such. I totally love it particularly for planning huge Posters for promotion. </p>
              </div>
              <div class="c-rev-div d-flex">
                <div class="d-flex w-65">
                  <div class="review-user-avtar">
                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_second.png" alt="Akshay Pubariya profile image" loading="lazy">
                  </div>
                  <div class="review-user-details">
                    <p>Akshay Purabiya</p>
                    <p>Digital Marketer</p>
                    <p>(India)</p>
                  </div>
                </div>
                <div class="w-35 position-relative c-rating-box">
                  <div class="d-block text-right">
                    <svg width="27" height="26" viewBox="0 0 27 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M27 0H0.9375V26H27V0Z" fill="#00B67A"></path>
                      <path d="M13.9686 17.5231L17.9322 16.521L19.5883 21.6127L13.9686 17.5231ZM23.0904 10.9419H16.1133L13.9686 4.3877L11.8238 10.9419H4.84668L10.4936 15.0044L8.34883 21.5585L13.9957 17.496L17.4707 15.0044L23.0904 10.9419Z" fill="white"></path>
                    </svg>
                  </div>
                  <div class="d-block text-right">
                    <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                      <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                      <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                      <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                      <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
        <div class="loved-review-box">
          <a target="_blank" href="https://www.g2.com/products/photoadking/reviews/photoadking-review-4724327" rel="noreferrer nofollow">
            <div class="user-review-box">
              <div class="c-rev-div d-flex">
                <div class="c-review-top-quote">
                  <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                    <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                  </svg>
                </div>
                <p class="c-review-text"> PhotoADKing is ideal for creating posters, presentations, graphics, documents, and visual content as there are many free templates available to use, and the templates are... </p>
              </div>
              <div class="c-rev-div d-flex">
                <div class="d-flex w-65">
                  <div class="review-user-avtar">
                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_third.png" alt="John w profile image" loading="lazy">
                  </div>
                  <div class="review-user-details">
                    <p>John W</p>
                    <p>Content Writer</p>
                    <p>(UK)</p>
                  </div>
                </div>
                <div class="w-35 position-relative c-rating-box">
                  <div class="d-block text-right">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                      <rect width="30" height="30" rx="15" fill="url(#pattern0)"></rect>
                      <defs>
                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                          <use xlink:href="#image0_1888_2991" transform="translate(-0.244275 -0.1) scale(0.00763359)"></use>
                        </pattern>
                        <image id="image0_1888_2991" width="217" height="150" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANkAAACWCAYAAABTsC3PAAAUaklEQVR4Ae2d+5cU1bXH8wdlRfGG642GlXgvkgADAgEGFwyEAI6MgJGJUUAciGgQuWIIqARyERfBoIBRIz7gqghGBF2CmhiNJkoYMwkMDPPgMTB7Z327KO2uru6px3lV1f6hV/Wj+tR57M/Z57HP3l8jIpaX1IHIgD4Z+JpUrr7KlbqVuoUMCGSiyWUko1kGBDLNFSzaTLSZQCaQiSbTLAMCmeYKFk0mmkwgE8hEk2mWAYFMcwWLJhNNJpAJZKLJNMuAQKa5gkWTiSYTyAQy0WSaZUAg01zBoslEkwlkNiG72M/U18vU3eW98B7f2cyTPFt5/QtkuoQKwBzYw7R1PdODdzG3tTC3NjHPHc88YwRz4zDm8UOZG65kHvl174X3+A6/4R7ci/+0tZTSKKWFNJG2rnxLusrrViBTJVQHX2XacD/z0puZm29gHnvVV/D4EKm6Im08Y+nc0jMJz1ZVDklHeV0KZEmFqqOdacta5iXNzNOH6wMqKpjIw5LmUp4IeUtaLvmf8roTyOIIVecJpk2rvSFc+TAvKgim7kPeWptKeSXkOU4Z5V7l9SWQRRGq57Z5GgvzJVOgqHoO8gwN99w25cIj8EZbORXIakGGVT7MsbD4oErgbaczd7w3h5MVTKMdjkAWhAwrdw/dzdzkwDxLF5RNw0tllFXKaJoorcYWyMohW7ucedp1+dFcg0E67TqmtcuZaMBoz55WaLP2f4EMkGGVcNao4sAVhG/WKG9VsrzDkffKOp5iQ3ZgD3PrtOLCFYStdZq3gS6AKQMMWre4kK1eLHAFIbv8mVYvVipkWRveqc5v8SDb9wLzvIkCWA3AvlxJnTeRad8LApsCrV4syNatYG64QgAbDDD/94YrmNatENBSglYMyPp6vc1kX3jkGq+jwWa2GCUn7mzyD9nBV/O1oWyrg8BGthgiJwIt35Dt2OwdG7ElmHl7buMwph2bEwma6sWELKWXX8g2ro43JMobEBrLQxtXC2gx5mn5hAyWGxqFTNL++mVLETNmSVnSWmF5zR9kOIUsgBmpA5z4DhMq+a6y88kXZAKYEbjKOzEBrRKosA4mP5DJENE4YD5snpHx4MIWJoBF+C4fkMkihzXAvgRNFkNqDp2zDxmW6WUO5kQdyPJ+uDbPNmTYaIb7NIHMjTrAPppsWFdptOxCBlOpPLkGyEtHAcsQMcGqAC2zkJVcseVFMPNWDtg6xtiszfu92YQM1vR5E8yclUes97+an2UPMpwHy9NxlbFDPOeo8ycxzxnNPPnafHQgOCYj59FKGj1zkGX2wOXCaZ6z0V1bmF7bzfT+20ztx5h6e7w5zKmTTH1438N0/DOmo28x7X2GaedjTBtWMo/+Rvbgw8FPGTZmzP1AllwGjBnCfMdMpu2/8qA5f45pIIFXqIsXPa/FGR1OiiuDLPn4gNObLAja8gVM0FZfHGO6dDF9T37mNPMPrh687FO+zbx4DvN9C5kfuY95xW3MC6Yw3/idwf+ruV5L0W0KrNEyM1x03qvUXc1Mb77CdOF8erB8gbx0iWnrw4ND8uASpg+PMvV0MV26HN8MWhOegs/2luZGvHz+4Onogg1esPwyFfCaDcjgF1GXAKRNd/5kphd36Ane193FPPW7tcs+43oPbAwpBxNewH94P/Oka2qnl7Yu6vwfvi0HzeNgZcjo75mAzEnHoxDwbRvUaq5yIYIm2vlYbSCaxzL1nIkvuM//tnaadSBJ3cnBgWp5+Qr03n3IXLSuv/92b2VQp6D0nGGeNTIciEnXMh37NFxoscCCFUu88D6Yx0uXmB9dGZ6uTshGFvegp9uQwXTKMd/09JtHmPoNxHXe/WRNEGjn5upFFSz9b9/IfGujt9c2exTzwyuY/vxeNWhv7K2ZdmqNVQ9U+N4voMmV25Ahukq9RjP8G+1/uVpgg5pCxefeHuaWCXXLTs9v/wq07jPMbbeE3/+zBV7Q9/J84X5LQQwRMadKu5bnLYfv3YXsYr874Ysmf5vp8xrDMx1C8crvw4EJdCol0Pp6mO+/o+79dCDQOZw/x7zQUgwAhG0qWHw0dyFDAL6AUFn5vKDRs8LQAVNYmhgiL5wauez01K/r3zvpGqbTJyu1x9k+5nH2ooYiuGKRtJmzkDlxjGXqd80CBuhwRk5h50KP/7J6BfTw60qfETu/OA4T1sHk9Ds3IUOMZoWCliitcUOZTneaFQZomEWz1ZT9tqlMB1+pXmEcGGBe+VM1z0jRRkWKYe0kZC6cFaPDB8wChl78yFvphf/HN3JproYl/DDN8O6b6Z+RAq4vO7wCnTlzD7LOE8zj7c0XIAS0c0u4gIYJrarvzp3l1KZPD7UxddXRvn86wjx7tBuQjR/K1HnCfD2raq8Y6bgH2Sa77rVL5j+wGYxRibHvxT4b9ou6u7wh6ccfMO15Jrnww/pk91O1rfxhdoW9sdkNyZ+hQnsF0qBNxXD37Rxk3NpkTxBW3cl0tk8PYLAdPHOK6chBpsd+wbywifmH32Mee1W68s4e7RkHh3UKOAXQcZz5kZ+ne0YAji+HfGm/b23SU9dhdWHxO7cg62i3tknKY4YwHT2kvtHPnWX67C/M2JJYMFmtsNcCDPtQx/7KtPkh5ptvUPvMtGCV/7/hSqaOdvV1bhGosFGNW5DZtLZ/cOlXFhQqGglDtH/9g+nxdZ7GKhcuFe+nDGP64J1qAe3tZqzc8fTr3YWrrPxFsM53CjJrq4oTrmb6+P1qgU0KG4acz/6G+eZx+gT9f5cwBY+49HQzr8lYwI0CrDK6Bdn04fqEsqz3rJpTrLtHHWA4fvLkJu3loLder8wzgNuZQW/K04dXliNpx+bw/9yBTLGlQxVItSDDsOtvH6tpaLgKWLNUO2A8a3T1Ak1vN/OtUzybRNgl1nvdNpX5JzP057NWnQe+z7vXYXcgs2Wr+OjK2kvfcXpHnGK+Y6YZwV0+L3z+iEWWKK/TJ5lvs2QgHACstC+Zc1tGZyDjpXPNCGigkentN9JrMVjC//x2c/lff2+6PJeMkC1ulQTaAG0ftiqXl+/cgazZwlIzDFXTOr65cIGxQhZ5eBoUsASfU0dPcQ2y5hsEMu09Cho97aZsAmHlDavSN66FuSS9/LS3sojFjiQvuDZwaLiIts/ziWk3NJkln4r0zh/SQYbFhrZ5RrVYSWPCggNz2KSv9feyVcuakA4xz74Z3YBs63rzgoql4zBHM3EWO35vyfNTiJCaHK7qeBZtXZ+uw4vTbobvdQMyGwHV19yVrlHhh2NBo/nOIYeAAdo8B3h3AjJuazEurHTgpXSQvf6i8Tzr0CDOpNnWkq49DGunOOsUbkBmwfK+5NY6acPAyQ/8zedUq1gpV44t8t2AzEJYWjrZkbznxFBxjuGzWbDSUPVysXPIsd8PNyCbMcKsVsCpXFhGJNVkH7xjNr/wt991igl7cmlfpzqZb6nv09GKJpsxInl7JG1HQ/9zA7LGYWaFduZIpv4LyRt1b4pTzAm0CE41J+4QwgTptd1m6ztKmRuHqS1jWLktfWcfMsxvTPv0gCenNLHDfrfVnJCuvD39VkNQuOAVa5mF/b16sGF0kVOnp/Yhg7WHaZfRy+YlNwpGtJVtjxqDrBTaNgiJis8fvW+sDJGGnzglnVM/+fYhg/V6vR5Ox2/wx54ktCyEG052fv2gkTyXHJMGD2aqAAxpXLjAvH6FkXJEbd+SYyFV5XMoHYEsbmOYguxHI5m6E8Qfi1MenH+bECFUro6OLiRNgSxO48W5V4aLodqEXn1e/0LAwCUe1Jd+CAxRNVOs+2S4SPoaXBY+qiGD9QNiPcfprJLeCyPnlvHVeTAFl/8cWfjQCBkRsyzhVwg5ffpnM4D5YO5/ueL5sTSQD0naqyzha4Ysa5vR7x3WJpT0q1XpD5L68ES9wkW4BfvRCphlM1ozZFk0q5r5ffWgTfhPz7IjKhwq7/vkQ/XliaPdxKxKM2RZNBDWcDyHdj/pbRGohCdqWv39zOvusQeaGAhrhszCUCX1UZf/f06tQN56I1NPt9m5WBBA7FmO+6backXVZnLURS9kOLBXMT6P2jBp7kNwiaCQxfms+NAm/fHddPmJk/da92KDfrDwuGnqvM5/5dBmrUZR9b0N9wNT/zu9TaAq9wMPLKp2VqqqbuOmA/d2zWONd3rifiBuQ8W935YjHYR7jZvX8vtVOdJZuzy5U5wKZzqrkpuLlZdrz9PmITuwJ11blOffsff2zapQIbZcwsGGMW2DWHAJV29oTX/7KF2ZsJx/k2EfmOISTvN87LKQsw3npnMa0u9J4RDljseM9/w1QVs2P91ZuV0WyiLOTQ1BZstN96F96Xr+kibuYXpigzOg0f6EToKwmDPxW+bLIW66zUBGeQg48bMF5gU0bMWuZUJ89woDA8wP32sl/2j71MP2tMN+jf93Y06GAtqa26gOnbT4JrOCOvG/Qp9H2zfGE9y//zU0nZrD0jC4E34noZM0Eh7svTgnQQB57TIzArtojhe6Nky4J13D1NUZDbT+C8zLLWlhCQJoaKh4GeRchbPd9bi+cLZjrmT6vzVMnf8sDQt51R3hUK+5O5qZ1qF94f8Pg1f1dxLO1ixkpkMQVQyFshKYHTZ+r79UuZF+9BDz5GtDQQkN3l4+OjnX5/lzVA1PxPQkMHt5Y5h439Fu3qmOLwxjhjAdPRRteBWnLuDf8bO/eJvNCDHrPy/u9b5Wpt1PMfWG2Df29zOOyISmvXhO/W2KZ7eF/y9u/pLcj9PQHe3q6zxO+xi4152Fj8uFtRrSB/aMZ/v0NDqCDZ45xfTe20xPPMq8sIkZ5+garqgW8rFDmJv+h3nB5FKAQfr8E6Yzp+vn6x/HmZvHVaeFYA4v7Qr/b08388zvhf4nFNgkINX7T44t78vXG5yDjDatttfoEMjfbow2j0nTA0LzwP1ZdxfT6U6mTz5k2v8y05G3vPcA6uQ/PQ0Exz1RngXj3p1bwuvupjHVnQfS3bwm/P56YCj8DW0dqWxRyu/wPe5B1nnCvLPTgODQa7uz2fgw7l00OxScqrlPx3Hmcf8Req8RLQafHp0nslnPMYF2DzL4/FjSbK/xLwNHH32QTQGoFdJp3FCm9s+9Ml04z7zGwvGi8s6sAKuKvpZ2EjJ6zuJk3BeESd8afB4Us0fzK13r9fw5rrmk/8CdXoxprEb65bR0RRtrrQeH2sZNyKDNLPj9qBK82aOZ+nqyJwx/OlJzyE1/2GvfaU6O/XmEdRzOQmbNljHYs98yKXugIXBDrSX922fY12I5t1UMguYuZHB62jTcukCUtNuEqzlVZE4bQ5fOfzHPHuVG/ZV3XE3Dcxu9JQiX/9ldyCCYD93tlJDQ9k3ZGjrutHA2rByokPdoU1/4inJ1GzKcmJ52nVOg8cMrmLDPZUM7xX0m6s+Cu72qua0P27TrchseqZ48uA0ZhAr+L/xGcuW64sdM7cfcBg2RRBFHTYcT1oTtgLasJ4x5/c19yLDSOMvBucWM65m2bahvFxhX8yi5f4DptReYHVjgqOgcZ41iogGBzNmeZMta97SZ35sjaPqLO9yYzP/xXeYHFjlZV1UWJ0o6FMOnRBLmOROaDPBzawoLdh8Inde7mpnefMWOZoNlPjqiKYYD3Eetz9ZphdRgvtLKDGRkyTdjxZAnilDdcyvTM9uYOo6nC/4+WK95potp7zOedccPRzipvfy6Q9v5AlfEa3Ygg9CtXuy0MPlCVbriuMrdLUzPbmP64u/eIcukcapRdmwwf3GM6fntzMvm1bToqMhDlE5B8z1osyKCVV7mbEGGYeO8idkBrVyAfzKdS0c7dm3hkpX/+297K5S9Pd6y9qmTXnRNRNj89EOmw/uZnn3Cs9y4d6HV08uJwZ03sfCAAbbMQUb7Xgg/6Fgu0Fl6D40HB0LzJzHPGsk8Zkg2O5FgnTdcwWir8h69qO+zBxmGTutW5EMQg4KZo89oo6JCFSx3NiHDsNGBM2eJh1E5gim0Dgp0ViwIVNjnzEJWClLhwnGYvAMTt3w4xgLXCoOtjhbo9+xChkaC1+FGR/eG4gpnHu5vHFZqEwGscpM825ABtB2bZX7mCKBoCwGsEjDUR/YhA2gb7Xq4Cp2XOCL4pvKGNhDAqgHLD2QAzUVr/YKAVlTr+qidSj40GSDDy0aA94KAVEsj5jmgelSIBrsvX5AJaEbnpwJY+PAwCF3+IJOhoxHQZIgYDTAAl0/IAJoshmiDTRY5ogOWb8gAGpb3ZR9NHWzYB5Nl+tirqPnVZIAML2xYi2VIetBgyXHw1dgCFpyfFPFz/iEDaPDaJLaOyUGDLaKYSiXuYIoBma/VYL0fFg+s4MvwtZbnUVdiTR9v/hWmqYsFGWDDebSsHvw02RngwKWcB0usvcphKx5kvlbLkisDk3AhEKK4DFAClw9acSEDbHDO47oXLJOAwatUwZ3e+GCovBYbMl+rwZ2aiw5UTQEGx6Nb1irtvVUKadbTEsh80HCFkbFrvvd1ggbf9HCdncaLVnn9yfvQjkogCwoGlqoRTcaVsE06IEP4IkRXkWX5UChUa06BLAiZ/xl+Djfcn6+NbGwoIwAfyuaXU67a60IgiyJkiGGNzezxQ5Nv6OrQSFHSRJ6xmVygGM2udSACWRTI/Hs6T5QclJZifjVc6S5wyFtrk+dMFXn28y9XK3UhkCUVvI52L8gDNByck0bRKjrvQR6gsbBKiLwlLZf8T3ndCWSqhArGs5jDLZ3L3HwD89ir9IGHtPGMpXO9OZYY7ioHQ2UnJZCpgiyYDlbusLG7db3nFqGtxQstixMBM0Z4R3AwXyofduI9vsPxHNyDexGOtq2llEYpLaQpq4JOQxUEVCALwmHyM1b5AEx3l/fCe1n5yxRAQaDCPgtkJqGSZ+UOoDCogt8JZCL4hRT8IAg6PwtkAplAplkGBDLNFayzh5S00x+oNFGHAplAJppMswwIZJor2ERPKc9wW6P9G75vd7vXaHVOAAAAAElFTkSuQmCC"></image>
                      </defs>
                    </svg>
                  </div>
                  <div class="d-block text-right">
                    <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                      <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                      <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                      <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                      <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
  <div class="loved-section-container tab-loved-new w-100 pb-0">
    <div class="loved-review-container w-100">
      <div class="user-review-box r-box-diff">
        <div class="review-feather">
          <svg viewBox="0 0 123 173" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M49.7327 160.198C56.4853 166.367 69.584 173.239 78.9854 168.496C71.7022 161.605 59.5402 157.828 49.7327 160.198Z" fill="#B6B6B6"></path>
            <path d="M39.0543 144.407C39.4653 144.906 39.8763 145.405 40.2873 145.905C40.4329 146.043 40.4928 146.154 40.6384 146.292C40.7841 146.43 40.8439 146.542 40.9896 146.68C41.1951 146.929 41.4864 147.205 41.6919 147.455C42.1888 147.98 42.7116 148.419 43.2084 148.945C43.4998 149.22 43.7312 149.384 44.0226 149.66C44.3139 149.936 44.5454 150.099 44.8367 150.375C45.3596 150.814 45.9682 151.28 46.517 151.634C47.0399 152.073 47.6745 152.453 48.2233 152.806C49.4068 153.54 50.6163 154.187 51.8258 154.835C54.2967 155.958 56.7598 156.798 59.2072 157.07C60.375 157.237 61.5948 157.231 62.7288 157.2C63.8888 157.083 64.9889 156.854 65.9433 156.487C65.5922 156.099 65.3269 155.738 65.0355 155.462C64.7702 155.101 64.4789 154.825 64.1875 154.549C63.7167 153.938 63.134 153.387 62.6112 152.948C62.3199 152.672 62.0026 152.482 61.7112 152.206C61.4199 151.931 61.0427 151.629 60.7254 151.439C60.0309 150.948 59.1908 150.319 58.0931 149.611C57.5443 149.258 56.8238 148.852 56.0773 148.532C55.76 148.342 55.3309 148.212 54.9017 148.082C54.4725 147.952 54.0694 147.737 53.6402 147.607C52.7819 147.347 51.9496 147.001 50.9795 146.801C50.5503 146.671 50.0353 146.515 49.5802 146.471L48.8935 146.263L48.2069 146.055C46.3784 145.594 44.524 145.22 42.901 145.01C41.4679 144.482 40.0166 144.323 39.0543 144.407Z" fill="#B6B6B6"></path>
            <path d="M30.74 127.927C31.3384 129.045 32.0225 130.189 32.8185 131.274C33.6145 132.358 34.4105 133.443 35.3183 134.467C35.8152 134.993 36.2262 135.492 36.749 135.931C36.9545 136.181 37.2458 136.457 37.4773 136.62C37.6828 136.87 38.0001 137.06 38.2056 137.31C38.7285 137.749 39.2513 138.189 39.8001 138.542C40.0316 138.706 40.3229 138.982 40.5544 139.145L40.9316 139.447L41.3088 139.749C42.4064 140.456 43.5301 141.078 44.6797 141.613C45.2545 141.881 45.8293 142.149 46.3443 142.305C46.9451 142.487 47.4601 142.643 48.0609 142.825C48.5759 142.981 49.2027 143.077 49.7177 143.233C49.9752 143.311 50.2587 143.303 50.5162 143.381C50.7997 143.373 51.0572 143.451 51.3407 143.443C52.4486 143.498 53.4109 143.414 54.3992 143.245C54.1339 142.884 53.9804 142.462 53.6891 142.187C53.5096 141.851 53.2443 141.49 53.0388 141.24C52.5679 140.629 52.1829 140.044 51.7121 139.433C51.4208 139.157 51.2413 138.822 50.9499 138.546C50.6586 138.27 50.3933 137.909 50.1019 137.633C49.4595 136.97 48.7051 136.367 47.8311 135.54C46.8973 134.601 45.5422 133.816 44.0154 132.978C43.2949 132.573 42.4886 132.141 41.6563 131.795C41.2531 131.58 40.8239 131.45 40.4208 131.234C40.0176 131.018 39.5885 130.888 39.1853 130.672C37.5207 129.981 35.8301 129.375 34.3709 128.933C32.9378 128.405 31.6503 128.015 30.74 127.927Z" fill="#B6B6B6"></path>
            <path d="M25.1958 110.036C25.5965 111.188 26.083 112.366 26.6553 113.571C27.2277 114.775 27.826 115.893 28.5362 116.951C29.2464 118.01 30.0424 119.094 30.8643 120.093C31.0698 120.343 31.2753 120.592 31.4808 120.842C31.6863 121.092 31.8918 121.341 32.1233 121.505C32.5343 122.004 32.9713 122.418 33.4942 122.857C33.6996 123.107 33.9311 123.271 34.1626 123.435C34.3941 123.598 34.5996 123.848 34.917 124.038C35.3799 124.366 35.9028 124.805 36.3658 125.133C36.8288 125.46 37.2918 125.788 37.8666 126.056C38.3556 126.298 38.8446 126.539 39.3335 126.781C41.3155 127.663 43.2298 128.149 45.1284 128.068C44.9749 127.647 44.7954 127.311 44.7017 127.002C44.5222 126.666 44.4545 126.271 44.275 125.935C43.916 125.264 43.6429 124.619 43.172 124.008C42.813 123.337 42.3422 122.726 41.8115 122.003C41.5462 121.642 41.2809 121.28 40.9297 120.893C40.6644 120.531 40.3392 120.058 39.988 119.67C39.577 119.171 39.166 118.672 38.5574 118.206C38.0606 117.681 37.4519 117.216 36.8432 116.75C36.2346 116.285 35.5401 115.793 34.8456 115.302C34.1511 114.81 33.4307 114.405 32.7362 113.913C32.0157 113.507 31.3212 113.016 30.6007 112.61C29.8802 112.205 29.2456 111.825 28.5849 111.531C27.2896 110.858 26.0541 110.296 25.1958 110.036Z" fill="#B6B6B6"></path>
            <path d="M25.3031 72.8676C25.233 73.4085 25.2226 74.0614 25.2383 74.6283C25.254 75.1953 25.2437 75.8481 25.2594 76.4151C25.275 76.9821 25.3765 77.5751 25.478 78.1681C25.5796 78.761 25.6811 79.354 25.7826 79.947C25.8841 80.54 26.0116 81.0471 26.1989 81.6661C26.3264 82.1733 26.5138 82.7922 26.7271 83.3254C26.9404 83.8585 27.0679 84.3657 27.2813 84.8988C27.4946 85.432 27.734 85.8793 27.9473 86.4124C28.4 87.3929 28.9645 88.3135 29.555 89.1483C30.1455 89.9831 30.762 90.7321 31.4044 91.3952C32.6894 92.7215 34.0784 93.7044 35.5375 94.1464C35.5739 93.4077 35.5842 92.7549 35.6205 92.0163C35.6308 91.3635 35.5813 90.5988 35.5058 89.92C35.4563 89.1554 35.3808 88.4765 35.1596 87.6599C35.0242 86.8693 34.8291 85.9668 34.5481 85.0383C34.4544 84.7288 34.3347 84.5052 34.2411 84.1957C34.1474 83.8862 34.0277 83.6626 33.8482 83.3271C33.549 82.7679 33.2499 82.2088 32.9507 81.6496C32.5657 81.0645 32.1807 80.4794 31.7957 79.8942C31.4107 79.3091 30.9139 78.7838 30.5289 78.1986C30.3234 77.949 30.0321 77.6733 29.8526 77.3378C29.6471 77.0882 29.4416 76.8385 29.1502 76.5629C28.7392 76.0636 28.2424 75.5383 27.8054 75.1248C27.3684 74.7113 26.8716 74.186 26.4944 73.8843C26.0574 73.4709 25.6803 73.1692 25.3031 72.8676Z" fill="#B6B6B6"></path>
            <path d="M72.6208 142.486C76.7464 148.046 81.4893 160.54 79.0716 168.522C70.7637 162.539 66.7986 151.499 72.6208 142.486Z" fill="#B6B6B6"></path>
            <path d="M61.8105 131.153C62.0498 131.6 62.2033 132.021 62.4427 132.469C62.5025 132.581 62.5623 132.692 62.6222 132.804L62.8017 133.14C62.9213 133.363 63.041 133.587 63.1347 133.897C63.374 134.344 63.5015 134.851 63.7149 135.384C63.8345 135.608 63.9282 135.917 63.962 136.115C64.0817 136.339 64.1754 136.648 64.295 136.872C64.5084 137.405 64.6359 137.912 64.8492 138.445C65.0625 138.978 65.164 139.571 65.3774 140.104C65.7182 141.145 66.0331 142.271 66.2361 143.457C66.7799 145.683 67.0403 147.917 67.129 150.099C67.1604 151.233 67.1319 152.255 66.9058 153.311C66.7655 154.393 66.4795 155.337 66.1675 156.367C65.7904 156.066 65.4132 155.764 65.1219 155.488C64.8305 155.213 64.4534 154.911 64.162 154.635C63.5794 154.084 63.0826 153.559 62.5857 153.034C62.2944 152.758 62.1149 152.422 61.8496 152.061C61.5842 151.699 61.4047 151.364 61.1394 151.003C60.7206 150.22 60.2159 149.411 59.7034 148.319C59.4302 147.674 59.2429 147.055 59.0217 146.238C58.954 145.843 58.8864 145.447 58.8187 145.052C58.751 144.657 58.6833 144.261 58.7275 143.806C58.704 142.956 58.6805 142.105 58.7428 141.281C58.7869 140.826 58.8051 140.456 58.8493 140.001C58.9012 139.83 58.8934 139.546 58.9454 139.374C58.9974 139.203 58.9896 138.919 59.0416 138.748C59.3638 137.065 59.8318 135.52 60.3336 134.173C60.6377 132.859 61.2332 131.821 61.8105 131.153Z" fill="#B6B6B6"></path>
            <path d="M54.4895 118.816C54.7705 119.744 55.0515 120.673 55.3065 121.687C55.5616 122.701 55.7307 123.689 55.9857 124.704C56.1133 125.211 56.1289 125.778 56.2564 126.285C56.2643 126.568 56.2981 126.766 56.3918 127.076C56.3996 127.359 56.5193 127.583 56.5271 127.866C56.5688 128.347 56.6703 128.94 56.686 129.507C56.6938 129.791 56.7017 130.074 56.7095 130.358L56.7772 130.753L56.8448 131.148C56.9022 132.197 56.9336 133.331 56.9051 134.353C56.8349 134.894 56.8506 135.461 56.8064 135.916C56.7363 136.457 56.6921 136.912 56.622 137.453C56.5518 137.994 56.3958 138.509 56.3517 138.964C56.2737 139.222 56.1957 139.479 56.1177 139.737C56.0397 139.994 55.9617 140.252 55.9097 140.423C55.6238 141.367 55.252 142.286 54.7085 143.152C54.4172 142.876 54.066 142.489 53.7747 142.213C53.4833 141.937 53.218 141.576 53.0125 141.326C52.5417 140.715 52.1567 140.13 51.7118 139.433C51.5323 139.097 51.3528 138.762 51.1733 138.426C50.9938 138.091 50.8403 137.67 50.6868 137.248C50.3798 136.406 50.0728 135.563 49.758 134.437C49.4691 133.225 49.3701 131.696 49.5546 130.159C49.6169 129.334 49.8249 128.647 49.973 127.849C50.077 127.506 50.207 127.076 50.311 126.733C50.363 126.561 50.415 126.39 50.493 126.132C50.5449 125.961 50.6229 125.703 50.6749 125.531C51.2287 124.012 51.9282 122.631 52.6615 121.448C53.0853 120.358 53.8524 119.372 54.4895 118.816Z" fill="#B6B6B6"></path>
            <path d="M49.2605 105.144C49.4557 106.046 49.5391 107.008 49.5964 108.057C49.6798 109.019 49.7371 110.067 49.7086 111.089C49.6802 112.111 49.6517 113.133 49.6232 114.156C49.631 114.439 49.553 114.697 49.5869 114.894C49.5947 115.178 49.5167 115.435 49.5246 115.719C49.4544 116.26 49.4103 116.715 49.3401 117.256C49.2699 117.797 49.114 118.312 49.0698 118.767C48.9997 119.308 48.8697 119.737 48.7137 120.252C48.5577 120.767 48.5135 121.222 48.3576 121.737C48.2016 122.252 48.0716 122.681 47.9416 123.11C47.2838 124.973 46.3945 126.671 45.2399 128.008C44.9746 127.647 44.7951 127.311 44.5298 126.95C44.3503 126.614 44.1708 126.279 43.9913 125.943C43.6323 125.272 43.3591 124.627 43.1119 123.897C42.8648 123.166 42.7034 122.461 42.5082 121.558C42.4406 121.163 42.3989 120.682 42.2454 120.261C42.2037 119.78 42.162 119.298 42.1464 118.731C42.1307 118.164 42.141 117.512 42.1773 116.773C42.2735 116.146 42.3956 115.434 42.6036 114.747C42.8116 114.06 43.0196 113.374 43.3394 112.627C43.6332 111.966 44.0388 111.246 44.4185 110.611C44.7981 109.977 45.1777 109.342 45.6432 108.733C46.1087 108.125 46.5482 107.602 46.9877 107.079C47.6508 106.437 48.5636 105.589 49.2605 105.144Z" fill="#B6B6B6"></path>
            <path d="M23.9599 89.9837C23.9756 90.5507 24.0511 91.2295 24.1526 91.8225C24.2541 92.4154 24.3556 93.0084 24.4571 93.6014C24.5587 94.1944 24.746 94.8133 24.8475 95.4063C25.0348 96.0253 25.2222 96.6443 25.4355 97.1774C25.6229 97.7964 25.8362 98.3295 26.1354 98.8887C26.3487 99.4218 26.6479 99.981 26.947 100.54C27.4595 101.632 28.0839 102.665 28.7342 103.611C29.0594 104.085 29.3845 104.558 29.7955 105.057C30.1207 105.531 30.4719 105.918 30.9089 106.332C31.697 107.133 32.4253 107.822 33.2655 108.451C34.0198 109.054 34.886 109.598 35.7183 109.944C36.5246 110.375 37.3829 110.635 38.2672 110.809C38.2177 110.045 38.0564 109.34 37.9808 108.661C37.8195 107.956 37.6582 107.252 37.4968 106.547C37.0025 105.085 36.4223 103.597 35.5768 101.748C35.1501 100.682 34.3801 99.5118 33.4125 98.3754C32.9416 97.7643 32.4448 97.2389 31.974 96.6278C31.4771 96.1025 30.9205 95.4654 30.3378 94.9141C29.2583 93.8374 28.093 92.7348 27.0474 91.8559C25.7261 91.2683 24.7402 90.5011 23.9599 89.9837Z" fill="#B6B6B6"></path>
            <path d="M47.6404 90.6911C47.5962 91.1462 47.5521 91.6014 47.4819 92.1424C47.4378 92.5975 47.3676 93.1385 47.2376 93.5676C47.1935 94.0228 47.0375 94.5378 46.8815 95.0528C46.7255 95.5678 46.6814 96.0229 46.5254 96.5379C46.3694 97.0529 46.2394 97.482 46.0834 97.997C45.9274 98.512 45.7974 98.9412 45.6415 99.4562C45.3555 100.4 44.8719 101.378 44.5001 102.296C44.2582 102.785 44.1283 103.215 43.8864 103.704C43.6706 104.107 43.4288 104.596 43.213 104.999C42.7553 105.891 42.2379 106.671 41.7204 107.452C41.2029 108.232 40.6256 108.9 39.9625 109.543C39.3852 110.211 38.6622 110.742 38.0511 111.213C37.8039 110.482 37.5568 109.751 37.3954 109.047C37.2341 108.342 37.1586 107.663 37.109 106.898C36.984 105.455 37.1945 103.832 37.5089 101.866C37.7611 100.724 38.2109 99.5481 38.9442 98.3647C39.2978 97.8158 39.7633 97.2072 40.1169 96.6584C40.5564 96.1355 40.9959 95.6127 41.5472 95.03C42.512 94.0104 43.6224 93.1286 44.6809 92.4184C45.8511 91.6484 46.8576 91.1099 47.6404 90.6911Z" fill="#B6B6B6"></path>
            <path d="M46.3572 76.0588C45.6685 79.8795 44.1293 83.7238 42.2051 86.9829C41.6876 87.7632 41.256 88.5695 40.6527 89.3238C40.1352 90.1041 39.5579 90.7726 38.9806 91.4411C37.852 92.6923 36.4919 93.7796 35.1759 94.4118C35.0146 93.7069 34.9391 93.0281 34.7778 92.3233C34.7023 91.6445 34.6267 90.9657 34.6631 90.2271C34.6994 89.4884 34.7357 88.7498 34.8838 87.9513C35.032 87.1528 35.292 86.2945 35.5519 85.4362C35.6299 85.1787 35.7079 84.9212 35.8717 84.6897C35.9497 84.4322 36.1136 84.2007 36.3034 83.8834C36.657 83.3346 36.9847 82.8716 37.3383 82.3228C37.7778 81.7999 38.1913 81.3629 38.6307 80.8401C39.1301 80.4291 39.5695 79.9063 40.1547 79.5213C40.4043 79.3158 40.654 79.1103 40.9036 78.9048C41.1533 78.6993 41.4888 78.5198 41.7384 78.3143C42.3236 77.9293 42.7969 77.6041 43.356 77.305C43.9152 77.0058 44.3625 76.7665 44.8956 76.5531C45.5146 76.3658 45.9619 76.1265 46.3572 76.0588Z" fill="#B6B6B6"></path>
            <path d="M28.4238 55.4464C28.2679 55.9614 28.1717 56.5882 28.1015 57.1292C28.0314 57.6702 27.9352 58.297 27.9509 58.864C27.8964 59.972 27.9018 61.1918 28.045 62.2659C28.1622 63.4259 28.3054 64.5 28.5604 65.5143C28.7894 66.6144 29.1302 67.6547 29.4971 68.6092C29.8639 69.5636 30.3166 70.5441 30.7354 71.3269C31.2141 72.2215 31.6589 72.9185 32.2754 73.6675C33.3627 75.0276 34.5281 76.1302 35.7375 76.7776C35.9195 76.1768 36.0157 75.55 36.052 74.8113C36.0701 74.442 36.0623 74.1585 36.0805 73.7892C36.0986 73.4199 36.1168 73.0506 36.0231 72.7411C35.9736 71.9764 35.9241 71.2118 35.7887 70.4211C35.6534 69.6305 35.544 68.754 35.4607 67.7917C35.3332 67.2846 35.2317 66.6916 35.1042 66.1845C34.8908 65.6513 34.7035 65.0323 34.4902 64.4992C34.0036 63.3211 33.4053 62.2028 32.7211 61.0585C32.422 60.4994 32.037 59.9142 31.7118 59.4409C31.3008 58.9416 31.0016 58.3825 30.5906 57.8831C30.2655 57.4098 29.8545 56.9105 29.5033 56.5231C29.1522 56.1356 28.801 55.7481 28.4238 55.4464Z" fill="#B6B6B6"></path>
            <path d="M49.4551 60.8799C48.3112 64.6565 46.1633 68.0352 43.6903 70.9407C43.0012 71.669 42.4239 72.3375 41.7867 72.8942C41.1236 73.5366 40.4864 74.0933 39.7895 74.5381C38.4554 75.5396 37.1134 76.2576 35.7637 76.6922C35.6881 76.0133 35.7843 75.3865 35.7946 74.7337C35.8128 74.3644 35.8908 74.1069 35.9948 73.7636C36.0988 73.4202 36.2027 73.0769 36.3067 72.7336C36.5147 72.047 36.8345 71.3005 37.1283 70.6398C37.4481 69.8933 37.8538 69.1728 38.2854 68.3665C38.769 67.3886 39.4842 66.5744 40.397 65.7264C41.1979 64.9382 42.2824 64.1423 43.3148 63.5179C43.7881 63.1928 44.3473 62.8936 44.9064 62.5944C45.4656 62.2953 45.9987 62.0819 46.5319 61.8686C47.065 61.6552 47.5981 61.4419 48.1053 61.3144C48.5266 61.1609 49.0338 61.0334 49.4551 60.8799Z" fill="#B6B6B6"></path>
            <path d="M34.885 38.7564C33.0701 42.892 32.8002 47.4955 33.5289 51.277C33.6981 52.2653 33.9791 53.1938 34.2003 54.0104C34.5073 54.853 34.8143 55.6957 35.1733 56.3667C35.9511 57.8204 36.833 58.9309 37.7329 59.672C38.838 57.5704 39.0691 54.6418 39.1987 51.1202C39.2272 50.0981 39.084 49.024 38.855 47.9238C38.626 46.8237 38.3112 45.6976 37.8845 44.6313C37.4578 43.565 37.0311 42.4987 36.4926 41.4923C35.9541 40.4858 35.4755 39.5912 34.885 38.7564Z" fill="#B6B6B6"></path>
            <path d="M53.6484 47.3447C51.8439 50.8275 49.0874 53.7408 46.1695 55.9493C45.4465 56.4799 44.7236 57.0106 43.9408 57.4294C43.2438 57.8743 42.461 58.2931 41.7042 58.6261C40.2764 59.3181 38.9266 59.7526 37.7069 59.758C38.0395 57.4224 39.6699 54.8239 41.8672 52.2098C42.5564 51.4815 43.3313 50.7792 44.278 50.1288C45.1986 49.5643 46.2909 49.0518 47.2713 48.5991C48.3376 48.1725 49.4637 47.8576 50.5639 47.6286C51.638 47.4854 52.7121 47.3422 53.6484 47.3447Z" fill="#B6B6B6"></path>
            <path d="M42.5741 25.4361C40.3821 29.27 39.709 33.6577 40.1204 37.2494C40.5058 40.9269 41.778 43.8423 43.2944 45.3324C44.4932 43.5403 45.0598 40.4322 45.4807 37.1863C45.5249 36.7311 45.595 36.1902 45.5534 35.709C45.5975 35.2539 45.5818 34.6869 45.4543 34.1797C45.4126 33.6986 45.2851 33.1914 45.2695 32.6244C45.142 32.1173 45.0145 31.6101 44.887 31.103C44.3509 29.1602 43.4716 27.1135 42.5741 25.4361Z" fill="#B6B6B6"></path>
            <path d="M59.6084 35.0938C57.5464 38.4985 54.5245 41.0503 51.4791 42.7517C48.4078 44.5388 45.3129 45.4755 43.2946 45.3327C43.7469 43.2208 45.8923 40.7783 48.329 38.6115C48.7165 38.2604 49.078 37.995 49.4394 37.7297C49.8009 37.4644 50.2482 37.225 50.6955 36.9857C51.1429 36.7464 51.5902 36.507 52.0973 36.3795C52.6305 36.1662 53.0518 36.0127 53.5589 35.8852C55.5277 35.2633 57.65 35.0628 59.6084 35.0938Z" fill="#B6B6B6"></path>
            <path d="M51.3945 13.0215C48.7993 16.6396 47.9468 20.6918 48.0148 24.1796C48.0619 25.8805 48.3325 27.4618 48.7671 28.8116C49.2016 30.1614 49.7401 31.1678 50.2967 31.8049C51.5632 30.4081 52.3794 27.0946 53.1177 24.0385C53.8402 20.4154 52.9399 16.5819 51.3945 13.0215Z" fill="#B6B6B6"></path>
            <path d="M66.6318 23.3525C64.3383 26.5935 61.0771 28.698 58.0499 30.03C56.5363 30.696 54.9889 31.1644 53.6052 31.4012C52.3074 31.6641 51.0617 31.7552 50.2632 31.6071C50.8091 29.8046 53.4097 27.4063 55.9141 25.6348C58.8059 23.5122 62.793 23.0331 66.6318 23.3525Z" fill="#B6B6B6"></path>
            <path d="M68.3072 6.99349C65.0407 7.8782 61.6858 9.6732 59.1946 12.948C56.7034 16.2228 56.5503 18.8938 57.3257 21.2839C59.8433 21.0157 62.2255 19.9569 64.5789 16.8278C66.9582 13.6128 67.9123 10.1536 68.3072 6.99349Z" fill="#B6B6B6"></path>
          </svg>
        </div>
        <div class="review-feather-text">
          <p>Social Review</p>
          <p>Boost Conversion</p>
          <div class="r-loved-line"></div>
          <p>Use PhotoADKing! Create your first design now!</p>
        </div>
        <div class="review-feather">
          <svg viewBox="0 0 124 173" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M73.4509 160.198C66.6983 166.367 53.5996 173.239 44.1981 168.496C51.4814 161.605 63.6434 157.828 73.4509 160.198Z" fill="#B6B6B6"></path>
            <path d="M84.1293 144.407C83.7183 144.906 83.3073 145.405 82.8963 145.905C82.7506 146.043 82.6908 146.154 82.5451 146.292C82.3995 146.43 82.3396 146.542 82.194 146.68C81.9885 146.929 81.6972 147.205 81.4917 147.455C80.9948 147.98 80.472 148.419 79.9752 148.945C79.6838 149.22 79.4524 149.384 79.161 149.66C78.8697 149.936 78.6382 150.099 78.3469 150.375C77.824 150.814 77.2154 151.28 76.6666 151.634C76.1437 152.073 75.5091 152.453 74.9603 152.806C73.7768 153.54 72.5673 154.187 71.3578 154.835C68.8869 155.958 66.4238 156.798 63.9764 157.07C62.8086 157.237 61.5888 157.231 60.4548 157.2C59.2948 157.083 58.1947 156.854 57.2402 156.487C57.5914 156.099 57.8567 155.738 58.1481 155.462C58.4134 155.101 58.7047 154.825 58.9961 154.549C59.4669 153.938 60.0495 153.387 60.5724 152.948C60.8637 152.672 61.181 152.482 61.4724 152.206C61.7637 151.931 62.1408 151.629 62.4582 151.439C63.1527 150.948 63.9928 150.319 65.0905 149.611C65.6393 149.258 66.3598 148.852 67.1063 148.532C67.4236 148.342 67.8527 148.212 68.2819 148.082C68.711 147.952 69.1142 147.737 69.5434 147.607C70.4017 147.347 71.234 147.001 72.2041 146.801C72.6333 146.671 73.1483 146.515 73.6034 146.471L74.2901 146.263L74.9767 146.055C76.8052 145.594 78.6596 145.22 80.2826 145.01C81.7157 144.482 83.167 144.323 84.1293 144.407Z" fill="#B6B6B6"></path>
            <path d="M92.4436 127.927C91.8452 129.045 91.1611 130.189 90.3651 131.274C89.5691 132.358 88.7731 133.443 87.8652 134.467C87.3684 134.993 86.9574 135.492 86.4346 135.931C86.2291 136.181 85.9378 136.457 85.7063 136.62C85.5008 136.87 85.1834 137.06 84.978 137.31C84.4551 137.749 83.9323 138.189 83.3835 138.542C83.152 138.706 82.8606 138.982 82.6291 139.145L82.252 139.447L81.8748 139.749C80.7772 140.456 79.6535 141.078 78.5039 141.613C77.9291 141.881 77.3543 142.149 76.8393 142.305C76.2385 142.487 75.7235 142.643 75.1227 142.825C74.6077 142.981 73.9809 143.077 73.4659 143.233C73.2084 143.311 72.9249 143.303 72.6674 143.381C72.3839 143.373 72.1264 143.451 71.8429 143.443C70.7349 143.498 69.7726 143.414 68.7843 143.245C69.0497 142.884 69.2032 142.462 69.4945 142.187C69.674 141.851 69.9393 141.49 70.1448 141.24C70.6157 140.629 71.0007 140.044 71.4715 139.433C71.7628 139.157 71.9423 138.822 72.2337 138.546C72.525 138.27 72.7903 137.909 73.0817 137.633C73.7241 136.97 74.4785 136.367 75.3525 135.54C76.2863 134.601 77.6414 133.816 79.1682 132.978C79.8887 132.573 80.695 132.141 81.5273 131.795C81.9305 131.58 82.3597 131.45 82.7628 131.234C83.166 131.018 83.5951 130.888 83.9983 130.672C85.6629 129.981 87.3535 129.375 88.8127 128.933C90.2458 128.405 91.5333 128.015 92.4436 127.927Z" fill="#B6B6B6"></path>
            <path d="M97.9878 110.036C97.5871 111.188 97.1006 112.366 96.5282 113.571C95.9559 114.775 95.3576 115.893 94.6474 116.951C93.9372 118.01 93.1412 119.094 92.3192 120.093C92.1137 120.343 91.9083 120.592 91.7028 120.842C91.4973 121.092 91.2918 121.341 91.0603 121.505C90.6493 122.004 90.2123 122.418 89.6894 122.857C89.4839 123.107 89.2525 123.271 89.021 123.435C88.7895 123.598 88.584 123.848 88.2666 124.038C87.8036 124.366 87.2808 124.805 86.8178 125.133C86.3548 125.46 85.8918 125.788 85.317 126.056C84.828 126.298 84.339 126.539 83.8501 126.781C81.8681 127.663 79.9538 128.149 78.0552 128.068C78.2087 127.647 78.3882 127.311 78.4819 127.002C78.6614 126.666 78.7291 126.271 78.9086 125.935C79.2676 125.264 79.5407 124.619 80.0116 124.008C80.3706 123.337 80.8414 122.726 81.3721 122.003C81.6374 121.642 81.9027 121.28 82.2539 120.893C82.5192 120.531 82.8444 120.058 83.1955 119.67C83.6065 119.171 84.0175 118.672 84.6262 118.206C85.123 117.681 85.7317 117.216 86.3403 116.75C86.949 116.285 87.6435 115.793 88.338 115.302C89.0325 114.81 89.7529 114.405 90.4474 113.913C91.1679 113.507 91.8624 113.016 92.5829 112.61C93.3034 112.205 93.938 111.825 94.5987 111.531C95.894 110.858 97.1295 110.296 97.9878 110.036Z" fill="#B6B6B6"></path>
            <path d="M97.8805 72.8676C97.9506 73.4085 97.961 74.0614 97.9453 74.6283C97.9296 75.1953 97.9399 75.8481 97.9242 76.4151C97.9086 76.9821 97.8071 77.5751 97.7055 78.1681C97.604 78.761 97.5025 79.354 97.401 79.947C97.2995 80.54 97.172 81.0471 96.9847 81.6661C96.8572 82.1733 96.6698 82.7922 96.4565 83.3254C96.2431 83.8585 96.1156 84.3657 95.9023 84.8988C95.689 85.432 95.4496 85.8793 95.2363 86.4124C94.7836 87.3929 94.2191 88.3135 93.6286 89.1483C93.0381 89.9831 92.4216 90.7321 91.7791 91.3952C90.4942 92.7215 89.1052 93.7044 87.6461 94.1464C87.6097 93.4077 87.5994 92.7549 87.5631 92.0163C87.5528 91.3635 87.6023 90.5988 87.6778 89.92C87.7273 89.1554 87.8028 88.4765 88.024 87.6599C88.1593 86.8693 88.3545 85.9668 88.6355 85.0383C88.7292 84.7288 88.8489 84.5052 88.9425 84.1957C89.0362 83.8862 89.1559 83.6626 89.3354 83.3271C89.6345 82.7679 89.9337 82.2088 90.2329 81.6496C90.6179 81.0645 91.0029 80.4794 91.3879 79.8942C91.7729 79.3091 92.2697 78.7838 92.6547 78.1986C92.8602 77.949 93.1515 77.6733 93.331 77.3378C93.5365 77.0882 93.742 76.8385 94.0334 76.5629C94.4444 76.0636 94.9412 75.5383 95.3782 75.1248C95.8152 74.7113 96.312 74.186 96.6892 73.8843C97.1262 73.4709 97.5033 73.1692 97.8805 72.8676Z" fill="#B6B6B6"></path>
            <path d="M50.5628 142.486C46.4372 148.046 41.6943 160.54 44.112 168.522C52.4199 162.539 56.385 151.499 50.5628 142.486Z" fill="#B6B6B6"></path>
            <path d="M61.3731 131.153C61.1338 131.6 60.9803 132.021 60.7409 132.469C60.6811 132.581 60.6213 132.692 60.5614 132.804L60.3819 133.14C60.2623 133.363 60.1426 133.587 60.0489 133.897C59.8096 134.344 59.6821 134.851 59.4687 135.384C59.3491 135.608 59.2554 135.917 59.2216 136.115C59.1019 136.339 59.0082 136.648 58.8886 136.872C58.6752 137.405 58.5477 137.912 58.3344 138.445C58.121 138.978 58.0195 139.571 57.8062 140.104C57.4654 141.145 57.1505 142.271 56.9475 143.457C56.4036 145.683 56.1433 147.917 56.0546 150.099C56.0232 151.233 56.0517 152.255 56.2778 153.311C56.4181 154.393 56.7041 155.337 57.0161 156.367C57.3932 156.066 57.7704 155.764 58.0617 155.488C58.3531 155.213 58.7302 154.911 59.0215 154.635C59.6042 154.084 60.101 153.559 60.5979 153.034C60.8892 152.758 61.0687 152.422 61.334 152.061C61.5994 151.699 61.7789 151.364 62.0442 151.003C62.463 150.22 62.9677 149.411 63.4802 148.319C63.7534 147.674 63.9407 147.055 64.1619 146.238C64.2296 145.843 64.2972 145.447 64.3649 145.052C64.4326 144.657 64.5002 144.261 64.4561 143.806C64.4796 142.956 64.5031 142.105 64.4408 141.281C64.3967 140.826 64.3785 140.456 64.3343 140.001C64.2823 139.83 64.2902 139.546 64.2382 139.374C64.1862 139.203 64.194 138.919 64.142 138.748C63.8197 137.065 63.3518 135.52 62.85 134.173C62.5459 132.859 61.9504 131.821 61.3731 131.153Z" fill="#B6B6B6"></path>
            <path d="M68.6941 118.816C68.4131 119.744 68.1321 120.673 67.877 121.687C67.622 122.701 67.4529 123.689 67.1978 124.704C67.0703 125.211 67.0547 125.778 66.9272 126.285C66.9193 126.568 66.8855 126.766 66.7918 127.076C66.784 127.359 66.6643 127.583 66.6565 127.866C66.6148 128.347 66.5133 128.94 66.4976 129.507C66.4898 129.791 66.4819 130.074 66.4741 130.358L66.4064 130.753L66.3387 131.148C66.2814 132.197 66.25 133.331 66.2785 134.353C66.3487 134.894 66.333 135.461 66.3772 135.916C66.4473 136.457 66.4915 136.912 66.5616 137.453C66.6318 137.994 66.7878 138.509 66.8319 138.964C66.9099 139.222 66.9879 139.479 67.0659 139.737C67.1439 139.994 67.2219 140.252 67.2739 140.423C67.5598 141.367 67.9316 142.286 68.4751 143.152C68.7664 142.876 69.1176 142.489 69.4089 142.213C69.7003 141.937 69.9656 141.576 70.1711 141.326C70.6419 140.715 71.0269 140.13 71.4717 139.433C71.6512 139.097 71.8308 138.762 72.0103 138.426C72.1898 138.091 72.3433 137.67 72.4968 137.248C72.8038 136.406 73.1108 135.563 73.4256 134.437C73.7145 133.225 73.8135 131.696 73.629 130.159C73.5667 129.334 73.3587 128.647 73.2106 127.849C73.1066 127.506 72.9766 127.076 72.8726 126.733C72.8206 126.561 72.7686 126.39 72.6906 126.132C72.6386 125.961 72.5607 125.703 72.5087 125.531C71.9549 124.012 71.2554 122.631 70.5221 121.448C70.0983 120.358 69.3312 119.372 68.6941 118.816Z" fill="#B6B6B6"></path>
            <path d="M73.9231 105.144C73.7279 106.046 73.6445 107.008 73.5872 108.057C73.5038 109.019 73.4465 110.067 73.475 111.089C73.5034 112.111 73.5319 113.133 73.5604 114.156C73.5526 114.439 73.6305 114.697 73.5967 114.894C73.5889 115.178 73.6669 115.435 73.659 115.719C73.7292 116.26 73.7733 116.715 73.8435 117.256C73.9136 117.797 74.0696 118.312 74.1138 118.767C74.1839 119.308 74.3139 119.737 74.4699 120.252C74.6259 120.767 74.67 121.222 74.826 121.737C74.982 122.252 75.112 122.681 75.242 123.11C75.8998 124.973 76.789 126.671 77.9436 128.008C78.209 127.647 78.3885 127.311 78.6538 126.95C78.8333 126.614 79.0128 126.279 79.1923 125.943C79.5513 125.272 79.8245 124.627 80.0717 123.897C80.3188 123.166 80.4802 122.461 80.6754 121.558C80.743 121.163 80.7847 120.682 80.9382 120.261C80.9799 119.78 81.0216 119.298 81.0372 118.731C81.0529 118.164 81.0426 117.512 81.0063 116.773C80.9101 116.146 80.788 115.434 80.58 114.747C80.372 114.06 80.164 113.374 79.8442 112.627C79.5504 111.966 79.1448 111.246 78.7651 110.611C78.3855 109.977 78.0058 109.342 77.5404 108.733C77.0749 108.125 76.6354 107.602 76.1959 107.079C75.5328 106.437 74.62 105.589 73.9231 105.144Z" fill="#B6B6B6"></path>
            <path d="M99.2237 89.9837C99.208 90.5507 99.1325 91.2295 99.031 91.8225C98.9295 92.4154 98.828 93.0084 98.7264 93.6014C98.6249 94.1944 98.4376 94.8133 98.3361 95.4063C98.1488 96.0253 97.9614 96.6443 97.7481 97.1774C97.5607 97.7964 97.3474 98.3295 97.0482 98.8887C96.8349 99.4218 96.5357 99.981 96.2365 100.54C95.724 101.632 95.0997 102.665 94.4494 103.611C94.1242 104.085 93.799 104.558 93.3881 105.057C93.0629 105.531 92.7117 105.918 92.2747 106.332C91.4866 107.133 90.7583 107.822 89.9181 108.451C89.1638 109.054 88.2976 109.598 87.4653 109.944C86.659 110.375 85.8007 110.635 84.9164 110.809C84.9659 110.045 85.1272 109.34 85.2027 108.661C85.3641 107.956 85.5254 107.252 85.6868 106.547C86.1811 105.085 86.7613 103.597 87.6068 101.748C88.0335 100.682 88.8035 99.5118 89.7711 98.3754C90.242 97.7643 90.7388 97.2389 91.2096 96.6278C91.7065 96.1025 92.2631 95.4654 92.8458 94.9141C93.9253 93.8374 95.0906 92.7348 96.1362 91.8559C97.4575 91.2683 98.4434 90.5011 99.2237 89.9837Z" fill="#B6B6B6"></path>
            <path d="M75.5432 90.6911C75.5874 91.1462 75.6315 91.6014 75.7017 92.1424C75.7458 92.5975 75.816 93.1385 75.946 93.5676C75.9901 94.0228 76.1461 94.5378 76.3021 95.0528C76.4581 95.5678 76.5022 96.0229 76.6582 96.5379C76.8142 97.0529 76.9442 97.482 77.1002 97.997C77.2562 98.512 77.3861 98.9412 77.5421 99.4562C77.8281 100.4 78.3117 101.378 78.6835 102.296C78.9254 102.785 79.0553 103.215 79.2972 103.704C79.513 104.107 79.7548 104.596 79.9706 104.999C80.4282 105.891 80.9457 106.671 81.4632 107.452C81.9806 108.232 82.558 108.9 83.2211 109.543C83.7984 110.211 84.5214 110.742 85.1325 111.213C85.3797 110.482 85.6268 109.751 85.7882 109.047C85.9495 108.342 86.025 107.663 86.0745 106.898C86.1996 105.455 85.9891 103.832 85.6747 101.866C85.4225 100.724 84.9727 99.5481 84.2394 98.3647C83.8858 97.8158 83.4203 97.2072 83.0667 96.6584C82.6272 96.1355 82.1877 95.6127 81.6364 95.03C80.6716 94.0104 79.5612 93.1286 78.5027 92.4184C77.3325 91.6484 76.326 91.1099 75.5432 90.6911Z" fill="#B6B6B6"></path>
            <path d="M76.8264 76.0588C77.5151 79.8795 79.0543 83.7238 80.9785 86.9829C81.496 87.7632 81.9276 88.5695 82.5309 89.3238C83.0484 90.1041 83.6257 90.7726 84.203 91.4411C85.3316 92.6923 86.6917 93.7796 88.0076 94.4118C88.169 93.7069 88.2445 93.0281 88.4058 92.3233C88.4813 91.6445 88.5569 90.9657 88.5205 90.2271C88.4842 89.4884 88.4479 88.7498 88.2998 87.9513C88.1516 87.1528 87.8916 86.2945 87.6317 85.4362C87.5537 85.1787 87.4757 84.9212 87.3119 84.6897C87.2339 84.4322 87.07 84.2007 86.8802 83.8834C86.5266 83.3346 86.1989 82.8716 85.8453 82.3228C85.4058 81.7999 84.9923 81.3629 84.5529 80.8401C84.0535 80.4291 83.6141 79.9063 83.0289 79.5213C82.7793 79.3158 82.5296 79.1103 82.28 78.9048C82.0303 78.6993 81.6948 78.5198 81.4452 78.3143C80.86 77.9293 80.3867 77.6041 79.8276 77.305C79.2684 77.0058 78.8211 76.7665 78.288 76.5531C77.669 76.3658 77.2217 76.1265 76.8264 76.0588Z" fill="#B6B6B6"></path>
            <path d="M94.7598 55.4464C94.9157 55.9614 95.0119 56.5882 95.082 57.1292C95.1522 57.6702 95.2484 58.297 95.2327 58.864C95.2872 59.972 95.2818 61.1918 95.1386 62.2659C95.0214 63.4259 94.8782 64.5 94.6232 65.5143C94.3942 66.6144 94.0534 67.6547 93.6865 68.6092C93.3197 69.5636 92.867 70.5441 92.4482 71.3269C91.9695 72.2215 91.5247 72.9185 90.9082 73.6675C89.8209 75.0276 88.6555 76.1302 87.4461 76.7776C87.2641 76.1768 87.1679 75.55 87.1316 74.8113C87.1135 74.442 87.1213 74.1585 87.1031 73.7892C87.085 73.4199 87.0668 73.0506 87.1605 72.7411C87.21 71.9764 87.2595 71.2118 87.3949 70.4211C87.5302 69.6305 87.6395 68.754 87.7229 67.7917C87.8504 67.2846 87.9519 66.6916 88.0794 66.1845C88.2928 65.6513 88.4801 65.0323 88.6934 64.4992C89.1799 63.3211 89.7783 62.2028 90.4624 61.0585C90.7616 60.4994 91.1466 59.9142 91.4718 59.4409C91.8828 58.9416 92.1819 58.3825 92.5929 57.8831C92.9181 57.4098 93.3291 56.9105 93.6803 56.5231C94.0314 56.1356 94.3826 55.7481 94.7598 55.4464Z" fill="#B6B6B6"></path>
            <path d="M73.7285 60.8799C74.8724 64.6565 77.0203 68.0352 79.4933 70.9407C80.1824 71.669 80.7597 72.3375 81.3969 72.8942C82.06 73.5366 82.6971 74.0933 83.3941 74.5381C84.7282 75.5396 86.0702 76.2576 87.4199 76.6922C87.4955 76.0133 87.3993 75.3865 87.389 74.7337C87.3708 74.3644 87.2928 74.1069 87.1888 73.7636C87.0848 73.4202 86.9809 73.0769 86.8769 72.7336C86.6689 72.047 86.3491 71.3005 86.0553 70.6398C85.7355 69.8933 85.3298 69.1728 84.8982 68.3665C84.4145 67.3886 83.6994 66.5744 82.7866 65.7264C81.9857 64.9382 80.9012 64.1423 79.8688 63.5179C79.3955 63.1928 78.8363 62.8936 78.2772 62.5944C77.718 62.2953 77.1849 62.0819 76.6517 61.8686C76.1186 61.6552 75.5854 61.4419 75.0783 61.3144C74.657 61.1609 74.1498 61.0334 73.7285 60.8799Z" fill="#B6B6B6"></path>
            <path d="M88.2986 38.7564C90.1135 42.892 90.3834 47.4955 89.6547 51.277C89.4855 52.2653 89.2045 53.1938 88.9833 54.0104C88.6763 54.853 88.3693 55.6957 88.0103 56.3667C87.2325 57.8204 86.3506 58.9309 85.4507 59.672C84.3456 57.5704 84.1145 54.6418 83.9849 51.1202C83.9564 50.0981 84.0996 49.024 84.3286 47.9238C84.5576 46.8237 84.8724 45.6976 85.2991 44.6313C85.7258 43.565 86.1525 42.4987 86.691 41.4923C87.2295 40.4858 87.7081 39.5912 88.2986 38.7564Z" fill="#B6B6B6"></path>
            <path d="M69.5352 47.3447C71.3397 50.8275 74.0962 53.7408 77.0141 55.9493C77.737 56.4799 78.46 57.0106 79.2428 57.4294C79.9398 57.8743 80.7226 58.2931 81.4794 58.6261C82.9072 59.3181 84.2569 59.7526 85.4767 59.758C85.1441 57.4224 83.5137 54.8239 81.3164 52.2098C80.6272 51.4815 79.8523 50.7792 78.9056 50.1288C77.985 49.5643 76.8927 49.0518 75.9123 48.5991C74.846 48.1725 73.7198 47.8576 72.6197 47.6286C71.5456 47.4854 70.4715 47.3422 69.5352 47.3447Z" fill="#B6B6B6"></path>
            <path d="M80.6095 25.4361C82.8015 29.27 83.4746 33.6577 83.0632 37.2494C82.6778 40.9269 81.4056 43.8423 79.8891 45.3324C78.6904 43.5403 78.1238 40.4322 77.7029 37.1863C77.6587 36.7311 77.5886 36.1902 77.6302 35.709C77.5861 35.2539 77.6018 34.6869 77.7293 34.1797C77.7709 33.6986 77.8985 33.1914 77.9141 32.6244C78.0416 32.1173 78.1691 31.6101 78.2966 31.103C78.8327 29.1602 79.712 27.1135 80.6095 25.4361Z" fill="#B6B6B6"></path>
            <path d="M63.5752 35.0938C65.6372 38.4985 68.6591 41.0503 71.7045 42.7517C74.7758 44.5388 77.8707 45.4755 79.889 45.3327C79.4367 43.2208 77.2913 40.7783 74.8546 38.6115C74.4671 38.2604 74.1056 37.995 73.7442 37.7297C73.3827 37.4644 72.9354 37.225 72.4881 36.9857C72.0407 36.7464 71.5934 36.507 71.0863 36.3795C70.5531 36.1662 70.1318 36.0127 69.6247 35.8852C67.6559 35.2633 65.5336 35.0628 63.5752 35.0938Z" fill="#B6B6B6"></path>
            <path d="M71.7891 13.0215C74.3843 16.6396 75.2368 20.6918 75.1688 24.1796C75.1217 25.8805 74.851 27.4618 74.4165 28.8116C73.982 30.1614 73.4435 31.1678 72.8869 31.8049C71.6204 30.4081 70.8042 27.0946 70.0659 24.0385C69.3434 20.4154 70.2437 16.5819 71.7891 13.0215Z" fill="#B6B6B6"></path>
            <path d="M56.5518 23.3525C58.8453 26.5935 62.1065 28.698 65.1337 30.03C66.6473 30.696 68.1947 31.1644 69.5784 31.4012C70.8761 31.6641 72.1219 31.7552 72.9204 31.6071C72.3745 29.8046 69.7739 27.4063 67.2695 25.6348C64.3777 23.5122 60.3906 23.0331 56.5518 23.3525Z" fill="#B6B6B6"></path>
            <path d="M54.8764 6.99349C58.1429 7.8782 61.4978 9.6732 63.989 12.948C66.4802 16.2228 66.6333 18.8938 65.8579 21.2839C63.3403 21.0157 60.9581 19.9569 58.6047 16.8278C56.2254 13.6128 55.2713 10.1536 54.8764 6.99349Z" fill="#B6B6B6"></path>
          </svg>
        </div>
      </div>
    </div>
    <div class="loved-review-section-tab">
      <div class="loved-review-box">
        <a target="_blank" href="https://www.capterra.com/p/187414/PhotoADKing/reviews/2827158/" rel="noreferrer nofollow">
          <div class="user-review-box">
            <div class="c-rev-div">
              <div class="c-review-top-quote">
                <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                  <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                </svg>
              </div>
              <p class="c-review-text"> This App has helped me to create amazing and beautiful designs for my online business and colleagues as well which has also given job opportunities as well as it's ... </p>
            </div>
            <div class="c-rev-div">
              <div class="d-flex w-65">
                <div class="review-user-avtar">
                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_first.png" alt="Emmanuel R. profile image" loading="lazy">
                </div>
                <div class="review-user-details">
                  <p>Emmanuel R.</p>
                  <p>Business Owner</p>
                  <p>(USA)</p>
                </div>
              </div>
              <div class="w-35 position-relative c-rating-box">
                <div class="cd-block text-right">
                  <svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 11.2335L12.6714 11.236L20.3765 11.2374V3.604L0 11.2335Z" fill="#FF9D28"></path>
                    <path d="M20.3765 3.60446V30.5541L29.9999 0L20.3765 3.60446Z" fill="#68C5ED"></path>
                    <path d="M20.3765 11.2376L12.6714 11.2363L20.3765 30.5539V11.2376Z" fill="#044D80"></path>
                    <path d="M0 11.2339L14.6473 16.193L12.6714 11.2364L0 11.2339Z" fill="#E54747"></path>
                  </svg>
                </div>
                <div class="cd-block text-right">
                  <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                    <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                    <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                    <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                    <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="loved-review-box">
        <a target="_blank" href="https://www.trustpilot.com/reviews/5f844e54798e6f0bcc428385" rel="noreferrer nofollow">
          <div class="user-review-box">
            <div class="c-rev-div">
              <div class="c-review-top-quote">
                <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                  <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                </svg>
              </div>
              <p class="c-review-text"> PhotoADKing is great. It permits you to be so inventive and gives you the instruments to do as such. I totally love it particularly for planning huge Posters for promotion. </p>
            </div>
            <div class="c-rev-div">
              <div class="d-flex w-65">
                <div class="review-user-avtar">
                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_second.png" alt="Akshay Pubariya profile image" loading="lazy">
                </div>
                <div class="review-user-details">
                  <p>Akshay Purabiya</p>
                  <p>Digital Marketer</p>
                  <p>(India)</p>
                </div>
              </div>
              <div class="w-35 position-relative c-rating-box">
                <div class="cd-block text-right">
                  <svg width="27" height="26" viewBox="0 0 27 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27 0H0.9375V26H27V0Z" fill="#00B67A"></path>
                    <path d="M13.9686 17.5231L17.9322 16.521L19.5883 21.6127L13.9686 17.5231ZM23.0904 10.9419H16.1133L13.9686 4.3877L11.8238 10.9419H4.84668L10.4936 15.0044L8.34883 21.5585L13.9957 17.496L17.4707 15.0044L23.0904 10.9419Z" fill="white"></path>
                  </svg>
                </div>
                <div class="cd-block text-right">
                  <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                    <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                    <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                    <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                    <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="loved-review-box">
        <a target="_blank" href="https://www.g2.com/products/photoadking/reviews/photoadking-review-4724327" rel="noreferrer nofollow">
          <div class="user-review-box">
            <div class="c-rev-div">
              <div class="c-review-top-quote">
                <svg viewBox="0 0 38 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.755 2.91781L12.8499 0C5.23374 3.28678 0 9.93668 0 17.3584C0 21.5782 1.18057 24.1822 3.37551 26.4978C4.75639 27.961 6.9428 29 9.2315 29C13.3486 29 16.69 25.6751 16.69 21.5782C16.69 17.6765 13.6597 14.5339 9.81539 14.1777C9.12495 14.1141 8.43024 14.131 7.76963 14.2159C7.76963 12.8927 7.64603 6.74744 14.755 2.91781Z" fill="#1980FF"></path>
                  <path d="M36.0651 2.91781L34.1599 0C26.5437 3.28678 21.31 9.93668 21.31 17.3584C21.31 21.5782 22.4906 24.1822 24.6855 26.4978C26.0664 27.961 28.2528 29 30.5415 29C34.6586 29 38 25.6751 38 21.5782C38 17.6765 34.9697 14.5339 31.1254 14.1777C30.4349 14.1141 29.7402 14.131 29.0796 14.2159C29.0796 12.8927 28.956 6.74744 36.0651 2.91781Z" fill="#1980FF"></path>
                </svg>
              </div>
              <p class="c-review-text"> PhotoADKing is ideal for creating posters, presentations, graphics, documents, and visual content as there are many free templates available to use, and the templates are... </p>
            </div>
            <div class="c-rev-div">
              <div class="d-flex w-65">
                <div class="review-user-avtar">
                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/user_review_third.png" alt="John w profile image" loading="lazy">
                </div>
                <div class="review-user-details">
                  <p>John W</p>
                  <p>Content Writer</p>
                  <p>(UK)</p>
                </div>
              </div>
              <div class="w-35 position-relative c-rating-box">
                <div class="cd-block text-right">
                  <svg class="c-diff-icon" version="1.1" id="layer" xmlns="http://www.w3.org/2000/svg" height="40" width="40" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 652 652" style="enable-background:new 0 0 652 652;" xml:space="preserve">
                    <style type="text/css">
                      .stnewg0 {
                        fill: #FF492C;
                      }

                      .stnewg1 {
                        fill: #FFFFFF;
                      }
                    </style>
                    <path class="stnewg0" d="M571.4,321.4c0,138.1-111.9,250-250,250c-138.1,0-250-111.9-250-250c0-138.1,111.9-250,250-250
    C459.5,71.4,571.4,183.4,571.4,321.4"></path>
                    <path class="stnewg1" d="M429.6,280.6H365v-3c0-11,2.2-20.1,6.6-27.2c4.4-7.2,12-13.5,23-19.1l5-2.5c8.9-4.5,11.2-8.4,11.2-13
    c0-5.5-4.8-9.5-12.5-9.5c-9.2,0-16.1,4.8-20.9,14.5L365,208.4c2.7-5.8,7.1-10.4,12.9-14.1c5.9-3.7,12.4-5.5,19.5-5.5
    c8.9,0,16.6,2.3,22.9,7.1c6.5,4.8,9.7,11.4,9.7,19.7c0,13.3-7.5,21.4-21.5,28.6l-7.9,4c-8.4,4.2-12.5,8-13.7,14.7h42.7V280.6z
     M423.9,301h-70.7l-35.3,61.2h70.7l35.4,61.2l35.3-61.2L423.9,301z M324,403.1c-45,0-81.6-36.6-81.6-81.6c0-45,36.6-81.6,81.6-81.6
    l28-58.5c-9.1-1.8-18.4-2.7-28-2.7c-78.9,0-142.9,64-142.9,142.8c0,78.9,63.9,142.9,142.9,142.9c31.4,0,60.5-10.2,84.1-27.4
    l-31-53.6C362.9,395.6,344.3,403.1,324,403.1"></path>
                  </svg>
                </div>
                <div class="cd-block text-right">
                  <svg width="106" height="22" viewBox="0 0 106 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.3343 2.04893C10.5438 1.40402 11.4562 1.40402 11.6657 2.04894L13.3125 7.11713C13.4062 7.40554 13.675 7.60081 13.9782 7.60081H19.3072C19.9854 7.60081 20.2673 8.46854 19.7187 8.86712L15.4074 11.9994C15.1621 12.1777 15.0594 12.4936 15.1531 12.7821L16.7999 17.8503C17.0094 18.4952 16.2713 19.0315 15.7227 18.6329L11.4114 15.5006C11.1661 15.3223 10.8339 15.3223 10.5886 15.5006L6.27729 18.6329C5.72869 19.0315 4.99056 18.4952 5.2001 17.8503L6.84686 12.7821C6.94057 12.4936 6.83791 12.1777 6.59257 11.9994L2.28131 8.86712C1.73271 8.46854 2.01465 7.60081 2.69276 7.60081H8.02177C8.32502 7.60081 8.59379 7.40554 8.68751 7.11712L10.3343 2.04893Z" fill="#FFD43B"></path>
                    <path d="M31.2864 2.04893C31.496 1.40402 32.4083 1.40402 32.6179 2.04894L34.2646 7.11713C34.3584 7.40554 34.6271 7.60081 34.9304 7.60081H40.2594C40.9375 7.60081 41.2194 8.46854 40.6708 8.86712L36.3596 11.9994C36.1142 12.1777 36.0116 12.4936 36.1053 12.7821L37.752 17.8503C37.9616 18.4952 37.2235 19.0315 36.6749 18.6329L32.3636 15.5006C32.1183 15.3223 31.786 15.3223 31.5407 15.5006L27.2294 18.6329C26.6808 19.0315 25.9427 18.4952 26.1523 17.8503L27.799 12.7821C27.8927 12.4936 27.7901 12.1777 27.5447 11.9994L23.2335 8.86712C22.6849 8.46854 22.9668 7.60081 23.6449 7.60081H28.9739C29.2772 7.60081 29.5459 7.40554 29.6397 7.11712L31.2864 2.04893Z" fill="#FFD43B"></path>
                    <path d="M52.239 2.04893C52.4486 1.40402 53.361 1.40402 53.5705 2.04894L55.2173 7.11713C55.311 7.40554 55.5798 7.60081 55.883 7.60081H61.212C61.8901 7.60081 62.1721 8.46854 61.6235 8.86712L57.3122 11.9994C57.0669 12.1777 56.9642 12.4936 57.0579 12.7821L58.7047 17.8503C58.9142 18.4952 58.1761 19.0315 57.6275 18.6329L53.3162 15.5006C53.0709 15.3223 52.7387 15.3223 52.4933 15.5006L48.1821 18.6329C47.6335 19.0315 46.8953 18.4952 47.1049 17.8503L48.7516 12.7821C48.8454 12.4936 48.7427 12.1777 48.4974 11.9994L44.1861 8.86712C43.6375 8.46854 43.9194 7.60081 44.5975 7.60081H49.9266C50.2298 7.60081 50.4986 7.40554 50.5923 7.11712L52.239 2.04893Z" fill="#FFD43B"></path>
                    <path d="M73.1912 2.04893C73.4007 1.40402 74.3131 1.40402 74.5227 2.04894L76.1694 7.11713C76.2631 7.40554 76.5319 7.60081 76.8352 7.60081H82.1642C82.8423 7.60081 83.1242 8.46854 82.5756 8.86712L78.2644 11.9994C78.019 12.1777 77.9164 12.4936 78.0101 12.7821L79.6568 17.8503C79.8664 18.4952 79.1282 19.0315 78.5796 18.6329L74.2684 15.5006C74.023 15.3223 73.6908 15.3223 73.4455 15.5006L69.1342 18.6329C68.5856 19.0315 67.8475 18.4952 68.057 17.8503L69.7038 12.7821C69.7975 12.4936 69.6948 12.1777 69.4495 11.9994L65.1382 8.86712C64.5896 8.46854 64.8716 7.60081 65.5497 7.60081H70.8787C71.182 7.60081 71.4507 7.40554 71.5444 7.11712L73.1912 2.04893Z" fill="#FFD43B"></path>
                    <path d="M94.1438 2.04893C94.3534 1.40402 95.2658 1.40402 95.4753 2.04894L97.1221 7.11713C97.2158 7.40554 97.4845 7.60081 97.7878 7.60081H103.117C103.795 7.60081 104.077 8.46854 103.528 8.86712L99.217 11.9994C98.9717 12.1777 98.869 12.4936 98.9627 12.7821L100.609 17.8503C100.819 18.4952 100.081 19.0315 99.5323 18.6329L95.221 15.5006C94.9757 15.3223 94.6435 15.3223 94.3981 15.5006L90.0869 18.6329C89.5383 19.0315 88.8001 18.4952 89.0097 17.8503L90.6564 12.7821C90.7501 12.4936 90.6475 12.1777 90.4021 11.9994L86.0909 8.86712C85.5423 8.46854 85.8242 7.60081 86.5023 7.60081H91.8313C92.1346 7.60081 92.4034 7.40554 92.4971 7.11712L94.1438 2.04893Z" fill="#FFD43B"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>


    <div class="row no-gutters bg-white">

        <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 pt-0 collection-container sec-faq-wrapper bg-blue bg-white">
            <h3 class="my-5 gm-sec-three-heading text-center text-black">{!! $json_data->faqs_section->heading !!}</h3>
            <div class="s-icons-wrapper faq-wrapper d-flex">
                <div class="panel-group ul-faq-list sec-accordian justify-content-center" id="acordion">

                    @foreach($json_data->faqs_section->data as $i => $faq_data)
                      <div class="accordian-col c-width-faqs">
                          <div class="panel panel-default p-0 sec-faq-item mr-0">
                              <div class="panel-heading p-0">
                                  <li class="ul-faq-list-li bg-light-blue">
                                      <a data-toggle="collapse" data-parent="#acordion" href="#collpse{!! $i !!}"
                                          rel="noreferrer"
                                          class="faq-question-title">
                                          <div>
                                              <h4 class="date-heading montserrat-medium c-black-text">
                                                  {!! $faq_data->title !!}
                                              </h4>
                                          </div>
                                          <svg class="ul-faq-list-img" xmlns="http://www.w3.org/2000/svg"
                                                version="1.0"
                                                viewBox="0 0 1024.000000 1024.000000"
                                                preserveAspectRatio="xMidYMid meet">
                                              <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                                  fill="#424d5c"
                                                  stroke="none">
                                                  <path
                                                          d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z"/>
                                              </g>
                                          </svg>
                                      </a>
                                  </li>
                              </div>
                              <div id="collpse{!! $i !!}" class="panel-collapse collapse in p-0">
                                  <div class="panel-body panel-body-desc faq-content-wrapper">
                                      <p class="m-0 lato-light">
                                          {!! $faq_data->content !!}
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="p-2 last-sec-wrapper bg-white">
        <div class="row col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 last-sec-slider-wrapper mb-0" style="margin-bottom: 0 !important;">
            <div class="col-lg-6 col-md-7 col-sm-12 px-2 px-lg-5 py-5 text-center text-md-left mb-3">
                <h2 class="heading  gm-secnd-sec-heading montserrat-bold pb-3 pb-lg-5 mb-0">{!! $json_data->footer_section->heading !!}</h2>
                <p class="sub-heading color-white paragraph-style montserrat-regular pb-3 pb-lg-5 ">{!! $json_data->footer_section->subheading !!}</p>

                <a href="{!! $json_data->footer_section->button->href !!}" target="_blank"
                   class="last-sec-nine-button text-blue" onclick="setUserkeyword('','','')">{!! $json_data->footer_section->button->text !!}
                    <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg c-aarow-none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                              fill="#0069FF"></path>
                    </svg>
                </a>

            </div>
            <div class="col-lg-6 col-md-5 im-brand-section overflow-hidden d-md-block d-none">
                @if($json_data->is_portrait == 0)
                    <div class="last-sec-slider landscape-slider d-flex pr-3">
                        <div class="last-sec-slider-row " id="ftr-first-row">

                        </div>
                        <div class=" mx-3 last-sec-slider-row-even" id="ftr-scnd-row">

                        </div>
                    </div>
                @else
                    <div class="last-sec-slider d-flex pr-3">
                        <div class="last-sec-slider-row " id="ftr-first-row">

                        </div>
                        <div class=" mx-3 last-sec-slider-row-even" id="ftr-scnd-row">

                        </div>
                        <div class="mr-3 last-sec-slider-third-row" id="ftr-third-row">

                        </div>
                        <div class=" last-sec-slider-row-even" id="ftr-fourth-row">

                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- <div class="different-tools-sec bg-free-tools bg-tool-blue pb-90" style="background-color: #ffffff !important;">
  <h2 class="heading fs-42 text-center heading-tool font-montserrat-bold ptb-66 explore-more-text fs-46" style="color: #151515;">
    <span class="">Explore</span> More Tools
  </h2>
  <div class="container c-container">
    <div class="row">
      <div class="col-md-3 c-margin-card hover-side">
        <a class="back-hover" href="../qr-code-generator/" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/chart/qr_code_editor.webp" type="image/webp">
                <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/chart/qr_code_editor.png" alt="QR Code Editor" height="204" width="315" loading="lazy" style="background: #1397C1">
              </picture>
            </div>
            <div class="text-sec-tool">QR Code</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 c-margin-card hover-side">
        <a class="back-hover" href="../graph-maker/" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/charts_editor.webp" type="image/webp">
                <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/charts_editor.png" alt="Chart Editor" height="204" width="315" loading="lazy" style="background: #F0DA9B">
              </picture>
            </div>
            <div class="text-sec-tool">Charts</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 c-margin-card hover-side">
        <a class="back-hover" href="../app/#/sign-up" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/chart/image_editor.webp" type="image/webp">
                <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/chart/image_editor.png" alt="Image Editor" height="204" width="315" loading="lazy" style="background: #2DE7C1">
              </picture>
            </div>
            <div class="text-sec-tool">Image Editor</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 c-margin-card hover-side">
          <a class="back-hover" href="../background-remover/" target="_blank">
            <div class="free-tool-card">
              <div class="img-sec-tool">
                <picture>
                  <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/bg_removal/tools-background.webp" type="image/webp">
                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/bg_removal/tools-background.png" alt="Chart Editor" height="204" width="315" loading="lazy" style="background: #F0DA9B">
                </picture>
              </div>
              <div class="text-sec-tool">Remove Background</div>
            </div>
          </a>
        </div>
    </div>
  </div>
</div> -->

<div class="different-tools-sec bg-free-tools pb-90 bg-white">
  <h2 class="heading fs-46 c-black-text text-center heading-tool font-montserrat-bold ptb-66 explore-more-text">
    <span class="">Explore</span> More Tools
  </h2>
  <div class="container c-container-barcode">
    <div class="row">
      <div class="col-md-3 mb-4 hover-side">
        <a class="back-hover" href="https://photoadking.com/qr-code-generator/" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/qr_code_editor.webp" type="image/webp">
                <img style="height: auto !important;" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/qr_code_editor.png" alt="QR Code Editor" height="204" width="315" loading="lazy" style="background: #1397C1">
              </picture>
            </div>
            <div class="text-sec-tool">QR Code</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-4 hover-side">
        <a class="back-hover" href="https://photoadking.com/graph-maker/" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/charts_editor.webp" type="image/webp">
                <img style="height: auto !important;" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/charts_editor.png" alt="Chart Editor" height="204" width="315" loading="lazy" style="background: #F0DA9B">
              </picture>
            </div>
            <div class="text-sec-tool">Charts</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-4 hover-side">
        <a class="back-hover" href="https://photoadking.com/app/#/dashboard" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/image_editor.webp" type="image/webp">
                <img style="height: auto !important;" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/barcode/image_editor.png" alt="Image Editor" height="204" width="315" loading="lazy" style="background: #2DE7C1">
              </picture>
            </div>
            <div class="text-sec-tool">Image Editor</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-4 hover-side">
        <a class="back-hover" href="https://photoadking.com/background-remover/" target="_blank">
          <div class="free-tool-card">
            <div class="img-sec-tool">
              <picture>
                <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/bg_removal/tools-background.webp" type="image/webp">
                <img style="height: auto !important;" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web/tools-images/bg_removal/tools-background.png" alt="Chart Editor" height="204" width="315" loading="lazy" style="background: #F0DA9B">
              </picture>
            </div>
            <div class="text-sec-tool">Remove Background</div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

    <div class="l-footer-container row no-gutters c-new-footer" id="hmFooter">
        <div class="col-12 col-xl-10 col-lg-12 col-md-12 col-sm-12 m-auto l-footer-content-container justify-content-around">
            <div class="n-footer-logo-container">
                <img src="{!! $cloudfront_link_path !!}/photoadking.svg"
                     data-src="{!! $cloudfront_link_path !!}/photoadking.svg" class="footer-logo height-init"
                     alt="image not found">
            </div>

            <ul class="n-photoadking-footer-menu-container pl-2">
                <li>Create Design</li>
                <li><a id="lnk_hm" onmouseover="rotateSvg('#lnk_hm')" onmouseout="rotatebackSvg('#lnk_hm')"
                       data-html="true" data-content="<div class=&quot;row m-0 marketing-opt&quot;>
              <div class=&quot;col-5 m-0 pr-0 pl-20px brdr-right&quot;>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/flyers/&quot;>Flyer</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/posters/&quot;>Poster</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/brochures/&quot;>Brochure</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/infographics/&quot;>Infographic</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/business-card/&quot;>Business Card</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/logo/&quot;>Logo</a>

              </div>
              <div class=&quot;col-7 m-0 pl-20px pr-0&quot;>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/gift-certificate/&quot;>Gift Certificate</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/restaurant-menu/&quot;>Menu</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/banner-maker/&quot;>Banner</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/billboard/&quot;>Billboard</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/ad-maker/&quot;>Advertisement</a>

              </div>
            </div>" data-original-title="" title="">
                        Marketing
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                        c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a></li>
                <li><a id="lnk_ftr" onmouseover="rotateSvg('#lnk_ftr')" onmouseout="rotatebackSvg('#lnk_ftr')"
                       data-html="true" data-content="<div class=&quot;d-flex m-0 socialmedia-opt&quot;>
              <div class=&quot; m-0 pr-3 pl-20px brdr-right&quot; >
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/facebook-ad-maker/&quot;>Facebook Ads</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/facebook-cover/&quot;>Facebook Cover</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/social-media-post/facebook-post/&quot;>Facebook Post</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/instagram-ad-maker/&quot;>Instagram Ads</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/instagram-post/&quot;>Instagram Post</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/social-story/instagram-story/&quot;>Instagram Story</a>
              </div>
              <div class=&quot;m-0 pl-20px pr-0&quot;>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/linkedin-banner/&quot;>LinkedIn Cover</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/linkedin-post/&quot;>LinkedIn Post</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/twitter-header/&quot;>Twitter Header</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/tumblr-header/&quot;>Tumblr Header</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/youtube-channel-art/&quot;>YouTube Channel Art</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/youtube-thumbnail/&quot;>YouTube Thumbnail</a>
              </div>

            </div>" data-original-title="" title="">Social Media
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                        c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a></li>

                <li><a id="lnk_primun" onmouseover="rotateSvg('#lnk_primun')" onmouseout="rotatebackSvg('#lnk_primun')"
                       data-html="true" data-content="<div class=&quot;row m-0 ebook-opt&quot;>
              <div class=&quot;col-12 m-0 pr-0 pl-20px&quot; >
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/book-cover/e-book/&quot;>eBook</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/album-covers/&quot;>Album Cover</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/blog-banner-maker/&quot;>Blog Banner</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/storyboard/&quot;>Storyboard</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/book-cover/magazine-cover/&quot;>Magazine Cover</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/book-cover/wattpad-cover/&quot;>Wattpad Cover</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/album-covers/moodboard/&quot;>Mood Board</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/album-covers/podcast-cover/&quot;>Podcast Cover</a>
              </div>

            </div>" data-original-title="" title="">Blogging &amp; eBooks
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                    c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a></li>
                <li><a id="lnk_invitation" onmouseover="rotateSvg('#lnk_invitation')"
                       onmouseout="rotatebackSvg('#lnk_invitation')" data-html="true"
                       data-content="<div class=&quot;row m-0 invitation-opt&quot;>
              <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/wedding-invitation-maker/&quot;>Wedding</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/invitations/&quot;>Invite</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/announcement-maker/&quot;>Announcement</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/greeting-card-maker/&quot;>Greeting Card</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/postcard-templates/&quot;>Postcard</a>
              </div>

            </div>" data-original-title="" title="">Invitation &amp; Events
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                    c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a>
                </li>
                <li><a id="lnk_sprt" onmouseover="rotateSvg('#lnk_sprt')" onmouseout="rotatebackSvg('#lnk_sprt')"
                       data-html="true" data-content="<div class=&quot;row m-0 personalization-opt&quot;>
              <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/calendar/&quot;>Calendar</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/planners/&quot;>Planner</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/flowchart-templates/&quot;>Flowchart</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/desktop-wallpaper/&quot;>Desktop Wallpaper</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/biodata/&quot;>Biodata</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/resume/&quot;>Resume</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/album-covers/scrapbook/&quot;>Scrapbook</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/album-covers/photobook/&quot;>Photo Book</a>
              </div>

            </div>" data-original-title="" title="">Personalization
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                    c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a></li>
                <li><a id="lnk_doc" onmouseover="rotateSvg('#lnk_doc')" onmouseout="rotatebackSvg('#lnk_doc')"
                       data-html="true" data-content="<div class=&quot;row m-0 letter-opt&quot;>
              <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/certificate/&quot;>Certificate</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/presentation-maker/&quot;>Presentation</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/letterhead-maker/ &quot;>Letterhead</a>
                <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! $activation_link_path !!}/templates/invoice-templates/&quot;>Invoice</a>


              </div>

            </div>" data-original-title="" title="">Documents &amp; Letter
                        <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg"
                             viewBox="0 0 16 8">
                            <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                    c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                            </path>
                        </svg>
                    </a></li>
            </ul>
            <ul class="n-photoadking-footer-menu-container pl-2 ">
                <li>Create Video</li>
                <li><a href="{!! $activation_link_path !!}/design/video-flyer-maker/">Video Flyer</a></li>
                <li><a href="{!! $activation_link_path !!}/design/video-brochure-maker/">Video Brochure</a></li>
                <li><a href="{!! $activation_link_path !!}/design/video-invitation-maker/">Video Invitation</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/youtube-intro-maker/">YouTube Intro</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/outro-video-maker/">Outro</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/marketing-video-maker/">Marketing Video</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/promo-video-maker/">Promo Video</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/video-ad-maker/">Video Ad</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/animated-video-maker/">Animated Video</a></li>
                <li><a href="{!! $activation_link_path !!}/templates/logo-reveal-intro-maker/">Logo Reveal Video</a>
                </li>
            </ul>

            <ul class="n-photoadking-footer-menu-container pl-2 ">
                <li>Popular Categories</li>
                <li><a id="lnk_flyr" role="main" href="{!! $activation_link_path !!}/flyer-maker/">Flyer Maker</a></li>
                <li><a id="lnk_brochure" role="main" href="{!! $activation_link_path !!}/poster-maker/">Poster Maker</a></li>
                <li><a id="lnk_logo" role="main" href="{!! $activation_link_path !!}/brochure-maker/">Brochure Maker</a></li>
                <li><a id="lnk_rsme" role="main" href="{!! $activation_link_path !!}/logo-maker/">Logo Maker</a></li>
                <li><a id="lnk_business" role="main" href="{!! $activation_link_path !!}/thumbnail-maker/">YouTube Thumbnail Maker</a></li>
                <li><a id="lnk_inv" role="main" href="{!! $activation_link_path !!}/youtube-banner-maker/">YouTube Banner Maker</a></li>
                <li><a id="lnk_certfcte" role="main" href="{!! $activation_link_path !!}/business-card-maker/">Business Card Maker</a></li>
                <li><a id="lnk_menu" role="main" href="{!! $activation_link_path !!}/menu-maker/">Menu Maker</a></li>
                <li><a id="lnk_certfctemaker" role="main" href="{!! $activation_link_path !!}/certificate-maker/">Certificate Maker</a></li>
                <li><a id="lnk_prdct" role="main" href="{!! $activation_link_path !!}/templates/youtube-intro-maker/">Intro Maker</a></li>
                <li><a id="lnk_prdctinsta" role="main" href="{!! $activation_link_path !!}/instagram-post-maker/">Instagram Post Maker</a></li>
                <li><a id="lnk_pstr" role="main" href="{!! $activation_link_path !!}/meme-maker/">Meme Maker</a></li>
                <li><a id="lnk_bio" role="main" href="{!! $activation_link_path !!}/infographic-maker/">Infographic Maker</a></li>
                <li><a id="lnk_anuncmnt" role="main" href="{!! $activation_link_path !!}/biodata-maker/">Biodata Maker</a></li>
                <li><a id="lnk_cnr" role="main" href="{!! $activation_link_path !!}/pamphlet-maker/">Pamphlet Maker</a></li>
                <li><a id="lnk_cnrgift" role="main" href="{!! $activation_link_path !!}/gift-certificate-maker/">Gift Certificate Maker</a></li>
                <li><a id="lnk_cnrinvi" role="main" href="{!! $activation_link_path !!}/invitation-maker/">Invitation Maker</a></li>
            </ul>
            <ul class="n-photoadking-footer-menu-container pl-2 ">
                <li>Support</li>
                <li><a id="lnk_fbpost" href="https://helpphotoadking.freshdesk.com/support/home">Help Center</a></li>
                <li><a id="lnk_insta" href="{!! $activation_link_path !!}/legal-information/contact/">Contact Us</a>
                </li>
                <!-- <li><a id="lnk_ytube" href="#">Tutorial</a></li> -->
                <li><a id="lnk_fbcvr" href="{!! $activation_link_path !!}/whats-new/">What's New</a></li>
              <li><a id="lnk_reviews" href="{!! $activation_link_path !!}/reviews/">Reviews</a></li>

            </ul>
            <ul class="n-photoadking-footer-menu-container pl-2 ">
                <li>Discover</li>
                <li><a id="lnk_ldrbrd" href="{!! $activation_link_path !!}/brand-identity-maker/">Brand Kit</a></li>
                <li><a id="lnk_skyscrpr" href="{!! $activation_link_path !!}/social-media-content-calendar/">Marketing
                        Calendar</a></li>
                <li><a id="lnk_bnr_qr" href="{!! $activation_link_path !!}/qr-code-generator/">QR Generator</a></li>
                <li><a id="lnk_bnr_graph" href="{!! $activation_link_path !!}/graph-maker/">Graph Maker</a></li>
                <li><a id="lnk_bnr_barcode" href="{!! $activation_link_path !!}/barcode-generator/">Barcode Maker</a></li>
                <li><a id="lnk_bnr_removal" href="{!! $activation_link_path !!}/background-remover/">Remove Background</a></li>

            </ul>
            <ul class="n-photoadking-footer-menu-container p-0 extra-footer-div">
            </ul>
        </div>
        <div class="col-12 col-lg-10 col-md-12 col-sm-12 ml-auto mr-auto l-last-div">
            <div class="uppar">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                        <div class="c-copyright-text">©
                            {!! date('Y') !!}
                            PHOTOADKING. ALL Rights Reserved.
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                        <div class="c-support-links">
                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="list-unstyled d-flex">
                                        <li><a id="lnk_trms"
                                               href="{!! $activation_link_path !!}/legal-information/terms-of-service/">Terms
                                                Of
                                                Services</a></li>
                                        <li><a id="lnk_rfnd"
                                               href="{!! $activation_link_path !!}/legal-information/refund-policy/">Refunds</a>
                                        </li>
                                        <li><a id="lnk_privcy"
                                               href="{!! $activation_link_path !!}/legal-information/privacy-policy/">Privacy</a>
                                        </li>
                                        <li><a id="lnk_cokie"
                                               href="{!! $activation_link_path !!}/legal-information/cookie-policy/">Cookie
                                                Policy</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12 icon_last">
                        <div class="c-footer-icon-container">
                            <a href="https://www.facebook.com/photoadking/" aria-label="photoadking" target="_blank"
                               id="sc_fb_lnk"
                               rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M12.9586 0.394043C15.3023 0.420788 17.4196 0.996805 19.3105 2.12209C21.1789 3.22492 22.7331 4.78863 23.8245 6.66369C24.943 8.56601 25.5155 10.6962 25.5422 13.0541C25.4758 16.2804 24.4582 19.036 22.4894 21.3209C20.5206 23.6057 17.999 25.0193 15.3888 25.5612V16.5156H17.8566L18.4147 12.9609H14.6778V10.6327C14.6571 10.1501 14.8097 9.6759 15.1081 9.29598C15.407 8.91503 15.9333 8.71481 16.6869 8.69531H18.9435V5.58148C18.9111 5.57107 18.6039 5.52987 18.0218 5.4579C17.3616 5.38067 16.6978 5.33941 16.0332 5.33432C14.5289 5.34126 13.3393 5.76558 12.4642 6.60728C11.5891 7.44873 11.1421 8.66614 11.1232 10.2595V12.9609H8.27942V16.5156H11.1232V25.5612C7.91811 25.0193 5.39656 23.6057 3.42778 21.3209C1.45899 19.036 0.441432 16.2804 0.375 13.0541C0.401579 10.696 0.974114 8.5659 2.0926 6.66369C3.18403 4.78863 4.73825 3.22492 6.60664 2.12209C8.49751 0.997021 10.6148 0.421004 12.9586 0.394043V0.394043Z"
                                          fill="#475993"></path>
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/photoadking/" aria-label="photoadking" target="_blank"
                               id="sc_insta_lnk"
                               rel="noreferrer nofollow">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.8503 0H7.14973C3.20735 0 0 3.20735 0 7.14973V16.8503C0 20.7926 3.20735 24 7.14973 24H16.8503C20.7926 24 24 20.7926 24 16.8503V7.14973C24 3.20735 20.7926 0 16.8503 0ZM21.5856 16.8503C21.5856 19.4655 19.4655 21.5856 16.8503 21.5856H7.14973C4.5345 21.5856 2.4144 19.4655 2.4144 16.8503V7.14973C2.4144 4.53446 4.5345 2.4144 7.14973 2.4144H16.8503C19.4655 2.4144 21.5856 4.53446 21.5856 7.14973V16.8503Z"
                                          fill="url(#paint0_linear_1888_3557)"></path>
                                    <path d="M12.0002 5.79297C8.57754 5.79297 5.79297 8.57754 5.79297 12.0002C5.79297 15.4228 8.57754 18.2074 12.0002 18.2074C15.4229 18.2074 18.2075 15.4229 18.2075 12.0002C18.2075 8.57749 15.4229 5.79297 12.0002 5.79297ZM12.0002 15.7931C9.90547 15.7931 8.20737 14.095 8.20737 12.0002C8.20737 9.90547 9.90551 8.20737 12.0002 8.20737C14.095 8.20737 15.7931 9.90547 15.7931 12.0002C15.7931 14.0949 14.0949 15.7931 12.0002 15.7931Z"
                                          fill="url(#paint1_linear_1888_3557)"></path>
                                    <path d="M18.2188 7.32683C19.0403 7.32683 19.7062 6.6609 19.7062 5.83944C19.7062 5.01798 19.0403 4.35205 18.2188 4.35205C17.3974 4.35205 16.7314 5.01798 16.7314 5.83944C16.7314 6.6609 17.3974 7.32683 18.2188 7.32683Z"
                                          fill="url(#paint2_linear_1888_3557)"></path>
                                    <defs>
                                        <linearGradient id="paint0_linear_1888_3557" x1="12" y1="23.9301" x2="12"
                                                        y2="0.186412" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#E09B3D"></stop>
                                            <stop offset="0.3" stop-color="#C74C4D"></stop>
                                            <stop offset="0.6" stop-color="#C21975"></stop>
                                            <stop offset="1" stop-color="#7024C4"></stop>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_1888_3557" x1="12.0002" y1="23.9304"
                                                        x2="12.0002" y2="0.186636" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#E09B3D"></stop>
                                            <stop offset="0.3" stop-color="#C74C4D"></stop>
                                            <stop offset="0.6" stop-color="#C21975"></stop>
                                            <stop offset="1" stop-color="#7024C4"></stop>
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_1888_3557" x1="18.2188" y1="23.9303"
                                                        x2="18.2188" y2="0.186492" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#E09B3D"></stop>
                                            <stop offset="0.3" stop-color="#C74C4D"></stop>
                                            <stop offset="0.6" stop-color="#C21975"></stop>
                                            <stop offset="1" stop-color="#7024C4"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </a>
                            <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" aria-label="photoadking"
                               id="sc_ytube_lnk"
                               target="_blank" rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.3809 15.4355L15.6096 13L11.3809 10.5645V15.4355Z"
                                          fill="#FF0000"></path>
                                    <path d="M13 0C5.8214 0 0 5.8214 0 13C0 20.1786 5.8214 26 13 26C20.1786 26 26 20.1786 26 13C26 5.8214 20.1786 0 13 0ZM21.123 13.0133C21.123 13.0133 21.123 15.6497 20.7886 16.9211C20.6011 17.6169 20.0524 18.1656 19.3566 18.3529C18.0853 18.6875 13 18.6875 13 18.6875C13 18.6875 7.92802 18.6875 6.64342 18.3396C5.94756 18.1523 5.39888 17.6034 5.21143 16.9076C4.87679 15.6497 4.87679 13 4.87679 13C4.87679 13 4.87679 10.3637 5.21143 9.09242C5.39868 8.39656 5.96085 7.8344 6.64342 7.64714C7.91473 7.3125 13 7.3125 13 7.3125C13 7.3125 18.0853 7.3125 19.3566 7.66043C20.0524 7.84769 20.6011 8.39656 20.7886 9.09242C21.1365 10.3637 21.123 13.0133 21.123 13.0133V13.0133Z"
                                          fill="#FF0000"></path>
                                </svg>
                            </a>
                            <a href="https://www.pinterest.com/photoadking/" aria-label="photoadking" target="_blank"
                               id="sc_pintrst_lnk"
                               rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.0001 0C5.84003 0 0.0224609 5.81757 0.0224609 12.9776C0.0224609 18.3029 3.19975 22.8675 7.80903 24.8812C7.76427 23.9862 7.80903 22.8675 8.03279 21.883C8.30132 20.809 9.68855 14.8124 9.68855 14.8124C9.68855 14.8124 9.28579 13.9621 9.28579 12.7539C9.28579 10.8296 10.4046 9.39757 11.7918 9.39757C12.9553 9.39757 13.5371 10.2926 13.5371 11.3666C13.5371 12.5301 12.7763 14.3201 12.3736 15.9759C12.0603 17.3631 13.0448 18.4819 14.4321 18.4819C16.8934 18.4819 18.5491 15.3046 18.5491 11.5903C18.5491 8.72623 16.6248 6.623 13.1343 6.623C9.19626 6.623 6.73503 9.57652 6.73503 12.8433C6.73503 13.962 7.04827 14.7676 7.58527 15.394C7.80903 15.6626 7.85379 15.7968 7.76427 16.11C7.7195 16.3338 7.5405 16.9156 7.49574 17.1393C7.40622 17.4525 7.13774 17.5868 6.82451 17.4525C4.98974 16.6918 4.18422 14.7228 4.18422 12.4852C4.18422 8.81571 7.27198 4.38542 13.4475 4.38542C18.4148 4.38542 21.6816 7.96547 21.6816 11.814C21.6816 16.9155 18.8624 20.7193 14.7006 20.7193C13.3133 20.7193 11.9708 19.9586 11.5233 19.1083C11.5233 19.1083 10.7626 22.1066 10.6283 22.6884C10.3598 23.6729 9.82279 24.7021 9.33055 25.4629C10.4941 25.8209 11.7471 25.9999 13.0001 25.9999C20.1601 25.9999 25.9777 20.1823 25.9777 13.0223C25.9777 5.86223 20.1602 0 13.0001 0Z"
                                          fill="#CB1F24"></path>
                                </svg>
                            </a>
                            <a href="https://twitter.com/photoadking" aria-label="photoadking" target="_blank"
                               id="sc_twtr_lnk"
                               rel="noreferrer nofollow">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_1888_3550)">
                                        <path d="M24 4.5585C23.1075 4.95 22.1565 5.2095 21.165 5.3355C22.185 4.7265 22.9635 3.7695 23.3295 2.616C22.3785 3.183 21.3285 3.5835 20.2095 3.807C19.3065 2.8455 18.0195 2.25 16.6155 2.25C13.8915 2.25 11.6985 4.461 11.6985 7.1715C11.6985 7.5615 11.7315 7.9365 11.8125 8.2935C7.722 8.094 4.1025 6.1335 1.671 3.147C1.2465 3.8835 0.9975 4.7265 0.9975 5.634C0.9975 7.338 1.875 8.8485 3.183 9.723C2.3925 9.708 1.617 9.4785 0.96 9.117C0.96 9.132 0.96 9.1515 0.96 9.171C0.96 11.562 2.6655 13.548 4.902 14.0055C4.5015 14.115 4.065 14.1675 3.612 14.1675C3.297 14.1675 2.979 14.1495 2.6805 14.0835C3.318 16.032 5.127 17.4645 7.278 17.511C5.604 18.8205 3.4785 19.6095 1.1775 19.6095C0.774 19.6095 0.387 19.5915 0 19.542C2.1795 20.9475 4.7625 21.75 7.548 21.75C16.602 21.75 21.552 14.25 21.552 7.749C21.552 7.5315 21.5445 7.3215 21.534 7.113C22.5105 6.42 23.331 5.5545 24 4.5585Z"
                                              fill="#03A9F4"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1888_3550">
                                            <rect width="24" height="24" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                            <a href="https://in.linkedin.com/showcase/photoadking" aria-label="photoadking"
                               id="sc_lnkdin_lnk" target="_blank"
                               rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M19.5 19.4878H16.8066V15.2726C16.8066 14.2675 16.7887 12.9748 15.4066 12.9748C14.0051 12.9748 13.7914 14.0701 13.7914 15.2011V19.4878H11.1004V10.8201H13.6825V12.0055H13.7199C14.079 11.3238 14.9581 10.6048 16.2687 10.6048C18.9963 10.6048 19.5 12.3996 19.5 14.7339V19.4878ZM8.06325 9.63625C7.19794 9.63625 6.5 8.93588 6.5 8.07381C6.5 7.21175 7.19794 6.51137 8.06325 6.51137C8.9245 6.51137 9.62406 7.21175 9.62406 8.07381C9.62406 8.93588 8.9245 9.63625 8.06325 9.63625ZM9.41037 19.4878H6.71369V10.8201H9.41037V19.4878ZM13 0C5.81994 0 0 5.81994 0 13C0 20.1793 5.81994 26 13 26C20.1801 26 26 20.1793 26 13C26 5.81994 20.1801 0 13 0Z"
                                          fill="#2E78B6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-block d-sm-none">
    <div class="d-flex justify-content-center mr-4">
      <a href="#0" class="small-btn-padding" id="floatingBtn" style="text-decoration: none;position: fixed;bottom: 20px;z-index: 999;" >
        <div class="floating-button">
          <div class="fbtn" style="width: 80%;text-align: center;">
            <!-- <span id="floatSpan" ></span>
            <marquee width="100%" direction="left" height="100%" behavior="scroll" id="floatMrq" behavior="scroll">

            </marquee> -->
            <span id="floatSpan"></span>
                    <span id="floatMrq"></span>
                    <!-- <div id="floatMrq" class="marquee">
                        <span>Create a Social Media Post now</span>
                    </div> -->
          </div>
          <div id="button-dot" class="dotted"></div>
          <div id="right-button" class="next-button">
            <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.07655 5.38275H11.3244L7.77945 1.83773C7.35902 1.41737 7.35902 0.735698 7.77945 0.315269C8.19981 -0.10509 8.88148 -0.10509 9.30191 0.315269L14.6847 5.69803C15.1051 6.11839 15.1051 6.80007 14.6847 7.2205L9.30191 12.6033C9.09163 12.8135 8.81617 12.9186 8.54065 12.9186C8.26512 12.9186 7.98959 12.8135 7.77945 12.6033C7.35902 12.1829 7.35902 11.5012 7.77945 11.0808L11.3244 7.53585H1.07655C0.482007 7.53585 0 7.05385 0 6.4593C0 5.86476 0.482007 5.38275 1.07655 5.38275Z" fill="#4069C2"/>
            </svg>
          </div>
        </div>
      </a>
    </div>
            </div>
</div>
<!--  <script>
  if (window.screen.width > 767) {
    document.currentScript.insertAdjacentHTML('beforeend',
      '<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" type="text/javascript" charset="utf-8"/>');
  }
</script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" type="text/javascript"
        charset="utf-8"></script>
<script>
    jQuery.event.special.touchstart = {
        setup: function (_, ns, handle) {
            this.addEventListener("touchstart", handle, {passive: !ns.includes("noPreventDefault")});
        }
    };
    jQuery.event.special.touchmove = {
        setup: function (_, ns, handle) {
            this.addEventListener("touchmove", handle, {passive: !ns.includes("noPreventDefault")});
        }
    };
    jQuery.event.special.wheel = {
        setup: function (_, ns, handle) {
            this.addEventListener("wheel", handle, {passive: true});
        }
    };
    jQuery.event.special.mousewheel = {
        setup: function (_, ns, handle) {
            this.addEventListener("mousewheel", handle, {passive: true});
        }
    };
</script>
<script id="templateJsonScript">
    let slider_JSON = `{!! json_encode($json_data->slider_template_section) !!}`;
    let cat_list = `{!! json_encode($json_data->template_section->data) !!}`;

    slider_JSON = JSON.parse(slider_JSON);
    cat_list = JSON.parse(cat_list);

</script>

<script>
    var hdrnavbtn = document.getElementById('hdrnavbtn');
    var l_r = localStorage.getItem('l_r');
    var ut = localStorage.getItem('ut');
    let dflt_playstore_url = "https://play.google.com/store/apps/details?id=com.bg.flyermaker&referrer=utm_source%3DOB_PAK";
    let dflt_appstore_url = "https://apps.apple.com/us/app/id1337666644";
    let dflt_cta_text = "Create Your Design Now"
    let app_cta_detail = `{!! json_encode($json_data->app_cta_detail) !!}`;
    app_cta_detail = JSON.parse(app_cta_detail);
    let playstore_url = app_cta_detail.playStoreLink;
    let appstore_url = app_cta_detail.appStoreLink;
    let cta_text = app_cta_detail.app_cta_text;

    if (l_r != null && l_r != '' && typeof l_r != 'undefined' && ut != '' && ut != null && typeof ut != 'undefined') {
        // hdrnavbtn.href = "{!! $activation_link_path !!}app/#/editor/hl903t0cbeffda";
    } else {
        hdrnavbtn.href = "{!! $activation_link_path !!}/app/#/sign-up";
        hdrnavbtn.target = "_blank";
    }

    // When the user scrolls down 50px from the top of the document, resize the header's font size
    function scrollFunction() {
        if (document.getElementById("mainbody").scrollTop > 50 || document.getElementById("mainbody").scrollTop > 50) {
            document.getElementById("docHeader").classList.remove("l-header-big");
            document.getElementById("docHeader").classList.add("l-header-small");
        } else {
            document.getElementById("docHeader").classList.remove("l-header-small");
            document.getElementById("docHeader").classList.add("l-header-big");
        }
        showFloatingbtn();
    }
    function showFloatingbtn  () {

      let elem = $("#hmFooter")[0];
      var rect = elem.getBoundingClientRect();
      var elemTop = rect.top;
      var elemBottom = rect.bottom;
      var isVisible = elemTop < window.innerHeight && elemBottom >= 0;
      if (isVisible) {
        //DO whatever you want
        // $("#floatingBtn").animate({"bottom": "-20px"});
        // if($('#floatingBtn').hasClass("fadeInUp")){
            $('#floatingBtn').removeClass('fadeInUp');
            $('#floatingBtn').addClass('fadeInDown');
        // }

      } else {
        if($('#floatingBtn').hasClass("fadeInDown")){
            $('#floatingBtn').removeClass('fadeInDown');
            $('#floatingBtn').addClass('fadeInUp');
        }

        // $("#floatingBtn").animate({"bottom": "20px"});
      }
    //   if ($(".l-header-small")[0]) {
    //     let element = $(".l-header-small")[0];
    //     var rectangle = element.getBoundingClientRect();
    //     var elementTop = rectangle.top;
    //     var elementBottom = rectangle.bottom;
    //     var isVisible = elementTop < window.innerHeight && elementBottom >= 0;
    //     if (isVisible) {
    //       //DO whatever you want
    //       $('#floatingBtn').addClass('d-none');
    //     }
    //   }

    };

    let copy_write_txt = '© ' + new Date().getFullYear() + ' PHOTOADKING. ALL Rights Reserved.';
    $('#copy_write_yr').text(copy_write_txt);

    function init() {
        var imgDefer = document.getElementsByTagName('img');
        for (var i = 0; i < imgDefer.length; i++) {
            if (imgDefer[i].getAttribute('data-src')) {
                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
            }
        }
    }

    window.onload = init;


    $(document).ready(function ($) {

        setFloatingButtonURL();
        $('.panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active-ul-faq-li-img');
        });

        $('.panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active-ul-faq-li-img');
        });
        // $('#header').load('{!! $activation_link_path !!}header.html');

        $('#mob-menu').click(function () {
            $(".l-mob-menu-container").css("transform", "scaleX(1)");
            $('.overlay').show();
        });

        $('.overlay').click(function () {
            $('.overlay').hide();
            $(".l-mob-menu-container").css("transform", "scaleX(0)");
            $('.collapse.show').siblings('a').css('background-color', '#ffffff')
            $('.collapse.show').removeClass('show');
            $('.collapse.show').siblings('a').attr("aria-expanded", false);
            $('.sub-opt.collapse.show').siblings('a').css('background-color', '#ffffff')
            $('.sub-opt.collapse.show').removeClass('show');
            $('.sub-opt.collapse.show').siblings('a').attr("aria-expanded", false);
        });

        var l_r = localStorage.getItem('l_r');
        var ut = localStorage.getItem('ut');
        if (l_r != null && l_r != '' && typeof l_r != 'undefined' && ut != '' && ut != null && typeof ut !=
            'undefined') {
            $('#hd-login').hide();
            $('#hd-logn').hide();
            $('#rlp-link').show();
            $('#rlp-text-mob').html('Dashboard');
            $('#rlp-text-mob').attr('href', '{!! $activation_link_path !!}/app/#/dashboard');
            $('#rlp-btn-txt span').html('Dashboard');
            $('#rlp-link').attr('href', '{!! $activation_link_path !!}/app/#/dashboard');

            /*  try {
               var login_response = JSON.parse(l_r);
               // To set unique user id in your system when it is available
               window.fcWidget.setExternalId(login_response.user_detail.user_name);

               // To set user name
               window.fcWidget.user.setFirstName(login_response.user_detail.first_name);

               // To set user email
               window.fcWidget.user.setEmail(login_response.user_detail.email_id);

               // To set user properties
               window.fcWidget.user.setProperties({
                 plan: "Estate", // meta property 1
                 status: "Active" // meta property 2
               });
             } catch (error) {
               console.log(error);
             } */

        } else {
            $('#hd-logn').show();
            $('#hd-login').show();
            $('#rlp-link').show();
        }

        // live id's


        //  staging id's

        /* var slider_JSON = [{
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1603.jpg',
               title: 'Education',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1603
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1604.jpg',
               title: 'Event & Party',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1604
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1605.jpg',
               title: 'Real Estate',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1605
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1606.jpg',
               title: 'Restaurant',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1606
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1607.jpg',
               title: 'Salon',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1607
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1608.jpg',
               title: 'Sports & Fitness',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1608
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1609.jpg',
               title: 'Travel',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1609
             },
             {
               image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1610.jpg',
               title: 'Grocery Flyer',
               sub_category_id: 37,
               catalog_id: 71,
               template_id: 1610
             }
           ] */

        // local id's

        /* var slider_JSON = [{
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1603.jpg',
            title: 'Education',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2413
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1604.jpg',
            title: 'Event & Party',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2414
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1605.jpg',
            title: 'Real Estate',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2415
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1606.jpg',
            title: 'Restaurant',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2416
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1607.jpg',
            title: 'Salon',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2417
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1608.jpg',
            title: 'Sports & Fitness',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2418
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1609.jpg',
            title: 'Travel',
            sub_category_id: 37,
            catalog_id: 62,
            template_id: 2419
          },
          {
            image: '{!! $activation_link_path !!}images/template-images/flyer/37-71-1610.jpg',
            title: 'Grocery Flyer',
            sub_category_id: 37,
            catalog_id: 71,
            template_id: 2420
          }
        ] */

        function setFloatingButtonURL(){
            let ctaText = cta_text?cta_text:dflt_cta_text;
            let appStrURL = appstore_url?appstore_url:dflt_appstore_url;
            let playStrURL = playstore_url?playstore_url:dflt_playstore_url;
            localStorage.setItem("pr_plstr_ur",playStrURL);
            localStorage.setItem("pr_apstr_ur",appStrURL);
            const userAgent = navigator.userAgent;
            $("#floatSpan").html(ctaText);
            $("#floatMrq").html(ctaText);
            $("#floatMrq").addClass("float-merquee");
            let fbtnlen = $('.floating-button').children().text();
            let fbtnlen2 = $('.floating-button').width();
            let fbtn = $('.fbtn').width();
            let fspan = $('#floatSpan').width();
            let fspan2 = $('#floatSpan').text();
            if(fspan > fbtn)
            {
                    $('#floatSpan').hide();
                    $('#floatMrq').show();
            }
            else
                {
                $('#floatSpan').show();
                $('#floatMrq').hide();

                }
                    if (/android/i.test(userAgent)) {
                        $('#floatingBtn').attr("href",playStrURL);

                    }else if (/iPad|iPhone|iPod/i.test(userAgent)) {
                        $('#floatingBtn').attr("href",appStrURL);
                    }
                }

        var width = $(window).width();
        var len = slider_JSON.length;
        if (width > 767) {
            for (var i = 0; i < len; i++) {
                    let tmpStr = '<li onclick=\'redirect(' + JSON.stringify(slider_JSON[i].sub_category_id) + "," + JSON.stringify(
                    slider_JSON[i].catalog_id) +
                    "," + JSON.stringify(slider_JSON[i].template_id) +
                    "," + "\"" +slider_JSON[i].title + "\"" +
                    ')\' ><img loading="lazy" width="210px" height="269px" src="' + slider_JSON[i].webp_image + '" class="slider-img" ' +
                    'onerror=" this.src=' + "'" + slider_JSON[i].image + "'" + '" alt="' + slider_JSON[i].alt +
                    '">';
                    if(slider_JSON[i].title.length > 18){
                        tmpStr += '<div class="slide-title"><span class="apply-marquee">' + slider_JSON[i].title + '</span></div></li>';
                    }else{
                        tmpStr += '<div class="slide-title">' + slider_JSON[i].title + '</div></li>';
                    }
                    $('#slider').append(tmpStr);
            }
        }

        if (window.screen.width > 768) {
            // header-slider
            $(".slider-container").slick({
                dots: false,
                infinite: true,
                slidesToShow: 7,
                slidesToScroll: 1,
                pauseOnHover: true,
                swipeToSlide: true,
                autoplay: true,
                arrows: false,
                // centerMode: true,
                // centerPadding: '20%',
                focusOnSelect: true,
                responsive: [{
                    breakpoint: 1650,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 1,
                        infinite: true,
                    }
                },
                    {
                        breakpoint: 1300,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 1185,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 763,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 500,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    }
                ]
            });

            var isSliding = false;

            $(".slider-container").on('beforeChange', function () {
                isSliding = true;
            });

            $(".slider-container").on('afterChange', function () {
                isSliding = false;
            });


            $(".slider-container").each(function () {
                this.slick.getNavigableIndexes = function () {
                    var _ = this,
                        breakPoint = 0,
                        counter = 0,
                        indexes = [],
                        max;
                    if (_.options.infinite === false) {
                        max = _.slideCount;
                    } else {
                        breakPoint = _.options.slideCount * -1;
                        counter = _.options.slideCount * -1;
                        max = _.slideCount * 2;
                    }
                    while (breakPoint < max) {
                        indexes.push(breakPoint);
                        breakPoint = counter + _.options.slidesToScroll;
                        counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _
                            .options
                            .slidesToShow;
                    }
                    return indexes;
                };
            });
        }
        redirect = function (sub_cat_id, catalog_id, template_id,title) {
            if (isSliding) {
                return;
            }
            localStorage.setItem("sub_cat_id", sub_cat_id);
            localStorage.setItem("catalog_id", catalog_id);
            localStorage.setItem("template_id", template_id);
            localStorage.setItem("is_l_re", "true");
            localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
            Object.assign(document.createElement('a'), { target: '_blank', href: '{!! $activation_link_path !!}/app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id}).click();
            setUserkeyword(title,'','');
        }

        // $('#footer').load('{!! $activation_link_path !!}footer.html');
        validateEmail = function (email) {
            var re =
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        $('.validate').submit(function (e) {
            var tmp_title, tmp_msg, tmp_btn;
            tmp_title = document.getElementById("modalTtl");
            tmp_msg = document.getElementById("modalMsg");
            tmp_btn = document.getElementById("modalbtnName");
            if (!e.target[0].value || e.target[0].value == "" || typeof e.target[0].value == "undefined") {
                tmp_title.innerHTML = "Error!";
                tmp_msg.innerHTML = "Please enter an valid email address.";
                tmp_btn.innerHTML = "OK";
                tmp_title.style.color = "#be0000";
                tmp_msg.style.color = "#be0000";
                tmp_btn.style.backgroundColor = "#be0000";
                tmp_btn.style.color = "#ffffff";
                tmp_btn.style.borderColor = "#be0000";
                tmp_btn.style.padding = "3px 20px"
                $('#myModal').modal('show');
                /* alert("Please enter an valid email address"); */
                return false;
            } else if (!validateEmail(e.target[0].value)) {
                tmp_title.innerHTML = "Error!";
                tmp_msg.innerHTML = "Please enter an valid email address.";
                tmp_btn.innerHTML = "OK";
                tmp_title.style.color = "#be0000";
                tmp_msg.style.color = "#be0000";
                tmp_btn.style.backgroundColor = "#be0000";
                tmp_btn.style.color = "#ffffff";
                tmp_btn.style.borderColor = "#be0000";
                tmp_btn.style.padding = "3px 20px"
                $('#myModal').modal('show');
                /* alert("The email address is not valid "); */
                return false;
            } else {
                var $this = $(this);
                $.ajax({
                    type: "GET", // GET & url for json slightly different
                    url: "https://photoadking.us19.list-manage.com/subscribe/post-json?c=?",
                    data: $this.serialize(),
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                    error: function (err) {
                        alert("Could not connect to the registration server.");
                    },
                    success: function (data) {
                        if (data.result != "success") {
                            // Something went wrong, parse data.msg string and display message
                            /* alert(data.msg); */
                            tmp_title.innerHTML = "Error!";
                            tmp_msg.innerHTML = data.msg;
                            tmp_btn.innerHTML = "OK";
                            tmp_title.style.color = "#be0000";
                            tmp_msg.style.color = "#be0000";
                            tmp_btn.style.backgroundColor = "#be0000";
                            tmp_btn.style.color = "#ffffff";
                            tmp_btn.style.borderColor = "#be0000";
                            tmp_btn.style.padding = "3px 20px";
                            $('#myModal').modal('show');
                        } else {
                            /* alert(data.msg); */
                            tmp_title.innerHTML = "Success!";
                            tmp_msg.innerHTML = data.msg;
                            tmp_btn.innerHTML = "OK";
                            tmp_title.style.color = "#10a116";
                            tmp_msg.style.color = "#10a116";
                            tmp_btn.style.backgroundColor = "#10a116";
                            tmp_btn.style.color = "#ffffff";
                            tmp_btn.style.borderColor = "#10a116";
                            tmp_btn.style.padding = "3px 20px";
                            $('#myModal').modal('show');
                            // It worked, so hide form and display thank-you message.
                            $('.email-input')
                            $this.closest('form').find("input[type=email], textarea").val("");
                        }
                    }
                });
                return false;
            }
        });
        $('#myModal').on('shown.bs.modal', function () {
            //To relate the z-index make sure backdrop and modal are siblings
            $(this).before($('.modal-backdrop'));
            //Now set z-index of modal greater than backdrop
            $(this).css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 1);
        });

        document.getElementById("mainbody").onscroll = function () {
            scrollFunction()
        };
        openPopover();
        openFooterPopover();
        var _len;
        if (width <= 767) {
            _len = cat_list.length - 4;
        } else {
            _len = cat_list.length;
        }
        for (var i = 0; i < _len; i++) {

                let tempStr = '<div class="content-item card-ol-block cursor-pointer cust-temp-item-new"><div onclick="storeDetails(\'' + cat_list[i].sub_category_id + "','" +
                cat_list[i]
                    .catalog_id +
                "','" + cat_list[i].template_id +
                "','" + cat_list[i].sub_category_name +
                "','" + cat_list[i].catalog_name +
                '\')"><img loading="lazy" draggable="false" alt="' + cat_list[i].alt +
                '" src="{!! $activation_link_path !!}images/spinner.svg" width="237px" height="291px" data-src="' +
                cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                '" alt="' + cat_list[i].alt + '">';
                if(cat_list[i].title.length > 30){
                    tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title"><span class="apply-marquee">'+ cat_list[i].title +'</span></div></a></div>';
                }else{
                    tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title">'+ cat_list[i].title +'</div></a></div>';
                }
                $('#card-wrapper').append(tempStr);
        }
        init();
        if (width >= 767) {
            let template_type = '{!! $json_data->is_portrait !!}';
            if (template_type == "0" && cat_list[0].content_type != 4) {
                for (var i = 0; i < 8; i++) {
                    if (cat_list[i]) {
                        $('#ftr-first-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + cat_list[i].alt +
                            '"src="' +
                            cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                            '" alt="' + cat_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 8; i < 12; i++) {
                    if (cat_list[i]) {
                        $('#ftr-scnd-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + cat_list[i].alt +
                            '" src="' +
                            cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                            '" alt="' + cat_list[i].alt + '"></div>'
                        );
                    }
                }
            } else {
                for (var i = 0; i < 4; i++) {
                    if (cat_list[i]) {
                        $('#ftr-first-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + cat_list[i].alt +
                            '"src="' +
                            cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                            '" alt="' + cat_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 4; i < 8; i++) {
                    if (cat_list[i]) {
                        $('#ftr-scnd-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + cat_list[i].alt +
                            '" src="' +
                            cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                            '" alt="' + cat_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 8; i < 12; i++) {
                    if (cat_list[i]) {
                        $('#ftr-third-row').append('<div class=" mb-3 "><img class="ftr-slider-image" loading="lazy" alt="' + cat_list[i].alt +
                            '"src="' +
                            cat_list[i].webp_image + '" onerror=" this.src=' + "'" + cat_list[i].image + "'" +
                            '" alt="' + cat_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 0; i < 4; i++) {
                    if (cat_list[i]) {
                        $('#ftr-fourth-row').append('<div class=" mb-3 "><img class="ftr-slider-image" loading="lazy" alt="' + slider_JSON[i].alt +
                            '"src="' +
                            slider_JSON[i].webp_image + '" onerror=" this.src=' + "'" + slider_JSON[i].image + "'" +
                            '" alt="' + slider_JSON[i].alt + '"></div>'
                        );
                    }
                }
            }
        }

        // var pathString = window.location.pathname
        // var visitArr = pathString.substring(1, pathString.length - 1).split("/");
        // if (visitArr[1] == "templates" || visitArr[1] == "design") {
        //     visitArr.shift();
        // }
        // localStorage.setItem("userVisited", visitArr);
        setUserkeyword('','','');
        /*   try {
            window.fcWidget.init({
              token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
              host: "https://wchat.freshchat.com"
            });
          } catch (error) {
            console.log(error);
          }
         function initFreshChat() {
           window.fcWidget.init({
             token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
             host: "https://wchat.freshchat.com"
           });
         }
         (function (d, id) {
         var fcJS;
         if (d.getElementById(id)) {
           initFreshChat();
           return;
         }
         fcJS = d.createElement('script');
         fcJS.id = id;
         fcJS.async = true;
         fcJS.src = 'https://wchat.freshchat.com/js/widget.js';
         fcJS.onload = initFreshChat;
         d.head.appendChild(fcJS);
       }(document, 'freshchat-js-sdk')); */

    });

    function storeDetails(sub_cat_id, catalog_id, template_id,sub_cat_name,catalog_name) {
        localStorage.setItem("sub_cat_id", sub_cat_id);
        localStorage.setItem("catalog_id", catalog_id);
        localStorage.setItem("template_id", template_id);
        localStorage.setItem("is_l_re", "true");
        localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
        Object.assign(document.createElement('a'), { target: '_blank', href: '{!! $activation_link_path !!}/app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id}).click();
        setUserkeyword('',sub_cat_name,catalog_name);
    }
    function setUserkeyword(title,sub_cat_name,catalog_name){
      var pathString = window.location.pathname;
            var visitArr = pathString.substring(1, pathString.length - 1).split("/");
            if (visitArr[1] == "templates" || visitArr[1] == "design")
            {
                // visitArr.shift();
                visitArr[1] = visitArr[1].toString().replace(/\w+[.!?]?$/, '').slice(0, -1);
                if(title != '')
                {
                  visitArr.pop();
                  visitArr.push(title);
                }
                if(sub_cat_name != '' && catalog_name != ''){
                  visitArr.pop();
                  visitArr.push(sub_cat_name,catalog_name);
                }
                if(window.location.search != " " && window.location.search.includes("utm_campaign"))
                {
                  visitArr.push('utm_campaign');
                }
                localStorage.setItem("userVisited", visitArr);
            }
            else{
                  // visitArr.shift();
                  visitArr = Array.from(visitArr);
                  visitArr.unshift('design');
                  visitArr[1] = visitArr[1].toString().replace(/\w+[.!?]?$/, '').slice(0, -1);
                  if(title != '')
                  {
                    visitArr.pop();
                    visitArr.push(title);
                  }
                  if(sub_cat_name != '' && catalog_name != '')
                  {
                    visitArr.pop();
                    visitArr.push(sub_cat_name,catalog_name);
                  }
                  if(window.location.search != " " && window.location.search.includes("utm_campaign"))
                  {
                    visitArr.push('utm_campaign');
                  }
                  localStorage.setItem("userVisited", visitArr);
             }
    }

    //this function is use to intialize the freshchat
    function initializeFeshchat() {
        $('#chat_ic').addClass('disply-none');
        $('#loader_ic').removeClass('disply-none');

        function initFreshChat() {
            window.fcWidget.init({
                token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
                host: "https://wchat.freshchat.com"
            });
        }

        (function (d, id) {
            var fcJS;
            if (d.getElementById(id)) {
                initFreshChat();
                return;
            }
            fcJS = d.createElement('script');
            fcJS.id = id;
            fcJS.async = true;
            fcJS.src = 'https://wchat.freshchat.com/js/widget.js';
            fcJS.onload = initFreshChat;
            d.head.appendChild(fcJS);
        }(document, 'freshchat-js-sdk'));
        js_added = true;
        let is_chat_open = false;
        let myTimer = setInterval(() => {
            if ((typeof (fcWidget) !== 'undefined') && (is_chat_open === false)) {
                fcWidget.open();
                is_chat_open = true;
                clearInterval(myTimer);
                hideChatButton();
            }
        }, 2000);
    }

    function hideChatButton() {
        let timer = setInterval(() => {
            if (fcWidget.open !== 'undefined') {
                $('#chat_icon').addClass('disply-none');
                $('#loader_ic').addClass('disply-none');
                clearInterval(timer);
            }

        }, 1000);
    }

    // let js_added = false;
    function initializeFeshchat() {
        // if (js_added == false) {
        $('#chat_ic').addClass('disply-none');
        $('#loader_ic').removeClass('disply-none');

        function initFreshChat() {
            window.fcWidget.init({
                token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
                host: "https://wchat.freshchat.com"
            });
        }

        (function (d, id) {
            var fcJS;
            if (d.getElementById(id)) {
                initFreshChat();
                return;
            }
            fcJS = d.createElement('script');
            fcJS.id = id;
            fcJS.async = true;
            fcJS.src = 'https://wchat.freshchat.com/js/widget.js';
            fcJS.onload = initFreshChat;
            d.head.appendChild(fcJS);
        }(document, 'freshchat-js-sdk'));
        js_added = true;
        let is_chat_open = false;
        /* setTimeout(() => {
          do {
            $('#chat_ic').addClass('disply-none');
            console.log(">>",fcWidget.open());
            fcWidget.open();
            console.log(">>",fcWidget.open());
          } while (fcWidget)
        }, 1500); */
        let myTimer = setInterval(() => {
            if ((typeof (fcWidget) !== 'undefined') && (is_chat_open === false)) {
                fcWidget.open();
                is_chat_open = true;
                clearInterval(myTimer);
                hideChatButton();
            }
        }, 2000);
        // }
    }

    function hideChatButton() {
        let timer = setInterval(() => {
            if (fcWidget.open !== 'undefined') {
                $('#chat_icon').addClass('disply-none');
                $('#loader_ic').addClass('disply-none');
                clearInterval(timer);
            }

        }, 1000);
    }

    let previouslyOpen;

    function openPopover() {
        ids = ['#template_lnk', '#home_lnk', '#goFeatures', '#learn_lnk', '#primium_lnk'];
        for (let id of ids) {
            $(id).popover({
                trigger: "manual",
                animation: true,
                placement: 'bottom'
            }).on("mouseover mouseenter", function () {
                var _this = this;
                if (previouslyOpen != id) {
                    $("[rel=popover]").not(_this).popover("destroy");
                    $(".popover").remove();
                }
                $(this).popover("show");
                previouslyOpen = id;
                $(".popover").on("mouseleave", function () {
                    setTimeout(() => {
                        if (!$(id).is(":hover")) {
                            $(_this).popover('hide');
                        }
                    }, 100);
                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length && (!$(id).is(":hover"))) {
                        $(_this).popover("hide");
                    }
                }, 200);
            });

        }
        /*    $('.popover').on('show.popover', function() {
           $(this).find('.popover-body').first().stop(true, true).slideDown();
         });

         $('.popover').on('.popover', function() {
           $(this).find('.popover-body').first().stop(true, true).slideUp();
         }); */
    }

    let previouslyOpenFooterCat;

    function openFooterPopover() {
        let ids = ['#lnk_ads', '#lnk_event', '#lnk_social_mda', '#lnk_doc', '#lnk_sprt', '#lnk_invitation', '#lnk_primun', '#lnk_ftr', '#lnk_hm'];
        let placement = 'right'

        for (let id of ids) {
            if (($(document).width() <= 390 && ((id == '#lnk_hm') || (id == '#lnk_ftr'))) || $(document).width() <= 360) {
                placement = 'bottom'
            }
            $(id).popover({
                trigger: "manual",
                animation: true,
                placement: placement
            }).on("mouseover mouseenter", function () {
                var _this = this;
                if (previouslyOpenFooterCat != id) {
                    $("[rel=popover]").not(_this).popover("destroy");
                    $(".popover").remove();
                }
                $(this).popover("show");
                $(".popover").addClass("dark-c-bg");
                previouslyOpenFooterCat = id;
                $(id).children('svg').css('transform', 'rotate(270deg)');
                $(id).children('svg').css({'fill': '#ffffff'});
                $(".popover").on("mouseleave", function () {

                    setTimeout(() => {
                        if (!$(id).is(":hover")) {
                            $(id).children('svg').css('transform', 'rotate(0deg)');
                            $(id).children('svg').css({'fill': '#828EAA'});

                            $(_this).popover('hide');
                        }
                    }, 200);

                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
                        $(id).children('svg').css('transform', 'rotate(0deg)');
                        $(id).children('svg').css({'fill': '#828EAA'});
                        $(_this).popover("hide");
                    }
                }, 200);
            });
        }

    }

    function rotateSvg(id) {
        $(id).children('svg').css('transform', 'rotate(270deg)');
    }

    function rotatebackSvg(id) {
        if (!$(".popover").length) {
            $(id).children('svg').css('transform', 'rotate(0deg)');
        }
    }

    var prev_active_cat, prev_active_sub_cat;

    function openExpansion(id) {
        if (((prev_active_cat != id))) {
            // if (($('.collapse.show').siblings('a').attr('aria-expanded') == "true")) {
            prev_active_cat = id;
            if ($('.collapse.show').attr('id') == 'mob_templates') {
                $('.sub-opt.collapse.show').siblings('a').css('background-color', '#ffffff')
                $('.sub-opt.collapse.show').removeClass('show');
                $('.sub-opt.collapse.show').siblings('a').attr("aria-expanded", false)
            }
            $('.collapse.show').siblings('a').css('background-color', '#ffffff')
            $('.collapse.show').siblings('a').attr("aria-expanded", false);
            $('.collapse.show').removeClass('show');
            $(id).siblings('a').css('background-color', '#daebff')
            // }
        } else {
            setTimeout(() => {
                if (($(id).siblings('a').attr('aria-expanded') == "true")) {
                    $(id).siblings('a').css('background-color', '#daebff')
                } else {
                    if ($(id).attr('id') == 'mob_templates') {
                        $('.sub-opt.collapse.show').siblings('a').css('background-color', '#ffffff')
                        $('.sub-opt.collapse.show').removeClass('show');
                        $('.sub-opt.collapse.show').siblings('a').attr("aria-expanded", false)
                    }
                    $(id).removeClass('show');
                    $(id).siblings('a').attr("aria-expanded", false);
                    $(id).siblings('a').css('background-color', '#ffffff')
                }
            }, 100);
        }

    }

    function openSubcatExpansion(id) {
        if (((prev_active_sub_cat != id))) {
            prev_active_sub_cat = id;
            $('.sub-opt.collapse.show').siblings('a').css('background-color', '#ffffff')
            $('.sub-opt.collapse.show').siblings('a').attr("aria-expanded", false)
            $('.sub-opt.collapse.show').removeClass('show');
            $(id).siblings('a').css('background-color', '#e3f0ff')
        } else {
            setTimeout(() => {
                if (($(id).siblings('a').attr('aria-expanded') == "true")) {
                    $(id).siblings('a').css('background-color', '#daebff')
                } else {
                    $(id).removeClass('show');
                    $(id).siblings('a').css('background-color', '#ffffff')
                    $(id).siblings('a').attr("aria-expanded", false);
                }
            }, 100);
        }
    }

    //this function is use to intialize the freshchat
    function closeActivePopup() {
        $("[rel=popover]").popover("destroy");
        $(".popover").remove();
    }

    function hideOverlay() {
        $('.overlay').hide();
        $(".l-mob-menu-container").css("transform", "scaleX(0)");
        $('.collapse.show').siblings('a').css('background-color', '#ffffff')
        $('.collapse.show').removeClass('show');
        $('.collapse.show').siblings('a').attr("aria-expanded", false);
        $('.sub-opt.collapse.show').siblings('a').css('background-color', '#ffffff')
        $('.sub-opt.collapse.show').removeClass('show');
        $('.sub-opt.collapse.show').siblings('a').attr("aria-expanded", false);
    }

    function categoryRedirect(sub_cat_id) {
        localStorage.setItem("sub_cat_id", sub_cat_id);
        localStorage.setItem("is_l_re", "true");
        localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/editor/' + sub_cat_id);
        setUserkeyword('','','');
    }

    function scrollRightSlider() {
      let leftpos = 1250 + $(`#ttcw1`).scrollLeft();
      $(`#ttcw1`).animate({
        scrollLeft: leftpos
      }, 600);
      setTimeout(() => {
        let leftpos = $(`#ttcw1`).scrollLeft();

        if (leftpos > 10) {
          // $('.shadow-img-left').show();
          $(`#ttcbleft`).show();
        }
        if ( leftpos +950 >= $(`#ttcw1`).width()) {
          // $('.shadow-img').hide();
          $(`#ttcbright`).hide();
        }
      }, 600);
    }

    function scrollLeftSlider() {

let rightPos = $(`#ttcw1`).scrollLeft();
$(`#ttcw1`).animate({
  scrollLeft: rightPos - $(`#ttcw1`).width()
}, 600);
setTimeout(() => {
  let rightPos = $(`#ttcw1`).scrollLeft();

  if (rightPos == 0) {
    $('.shadow-img-left').hide();
    $(`#ttcbleft`).hide();
  }
  if (rightPos >= 0) {
    $('.shadow-img').show();
    $(`#ttcbright`).show();
  }
}, 600);
}

</script>

<br><br><br><br><br>


<br></body>
</html><br>
