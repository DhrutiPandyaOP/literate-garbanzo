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
    $cloudfront_link_path = "https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web";
    $editor = "video-editor";
    if($json_data->content_type == config('constant.CONTENT_TYPE_OF_INTRO_VIDEO')){
      $editor = "intro-editor";
    }
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
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Regular.otf" as="font" crossorigin="">
    <link rel="preload" href="{!! $activation_link_path !!}/fonts/Myriadpro-Bold.otf" as="font" crossorigin="">
    <link async="" rel="preload" href="{!! $activation_link_path !!}/css/new_style.css?v4.86" as="style">
    <link async="" rel="preload" href="{!! $activation_link_path !!}/css/new_css.css?v4.86" as="style">
    <!--  <link async rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          type="text/css" media="all" /> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">

    <link async="" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          type="text/css" media="all">
    <link async="" rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
          as="style">
    <link async="" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
          type="text/css" media="all">
    <link async="" rel="stylesheet" href="{!! $activation_link_path !!}/css/new_css.css?v4.86" type="text/css"
          media="all">
    <link rel="stylesheet" href="{!! $activation_link_path !!}/css/new_style.css?v4.86" type="text/css" media="all">

    <meta property="og:image:height" content="462">
    <meta property="og:image:width" content="883">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{!! $json_data->seo_section->page_detail->page_title !!}">
    <meta property="og:description" content="{!! $json_data->seo_section->page_detail->meta !!}">
    <meta property="og:image" content="{!! $activation_link_path !!}/photoadking.png?v1.6">
    <meta property="og:url" content="{!! $json_data->seo_section->page_detail->canonical !!}">

    <meta name="twitter:title" content="{!! $json_data->seo_section->page_detail->page_title !!}">
    <meta name="twitter:description" content="{!! $json_data->seo_section->page_detail->meta !!}">
    <meta name="twitter:image" content="{!! $activation_link_path !!}/photoadking.png?v1.6">
    <meta name="twitter:url" content="{!! $activation_link_path !!}">
    <meta http-equiv="Expires" content="1">

    <link rel="apple-touch-icon" sizes="57x57" href="{!! $cloudfront_link_path !!}/favicon/apple-icon-57x57.png?v1.3">
    <link rel="apple-touch-icon" sizes="60x60" href="{!! $cloudfront_link_path !!}/favicon/apple-icon-60x60.png?v1.3">
    <link rel="apple-touch-icon" sizes="72x72" href="{!! $cloudfront_link_path !!}/favicon/apple-icon-72x72.png?v1.3">
    <link rel="apple-touch-icon" sizes="76x76" href="{!! $cloudfront_link_path !!}/favicon/apple-icon-76x76.png?v1.3">
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
    <link rel="icon" type="image/png" sizes="96x96" href="{!! $cloudfront_link_path !!}/favicon/favicon-96x96.png?v1.3">
    <link rel="icon" type="image/png" sizes="48x48"
          href="{!! $cloudfront_link_path !!}/favicon/android-icon-48x48.png?v1.3">
    <link rel="icon" type="image/png" sizes="32x32" href="{!! $cloudfront_link_path !!}/favicon/favicon-32x32.png?v1.3">
    <link rel="icon" type="image/png" sizes="16x16" href="{!! $cloudfront_link_path !!}/favicon/favicon-16x16.png?v1.3">
    <link rel="shortcut icon" type="image/icon" href="{!! $cloudfront_link_path !!}/favicon/favicon.ico?v1.3">
    <link rel="icon" type="image/icon" href="{!! $cloudfront_link_path !!}/favicon/favicon.ico?v1.3">
    <link rel="mask-icon" href="{!! $cloudfront_link_path !!}/favicon/safari-pinned-tab.svg" color="#1b94df">
    <link rel="manifest" href="{!! $cloudfront_link_path !!}/favicon/manifest.json?v1.3">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{!! $cloudfront_link_path !!}/favicon/ms-icon-144x144.png?v1.3">
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
<div class="l-body-container" id="mainbody">
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
                                                  class="btn btn-sm w-100 signupbtn_link" target="_blank"
                                                  href="#">Sign
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
                                                   class="remove-style" onclick="hideOverlay()">Flyer</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/posters/"
                                                   class="remove-style" onclick="hideOverlay()">Poster</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/brochures/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Brochure</a>
                                            </li>
                                            <li><a href="{!! $activation_link_path !!}/templates/business-card/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Business
                                                    Card</a></li>
                                            <li><a href="{!! $activation_link_path !!}/templates/gift-certificate/"
                                                   class="remove-style"
                                                   onclick="hideOverlay()">Gift
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
                                                   class="remove-style" onclick="hideOverlay()">Resume</a>
                                            </li>
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
                        <div id="mob_features" class="collapse">
                            <ul style="padding-left: 15px;">
                                <li><a href="{!! $activation_link_path !!}/features/online-graphic-editor/"
                                       class="remove-style"
                                       onclick="hideOverlay()">Graphic
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
                    <div class="col-12 col-lg-12 col-md-12 col-sm-12 l-min-md-pd px-0 m-auto"><a
                                href="{!! $activation_link_path !!}/">

                            <div class="l-logo-div float-left ml-0 pl-0"><img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        data-src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        alt="PhotoADKing Logo" width="180px" height="39px"
                                        id="brand_logo1" name="brand-img1" class="l-blue-logo"> <img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg"
                                        width="180px" height="39px"
                                        data-src="{!! $cloudfront_link_path !!}/photoadking.svg" id="brand_logo2"
                                        name="brand-img2" alt="PhotoADKing Logo" class="l-wht-logo"> <img
                                        src="{!! $cloudfront_link_path !!}/photoadking.svg" width="180px" height="39px"
                                        id="brand_logo3" name="brand-img3"
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
                                <li class="mr-cust-15" id="hd-logn" name="login-btn"><a
                                            href="{!! $activation_link_path !!}/app/#/login" target="_blank">Log In</a></li>
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
                        <h1 class="f-heading font-72px">{!! $json_data->header_section->heading !!}</h1>
                        <p class="s-sub-header c-font-size-subheading margin-0">{!! $json_data->header_section->subheading !!}</p>
                        <!-- 129 -->
                        <a href="{!! $json_data->header_section->button->href !!}" target="_blank" id="hdrnavbtn"
                           class="btn sec-first-button m-bottom-30 c-bg-text mob-btn-set" style="overflow: auto;" onclick="setUserkeyword('','','')">{!! $json_data->header_section->button->text !!}
                            <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                                      fill="#0069FF"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="slider-wrapper sm-hd" style="min-height: 220px;">
                <ul class="slider-container slider-container-cstm" id="slider">
                </ul>
            </div>
            <div class="review-wrapper pt-1 pb-1">
          <span class="desc">{!! $json_data->review_section->review !!}
            <br>
        </span>
                <span class="Author whitespace-nowrap" style="overflow: auto;">
          {!! $json_data->review_section->review_by !!}
        </span>
                <span class="Author whitespace-nowrap" style="overflow: auto;">
          {!! $json_data->review_section->rating->text !!}
        </span>
                <span class="d-inline-block">
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
        </span>
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
                         class="video-player-css s-video dp-lrg-imge" loading="lazy" alt="{!! $json_data->intro_section->alt!!}">
                @endif
                <div class="text-center mt-5">
                    <a href="{!! $json_data->intro_section->button->href !!}" target="_blank"
                       class="Click to sign-up and get started" id="try_now_btn" onclick="setUserkeyword('','','')"
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
            </div>

        </div>
    </div>
    <div class="row mx-0 bkg-white">
        <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container text-center pb-0 pt-3 px-3 px-sm-0">
            <h2 class="c-section-main-heading"
                spellcheck="false">{!! $json_data->template_section->heading !!}</h2>
            <p class="sub-heading sec-four-content mb-5"
               spellcheck="false">{!! $json_data->template_section->subheading !!}</p>

            <div class="card-wrapper col-three col-three-md col-two-sm" id="card-wrapper">
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
                        <h2 class="pb-3 pb-lg-4 sec-three-font-style text-blck c-heading-font">{!! $data->heading !!}</h2>
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
                             class="w-100img pl-2" alt="Flyer maker editor" width="100px">
                    </div>

                @elseif($i % 2 == 0)
                    <div class="col-lg-6 col-md-12 px-0 pl-lg-2 pr-lg-5 m-0 pt-lg-4 pb-lg-5 text-center text-lg-left">
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
                    <div class="w-33p how-to-step-wrapper m-3">
                        <div class="">
                            <div class="position-relative d-flex justify-content-between">
                                <h3 class="how-to-step-title step-title">{!! $how_to_data->title !!}</h3>
                                <div class="how-to-step-number">{!! $i+1 !!}</div>
                            </div>
                            <p class="how-to-step-content step-content c-line-height">{!! $how_to_data->content !!}</p>
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
            <img src="{!! $json_data->tag_section->bg_image->webp_image !!}" style="max-width: fit-content;" loading="lazy"
               alt="{!! $json_data->tag_section->bg_image->alt !!}"
               onerror="this.src='{!! $json_data->tag_section->bg_image->image !!}'">
            </div>
        </div>
    </div>

    <div class="row no-gutters bg-white">

        <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 pt-0 collection-container sec-faq-wrapper bg-blue bg-white">
            <h3 class="my-5 gm-sec-three-heading text-center text-black">{!! $json_data->faqs_section->heading !!}</h3>
            <div class="s-icons-wrapper faq-wrapper">
                <div class="panel-group ul-faq-list sec-accordian" id="acordion">

                    @foreach($json_data->faqs_section->data as $i => $faq_data)
                        @if($i % 2 == 0)
                            <div class="accordian-col">
                                <div class="panel panel-default p-0 sec-faq-item">
                                    <div class="panel-heading p-0">
                                        <li class="ul-faq-list-li bg-light-blue c-custom-border">
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
                                        <div class="panel-body panel-body-desc faq-content-wrapper c-box-shedow">
                                            <p class="m-0 lato-light">
                                                {!! $faq_data->content !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @elseif($i % 2 != 0)
                            <div class="accordian-col">

                                <div class="panel panel-default p-0 sec-faq-item">
                                    <div class="panel-heading p-0">
                                        <li class="ul-faq-list-li bg-light-blue c-custom-border">
                                            <a data-toggle="collapse" data-parent="#acordion"
                                               href="#collpse{!! $i !!}"
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
                                        <div class="panel-body panel-body-desc faq-content-wrapper c-box-shedow">
                                            <p class="m-0 lato-light">
                                                {!! $faq_data->content !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="p-2 last-sec-wrapper bg-white">
        <div class="row col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 last-sec-slider-wrapper mb-5">
            <div class="col-lg-6 col-md-7 col-sm-12 px-2 px-lg-5 py-5 text-center text-md-left mb-3">
                <h2 class="heading  gm-secnd-sec-heading montserrat-bold pb-3 pb-lg-5 mb-0">{!! $json_data->footer_section->heading !!}</h2>
                <p class="sub-heading color-white paragraph-style montserrat-regular pb-3 pb-lg-5 ">{!! $json_data->footer_section->subheading !!}</p>

                <a href="{!! $json_data->footer_section->button->href !!}" target="_blank"
                   class="last-sec-nine-button text-blue" onclick="setUserkeyword('','','')">{!! $json_data->footer_section->button->text !!}
                    <svg viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                              fill="#1980FF"></path>
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
    <!-- <div id="footer"></div> -->
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
                                        c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z
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
                            <script>
                                document.write(new Date().getFullYear())

                            </script>
                            2022 PHOTOADKING. ALL Rights Reserved.
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
                               id="sc_fb_lnk" rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M12.9586 0.394043C15.3023 0.420788 17.4196 0.996805 19.3105 2.12209C21.1789 3.22492 22.7331 4.78863 23.8245 6.66369C24.943 8.56601 25.5155 10.6962 25.5422 13.0541C25.4758 16.2804 24.4582 19.036 22.4894 21.3209C20.5206 23.6057 17.999 25.0193 15.3888 25.5612V16.5156H17.8566L18.4147 12.9609H14.6778V10.6327C14.6571 10.1501 14.8097 9.6759 15.1081 9.29598C15.407 8.91503 15.9333 8.71481 16.6869 8.69531H18.9435V5.58148C18.9111 5.57107 18.6039 5.52987 18.0218 5.4579C17.3616 5.38067 16.6978 5.33941 16.0332 5.33432C14.5289 5.34126 13.3393 5.76558 12.4642 6.60728C11.5891 7.44873 11.1421 8.66614 11.1232 10.2595V12.9609H8.27942V16.5156H11.1232V25.5612C7.91811 25.0193 5.39656 23.6057 3.42778 21.3209C1.45899 19.036 0.441432 16.2804 0.375 13.0541C0.401579 10.696 0.974114 8.5659 2.0926 6.66369C3.18403 4.78863 4.73825 3.22492 6.60664 2.12209C8.49751 0.997021 10.6148 0.421004 12.9586 0.394043V0.394043Z"
                                          fill="#475993"></path>
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/photoadking/" aria-label="photoadking" target="_blank"
                               id="sc_insta_lnk" rel="noreferrer nofollow">
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
                            <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" aria-label="photoadking" id="sc_ytube_lnk"
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
                               id="sc_pintrst_lnk" rel="noreferrer nofollow">
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.0001 0C5.84003 0 0.0224609 5.81757 0.0224609 12.9776C0.0224609 18.3029 3.19975 22.8675 7.80903 24.8812C7.76427 23.9862 7.80903 22.8675 8.03279 21.883C8.30132 20.809 9.68855 14.8124 9.68855 14.8124C9.68855 14.8124 9.28579 13.9621 9.28579 12.7539C9.28579 10.8296 10.4046 9.39757 11.7918 9.39757C12.9553 9.39757 13.5371 10.2926 13.5371 11.3666C13.5371 12.5301 12.7763 14.3201 12.3736 15.9759C12.0603 17.3631 13.0448 18.4819 14.4321 18.4819C16.8934 18.4819 18.5491 15.3046 18.5491 11.5903C18.5491 8.72623 16.6248 6.623 13.1343 6.623C9.19626 6.623 6.73503 9.57652 6.73503 12.8433C6.73503 13.962 7.04827 14.7676 7.58527 15.394C7.80903 15.6626 7.85379 15.7968 7.76427 16.11C7.7195 16.3338 7.5405 16.9156 7.49574 17.1393C7.40622 17.4525 7.13774 17.5868 6.82451 17.4525C4.98974 16.6918 4.18422 14.7228 4.18422 12.4852C4.18422 8.81571 7.27198 4.38542 13.4475 4.38542C18.4148 4.38542 21.6816 7.96547 21.6816 11.814C21.6816 16.9155 18.8624 20.7193 14.7006 20.7193C13.3133 20.7193 11.9708 19.9586 11.5233 19.1083C11.5233 19.1083 10.7626 22.1066 10.6283 22.6884C10.3598 23.6729 9.82279 24.7021 9.33055 25.4629C10.4941 25.8209 11.7471 25.9999 13.0001 25.9999C20.1601 25.9999 25.9777 20.1823 25.9777 13.0223C25.9777 5.86223 20.1602 0 13.0001 0Z"
                                          fill="#CB1F24"></path>
                                </svg>
                            </a>
                            <a href="https://twitter.com/photoadking" aria-label="photoadking" target="_blank"
                               id="sc_twtr_lnk" rel="noreferrer nofollow">
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
                               id="sc_lnkdin_lnk" target="_blank" rel="noreferrer nofollow">
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
    let template_details = `{!! json_encode($json_data->slider_template_section) !!}`;
    let template_list = `{!! json_encode($json_data->template_section->data) !!}`;
    slider_JSON = JSON.parse(template_details);
    cat_list = JSON.parse(template_list);
    template_list = JSON.parse(template_list);
    template_details = JSON.parse(template_details);
</script>


<script>
    var hdrnavbtn = document.getElementById('hdrnavbtn');
    var l_r = localStorage.getItem('l_r');
    var ut = localStorage.getItem('ut');
    let dflt_playstore_url = "https://play.google.com/store/apps/details?id=com.bg.flyermaker&referrer=utm_source%3DOB_PAK";
    let dflt_appstore_url = "https://apps.apple.com/us/app/id1337666644";
    let dflt_cta_text = "Create Your Design Now";
    let app_cta_detail = `{!! json_encode($json_data->app_cta_detail) !!}`;
    app_cta_detail = JSON.parse(app_cta_detail);
    let playstore_url = app_cta_detail.playStoreLink;
    let appstore_url = app_cta_detail.appStoreLink;
    let cta_text = app_cta_detail.app_cta_text;

    if (l_r != null && l_r != '' && typeof l_r != 'undefined' && ut != '' && ut != null && typeof ut != 'undefined') {
        // hdrnavbtn.href = "{!! $activation_link_path !!}/app/#/video-editor/r0jfab0cbeffda";
    } else {
        hdrnavbtn.href = "{!! $activation_link_path !!}/app/#/sign-up";
        hdrnavbtn.target="_blank";
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
        // $('#header').load('../header.html');
        try {
            window.fcWidget.init({
                token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
                host: "https://wchat.freshchat.com"
            });
        } catch (error) {
            console.log(error);
        }
        $('#mob-menu').click(function () {
            $(".l-mob-menu-container").css("transform", "scaleX(1)");
            $('.overlay').show();
        });

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

            try {
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
            }

        } else {
            $('#hd-logn').show();
            $('#hd-login').show();
            $('#rlp-link').show();
        }

        // live id's


        var width = $(window).width();
        if (width > 768) {
            for (var i = 0; i < template_details.length; i++) {
                $('#slider').append(
                    '<li class="slider-list"><div onmouseenter="showSliderVideo(' + i + ')" onclick=\'redirect(' + JSON
                        .stringify(template_details[i].sub_category_id) + "," + JSON.stringify(template_details[i].catalog_id) +
                    "," + JSON.stringify(template_details[i].template_id) + "," + "\"" +  template_details[i].title + "\"" + ')\'  onmouseleave="hideSliderVideo(' + i +
                    ')"><img class="slider-img" loading="lazy" width="220px" height="220px" id ="i' + i + '" src="' +
                    template_details[i].webp_image + '"onerror=" this.src=' + "'" + template_details[i].image + "'" +
                    '"alt="' + template_details[i].title +
                    '"><video class="slider-img slider-video" preload="none" id = "v' + i +
                    '" autoplay loop muted playsinline><source type="video/mp4"' + ' src="' +
                    template_details[i].video + '?2"alt="' + template_details[i].title +
                    '" ></video><div id = "playButton' + i +
                    '" class= "play-btn-ic"><img  src="{!! $cloudfront_link_path !!}/play.svg" aria-label="photoadking" class="playButton-ic playButton-ic-design" ></div></div>' /* <div class="slide-title"> + template_details[i].title + */ /* </div> */ +
                    '<div ><img src="{!! $cloudfront_link_path !!}/Spinner1.gif" class="video-loader slider-video-loader" id = "ldrid' + i +
                    '" ></div></li>'
                );

            }
        } else {

            for (var i = 0; i < template_details.length - 4; i++) {
                $('#slider').append(
                    '<li class="slider-list"><div class="position-relative" onclick="showSliderVideo(' + i +
                    ')"  onmouseleave="hideSliderVideo(' + i + ')" ><img class="slider-img" loading="lazy" width="220px" height="220px"id ="i' + i + '" src="' +
                    template_details[i].webp_image + '"onerror=" this.src=' + "'" + template_details[i].image + "'" +
                    '"alt="' + template_details[i].title +
                    '"><video class="slider-img slider-video" preload="none" id = "v' + i +
                    '" autoplay loop muted playsinline><source type="video/mp4"' + ' src="' +
                    template_details[i].video + '?2"alt="' + template_details[i].title +
                    '" ></video><div id = "playButton' + i +
                    '" class= "play-btn-ic"><img  src="{!! $cloudfront_link_path !!}/play.svg" aria-label="photoadking" class="playButton-ic playButton-ic-design" ></div><div ><img src="{!! $cloudfront_link_path !!}/Spinner1.gif" class="video-loader slider-video-loader" id = "ldrid' +
                    i + '" ></div></div>' /* <div class="slide-title"> + template_details[i].title + */ /* </div> */ +
                    '</li>'
                );

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
                autoplay: false,
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
            localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/{!! $editor !!}/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
            Object.assign(document.createElement('a'), { target: '_blank', href:'{!! $activation_link_path !!}/app/#/{!! $editor !!}/' + sub_cat_id + '/' + catalog_id + '/' + template_id}).click();
            setUserkeyword(title,'','');
        }

        // $('#footer').load('../footer.html');
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
        if (width >= 767) {
            for (var i = 0; i < template_list.length; i++) {
                $('#card-wrapper').append(
                    '<div class="content-item video-content-item card-ol-block cursor-pointer" onmouseenter="showVideo(' +
                    i + ')"  onclick="playVideoInModel(' + i + ')"   onmouseleave="hideVideo(' + i +
                    ')"><img draggable="false" loading="lazy" width="322px" height="184px"id ="img' + i + '" src="/spinner.svg" data-src="' +
                    template_list[i].webp_image + '"onerror=" this.src=' + "'" + template_list[i].image + "'" +
                    '"alt="PhotoADKing Logo"><video style="max-width: 100%;max-height: 550px;"  class="template-video" preload="none" id = "vdo' + i +
                    '" autoplay loop muted playsinline><source id="vdosrc' + i +
                    ' " draggable="false" type="video/mp4" src="' +
                    template_list[i].video +
                    '?2" alt="PhotoADKing Logo"></video><div> <img src="{!! $cloudfront_link_path !!}/Spinner1.gif" class="video-loader" id = "ldr' +
                    i + '" ></div><div id = "playButton' + i +
                    '" class= "play-btn-ic"><img  src="{!! $cloudfront_link_path !!}/play.svg" aria-label="photoadking" class="playButton-ic playButton-ic-design" ></div><div class= "seekbar-container" id="seekbar' +
                    i + '"><div class="custom-seekbar" id="custom-seekbar' + i + '"><span id="cs' + i +
                    '"></span></div></div><a class= "editVideo-txt" aria-label="photoadking" target="_blank" href="{!! $activation_link_path !!}/app/#/{!! $editor !!}/' +
                    template_list[i].sub_category_id + "/" + template_list[i].catalog_id + "/" + template_list[i]
                        .template_id + '" onclick=\'storeDetails(' + JSON.stringify(cat_list[i].sub_category_id) + "," +
                    JSON.stringify(cat_list[i]
                        .catalog_id) +
                    "," + JSON.stringify(cat_list[i].template_id) +
                    "," + "\"" + cat_list[i].sub_category_name + "\"" +
                    "," + "\"" + cat_list[i].catalog_name + "\"" +
                    ')\'><div class="edit-video-button-design mx-auto" id = "editButton' + i +
                    '"><img src="{!! $cloudfront_link_path !!}/Edit.svg" alt="" class="editButton-ic">EDIT VIDEO</div></a></div>'
                );
            }
        } else {
            for (var i = 0; i < template_list.length - 4; i++) {
                $('#card-wrapper').append(
                    '<div class="content-item video-content-item card-ol-block cursor-pointer" onclick="showVideo(' + i +
                    ')"  onmouseleave="hideVideo(' + i +
                    ')"><img draggable="false" loading="lazy" width="322px" height="184px"id ="img' + i + '" src="/spinner.svg" data-src="' +
                    template_list[i].webp_image + '"onerror=" this.src=' + "'" + template_list[i].image + "'" +
                    '"alt="PhotoADKing Logo"><video class="template-video" preload="none" id = "vdo' + i +
                    '" autoplay loop muted playsinline><source id="vdosrc' + i +
                    ' " draggable="false" type="video/mp4" src="' +
                    template_list[i].video +
                    '?2"alt="PhotoADKing Logo"></video><div> <img src="{!! $cloudfront_link_path !!}/Spinner1.gif" class="video-loader" id = "ldr' +
                    i + '" ></div><div id = "playButton' + i +
                    '" class= "play-btn-ic"><img  src="{!! $cloudfront_link_path !!}/play.svg" aria-label="photoadking"  class="playButton-ic playButton-ic-design" ></div><div class= "seekbar-container"   id="seekbar' +
                    i + '"><div class="custom-seekbar"  id="custom-seekbar' + i + '"><span id="cs' + i +
                    '"></span></div></div><a class= "editVideo-txt" aria-label="photoadking" target="_blank" href="{!! $activation_link_path !!}/app/#/"><div class="edit-video-button-design mx-auto" id = "editButton' +
                    i + '"><img src="{!! $cloudfront_link_path !!}/Edit.svg" alt="" class="editButton-ic">EDIT VIDEO</div></a></div>'
                );
            }
        }
        init();
        if (width >= 767) {
            let template_type = '{!! $json_data->is_portrait !!}';
            if (template_type == "0") {
                for (var i = 0; i < 8; i++) {
                    if (template_list[i]) {
                        $('#ftr-first-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + template_list[i].alt +
                            '"src="' +
                            template_list[i].webp_image + '" onerror=" this.src=' + "'" + template_list[i].image + "'" +
                            '" alt="' + template_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 8; i < 12; i++) {
                    if (template_list[i]) {
                        $('#ftr-scnd-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + template_list[i].alt +
                            '" src="' +
                            template_list[i].webp_image + '" onerror=" this.src=' + "'" + template_list[i].image + "'" +
                            '" alt="' + template_list[i].alt + '"></div>'
                        );
                    }
                }
            } else {
                for (var i = 0; i < 4; i++) {
                    if (template_list[i]) {
                        $('#ftr-first-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + template_list[i].alt +
                            '"src="' +
                            template_list[i].webp_image + '" onerror=" this.src=' + "'" + template_list[i].image + "'" +
                            '" alt="' + template_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 4; i < 8; i++) {
                    if (template_list[i]) {
                        $('#ftr-scnd-row').append('<div class=" mb-3"><img class="ftr-slider-image" loading="lazy" alt="' + template_list[i].alt +
                            '" src="' +
                            template_list[i].webp_image + '" onerror=" this.src=' + "'" + template_list[i].image + "'" +
                            '" alt="' + template_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 8; i < 12; i++) {
                    if (template_list[i]) {
                        $('#ftr-third-row').append('<div class=" mb-3 "><img class="ftr-slider-image" loading="lazy" alt="' + template_list[i].alt +
                            '"src="' +
                            template_list[i].webp_image + '" onerror=" this.src=' + "'" + template_list[i].image + "'" +
                            '" alt="' + template_list[i].alt + '"></div>'
                        );
                    }

                }
                for (var i = 0; i < 4; i++) {
                    if (template_list[i]) {
                        $('#ftr-fourth-row').append('<div class=" mb-3 "><img class="ftr-slider-image" loading="lazy" alt="' + slider_JSON[i].alt +
                            '"src="' +
                            template_details[i].webp_image + '" onerror=" this.src=' + "'" + template_details[i].image + "'" +
                            '" alt="' + template_details[i].alt + '"></div>'
                        );
                    }
                }
            }
        }
    });

    setUserkeyword('','','');

    setTimeout(function () {
        $('.play-btn-ic').css('display', 'block');

    }, 5000);
    var clearTimeOut, clearTimeOut1;

    function showSliderVideo(i) {
        var str = "#v" + i;
        var s = "#i" + i;
        var ldrid = "#ldrid" + i;
        $(ldrid).show();
        clearTimeOut1 = setTimeout(() => {
            $(str)[0].preload = "auto";
            if ($(str)[0] && $(str)[0].readyState === 4) {
                $(s).hide();
                $(ldrid).hide();
                $(str).show();
                $(str)[0].play();
            } else {
                $(str)[0].onloadeddata = async function () {
                    if ($(str)[0] && $(str)[0].readyState === 4) {
                        $(s).hide();
                        $(ldrid).hide();
                        $(str).show();
                        $(str)[0].play();
                    }
                }
            }
        }, 2000);
    }

    function showVideo(i) {
        var str = "#vdo" + i;
        var s = "#img" + i;
        var st = "vdo" + i;
        var video = document.getElementById(st);
        var customSeekbar = '#custom-seekbar' + i;
        CS = '#cs' + i;
        var seekbarContainer = '#seekbar' + i;
        var editButton = '#editButton' + i;
        var ldrid = "#ldr" + i;
        $(ldrid).show();
        clearTimeOut = setTimeout(() => {
            $(str)[0].preload = "auto";
            if ($(str)[0].readyState === 4) {
                $(s).hide();
                $(ldrid).hide();
                $(str).css("display", "block");
                $(str)[0].play();
                $(seekbarContainer).show();
                $(customSeekbar).show();
                $(CS).show();
            } else {
                $(str)[0].onloadeddata = async function () {
                    if ($(str)[0] && $(str)[0].readyState === 4) {
                        $(s).hide();
                        $(ldrid).hide();
                        $(str).show();
                        $(str)[0].play();
                        $(seekbarContainer).show();
                        $(customSeekbar).show();
                        $(CS).show();
                    }
                }
            }
            video.ontimeupdate = function () {
                var percentage = (video.currentTime / video.duration) * 100;
                $(CS).css("width", percentage + "%");
            };
        }, 2000);
        $(editButton).show();
    }

    function hideSliderVideo(i) {
        var str = "#v" + i;
        var s = "#i" + i;
        var ldrid = "#ldrid" + i;
        $(ldrid).hide();
        if (this.clearTimeOut1) {
            clearTimeout(this.clearTimeOut1);
        }
        $(s).show();
        $(str).hide();
    }

    function hideVideo(i) {
        var str = "#vdo" + i;
        var s = "#img" + i;
        var customSeekbar = '#custom-seekbar' + i;
        CS = '#cs' + i;
        var seekbarContainer = '#seekbar' + i;
        var editButton = '#editButton' + i;
        var ldrid = "#ldr" + i;
        $(ldrid).hide();
        if (this.clearTimeOut) {
            clearTimeout(this.clearTimeOut);
        }
        $(editButton).hide();
        $(str).hide();
        $(seekbarContainer).hide();
        $(customSeekbar).hide();
        $(CS).hide();
        $(s).show();
        $(str)[0].pause();
    }

    function playVideoInModel(i) {
        var str = "#vdo" + i;
        $(str).find('source').each(function () {
            var video = $('<video />', {
                id: 'video',
                src: $(this).attr("src"),
                type: 'video/mp4',
                preload: "auto",
                autoplay: true,
                loop: true,
                class: "video-player"
            });
            video.appendTo($('#video-modal-container'));
            var customSeekbar = '#custom-seekbar-mdl';
            var CSmdl = '#cs-mdl';
            var seekbarContainer = '#seekbar-mdl';
            setTimeout(() => {
                if ($(video)[0].readyState === 4) {
                    $(video).css("display", "block");
                    $(video)[0].play();
                    $(seekbarContainer).show();
                    $(customSeekbar).show();
                    $(CSmdl).show();
                } else {
                    $(video)[0].onloadeddata = async function () {
                        if ($(video)[0] && $(video)[0].readyState === 4) {
                            $(video).show();
                            $(video)[0].play();
                            $(seekbarContainer).show();
                            $(customSeekbar).show();
                            $(CSmdl).show();
                        }
                    }
                }
                $(video)[0].ontimeupdate = function () {
                    var percentage = ($(video)[0].currentTime / $(video)[0].duration) * 100;
                    $(CSmdl).css("width", percentage + "%");
                };
            }, 100);
        });
        $('#my_modal').modal('show');
    }

    function closeDialog() {
        $('#video-modal-container').empty();
        $('#my_modal').modal('hide');
    }

    $('#my_modal').on('hidden.bs.modal', function () {
        $('#video-modal-container').empty();
    });

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

    function storeDetails(sub_cat_id, catalog_id, template_id,sub_cat_name,catalog_name) {
        localStorage.setItem("sub_cat_id", sub_cat_id);
        localStorage.setItem("catalog_id", catalog_id);
        localStorage.setItem("template_id", template_id);
        localStorage.setItem("is_l_re", "true");
        localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/{!! $editor !!}/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
        setUserkeyword('',sub_cat_name,catalog_name);
    }

    function categoryRedirect(sub_cat_id) {
        localStorage.setItem("sub_cat_id", sub_cat_id);
        localStorage.setItem("is_l_re", "true");
        localStorage.setItem("re_url", '{!! $activation_link_path !!}/app/#/{!! $editor !!}/' + sub_cat_id);
        setUserkeyword('','','');
    }
    function setUserkeyword(title,sub_cat_name,catalog_name){
      var pathString = window.location.pathname;
            var visitArr = pathString.substring(1, pathString.length - 1).split("/");
            if (visitArr[0] == "templates" || visitArr[0] == "design")
            {
                visitArr[1] = visitArr[1].toString().replace(/\w+[.!?]?$/, '').slice(0, -1);
                if(title != '')
                 {
                    if(title.includes("video") == false)
                    {
                        title = title.concat(" ", "video");
                    }
                  visitArr.pop();
                  visitArr.push(title);
                 }
                 if(sub_cat_name != '' && catalog_name != ''){
                   visitArr.pop();
                   catalog_name = catalog_name.concat(" ", "video")
                   visitArr.push(sub_cat_name,catalog_name);
                 }
                 if(window.location.search != " " && window.location.search.includes("utm_campaign"))
                 {
                   visitArr.push('utm_campaign');
                 }
                 localStorage.setItem("userVisited", visitArr);
            }
            else{
                  visitArr = Array.from(visitArr);
                  visitArr.unshift('design');
                  visitArr[1] = visitArr[1].toString().replace(/\w+[.!?]?$/, '').slice(0, -1);
                  if(title != '')
                  {
                    if(title.includes("video") == false)
                    {
                        title = title.concat(" ", "video");
                    }
                    visitArr.push(title);
                  }
                 if(sub_cat_name != '' && catalog_name != '')
                 {
                    catalog_name = catalog_name.concat(" ", "video")
                    visitArr.push(sub_cat_name,catalog_name);
                 }
                 if(window.location.search != " " && window.location.search.includes("utm_campaign"))
                 {
                   visitArr.push('utm_campaign');
                 }
                 localStorage.setItem("userVisited", visitArr);
               }
    }
</script>

</body>
</html>
