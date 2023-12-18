<!-- ******************************************************************************
OptimumBrew Technology Pvt. Ltd.

Title:            photoadking
File:             Template Page HTML
Since:            22 Sept, 2018
Author:           Mayur kukadiya, Umesh Patadiya
Email:            mayur.optimumbrew@gmail.com, umeshpatadiya1995@gmail.com

****************************************************************************** -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>{!! $main_template['page_detail']->page_title !!}</title>
  <meta name="theme-color" content="#317EFB" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="Description"
        content="{!! $main_template['page_detail']->meta !!}" />
  <meta name="COPYRIGHT" content="PhotoADKing" />
  <meta name="AUTHOR" content="PhotoADKing" />
  <link rel="canonical" href="{!! $main_template['page_detail']->canonical !!}" />

  <link async rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/bootstrap.min.css" as="style">

  <link async rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/new_css.css?v4.86" as="style">
  <link async rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/new_style.css?v4.86" as="style">

  <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/bootstrap.min.css" type="text/css" media="all" />

  <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/new_css.css?v4.86" type="text/css" media="all"/>
  <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/new_style.css?v4.86" type="text/css" media="all"/>

  <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <!-- <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/new_style.css?v4.86" type="text/css" media="all"/> -->
  <meta property="og:image:height" content="462">
  <meta property="og:image:width" content="883">
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="{!! $main_template['page_detail']->page_title !!}" />
  <meta property="og:description"
        content="{!! $main_template['page_detail']->meta !!}" />
  <meta property="og:image" content="https://photoadking.com/photoadking.png?v1.5" />
  <meta property="og:url" content="{!! $main_template['page_detail']->canonical !!}" />

  <meta name="twitter:title" content="{!! $main_template['page_detail']->page_title !!}" />
  <meta name="twitter:description"
        content="{!! $main_template['page_detail']->meta !!}" />
  <meta name="twitter:image" content="https://photoadking.com/photoadking.png?v1.5" />
  <meta name="twitter:url" content="https://photoadking.com">
  <meta http-equiv="Expires" content="1" />

  <link rel="apple-touch-icon" sizes="57x57" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-57x57.png?v1.3">
  <link rel="apple-touch-icon" sizes="60x60" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-60x60.png?v1.3">
  <link rel="apple-touch-icon" sizes="72x72" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-72x72.png?v1.3">
  <link rel="apple-touch-icon" sizes="76x76" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-76x76.png?v1.3">
  <link rel="apple-touch-icon" sizes="114x114"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-114x114.png?v1.3">
  <link rel="apple-touch-icon" sizes="120x120"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-120x120.png?v1.3">
  <link rel="apple-touch-icon" sizes="144x144"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-144x144.png?v1.3">
  <link rel="apple-touch-icon" sizes="152x152"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-152x152.png?v1.3">
  <link rel="apple-touch-icon" sizes="180x180"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/apple-icon-180x180.png?v1.3">
  <link rel="icon" type="image/png" sizes="512x512"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/android-icon-512x512.png?v1.3">
  <link rel="icon" type="image/png" sizes="192x192"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/android-icon-192x192.png?v1.3">
  <link rel="icon" type="image/png" sizes="96x96" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/favicon-96x96.png?v1.3">
  <link rel="icon" type="image/png" sizes="48x48"
        href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/android-icon-48x48.png?v1.3">
  <link rel="icon" type="image/png" sizes="32x32" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/favicon-32x32.png?v1.3">
  <link rel="icon" type="image/png" sizes="16x16" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/favicon-16x16.png?v1.3">
  <link rel="shortcut icon" type="image/icon" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/favicon.ico?v1.3">
  <link rel="icon" type="image/icon" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/favicon.ico?v1.3">
  <link rel="mask-icon" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/safari-pinned-tab.svg" color="#1b94df">
  <link rel="manifest" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/manifest.json?v1.3">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/ms-icon-144x144.png?v1.3">
  <meta name="msapplication-config" content="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/favicon/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">
  <!-- <link rel="preload" href="https://photoadking.com/fonts/Myriadpro-Regular.otf" as="font" crossorigin>
  <link rel="preload" href="https://photoadking.com/fonts/Myriadpro-Bold.otf" as="font" crossorigin> -->
  <link rel="preload" href="https://photoadking.com/fonts/Montserrat-Medium.ttf" as="font" crossorigin>
  <link rel="preload" href="https://photoadking.com/fonts/Montserrat-Bold.ttf" as="font" crossorigin>
  <link rel="preload" href="https://photoadking.com/fonts/Montserrat-SemiBold.ttf" as="font" crossorigin>
  <link rel="preload" href="https://photoadking.com/fonts/Montserrat-Regular.ttf" as="font" crossorigin>
  <link rel="preload" as="image" href="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/tmplate_bg.png" crossorigin>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" type="text/css" media="all" async/> -->
  <!-- <link rel="preload" as="font" type="font/woff2" crossorigin href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0"/> -->
  <script src="https://apis.google.com/js/platform.js" async rel="preconnect"></script>
  <script src="https://www.googletagmanager.com/gtag/js?id=G-QT2F0WB1Q1" rel="preconnect"></script>
  <script src="https://www.googletagmanager.com/gtag/js?id=AW-859101740" rel="preconnect"></script>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script> -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script> -->
  <!-- <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script> -->
  <script src="https://unpkg.com/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
    <!-- or -->
    <!-- <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script> -->
  <script>
    //Start Page traking script
    //End Page traking script

    if (window.location.host == 'photoadking.com') {
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'AW-859101740');
      gtag('config', 'G-QT2F0WB1Q1');
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

        ga('create', 'G-QT2F0WB1Q1', 'auto');
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
  @php
    $is_rating = 0;
    $faqs = $main_template['faqs'];
    $guide_steps = $main_template['guide_steps'];
    $faqs_schema = "";
    $guide_schema = "";
    $rating_schema = "";

    if(count($faqs) > 0){
      foreach ($faqs as $key=> $faq){
       if($key != 0){
           $faqs_schema .=",";
         }
         $faqs_schema .= '{
           "@type": "Question",
           "name": "'.$faq['question'].'",
           "acceptedAnswer": {
             "@type": "Answer",
             "text": "'.$faq['answer'].'"
           }
         }';
      }

      $faqs_schema = '{
         "@context": "https://schema.org",
         "@type": "FAQPage",
         "name": "FAQs",
         "mainEntity": ['.$faqs_schema.']
       }';
    }

   if(count($guide_steps) > 0){
      foreach ($guide_steps as $key => $guide){
         if($key != 0){
           $guide_schema .=",";
         }
         $position = $key+1;
         $guide_schema .= '{
           "@type": "HowToStep",
           "position": '.$position.',
           "name": "'.$guide['heading'].'",
           "itemListElement": {
             "@type": "HowToDirection",
             "text":"'.$guide['description'].'"
           }
         }';
      }
      $guide_schema = '{
         "@context": "https://schema.org",
         "@type": "HowTo",
         "name": "'.$main_template['guide_detail']->guide_heading.'",
         "step": ['.$guide_schema.']
       }';
   }


   if($main_template['rating_schema']->name !='' && $main_template['rating_schema']->description !="" && $main_template['rating_schema']->ratingValue !="" && $main_template['rating_schema']->reviewCount !=""){
     $is_rating=1;
     $rating_schema = '{
      "@context": "https://schema.org",
      "@type": "Product",
      "brand": "PhotoADKing",
      "name": "'.$main_template['rating_schema']->name.'",
      "description": "'.$main_template['rating_schema']->description.'",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": '.$main_template['rating_schema']->ratingValue.',
        "bestRating": 5,
        "reviewCount": '.$main_template['rating_schema']->reviewCount.'
      }
    }';
   }
  @endphp

  @if(count($faqs) > 0)
    <script type="application/ld+json">
      {!! $faqs_schema !!}
    </script>
  @endif

  @if(count($guide_steps) > 0)
    <script type="application/ld+json">
      {!! $guide_schema !!}
    </script>
  @endif

  @if($is_rating)
    <script type="application/ld+json">
      {!! $rating_schema !!}
    </script>
@endif
  <!--  <noscript><img height="1" width="1" style="display:none"
                  src="https://www.facebook.com/tr?id=245438776057897&ev=PageView&noscript=1" /></noscript> -->
  <!-- End Facebook Pixel Code -->
</head>

<body class="position-relative">
<div class="w-100">
  <div class="fchat-wrapper-div">
    <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/BG_shadow.png" id="chat_icon" width="90px" height="90px" onclick="initializeFeshchat()" alt="freshchat icon"
         class="fchat-bg-style">
    <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/chat.svg" id="chat_ic" class="fchat-chat-icon" height="24px" width="24px" alt="chat icon"
         onclick="initializeFeshchat()">

    <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/rolling.svg" id="loader_ic" alt="loader icon" class="disply-none fchat-loder-icon" height="24px"
         width="24px">
  </div>

</div>
<div class="l-body-container bg-white" id="mainbody">
    <div class="c-con-pedding pb-4">
    <div class="w-100 privacy-header-bg l-blue-bg position-relative sec-first c-header-template-bg" id="Home">
      <!-- <div id="header"></div> -->
      <div class="w-100">
        <ul class="l-mob-menu-container " id="mobmenu">
          <li class="px-4 pb-1 pt-cust-18"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/" target="_blank" class="btn btn-sm w-100 loginbtn_link" href="#">Log
              In</a>
          </li>
          <li class="px-4 py-2 mb-2"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/sign-up" target="_blank" class="btn btn-sm w-100 signupbtn_link" href="#">Sign
              Up
              Free</a></li>
          <li>
            <a onmousedown="openExpansion('#mob_create')" data-parent="#Home" class="position-relative" href="#mob_create" data-toggle="collapse" aria-expanded="false">Create<svg class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                  <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                  </path>
                </g>
              </svg></a>
            <div id="mob_create" class="collapse in">
              <ul style="padding-left: 15px;">
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/online-graphic-maker/" class="remove-style" onclick="hideOverlay()">Graphic
                    Maker</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/video-templates/" class="remove-style" onclick="hideOverlay()">Promo Video
                    Maker</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/youtube-intro-maker/" class="remove-style" onclick="hideOverlay()">Intro
                    Maker</a></li>
              </ul>
            </div>
          </li>
          <li>
            <a onclick="openExpansion('#mob_templates')" data-parent="#Home" class="position-relative" href="#mob_templates" data-toggle="collapse" aria-expanded="false">templates<svg class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                  <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                  </path>
                </g>
              </svg></a>
            <div id="mob_templates" class="collapse">
              <ul style="padding-left: 0px;">
                <li><a onclick="openSubcatExpansion('#social')" class="position-relative" href="#social" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Social Media</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="social" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/facebook-cover/" class="remove-style" onclick="hideOverlay()">Facebook
                          Cover </a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-media-post/facebook-post/" class="remove-style" onclick="hideOverlay()">Facebook
                          Post</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/instagram-post/" class="remove-style" onclick="hideOverlay()">Instagram
                          Post</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-story/instagram-story/" class="remove-style" onclick="hideOverlay()">Instagram
                          Story</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/linkedin-banner/" class="remove-style" onclick="hideOverlay()">LinkedIn
                          Cover</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/pinterest-post/" class="remove-style" onclick="hideOverlay()">Pinterest
                          Graphic</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-channel-art/" class="remove-style" onclick="hideOverlay()">YouTube
                          Channel Art</a>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-thumbnail/" class="remove-style" onclick="hideOverlay()">YouTube
                          Thumbnail</a></li>
                    </ul>
                  </div>
                </li>
                <li><a onclick="openSubcatExpansion('#Marketing')" class="position-relative" href="#Marketing" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Marketing</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="Marketing" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/flyers/" class="remove-style" onclick="hideOverlay()">Flyer</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/posters/" class="remove-style" onclick="hideOverlay()">Poster</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/brochures/" class="remove-style" onclick="hideOverlay()">Brochure</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/business-card/" class="remove-style" onclick="hideOverlay()">Business
                          Card</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/gift-certificate/" class="remove-style" onclick="hideOverlay()">Gift
                          Certificate</a></li>
                    </ul>
                  </div>
                </li>
                <li><a onclick="openSubcatExpansion('#Blogging')" class="position-relative" href="#Blogging" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Blogging</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="Blogging" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/infographics/" class="remove-style" onclick="hideOverlay()">Infographic</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/book-cover/e-book/" class="remove-style" onclick="hideOverlay()">E-Book</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/blog-image/" class="remove-style" onclick="hideOverlay()">Blog
                          Graphic</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/storyboard/" class="remove-style" onclick="hideOverlay()">Storyboard</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/" class="remove-style" onclick="hideOverlay()">Album
                          Cover </a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/email-header/" class="remove-style" onclick="hideOverlay()">Email
                          Header</a></li>
                    </ul>
                  </div>
                </li>
                <li><a onclick="openSubcatExpansion('#personal')" class="position-relative" href="#personal" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Personal</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="personal" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invitations/" class="remove-style" onclick="hideOverlay()">Invitation</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/resume/" class="remove-style" onclick="hideOverlay()">Resume</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/biodata/" class="remove-style" onclick="hideOverlay()">Bio-Data</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/planners/" class="remove-style" onclick="hideOverlay()">Planner</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/postcard-templates/" class="remove-style" onclick="hideOverlay()">Postcard</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/greeting-cards/" class="remove-style" onclick="hideOverlay()">Greeting</a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li><a onclick="openSubcatExpansion('#Business')" class="position-relative" href="#Business" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Business</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="Business" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/logo/" class="remove-style" onclick="hideOverlay()">Logo</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invoice-templates/" class="remove-style" onclick="hideOverlay()">Invoice</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/letterhead-maker/ " class="remove-style" onclick="hideOverlay()">Letterhead</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/presentation-maker/" class="remove-style" onclick="hideOverlay()">Presentation</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/certificate/" class="remove-style" onclick="hideOverlay()">Certificate</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/restaurant-menu/" class="remove-style" onclick="hideOverlay()">Menu</a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li><a onclick="openSubcatExpansion('#Trending')" class="position-relative" href="#Trending" data-toggle="collapse" aria-expanded="false"><span class="pl-3">Trending</span><svg class=" ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                      <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                        <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                        </path>
                      </g>
                    </svg></a>
                  <div id="Trending" class="sub-opt collapse">
                    <ul style="padding-left: 30px;">
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/animated-video-maker/" class="remove-style" onclick="hideOverlay()">Animated Video</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-story/whatsapp-status/" class="remove-style" onclick="hideOverlay()">Status Video</a>
                      </li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-intro-maker/" class="remove-style" onclick="hideOverlay()">Intro
                          Video</a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/outro-video-maker/" class="remove-style" onclick="hideOverlay()">Outro
                          Video </a></li>
                      <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/design/video-flyer-maker/" class="remove-style" onclick="hideOverlay()">Video
                          Flyer</a></li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </li>

          <li><a onclick="openExpansion('#mob_features')" class="position-relative" data-parent="#Home" href="#mob_features" data-toggle="collapse" aria-expanded="false">Features<svg class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                  <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                  </path>
                </g>
              </svg></a>
            <div id="mob_features" class="collapse">
              <ul style="padding-left: 15px;">
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-graphic-editor/" class="remove-style" onclick="hideOverlay()">Graphic
                    Editor</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-video-maker/" class="remove-style" onclick="hideOverlay()">Video
                    Maker</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/free-intro-maker/" class="remove-style" onclick="hideOverlay()">Intro
                    Editor</a></li>
              </ul>
            </div>
          </li>
          <li><a onclick="openExpansion('#mob_learn')" class="position-relative" href="#mob_learn" data-parent="#Home" data-toggle="collapse" aria-expanded="false">Learn<svg class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                  <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                  </path>
                </g>
              </svg></a>
            <div id="mob_learn" class="collapse">
              <ul style="padding-left: 15px;">
                <li><a href="https://blog.photoadking.com/" class="remove-style" onclick="hideOverlay()">Getting
                    Started</a></li>
                <li><a href="https://blog.photoadking.com/ideas-and-inspirations/" class="remove-style" onclick="hideOverlay()">Design
                    Inspiration</a></li>
                <li><a href="https://blog.photoadking.com/video-marketing/" class="remove-style" onclick="hideOverlay()">Marketing Mania</a>
                </li>
                <li><a href="https://blog.photoadking.com/how-to-make/" class="remove-style" onclick="hideOverlay()">Tutorials</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/whats-new/" class="remove-style" onclick="hideOverlay()">What's New</a></li>
              </ul>
            </div>
          </li>
          <li><a onclick="openExpansion('#mob_pricing')" class="position-relative" data-parent="#Home" href="#mob_pricing" data-toggle="collapse" aria-expanded="false">Pricing<svg class="ul-header-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                  <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z">
                  </path>
                </g>
              </svg></a>
            <div id="mob_pricing" class="collapse">
              <ul style="padding-left: 15px;">
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" class="remove-style" onclick="hideOverlay()">Free</a></li>
                <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" class="remove-style" onclick="hideOverlay()">Pro</a></li>
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
          <div class="col-12 col-lg-12 col-md-12 col-sm-12 l-min-md-pd px-0 m-auto"> <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/">

              <div class="l-logo-div float-left ml-0 pl-0"> <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" data-src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" alt="image not available" width="180px" height="39px" id="brand_logo1" name="brand-img1" class="l-blue-logo"> <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" width="180px" height="39px" data-src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" id="brand_logo2" name="brand-img2" alt="image not available" class="l-wht-logo"> <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" width="180px" height="39px" id="brand_logo3" name="brand-img3" data-src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" alt="image not available" class="l-white-logo"></div>
            </a>
            <div class="float-left l-menu align-items-center">
              <ul class="l-menu-container  mt-10px">
                <li><a id="home_lnk" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/online-graphic-maker/" name="home-btn" role="button" data-html="true" data-content='<div class="row m-0 p-8px mt-1 createbtn">
                  <div class="col-4 m-0 pl-20">
                    <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/online-graphic-maker/"><div class="popover-image"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Create_graphic_design.png" width="160px" height="111px" alt="" ></div></a>
                    <a class="remove-style" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/online-graphic-maker/"><h4 class="header-popover-heading">Graphic Maker</h4></a>
                    <p class="header-popover-desc">Create stunning graphics with graphic design templates. </p>
                  </div>
                  <div class="col-4 m-0 pl-20">
                    <div class="text-center popover-image">
                      <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/video-templates/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Create_promo_video.png" width="160px" height="111px" alt="" class="popover-image"></a>
                    </div>
                    <a class="remove-style" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/video-templates/"><h4 class="header-popover-heading">Promo Video</h4></a>
                    <p class="header-popover-desc">Create short video ads with promo video maker. </p>
                  </div>
                  <div class="col-4 m-0 pl-20">
                    <div class="popover-image text-right ">
                    <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/youtube-intro-maker/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Create_intro_maker.png" width="160px" height="111px" alt="" class="popover-image text-right" >
                    </div>
                    <a class="remove-style" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/create/youtube-intro-maker/"><h4 class="header-popover-heading">Intro Maker</h4></a>
                    <p class="header-popover-desc">Create stunning YouTube video intro with templates.</p>
                  </div>
                </div>'>Create </a></li>
                <li><a id="template_lnk" name="template-btn" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/" role="button" data-html="true" data-content='<div class="row m-0 templatebtn p-8px mt-1">
                    <div class="col-2 m-0 pl-20">
                      <a ><div class="popover-image"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_Social_media.png" width="160px" height="111px" alt="" ></div></a>
                      <a  class="remove-style"><h4 class="header-popover-heading">Social Media</h4></a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/facebook-cover/">Facebook Cover </a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-media-post/facebook-post/">Facebook Post</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/instagram-post/">Instagram Post</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-story/instagram-story/">Instagram Story</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/linkedin-banner/">LinkedIn Cover</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/pinterest-post/">Pinterest Graphic</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-channel-art/">YouTube Channel Art</a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-thumbnail/">YouTube Thumbnail</a>
                      </div>

                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_marketing.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Marketing</h4></a>
                      <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/flyers/">Flyer</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/posters/">Poster</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/brochures/">Brochure</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/business-card/">Business Card</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/gift-certificate/">Gift Certificate</a>
                    </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_blogging.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Blogging</h4></a>
                     <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/infographics/">Infographic</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/book-cover/e-book/">E-Book</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/blog-image/">Blog Graphic</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/storyboard/">Storyboard</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/">Album Cover</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/email-header/">Email Header</a> </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_Personal.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Personal</h4></a>
                     <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invitations/">Invitation</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/resume/">Resume</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/biodata/">Bio-Data</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/planners/">Planner</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/postcard-templates/">Postcard</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/greeting-cards/">Greeting</a> </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-left popover-image">
                        <a ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_business.png" width="160px" height="111px" alt="" ></a>
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Business</h4></a>
                     <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/logo/">Logo</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invoice-templates/">Invoice</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/letterhead-maker/ ">Letterhead</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/presentation-maker/">Presentation</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/certificate/">Certificate</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/restaurant-menu/">Menu</a>
                    </div>
                    <div class="col-2 m-0 pl-20">
                      <div class="text-right popover-image">
                        <a ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Template_Trending.png" width="160px" height="111px" alt="" class=" text-right">
                      </div>
                      <a  class="remove-style"><h4 class="header-popover-heading">Trending</h4></a>
                     <a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/animated-video-maker/">Animated Video</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-story/whatsapp-status/">Status Video</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-intro-maker/  ">Intro Video</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/outro-video-maker/">Outro Video</a><a class="template-link" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/design/video-flyer-maker/">Video Flyer</a>
                    </div>
                  </div>'> Templates</a></li>
                <li><a id="goFeatures" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-graphic-editor/" name="feature-btn" role="button" data-html="true" data-content='<div class="row m-0 p-8px mt-1 createbtn">
                <div class="col-4 m-0 pl-20">
                  <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-graphic-editor/"><div class="popover-image"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Feature_graphic_editor.png" width="160px" height="111px" alt="" ></div></a>
                  <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-graphic-editor/"  class="remove-style"><h4 class="header-popover-heading">Graphic Editor</h4></a>
                  <p class="header-popover-desc">Designing made easy with PhotoADKing&apos;s online graphic editor.</p>
                </div>
                <div class="col-4 m-0 pl-20">
                  <div class="text-center popover-image">
                    <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-video-maker/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Feature_video_maker.png" width="160px" height="111px" alt="" ></a>
                  </div>
                  <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/online-video-maker/"  class="remove-style"><h4 class="header-popover-heading">Video Maker</h4></a>
                  <p class="header-popover-desc">Easy to use short video maker for your business.</p>
                </div>
                <div class="col-4 m-0 pl-20">
                  <div class="text-right popover-image">
                  <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/free-intro-maker/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Feature_Intro_editor.png" width="160px" height="111px" alt="" class=" text-right" >
                  </div>
                  <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/features/free-intro-maker/" class="remove-style"><h4 class="header-popover-heading">Intro Editor</h4></a>
                  <p class="header-popover-desc">Customize intro templates with ease.</p>
                </div>
              </div>'>Features</a></li>
                <li><a id="learn_lnk" href="https://blog.photoadking.com/" name="learn-btn" role="button" data-html="true" data-content='<div class="row m-0 p-8px mt-1 featurebtn">
              <div class="col m-0 pl-20">
                <a href="https://blog.photoadking.com/"><div class="popover-image"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Learn_getting_started.png" width="160px" height="111px" alt="" ></div></a>
                <a href="https://blog.photoadking.com/" class="remove-style"><h4 class="header-popover-heading">Getting Started</h4></a>
                <p class="header-popover-desc">Get started with your next design ideas.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/ideas-and-inspirations/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Learn_design_inspiration.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/ideas-and-inspirations/"  class="remove-style"><h4 class="header-popover-heading">Design Inspiration</h4></a>
                <p class="header-popover-desc">Get ideas & inspiration for your next design project.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/video-marketing/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Learn_marketing_mania.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/video-marketing/"  class="remove-style"><h4 class="header-popover-heading">Marketing Mania</h4></a>
                <p class="header-popover-desc">Creative marketing ideas to boost your business.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-left popover-image">
                  <a href="https://blog.photoadking.com/how-to-make/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Learn_tutorials.png" width="160px" height="111px" alt="" ></a>
                </div>
                <a href="https://blog.photoadking.com/how-to-make/"  class="remove-style"><h4 class="header-popover-heading">Tutorials</h4></a>
                <p class="header-popover-desc">Step-by-step tutorial for designing from scratch in PhotoADKing.</p>
              </div>
              <div class="col m-0 pl-20">
                <div class="text-right popover-image">
                <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/whats-new/"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Learn_whats_new.png" width="160px" height="111px" alt="" class=" text-right" >
                </div>
                <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/whats-new/"  class="remove-style"><h4 class="header-popover-heading">What&apos;s New</h4></a>
                <p class="header-popover-desc">It&apos;s a time to take advantage of our new updates.</p>
              </div>
            </div>'>Learn</a></li>
                <li><a id="primium_lnk" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" name="pricing-btn" role="button" data-html="true" data-content='<div class="row m-0 p-8px mt-1 pricingbtn">
            <div class="col-6 m-0 pl-20">
              <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/"><div class="popover-image"><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Pricing_free.png" width="160px" height="111px" alt="" ></div></a>
              <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" class="remove-style"><h4 class="header-popover-heading">Free</h4></a>
              <p class="header-popover-desc">Quickly create and download designs for any occasion. Use it as long as you want.</p>
            </div>
            <div class="col-6 m-0 pl-20">
              <div class="text-left popover-image">
                <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" ><img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/resources/Pricing_pro.png" width="160px" height="111px" alt="" ></a>
              </div>
              <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/go-premium/" class="remove-style"><h4 class="header-popover-heading">Pro</h4></a>
              <p class="header-popover-desc">Easily create unlimited branded videos and get unlimited templates and more content.</p>
            </div>

          </div>'>Pricing</a></li>
              </ul>

            </div>

            <div class="float-right l-menu align-items-center">
              <ul class="l-menu-container mr-0">
                <li class="mr-cust-15" id="hd-logn" name="login-btn"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/login" target="_blank">Log In</a></li>
              </ul> <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/sign-up" target="_blank" id="rlp-link"> <button class="l-signup-btn my-1" name="signup-btn" id="rlp-btn-txt"> <span>Sign Up Free</span></button> </a>
            </div>
            <div class="float-right l-mob-menu" id="mob-menu"> <button class="l-transparent-button" name="navbar"> <span>
                  <svg class="l-sm-white" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.75 0.499969H1.25001C0.559624 0.499969 0 1.05964 0 1.74998C0 2.44033 0.559624 3 1.25001 3H18.75C19.4404 3 20 2.44033 20 1.74998C20 1.05964 19.4404 0.499969 18.75 0.499969Z" fill="#151515" />
                    <path d="M18.75 6.74996H1.25001C0.559624 6.74996 0 7.30959 0 7.99993C0 8.69027 0.559624 9.24994 1.25001 9.24994H18.75C19.4404 9.24994 20 8.69031 20 7.99993C20 7.30954 19.4404 6.74996 18.75 6.74996Z" fill="#151515" />
                    <path d="M18.75 13H1.25001C0.559624 13 0 13.5596 0 14.25C0 14.9404 0.559624 15.5 1.25001 15.5H18.75C19.4404 15.5 20 14.9404 20 14.25C20 13.5596 19.4404 13 18.75 13Z" fill="#151515" />
                  </svg>
                </span> <span> <svg class="l-sm-blue" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.75 0.499969H1.25001C0.559624 0.499969 0 1.05964 0 1.74998C0 2.44033 0.559624 3 1.25001 3H18.75C19.4404 3 20 2.44033 20 1.74998C20 1.05964 19.4404 0.499969 18.75 0.499969Z" fill="#151515" />
                    <path d="M18.75 6.74996H1.25001C0.559624 6.74996 0 7.30959 0 7.99993C0 8.69027 0.559624 9.24994 1.25001 9.24994H18.75C19.4404 9.24994 20 8.69031 20 7.99993C20 7.30954 19.4404 6.74996 18.75 6.74996Z" fill="#151515" />
                    <path d="M18.75 13H1.25001C0.559624 13 0 13.5596 0 14.25C0 14.9404 0.559624 15.5 1.25001 15.5H18.75C19.4404 15.5 20 14.9404 20 14.25C20 13.5596 19.4404 13 18.75 13Z" fill="#151515" />
                  </svg> </span> </button></div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 l-min-md-pd p-0">
        <div class="header-content-padding sec-first-content-wrapper c-container-pedding">
          <div class="s-header-content-container sec-first-heading-container">
            <h1 class="f-heading font-72px" spellcheck="false">{!! $main_template['header_detail']->h1 !!}</h1>
            <p class="s-sub-header template-main-text-content margin-0">{!! $main_template['header_detail']->h2 !!}</p>
            <!-- 37 -->
            <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/dashboard" id="hdrnavbtn" target="_blank" style="overflow: auto;" class="sec-temp-btn sec-first-button m-bottom-45 c-bg-text">{!! $main_template['header_detail']->cta_text !!}
              <svg viewBox="0 0 22 20" class="sec-first-button-svg mb-1" xmlns="http://www.w3.org/2000/svg">
                <path fill="#0069FF" d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
            </a>
          </div>
        </div>
      </div>
      @if($is_rating)
        <div class="review-wrapper pt-1 pb-1 c-custom-bg">
          <span class="desc">{!! $main_template['rating_schema']->description !!}</span>
          <br>
          <span class="Author whitespace-nowrap d-inline-block" style="overflow: auto;">
          {!! $main_template['rating_schema']->userName !!}
        </span>
          <span class="Author whitespace-nowrap d-inline-block" style="overflow: auto;">
          Rating: 5 / {!! $main_template['rating_schema']->ratingValue  !!}
        </span>
          <span class="d-inline-block" style="position: relative; top: -6px;">
         @if($main_template['rating_schema']->ratingValue >= 3 && $main_template['rating_schema']->ratingValue <= 4)
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d_590_97741)">
          <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
        </g>
        <defs>
          <filter id="filter0_d_590_97741" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
            <feOffset />
            <feGaussianBlur stdDeviation="2" />
            <feComposite in2="hardAlpha" operator="out" />
            <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
          </filter>
        </defs>
        </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_d_590_97742)">
            <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
          </g>
          <defs>
            <filter id="filter0_d_590_97742" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix" />
              <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
              <feOffset />
              <feGaussianBlur stdDeviation="2" />
              <feComposite in2="hardAlpha" operator="out" />
              <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
              <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
            </filter>
          </defs>
        </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_d_590_97743)">
            <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
          </g>
          <defs>
            <filter id="filter0_d_590_97743" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix" />
              <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
              <feOffset />
              <feGaussianBlur stdDeviation="2" />
              <feComposite in2="hardAlpha" operator="out" />
              <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
              <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
            </filter>
          </defs>
        </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_d_590_97744)">
            <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
          </g>
          <defs>
            <filter id="filter0_d_590_97744" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix" />
              <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
              <feOffset />
              <feGaussianBlur stdDeviation="2" />
              <feComposite in2="hardAlpha" operator="out" />
              <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
              <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
            </filter>
          </defs>
        </svg>
            @elseif($main_template['rating_schema']->ratingValue > 4 && $main_template['rating_schema']->ratingValue <= 5)
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_d_590_97745)">
            <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
          </g>
          <defs>
            <filter id="filter0_d_590_97745" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix" />
              <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
              <feOffset />
              <feGaussianBlur stdDeviation="2" />
              <feComposite in2="hardAlpha" operator="out" />
              <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
              <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
            </filter>
          </defs>
          </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_590_97746)">
              <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
            </g>
            <defs>
              <filter id="filter0_d_590_97746" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                <feOffset />
                <feGaussianBlur stdDeviation="2" />
                <feComposite in2="hardAlpha" operator="out" />
                <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
              </filter>
            </defs>
          </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_590_97747)">
              <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
            </g>
            <defs>
              <filter id="filter0_d_590_97747" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                <feOffset />
                <feGaussianBlur stdDeviation="2" />
                <feComposite in2="hardAlpha" operator="out" />
                <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
              </filter>
            </defs>
          </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_590_97748)">
              <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
            </g>
            <defs>
              <filter id="filter0_d_590_97748" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                <feOffset />
                <feGaussianBlur stdDeviation="2" />
                <feComposite in2="hardAlpha" operator="out" />
                <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
              </filter>
            </defs>
          </svg>
              <svg width="21" height="21" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_590_97749)">
              <path d="M11.5489 4.92705C11.8483 4.00574 13.1517 4.00574 13.4511 4.92705L14.6329 8.56434C14.7668 8.97636 15.1507 9.25532 15.5839 9.25532H19.4084C20.3771 9.25532 20.7799 10.4949 19.9962 11.0643L16.9021 13.3123C16.5516 13.5669 16.405 14.0183 16.5389 14.4303L17.7207 18.0676C18.02 18.9889 16.9656 19.7551 16.1818 19.1857L13.0878 16.9377C12.7373 16.6831 12.2627 16.6831 11.9122 16.9377L8.81815 19.1857C8.03444 19.7551 6.97996 18.9889 7.27931 18.0676L8.46114 14.4303C8.59501 14.0183 8.44835 13.5669 8.09787 13.3123L5.00381 11.0643C4.22009 10.4949 4.62287 9.25532 5.59159 9.25532H9.41606C9.84929 9.25532 10.2332 8.97636 10.3671 8.56434L11.5489 4.92705Z" fill="#FFD923" />
            </g>
            <defs>
              <filter id="filter0_d_590_97749" x="0.589722" y="0.236328" width="23.8206" height="23.1445" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                <feOffset />
                <feGaussianBlur stdDeviation="2" />
                <feComposite in2="hardAlpha" operator="out" />
                <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.5 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_590_9774" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_590_9774" result="shape" />
              </filter>
            </defs>
          </svg>
            @endif

        </span>


        </div>
      @endif
    </div>
    </div>
  <div class="modal fade-scale" id="my_modal" class="display-video-modal">
        <div class="modal-dialog edit-video-dialog modal-dialog-centered">
          <div class="modal-content">

            <!-- Modal body -->
            <div class="modal-body p-3 edit-video-modal-body">
              <div class="tmp_edit_vdo_cls_btn">
                <svg id="Layer_1" width="30" height="30" fill="#484b6e" onclick="closeDialog()" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M481.18,236.18,376.38,341l104.8,104.79-35.36,35.36L341,376.33,236.23,481.13l-35.36-35.36L305.67,341l-104.8-104.8,35.36-35.36L341,305.63l104.8-104.81ZM661,341A320,320,0,0,1,114.73,567.27,320,320,0,1,1,567.27,114.73,317.89,317.89,0,0,1,661,341Zm-50,0C611,192.13,489.88,71,341,71S71,192.13,71,341,192.13,611,341,611,611,489.88,611,341Z" transform="translate(-21 -21)"/></svg>
              </div>
              <div class="text-center">
                <p class=" mb-2 edit-video-quote">IMAGINE IT.
                  CREATE IT.</p>
                <div class="mx-auto ev-heading-wrapper">
                  <hr class="my-0 edit-video-hr">
                  <p class="mb-0 px-2 edit-video-heading">Welcome to
                    PhotoADKing </p>
                  <hr class="mb-2 mt-0 edit-video-hr">
                </div>

                <p class="mb-0 edit-video-sub-heading">Thousands of
                  ready-to-use video
                  templates at your fingertips</p>
              </div>

              <div class="text-center p-3 ">
                <div class="text-center mx-auto px-3 pt-3 pb-4 ev-preview-video-wrapper">
                  <div class="position-relative">
                    <div id="video-modal-container"> </div>
                    <!-- <div class="seekbar-container" id="seekbar-mdl" > -->
                    <!-- <div class="custom-seekbar edit-video-seekbar" id="custom-seekbar-mdl" style="bottom: 0px;"><span
                        id="cs-mdl"></span>
                    </div> -->
                    <!-- <p id="start_time" class="float-left seekbar-time">00.00</p>
                    <p id="end_time" class="float-right seekbar-time">00.00</p> -->

                    <!-- </div> -->
                  </div>
                </div>
              </div>
              <div class="text-center">
              <a href="https://photoadking.com/app/#/editor" target="_blank" id="dialog-edit-btn" class="my-1 px-4"><button class="c-cta-btn">
              <span>Edit Video Now</span>
              <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
            </button></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="templateTop" style="padding-top: 100px;margin-top:-100px;"></div>
  <div class="col-12 col-lg-10 col-md-11 col-sm-11 m-auto collection-container collection-container-1 pt-0" style="width:100%">
    <div class="item-container">

      {!! $main_template['left_nav'] !!}

      <div class="video-template-container" style="padding:0 20px;height:auto">
        <!-- <div class="card-columns template-wrapper w-100 mb-0" id="card-item" style="min-height:1000px" > -->


          <div class="row gy-4 masonry masnory-grid" id="card-item" style="column-gap:0px;min-height:1000px;margin-bottom: 50px;">
          <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center card-item card-ol-block">
          </div>

  </div>
 <!-- <div style="position:relative;margin-top: 55px;" class="template_loader" hidden>
 <div class="load-icon center">
	<span></span>
	<span></span>
	<span></span>
</div>
</div> -->
<div class="pb-2 w-100 template_loader" style="margin-top: 55px;" hidden><div class="temp-loader"><div></div><div></div><div></div><div></div><div></div></div></div>
        <!-- </div> -->
        <div class="template-wrapper-1 w-100 visibility-hidden py-0 my-0" id="temparary_wrapper" style="padding-bottom: 50px !important;">
          <div class="col col-xs-12  text-center temparary-item card-ol-block mx-auto">
          </div>
        </div>
  </div>
  </div>
        <div class="btn-pegin-container w-100 mt-5 mb-5 deskNextcontainer" style="display:none;">
          <!-- <a href="https://photoadking.com/app/#/sign-up"> -->
          <!-- <div class="w-100 text-center">
             <button class="prev-cta-btn" style="display:none;" onclick="changePage(false,'')" id="deskPrevBtn">
              <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff"
                  d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
              <svg class="btn-loader-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
<g transform="rotate(0 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(30 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(60 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(90 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(120 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(150 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(180 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(210 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(240 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(270 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(300 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(330 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
  </rect>
</g></svg>
              <span>Load more..</span>
            </button>
          </div>  -->
          <div class="w-100 text-center">
            <button class="c-cta-btn"  style="display:inline-block;" id="deskNextBtn" onclick="loadMore(false,'deskNextBtn')">
              <span>Load More..</span>
              <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff"
                  d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
              <svg class="btn-loader-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
<g transform="rotate(0 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(30 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(60 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(90 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(120 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(150 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(180 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(210 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(240 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(270 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(300 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(330 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
  </rect>
</g>
  </svg>
            </button>
          </div>
          <!-- </a> -->
        <!-- </div> -->
        <div class="btn-pegin-container pegin-btn-tab w-100 mt-5 mb-5" style="display:block;">
          <!-- <a href="https://photoadking.com/app/#/sign-up"> -->
          <!-- <button class="prev-cta-btn" id="tabprevBtn" onclick="changePage(true,'tabprevBtn')">
          <svg class="btn-loader-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
<g transform="rotate(0 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(30 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(60 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(90 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(120 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(150 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(180 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(210 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(240 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(270 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(300 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(330 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#0069ff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
  </rect>
</g></svg>
            <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
              <path fill="#ffffff"
                d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
              </path>
            </svg>
          </button> -->
          <!-- <button class="c-cta-btn" id="tabnextBtn" onclick="changePage(false,'tabnextBtn')">
            <span>Next Page</span>
            <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
              <path fill="#ffffff"
                d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
              </path>
            </svg>
            <svg class="btn-loader-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
<g transform="rotate(0 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
  </rect>`
</g><g transform="rotate(30 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(60 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(90 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(120 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(150 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(180 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(210 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(240 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(270 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(300 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
  </rect>
</g><g transform="rotate(330 50 50)">
  <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#ffffff">
    <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
  </rect>
</g>
  </svg>
          </button>
          </a> -->
        </div>
      </div>
  </div>
    <!-- </div>
  </div> -->
  <!-- Popular categories code  -->
{{--  @php
    $sub_pages = $main_template['sub_pages'];
  @endphp
  <div style="background-color: #0069FF;">
    @if(count($sub_pages) > 0)
      <div
              class="col-12 col-xl-10 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container popular-category-container pb-0 c-background">
        <h2 class="heading text-center py-4 text-white font-weight-normal Montserrat-SemiBold">Popular Categories</h2>
        <div class="pb-5">
          <div id="popular_tag_container" class="tag-container">
            @foreach($sub_pages as $page)
              <a class="tag-style text-black" target="_blank" href="{{ $page->page_url }}">{{ ucfirst($page->tag) }}</a>
            @endforeach
          </div>
          <div id="btn-wrapper" class="text-center pb-2">
            <button id="explore_more" type="button" class="button-style"><span class="ml-2 montserrat-regular">Explore More</span>
            <svg  viewBox="0 0 22 20" fill="none" class="sec-first-button-svg mr-2"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                  fill="#ffffff" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    @endif
  </div>--}}
<!-- how to section code started  -->
  @if(count($guide_steps) > 0)
    <div class="row no-gutters how-to-sec-tmp-bg pb-3">
      <div class="col-12 col-lg-9 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container pb-0">
        <h2 class="heading my-4 pb-1 sec-three-heading new-sec-three-heading  sec-three-special">{{ isset($main_template['guide_detail']->guide_heading) && $main_template['guide_detail']->guide_heading !=""  ? $main_template['guide_detail']->guide_heading : ""}}
        </h2>
        <!-- below is two column list and display in large screen -->

        <div class="col-12 justify-content-center d-flex mx-0 pb-5" style="flex-wrap: wrap;">
          @foreach($guide_steps as $i => $step)
            <div class="w-33p how-to-step-wrapper m-3">
              <div class="">
                <div class="position-relative d-flex justify-content-between">
                  <div class="how-to-step-title step-title">{{ $step['heading'] }}</div>
                  <div class="how-to-step-number">0{{$i+1}}</div>
                </div>
                <p class="how-to-step-content step-content">{{ $step['description'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
        <!-- below list is one column list and display in smalll devices -->

        <div class="see-more-btn-wrapper sec-five-btn-wrapper">
          <a href="https://photoadking.com/app/#/sign-up" target="_blank" class="m-auto  see-more-btn d-inline-block sec-four-button">
            <span class="sec-four-btn-txt">{!! $main_template['guide_detail']->guide_btn_text !!}</span>
            <svg  viewBox="0 0 22 20" fill="none" class="sec-first-button-svg"
                  xmlns="http://www.w3.org/2000/svg">
              <path
                d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z"
                fill="#ffffff" />
            </svg>
          </a>
        </div>
      </div>
    </div>
@endif
<!-- how to section code ended  -->

   <!--Testimonial code started -->

   <div class="l-last-slider-container pt-0 m-0 pb-lg-5 pb-md-0 bg-white" >

    <div class="row col-12 col-lg-9 col-md-10 col-sm-12 m-auto mt-4 ">
    <h2 class="sec-three-special mx-auto new-sec-three-heading py-2 pb-4 pt-5">What Users Says About PhotoADKing</h2>
    <p class="l-last-slider-subtitle pb-4">From business owner to marketer, from designers to developers, PhotoADKing is
      trusted and recommended by professionals around the world.</p>
      <div class="col-lg-4 col-md-12 l-last-slider-hide my-3">
        <div class="feedback-main-wrapper h-100">
          <div class="feedback-sub-wrapper h-100">
            <div class="position-relative text-center pt-5 h-100">
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M25.6272 5.13133L22.3183 0C9.09018 5.7802 0 17.4748 0 30.5269C0 37.9479 2.05047 42.5273 5.86272 46.5996C8.2611 49.1727 12.0586 51 16.0337 51C23.1844 51 28.9879 45.1527 28.9879 37.9479C28.9879 31.0863 23.7248 25.5597 17.0478 24.9332C15.8486 24.8213 14.642 24.8511 13.4946 25.0003C13.4946 22.6733 13.2799 11.8662 25.6272 5.13133Z"
                  fill="#1980FF" />
                <path
                  d="M62.6393 5.13133L59.3304 0C46.1023 5.7802 37.0121 17.4748 37.0121 30.5269C37.0121 37.9479 39.0626 42.5273 42.8748 46.5996C45.2732 49.1727 49.0707 51 53.0458 51C60.1965 51 66 45.1527 66 37.9479C66 31.0863 60.7369 25.5597 54.0599 24.9332C52.8607 24.8213 51.6541 24.8511 50.5067 25.0003C50.5067 22.6733 50.2921 11.8662 62.6393 5.13133Z"
                  fill="#1980FF" />
              </svg>

              <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Emmanuel.webp" class="fdb-usr-img" loading="lazy" width="85px" height="85px" onerror="this.src='{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Emmanuel.jpeg'" alt="user's photo">
              <p class="fdb-usr-name montserrat-medium">Emmanuel R.</p>
              <p class="fdb-usr-occupation montserrat-medium">Student</p>
              <p class="fdb-txt montserrat-medium">
              This App has helped me to create amazing and beautiful designs for my online business and colleagues as well which has also given job opportunities as well as it's designs are quite distinctive and eye catchy.
              </p>
              <div class="d-flex justify-content-center">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
              </div>
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M40.3728 45.8687L43.6817 51C56.9098 45.2198 66 33.5252 66 20.4731C66 13.0521 63.9495 8.47265 60.1373 4.40041C57.7389 1.82729 53.9414 0 49.9663 0C42.8156 0 37.0121 5.84732 37.0121 13.0521C37.0121 19.9137 42.2752 25.4403 48.9522 26.0668C50.1514 26.1787 51.358 26.1489 52.5054 25.9997C52.5054 28.3267 52.7201 39.1338 40.3728 45.8687Z"
                  fill="#1980FF" />
                <path
                  d="M3.3607 45.8687L6.66958 51C19.8977 45.2198 28.9879 33.5252 28.9879 20.4731C28.9879 13.0521 26.9374 8.47265 23.1252 4.40041C20.7268 1.82729 16.9293 0 12.9542 0C5.8035 0 0 5.84732 0 13.0521C0 19.9137 5.26312 25.4403 11.9401 26.0668C13.1393 26.1787 14.3459 26.1489 15.4933 25.9997C15.4933 28.3267 15.7079 39.1338 3.3607 45.8687Z"
                  fill="#1980FF" />
              </svg>

            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-12 l-last-slider-hide my-3">
        <div class="feedback-main-wrapper h-100">
          <div class="feedback-sub-wrapper h-100">
            <div class="position-relative text-center pt-5 h-100">
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M25.6272 5.13133L22.3183 0C9.09018 5.7802 0 17.4748 0 30.5269C0 37.9479 2.05047 42.5273 5.86272 46.5996C8.2611 49.1727 12.0586 51 16.0337 51C23.1844 51 28.9879 45.1527 28.9879 37.9479C28.9879 31.0863 23.7248 25.5597 17.0478 24.9332C15.8486 24.8213 14.642 24.8511 13.4946 25.0003C13.4946 22.6733 13.2799 11.8662 25.6272 5.13133Z"
                  fill="#1980FF" />
                <path
                  d="M62.6393 5.13133L59.3304 0C46.1023 5.7802 37.0121 17.4748 37.0121 30.5269C37.0121 37.9479 39.0626 42.5273 42.8748 46.5996C45.2732 49.1727 49.0707 51 53.0458 51C60.1965 51 66 45.1527 66 37.9479C66 31.0863 60.7369 25.5597 54.0599 24.9332C52.8607 24.8213 51.6541 24.8511 50.5067 25.0003C50.5067 22.6733 50.2921 11.8662 62.6393 5.13133Z"
                  fill="#1980FF" />
              </svg>

              <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/akshay.webp" class="fdb-usr-img" loading="lazy" width="85px" height="85px" onerror="this.src='{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/akshay.png'" alt="user's photo">
              <p class="fdb-usr-name montserrat-medium">Akshay Purabiya</p>
              <p class="fdb-usr-occupation montserrat-medium">Business Owner</p>
              <p class="fdb-txt montserrat-medium">
              PhotoADKing is great. It permits you to be so inventive and gives you the instruments to do as such. I totally love it particularly for planning huge Posters for promotion.
              </p>
              <div class="d-flex justify-content-center">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
              </div>
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M40.3728 45.8687L43.6817 51C56.9098 45.2198 66 33.5252 66 20.4731C66 13.0521 63.9495 8.47265 60.1373 4.40041C57.7389 1.82729 53.9414 0 49.9663 0C42.8156 0 37.0121 5.84732 37.0121 13.0521C37.0121 19.9137 42.2752 25.4403 48.9522 26.0668C50.1514 26.1787 51.358 26.1489 52.5054 25.9997C52.5054 28.3267 52.7201 39.1338 40.3728 45.8687Z"
                  fill="#1980FF" />
                <path
                  d="M3.3607 45.8687L6.66958 51C19.8977 45.2198 28.9879 33.5252 28.9879 20.4731C28.9879 13.0521 26.9374 8.47265 23.1252 4.40041C20.7268 1.82729 16.9293 0 12.9542 0C5.8035 0 0 5.84732 0 13.0521C0 19.9137 5.26312 25.4403 11.9401 26.0668C13.1393 26.1787 14.3459 26.1489 15.4933 25.9997C15.4933 28.3267 15.7079 39.1338 3.3607 45.8687Z"
                  fill="#1980FF" />
              </svg>

            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-12 l-last-slider-hide my-3">
        <div class="feedback-main-wrapper h-100">
          <div class="feedback-sub-wrapper h-100">
            <div class="position-relative text-center pt-5 h-100">
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M25.6272 5.13133L22.3183 0C9.09018 5.7802 0 17.4748 0 30.5269C0 37.9479 2.05047 42.5273 5.86272 46.5996C8.2611 49.1727 12.0586 51 16.0337 51C23.1844 51 28.9879 45.1527 28.9879 37.9479C28.9879 31.0863 23.7248 25.5597 17.0478 24.9332C15.8486 24.8213 14.642 24.8511 13.4946 25.0003C13.4946 22.6733 13.2799 11.8662 25.6272 5.13133Z"
                  fill="#1980FF" />
                <path
                  d="M62.6393 5.13133L59.3304 0C46.1023 5.7802 37.0121 17.4748 37.0121 30.5269C37.0121 37.9479 39.0626 42.5273 42.8748 46.5996C45.2732 49.1727 49.0707 51 53.0458 51C60.1965 51 66 45.1527 66 37.9479C66 31.0863 60.7369 25.5597 54.0599 24.9332C52.8607 24.8213 51.6541 24.8511 50.5067 25.0003C50.5067 22.6733 50.2921 11.8662 62.6393 5.13133Z"
                  fill="#1980FF" />
              </svg>

              <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/john_w.webp" class="fdb-usr-img" loading="lazy" width="85px" height="85px" onerror="this.src='{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/john_w.jpeg'" alt="user's photo">
              <p class="fdb-usr-name montserrat-medium">John W.</p>
              <p class="fdb-usr-occupation montserrat-medium">Content Writer</p>
              <p class="fdb-txt montserrat-medium">
              PhotoADKing is ideal for creating posters, presentations, graphics, documents, and visual content as there are many free templates available to use, and the templates are excellent. There are beautiful illustrations that we can use for free as well.
              </p>
              <div class="d-flex justify-content-center">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
                <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/full_star.svg" width="15px" height="15px" class="w-20px m-0" alt="rating star">
              </div>
              <svg width="66" height="51" viewBox="0 0 66 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M40.3728 45.8687L43.6817 51C56.9098 45.2198 66 33.5252 66 20.4731C66 13.0521 63.9495 8.47265 60.1373 4.40041C57.7389 1.82729 53.9414 0 49.9663 0C42.8156 0 37.0121 5.84732 37.0121 13.0521C37.0121 19.9137 42.2752 25.4403 48.9522 26.0668C50.1514 26.1787 51.358 26.1489 52.5054 25.9997C52.5054 28.3267 52.7201 39.1338 40.3728 45.8687Z"
                  fill="#1980FF" />
                <path
                  d="M3.3607 45.8687L6.66958 51C19.8977 45.2198 28.9879 33.5252 28.9879 20.4731C28.9879 13.0521 26.9374 8.47265 23.1252 4.40041C20.7268 1.82729 16.9293 0 12.9542 0C5.8035 0 0 5.84732 0 13.0521C0 19.9137 5.26312 25.4403 11.9401 26.0668C13.1393 26.1787 14.3459 26.1489 15.4933 25.9997C15.4933 28.3267 15.7079 39.1338 3.3607 45.8687Z"
                  fill="#1980FF" />
              </svg>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Testimonial section code ended  -->
  @if(!empty($main_template['sub_detail']))
  <div class="row no-gutters how-to-sec-bg c-grey-color">
    <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 collection-container">
      <h2 class="heading mb-4 mt-4 sec-three-heading new-sec-three-heading sec-three-special text-center">{!! $main_template['sub_detail']->main_h2 !!}</h2>
      <p class="sub-heading mb-4 sec-three-content c-black-text text-center">{!! $main_template['sub_detail']->main_description !!}</p>
            <div class="text-center mb-4 mt-5">
          <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/sign-up" target="_blank" class="Click to sign-up and get started" id="try_now_btn" title="Click to try now!">
            <button class="c-cta-btn">
              <span id="tryitnow">Try It Now - It's Free</span>
              <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
            </button>
          </a>
        </div>
    </div>
  </div>
  @endif
   <!-- FAQ section code started  -->
  @if(!empty($main_template['faqs']))
    @if(count($main_template['faqs']) > 0 )
      <div class="row no-gutters sec-four-wrapper bg-blue bg-white">
        <div
          class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 pt-0 collection-container sec-faq-wrapper bg-blue bg-white">
          <h2 class="heading my-5 sec-three-heading new-sec-three-heading  sec-three-special">Frequently Asked
          Questions</h2>
          <div class="s-icons-wrapper faq-wrapper">
            <div class="panel-group ul-faq-list sec-accordian justify-content-center" id="accordion">
              <div class="accordian-col" style="width: 75%;">
                @foreach($main_template['faqs'] as $key => $faq)
                  @if(($key + 1) % 2 != 0)
                    <div class="panel panel-default li-box-shadow p-0 sec-faq-item">
                      <div class="panel-heading p-0">
                        <li class="ul-faq-list-li bg-light-blue">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collpse{!! $key !!}" rel="noreferrer"
                             class="faq-question-title">
                            <div>
                              <h4 class="date-heading montserrat-medium c-black-text">
                                {!! $faq['question'] !!}
                              </h4>
                            </div>
                            <svg class="ul-faq-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0"
                                 viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                              <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                 stroke="none">
                                <path
                                  d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z"></path>
                              </g>
                            </svg>
                          </a>
                        </li>
                      </div>
                      <div id="collpse{!! $key !!}" class="panel-collapse collapse in p-0">
                        <div class="panel-body panel-body-desc faq-content-wrapper">
                          <p class="m-0 lato-light">{!! $faq['answer'] !!}</p>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
              <div class="accordian-col" style="width: 75%;">
                @foreach($main_template['faqs'] as $key => $faq)
                  @if(($key + 1) % 2 == 0)
                    <div class="panel panel-default li-box-shadow p-0 sec-faq-item">
                      <div class="panel-heading p-0">
                        <li class="ul-faq-list-li bg-light-blue">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collpse{!! $key !!}" rel="noreferrer"
                             class="faq-question-title">
                            <div>
                              <h4 class="date-heading montserrat-medium c-black-text">{!! $faq['question'] !!}</h4>
                            </div>
                            <svg class="ul-faq-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0"
                                 viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                              <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c"
                                 stroke="none">
                                <path
                                  d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z"></path>
                              </g>
                            </svg>
                          </a>
                        </li>
                      </div>
                      <div id="collpse{!! $key !!}" class="panel-collapse collapse in p-0">
                        <div class="panel-body panel-body-desc faq-content-wrapper">
                          <p class="mb-0 lato-light">{!! $faq['answer'] !!}</p>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
  @endif
@endif
  <!-- FAQ section code ended  -->
  <!-- description section code ended  -->
  <div class="row no-gutters how-to-sec-bg c-margin-bottom">
    <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 collection-container">
      <h2 class="heading mb-4 mt-4 sec-three-heading new-sec-three-heading sec-three-special text-center">{!! $main_template['sub_detail']->h2 !!}</h2>
      <p class="sub-heading mb-4 sec-three-content c-black-text">{!! $main_template['sub_detail']->description !!}</p>
    </div>
  </div>
  <!-- description section code ended  -->
  <div id="hm_footer_section">
      <div class="pb-3 pt-3 position-relative l-footer-svg c-footer-svg text-center">
        <div class="l-footer-icon">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="400" height="400" viewBox="0 0 400 400" class="svg-h-w-50px">
            <defs xmlns="http://www.w3.org/2000/svg">
              <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" class="svg-grad-stop-1"></stop>
                <stop offset="100%" class="svg-grad-stop-2"></stop>
              </linearGradient>
            </defs>
            <g id="svgg">
              <path id="path0" d="M191.761 99.569 C 186.168 101.358,181.934 106.373,181.366 111.880 L 181.148 113.996 176.929 114.127 C 169.179 114.367,168.986 114.755,168.817 130.469 C 168.652 145.822,168.761 146.889,170.712 148.981 L 172.210 150.586 196.255 150.586 L 220.299 150.586 221.771 149.115 L 223.242 147.644 223.353 132.884 L 223.464 118.124 222.564 116.777 C 221.145 114.653,219.543 114.064,215.181 114.063 L 211.417 114.063 211.141 112.222 C 209.729 102.804,200.494 96.775,191.761 99.569 M199.883 108.448 C 201.443 109.612,202.336 110.980,202.600 112.607 L 202.837 114.063 196.145 114.063 C 188.586 114.063,188.771 114.157,190.268 111.063 C 191.915 107.661,196.983 106.283,199.883 108.448 M134.375 129.395 C 132.744 130.374,132.011 131.143,131.169 132.759 L 130.078 134.855 130.078 213.143 C 130.078 282.552,130.146 291.594,130.676 292.863 C 131.674 295.250,133.203 296.911,135.306 297.890 L 137.305 298.820 195.853 298.824 C 238.602 298.827,254.803 298.708,255.890 298.382 C 257.870 297.789,259.981 295.997,261.046 294.005 L 261.914 292.383 262.021 263.672 C 262.112 239.164,262.048 235.075,261.588 235.742 C 236.684 271.827,233.506 276.160,231.078 277.328 C 228.688 278.478,215.357 282.031,213.432 282.031 C 206.444 282.031,203.097 277.553,202.157 266.943 C 201.290 257.165,201.347 255.616,202.669 252.994 L 203.776 250.799 180.628 250.693 C 157.941 250.588,157.457 250.570,156.400 249.781 C 152.427 246.814,152.528 241.530,156.610 238.779 L 157.928 237.891 185.507 237.886 L 213.086 237.881 219.016 229.301 L 224.946 220.720 191.477 220.614 L 158.008 220.508 156.626 219.648 C 152.274 216.942,152.602 210.249,157.188 208.166 C 158.713 207.473,160.565 207.439,196.374 207.430 L 233.960 207.422 235.460 205.371 C 236.285 204.243,242.626 195.098,249.551 185.048 L 262.142 166.775 262.028 150.477 L 261.914 134.180 260.818 132.315 C 260.216 131.290,258.998 129.972,258.113 129.386 L 256.504 128.320 244.291 128.207 L 232.078 128.094 231.957 138.559 L 231.836 149.023 230.759 151.216 C 229.474 153.832,226.159 157.093,223.551 158.308 L 221.680 159.180 196.289 159.180 L 170.898 159.180 168.673 158.137 C 162.021 155.018,160.777 151.995,160.621 138.574 L 160.500 128.125 148.495 128.125 L 136.489 128.125 134.375 129.395 M299.144 128.881 C 298.152 129.662,222.528 239.063,222.375 239.939 C 222.292 240.418,238.707 252.148,239.461 252.148 C 239.778 252.148,308.487 152.950,314.510 143.798 C 317.785 138.820,317.492 138.233,308.640 132.049 C 302.511 127.767,301.141 127.310,299.144 128.881 M235.415 177.824 C 240.168 180.425,239.852 187.630,234.894 189.702 C 232.782 190.584,159.037 190.481,157.166 189.593 C 151.793 187.044,152.629 178.702,158.388 177.386 C 161.637 176.644,234.010 177.056,235.415 177.824 M212.549 253.392 C 209.805 257.366,209.846 257.080,210.692 266.290 C 211.464 274.690,210.646 274.462,225.013 270.290 C 227.401 269.596,232.452 264.095,232.416 262.228 C 232.409 261.904,216.447 250.532,215.475 250.158 C 215.114 250.020,214.119 251.120,212.549 253.392 " stroke="none" fill="#fbfbfc" fill-rule="evenodd"></path>
              <path id="path1" d="M183.789 1.230 C 136.936 4.935,92.447 25.523,58.985 58.985 C -18.823 136.793,-18.823 263.207,58.985 341.015 C 136.788 418.818,263.212 418.818,341.015 341.015 C 372.019 310.011,391.990 269.633,397.633 226.546 C 414.190 100.116,310.807 -8.814,183.789 1.230 M199.908 99.385 C 205.749 100.940,210.214 106.043,211.141 112.222 L 211.417 114.063 215.181 114.063 C 219.543 114.064,221.145 114.653,222.564 116.777 L 223.464 118.124 223.353 132.884 L 223.242 147.644 221.771 149.115 L 220.299 150.586 196.255 150.586 L 172.210 150.586 170.712 148.981 C 168.761 146.889,168.652 145.822,168.817 130.469 C 168.986 114.755,169.179 114.367,176.929 114.127 L 181.148 113.996 181.366 111.880 C 182.258 103.234,191.350 97.107,199.908 99.385 M192.710 108.381 C 191.668 109.020,190.790 109.984,190.268 111.063 C 188.771 114.157,188.586 114.063,196.145 114.063 L 202.837 114.063 202.600 112.607 C 201.896 108.267,196.591 106.000,192.710 108.381 M160.621 138.574 C 160.777 151.995,162.021 155.018,168.673 158.137 L 170.898 159.180 196.289 159.180 L 221.680 159.180 223.551 158.308 C 226.159 157.093,229.474 153.832,230.759 151.216 L 231.836 149.023 231.957 138.559 L 232.078 128.094 244.291 128.207 L 256.504 128.320 258.113 129.386 C 258.998 129.972,260.216 131.290,260.818 132.315 L 261.914 134.180 262.028 150.477 L 262.142 166.775 249.551 185.048 C 242.626 195.098,236.285 204.243,235.460 205.371 L 233.960 207.422 196.374 207.430 C 160.565 207.439,158.713 207.473,157.188 208.166 C 152.602 210.249,152.274 216.942,156.626 219.648 L 158.008 220.508 191.477 220.614 L 224.946 220.720 219.016 229.301 L 213.086 237.881 185.507 237.886 L 157.928 237.891 156.610 238.779 C 152.528 241.530,152.427 246.814,156.400 249.781 C 157.457 250.570,157.941 250.588,180.628 250.693 L 203.776 250.799 202.669 252.994 C 201.347 255.616,201.290 257.165,202.157 266.943 C 203.097 277.553,206.444 282.031,213.432 282.031 C 215.357 282.031,228.688 278.478,231.078 277.328 C 233.506 276.160,236.684 271.827,261.588 235.742 C 262.048 235.075,262.112 239.164,262.021 263.672 L 261.914 292.383 261.046 294.005 C 259.981 295.997,257.870 297.789,255.890 298.382 C 254.803 298.708,238.602 298.827,195.853 298.824 L 137.305 298.820 135.306 297.890 C 133.203 296.911,131.674 295.250,130.676 292.863 C 130.146 291.594,130.078 282.552,130.078 213.143 L 130.078 134.855 131.169 132.759 C 132.011 131.143,132.744 130.374,134.375 129.395 L 136.489 128.125 148.495 128.125 L 160.500 128.125 160.621 138.574 M308.640 132.049 C 317.492 138.233,317.785 138.820,314.510 143.798 C 308.487 152.950,239.778 252.148,239.461 252.148 C 238.707 252.148,222.292 240.418,222.375 239.939 C 222.528 239.063,298.152 129.662,299.144 128.881 C 301.141 127.310,302.511 127.767,308.640 132.049 M158.388 177.386 C 152.629 178.702,151.793 187.044,157.166 189.593 C 159.037 190.481,232.782 190.584,234.894 189.702 C 239.852 187.630,240.168 180.425,235.415 177.824 C 234.010 177.056,161.637 176.644,158.388 177.386 M224.215 256.141 C 228.722 259.316,232.412 262.055,232.416 262.228 C 232.452 264.095,227.401 269.596,225.013 270.290 C 213.566 273.614,212.780 273.740,211.691 272.427 C 211.209 271.846,210.188 262.653,210.177 258.789 C 210.170 256.543,214.353 249.728,215.475 250.158 C 215.775 250.274,219.709 252.966,224.215 256.141 " stroke="none" fill="#0069FF" fill-rule="evenodd"></path>
            </g>
          </svg>
        </div>
        <p class="c-section-inner-heading mt-5 mb-2 custom-font">Get Started For Free</p>
        <p class="c-section-inner-text">Easily customize any design and give your images a fresh new look at any
          moment! NO
          design skills or technical knowledge required.</p>
        <div class="text-center mb-4">
          <a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/sign-up" target="_blank" class="Click to sign-up and get started" id="try_now_btn_1" title="Click to try now!">
            <button class="c-cta-btn">
              <span >Try It Now - It's Free</span>
              <svg viewBox="0 0 22 20" class="sec-first-button-svg" xmlns="http://www.w3.org/2000/svg">
                <path fill="#ffffff" d="M1.54545 11.5457H16.7235L11.6344 16.6348C11.0309 17.2382 11.0309 18.2168 11.6344 18.8204C11.9362 19.122 12.3317 19.273 12.7273 19.273C13.1228 19.273 13.5183 19.122 13.82 18.8203L21.5473 11.093C22.1508 10.4895 22.1508 9.51095 21.5473 8.9074L13.82 1.18013C13.2166 0.576677 12.238 0.576677 11.6344 1.18013C11.0309 1.78357 11.0309 2.76216 11.6344 3.36571L16.7235 8.45479H1.54545C0.691951 8.45479 0 9.14674 0 10.0002C0 10.8537 0.691951 11.5457 1.54545 11.5457Z">
                </path>
              </svg>
            </button>
          </a>
        </div>
      </div>
      <div class="misc-content">
        <h2>Flyers Posters Advertisements Invitation Announcement Video Maker, Poster Maker, Flyer
          Designer, Ad Creator online</h2>
        <p>Free Online Card Maker With Stunning Designs by PhotoADKing</p>
        <p>Flyers, Business Cards, Logo Maker, Resume Templates, Cover Letter</p>
        <p>Create Facebook Posts, Cover Letter, Instagram Story, Youtube Covers, Flyers, Business Cards, Logo Design,
          Brochures, Invitation Cards, Advertisement, Banners, Resume, Announcements, Posters, Presentations,
          Certificates, Facebook Ads, Google Ads Using Free Templates.</p>
        <p>Create posters and flyers online for your business within seconds using beautiful and professional design
          templates.
          Digital Marketing, Branding, Marketing Experts, Social media marketing, designing, cover photos for your shop
          restaurant office or social sites Advertisement banner maker Typography Artwork online.</p>
        <p>Design an original card online in under 5 minutes! Choose from thousands of professionally designed
          templates.
          Try
          PhotoADKing now – it's free!</p>
        <p>Create amazing posters without design skills using the online editor PhotoADKing. Choose your poster,
          advertisements, invitation design
          from thousands of templates. Completely free.</p>
        <p>Make your own beautiful cards with PhotoADKing, the online card maker. 1000+ professional and editable
          templates like poster, advertisements, invitation and so on. No design skill required.</p>
        <p>Online, Simply customize the images, fonts and colors in your favourite PhotoADKing layout to create an
          original poster, flyer, business card, advertisement, invitation, ad, facebook post, instagram post, facebook
          cover, pinterest, A4, and many more that will wow your audience. With postermaker, you won't need to hire a
          designer to create incredible marketing materials, edgy music posters and more!</p>
        <!-- for Top 2 Word Key Phrase -->
        <h3>Poster Maker</h3><!-- for Top 3 Word Key Phrase -->
        <h4>Poster Flyer Creator</h4><!-- for internal linking --> <a title="View pricing" href="https://photoadking.com/go-premium/">Flyers
          Posters
          Advertisements Invitation Announcement Video Maker</a>
        <a title="Click to check other apps" href="https://postermaker.co.in/">Flyers
          Posters Advertisements Invitation Announcement Video Maker with 1000+ beatifully designed professional
          templates.</a> <a title="Click to check other apps" href="https://postermaker.co.in/">https://postermaker.co.in</a>
        <p>PhotoADKing's online card maker provides a wide range of beautiful photo cards templates and layouts, helping
          you
          easily design your own greeting cards and photo cards online for all of occasions and events with a few
          clicks.
        </p>
        <a title="Check our game store" href="https://photoeditorlab.co.in/">https://photoeditorlab.co.in</a>
        <p>Also you can find mobile apps available on our appstore. <a title="View more apps" href="https://postermaker.co.in/">Click
            here</a>
          to get more apps on your mobile.</p>
        <p>Increase Brand Awareness.</p>
        <p>Grow Your Business.</p>
        <p>Share Knowledge With Teams.</p>
        <p>Explain Business Strategy And Processes</p>
        <p>Motivate People To Take Actions</p>
        <p>Explain New Public Initiatives</p>
        <p>Invite People</p>
        <p>Boost Engagement.</p>
        <p>Communicate Your Ideas.</p>
        <p>Increase Brand Awareness.</p>
        <p>Grow Your Business.</p>
        <p>Share Knowledge With Teams.</p>
        <p>Explain Business Strategy And Processes</p>
        <p>Motivate People To Take Actions</p>
        <p>Explain New Public Initiatives</p>
        <p>Invite People</p>
        <p>Boost Engagement.</p>
        <p>Communicate Your Ideas.</p>
        <p>Increase Brand Awareness.</p>
        <p>Grow Your Business.</p>
        <p>Share Knowledge With Teams.</p>
        <p>Explain Business Strategy And Processes</p>
        <p>Motivate People To Take Actions</p>
        <p>Explain New Public Initiatives</p>
        <p>Invite People</p>
        <p>Boost Engagement.</p>
        <p>Communicate Your Ideas.</p>
        <p>Increase Brand Awareness.</p>
        <p>Grow Your Business.</p>
        <p>Share Knowledge With Teams.</p>
        <p>Explain Business Strategy And Processes</p>
        <p>Motivate People To Take Actions</p>
        <p>Explain New Public Initiatives</p>
        <p>Invite People</p>
        <p>Boost Engagement.</p>
        <p>Communicate Your Ideas.</p>
        <p>We also provides you with the space to include incentives such as coupon codes and
          vouchers. This is a great way to try and drum up some interest in your event and also create some early
          awareness. Use your designed Ad as an incentive mechanism by providing a discount to the first 20 people
          who use a certain coupon code.</p>
        <p>High-level customization support</p>
        <p>Cool sticker collection with adding your own option</p>
        <p>Add text with multiple fonts and text effects</p>
        <p>Change background from gallery or from background collection</p>
        <p>Undo or Redo your changes</p>
        <p>Autosave your work</p>
        <p>Multiple layers</p>
        <p>Social media certainly has its benefits in helping to
          promote business and events, poster, advertisements, invitation, generate some awareness and create a buzz
          around them. You
          don't
          need a graphic designer to create such buzz on social media.</p>
      </div>
      <!-- <div id="footer"></div> -->
      <div class="l-footer-container row no-gutters c-new-footer" id="hmFooter">
        <div class="col-12 col-xl-10 col-lg-12 col-md-12 col-sm-12 m-auto l-footer-content-container justify-content-around">
          <div class="n-footer-logo-container">
            <img src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" data-src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/photoadking.svg" class="footer-logo height-init" alt="image not found">
          </div>

          <ul class="n-photoadking-footer-menu-container pl-2">
            <li>Create Design</li>
            <li><a id="lnk_hm" onmouseover="rotateSvg('#lnk_hm')" onmouseout="rotatebackSvg('#lnk_hm')" data-html="true" data-content="<div class=&quot;row m-0 marketing-opt&quot;>
                    <div class=&quot;col-5 m-0 pr-0 pl-20px brdr-right&quot;>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/flyers/&quot;>Flyer</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/posters/&quot;>Poster</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/brochures/&quot;>Brochure</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/infographics/&quot;>Infographic</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/business-card/&quot;>Business Card</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/logo/&quot;>Logo</a>

                    </div>
                    <div class=&quot;col-7 m-0 pl-20px pr-0&quot;>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/gift-certificate/&quot;>Gift Certificate</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/restaurant-menu/&quot;>Menu</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/banner-maker/&quot;>Banner</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/billboard/&quot;>Billboard</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/ad-maker/&quot;>Advertisement</a>

                    </div>
                  </div>" data-original-title="" title="">
                Marketing <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                              c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a></li>
            <li><a id="lnk_ftr" onmouseover="rotateSvg('#lnk_ftr')" onmouseout="rotatebackSvg('#lnk_ftr')" data-html="true" data-content="<div class=&quot;d-flex m-0 socialmedia-opt&quot;>
                    <div class=&quot; m-0 pr-3 pl-20px brdr-right&quot; >
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/facebook-ad-maker/&quot;>Facebook Ads</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/facebook-cover/&quot;>Facebook Cover</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-media-post/facebook-post/&quot;>Facebook Post</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/instagram-ad-maker/&quot;>Instagram Ads</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/instagram-post/&quot;>Instagram Post</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/social-story/instagram-story/&quot;>Instagram Story</a>
                    </div>
                    <div class=&quot;m-0 pl-20px pr-0&quot;>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/linkedin-banner/&quot;>LinkedIn Cover</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/linkedin-post/&quot;>LinkedIn Post</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/twitter-header/&quot;>Twitter Header</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/tumblr-header/&quot;>Tumblr Header</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-channel-art/&quot;>YouTube Channel Art</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-thumbnail/&quot;>YouTube Thumbnail</a>
                    </div>

                  </div>" data-original-title="" title="">Social Media <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                              c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a></li>

            <li><a id="lnk_primun" onmouseover="rotateSvg('#lnk_primun')" onmouseout="rotatebackSvg('#lnk_primun')" data-html="true" data-content="<div class=&quot;row m-0 ebook-opt&quot;>
                    <div class=&quot;col-12 m-0 pr-0 pl-20px&quot; >
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/book-cover/e-book/&quot;>eBook</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/&quot;>Album Cover</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/blog-banner-maker/&quot;>Blog Banner</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/storyboard/&quot;>Storyboard</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/book-cover/magazine-cover/&quot;>Magazine Cover</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/book-cover/wattpad-cover/&quot;>Wattpad Cover</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/moodboard/&quot;>Mood Board</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/podcast-cover/&quot;>Podcast Cover</a>
                    </div>

                  </div>" data-original-title="" title="">Blogging &amp; eBooks <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                          c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a></li>
            <li><a id="lnk_invitation" onmouseover="rotateSvg('#lnk_invitation')" onmouseout="rotatebackSvg('#lnk_invitation')" data-html="true" data-content="<div class=&quot;row m-0 invitation-opt&quot;>
                    <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/wedding-invitation-maker/&quot;>Wedding</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invitations/&quot;>Invite</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/announcement-maker/&quot;>Announcement</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/greeting-card-maker/&quot;>Greeting Card</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/postcard-templates/&quot;>Postcard</a>
                    </div>

                  </div>" data-original-title="" title="">Invitation &amp; Events <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                          c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a>
            </li>
            <li><a id="lnk_sprt" onmouseover="rotateSvg('#lnk_sprt')" onmouseout="rotatebackSvg('#lnk_sprt')" data-html="true" data-content="<div class=&quot;row m-0 personalization-opt&quot;>
                    <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/calendar/&quot;>Calendar</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/planners/&quot;>Planner</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/flowchart-templates/&quot;>Flowchart</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/desktop-wallpaper/&quot;>Desktop Wallpaper</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/biodata/&quot;>Biodata</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/resume/&quot;>Resume</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/scrapbook/&quot;>Scrapbook</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/album-covers/photobook/&quot;>Photo Book</a>
                    </div>

                  </div>" data-original-title="" title="">Personalization <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                          c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a></li>
            <li><a id="lnk_doc" onmouseover="rotateSvg('#lnk_doc')" onmouseout="rotatebackSvg('#lnk_doc')" data-html="true" data-content="<div class=&quot;row m-0 letter-opt&quot;>
                    <div class=&quot;col-12 m-0 pl-20px pr-0&quot; >
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/certificate/&quot;>Certificate</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/presentation-maker/&quot;>Presentation</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/letterhead-maker/ &quot;>Letterhead</a>
                      <a class=&quot;c-footer-link-txt&quot; onclick=&quot;closeActivePopup()&quot; href=&quot;{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/invoice-templates/&quot;>Invoice</a>


                    </div>

                  </div>" data-original-title="" title="">Documents &amp; Letter <svg version="1.1" x="0px" y="0px" width="10px" height="6px" class="footer-svg" viewBox="0 0 16 8">
                  <path d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
                                          c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
                  </path>
                </svg></a></li>
          </ul>
          <ul class="n-photoadking-footer-menu-container pl-2 ">
            <li>Create Video</li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/design/video-flyer-maker/">Video Flyer</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/design/video-brochure-maker/">Video Brochure</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/design/video-invitation-maker/">Video Invitation</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-intro-maker/">YouTube Intro</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/outro-video-maker/">Outro</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/marketing-video-maker/">Marketing Video</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/promo-video-maker/">Promo Video</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/video-ad-maker/">Video Ad</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/animated-video-maker/">Animated Video</a></li>
            <li><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/logo-reveal-intro-maker/">Logo Reveal Video</a></li>
          </ul>

          <ul class="n-photoadking-footer-menu-container pl-2 ">
            <li>Popular Categories</li>
            <li><a id="lnk_flyr" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/flyer-maker/">Flyer Maker</a></li>
            <li><a id="lnk_brochure" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/poster-maker/">Poster Maker</a></li>
            <li><a id="lnk_logo" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/brochure-maker/">Brochure Maker</a></li>
            <li><a id="lnk_rsme" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/logo-maker/">Logo Maker</a></li>
            <li><a id="lnk_business" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/thumbnail-maker/">YouTube Thumbnail Maker</a></li>
            <li><a id="lnk_inv" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/youtube-banner-maker/">YouTube Banner Maker</a></li>
            <li><a id="lnk_certfcte" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/business-card-maker/">Business Card Maker</a></li>
            <li><a id="lnk_menu" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/menu-maker/">Menu Maker</a></li>
            <li><a id="lnk_certfctemaker" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/certificate-maker/">Certificate Maker</a></li>
            <li><a id="lnk_prdct" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/templates/youtube-intro-maker/">Intro Maker</a></li>
            <li><a id="lnk_prdctinsta" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/instagram-post-maker/">Instagram Post Maker</a></li>
            <li><a id="lnk_pstr" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/meme-maker/">Meme Maker</a></li>
            <li><a id="lnk_bio" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/infographic-maker/">Infographic Maker</a></li>
            <li><a id="lnk_anuncmnt" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/biodata-maker/">Biodata Maker</a></li>
            <li><a id="lnk_cnr" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/pamphlet-maker/">Pamphlet Maker</a></li>
            <li><a id="lnk_cnrgift" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/gift-certificate-maker/">Gift Certificate Maker</a></li>
            <li><a id="lnk_cnrinvi" role="main" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/invitation-maker/">Invitation Maker</a></li>
          </ul>
          <ul class="n-photoadking-footer-menu-container pl-2 ">
            <li>Support</li>
            <li><a id="lnk_fbpost" href="https://helpphotoadking.freshdesk.com/support/home">Help Center</a></li>
            <li><a id="lnk_insta" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/legal-information/contact/">Contact Us</a></li>
            <!-- <li><a id="lnk_ytube" href="#">Tutorial</a></li> -->
            <li><a id="lnk_fbcvr" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/whats-new/">What's New</a></li>

          </ul>
          <ul class="n-photoadking-footer-menu-container pl-2 ">
            <li>Discover</li>
            <li><a id="lnk_ldrbrd" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/brand-identity-maker/">Brand Kit</a></li>
            <li><a id="lnk_skyscrpr" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/social-media-content-calendar/">Marketing Calendar</a></li>
            <li><a id="lnk_bnr_qr" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/qr-code-generator/">QR Generator</a></li>
            <li><a id="lnk_bnr_graph" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/graph-maker/">Graph Maker</a></li>
            <li><a id="lnk_bnr_barcode" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/barcode-generator/">Barcode Maker</a></li>
            <li><a id="lnk_bnr_removal" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/background-remover/">Remove Background</a></li>

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
                  </script> PHOTOADKING. ALL Rights Reserved.
                </div>
              </div>
              <div class="col-xl-5 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                <div class="c-support-links">
                  <div class="row">
                    <div class="col-lg-12">
                      <ul class="list-unstyled d-flex">
                        <li><a id="lnk_trms" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/legal-information/terms-of-service/">Terms Of
                            Services</a></li>
                        <li><a id="lnk_rfnd" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/legal-information/refund-policy/">Refunds</a></li>
                        <li><a id="lnk_privcy" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/legal-information/privacy-policy/">Privacy</a></li>
                        <li><a id="lnk_cokie" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/legal-information/cookie-policy/">Cookie Policy</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12 icon_last">
                <div class="c-footer-icon-container">
                  <a href="https://www.facebook.com/photoadking/" aria-label="photoadking" target="_blank" id="sc_fb_lnk" rel="noreferrer nofollow">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9586 0.394043C15.3023 0.420788 17.4196 0.996805 19.3105 2.12209C21.1789 3.22492 22.7331 4.78863 23.8245 6.66369C24.943 8.56601 25.5155 10.6962 25.5422 13.0541C25.4758 16.2804 24.4582 19.036 22.4894 21.3209C20.5206 23.6057 17.999 25.0193 15.3888 25.5612V16.5156H17.8566L18.4147 12.9609H14.6778V10.6327C14.6571 10.1501 14.8097 9.6759 15.1081 9.29598C15.407 8.91503 15.9333 8.71481 16.6869 8.69531H18.9435V5.58148C18.9111 5.57107 18.6039 5.52987 18.0218 5.4579C17.3616 5.38067 16.6978 5.33941 16.0332 5.33432C14.5289 5.34126 13.3393 5.76558 12.4642 6.60728C11.5891 7.44873 11.1421 8.66614 11.1232 10.2595V12.9609H8.27942V16.5156H11.1232V25.5612C7.91811 25.0193 5.39656 23.6057 3.42778 21.3209C1.45899 19.036 0.441432 16.2804 0.375 13.0541C0.401579 10.696 0.974114 8.5659 2.0926 6.66369C3.18403 4.78863 4.73825 3.22492 6.60664 2.12209C8.49751 0.997021 10.6148 0.421004 12.9586 0.394043V0.394043Z" fill="#475993"></path>
                    </svg>
                  </a>
                  <a href="https://www.instagram.com/photoadking/" aria-label="photoadking" target="_blank" id="sc_insta_lnk" rel="noreferrer nofollow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16.8503 0H7.14973C3.20735 0 0 3.20735 0 7.14973V16.8503C0 20.7926 3.20735 24 7.14973 24H16.8503C20.7926 24 24 20.7926 24 16.8503V7.14973C24 3.20735 20.7926 0 16.8503 0ZM21.5856 16.8503C21.5856 19.4655 19.4655 21.5856 16.8503 21.5856H7.14973C4.5345 21.5856 2.4144 19.4655 2.4144 16.8503V7.14973C2.4144 4.53446 4.5345 2.4144 7.14973 2.4144H16.8503C19.4655 2.4144 21.5856 4.53446 21.5856 7.14973V16.8503Z" fill="url(#paint0_linear_1888_3557)"></path>
                      <path d="M12.0002 5.79297C8.57754 5.79297 5.79297 8.57754 5.79297 12.0002C5.79297 15.4228 8.57754 18.2074 12.0002 18.2074C15.4229 18.2074 18.2075 15.4229 18.2075 12.0002C18.2075 8.57749 15.4229 5.79297 12.0002 5.79297ZM12.0002 15.7931C9.90547 15.7931 8.20737 14.095 8.20737 12.0002C8.20737 9.90547 9.90551 8.20737 12.0002 8.20737C14.095 8.20737 15.7931 9.90547 15.7931 12.0002C15.7931 14.0949 14.0949 15.7931 12.0002 15.7931Z" fill="url(#paint1_linear_1888_3557)"></path>
                      <path d="M18.2188 7.32683C19.0403 7.32683 19.7062 6.6609 19.7062 5.83944C19.7062 5.01798 19.0403 4.35205 18.2188 4.35205C17.3974 4.35205 16.7314 5.01798 16.7314 5.83944C16.7314 6.6609 17.3974 7.32683 18.2188 7.32683Z" fill="url(#paint2_linear_1888_3557)"></path>
                      <defs>
                        <linearGradient id="paint0_linear_1888_3557" x1="12" y1="23.9301" x2="12" y2="0.186412" gradientUnits="userSpaceOnUse">
                          <stop stop-color="#E09B3D"></stop>
                          <stop offset="0.3" stop-color="#C74C4D"></stop>
                          <stop offset="0.6" stop-color="#C21975"></stop>
                          <stop offset="1" stop-color="#7024C4"></stop>
                        </linearGradient>
                        <linearGradient id="paint1_linear_1888_3557" x1="12.0002" y1="23.9304" x2="12.0002" y2="0.186636" gradientUnits="userSpaceOnUse">
                          <stop stop-color="#E09B3D"></stop>
                          <stop offset="0.3" stop-color="#C74C4D"></stop>
                          <stop offset="0.6" stop-color="#C21975"></stop>
                          <stop offset="1" stop-color="#7024C4"></stop>
                        </linearGradient>
                        <linearGradient id="paint2_linear_1888_3557" x1="18.2188" y1="23.9303" x2="18.2188" y2="0.186492" gradientUnits="userSpaceOnUse">
                          <stop stop-color="#E09B3D"></stop>
                          <stop offset="0.3" stop-color="#C74C4D"></stop>
                          <stop offset="0.6" stop-color="#C21975"></stop>
                          <stop offset="1" stop-color="#7024C4"></stop>
                        </linearGradient>
                      </defs>
                    </svg>
                  </a>
                  <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" aria-label="photoadking" id="sc_ytube_lnk" target="_blank" rel="noreferrer nofollow">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11.3809 15.4355L15.6096 13L11.3809 10.5645V15.4355Z" fill="#FF0000"></path>
                      <path d="M13 0C5.8214 0 0 5.8214 0 13C0 20.1786 5.8214 26 13 26C20.1786 26 26 20.1786 26 13C26 5.8214 20.1786 0 13 0ZM21.123 13.0133C21.123 13.0133 21.123 15.6497 20.7886 16.9211C20.6011 17.6169 20.0524 18.1656 19.3566 18.3529C18.0853 18.6875 13 18.6875 13 18.6875C13 18.6875 7.92802 18.6875 6.64342 18.3396C5.94756 18.1523 5.39888 17.6034 5.21143 16.9076C4.87679 15.6497 4.87679 13 4.87679 13C4.87679 13 4.87679 10.3637 5.21143 9.09242C5.39868 8.39656 5.96085 7.8344 6.64342 7.64714C7.91473 7.3125 13 7.3125 13 7.3125C13 7.3125 18.0853 7.3125 19.3566 7.66043C20.0524 7.84769 20.6011 8.39656 20.7886 9.09242C21.1365 10.3637 21.123 13.0133 21.123 13.0133V13.0133Z" fill="#FF0000"></path>
                    </svg>
                  </a>
                  <a href="https://www.pinterest.com/photoadking/" aria-label="photoadking" target="_blank" id="sc_pintrst_lnk" rel="noreferrer nofollow">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M13.0001 0C5.84003 0 0.0224609 5.81757 0.0224609 12.9776C0.0224609 18.3029 3.19975 22.8675 7.80903 24.8812C7.76427 23.9862 7.80903 22.8675 8.03279 21.883C8.30132 20.809 9.68855 14.8124 9.68855 14.8124C9.68855 14.8124 9.28579 13.9621 9.28579 12.7539C9.28579 10.8296 10.4046 9.39757 11.7918 9.39757C12.9553 9.39757 13.5371 10.2926 13.5371 11.3666C13.5371 12.5301 12.7763 14.3201 12.3736 15.9759C12.0603 17.3631 13.0448 18.4819 14.4321 18.4819C16.8934 18.4819 18.5491 15.3046 18.5491 11.5903C18.5491 8.72623 16.6248 6.623 13.1343 6.623C9.19626 6.623 6.73503 9.57652 6.73503 12.8433C6.73503 13.962 7.04827 14.7676 7.58527 15.394C7.80903 15.6626 7.85379 15.7968 7.76427 16.11C7.7195 16.3338 7.5405 16.9156 7.49574 17.1393C7.40622 17.4525 7.13774 17.5868 6.82451 17.4525C4.98974 16.6918 4.18422 14.7228 4.18422 12.4852C4.18422 8.81571 7.27198 4.38542 13.4475 4.38542C18.4148 4.38542 21.6816 7.96547 21.6816 11.814C21.6816 16.9155 18.8624 20.7193 14.7006 20.7193C13.3133 20.7193 11.9708 19.9586 11.5233 19.1083C11.5233 19.1083 10.7626 22.1066 10.6283 22.6884C10.3598 23.6729 9.82279 24.7021 9.33055 25.4629C10.4941 25.8209 11.7471 25.9999 13.0001 25.9999C20.1601 25.9999 25.9777 20.1823 25.9777 13.0223C25.9777 5.86223 20.1602 0 13.0001 0Z" fill="#CB1F24"></path>
                    </svg>
                  </a>
                  <a href="https://twitter.com/photoadking" aria-label="photoadking" target="_blank" id="sc_twtr_lnk" rel="noreferrer nofollow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_1888_3550)">
                        <path d="M24 4.5585C23.1075 4.95 22.1565 5.2095 21.165 5.3355C22.185 4.7265 22.9635 3.7695 23.3295 2.616C22.3785 3.183 21.3285 3.5835 20.2095 3.807C19.3065 2.8455 18.0195 2.25 16.6155 2.25C13.8915 2.25 11.6985 4.461 11.6985 7.1715C11.6985 7.5615 11.7315 7.9365 11.8125 8.2935C7.722 8.094 4.1025 6.1335 1.671 3.147C1.2465 3.8835 0.9975 4.7265 0.9975 5.634C0.9975 7.338 1.875 8.8485 3.183 9.723C2.3925 9.708 1.617 9.4785 0.96 9.117C0.96 9.132 0.96 9.1515 0.96 9.171C0.96 11.562 2.6655 13.548 4.902 14.0055C4.5015 14.115 4.065 14.1675 3.612 14.1675C3.297 14.1675 2.979 14.1495 2.6805 14.0835C3.318 16.032 5.127 17.4645 7.278 17.511C5.604 18.8205 3.4785 19.6095 1.1775 19.6095C0.774 19.6095 0.387 19.5915 0 19.542C2.1795 20.9475 4.7625 21.75 7.548 21.75C16.602 21.75 21.552 14.25 21.552 7.749C21.552 7.5315 21.5445 7.3215 21.534 7.113C22.5105 6.42 23.331 5.5545 24 4.5585Z" fill="#03A9F4"></path>
                      </g>
                      <defs>
                        <clipPath id="clip0_1888_3550">
                          <rect width="24" height="24" fill="white"></rect>
                        </clipPath>
                      </defs>
                    </svg>
                  </a>
                  <a href="https://in.linkedin.com/showcase/photoadking" aria-label="photoadking" id="sc_lnkdin_lnk" target="_blank" rel="noreferrer nofollow">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5 19.4878H16.8066V15.2726C16.8066 14.2675 16.7887 12.9748 15.4066 12.9748C14.0051 12.9748 13.7914 14.0701 13.7914 15.2011V19.4878H11.1004V10.8201H13.6825V12.0055H13.7199C14.079 11.3238 14.9581 10.6048 16.2687 10.6048C18.9963 10.6048 19.5 12.3996 19.5 14.7339V19.4878ZM8.06325 9.63625C7.19794 9.63625 6.5 8.93588 6.5 8.07381C6.5 7.21175 7.19794 6.51137 8.06325 6.51137C8.9245 6.51137 9.62406 7.21175 9.62406 8.07381C9.62406 8.93588 8.9245 9.63625 8.06325 9.63625ZM9.41037 19.4878H6.71369V10.8201H9.41037V19.4878ZM13 0C5.81994 0 0 5.81994 0 13C0 20.1793 5.81994 26 13 26C20.1801 26 26 20.1793 26 13C26 5.81994 20.1801 0 13 0Z" fill="#2E78B6"></path>
                    </svg>
                  </a>
                </div>
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


<!-- -----------------image template--------------------- -->

<div class="">
  <div class="error-wrapper" id="copyTempURL">
    <p class="heading" id="suces_hding"> Link copied</p>
    <p class="message" id="suces_msg"> Share this template anywhere.</p>
    <div class="icon-wrapper">
      <svg class="err-icon" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <path d="M6.42,16L0,9.45l2.15-2.2L6,11.14l0.46,0.47L15.85,2,18,4.19,6.43,16h0Z" style="fill:#ffffff"></path>
      </svg>
    </div>
    <svg class="error-close-custom" id="dismis_btn" onclick="dismissCopyURLPopup('copyTempURL')" xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 320 512">
      <path fill="#dc3545" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z"></path>
    </svg>
  </div>
  <div id="prevtemp_parent" style="height:100%;display:none;position:absolute;left: 50%;transform: translateX(-50%);background-color:white;">
  <div class="new-template-box" id="prevtemp" style="display:none;scrollbar-width: none;max-height:4000px;">
    <div class="close-btn-template" onclick="closebtntmp('copyTempURL')">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.8927 0.346102C14.0108 0.54307 14.0333 0.770053 13.9508 0.998911L13.9039 1.13022L10.9801 4.06224L8.05446 6.99426L10.9482 9.89063L13.8383 12.7908L13.8908 12.9408C13.9527 13.1209 13.9489 13.2729 13.8833 13.4548C13.8401 13.5749 13.8251 13.5974 13.7089 13.7137C13.5888 13.8337 13.5757 13.8431 13.4313 13.89C13.2044 13.9688 12.9812 13.9426 12.7862 13.8225C12.7411 13.7925 11.6947 12.757 9.85487 10.9167L6.99297 8.05413L4.06168 10.9805L1.1304 13.905L0.978489 13.9557C0.745936 14.0345 0.522761 14.0082 0.31834 13.8769C0.0707846 13.7174 -0.0586195 13.3629 0.025775 13.0759C0.0914145 12.8545 -0.0942535 13.0459 3.04895 9.90376L5.94648 7.00551L3.05458 4.10726L0.164556 1.20901L0.11767 1.0777C0.0295248 0.831957 0.0614071 0.593719 0.205814 0.381744C0.262077 0.297329 0.299586 0.261687 0.38398 0.203534C0.595902 0.0572146 0.834081 0.0253245 1.07976 0.115367L1.21104 0.162264L4.11045 3.05113L7.00797 5.94563L9.89237 3.06051C11.9516 1.00079 12.7993 0.160389 12.8593 0.120995C13.1068 -0.0403316 13.4238 -0.0403316 13.6732 0.120995C13.7782 0.188527 13.827 0.239176 13.8927 0.346102Z" fill="white"/>
      </svg>
    </div>
    <div class="template-model">
      <div class="template-two-part">
        <div class="template-part">
          <div class="new-template-sub" tabindex="-1">
          <div class="one-template-model" id="active_temp_id" style="margin-top:0px !important;" >
          </div>
          <div>
            <div class="sub-template-page p-0" style="margin-top:0;">
              <div class="sub-template">
              <button class="c-slick-prev" style="top: 70px !important;left: 20px !important;" id="ttcbleft" aria-label="photoadking" onclick="scrollLeftSlider()"></button>
              <button class="c-slick-next" style="top: 70px !important;right: 20px !important;display:none;" id="ttcbright" aria-label="photoadking" onclick="scrollRightSlider()"></button>
              <div class="c-template-slider" id="ttcw">
              <div class="w-max-content" id="template_slider">

              </div>
              </div>
                <div class="FmtbLQ TfRV3Q _252raA jAXz3w">
                  <p class="fFOiLQ _2xcaIA _5Ob_nQ fM_HdA">
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="Puzyyw Puzyyw-i">

      </div>
    </div>
  </div>
      <div class="template-heading ml-3"></div>

      <div class="card-wrapper col-five col-three-md col-two-sm" id="card-wrapper">

      </div>
        </div>
    </div>
  </div>
</div>


<!-- -----------------video template--------------------- -->


<div class="">
  <div class="error-wrapper" id="copyVideoTempURL">
    <p class="heading" id="suces_hdingv"> Link copied</p>
    <p class="message" id="suces_msgv"> Share this template anywhere.</p>
    <div class="icon-wrapper">
      <svg class="err-icon" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <path d="M6.42,16L0,9.45l2.15-2.2L6,11.14l0.46,0.47L15.85,2,18,4.19,6.43,16h0Z" style="fill:#ffffff"></path>
      </svg>
    </div>
    <svg class="error-close-custom" id="dismis_btn" onclick="dismissCopyURLPopup('copyVideoTempURL')" xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 320 512">
      <path fill="#dc3545" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z"></path>
    </svg>
  </div>
  <div id="videoprevtemp_parent" style="height:100%;display:none;position:absolute;left: 50%;transform: translateX(-50%);background-color:white;">
  <div class="new-template-box" id="videoprevtemp" style="display:none;scrollbar-width: none;max-height:4000px;">
    <div class="close-btn-template" onclick="closebtntmp('copyVideoTempURL')">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.8927 0.346102C14.0108 0.54307 14.0333 0.770053 13.9508 0.998911L13.9039 1.13022L10.9801 4.06224L8.05446 6.99426L10.9482 9.89063L13.8383 12.7908L13.8908 12.9408C13.9527 13.1209 13.9489 13.2729 13.8833 13.4548C13.8401 13.5749 13.8251 13.5974 13.7089 13.7137C13.5888 13.8337 13.5757 13.8431 13.4313 13.89C13.2044 13.9688 12.9812 13.9426 12.7862 13.8225C12.7411 13.7925 11.6947 12.757 9.85487 10.9167L6.99297 8.05413L4.06168 10.9805L1.1304 13.905L0.978489 13.9557C0.745936 14.0345 0.522761 14.0082 0.31834 13.8769C0.0707846 13.7174 -0.0586195 13.3629 0.025775 13.0759C0.0914145 12.8545 -0.0942535 13.0459 3.04895 9.90376L5.94648 7.00551L3.05458 4.10726L0.164556 1.20901L0.11767 1.0777C0.0295248 0.831957 0.0614071 0.593719 0.205814 0.381744C0.262077 0.297329 0.299586 0.261687 0.38398 0.203534C0.595902 0.0572146 0.834081 0.0253245 1.07976 0.115367L1.21104 0.162264L4.11045 3.05113L7.00797 5.94563L9.89237 3.06051C11.9516 1.00079 12.7993 0.160389 12.8593 0.120995C13.1068 -0.0403316 13.4238 -0.0403316 13.6732 0.120995C13.7782 0.188527 13.827 0.239176 13.8927 0.346102Z" fill="white"/>
      </svg>
    </div>
    <div class="template-model mb-5">
      <div class="template-two-part video-template-height" style="max-height:fit-content;">
        <div class="template-part">
          <div class="new-template-sub" tabindex="-1">
            <div class="one-template-model template-height-model" style="position:relative;margin-top:0px !important;">
              <div id='activeVideoTemplateShimmer' class="shimmerBG" style="max-width:90%;background-color: #EEEEEF;border-radius: 4.267px;max-height:100%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
              </div>
              <div class="border-redius template-after position-relative" id="activeVideoTemplate" style="overflow: hidden;display:none;align-items:center;justify-content:center;z-index:1;">
                <div class="lDm9lQ">
                  <div id="video-modal-container2">
                    <!-- <video style id="video" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/video/5e75c18778e91_preview_video_1584775559.mp4" type="video/mp4" preload="auto" autoplay="autoplay" loop="loop" class="video-player"></video> -->
                  </div>
                  <div>
                    <div class="custom-seekbar edit-video-seekbar" id="custom-seekbar-mdl" style="bottom: -8px;display: block;">
                      <span id="cs-mdl" style="background-color: #0069ff !important;"></span>
                    </div>
                    <p id="start_time" style="font-size: 13px !important;" class="float-left seekbar-time mt-2">00.00</p>
                    <p id="end_time" style="font-size: 13px !important;" class="float-right seekbar-time mt-2">00.00</p>
                  </div>
                </div>
              </div>
            </div>
          <div>
        </div>
      </div>
    </div>
    <div class="Puzyyw Puzyyw-v"></div>
  </div>
</div>
      <div class="template-heading ml-3"></div>
      <div class="card-wrapper col-five col-three-md col-two-sm video-recommended-templates" id="card-wrapper-video">
      </div>
    </div>
        </div>
  </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="{!! config('constant.ACTIVATION_LINK_PATH') !!}/js/spellchecker.js"></script>
<script>
  jQuery.event.special.touchstart = {
    setup: function (_, ns, handle) {
      this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
    }
  };
  jQuery.event.special.touchmove = {
    setup: function (_, ns, handle) {
      this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
    }
  };
  jQuery.event.special.wheel = {
    setup: function (_, ns, handle) {
      this.addEventListener("wheel", handle, { passive: true });
    }
  };
  jQuery.event.special.mousewheel = {
    setup: function (_, ns, handle) {
      this.addEventListener("mousewheel", handle, { passive: true });
    }
  };
</script>
</body>

<script>
  let dflt_playstore_url = "https://play.google.com/store/apps/details?id=com.bg.flyermaker&referrer=utm_source%3DOB_PAK";
    let dflt_appstore_url = "https://apps.apple.com/us/app/id1337666644";
    let dflt_cta_text = "Create Your Design Now"
    @if(isset($main_template["app_cta_detail"]->app_cta_text) && isset($main_template["app_cta_detail"]->playStoreLink) && isset($main_template["app_cta_detail"]->appStoreLink))
      let playstore_url = `{!! $main_template["app_cta_detail"]->playStoreLink !!}`;
      let appstore_url = `{!! $main_template["app_cta_detail"]->appStoreLink !!}`;
      let cta_text = `{!! $main_template["app_cta_detail"]->app_cta_text !!}`;
    @else
      let playstore_url = "";
      let appstore_url = "";
      let cta_text = "";
    @endif
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

  function init() {
    var imgDefer = document.getElementsByTagName('img');
    for (var i = 0; i < imgDefer.length; i++) {
      if (imgDefer[i].getAttribute('data-src')) {
        imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
      }
    }
  }
  window.onload = init;
  let card_width = Math.floor($(".card-item:first-child").width()) - 20;
  let temp_width = Math.floor($(".temparary-item:first-child").width());
  var cat_list;
  var requestdata;
  let image_template_lists = `{!! $main_template['image_template_list'] !!}`;
  let video_template_lists = `{!! $main_template['video_template_list'] !!}`;
  let image_template_list = JSON.parse(image_template_lists);
  let video_template_list = JSON.parse(video_template_lists);
  let allImageTemplateList = {
    "page_0": {
      "templates": image_template_list,
      "is_next_page": true
    }
  }
  let allVideoTemplateList = {
    "page_0": {
      "templates": video_template_list,
      "is_next_page": true
    }
  }
  let pageNumber = 0;
  let is_api_calling = false;
  let is_next_page = true;
  let content_type = 4;
  let rList = [];
  let buffered_word = "";
  let lower_thresh = 0.6;
  let rLock = -1;
  let WordList;
  let search_str;
  $(document).ready(function ($) {
    checkURLAndCallApi();
    setFloatingButtonURL();
    // $(".btn-pegin-container").addClass("btn-next-only");
    $('body').on('click', function (e) {
      $("#autoCompleteDrop").hide();
    });
    $('.panel-collapse').on('show.bs.collapse', function () {
      $(this).siblings('.panel-heading').addClass('active-ul-faq-li-img');
      $(this).parents('.li-box-shadow').css("box-shadow", "0px 2px 8px rgba(0, 0, 0, 0.15);")
    });

    $('.panel-collapse').on('hide.bs.collapse', function () {
      $(this).siblings('.panel-heading').removeClass('active-ul-faq-li-img');
      $(this).parents('.li-box-shadow').css("box-shadow", "none")
    });
    // $('#header').load('{!! config('constant.ACTIVATION_LINK_PATH') !!}/header.html');
    /*  try {
         window.fcWidget.init({
             token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
             host: "https://wchat.freshchat.com"
         });
     } catch (error) {
         console.log(error);
     } */
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
      $('#rlp-text-mob').attr('href', 'https://photoadking.com/app/#/dashboard');
      $('#rlp-btn-txt span').html('Dashboard');
      $('#rlp-link').attr('href', 'https://photoadking.com/app/#/dashboard');

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
    }else{
      $('#hdrnavbtn').attr({href:'{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/sign-up',target:'_blank'});
      $('#hd-logn').show();
      $('#hd-login').show();
      $('#rlp-link').show();
    }
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

    // $('#footer').load('{!! config('constant.ACTIVATION_LINK_PATH') !!}/footer.html');
    validateEmail = function (email) {
      var re =
              /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }

    let catList = $('.sub_cat_list .categorylist');

    let category = $(catList[0]).children('ul').children('li');
    let subcategories = $(category).find('a');
    let subcategory = subcategories[0];

    $("#nav-images-tab").addClass(" active");

    $(subcategory).parents('li').addClass(" active");
    $(catList[0]).addClass(" active");
    $(catList[0]).addClass(" activeSubCategory");
    $(catList[0]).addClass(" activePageSubCategory");
    /* if ($(window).width() >= 1151) {
        $(catList[0]).children('ul').animate({ "height": $(catList[0]).children('ul')[0].scrollHeight }, 50);
        $(catList[0]).children('a').children('svg').css("transform", "rotate(-180deg)");
        // let catList = $('.sub_cat_list .categorylist');
        $(catList[0]).children('ul').css("margin", "5px 0px");

    }else */ if($(window).width() <= 1151){
      $(catList[0]).children('ul').addClass(" active");
      $(catList[0]).children('ul').css("display", "flex");
      $(subcategory).addClass(" active");

    }
    $("#nav-videos").removeClass('active');
    $('#nav-images').addClass("active");
    // showTemplate(allImageTemplateList["page_0"]);

    var tabs = $('.dashboard-tabs');
    var selector = $('.dashboard-tabs').find('a').length;
    var activeItem = tabs.find('.active');
    var activeWidth = activeItem.innerWidth();
    $(".selector").css({
      "left": activeItem.position.left + "px",
      "width": activeWidth + "px"
    });
    localStorage.setItem('isN','true');
    $(".dashboard-tabs").on("click", "a", function (e) {
      e.preventDefault();
      $('.dashboard-tabs a').removeClass("active");
      $(this).addClass('active');
      var activeWidth = $(this).innerWidth();
      var itemPos = $(this).position();
      $(".selector").css({
        "left": itemPos.left + "px",
        "width": activeWidth + "px"
      });
      localStorage.setItem('isN','true');
      if (this.id == "nav-videos-tab") {
        $('#nav-images').removeClass("active");
        $("#nav-videos").addClass('active');
        $('#card-item').empty();
        content_type = 9;
        pageNumber = 0;
        // $(".btn-pegin-container").addClass("btn-next-only");
        // $(".btn-pegin-container").removeClass("btn-prev-only");
        window.history.replaceState(null, null, window.location.pathname);
        showVideoTemplate(allVideoTemplateList["page_0"].templates);
      } else if (this.id == "nav-images-tab") {
        $("#nav-videos").removeClass('active');
        $('#nav-images').addClass("active");
        content_type = 4;
        pageNumber = 0;
        $('#card-item').empty();

        // $(".btn-pegin-container").addClass("btn-next-only");
        // $(".btn-pegin-container").removeClass("btn-prev-only");
        window.history.replaceState(null, null, window.location.pathname);
        showTemplate(allImageTemplateList["page_0"].templates);
      }
    });
    if (activeItem[0].id == "nav-images-tab") {
      content_type = 4;
      showTemplate(allImageTemplateList["page_0"].templates);
    }
    /* function getImageSize(url) {
        var img = new Image();
        img.onload = function () {
            if (this.width > this.height + (this.width / 6)) {
                $('#card-item').removeClass('template-wrapper');
                $('#card-item').addClass('template-wrapper-1');
                card_width = temp_width;
            }
        };
        img.src = url;
    }  */
    // function showTemplate(template_list) {
    //   $('#card-item').empty();
    //   let class_name;
    //   let overlay_class = '';
    //         let getImageSize = new Promise(function (resolve, reject) {
    //         if (template_list[0] && template_list[0].sample_image) {
    //           var img = new Image();
    //           img.src = template_list[0].sample_image;
    //           img.onload = function () {
    //             if (this.width > this.height + (this.width / 6)) {
    //               // $('#card-item').removeClass('template-wrapper');
    //               // $('#card-item').addClass('template-wrapper-1');
    //               // $('.card-ol-block').addClass('template-col-wrapper');
    //               // card_width = temp_width;
    //               $('.video-template-container').css('padding','0 35px');
    //               $('#card-item').css({'column-gap':'10px'});
    //               let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-col-wrapper card-item card-ol-block"></div>';
    //               $('#card-item').append(tempstr);
    //               if($(document).width() > 1440)
    //               {
    //                 card_width = Math.floor($('.card-item:first-child').width() - 34);
    //               }else{
    //               card_width = Math.floor($('.card-item:first-child').width());}
    //               $('#card-item').empty();
    //               class_name = 'template-col-wrapper';
    //               overlay_class = 'crd-overlay-div';
    //               // card_width = card_width - 2;
    //               resolve();
    //             } else {
    //               $('.video-template-container').css('padding','0 20px');
    //               let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center card-item card-ol-block"></div>';
    //               $('#card-item').append(tempstr);
    //               if($(document).width() > 768)
    //               {
    //                 card_width = Math.floor($('.card-item:first-child').width() - 20);
    //               }
    //               else
    //               {
    //               card_width = Math.floor($('.card-item:first-child').width() - 20);
    //             }
    //               $('#card-item').empty();
    //               class_name = '';
    //               overlay_class = '';
    //               // $('.card-ol-block').removeClass('template-col-wrapper');
    //               resolve();
    //             }
    //           };
    //         }else{
    //           // $('#card-item').css('min-height','50px');
    //         }
    //       });
    //       // if(template_list[0].sample_image){
    //       //       getImageSize(template_list[0].sample_image);
    //       // }
    //       // setTimeout(() => {
    //       getImageSize.then(
    //               function (result) {
    //                 for (var k = 0; k < template_list.length; k++) {
    //                   let ratio = template_list[k].height / template_list[k].width;
    //                   let card_height = '';
    //                   if(card_width >= template_list[k].width){
    //                     card_height = template_list[k].height;
    //                   }
    //                   else{
    //                   card_height = (card_width * ratio) < 551 ? (Math.floor(card_width * ratio) >= 40 ? Math.floor(card_width * ratio) :40 ): 550;}
    //                   let templateName = template_list[k].template_name?template_list[k].template_name:"";
    //                   if ($(document).width() > 768) {

    //                     // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block template-page-item"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
    //                     //         template_list[k].catalog_id + '/' + template_list[k].content_id +
    //                     //         '" onclick="storeDetails(\'' +
    //                     //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
    //                     //                 .content_id +
    //                     //         '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img "><img id="imge' + template_list[k].content_id + '" loading="lazy" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
    //                     //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
    //                     //         '" alt="' + templateName +
    //                     //         '" data-isLoaded = "false">';
    //                     // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
    //                     //   if(template_list[k].template_name.length > 30){
    //                     //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div>';
    //                     //   }else{
    //                     //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title">'+ template_list[k].template_name +'</div></a></div>';
    //                     //   }

    //                     // }else{
    //                     //   tempStr += '<div class="crd-overlay"></div><button class="crd-ol-btn">Start from this</button></div></div>'
    //                     // }
    //                     // $('#card-item').append(
    //                     //   tempStr
    //                     // );
    //                     // let tempStr = '<div class="col-lg-3 col-md-4 col-6 text-center g-item card-ol-block"><div class="row"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +  template_list[k].catalog_id + '/' + template_list[k].content_id +  '" onclick="storeDetails(\'' +  template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id +  '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img" style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 0 0;min-width: 290px;min-height: 50px;display:block;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="width: fit-content;border-radius: 0;border:none;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
    //                     //         '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
    //                     //         if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
    //                     //   if(template_list[k].template_name.length > 30){
    //                     //     tempStr += '<div class="crd-overlay low-height" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="border-top: none !important;border: 1px solid #C4C4C4;border-radius: 0px 0px 8px 8px;"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div></div>';
    //                     //   }else{
    //                     //     tempStr += '<div class="crd-overlay low-height" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="border-top: none !important;border: 1px solid #C4C4C4;border-radius: 0px 0px 8px 8px;">'+ template_list[k].template_name +'</div></a></div></div>';
    //                     //   }
    //                     // }else{
    //                     //   tempStr += '<div class="crd-overlay" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button></div></a></div></div>'
    //                     // }

    //                     // $('#card-item').append(
    //                     //   tempStr
    //                     // );
    //                                     // }
    //     let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center  card-ol-block '+class_name+'" style="margin-bottom:1rem;padding-left:10px;padding-right:10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +  template_list[k].catalog_id + '/' + template_list[k].content_id +  '" onclick="storeDetails(\'' +  template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id +  '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;width:100%;"><div class="img_wrper temp-img prograssive_img template-img" style="display:block;width:100%;max-width:100%;height:'+card_height+'px;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid g-item" style="height:'+card_height+'px;width:fit-content;border-radius: 0;border:0px;transition: all .3s ease .15s;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
    //                             '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
    //                             if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
    //                       if(template_list[k].template_name.length > 30){
    //                         tempStr += '<div class="crd-overlay low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style=""><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></div></a></div>';
    //                       }else{
    //                         tempStr += '<div class="crd-overlay low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
    //                       }
    //                     }else{
    //                       tempStr += '<div class="crd-overlay" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></a></div>'
    //                     }

    //                     $('#card-item').append(
    //                       tempStr
    //                     );

    //                     var msnry = new Masonry( '.masonry', {
    //                                 // stagger: 100,
    //                                 // horizontalOrder: true
    //                     });

    //                     $('.template_loader').attr('hidden',true);
    //                     is_api_calling  = false;

    //                   } else if($(document).width() <= 768 && k<10){
    //                     // let tempStr = '<div class="col col-sm-12 p-0 text-center template-page-item"><div class="img_wrper temp-img prograssive_img "><a href="https://photoadking.com/app/#/" target="_blank" style="text-decoration:none;"><img loading="lazy"  id="imge' + template_list[k].content_id + '" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
    //                     //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
    //                     //         '" alt="' + templateName +
    //                     //         '" data-isLoaded = "false"></a></div>';
    //                     // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
    //                     //   tempStr += '<div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
    //                     // }else{
    //                     //   tempStr += '</div>';
    //                     // }
    //                     // $('#card-item').append(tempStr);
    //                     let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center g-item card-ol-block '+class_name+'" style="margin-bottom:1rem;padding:0 10px;"><a href="https://photoadking.com/app/#/"  target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img" style="height:'+card_height+'px;width:100%;max-width:100%;display: block;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="border-radius: 0;border:0px;height:'+card_height+'px;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
    //                             '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
    //                             if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){

    //                         tempStr += '<div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
    //                       }else{
    //                       tempStr += '</div></a></div>'
    //                     }
    //                     $('#card-item').append(tempStr);
    //                     $('.template_loader').attr('hidden',true);
    //                     is_api_calling  = false;
    //                   }
    //                   let img_id = '#imge' + template_list[k].content_id;
    //                   if(card_width >= template_list[k].width){
    //                     // card_height = template_list[k].height;
    //                     $(img_id).css('width', template_list[k].width + 'px');
    //                     // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
    //                   }else{
    //                     // if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
    //                     // {
    //                     //   // if(template_list[k].width < 100)
    //                     //   // {
    //                     //     let img_width = (card_height * template_list[k].width)/template_list[k].height;
    //                     //     $(img_id).css('width', img_width + 'px');
    //                     //     // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
    //                     //   // }
    //                     //   // else{
    //                     //   //   $(img_id).css('width', '100%');
    //                     //   //   $(img_id).parents('div.img_wrper').css('width', '100%');
    //                     //   // }
    //                     // }
    //                       // else{
    //                         let img_width = (card_height * template_list[k].width)/template_list[k].height;
    //                         $(img_id).css('width', img_width + 'px');
    //                   // }
    //                     // $(img_id).css('width', card_width + 'px');
    //                     // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
    //                   }
    //                   // card_width = card_width +2;
    //                   // card_height = card_height +2;
    //                   // $(img_id).css('height', card_height + 'px');

    //                   // $(img_id).attr('height', card_height + 'px');
    //                   // $(img_id).attr('width', card_width + 'px');
    //                   // let wrapper_height= card_height+3;

    //                   // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
    //                   // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
    //                 }
    //                 // $('img').on('load', function () {
    //                 // if ($(this).attr('data-isLoaded') == 'false') {
    //                 //     $(this).attr('src', $(this).attr('data-src'));
    //                 //     $(this).attr('data-isLoaded', 'true');
    //                 // }
    //                 //     let img_id = '#imge' + k;
    //                 //     $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
    //                 // });
    //                 // }, 1500);
    //                 $('#card-item').css('min-height','100px');
    //               });

    // }

    function showTemplate(template_list) {
      $('#card-item').empty();
      let class_name;
      let overlay_class = '';
            let getImageSize = new Promise(function (resolve, reject) {
            if (template_list[0] && template_list[0].sample_image) {
              var img = new Image();
              img.src = template_list[0].sample_image;
              img.onload = function () {
                if (this.width > this.height + (this.width / 6)) {
                  // $('#card-item').removeClass('template-wrapper');
                  // $('#card-item').addClass('template-wrapper-1');
                  // $('.card-ol-block').addClass('template-col-wrapper');
                  // card_width = temp_width;
                  $('.video-template-container').css('padding','0 35px');
                  $('#card-item').css({'column-gap':'10px'});
                  let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-col-wrapper card-item card-ol-block"></div>';
                  $('#card-item').append(tempstr);
                  if($(document).width() > 1440)
                  {
                    card_width = Math.floor($('.card-item:first-child').width() - 34);
                  }else{
                  card_width = Math.floor($('.card-item:first-child').width());}
                  $('#card-item').empty();
                  class_name = 'template-col-wrapper';
                  overlay_class = 'crd-overlay-div';
                  // card_width = card_width - 2;
                  resolve();
                } else {
                  $('.video-template-container').css('padding','0 20px');
                  let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center card-item card-ol-block"></div>';
                  $('#card-item').append(tempstr);
                  if($(document).width() > 768)
                  {
                    card_width = Math.floor($('.card-item:first-child').width() - 20);
                  }
                  else
                  {
                  card_width = Math.floor($('.card-item:first-child').width() - 20);
                }
                  $('#card-item').empty();
                  class_name = '';
                  overlay_class = '';
                  // $('.card-ol-block').removeClass('template-col-wrapper');
                  resolve();
                }
              };
            }else{
              // $('#card-item').css('min-height','50px');
            }
          });
          // if(template_list[0].sample_image){
          //       getImageSize(template_list[0].sample_image);
          // }
          // setTimeout(() => {
          getImageSize.then(
                  function (result) {
                    for (var k = 0; k < template_list.length; k++) {
                      let ratio = template_list[k].height / template_list[k].width;
                      let card_height = '';
                      let prev_id = 'maninDiv' + template_list[k].content_id;
                      if(card_width >= template_list[k].width){
                        card_height = template_list[k].height;
                      }
                      else{
                      card_height = (card_width * ratio) < 551 ? (Math.floor(card_width * ratio) >= 40 ? Math.floor(card_width * ratio) :40 ): 550;}
                      let templateName = template_list[k].template_name?template_list[k].template_name:"";
                      if ($(document).width() > 768) {

                        // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block template-page-item"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
                        //         template_list[k].catalog_id + '/' + template_list[k].content_id +
                        //         '" onclick="storeDetails(\'' +
                        //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                        //                 .content_id +
                        //         '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img "><img id="imge' + template_list[k].content_id + '" loading="lazy" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName +
                        //         '" data-isLoaded = "false">';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   if(template_list[k].template_name.length > 30){
                        //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div>';
                        //   }else{
                        //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title">'+ template_list[k].template_name +'</div></a></div>';
                        //   }

                        // }else{
                        //   tempStr += '<div class="crd-overlay"></div><button class="crd-ol-btn">Start from this</button></div></div>'
                        // }
                        // $('#card-item').append(
                        //   tempStr
                        // );
                        // let tempStr = '<div class="col-lg-3 col-md-4 col-6 text-center g-item card-ol-block"><div class="row"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +  template_list[k].catalog_id + '/' + template_list[k].content_id +  '" onclick="storeDetails(\'' +  template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id +  '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img" style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 0 0;min-width: 290px;min-height: 50px;display:block;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="width: fit-content;border-radius: 0;border:none;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
                        //         if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   if(template_list[k].template_name.length > 30){
                        //     tempStr += '<div class="crd-overlay low-height" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="border-top: none !important;border: 1px solid #C4C4C4;border-radius: 0px 0px 8px 8px;"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div></div>';
                        //   }else{
                        //     tempStr += '<div class="crd-overlay low-height" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="border-top: none !important;border: 1px solid #C4C4C4;border-radius: 0px 0px 8px 8px;">'+ template_list[k].template_name +'</div></a></div></div>';
                        //   }
                        // }else{
                        //   tempStr += '<div class="crd-overlay" style="margin: auto 12px;width: calc(100% - 24px);"></div><button class="crd-ol-btn">Start from this</button></div></a></div></div>'
                        // }

                        // $('#card-item').append(
                        //   tempStr
                        // );
                                        // }
                                        // onclick="viewpreview()"
        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center  card-ol-block '+class_name+'" style="margin-bottom:1rem;padding-left:10px;padding-right:10px;"><a  href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
                                template_list[k].catalog_id + '/' + template_list[k].content_id +
                                '" target="_blank" style="text-decoration:none;" onclick="storeDetails(\'' + template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id + '\','+ template_list[k].content_type +')"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;width:100%;"><div class="img_wrper temp-img prograssive_img template-img" style="display:block;width:100%;max-width:100%;height:'+card_height+'px;min-height:100px;display:flex;align-items: center;justify-content: center;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid g-item" style="height:'+card_height+'px;width:fit-content;border-radius: 0;border:0px;transition: all .3s ease .15s;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
                                '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
                        if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                          if(template_list[k].template_name.length > 30){
                            tempStr += '<div class="crd-overlay low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button  class="crd-ol-btn c-explore-template" style="outline: none;" onclick="viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')"><svg class="mr-2" width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.99604 14.1611C9.80772 14.1611 9.61898 14.1561 9.4294 14.1461C5.39752 13.9311 1.72479 11.4137 0.0732114 7.73225C-0.115112 7.31227 0.0723779 6.81938 0.492357 6.63064C0.912752 6.44274 1.40523 6.62981 1.59397 7.0502C2.99265 10.1675 6.10291 12.2999 9.51815 12.4816C13.2871 12.6837 16.8582 10.4992 18.406 7.0502C18.5944 6.62981 19.0877 6.44274 19.5076 6.63064C19.9276 6.81897 20.1151 7.31227 19.9268 7.73225C18.1765 11.6325 14.235 14.1611 9.99604 14.1611Z" fill="#0069FF"/><path d="M0.833173 8.22373C0.719012 8.22373 0.603184 8.19998 0.492357 8.1504C0.0723779 7.96208 -0.115112 7.46877 0.0732114 7.04879C1.90104 2.97566 6.12082 0.399961 10.5702 0.634949C14.6021 0.849938 18.2748 3.36731 19.9264 7.04879C20.1147 7.46877 19.9272 7.96166 19.5072 8.1504C19.0877 8.33872 18.5944 8.15123 18.4056 7.73084C17.0069 4.6135 13.8967 2.48111 10.4814 2.29945C6.71412 2.09821 3.14139 4.28185 1.59355 7.73084C1.45522 8.04041 1.15149 8.22373 0.833173 8.22373Z" fill="#0069FF"/><path d="M9.99984 10.825C8.10618 10.825 6.56543 9.28468 6.56543 7.39102C6.56543 5.49737 8.10618 3.95703 9.99984 3.95703C11.8935 3.95703 13.4342 5.49737 13.4342 7.39102C13.4342 9.28468 11.8935 10.825 9.99984 10.825ZM9.99984 5.62361C9.04155 5.62361 8.23201 6.43274 8.23201 7.39102C8.23201 8.34931 9.04155 9.15843 9.99984 9.15843C10.9581 9.15843 11.7677 8.34931 11.7677 7.39102C11.7677 6.43274 10.9581 5.62361 9.99984 5.62361Z" fill="#0069FF"/></svg>Preview</button><button class="crd-ol-btn c-check-btn" style="outline: none;"><svg width="22" class="mr-2" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.4367 18.6719H1.94103C1.5732 18.6719 1.28534 18.9757 1.28534 19.3276C1.28534 19.6794 1.58919 19.9832 1.94103 19.9832H19.4526C19.8205 19.9832 20.1083 19.6794 20.1083 19.3276C20.1083 18.9757 19.8045 18.6719 19.4367 18.6719Z" fill="white"/><path d="M1.28522 12.8667L1.26923 15.9852C1.26923 16.1611 1.3332 16.337 1.46113 16.4649C1.58907 16.5929 1.749 16.6568 1.92491 16.6568L5.02743 16.6408C5.20334 16.6408 5.36327 16.5769 5.49121 16.4489L16.2061 5.73407C16.462 5.47819 16.462 5.06239 16.2061 4.79052L13.1355 1.688C12.8797 1.43212 12.4639 1.43212 12.192 1.688L10.049 3.84697L1.47713 12.4029C1.36518 12.5308 1.28522 12.6907 1.28522 12.8667ZM12.6718 3.09533L14.8307 5.2543L13.6153 6.46971L11.4564 4.31075L12.6718 3.09533ZM2.61258 13.1545L10.5128 5.2543L12.6718 7.41326L4.77155 15.2975L2.59659 15.3135L2.61258 13.1545Z" fill="white"/></svg>Edit</button><div class="template-name-title" style=""><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></div></a></div>';
                          }else{
                            tempStr += '<div class="crd-overlay low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn c-explore-template" style="outline: none;" onclick="viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')"><svg class="mr-2" width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.99604 14.1611C9.80772 14.1611 9.61898 14.1561 9.4294 14.1461C5.39752 13.9311 1.72479 11.4137 0.0732114 7.73225C-0.115112 7.31227 0.0723779 6.81938 0.492357 6.63064C0.912752 6.44274 1.40523 6.62981 1.59397 7.0502C2.99265 10.1675 6.10291 12.2999 9.51815 12.4816C13.2871 12.6837 16.8582 10.4992 18.406 7.0502C18.5944 6.62981 19.0877 6.44274 19.5076 6.63064C19.9276 6.81897 20.1151 7.31227 19.9268 7.73225C18.1765 11.6325 14.235 14.1611 9.99604 14.1611Z" fill="#0069FF"/><path d="M0.833173 8.22373C0.719012 8.22373 0.603184 8.19998 0.492357 8.1504C0.0723779 7.96208 -0.115112 7.46877 0.0732114 7.04879C1.90104 2.97566 6.12082 0.399961 10.5702 0.634949C14.6021 0.849938 18.2748 3.36731 19.9264 7.04879C20.1147 7.46877 19.9272 7.96166 19.5072 8.1504C19.0877 8.33872 18.5944 8.15123 18.4056 7.73084C17.0069 4.6135 13.8967 2.48111 10.4814 2.29945C6.71412 2.09821 3.14139 4.28185 1.59355 7.73084C1.45522 8.04041 1.15149 8.22373 0.833173 8.22373Z" fill="#0069FF"/><path d="M9.99984 10.825C8.10618 10.825 6.56543 9.28468 6.56543 7.39102C6.56543 5.49737 8.10618 3.95703 9.99984 3.95703C11.8935 3.95703 13.4342 5.49737 13.4342 7.39102C13.4342 9.28468 11.8935 10.825 9.99984 10.825ZM9.99984 5.62361C9.04155 5.62361 8.23201 6.43274 8.23201 7.39102C8.23201 8.34931 9.04155 9.15843 9.99984 9.15843C10.9581 9.15843 11.7677 8.34931 11.7677 7.39102C11.7677 6.43274 10.9581 5.62361 9.99984 5.62361Z" fill="#0069FF"/></svg>Preview</button><button class="crd-ol-btn c-check-btn" style="outline: none;"><svg width="22" class="mr-2" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.4367 18.6719H1.94103C1.5732 18.6719 1.28534 18.9757 1.28534 19.3276C1.28534 19.6794 1.58919 19.9832 1.94103 19.9832H19.4526C19.8205 19.9832 20.1083 19.6794 20.1083 19.3276C20.1083 18.9757 19.8045 18.6719 19.4367 18.6719Z" fill="white"/><path d="M1.28522 12.8667L1.26923 15.9852C1.26923 16.1611 1.3332 16.337 1.46113 16.4649C1.58907 16.5929 1.749 16.6568 1.92491 16.6568L5.02743 16.6408C5.20334 16.6408 5.36327 16.5769 5.49121 16.4489L16.2061 5.73407C16.462 5.47819 16.462 5.06239 16.2061 4.79052L13.1355 1.688C12.8797 1.43212 12.4639 1.43212 12.192 1.688L10.049 3.84697L1.47713 12.4029C1.36518 12.5308 1.28522 12.6907 1.28522 12.8667ZM12.6718 3.09533L14.8307 5.2543L13.6153 6.46971L11.4564 4.31075L12.6718 3.09533ZM2.61258 13.1545L10.5128 5.2543L12.6718 7.41326L4.77155 15.2975L2.59659 15.3135L2.61258 13.1545Z" fill="white"/></svg>Edit</button><div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
                          }
                        }else{
                          tempStr += '<div class="crd-overlay" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></a></div>'
                        }

                        $('#card-item').append(
                          tempStr
                        );

                        var msnry = new Masonry( '.masonry', {
                                    // stagger: 100,
                                    // horizontalOrder: true
                        });

                        $('.template_loader').attr('hidden',true);
                        is_api_calling  = false;

                      } else if($(document).width() <= 768 && k<10){
                        // let tempStr = '<div class="col col-sm-12 p-0 text-center template-page-item"><div class="img_wrper temp-img prograssive_img "><a href="https://photoadking.com/app/#/" target="_blank" style="text-decoration:none;"><img loading="lazy"  id="imge' + template_list[k].content_id + '" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName +
                        //         '" data-isLoaded = "false"></a></div>';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   tempStr += '<div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
                        // }else{
                        //   tempStr += '</div>';
                        // }
                        // $('#card-item').append(tempStr);
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center g-item card-ol-block '+class_name+'" style="margin-bottom:1rem;padding:0 10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/"  target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img" style="min-height:'+card_height+'px;width:100%;max-width:100%;display: block;"><div onclick="event.stopPropagation(); viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')""><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="top: 10px;right: 20px;width: auto;border-radius:0px;height:30px !important;width:30px !important;"></div><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="border-radius: 0;border:0px;height:'+card_height+'px;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
                                '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
                                if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                            tempStr += '<div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
                        }else{
                          tempStr += '<div class="crd-overlay" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></div>'
                        }
                        $('#card-item').append(tempStr);
                        $('.template_loader').attr('hidden',true);
                        is_api_calling  = false;
                      }
                      let img_id = '#imge' + template_list[k].content_id;
                      if(card_width >= template_list[k].width){
                        // card_height = template_list[k].height;
                        $(img_id).css('width', template_list[k].width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
                      }else{
                        // if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                        // {
                        //   // if(template_list[k].width < 100)
                        //   // {
                        //     let img_width = (card_height * template_list[k].width)/template_list[k].height;
                        //     $(img_id).css('width', img_width + 'px');
                        //     // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                        //   // }
                        //   // else{
                        //   //   $(img_id).css('width', '100%');
                        //   //   $(img_id).parents('div.img_wrper').css('width', '100%');
                        //   // }
                        // }
                          // else{
                            let img_width = (card_height * template_list[k].width)/template_list[k].height;
                            $(img_id).css('width', img_width + 'px');
                      // }
                        // $(img_id).css('width', card_width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
                      }
                      // card_width = card_width +2;
                      // card_height = card_height +2;
                      // $(img_id).css('height', card_height + 'px');

                      // $(img_id).attr('height', card_height + 'px');
                      // $(img_id).attr('width', card_width + 'px');
                      // let wrapper_height= card_height+3;

                      // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                      // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                    }
                    // $('img').on('load', function () {
                    // if ($(this).attr('data-isLoaded') == 'false') {
                    //     $(this).attr('src', $(this).attr('data-src'));
                    //     $(this).attr('data-isLoaded', 'true');
                    // }
                    //     let img_id = '#imge' + k;
                    //     $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
                    // });
                    // }, 1500);
                    $('#card-item').css('min-height','100px');
                  });

    }
    function showVideoTemplate(template_list) {
      let col_class_name, overlay_class;
          $('#nav-images').removeClass("active");
          $("#nav-videos").addClass('active');
          let videoCatList = $('.sub_cat_list_video .categorylist');

          let category = $(videoCatList[0]).children('a');

          $("#nav-videos-tab").addClass(" active");
          if($("#nav-images-tab").hasClass("active")){
            $("#nav-images-tab").removeClass('active');

          }

          $(videoCatList[0]).addClass(" active");
          $(videoCatList[0]).addClass(" activeVideoCategory");

          let getImageSize = new Promise(function (resolve, reject) {
            if (template_list[0] && template_list[0].sample_image) {
              var img = new Image();
              img.src = template_list[0].sample_image;
              img.onload = function () {
                if (this.width > this.height + (this.width / 6) ) {
                  // $('#card-item').removeClass('template-wrapper');
                  // $('#card-item').addClass('template-wrapper-1');
                  // card_width = temp_width;
                  $('.video-template-container').css('padding','0 35px');
                  $('#card-item').css({'column-gap':'10px'});
                  let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-col-wrapper video-card-item-1 mx-auto template-page-item card-ol-block" style="padding-left:10px;padding-right:10px;"></div>';
                  $('#card-item').append(tempstr);
                  card_width = Math.floor($('.video-card-item-1').width());
                  $('#card-item').empty();
                  col_class_name = 'template-col-wrapper';
                 overlay_class = 'crd-overlay-video';
                  resolve();
                } else {
                  let tempstr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center video-card-item-1 mx-auto template-page-item card-ol-block" style="padding-left:10px;padding-right:10px;"></div>';
                  $('#card-item').append(tempstr);
                  card_width = Math.floor($('.video-card-item-1').width());
                  $('#card-item').empty();
                  overlay_class = '';
                 col_class_name = '';
                  resolve();
                }
              };
            }else{
              $('#card-item').css('min-height','50px');
            }
          });
          let editroUrl;
          if (template_list[0] && template_list[0].content_type == 9) {
            editroUrl = "https://photoadking.com/app/#/video-editor/";
          } else if (template_list[0].content_type == 10) {
            editroUrl = "https://photoadking.com/app/#/intro-editor/";
          }

          // if(template_list[0].sample_image){
          //       getImageSize(template_list[0].sample_image);
          //     }
          // setTimeout(() => {
          getImageSize.then(
                  function (result) {
                    for (var k = 0; k < template_list.length; k++) {
                      let ratio = template_list[k].height / template_list[k].width;
                      let card_height='';
                      if(card_width >= template_list[k].width){
                        card_height = template_list[k].height;
                      }
                      else{
                     card_height = (card_width * ratio) < 551 ? Math.floor(card_width * ratio) : 550;}
                      let temp_param =  "'" + editroUrl+"','" + template_list[k].sub_category_id + "','" + template_list[k].catalog_id + "','" + template_list[k].content_id + "','" + template_list[k].content_id+"','" + template_list[k].height +"','" + template_list[k].width +"'";
                      let templateName = template_list[k].template_name?template_list[k].template_name:"";
                      let _id = "'"+ template_list[k].content_id + "'";
                      if ($(document).width() > 768) {
                        let isTemplateNameFound = template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != "";
                        let className = isTemplateNameFound == true?'template-name-found':'';
                        let imageClassName = isTemplateNameFound == true?'marquee-image':'';
                        // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block video-card-item-1 mx-auto template-page-item '+ className +'" onmouseenter="showVideo(' + _id + ')" onmouseleave="hideVideo(' + _id + ')" ><div onclick="playVideoInModel('+ temp_param +')" class="img_wrper temp-img prograssive_img '+imageClassName+'"><img loading="lazy" onload="removeShimmer(i' +_id+ ')" draggable="false" id ="i' +template_list[k].content_id+ '" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + template_list[k].content_id+ '" loop muted><source draggable="false" type="video/mp4" src="' +
                        //         template_list[k].content_file +
                        //         '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id+ '" ></div><div id = "playButton' +template_list[k].content_id+ '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' +template_list[k].content_id
                        //         + '"><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" onclick="storeDetails(\'' +
                        //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                        //                 .content_id +
                        //         '\','+ template_list[0].content_type +')" href="' + editroUrl +
                        //         template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
                        //         '">';
                        //
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center video-card-item-1 mx-auto video-card-item-1 template-page-item card-ol-block '+ className +' '+ col_class_name +'" onmouseenter="showVideo(' +_id+ ')" onmouseleave="hideVideo(' +_id +')" style="margin-bottom:1rem !important;padding-left:10px;padding-right:10px;"><div style="border: 1px solid #C4C4C4 !important;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;"><div onclick="playVideoInModel(' + temp_param +')" class="img_wrper cursor-pointer temp-img prograssive_img '+imageClassName+'"  style="display:block;max-width:100%;width:100%;height:'+card_height+'px"><img loading="lazy" style="height:'+card_height+'px;max-width:100%;border-radius: 0px !important;border:0px;transition:.1s ease;" onload="removeShimmer(i' +template_list[k].content_id + ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                                template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video"  style="" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                                template_list[k].content_file +
                                '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" style="top: 8px;left: 20px;"></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="border-radius:0;"></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                                + '" style="width:93%;"><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '" ><span id="cs' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" href="' + editroUrl +
                                template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
                                '" target="_blank" onclick="storeDetails(\'' +
                                template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                                        .content_id +
                                '\','+ template_list[0].content_type +')">';
                                 if(isTemplateNameFound == true){
                          tempStr += '<div class="edit-video-button mx-auto marque-video '+ overlay_class+'"  id = "editButton' + template_list[k].content_id+ '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></a><div class="template-name-title" style="height:30px;">'+ template_list[k].template_name +'</div></div>';
                        }else{
                          tempStr += '<div class="edit-video-button mx-auto '+ overlay_class+'"  id = "editButton' + template_list[k].content_id+ '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></a></div>';
                        }
                        $('#card-item').append(
                          tempStr
                        );
                        var msnry = new Masonry( '.masonry', {
                          // horizontalOrder: true
                                    // stagger: 100,
                        });

                        is_api_calling = false;
                        $('.template_loader').attr('hidden',true);


                      } else if($(document).width() <= 768 && k<10){
                        let isTemplateNameFound = template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != "";
                        let className = isTemplateNameFound == true?'template-name-found':'';
                        let imageClassName = isTemplateNameFound == true?'marquee-image':'';
                        // let tempStr =  '<div class="col col-sm-12 p-0 text-center card-ol-block video-card-item-1 mx-auto template-page-item pb-0 '+className+'" onmouseenter="showVideo(' +template_list[k].content_id+ ')" onmouseleave="hideVideo(' + template_list[k].content_id+ ')" ><div class="img_wrper temp-img prograssive_img '+imageClassName+'"><img loading="lazy" onload="removeShimmer(i' +template_list[k].content_id+ ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + template_list[k].content_id+ '" loop muted><source draggable="false" type="video/mp4" src="' +
                        //         template_list[k].content_file +
                        //         '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id+ '" ></div><div id = "playButton' + template_list[k].content_id+ '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                        //         + '"><div class="custom-seekbar"  id="custom-seekbar' + template_list[k].content_id+ '"><span id="cs' + template_list[k].content_id+ '"></span></div></div></div><a class= "editVideo-txt" target="_blank" href="https://photoadking.com/app/#/">';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   tempStr += '<div class="edit-video-button mx-auto marque-video" id = "editButton' + template_list[k].content_id+ '"><span>EDIT VIDEO</span></div></a><div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
                        // }else{
                        //   tempStr += '<div class="edit-video-button mx-auto" id = "editButton' + template_list[k].content_id+ '"><span>EDIT VIDEO</span></div></a></div>';
                        // }
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-page-item g-item card-ol-block '+ className +' '+ col_class_name +'" style="margin-bottom:1rem !important;padding:0 10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/"  target="_blank" style="text-decoration:none;"><div style="border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;"><div class="img_wrper temp-img prograssive_img '+imageClassName+'"  style="height:'+card_height+'px;display:block;width:100%;max-width:100%;"><img loading="lazy" style="max-width:100%;border-radius: 0px !important;border: 0px;height:'+card_height+'px;" onload="removeShimmer(i' +template_list[k].content_id + ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                                template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                '" alt="' + templateName + '" data-isLoaded="false" ><video class="template-video"  style="" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                                template_list[k].content_file +
                                '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" style="top: 8px;left: 20px;"></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic" onclick="event.stopPropagation(); playVideoInModel(' + temp_param +')"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="top: 10px;right: 20px;height:30px !important;width:30px !important;border-radius:0;"></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                                + '" style="width:93%;><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div>';
                                if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                                  tempStr += '<div class="edit-video-button mx-auto marque-video '+ overlay_class+'" id = "editButton' + template_list[k].content_id + '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div><div class="template-name-title" style="height:27px;">'+ template_list[k].template_name +'</div></div></a></div>';
                        }else{
                          tempStr += '<div class="edit-video-button mx-auto '+ overlay_class+'" id = "editButton' + template_list[k].content_id + '" style="border-radius: 0 0 8px 8px;border: 1px solid #C4C4C4 !important;border-top:0px !important;margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></div></a></div>';
                        }
                        $('#card-item').append(
                          tempStr
                        );
                        var msnry = new Masonry( '.masonry', {
                                    // stagger: 100,
                        });
                        is_api_calling = false;
                        $('.template_loader').attr('hidden',true);
                      }
                      let img_id = '#i' + template_list[k].content_id;
                      // if(card_width >= template_list[k].width){
                        // card_height = template_list[k].height;
                        $(img_id).css('width',  template_list[k].width + 'px');
                      //   if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                      //   {
                      //     if(template_list[k].width < 100)
                      //     {
                      //       $(img_id).css('width',  template_list[k].width + 'px');
                      //       $(img_id).parents('div.img_wrper').css('width',  template_list[k].width + 'px');
                      //     }
                      //     else{
                      //       $(img_id).css('width', '100%');
                      //       $(img_id).parents('div.img_wrper').css('width', '100%');
                      //     }
                      //   }
                      //     else{
                      //   $(img_id).css('width', template_list[k].width + 'px');
                      // }
                        // $(img_id).css('width', template_list[k].width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
                      // }else{
                      //   if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                      //   {
                      //     if(template_list[k].width < 100)
                      //     {
                      //       $(img_id).css('width', card_width + 'px');
                      //       $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                      //     }
                      //     else{
                      //       $(img_id).css('width', '100%');
                      //       $(img_id).parents('div.img_wrper').css('width', '100%');
                      //     }
                      //   }
                      //     else{
                      //   $(img_id).css('width', card_width + 'px');
                      // }
                        // $(img_id).css('width', card_width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
                      // }
                      // $(img_id).css('height', card_height + 'px');
                      // $(img_id).css('width', card_width + 'px');
                      // $(img_id).attr('height', card_height + 'px');
                      // $(img_id).attr('width', card_width + 'px');
                      // let wrapper_height= card_height+3;
                      // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                      // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                    }

                    // $('img').on('load', function () {
                    //   let img_id = '#i' + k;
                    //   $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
                    // if ($(this).attr('data-isLoaded') == 'false') {
                    //     $(this).attr('src', $(this).attr('data-src'));
                    //     $(this).attr('data-isLoaded', 'true');
                    // }

                    setTimeout(function () {
                      $('.play-btn-ic').css('display', 'block');
                      $('#card-item').css('min-height','100px');
                    }, 5000);
                    // });
                    // }, 1500);
                  });

    }

    if(document.getElementById("popular_tag_container") != null) {
      if($("#popular_tag_container").height() >= '86') {
        $("#popular_tag_container").css("height", "85px");
      }
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
      $(this).before($('.template-overlay'));
      //Now set z-index of modal greater than backdrop
      $(this).css("z-index", parseInt($('.template-overlay').css('z-index')) + 1);
    });

    document.getElementById("mainbody").onscroll = function (e) {
        if(is_api_calling  == false){
        if($('#mainbody').scrollTop() > Math.round($('.video-template-container')[0].scrollHeight * .70) && $('#mainbody').scrollTop() < $('.video-template-container')[0].scrollHeight)
        {
          is_api_calling = true;
          if(pageNumber != 5 && localStorage.getItem('isN') == 'true'){
            $('.template_loader').attr('hidden',false);
         changePage(false,'');
          }


        }
    }
      scrollFunction()
    };
    openPopover();
    openFooterPopover();
    init();

  });

  function showOtherTemplate(page_number,content_type, id = undefined) {
            let content_uuids = [];
            if(content_type == 4){
              // for (var prop in allImageTemplateList) {
                content_uuids = allImageTemplateList["page_0"].templates.map(value => value.content_id);
              // }
            }else{
              // for (var prop in allVideoTemplateList) {
                content_uuids = allVideoTemplateList["page_0"].templates.map(value => value.content_id);
              // }
            }
            $.ajax({
                type: "POST",
                url: '{!! $main_template['API_getTemplatesByCategoryIdV2'] !!}',
                data: JSON.stringify({
                  'page': page_number,
                  'item_count': 40,
                  'content_uuids': content_uuids,
                  'content_type': content_type
                }),
                dataType: 'json',
                contentType: 'application/json',
                error: function (err) {
                    alert("Could not connect to the server.");
                },
                success: function (result) {
                  if(result.code == 201){
                    // window.history.replaceState(null, null, window.location.pathname);
                    // pageNumber = 0;
                    if( $(".deskNextcontainer"). css("display") == "none" ){
                      $(".deskNextcontainer").css('display','block');
                    }
                    else{
                      setTimeout(() => {
                        $(".deskNextcontainer").css('display','none');
                      }, 2000);

                    }

                    return;
                  }
                  if(result.code == 200)
                  {
                    $(".deskNextcontainer").css('display','none');
                  }
                  if(result.data.result.length == 0){
                    // window.history.replaceState(null, null, window.location.pathname);
                    // pageNumber = 0;
                    $(".deskNextcontainer").css('display','block');
                    return;
                  }
                  // if(result.data.is_next_page == false && page_number == 2){
                  //   $(".btn-pegin-container").hide();
                  // }else{
                    if(result.data.is_next_page == false){
                      localStorage.setItem('isN','false');
                      // $(".btn-pegin-container").removeClass("btn-next-only");
                      // $(".btn-pegin-container").addClass("btn-prev-only");
                    }
                    if(result.data.is_next_page == true){
                      localStorage.setItem('isN','true');
                      // $(".btn-pegin-container").removeClass("btn-prev-only");
                      // $(".btn-pegin-container").removeClass("btn-next-only");
                    }
                    // if(page_number == 5){
                    //   $(".btn-pegin-container").removeClass("btn-next-only");
                    //   $(".btn-pegin-container").addClass("btn-prev-only");
                    // }
                  // }
                  let keyName = "page_"+page_number;
                  if(content_type == 9){
                    allVideoTemplateList[keyName] = {
                      "templates": result.data.result,
                      "is_next_page": page_number == 5?false:result.data.is_next_page
                    };
                    showNewVideoTemplate(result.data.result, id);
                  }else{
                    allImageTemplateList[keyName] = {
                      "templates": result.data.result,
                      "is_next_page": page_number == 5?false:result.data.is_next_page
                    };
                    showNewImageTemplate(result.data.result, id);
                  }
                  // scrollTo("card-item");
                }
            });
  }
  function scrollTo(id) {
    var element = document.getElementById(id);
    var headerOffset = 60;
    var elementPosition = element.offsetTop;
    var offsetPosition = elementPosition - headerOffset;
    document.documentElement.scrollTop = offsetPosition;
    document.body.scrollTop = offsetPosition; // For Safari
  }
  // function showNewImageTemplate(template_list, id = undefined) {
  //           let getImageSize = new Promise(function (resolve, reject) {
  //           if (template_list[0].sample_image) {
  //             var img = new Image();
  //             img.src = template_list[0].sample_image;
  //             img.onload = function () {
  //               if ($('#card-item').children().eq(1).hasClass('template-col-wrapper') == true) {
  //                 class_name = 'template-col-wrapper';
  //                 overlay_class = 'crd-overlay-div';
  //                 // $('#card-item').removeClass('template-wrapper');
  //                 // $('.card-ol-block').addClass('template-col-wrapper');
  //                 // card_width = temp_width;
  //                 // card_width = card_width - 2;
  //                 resolve();
  //               } else {
  //                 class_name = '';
  //                 overlay_class = '';
  //                 // $('.card-ol-block').removeClass('template-col-wrapper');
  //                 resolve();
  //               }

  //             };
  //           }else{
  //             // $('#card-item').css('min-height','50px');
  //           }
  //         });
  //         // if(template_list[0].sample_image){
  //         //       getImageSize(template_list[0].sample_image);
  //         // }
  //         // setTimeout(() => {
  //         getImageSize.then(
  //                 function (result) {
  //                   if(id){
  //                     // let arrowIcon = $("#"+id+" svg.sec-first-button-svg");
  //                     // let loaderSVG = $("#"+id+" svg.btn-loader-svg");
  //                     // loaderSVG.hide();
  //                     // arrowIcon.show();
  //                 //     document.getElementById('templateTop').scrollIntoView({
  //                 //   behavior: 'smooth',
  //                 //   block: 'start',
  //                 //   inline: 'start'
  //                 // });
  //                   }
  //                   // $('#card-item').empty();
  //                   for (var k = 0; k < template_list.length; k++) {
  //                     let card_height = '';
  //                     let ratio = template_list[k].height / template_list[k].width;
  //                     if( card_width >= template_list[k].width){
  //                       card_height = template_list[k].height;
  //                     }
  //                     else
  //                     {card_height = (card_width * ratio) < 551 ? (Math.floor(card_width * ratio) >= 40 ? Math.floor(card_width * ratio) :40 ): 550;}
  //                     let templateName = template_list[k].template_name?template_list[k].template_name:"";
  //                     if ($(document).width() > 768) {
  //                       // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block template-page-item"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
  //                       //         template_list[k].catalog_id + '/' + template_list[k].content_id +
  //                       //         '" onclick="storeDetails(\'' +
  //                       //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
  //                       //                 .content_id +
  //                       //         '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img "><img id="imge' + template_list[k].content_id + '" loading="lazy" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
  //                       //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
  //                       //         '" alt="'+ templateName +'" data-isLoaded = "false">';
  //                       // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
  //                       //   if(template_list[k].template_name.length > 30){
  //                       //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div>';
  //                       //   }else{
  //                       //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title">'+ template_list[k].template_name +'</div></a></div>';
  //                       //   }

  //                       // }else{
  //                       //   tempStr += '<div class="crd-overlay"></div><button class="crd-ol-btn">Start from this</button></div></div>'
  //                       // }
  //       let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center card-ol-block '+class_name+'" style="margin-bottom:1rem;padding-left:10px;padding-right:10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +  template_list[k].catalog_id + '/' + template_list[k].content_id +  '" onclick="storeDetails(\'' +  template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id +  '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img template-img" style="display:block;width:100%;max-width:100%;height:'+card_height+'px;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid g-item" style="height:'+card_height+'px;border-radius: 0;border:0px;transition: all .3s ease .15s;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
  //                               '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
  //                               if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
  //                         if(template_list[k].template_name.length > 30){
  //                           tempStr += '<div class="crd-overlay '+overlay_class+' low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style=""><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></div></a></div>';
  //                         }else{
  //                           tempStr += '<div class="crd-overlay '+overlay_class+' low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button><div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
  //                         }
  //                       }else{
  //                         tempStr += '<div class="crd-overlay '+overlay_class+'" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></a></div>'
  //                       }

  //                       $('#card-item').append(
  //                         tempStr
  //                       );

  //                       var msnry = new Masonry( '.masonry', {
  //                         // horizontalOrder: true
  //                                   // gutter: 10
  //                       });
  //                       is_api_calling  = false;

  //                     } else if($(document).width() <= 768 && k<10){
  //                       // let tempStr = '<div class="col col-sm-12 p-0 text-center template-page-item"><div class="img_wrper temp-img prograssive_img "><a href="https://photoadking.com/app/#/" target="_blank" style="text-decoration:none;"><img loading="lazy"  id="imge' + template_list[k].content_id + '" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
  //                       //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
  //                       //         '" alt="' + templateName +
  //                       //         '" data-isLoaded = "false"></a></div>';
  //                       // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
  //                       //   tempStr += '<div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
  //                       // }else{
  //                       //   tempStr += '</div>';
  //                       // }
  //                       let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center g-item card-ol-block" style="margin-bottom:1rem;padding:0 10px;"><a href="https://photoadking.com/app/#/"  target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img" style="width:100%;max-width:100%;display: block;height:'+card_height+'px;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="border-radius: 0;border:0px;height:'+card_height+'px;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
  //                               '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
  //                               if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){

  //                           tempStr += '<div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></div></a></div>';
  //                         }else{
  //                         tempStr += '</div></div></a></div>'
  //                       }
  //                       $('#card-item').append(tempStr);

  //                       var msnry = new Masonry( '.masonry', {
  //                         // itemselector:'g-item'
  //                       });
  //                       is_api_calling  = false;
  //                     }
  //                     let img_id = '#imge' + template_list[k].content_id;
  //                     if( card_width >= template_list[k].width){
  //                       // card_height = template_list[k].height;
  //                       $(img_id).css('width', template_list[k].width + 'px');
  //                       // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
  //                     }else{
  //                       // if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
  //                       // {
  //                       //   // if(template_list[k].width < 100)
  //                       //   // {
  //                       //     let img_width = (card_height * template_list[k].width)/template_list[k].height;
  //                       //     $(img_id).css('width', img_width + 'px');
  //                       //     // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
  //                       //   // }
  //                       //   // else{
  //                       //   //   $(img_id).css('width', '100%');
  //                       //   //   $(img_id).parents('div.img_wrper').css('width', '100%');
  //                       //   // }
  //                       // }
  //                       //   else{
  //                           let img_width = (card_height * template_list[k].width)/template_list[k].height;
  //                           $(img_id).css('width', img_width + 'px');
  //                     // }
  //                     //   // }

  //                       // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
  //                     }
  //                     // card_width = card_width +2;
  //                     // card_height = card_height +2;
  //                     // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
  //                     // $(img_id).css('height', card_height + 'px');

  //                     // $(img_id).attr('height', card_height + 'px');
  //                     // $(img_id).attr('width', card_width + 'px');
  //                     // let wrapper_height= card_height+3;

  //                     // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');

  //                   }

  //                   $('.template_loader').attr('hidden',true);
  //                   // $('img').on('load', function () {
  //                   // if ($(this).attr('data-isLoaded') == 'false') {
  //                   //     $(this).attr('src', $(this).attr('data-src'));
  //                   //     $(this).attr('data-isLoaded', 'true');
  //                   // }
  //                   //     let img_id = '#imge' + k;
  //                   //     $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
  //                   // });
  //                   // }, 1500);
  //                   $('#card-item').css('min-height','100px');
  //                 });


  //   }
  function showNewImageTemplate(template_list, id = undefined) {
            let getImageSize = new Promise(function (resolve, reject) {
            if (template_list[0].sample_image) {
              var img = new Image();
              img.src = template_list[0].sample_image;
              img.onload = function () {
                if ($('#card-item').children().eq(1).hasClass('template-col-wrapper') == true) {
                  class_name = 'template-col-wrapper';
                  overlay_class = 'crd-overlay-div';
                  // $('#card-item').removeClass('template-wrapper');
                  // $('.card-ol-block').addClass('template-col-wrapper');
                  // card_width = temp_width;
                  // card_width = card_width - 2;
                  resolve();
                } else {
                  class_name = '';
                  overlay_class = '';
                  // $('.card-ol-block').removeClass('template-col-wrapper');
                  resolve();
                }

              };
            }else{
              // $('#card-item').css('min-height','50px');
            }
          });
          // if(template_list[0].sample_image){
          //       getImageSize(template_list[0].sample_image);
          // }
          // setTimeout(() => {
          getImageSize.then(
                  function (result) {
                    if(id){
                      // let arrowIcon = $("#"+id+" svg.sec-first-button-svg");
                      // let loaderSVG = $("#"+id+" svg.btn-loader-svg");
                      // loaderSVG.hide();
                      // arrowIcon.show();
                  //     document.getElementById('templateTop').scrollIntoView({
                  //   behavior: 'smooth',
                  //   block: 'start',
                  //   inline: 'start'
                  // });
                    }
                    // $('#card-item').empty();
                    for (var k = 0; k < template_list.length; k++) {
                      let card_height = '';
                      let ratio = template_list[k].height / template_list[k].width;
                      if( card_width >= template_list[k].width){
                        card_height = template_list[k].height;
                      }
                      else
                      {card_height = (card_width * ratio) < 551 ? (Math.floor(card_width * ratio) >= 40 ? Math.floor(card_width * ratio) :40 ): 550;}
                      let templateName = template_list[k].template_name?template_list[k].template_name:"";
                      if ($(document).width() > 768) {
                        // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block template-page-item"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
                        //         template_list[k].catalog_id + '/' + template_list[k].content_id +
                        //         '" onclick="storeDetails(\'' +
                        //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                        //                 .content_id +
                        //         '\','+ template_list[0].content_type +')" target="_blank" style="text-decoration:none;"><div class="img_wrper temp-img prograssive_img "><img id="imge' + template_list[k].content_id + '" loading="lazy" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="'+ templateName +'" data-isLoaded = "false">';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   if(template_list[k].template_name.length > 30){
                        //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title"><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></a></div>';
                        //   }else{
                        //     tempStr += '<div class="crd-overlay low-height"></div><button class="crd-ol-btn">Start from this</button></div><div class="template-name-title">'+ template_list[k].template_name +'</div></a></div>';
                        //   }

                        // }else{
                        //   tempStr += '<div class="crd-overlay"></div><button class="crd-ol-btn">Start from this</button></div></div>'
                        // }
                        // onclick="viewpreview()"
        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center card-ol-block '+class_name+'" style="margin-bottom:1rem;padding-left:10px;padding-right:10px;"><a  href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/' + template_list[k].sub_category_id  + '/' +
                                template_list[k].catalog_id + '/' + template_list[k].content_id +
                                '" target="_blank" style="text-decoration:none;" onclick="storeDetails(\'' + template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k].content_id + '\','+ template_list[k].content_type +')"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img template-img" style="display:block;width:100%;max-width:100%;height:'+card_height+'px;min-height:100px;"><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid g-item" style="height:'+card_height+'px;border-radius: 0;border:0px;transition: all .3s ease .15s;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
                                '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
                                if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                          if(template_list[k].template_name.length > 30){
                            tempStr += '<div class="crd-overlay '+overlay_class+' low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn c-explore-template" style="outline: none;" onclick="viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')"><svg class="mr-2" width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.99604 14.1611C9.80772 14.1611 9.61898 14.1561 9.4294 14.1461C5.39752 13.9311 1.72479 11.4137 0.0732114 7.73225C-0.115112 7.31227 0.0723779 6.81938 0.492357 6.63064C0.912752 6.44274 1.40523 6.62981 1.59397 7.0502C2.99265 10.1675 6.10291 12.2999 9.51815 12.4816C13.2871 12.6837 16.8582 10.4992 18.406 7.0502C18.5944 6.62981 19.0877 6.44274 19.5076 6.63064C19.9276 6.81897 20.1151 7.31227 19.9268 7.73225C18.1765 11.6325 14.235 14.1611 9.99604 14.1611Z" fill="#0069FF"/><path d="M0.833173 8.22373C0.719012 8.22373 0.603184 8.19998 0.492357 8.1504C0.0723779 7.96208 -0.115112 7.46877 0.0732114 7.04879C1.90104 2.97566 6.12082 0.399961 10.5702 0.634949C14.6021 0.849938 18.2748 3.36731 19.9264 7.04879C20.1147 7.46877 19.9272 7.96166 19.5072 8.1504C19.0877 8.33872 18.5944 8.15123 18.4056 7.73084C17.0069 4.6135 13.8967 2.48111 10.4814 2.29945C6.71412 2.09821 3.14139 4.28185 1.59355 7.73084C1.45522 8.04041 1.15149 8.22373 0.833173 8.22373Z" fill="#0069FF"/><path d="M9.99984 10.825C8.10618 10.825 6.56543 9.28468 6.56543 7.39102C6.56543 5.49737 8.10618 3.95703 9.99984 3.95703C11.8935 3.95703 13.4342 5.49737 13.4342 7.39102C13.4342 9.28468 11.8935 10.825 9.99984 10.825ZM9.99984 5.62361C9.04155 5.62361 8.23201 6.43274 8.23201 7.39102C8.23201 8.34931 9.04155 9.15843 9.99984 9.15843C10.9581 9.15843 11.7677 8.34931 11.7677 7.39102C11.7677 6.43274 10.9581 5.62361 9.99984 5.62361Z" fill="#0069FF"/></svg>Preview</button><button class="crd-ol-btn c-check-btn" style="outline: none;"><svg width="22" class="mr-2" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.4367 18.6719H1.94103C1.5732 18.6719 1.28534 18.9757 1.28534 19.3276C1.28534 19.6794 1.58919 19.9832 1.94103 19.9832H19.4526C19.8205 19.9832 20.1083 19.6794 20.1083 19.3276C20.1083 18.9757 19.8045 18.6719 19.4367 18.6719Z" fill="white"/><path d="M1.28522 12.8667L1.26923 15.9852C1.26923 16.1611 1.3332 16.337 1.46113 16.4649C1.58907 16.5929 1.749 16.6568 1.92491 16.6568L5.02743 16.6408C5.20334 16.6408 5.36327 16.5769 5.49121 16.4489L16.2061 5.73407C16.462 5.47819 16.462 5.06239 16.2061 4.79052L13.1355 1.688C12.8797 1.43212 12.4639 1.43212 12.192 1.688L10.049 3.84697L1.47713 12.4029C1.36518 12.5308 1.28522 12.6907 1.28522 12.8667ZM12.6718 3.09533L14.8307 5.2543L13.6153 6.46971L11.4564 4.31075L12.6718 3.09533ZM2.61258 13.1545L10.5128 5.2543L12.6718 7.41326L4.77155 15.2975L2.59659 15.3135L2.61258 13.1545Z" fill="white"/></svg>Edit</button><div class="template-name-title" style=""><span class="apply-marquee">'+ template_list[k].template_name +'</span></div></div></a></div>';
                          }else{
                            tempStr += '<div class="crd-overlay '+overlay_class+' low-height" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn c-explore-template" style="outline: none;" onclick="viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')"><svg class="mr-2" width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.99604 14.1611C9.80772 14.1611 9.61898 14.1561 9.4294 14.1461C5.39752 13.9311 1.72479 11.4137 0.0732114 7.73225C-0.115112 7.31227 0.0723779 6.81938 0.492357 6.63064C0.912752 6.44274 1.40523 6.62981 1.59397 7.0502C2.99265 10.1675 6.10291 12.2999 9.51815 12.4816C13.2871 12.6837 16.8582 10.4992 18.406 7.0502C18.5944 6.62981 19.0877 6.44274 19.5076 6.63064C19.9276 6.81897 20.1151 7.31227 19.9268 7.73225C18.1765 11.6325 14.235 14.1611 9.99604 14.1611Z" fill="#0069FF"/><path d="M0.833173 8.22373C0.719012 8.22373 0.603184 8.19998 0.492357 8.1504C0.0723779 7.96208 -0.115112 7.46877 0.0732114 7.04879C1.90104 2.97566 6.12082 0.399961 10.5702 0.634949C14.6021 0.849938 18.2748 3.36731 19.9264 7.04879C20.1147 7.46877 19.9272 7.96166 19.5072 8.1504C19.0877 8.33872 18.5944 8.15123 18.4056 7.73084C17.0069 4.6135 13.8967 2.48111 10.4814 2.29945C6.71412 2.09821 3.14139 4.28185 1.59355 7.73084C1.45522 8.04041 1.15149 8.22373 0.833173 8.22373Z" fill="#0069FF"/><path d="M9.99984 10.825C8.10618 10.825 6.56543 9.28468 6.56543 7.39102C6.56543 5.49737 8.10618 3.95703 9.99984 3.95703C11.8935 3.95703 13.4342 5.49737 13.4342 7.39102C13.4342 9.28468 11.8935 10.825 9.99984 10.825ZM9.99984 5.62361C9.04155 5.62361 8.23201 6.43274 8.23201 7.39102C8.23201 8.34931 9.04155 9.15843 9.99984 9.15843C10.9581 9.15843 11.7677 8.34931 11.7677 7.39102C11.7677 6.43274 10.9581 5.62361 9.99984 5.62361Z" fill="#0069FF"/></svg>Preview</button><button class="crd-ol-btn c-check-btn" style="outline: none;"><svg width="22" class="mr-2" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.4367 18.6719H1.94103C1.5732 18.6719 1.28534 18.9757 1.28534 19.3276C1.28534 19.6794 1.58919 19.9832 1.94103 19.9832H19.4526C19.8205 19.9832 20.1083 19.6794 20.1083 19.3276C20.1083 18.9757 19.8045 18.6719 19.4367 18.6719Z" fill="white"/><path d="M1.28522 12.8667L1.26923 15.9852C1.26923 16.1611 1.3332 16.337 1.46113 16.4649C1.58907 16.5929 1.749 16.6568 1.92491 16.6568L5.02743 16.6408C5.20334 16.6408 5.36327 16.5769 5.49121 16.4489L16.2061 5.73407C16.462 5.47819 16.462 5.06239 16.2061 4.79052L13.1355 1.688C12.8797 1.43212 12.4639 1.43212 12.192 1.688L10.049 3.84697L1.47713 12.4029C1.36518 12.5308 1.28522 12.6907 1.28522 12.8667ZM12.6718 3.09533L14.8307 5.2543L13.6153 6.46971L11.4564 4.31075L12.6718 3.09533ZM2.61258 13.1545L10.5128 5.2543L12.6718 7.41326L4.77155 15.2975L2.59659 15.3135L2.61258 13.1545Z" fill="white"/></svg>Edit</button><div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
                          }
                        }else{
                          tempStr += '<div class="crd-overlay '+overlay_class+'" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></a></div>'
                        }

                        $('#card-item').append(
                          tempStr
                        );

                        var msnry = new Masonry( '.masonry', {
                          // horizontalOrder: true
                                    // gutter: 10
                        });
                        is_api_calling  = false;

                      } else if($(document).width() <= 768 && k<10){
                        // let tempStr = '<div class="col col-sm-12 p-0 text-center template-page-item"><div class="img_wrper temp-img prograssive_img "><a href="https://photoadking.com/app/#/" target="_blank" style="text-decoration:none;"><img loading="lazy"  id="imge' + template_list[k].content_id + '" onload="removeShimmer(imge' +template_list[k].content_id + ')" draggable="false" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName +
                        //         '" data-isLoaded = "false"></a></div>';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   tempStr += '<div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
                        // }else{
                        //   tempStr += '</div>';
                        // }
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center g-item card-ol-block" style="margin-bottom:1rem;padding:0 10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/"  target="_blank" style="text-decoration:none;"><div style="overflow: hidden;border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;"><div class="img_wrper temp-img prograssive_img" style="width:100%;max-width:100%;display: block;height:'+card_height+'px;"><div onclick="event.stopPropagation(); viewpreview(event,\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')""><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="top: 10px;right: 20px;width: auto;border-radius:0px;height:30px !important;width:30px !important;"></div><img src="'+ template_list[k].webp_thumbnail +'" data-src="'+ template_list[k].webp_thumbnail +'" id="imge' + template_list[k].content_id + '" loading="lazy" class="img-fluid" style="border-radius: 0;border:0px;height:'+card_height+'px;" onload="removeShimmer(imge' +template_list[k].content_id + ')" onerror="this.src=' + "'" + template_list[k].sample_image + "'" +
                                '"" draggable="false" alt="'+ templateName +'" data-isLoaded = "false"></div>';
                                if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                            tempStr += '<div class="template-name-title" style="">'+ template_list[k].template_name +'</div></div></a></div>';
                        }else{
                          tempStr += '<div class="crd-overlay" style="margin: auto 10px;width: calc(100% - 20px);height:calc(100% - 32px);"></div><button class="crd-ol-btn">Start from this</button></div></div></div>'
                        }
                        $('#card-item').append(tempStr);

                        var msnry = new Masonry( '.masonry', {
                          // itemselector:'g-item'
                        });
                        is_api_calling  = false;
                      }
                      let img_id = '#imge' + template_list[k].content_id;
                      if( card_width >= template_list[k].width){
                        // card_height = template_list[k].height;
                        $(img_id).css('width', template_list[k].width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
                      }else{
                        // if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                        // {
                        //   // if(template_list[k].width < 100)
                        //   // {
                        //     let img_width = (card_height * template_list[k].width)/template_list[k].height;
                        //     $(img_id).css('width', img_width + 'px');
                        //     // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                        //   // }
                        //   // else{
                        //   //   $(img_id).css('width', '100%');
                        //   //   $(img_id).parents('div.img_wrper').css('width', '100%');
                        //   // }
                        // }
                        //   else{
                            let img_width = (card_height * template_list[k].width)/template_list[k].height;
                            $(img_id).css('width', img_width + 'px');
                      // }
                      //   // }

                        // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
                      }
                      // card_width = card_width +2;
                      // card_height = card_height +2;
                      // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                      // $(img_id).css('height', card_height + 'px');

                      // $(img_id).attr('height', card_height + 'px');
                      // $(img_id).attr('width', card_width + 'px');
                      // let wrapper_height= card_height+3;

                      // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');

                    }

                    $('.template_loader').attr('hidden',true);
                    // $('img').on('load', function () {
                    // if ($(this).attr('data-isLoaded') == 'false') {
                    //     $(this).attr('src', $(this).attr('data-src'));
                    //     $(this).attr('data-isLoaded', 'true');
                    // }
                    //     let img_id = '#imge' + k;
                    //     $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
                    // });
                    // }, 1500);
                    $('#card-item').css('min-height','100px');
                  });


    }

    function imgload(){
        var msnry = new Masonry( '.masonry', {
          // horizontalOrder: true
          // stagger: 100,
          // itemselector:'g-item'
});
}
function myFunction(img,id){
    $('#img'+id).src = 'http://192.168.0.108/photoadking/image_bucket/thumbnail/616808fa5699a_json_image_1634207994.jpg';
    var $container = $(".masonry");
        var msnry = new Masonry( '.masonry', {
});
      }

    function showNewVideoTemplate(template_list, id = undefined) {

          $('#nav-images').removeClass("active");
          $("#nav-videos").addClass('active');
          let videoCatList = $('.sub_cat_list_video .categorylist');

          let category = $(videoCatList[0]).children('a');

          $("#nav-videos-tab").addClass(" active");
          if($("#nav-images-tab").hasClass("active")){
            $("#nav-images-tab").removeClass('active');
          }
          let col_class_name, overlay_class;
          $(videoCatList[0]).addClass(" active");
          $(videoCatList[0]).addClass(" activeVideoCategory");
          let getImageSize = new Promise(function (resolve, reject) {
            if (template_list[0].sample_image) {
              var img = new Image();
              img.src = template_list[0].sample_image;
              img.onload = function () {
                if ($('#card-item').children().eq(1).hasClass('template-col-wrapper') == true) {
                  // $('#card-item').removeClass('template-wrapper');
                  // $('#card-item').addClass('template-wrapper-1');
                  col_class_name = 'template-col-wrapper';
                  overlay_class = 'crd-overlay-div';
                  // card_width = temp_width;
                  resolve();
                } else {
                  col_class_name = '';
                  overlay_class = '';
                  resolve();
                }
              };
            }else{
              $('#card-item').css('min-height','50px');
            }
          });
          let editroUrl;
          if (template_list[0].content_type == 9) {
            editroUrl = "https://photoadking.com/app/#/video-editor/";
          } else if (template_list[0].content_type == 10) {
            editroUrl = "https://photoadking.com/app/#/intro-editor/";
          }

          // if(template_list[0].sample_image){
          //       getImageSize(template_list[0].sample_image);
          //     }
          // setTimeout(() => {
          getImageSize.then(
                  function (result) {
                  //   if(id){
                  //     let arrowIcon = $("#"+id+" svg.sec-first-button-svg");
                  //     let loaderSVG = $("#"+id+" svg.btn-loader-svg");
                  //     loaderSVG.hide();
                  //     arrowIcon.show();
                  //     document.getElementById('templateTop').scrollIntoView({
                  //   behavior: 'smooth',
                  //   block: 'start',
                  //   inline: 'start'
                  // });
                  //   }
                    // $('#card-item').empty();
                    for (var k = 0; k < template_list.length; k++) {
                      let ratio = template_list[k].height / template_list[k].width;
                      let card_height = '';
                      if(card_width >= template_list[k].width){
                        card_height = template_list[k].height;
                      }
                      else{
                        card_height = (card_width * ratio) < 551 ? Math.floor(card_width * ratio) : 550;
                      }

                      let temp_param =  "'" + editroUrl+"','" + template_list[k].sub_category_id + "','" + template_list[k].catalog_id + "','" + template_list[k].content_id + "','" + template_list[k].content_id +"','" + template_list[k].height +"','" + template_list[k].width +"'";
                      let templateName = template_list[k].template_name?template_list[k].template_name:"";
                      let _id = "'"+ template_list[k].content_id + "'";
                      if ($(document).width() > 768) {
                        let isTemplateNameFound = template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != "";
                        let className = isTemplateNameFound == true?'template-name-found':'';
                        let imageClassName = isTemplateNameFound == true?'marquee-image':'';
                        // let tempStr = '<div class="col col-sm-12 p-0 text-center card-ol-block video-card-item-1 mx-auto template-page-item '+ className +'" onmouseenter="showVideo(' +_id+ ')" onmouseleave="hideVideo(' +_id +')" ><div onclick="playVideoInModel(' + temp_param +')" class="img_wrper temp-img prograssive_img '+imageClassName+'"><img loading="lazy" onload="removeShimmer(i' +template_list[k].content_id + ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                        //         template_list[k].content_file +
                        //         '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" ></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                        //         + '"><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" onclick="storeDetails(\'' +
                        //         template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                        //                 .content_id +
                        //         '\','+ template_list[0].content_type +')" href="' + editroUrl +
                        //         template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
                        //         '" target="_blank">';
                        // if(isTemplateNameFound == true){
                        //   tempStr += '<div class="edit-video-button mx-auto marque-video"  id = "editButton' + template_list[k].content_id + '"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic"><span>EDIT VIDEO</span></div></a><div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
                        // }else{
                        //   tempStr += '<div class="edit-video-button mx-auto"  id = "editButton' + template_list[k].content_id + '"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic"><span>EDIT VIDEO</span></div></a></div>';
                        // }
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-page-item g-item card-ol-block '+ className +' '+ col_class_name +'"  onmouseenter="showVideo(' +_id+ ')" onmouseleave="hideVideo(' +_id +')" style="margin-bottom:1rem !important;padding:0 10px;"><div style="border: 1px solid #C4C4C4 !important;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;"><div onclick="playVideoInModel(' + temp_param +')" class="img_wrper temp-img cursor-pointer prograssive_img '+imageClassName+'"  style="max-width:100%;display:block;width:100%;height:'+card_height+'px"><img loading="lazy" style="height:'+card_height+'px;border-radius: 0px !important;border:0px;transition:.1s ease;max-width:100%;" onload="removeShimmer(i' +template_list[k].content_id + ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                                template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                '" alt="' + templateName + '" data-isLoaded="false" ><video class="template-video"  style="" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                                template_list[k].content_file +
                                '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" style="top: 8px;left: 20px;"></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="border-radius:0;"></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                                + '" style="width:93%;"><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" href="' + editroUrl +
                                template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
                                '" target="_blank" onclick="storeDetails(\'' +
                                template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                                        .content_id +
                                '\','+ template_list[0].content_type +')">';
                                 if(isTemplateNameFound == true){
                          tempStr += '<div class="edit-video-button mx-auto marque-video '+overlay_class +'"  id = "editButton' + template_list[k].content_id+ '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></a><div class="template-name-title" style="height:30px;">'+ template_list[k].template_name +'</div></div></div>';
                        }else{
                          tempStr += '<div class="edit-video-button mx-auto '+overlay_class +'"  id = "editButton' + template_list[k].content_id+ '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></a></div></div>';
                        }
                        $('#card-item').append(
                          tempStr
                        );
                        var msnry = new Masonry( '.masonry', {
                          // horizontalOrder: true
                                    // stagger: 100,
                        });
                        is_api_calling = false;
                        $('.template_loader').attr('hidden',true);


                      } else if($(document).width() <= 768 && k<10){
                        let isTemplateNameFound = template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != "";
                        let className = isTemplateNameFound == true?'template-name-found':'';
                        let imageClassName = isTemplateNameFound == true?'marquee-image':'';

                        // let tempStr =  '<div class="col col-sm-12 p-0 text-center card-ol-block video-card-item-1 mx-auto template-page-item pb-0 '+className+'" onmouseenter="showVideo('+_id + ')" onmouseleave="hideVideo('+ _id + ')" ><div class="img_wrper temp-img prograssive_img '+imageClassName+'"><img loading="lazy" onload="removeShimmer(i' +k + ')" draggable="false" id ="i' + k + '" src="' +
                        //         template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                        //         '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                        //         template_list[k].content_file +
                        //         '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" ></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                        //         + '"><div class="custom-seekbar"  id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" target="_blank" href="https://photoadking.com/app/#/">';
                        // if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                        //   tempStr += '<div class="edit-video-button mx-auto marque-video" id = "editButton' + template_list[k].content_id + '"><span>EDIT VIDEO</span></div></a><div class="template-name-title">'+ template_list[k].template_name +'</div></div>';
                        // }else{
                        //   tempStr += '<div class="edit-video-button mx-auto" id = "editButton' + template_list[k].content_id + '"><span>EDIT VIDEO</span></div></a></div>';
                        // }
                        let tempStr = '<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center template-page-item g-item card-ol-block '+ className +' '+ col_class_name +'" style="margin-bottom:1rem !important;padding:0 10px;"><a href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/"  target="_blank" style="text-decoration:none;"><div style="border: 1px solid #C4C4C4;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;"><div class="img_wrper temp-img prograssive_img '+imageClassName+'"  style="height:'+card_height+'px;display:block;width:100%;max-width:100%;"><img loading="lazy" style="max-width:100%;border-radius: 0px !important;border: 0px;height:'+card_height+'px;" onload="removeShimmer(i' +template_list[k].content_id + ')" draggable="false" id ="i' + template_list[k].content_id + '" src="' +
                                template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                '" alt="' + templateName + '" data-isLoaded="false" ><video class="template-video"  style="" id = "v' + template_list[k].content_id + '" loop muted><source draggable="false" type="video/mp4" src="' +
                                template_list[k].content_file +
                                '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldr' + template_list[k].content_id + '" style="top: 8px;left: 20px;"></div><div id = "playButton' + template_list[k].content_id + '" class= "play-btn-ic" onclick="event.stopPropagation(); playVideoInModel(' + temp_param +')"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="top: 10px;right: 20px;height:30px !important;width:30px !important;border-radius:0;"></div><div class= "seekbar-container" id="seekbar' + template_list[k].content_id
                                + '" style="width:93%;><div class="custom-seekbar" id="custom-seekbar' + template_list[k].content_id + '"><span id="cs' + template_list[k].content_id + '"></span></div></div>';
                                if(template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != ""){
                                  tempStr += '<div class="edit-video-button mx-auto marque-video '+ overlay_class+'" id = "editButton' + template_list[k].content_id + '" style="margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div><div class="template-name-title" style="height:27px;">'+ template_list[k].template_name +'</div></div></a></div>';
                        }else{
                          tempStr += '<div class="edit-video-button mx-auto '+ overlay_class+'" id = "editButton' + template_list[k].content_id + '" style="border-radius: 0 0 8px 8px;border: 1px solid #C4C4C4 !important;border-top:0px !important;margin: auto 10px;width: calc(100% - 21px);height:32px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px"><span>EDIT VIDEO</span></div></div></a></div>';
                        }
                        $('#card-item').append(
                          tempStr
                        );
                        var msnry = new Masonry( '.masonry', {
                                    // stagger: 100,
                        });

                        $('.template_loader').attr('hidden',true);
                        is_api_calling = false;
                      }
                      let img_id = '#i' + template_list[k].content_id;
                      // if(card_width >= template_list[k].width){
                        // card_height = template_list[k].height;
                        // if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                        // {
                        //   console.log("tru width  < 1024");
                        //   if(template_list[k].width < 100)
                        //   {
                        //     $(img_id).css('width',  template_list[k].width + 'px');
                        //     $(img_id).parents('div.img_wrper').css('width',  template_list[k].width + 'px');
                        //   }
                        //   else{
                        //     $(img_id).css('width', '100%');
                        //     $(img_id).parents('div.img_wrper').css('width', '100%');
                        //   }
                        // }
                          // else{
                        $(img_id).css('width', template_list[k].width + 'px');
                      // }
                        // $(img_id).css('width', template_list[k].width + 'px');
                        // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
                      // }else{
                      //   if(window.innerWidth <= 1024  || window.innerWidth >= 1440)
                      //   {
                      //     if(template_list[k].width < 100)
                      //     {
                      //       $(img_id).css('width', card_width + 'px');
                      //       $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                      //     }
                      //     else{
                      //       $(img_id).css('width', '100%');
                      //       $(img_id).parents('div.img_wrper').css('width', '100%');
                      //     }
                      //   }
                      //     else{
                      //   $(img_id).css('width', card_width + 'px');
                      //   $(img_id).parents('div.img_wrper').css('width', card_width + 'px');

                      // }
                      //   // $(img_id).css('width', card_width + 'px');
                      //   // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
                      // }
                      // $(img_id).css('height', card_height + 'px');
                      // $(img_id).css('width', card_width + 'px');
                      // $(img_id).attr('height', card_height + 'px');
                      // $(img_id).attr('width', card_width + 'px');
                      // let wrapper_height= card_height+3;
                      // $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                      // $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                    }

                    // $('img').on('load', function () {
                    //   let img_id = '#i' + k;
                    //   $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
                    // if ($(this).attr('data-isLoaded') == 'false') {
                    //     $(this).attr('src', $(this).attr('data-src'));
                    //     $(this).attr('data-isLoaded', 'true');
                    // }

                    setTimeout(function () {
                      $('.play-btn-ic').css('display', 'block');
                      $('#card-item').css('min-height','100px');
                      // $('.template_loader').attr('hidden',true);
                    }, 5000);
                    // });
                    // }, 1500);
                  });

    }

  function storeDetails(sub_cat_id, catalog_id, template_id, content_type) {
    localStorage.setItem("sub_cat_id", sub_cat_id);
    localStorage.setItem("catalog_id", catalog_id);
    localStorage.setItem("template_id", template_id);
    localStorage.setItem("is_l_re","true");
    if(content_type == 4){
      localStorage.setItem("re_url",'./app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
    }else if(content_type == 9){
      localStorage.setItem("re_url",'./app/#/video-editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
    }else{
      localStorage.setItem("re_url",'./app/#/intro-editor/' + sub_cat_id + '/' + catalog_id + '/' + template_id);
    }
  }

  function redirectCustomize(sub_cat_id, catalog_id, content_id,content_type){
    let windowOrigin =  window.location.origin
    if($(document).width() <= 768){
      window.open(windowOrigin + '/app/#');
    }
    else{
      storeDetails(sub_cat_id, catalog_id, content_id,content_type);
      if(content_type == 4){
        window.open(windowOrigin + '/app/#/editor/' + sub_cat_id + '/' + catalog_id + '/' + content_id, '_blank');
      }
      else if(content_type == 9){
        window.open(windowOrigin + '/app/#/video-editor/' + sub_cat_id + '/' + catalog_id + '/' + content_id, '_blank');
      }
      else if(content_type == 10){
        window.open(windowOrigin + '/app/#/intro-editor/' + sub_cat_id + '/' + catalog_id + '/' + content_id, '_blank');
      }
    }
  }

  function storeDetailsUrl(sub_cat_id, catalog_id, template_id, url) {
    localStorage.setItem("sub_cat_id", sub_cat_id);
    localStorage.setItem("catalog_id", catalog_id);
    localStorage.setItem("template_id", template_id);
    localStorage.setItem("is_l_re","true");
    localStorage.setItem("re_url",url);
  }

  var clearTimeOut;

  function showVideo(i) {
    var str = "#v" + i;
    var editButton = '#editButton' + i;
    $(editButton).show();
    var playButton = '#playButton' + i;
    var ldrid = "#ldr" + i;
    $(ldrid).css("display", "block");

    var st = "v" + i;
    var s = "#i" + i;
    var customSeekbar = '#custom-seekbar' + i;
    var CS = '#cs' + i;
    var seekbarContainer = '#seekbar' + i;
    var video = document.getElementById(st);

    clearTimeOut = setTimeout(() => {
      // $(str)[0].preload = "auto";
      if ($(str)[0] && $(str)[0].readyState === 4) {
        $(str).css("display", "block");
        $(s).hide();
        $(str)[0].play();
        $(ldrid).css("display", "none");
        $(seekbarContainer).show();
        $(customSeekbar).show();
        $(CS).show();
      } else {
        $(str)[0].onloadeddata =  function () {
          if ($(str)[0] && $(str)[0].readyState === 4) {
            $(str).css("display", "block");
            $(s).hide();
            $(str)[0].play();
            $(ldrid).css("display", "none");
            $(seekbarContainer).show();
            $(customSeekbar).show();
            $(CS).show();
          }
        }
      }
      $(str)[0].play();
      video.ontimeupdate = function () {
        var percentage = (video.currentTime / video.duration) * 100;
        $(CS).css("width", percentage + "%");
      };
    }, 2000);


  }

  function hideVideo(i) {
    var str = "#v" + i;
    var s = "#i" + i;
    var customSeekbar = '#custom-seekbar' + i;
    var CS = '#cs' + i;
    var seekbarContainer = '#seekbar' + i;
    var editButton = '#editButton' + i;
    var playButton = '#playButton' + i;
    var ldrid = "#ldr" + i;
    $(ldrid).css("display", "none");
    $(editButton).hide();
    if (this.clearTimeOut) {
      clearTimeout(this.clearTimeOut);
    }
    $(playButton).hide();
    $(str).hide();
    $(seekbarContainer).hide();
    $(customSeekbar).hide();
    $(playButton).show();
    $(CS).hide();
    $(s).show();
    $(str)[0].pause();
  }


  function closeDialog() {
    $('#video-modal-container').empty();
    $('#my_modal').modal('hide');
  }

  $('#my_modal').on('hidden.bs.modal', function () {
    $('#video-modal-container').empty();
  });

  function playVideoInModel(editroUrl ,sub_cat_id, catalog_id, template_id, i,selectedVideoHeight,selectedVideoWidth) {
    event.stopPropagation();
    event.preventDefault();
    event.stopImmediatePropagation();
  //   console.log("playVideoInModel")
  let templateUrl = editroUrl + sub_cat_id + '/'+catalog_id+ '/'+template_id;
  let onClickUrl = `storeDetailsUrl('${sub_cat_id}','${catalog_id}', '${template_id}','${templateUrl}')`;
    var str = "#v" + i;
    // $('#dialog-edit-btn').attr("href",templateUrl);
    // $('#dialog-edit-btn').attr("target","_blank");
    // $('#dialog-edit-btn').attr("onclick",onClickUrl);
    // $(str).find('source').each(function () {
    //   var video = $('<video />', {
    //     id: 'video',
    //     src: $(this).attr("src"),
    //     type: 'video/mp4',
    //     preload: "auto",
    //     autoplay: true,
    //     loop: true,
    //     class: "video-player"
    //   });
    //   video.appendTo($('#video-modal-container'));
    //   var customSeekbar = '#custom-seekbar-mdl';
    //   var CSmdl = '#cs-mdl';
    //   let endtime = '#end_time'
    //   // var seekbarContainer = '#seekbar-mdl';
    //   setTimeout(() => {
    //     if ($(video)[0].readyState === 4) {
    //       $(video).css("display", "block");
    //       $(video)[0].play();
    //       // $(seekbarContainer).show();
    //       $(customSeekbar).show();
    //       $(CSmdl).show();
    //     } else {
    //       $(video)[0].onloadeddata =  function () {
    //         if ($(video)[0] && $(video)[0].readyState === 4) {
    //           $(video).show();
    //           $(video)[0].play();
    //           // $(seekbarContainer).show();
    //           $(customSeekbar).show();
    //           $(CSmdl).show();
    //         }
    //       }
    //     }
    //     $(video)[0].ontimeupdate = function () {
    //       let time = '0' + (($(video)[0].duration) / 100).toFixed(2)
    //     $(endtime).text(time);
    //       var percentage = ($(video)[0].currentTime / $(video)[0].duration) * 100;
    //       $(CSmdl).css("width", percentage + "%");
    //     };
    //   }, 100);
    // });
    $(str).find('source').each(function () {
    var video = $('<video />', {
        id: 'video',
        src: $(this).attr("src"),
        type: 'video/mp4',
        autoplay: true,
        playsinline: true,
        muted: true,
        loop: true,
        class: "video-player videowidthadd video-border-line"
      });
      video.appendTo($('#video-modal-container2'));
      var customSeekbar = '#custom-seekbar-mdl';
      var CSmdl = '#cs-mdl';
      let endtime = '#end_time'
      var seekbarContainer = '#seekbar-mdl';
      setTimeout(() => {
        if ($(video)[0].readyState === 4) {
          $(video).css("display", "block");
          $(video)[0].muted = true;
          $(video)[0].play();
          $(seekbarContainer).show();
          $(customSeekbar).show();
          $(CSmdl).show();
        } else {
          $(video)[0].onloadeddata =  function () {
            if ($(video)[0] && $(video)[0].readyState === 4) {
              $(video).show();
              $(video)[0].muted = true;
              $(video)[0].play();
              $(seekbarContainer).show();
              $(customSeekbar).show();
              $(CSmdl).show();
            }
          }
        }
        $(video)[0].ontimeupdate = function () {
          // console.log("onupdate")
          let time = '0' + (($(video)[0].duration) / 100).toFixed(2);
          // console.log(time,'time')
        $("#end_time").text(time);
          var percentage = ($(video)[0].currentTime / $(video)[0].duration) * 100;
          // console.log(percentage,'percentage')
          $(CSmdl).css("width", percentage + "%");
        };
      }, 100);
    // });
    });
    // $('#videoprevtemp').modal('show');
    // $('#my_modal').modal('show');
    let deviceWidth = window.innerWidth;
      if(deviceWidth > 600){
        $("#videoprevtemp").addClass("dialogStyle");
        $("#videoprevtemp_parent").addClass("dialogStyle_parent");
      }
      else{
        $("#videoprevtemp").removeClass("dialogStyle");
        $("#videoprevtemp_parent").removeClass("dialogStyle_parent");
        $("#videoprevtemp").css({"top":"0px","width":"100%"});
        $("#videoprevtemp_parent").css({"top":"0px","width":"100%"});
      }
    $('#videoprevtemp').css('display','block');
    $('#videoprevtemp_parent').css('display','block');
    $('#videoprevtemp').parent().parent().addClass('template-overlay');
    getTemplates(sub_cat_id, template_id,selectedVideoHeight,selectedVideoWidth,"video");
    $("#activeVideoTemplateShimmer").css("display","block")
    $("#activeVideoTemplate").css("display","none")
    $(".new-template-box").css("width","100%");
    $('#videoprevtemp').animate({
     scrollTop: $("#videoprevtemp").offset().top - 40
    });
  }
  $('.sub_cat_list .categorylist svg.icon-svg').on("click", function (e) {
    let element = e.target;
    if (element) {
      c = $(element).parents('a').attr("data-category");
      if ($(window).width() >= 1151) {
        if ($(".sublist*[data-category='" + c + "']").hasClass("active")) {
          $(".sublist*[data-category='" + c + "']").animate({ "height": "0px" }, 50);
          $(".sublist*[data-category='" + c + "']").css("margin", "0px 0px");
          $(".sublist*[data-category='" + c + "']").removeClass(" active");

          if ($(element).hasClass("icon-svg")) {
            $(element).css("transform", "rotate(0deg)");
            $(element).css("transition", "0.3s");
          } else {
            $(element).parents('svg').css("transform", "rotate(0deg)");
            $(element).parents('svg').css("transition", "0.3s");
          }
          $(element).parents('li').removeClass(" activeSubCategory");
        } else {
          $(element).siblings().removeClass("active");
          $(element).addClass("active");
          $(".sublist*[data-category='" + c + "']").animate({ "height": $(".sublist*[data-category='" + c + "']")[0].scrollHeight }, 50);
          $(".sublist*[data-category='" + c + "']").css("margin", "5px 0px");
          $(".sublist*[data-category='" + c + "']").addClass(" active");
          if ($(element).hasClass("icon-svg")) {
            $(element).css("transform", "rotate(-180deg)");
            $(element).css("transition", "0.3s");
          } else {
            $(element).parents('svg').css("transform", "rotate(-180deg)");
            $(element).parents('svg').css("transition", "0.3s");
          }
          $(element).parents('li').addClass(" activeSubCategory");
        }
      }
    }
  });

  //this code is use to show and hide tags of popular tag categories and similar tag categories
  const explore_more = document.getElementById('explore_more');
  const similar_explore_more = document.getElementById('similar_explore_more');
  const buttonUp = document.getElementById('up');
  if(explore_more != null){
    explore_more.onclick = function () {
      if ($("#popular_tag_container").css("height") == $("#popular_tag_container")[0].scrollHeight + 'px') {
        $("#popular_tag_container").animate({ "height": "85px" }, 600);
        // $("#explore_more").children('svg').css("transform", "rotate(0deg)");
        // $("#explore_more").children('svg').css("transition", "0.5s");
        $("#explore_more").children('span').text("Explore More");
      } else {
        $("#popular_tag_container").animate({ "height": $("#popular_tag_container")[0].scrollHeight }, 600);
        // $("#explore_more").children('svg').css("transform", "rotate(-180deg)");
        // $("#explore_more").children('svg').css("transition", "0.5s");
        $("#explore_more").children('span').text("Explore Less");
      }
    };
  }


  $('.sub_cat_list .categorylist a.list').on("click", function (e) {
    let element = e.target;
    if (element) {
      c = element.getAttribute("data-category");
      let child = element.children[0];
      if ($(window).width() >= 1151) {
        if ($(element).attr("data-category")) {
          if ($(".sublist*[data-category='" + c + "']").css("height") == $(".sublist*[data-category='" + c + "']")[0].scrollHeight + 'px') {
            $(".sublist*[data-category='" + c + "']").animate({ "height": "0px" }, 50);
            $(".sublist*[data-category='" + c + "']").css("margin", "0px 0px");
            $(".sublist*[data-category='" + c + "']").removeClass(" active");

            $(element).children('svg').css("transform", "rotate(0deg)");
            $(element).children('svg').css("transition", "0.3s");
            $(element).parents('li').removeClass(" activeSubCategory");
          } else {
            $(element).parents('li').siblings().removeClass("active");
            $(element).parents('li').addClass("active");
            $(".sublist*[data-category='" + c + "']").animate({ "height": $(".sublist*[data-category='" + c + "']")[0].scrollHeight }, 50);
            $(".sublist*[data-category='" + c + "']").css("margin", "5px 0px");
            $(".sublist*[data-category='" + c + "']").addClass(" active");
            $(element).children('svg').css("transform", "rotate(-180deg)");
            $(element).children('svg').css("transition", "0.3s");
            $(element).parents('li').addClass(" activeSubCategory");
          }
        }


      } else if ($(window).width() <= 1150) {
        $(".sublist*[data-category='" + c + "']").css("margin", "0px 0px");
        if ($(".sublist*[data-category='" + c + "']").css("display") == "flex") {
          $(".sublist*[data-category='" + c + "']").css("display", "none");
          $(".sublist*[data-category='" + c + "']").removeClass("active");
          $(element).parents('li').removeClass(" activeSubCategory");
        } else {
          $(".sublist*[data-category='" + c + "']").css("display", "flex");
          // $(".sublist*[data-category='" + c + "']").css("transition", "all .3s ");
          $(".sublist*[data-category='" + c + "']").addClass(" active");
          $(element).parents('li').addClass(" activeSubCategory");
        }
      }
    }
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

  function removeShimmer(id,event) {

    if($(id).closest('div').hasClass('prograssive_img')){
      $(id).closest('div').removeClass("prograssive_img");
      // $(id).closest('div').css('height','auto');
      imgload();
      // $('#playButton' + id).css('display','block');
      // $(id).closest('div').css('display')

    }


  }
  let previouslyOpen;
  function openPopover() {
    ids = ['#template_lnk', '#home_lnk', '#goFeatures', '#learn_lnk', '#primium_lnk'];
    for (let id of ids) {
      $(id).popover({ trigger: "manual", animation: true, placement: 'bottom' }).on("mouseover mouseenter", function () {
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
      $(id).popover({ trigger: "manual", animation: true, placement: placement }).on("mouseover mouseenter", function () {
        var _this = this;
        if (previouslyOpenFooterCat != id) {
          $("[rel=popover]").not(_this).popover("destroy");
          $(".popover").remove();
        }
        $(this).popover("show");
        $(".popover").addClass("dark-c-bg");
        previouslyOpenFooterCat = id;
        $(id).children('svg').css('transform', 'rotate(270deg)');
        $(id).children('svg').css({ 'fill': '#ffffff' });
        $(".popover").on("mouseleave", function () {

          setTimeout(() => {
            if (!$(id).is(":hover")) {
              $(id).children('svg').css('transform', 'rotate(0deg)');
              $(id).children('svg').css({ 'fill': '#828EAA' });

              $(_this).popover('hide');
            }
          }, 200);

        });
      }).on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
          if (!$(".popover:hover").length) {
            $(id).children('svg').css('transform', 'rotate(0deg)');
            $(id).children('svg').css({ 'fill': '#828EAA' });
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
  function enterKeyPressed(event) {
      if (event.keyCode == 13) {
        let text = "";
        // if ($(window).width() < 768) {
        //   text = $("#searchInput1").val();
        // }else{
          text = $("#searchInput").val();
        // }
        if(text && text.trim()){
          nevigateToSearch(text);
        }
         return true;
      } else {
         return false;
      }
  }

  function onSearchClick() {
        let text = "";
        // if ($(window).width() < 768) {
        //   text = $("#searchInput1").val();
        // }else{
          text = $("#searchInput").val();
        // }
        if(text && text.trim()){
          nevigateToSearch(text);
        }
  }
  function nevigateToSearch(text){
    window.location.href = `{!! config('constant.ACTIVATION_LINK_PATH') !!}/search/?q=${text.trim().replace(/[%&]/g,"")}&sci=0&ct=0&p=1`
  }
function loadMore(isNext,id)
{
  let arrowIcon = $("#"+id+" svg.sec-first-button-svg");
    let loaderSVG = $("#"+id+" svg.btn-loader-svg");
    arrowIcon.hide();
    loaderSVG.show();
    checkPageAndApiCall(pageNumber, '');
}
  function changePage(isPrev,id){
    // let arrowIcon = $("#"+id+" svg.sec-first-button-svg");
    // let loaderSVG = $("#"+id+" svg.btn-loader-svg");
    // arrowIcon.hide();
    // loaderSVG.show();
    // if(isPrev == false){
    //   console.log(pageNumber,'pageNumber');
    //   if(pageNumber > 5){
    //     return;
    //   }
      pageNumber += 1;
      checkPageAndApiCall(pageNumber, id);
    // }
    // else{
    //   pageNumber -= 1;
    //   if(pageNumber <= 0){
    //     pageNumber = 0;
    //     // window.history.replaceState(null, null, window.location.pathname);
    //     // $(".btn-pegin-container").removeClass("btn-prev-only");
    //     // $(".btn-pegin-container").addClass("btn-next-only");
    //     if(content_type == 9){
    //       showNewVideoTemplate(allVideoTemplateList["page_0"].templates, id);
    //     }else{
    //       showNewImageTemplate(allImageTemplateList["page_0"].templates, id);
    //     }
    //   }else{
    //     checkPageAndApiCall(pageNumber, id);
    //   }
    // }
  }

  function checkPageAndApiCall(page_number, id = undefined){
    // addQueryParam(page_number);
    let keyName = "page_" + page_number;
    let listObj = content_type == 9?allVideoTemplateList:allImageTemplateList;
    let isPageFound = listObj[keyName]?true:false;
    if(isPageFound == false){
      showOtherTemplate(page_number, content_type, id);
    }else{
      if(content_type == 9){
        $("#card-item").empty();
        showNewVideoTemplate(allVideoTemplateList[keyName].templates, id);
      }else{
        $("#card-item").empty();
        showNewImageTemplate(allImageTemplateList[keyName].templates, id);
      }
      let lastKey = Object.keys(listObj).sort().pop();
      let lastPageNumber = Number(lastKey.charAt(lastKey.length - 1));
      if(page_number != lastPageNumber && listObj[keyName].is_next_page == true){
        $(".btn-pegin-container").removeClass("btn-prev-only");
        $(".btn-pegin-container").removeClass("btn-next-only");
      }
      if(page_number == lastPageNumber && listObj[keyName].is_next_page == false){
        $(".btn-pegin-container").removeClass("btn-next-only");
        $(".btn-pegin-container").addClass("btn-prev-only");
      }
    }
  }

  function addQueryParam(value) {
    const url = new URL(window.location.href);
    url.searchParams.set("p", value);
    window.history.pushState({}, '', url.toString());
  };

  function checkURLAndCallApi(){
    if(pageNumber != 0){
    checkPageAndApiCall(pageNumber);
    }

      // const queryParams = get_query();
      // if(queryParams[""] != 'undefined'){
      //   if(queryParams){
      //     pageNumber = Number(queryParams.p);
      //     if(pageNumber > 5){
      //       pageNumber = 5;
      //     }
      //     checkPageAndApiCall(pageNumber);
      //   }else{
      //     if(content_type == 9){
      //       showNewVideoTemplate(allVideoTemplateList["page_0"].templates);
      //     }else{
      //       showNewImageTemplate(allImageTemplateList["page_0"].templates);
      //     }
      //   }
      // }else{
      //   if(content_type == 9){
      //     showNewVideoTemplate(allVideoTemplateList["page_0"].templates);
      //   }else{
      //     showNewImageTemplate(allImageTemplateList["page_0"].templates);
      //   }
      // }
  }
  function get_query(){
    var url = location.search;
    var qs = url.substring(url.indexOf('?') + 1).split('&');
      for(var i = 0, result = {}; i < qs.length; i++){
        qs[i] = qs[i].split('=');
        result[qs[i][0]] = decodeURIComponent(qs[i][1]);
    }
    return result;
  }

  function makeRecommendation(new_word) {
    if (rLock == 1) {
      rLock = 0;
      if (buffered_word == new_word) {
        rLock = -1;
        rList = spellCheck.find_similar(new_word, lower_thresh)[0];
        // displayRecommendation(new_word);
      } else {
        buffered_word = "";
        wordTyped(search_str);
      }
    }
  }

  function wordTyped(value) {
    if (value.length <= 1) {
      rList = [];
      $("#autoCompleteDrop").hide();
      return;
    }
    WordList = spellCheck.get_dictionary();
    search_str = value;
    var word_list = WordList.split("|");
    spellCheck.set_valid_word_list(word_list, WordList);
    var new_word = value;
    if (new_word.length >= 2 && new_word != buffered_word) {
      buffered_word = new_word;
      buffered_word = new_word;
      if (rLock < 1) {
        rLock = 1;
        makeRecommendation(new_word);
      }
    }
    if (rList.length > 0) {
      $("#autoCompleteDrop").show();
      let rListText = "";
      rList.forEach(word => {
        rListText += `<div class="auto-complete-item d-flex" onclick="hideAutoCompleteDropDown('${word}')">
                    <div class="auto-complete-text">
                      <p class="mb-0">${word}</p>
                    </div>
                    <div class="auto-complete-icon">
                      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 492.004 492.004"
                        class="list-svg-style" xml:space="preserve" fill="#A9A9A9">
                        <g>
                          <g>
                            <path fill="#A9A9A9"
                              d="M484.14,226.886L306.46,49.202c-5.072-5.072-11.832-7.856-19.04-7.856c-7.216,0-13.972,2.788-19.044,7.856l-16.132,16.136
                         c-5.068,5.064-7.86,11.828-7.86,19.04c0,7.208,2.792,14.2,7.86,19.264L355.9,207.526H26.58C11.732,207.526,0,219.15,0,234.002
                         v22.812c0,14.852,11.732,27.648,26.58,27.648h330.496L252.248,388.926c-5.068,5.072-7.86,11.652-7.86,18.864
                         c0,7.204,2.792,13.88,7.86,18.948l16.132,16.084c5.072,5.072,11.828,7.836,19.044,7.836c7.208,0,13.968-2.8,19.04-7.872
                         l177.68-177.68c5.084-5.088,7.88-11.88,7.86-19.1C492.02,238.762,489.228,231.966,484.14,226.886z" />
                          </g>
                        </g>
                      </svg>
                    </div>
                    </div>`
      });
      $("#autoCompleteDrop").html(rListText);
    } else {
      $("#autoCompleteDrop").hide();
    }
  }

  function hideAutoCompleteDropDown(word) {
    if(event){
      event.stopPropagation();
    }
    text = $("#searchInput").val(word);
    $("#autoCompleteDrop").hide();
    rList = [];
  }
  function viewpreview(event,sub_cat_id,content_id,selectedTempHeight,selectedTempWidth){
    event.stopPropagation();
    event.preventDefault();
    event.stopImmediatePropagation();
    $('#prevtemp').animate({
     scrollTop: $("#prevtemp").offset().top - 20
    });
//     if(iPath != '')
//     {
//       $("#prevImg").attr('src',iPath);
//       $('#prevtemp').animate({
//    scrollTop: $("#closeBtn").offset().top
// }, 'slow');
//       let scrollId = "prevImg";
//    let el = document.getElementById(scrollId);
//    el.scrollIntoView({ behavior: "smooth", block: "start", inline: "start" });


//     }
//     else{
      let deviceWidth = window.innerWidth;
      if(deviceWidth > 600){
        $("#prevtemp").addClass("dialogStyle")
        $("#prevtemp_parent").addClass("dialogStyle_parent")
      }
      else{
        $("#prevtemp").removeClass("dialogStyle")
        $("#prevtemp_parent").removeClass("dialogStyle_parent")
        $("#prevtemp").css({"top":"0px","width":"100%"});
        $("#prevtemp_parent").css({"top":"0px","width":"100%"});
      }
      $('#prevtemp').css('display','block')
      $('#prevtemp_parent').css('display','block')
      $('#prevtemp').css('background','white')
      $('#prevtemp').addClass('template-overlay')
      $('#prevtemp').parent().parent().addClass('template-overlay');
      // setTimeout(() => {
        getTemplates(sub_cat_id, content_id,selectedTempHeight,selectedTempWidth);
      // }, 100);
    // }

}

  function getTemplates(sub_cat_id, content_id,selectedTempHeight,selectedTempWidth,type) {

    let screenWidth = window.screen.width;
    let isMobile = screenWidth <= 768;

    let details = navigator.userAgent;

    let regexp = /android|iphone|kindle|ipad/i;

    let isMobileDevice = regexp.test(details);

    if (isMobileDevice && isMobile) {
    } else if (isMobile) {
      $("#card-wrapper").css("margin-bottom","170px");
      $("#card-wrapper-video").css("margin-bottom","170px");
    } else {
    }


    selectedTempHeight = selectedTempHeight * 2;
    selectedTempWidth = selectedTempWidth * 2;
    if(selectedTempHeight > 546){
      TempHeight = selectedTempHeight / 546;
      selectedTempWidth = Math.round(selectedTempWidth / TempHeight);
      selectedTempHeight = 546
    }

    if(selectedTempWidth > 939){
      TempWidth = selectedTempWidth / 939;
      selectedTempHeight = Math.round(selectedTempHeight / TempWidth);
      selectedTempWidth = 939;
    }

    if(selectedTempWidth < 120){
      $("#active_temp_id").html(`<div class="shimmerBG" style="height: 100%;width: 120px;min-width:120px;max-height: 546px; background-color: #EEEEEF;border-radius: 4.267px;"></div>`);
      $("#activeVideoTemplateShimmer").css({'height': '100%','width':'120px','max-height':'100%'});
    }
    else{
      $("#active_temp_id").html(`<div class="shimmerBG" style="height: ${selectedTempHeight}px;width: ${selectedTempWidth}px;min-width:120px;max-height: min(98vh, 546px); background-color: #EEEEEF;border-radius: 4.267px;"></div>`);
      $("#activeVideoTemplateShimmer").css({'height':selectedTempHeight,'width':selectedTempWidth,'max-height':'100%'});
    }
    $("#ttcbright").hide();
    $("#ttcbleft").hide();
    // $("#active_temp_id").css("margin-top","0");
    $(".Puzyyw").html(`<div>
          <div>
            <div class="shimmerBG" style="height: 45px;width: 100%;background-color: #EEEEEF;border-radius: 4.267px;margin-top:42px;"></div>
            <div class="shimmerBG" style="height: 19px;width: 65%;background-color: #EEEEEF;border-radius: 4.267px;margin: 26px 0 25px 0;"></div>
            <div class="d-flex" style="width:100%;">
              <div class="shimmerBG" style="height: 45px;width: 423px;background-color: #EEEEEF;border-radius: 4.267px;"></div>
              <div class="shimmerBG" style="height: 45px;width: 60px;background-color: #EEEEEF;border-radius: 4.267px;margin-left: 11px;"></div>
            </div>
          <div class="explore-more-teg mt-4">
            <div class="shimmerBG" style="height: 19px;width: 65%;background-color: #EEEEEF;border-radius: 4.267px;margin: 38px 0 23px 0;"></div>
            <div class="shimmerBG" style="height: 251px;width:100%;background-color: #EEEEEF;border-radius: 4.267px;"></div>
          </div>
        </div>`);
    $("#template_slider").html('');
    //  $(".template-heading").html(`<div class="shimmerBG" style="height: 33px;width: 60%;background-color: #EEEEEF;border-radius: 4.267px;"></div>`);
     $(".template-heading").html(``);
     $(".card-wrapper").html(`<div class="pb-2 w-100 template_loader" style="margin-top: 55px;"><div class="temp-loader"><div></div><div></div><div></div><div></div><div></div></div></div>`);
    $(".sub-template-page").css({"margin-top":"0","height":"0px"});

    let requestData = {
    content_id: content_id,
    sub_category_id: sub_cat_id
    };
    $.ajax({
      type: "POST",
      url: "{!! config('constant.ACTIVATION_LINK_PATH') !!}/api/public/api/getTemplateDetailsBySubCategoryId",
      data: JSON.stringify(requestData),
      dataType: 'json',
      contentType: 'application/json',
      error: function (err) {
        if(type == "video"){
          $("#suces_msgv").html("Unable to connect with server, please check your internet connection.");
          $("#suces_hdingv").html("Error");
          $(".error-wrapper").css("height","128px");
          $(".icon-wrapper").css("background-color","#E46675");
          $(".icon-wrapper").html(`<svg aria-hidden="true" role="img" class="err-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                    <path d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z"></path>
                                  </svg>`);
          document.getElementById("copyVideoTempURL").classList.toggle("open-error-wrapper");
          $(".template_loader").html('');
        }
        else{
          $("#suces_msg").html("Unable to connect with server, please check your internet connection.");
          $("#suces_hding").html("Error");
          $(".error-wrapper").css("height","128px");
          $(".icon-wrapper").css("background-color","#E46675");
          $(".icon-wrapper").html(`<svg aria-hidden="true" role="img" class="err-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                    <path d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z"></path>
                                  </svg>`);
          document.getElementById("copyTempURL").classList.toggle("open-error-wrapper");
          $(".template_loader").html('');
        }

      },
      success: function (results) {
        // document.getElementById("ttcbright").style.display = 'block';
        if(results.data.content_details == [] || results.data.content_details == ""){
          window.location.href = '{!! config('constant.ACTIVATION_LINK_PATH') !!}/404/';
        }
        else{
          if(results.data.content_details[0].content_type == 4){
            setTimeout(() => {
              renderImageTemplates(results,selectedTempHeight,selectedTempWidth);
            }, 400);
          }
          else{
            setTimeout(() => {
              renderVideoTemplates(results);
            }, 400);
          }
        }
      }
    });
  }

  function renderImageTemplates(results,selectedTempHeight,selectedTempWidth) {
    if(selectedTempWidth / selectedTempHeight > 2){
      content_items_width = "calc(33.33% - 30px)";
    }
    else{
      content_items_width = "calc(25% - 30px)";
    }

    if(selectedTempWidth < 120){
      finalSelectedTempHeight = '100%';
      finalSelectedTempWidth = 120;
    }
    else{
      finalSelectedTempHeight = selectedTempHeight + 'px';
      finalSelectedTempWidth = selectedTempWidth;
    }
    let templateRenderText = "";
    template_response = results;

    results.data.content_details.forEach((template, index) => {
      if(index != 0){
        webp_thumbnail = template.webp_thumbnail;
        webp_original = webp_thumbnail.replace("webp_thumbnail", "webp_original")
        if ($(document).width() > 768){
          templateRenderText += `<div class="content-item d-flex flex-column card-ol-block cursor-pointer cust-temp-item-new template-preview-tab" style="width:${content_items_width};">
          <a  href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/editor/${template.sub_category_id}/${template.catalog_id}/${template.content_id}" target="_blank" style="text-decoration:none;display:flex;flex-direction:column;height:100%;" onclick="storeDetails('${template.sub_category_id}','${template.catalog_id}','${template.content_id}','${template.content_type}')">
          <div id='img_card_container${index}' class="prograssive_img" style='flex-grow:1;display:flex;align-items:center;min-height:100px;max-height:550px;'>
            <img onload='loadTemplateImg(${index})' draggable="false" alt="${template.catalog_name}" src="${webp_original}"  height="291px" data-src="${webp_original}" onerror=" this.src='${template.compressed_img}'">
            <!-- <div class="crd-overlay low-height"></div>
            <button class="crd-ol-btn">Start from this</button> -->
            <div class="crd-overlay low-height" style="height: calc(100% - 32px); !important"></div>
          <button class="crd-ol-btn c-explore-template" style="outline: none;" onclick="dialogViewPreview(event,${index},'${template.sub_category_id}','${template.content_id}','${template.height}','${template.width}')"><svg class="mr-2" width="20" height="15" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99604 14.1611C9.80772 14.1611 9.61898 14.1561 9.4294 14.1461C5.39752 13.9311 1.72479 11.4137 0.0732114 7.73225C-0.115112 7.31227 0.0723779 6.81938 0.492357 6.63064C0.912752 6.44274 1.40523 6.62981 1.59397 7.0502C2.99265 10.1675 6.10291 12.2999 9.51815 12.4816C13.2871 12.6837 16.8582 10.4992 18.406 7.0502C18.5944 6.62981 19.0877 6.44274 19.5076 6.63064C19.9276 6.81897 20.1151 7.31227 19.9268 7.73225C18.1765 11.6325 14.235 14.1611 9.99604 14.1611Z" fill="#0069FF"></path>
            <path d="M0.833173 8.22373C0.719012 8.22373 0.603184 8.19998 0.492357 8.1504C0.0723779 7.96208 -0.115112 7.46877 0.0732114 7.04879C1.90104 2.97566 6.12082 0.399961 10.5702 0.634949C14.6021 0.849938 18.2748 3.36731 19.9264 7.04879C20.1147 7.46877 19.9272 7.96166 19.5072 8.1504C19.0877 8.33872 18.5944 8.15123 18.4056 7.73084C17.0069 4.6135 13.8967 2.48111 10.4814 2.29945C6.71412 2.09821 3.14139 4.28185 1.59355 7.73084C1.45522 8.04041 1.15149 8.22373 0.833173 8.22373Z" fill="#0069FF"></path>
            <path d="M9.99984 10.825C8.10618 10.825 6.56543 9.28468 6.56543 7.39102C6.56543 5.49737 8.10618 3.95703 9.99984 3.95703C11.8935 3.95703 13.4342 5.49737 13.4342 7.39102C13.4342 9.28468 11.8935 10.825 9.99984 10.825ZM9.99984 5.62361C9.04155 5.62361 8.23201 6.43274 8.23201 7.39102C8.23201 8.34931 9.04155 9.15843 9.99984 9.15843C10.9581 9.15843 11.7677 8.34931 11.7677 7.39102C11.7677 6.43274 10.9581 5.62361 9.99984 5.62361Z" fill="#0069FF"></path>\
          </svg>Preview</button>
          <button onclick="storeDetails('${template.sub_category_id}','${template.catalog_id}','${template.content_id}','${template.content_type}')" class="crd-ol-btn c-check-btn" style="outline: none;">
          <svg width="22" class="mr-2" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19.4367 18.6719H1.94103C1.5732 18.6719 1.28534 18.9757 1.28534 19.3276C1.28534 19.6794 1.58919 19.9832 1.94103 19.9832H19.4526C19.8205 19.9832 20.1083 19.6794 20.1083 19.3276C20.1083 18.9757 19.8045 18.6719 19.4367 18.6719Z" fill="white"></path>
            <path d="M1.28522 12.8667L1.26923 15.9852C1.26923 16.1611 1.3332 16.337 1.46113 16.4649C1.58907 16.5929 1.749 16.6568 1.92491 16.6568L5.02743 16.6408C5.20334 16.6408 5.36327 16.5769 5.49121 16.4489L16.2061 5.73407C16.462 5.47819 16.462 5.06239 16.2061 4.79052L13.1355 1.688C12.8797 1.43212 12.4639 1.43212 12.192 1.688L10.049 3.84697L1.47713 12.4029C1.36518 12.5308 1.28522 12.6907 1.28522 12.8667ZM12.6718 3.09533L14.8307 5.2543L13.6153 6.46971L11.4564 4.31075L12.6718 3.09533ZM2.61258 13.1545L10.5128 5.2543L12.6718 7.41326L4.77155 15.2975L2.59659 15.3135L2.61258 13.1545Z" fill="white"></path>
          </svg>Edit</button>
          </div>`;

          if(template.template_name.length > 30){
            templateRenderText += `<div class="template-name-title"><span class="apply-marquee">${template.template_name}</span></div></a></div>`;
          }
          else{
            templateRenderText += `<div class="template-name-title">${template.template_name}</div></a></div>`;
          }
        }
        else if($(document).width() <= 768){
          templateRenderText += `<div class="content-item d-flex flex-column card-ol-block cursor-pointer cust-temp-item-new template-preview-tab" style="width:${content_items_width};">
          <a  href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/" target="_blank" style="text-decoration:none;display:flex;flex-direction:column;height:100%;">
          <div id='img_card_container${index}' class="prograssive_img" style='flex-grow:1;display:flex;align-items:center;min-height:100px;max-height:550px;'>
            <img onload='loadTemplateImg(${index})' draggable="false" alt="${template.catalog_name}" src="${webp_original}"  height="291px" data-src="${webp_original}" onerror=" this.src='${template.compressed_img}'">
            <!-- <div class="crd-overlay low-height"></div>
            <button class="crd-ol-btn">Start from this</button> -->
            <div onclick="event.stopPropagation(); dialogViewPreview(event,${index},'${template.sub_category_id}','${template.content_id}','${template.height}','${template.width}')"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="top: 5px;right: 5px;width: auto;border-radius:0px;height:26px !important;width:26px !important;"></div>
          </div>`;
          templateRenderText += `<div class="template-name-title">${template.template_name}</div></a></div>`;

          $("#floatingBtn").addClass("fadeInDown");
          $("#floatingBtn").removeClass("fadeInUp");
        }
      }
    });
    const userAgent = window.navigator.userAgent;
    if (/Android/i.test(userAgent)) {
      $("#card-wrapper").css("margin-bottom","125px");
    } else if (/iPhone/i.test(userAgent)) {
      $("#card-wrapper").css("margin-bottom","125px");
    } else if (navigator.platform == 'Mac68K' || navigator.platform == 'MacPPC' || navigator.platform =='MacIntel') {
      $("#card-wrapper").css("margin-bottom","100px");
    }
    else if((navigator.appVersion).match(/OS (\d+)(\d+)?(\d+)?/)){
      $("#card-wrapper").css("margin-bottom","100px");
    }
    $("#card-wrapper").html(templateRenderText);
    results.data.content_details.forEach((template, index) => {
      let card_width = $("#img_card_container"+index).width();
      let ratio = template.height / template.width;
      if( card_width >= template.width){
        card_height = template.height;
      }
      else{
        card_height = (card_width * ratio) < 551 ? card_width * ratio : 550;
      }
      $("#img_card_container"+index).css("height",card_height);
      $("#img_card_container"+index).children("img").css("height",card_height)
    });
    let sliderRenderText = "";
    let active_template = "";
    let multiple_images_obj = [];
    results.data.content_details.forEach((template, index) => {
      if(index == 0){
        template_name = template.template_name;
        sub_category_name = template.sub_category_name;
        is_free = template.is_free;
        template_height = template.height * 2;
        template_width = template.width * 2;
        sub_cat_id = template.sub_category_id;
        catalog_id = template.catalog_id;
        content_id = template.content_id;
        content_type = template.content_type;
        search_category_arr = [];
        search_category_arr_with_empty_element = template.search_category.split(',');
        search_category_arr_with_empty_element.forEach((element,index) => {
          if(element.trim() != ''){
            search_category_arr.push(element);
          }
        });
        if(template.multiple_images != ""){
          let mjson = JSON.parse(template.multiple_images);
          let pages_sequence = template.pages_sequence.split(",");
          pages_sequence.forEach(pages_data => {
            multiple_imagesOBJ = mjson[pages_data]
            multiple_images_obj.push(multiple_imagesOBJ);
          });
        }
        else{
          webp_url_arr = template.webp_thumbnail.split("/");
          jpg_url_arr = template.compressed_img.split("/");
          obj = {
            1: {
              name: jpg_url_arr[5],
              webp_name:webp_url_arr[5],
              width: template.width,
              height: template.height
            }
          }
          multiple_images_obj = obj;
        }
      }
    });
    multiple_images_arr = Object.values(multiple_images_obj);
    multiple_images_arr.forEach((template, index) => {
      if(index == 0){
        active_template += `<div class="border-redius template-after position-relative" style="overflow: hidden;">
                  <div class="lDm9lQ">
                    <div id='activeTemplateShimmer' class="shimmerBG" style="height: ${finalSelectedTempHeight};width: ${finalSelectedTempWidth}px;min-width:120px;max-height: min(98vh, 546px);background-color: #EEEEEF;border-radius: 4.267px;"></div>
                    <img id='activeTemplateImg' src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/webp_original/${template.webp_name}" onload="loadActiveTemplate(${finalSelectedTempWidth})" onerror=" this.src='http://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/${template.name}'" class="BIOuOQ" style="max-height: min(98vh, 546px);display:none;border-radius: 8px;" id="prevImg">
                  </div>
                </div>`;
      }
      sliderRenderText += `<div class="c-template-item mob-sub-explore-tmp mr-0" style="padding-bottom:0 !important;">
          <div >
            <div class="c-template-inner-box">
              <div class="c-template-img-wrap">
                <div id='slider_shimmer${index}' class="shimmerBG" style="height: 80px;width: 70px;background-color: #EEEEEF;border-radius: 4.267px;padding-bottom:36px;margin-right:1px;margin-left:1px;"></div>
                <picture id='sliderImgLoad${index}' style='display:none;place-content:center;min-height:80px;min-width:60px;' class="c-template-active" onclick="selectTemplate(${index},'https://d3jmn01ri1fzgl.cloudfront.net/photoadking/webp_original/${template.webp_name}')">
                  <source srcset="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/webp_original/${template.webp_name}" type="image/webp">
                  <img onload='loadSliderImg(${index})' class="img-fluid c-sub-template" style="background: #EBEBED" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/webp_original/${template.webp_name}" alt="Flyer" height="140px" width="114px">
                </picture>
              </div>
            </div>
          </div>
        </div>`;
    });

    let template_data = "";
    template_data = `
              <div>
                <div>
                  <div class="_8VoL_g d-flex" style="justify-content: space-between;">
                    <span class="USE2Rg CWAxhQ _H6mgQ p-0">`;

      if(is_free == 0){
        template_data += `
        <span aria-hidden="true" class="edzEug uRWxVA dkWypw">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_13011_38602)">
                          <path d="M16.9999 8.50001C17.0011 8.06531 16.8588 7.64235 16.5952 7.29672C16.5115 7.18677 16.454 7.05914 16.4272 6.92359C16.4003 6.78804 16.4048 6.64814 16.4402 6.51458C16.5505 6.0942 16.5203 5.64927 16.354 5.24771C16.1878 4.84615 15.8947 4.51 15.5196 4.29059C15.3999 4.22088 15.2978 4.12478 15.2209 4.0096C15.144 3.89442 15.0945 3.76322 15.076 3.62598C15.0171 3.19523 14.8187 2.79563 14.5112 2.48826C14.2037 2.1809 13.804 1.98267 13.3732 1.92388C13.2361 1.9055 13.1049 1.85606 12.9897 1.77931C12.8745 1.70257 12.7784 1.60056 12.7086 1.48104C12.4897 1.10577 12.154 0.81252 11.7527 0.646133C11.3513 0.479746 10.9066 0.449381 10.4864 0.559682C10.4109 0.579342 10.3332 0.589333 10.2552 0.589415C10.0562 0.589137 9.86272 0.524194 9.70389 0.404373C9.35803 0.140695 8.9348 -0.00144604 8.49989 1.10923e-05L6.50781 8.50001L8.49989 17C8.9346 17.0012 9.35756 16.859 9.70319 16.5953C9.86216 16.4752 10.0559 16.41 10.2552 16.4096C10.3328 16.4096 10.4102 16.4196 10.4853 16.4393C10.6507 16.4827 10.8209 16.5047 10.9918 16.5047C11.3392 16.5042 11.6804 16.4129 11.9817 16.24C12.2829 16.067 12.5337 15.8184 12.7093 15.5186C12.779 15.399 12.8751 15.2968 12.9903 15.22C13.1055 15.1431 13.2367 15.0936 13.3739 15.0751C13.8045 15.0162 14.204 14.8179 14.5114 14.5107C14.8187 14.2034 15.017 13.8039 15.076 13.3733C15.0944 13.2362 15.1439 13.105 15.2206 12.9898C15.2973 12.8747 15.3994 12.7785 15.5189 12.7087C15.8939 12.4896 16.1869 12.1538 16.3533 11.7526C16.5196 11.3513 16.5502 10.9067 16.4402 10.4865C16.4049 10.3528 16.4005 10.2128 16.4274 10.0771C16.4543 9.94148 16.5118 9.81375 16.5955 9.70366C16.8593 9.35795 17.0014 8.93482 16.9999 8.50001Z" fill="#0F9AF0"></path>
                          <path d="M7.29643 0.40472C7.13745 0.524866 6.94372 0.590058 6.74445 0.590461C6.66678 0.59042 6.58942 0.580428 6.51429 0.560728C6.34894 0.517371 6.17872 0.495387 6.00778 0.495317C5.66042 0.495824 5.31921 0.587076 5.01796 0.760034C4.71671 0.932992 4.46587 1.18165 4.2903 1.48139C4.22062 1.60067 4.12468 1.70251 4.00976 1.77918C3.89485 1.85585 3.76398 1.90533 3.62709 1.92388C3.19601 1.9824 2.79601 2.18061 2.48833 2.48816C2.18066 2.7957 1.98228 3.19563 1.92359 3.62668C1.90517 3.76388 1.85567 3.89507 1.77887 4.01025C1.70207 4.12542 1.59998 4.22154 1.4804 4.29129C1.10544 4.51048 0.812478 4.8463 0.64619 5.24753C0.479901 5.64876 0.449418 6.09336 0.559392 6.51353C0.59473 6.64724 0.599109 6.78724 0.572196 6.9229C0.545284 7.05855 0.487788 7.18628 0.404084 7.29637C0.141287 7.64254 -0.000976563 8.06522 -0.000976562 8.49984C-0.000976563 8.93446 0.141287 9.35714 0.404084 9.70331C0.487847 9.81323 0.545384 9.94084 0.5723 10.0764C0.599217 10.212 0.594802 10.3519 0.559392 10.4855C0.449087 10.9058 0.479362 11.3508 0.645596 11.7523C0.81183 12.1539 1.10489 12.49 1.48005 12.7094C1.59953 12.779 1.70157 12.8749 1.77843 12.9898C1.85529 13.1047 1.90493 13.2357 1.92359 13.3727C1.98211 13.8037 2.18032 14.2037 2.48787 14.5114C2.79541 14.8191 3.19534 15.0175 3.62639 15.0762C3.76359 15.0946 3.89478 15.1441 4.00995 15.2209C4.12513 15.2977 4.22125 15.3998 4.291 15.5193C4.50993 15.8946 4.84572 16.1877 5.24703 16.3541C5.64834 16.5204 6.09307 16.5507 6.51324 16.4404C6.64692 16.4054 6.78679 16.4012 6.92233 16.4281C7.05786 16.455 7.18553 16.5123 7.29573 16.5957C7.64159 16.8593 8.06481 17.0015 8.49972 17V7.56167e-06C8.06501 -0.00119497 7.64205 0.141062 7.29643 0.40472Z" fill="#13BDF7"></path>
                          <path d="M8.49999 2.02539L7.07178 8.50008L8.49999 14.9748C10.2172 14.9748 11.864 14.2926 13.0783 13.0784C14.2925 11.8641 14.9747 10.2173 14.9747 8.50008C14.9747 6.78289 14.2925 5.13602 13.0783 3.92178C11.864 2.70754 10.2172 2.02539 8.49999 2.02539Z" fill="#085AAE"></path>
                          <path d="M2.02539 8.50008C2.02539 10.2173 2.70754 11.8641 3.92178 13.0784C5.13603 14.2926 6.78289 14.9748 8.50009 14.9748V2.02539C6.78289 2.02539 5.13603 2.70754 3.92178 3.92178C2.70754 5.13602 2.02539 6.78289 2.02539 8.50008Z" fill="#0A77E8"></path>
                          <path d="M8.50012 5.44824L7.50391 8.93464L13.0303 7.93843L11.7466 5.44824H8.50012Z" fill="#B2F5FF"></path>
                          <path d="M8.49992 5.44824H5.25347L3.96973 7.93843L8.49992 8.93464V5.44824Z" fill="white"></path>
                          <path d="M8.50012 13.1459L13.0303 7.93848H8.50012L7.50391 10.542L8.50012 13.1459Z" fill="#18E0FF"></path>
                          <path d="M3.96973 7.93848L8.49992 13.1459V7.93848H3.96973Z" fill="#B2F5FF"></path>
                          </g>
                          <defs>
                          <clipPath id="clip0_13011_38602">
                          <rect width="17" height="17" fill="white"></rect>
                          </clipPath>
                          </defs>
                        </svg>
                      </span> PRO `;
      }
      template_data += `</span>
                  </div>
                </div>
                <div class="w9feWg mt-3">
                  <h1 class="explore-heading">${template_name}</h1>
                </div>
                <p class="sub-heading-template m-0 pt-2">${sub_category_name}  |  ${template_width} x ${template_height} px</p>
                <div class="d-flex mt-4 align-items-center position-relative">
                  <button onclick="redirectCustomize('${sub_cat_id}','${catalog_id}','${content_id}','${content_type}')" class="template-btn-customize">Customize this template</button>
                  <span class="ml-3 sharebtnHover" onclick="showPopup('#shareoption')"  onmouseenter="showShareBtnTooltip()" onmouseleave="hideShareBtnTooltip()" style="cursor: pointer;height: 44px;width: 48px;border-radius: 5px;display: flex;justify-content: center;align-items: center;">
                      <svg id="sharebtn" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M18.7033 0.0747833C18.3939 0.0911903 18.0564 0.149784 17.7095 0.250565C16.5494 0.583378 15.5298 1.42947 14.9861 2.51228C14.6767 3.12869 14.5267 3.7615 14.5267 4.45291C14.5267 4.86541 14.5525 5.08103 14.6486 5.47713C14.6837 5.62947 14.7119 5.75603 14.7095 5.76072C14.6908 5.77947 8.45874 9.18728 8.44233 9.18728C8.43296 9.18728 8.36733 9.124 8.29937 9.04432C7.87749 8.56619 7.15562 8.09275 6.50405 7.86775C5.97905 7.68728 5.5314 7.61228 4.9689 7.61228C3.4478 7.61463 2.05562 8.36697 1.21187 9.64431C0.438428 10.8162 0.29546 12.349 0.834522 13.6615C1.15562 14.4396 1.76733 15.1779 2.48452 15.6513C3.99624 16.6498 5.97671 16.6545 7.47671 15.6631C7.78843 15.4568 7.97358 15.3045 8.24077 15.0303L8.45405 14.8123L11.1939 16.3146C12.7009 17.1396 14.1142 17.9154 14.3345 18.0373L14.7353 18.2576L14.6673 18.5084C14.4869 19.1928 14.4892 19.931 14.6697 20.6412C14.883 21.4732 15.3845 22.2842 16.0314 22.8467C16.4181 23.1842 16.9876 23.5217 17.433 23.681C17.7025 23.7771 18.1033 23.8779 18.3869 23.9178C18.7126 23.9646 19.3384 23.967 19.6455 23.9201C20.6228 23.7701 21.4619 23.3599 22.1439 22.7013C23.3017 21.5834 23.7494 19.9943 23.3439 18.4474C23.1001 17.524 22.54 16.6873 21.7666 16.0967C20.4095 15.0584 18.5673 14.8732 17.0275 15.6185C16.5611 15.8435 16.0126 16.2467 15.7056 16.5888L15.565 16.7459L15.4759 16.6967C15.4267 16.6709 14.0134 15.8951 12.333 14.974C9.39624 13.3685 9.27905 13.3006 9.28843 13.242C9.29546 13.2092 9.32593 13.0685 9.35874 12.9303C9.51108 12.2834 9.49702 11.5803 9.3189 10.9076L9.26499 10.7084L9.45483 10.6053C9.5603 10.549 10.9712 9.77557 12.5931 8.88728L15.5439 7.27244L15.7923 7.52322C17.1001 8.83338 19.0267 9.21541 20.7517 8.50994C21.5298 8.19119 22.2705 7.58182 22.7509 6.86697C23.6134 5.5826 23.7189 3.9115 23.0275 2.53103C22.3666 1.21385 21.0986 0.309158 19.6291 0.107595C19.2986 0.0630646 19.0666 0.05369 18.7033 0.0747833ZM19.5822 1.83494C20.1072 1.94978 20.5876 2.21463 20.972 2.599C21.2603 2.88728 21.4666 3.21775 21.6025 3.60916C21.708 3.90916 21.7384 4.099 21.7384 4.46463C21.7384 4.83025 21.708 5.0201 21.6025 5.3201C21.2837 6.23182 20.5197 6.89275 19.5353 7.10603C19.4509 7.12244 19.2376 7.1365 19.008 7.1365C18.483 7.1365 18.183 7.06619 17.7376 6.83885C16.4462 6.1826 15.9119 4.6615 16.5142 3.35369C16.7556 2.82869 17.2173 2.35056 17.7423 2.08572C17.9791 1.96619 18.2791 1.86306 18.4923 1.82791C18.5884 1.81384 18.6845 1.79744 18.7033 1.79275C18.79 1.77166 19.4345 1.80213 19.5822 1.83494ZM5.47515 9.36306C6.3353 9.51072 7.06421 10.0545 7.45562 10.8396C7.81655 11.5662 7.82827 12.3795 7.48608 13.1201C7.32671 13.467 6.99858 13.8771 6.67983 14.1209C6.37046 14.3576 5.93687 14.5592 5.55483 14.6388C5.28062 14.6974 4.70405 14.6974 4.42983 14.6388C3.88608 14.524 3.40327 14.2545 3.01187 13.8467C2.77749 13.6053 2.67671 13.467 2.54312 13.2068C2.35796 12.8412 2.27827 12.5412 2.25718 12.117C2.22202 11.3763 2.49155 10.6826 3.02358 10.1482C3.43374 9.73338 3.93765 9.46385 4.48843 9.36541C4.74155 9.32088 5.21968 9.31853 5.47515 9.36306ZM19.5822 16.931C20.2806 17.0857 20.9158 17.5123 21.2884 18.0771C21.4478 18.3162 21.5158 18.4498 21.6025 18.7029C21.708 19.0029 21.7384 19.1928 21.7384 19.5584C21.7384 19.924 21.708 20.1138 21.6025 20.4138C21.3259 21.2084 20.7166 21.8084 19.8986 22.0943C19.5892 22.2045 19.4111 22.2303 18.9962 22.2303C18.6025 22.2279 18.4689 22.2092 18.1525 22.1084C17.1869 21.799 16.4462 20.9459 16.3009 19.9732C16.1509 18.9818 16.5447 18.0256 17.3509 17.4185C17.6533 17.1935 18.1291 16.9849 18.4806 16.924C18.558 16.9099 18.647 16.8935 18.6798 16.8888C18.8041 16.8654 19.4251 16.8959 19.5822 16.931Z" fill="#9A9A9A"></path>
                      </svg>
                      <div class="shareTooltip" style="display:none;">Share</div>
                  </span>
                  <li class="position-absolute" id="shareoption" style="background-color: #ffffff !important;padding: 15px 16px !important;width: 342px;top: 50px;right: -25px;list-style: none;border-radius: 7px;box-shadow: 1px 3px 7px 0px rgba(0, 0, 0, 0.25);display: none;z-index:45;">
                    <span class="mb-3 sharing-menu">Sharing Options</span>
                    <div class="d-flex mt-3" style="flex-wrap: wrap;">
                      <div onclick="copyTemplateURL('copyTempURL','${sub_cat_id}','${content_id}')" class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover cursor-pointer">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#EFF0F1" height="40" rx="20" width="40"></rect>
                              <path clip-rule="evenodd" d="M27.7775 10H12.2225C11.6331 10 11.0678 10.2342 10.651 10.651C10.2342 11.0678 10 11.6331 10 12.2225V27.7775C10 28.3669 10.2342 28.9322 10.651 29.349C11.0678 29.7658 11.6331 30 12.2225 30H27.7775C28.3669 30 28.9322 29.7658 29.349 29.349C29.7658 28.9322 30 28.3669 30 27.7775V12.2225C30 11.6331 29.7658 11.0678 29.349 10.651C28.9322 10.2342 28.3669 10 27.7775 10ZM11.6667 12.2225C11.6667 11.915 11.915 11.6667 12.2225 11.6667H21.1108V28.3333H12.2225C12.1495 28.3333 12.0772 28.319 12.0098 28.291C11.9424 28.2631 11.8811 28.2221 11.8295 28.1705C11.7779 28.1189 11.7369 28.0576 11.709 27.9902C11.681 27.9228 11.6667 27.8505 11.6667 27.7775V12.2225ZM22.7775 17.7775V11.6667H27.7775C28.085 11.6667 28.3333 11.915 28.3333 12.2225V17.7775H22.7775ZM22.7775 19.4442V28.3333H27.7775C27.8505 28.3333 27.9228 28.319 27.9902 28.291C28.0576 28.2631 28.1189 28.2221 28.1705 28.1705C28.2221 28.1189 28.2631 28.0576 28.291 27.9902C28.319 27.9228 28.3333 27.8505 28.3333 27.7775V19.4442H22.7775Z" fill="#575D68" fill-rule="evenodd"></path>
                            </svg>
                            <span class="sharing-operation">Copy link</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://twitter.com/intent/tweet?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing.&amp;&amp;" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E4F5FD" height="40" rx="20" width="40"></rect>
                              <path d="M30 13.9238C29.2563 14.25 28.4637 14.4662 27.6375 14.5712C28.4875 14.0638 29.1363 13.2662 29.4412 12.305C28.6488 12.7775 27.7738 13.1113 26.8412 13.2975C26.0887 12.4963 25.0162 12 23.8462 12C21.5763 12 19.7487 13.8425 19.7487 16.1013C19.7487 16.4262 19.7762 16.7387 19.8438 17.0362C16.435 16.87 13.4188 15.2363 11.3925 12.7475C11.0387 13.3613 10.8313 14.0638 10.8313 14.82C10.8313 16.24 11.5625 17.4987 12.6525 18.2275C11.9937 18.215 11.3475 18.0238 10.8 17.7225C10.8 17.735 10.8 17.7512 10.8 17.7675C10.8 19.76 12.2212 21.415 14.085 21.7962C13.7512 21.8875 13.3875 21.9312 13.01 21.9312C12.7475 21.9312 12.4825 21.9163 12.2337 21.8612C12.765 23.485 14.2725 24.6788 16.065 24.7175C14.67 25.8088 12.8988 26.4662 10.9813 26.4662C10.645 26.4662 10.3225 26.4513 10 26.41C11.8162 27.5813 13.9688 28.25 16.29 28.25C23.835 28.25 27.96 22 27.96 16.5825C27.96 16.4012 27.9538 16.2263 27.945 16.0525C28.7588 15.475 29.4425 14.7538 30 13.9238Z" fill="#1DA1F2"></path>
                            </svg>
                            <span class="sharing-operation">Twitter</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.facebook.com/sharer/sharer.php?u={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E6EEFF" height="40" rx="20" width="40"></rect>
                              <rect fill="#323999" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path clip-rule="evenodd" d="M18.3798 20.0022V26.2518C18.3798 26.3465 18.4429 26.4097 18.5376 26.4097H20.8733C20.968 26.4097 21.0311 26.3465 21.0311 26.2518V19.9075H22.704C22.7987 19.9075 22.8619 19.8444 22.8619 19.7497L23.0197 17.8243C23.0197 17.7296 22.9565 17.6349 22.8619 17.6349H21.0311V16.3092C21.0311 15.9936 21.2837 15.741 21.5993 15.741H22.8934C22.9881 15.741 23.0512 15.6779 23.0512 15.5832V13.6578C23.0512 13.5631 22.9881 13.5 22.8934 13.5H20.7155C19.4214 13.5 18.3798 14.5416 18.3798 15.8357V17.6664H17.2119C17.1172 17.6664 17.0541 17.7296 17.0541 17.8243V19.7497C17.0541 19.8444 17.1172 19.9075 17.2119 19.9075H18.3798V20.0022Z" fill="white" fill-rule="evenodd"></path>
                            </svg>
                            <span class="sharing-operation">Facebook</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.instagram.com/photoadking/" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="url(#paint0_linear_1360_2071)" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1360_2071)">
                                <path d="M24.0436 10H15.9601C12.6715 10 10 12.6715 10 15.9601V24.0436C10 27.3285 12.6715 30 15.9601 30H24.0436C27.3285 30 30.0036 27.3285 30.0036 24.0399V15.9601C30 12.6715 27.3285 10 24.0436 10ZM27.9891 24.0436C27.9891 26.2214 26.2214 27.9891 24.0436 27.9891H15.9601C13.7822 27.9891 12.0145 26.2214 12.0145 24.0436V15.9601C12.0145 13.7822 13.7822 12.0145 15.9601 12.0145H24.0436C26.2214 12.0145 27.9891 13.7822 27.9891 15.9601V24.0436Z" fill="url(#paint1_linear_1360_2071)"></path>
                                <path d="M20 14.8271C17.147 14.8271 14.8276 17.1466 14.8276 19.9996C14.8276 22.8526 17.147 25.172 20 25.172C22.853 25.172 25.1724 22.8526 25.1724 19.9996C25.1724 17.1466 22.853 14.8271 20 14.8271ZM20 23.1611C18.2541 23.1611 16.8385 21.7455 16.8385 19.9996C16.8385 18.2536 18.2541 16.838 20 16.838C21.7459 16.838 23.1615 18.2536 23.1615 19.9996C23.1615 21.7455 21.7459 23.1611 20 23.1611Z" fill="url(#paint2_linear_1360_2071)"></path>
                                <path d="M25.1833 16.1054C25.8669 16.1054 26.4211 15.5512 26.4211 14.8676C26.4211 14.184 25.8669 13.6299 25.1833 13.6299C24.4997 13.6299 23.9456 14.184 23.9456 14.8676C23.9456 15.5512 24.4997 16.1054 25.1833 16.1054Z" fill="url(#paint3_linear_1360_2071)"></path>
                              </g>
                              <defs>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint0_linear_1360_2071" x1="40" x2="-2.38419e-06" y1="0" y2="40">
                                  <stop stop-color="#F1DBFA"></stop>
                                  <stop offset="0.494792" stop-color="#FDE8EB"></stop>
                                  <stop offset="1" stop-color="#FFFAE3"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint1_linear_1360_2071" x1="11.7952" x2="28.1258" y1="28.206" y2="11.8754">
                                  <stop stop-color="#F3CA5C"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint2_linear_1360_2071" x1="20.0006" x2="20.0006" y1="29.9426" y2="10.1549">
                                  <stop stop-color="#E09B3D"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint3_linear_1360_2071" x1="25.1837" x2="25.1837" y1="29.9431" y2="10.1555">
                                  <stop stop-color="#E09B3D"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <clipPath id="clip0_1360_2071">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Instagram</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.linkedin.com/shareArticle?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;title=PhotoAdKing&amp;summary=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing." rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E0EFF6" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7534)">
                                <path d="M28.1908 10H11.8092C10.81 10 10 10.81 10 11.8092V28.1908C10 29.19 10.81 30 11.8092 30H28.1908C29.19 30 30 29.19 30 28.1908V11.8092C30 10.81 29.19 10 28.1908 10ZM16.1888 27.2693C16.1888 27.5601 15.9531 27.7958 15.6624 27.7958H13.4212C13.1304 27.7958 12.8947 27.5601 12.8947 27.2693V17.8745C12.8947 17.5837 13.1304 17.348 13.4212 17.348H15.6624C15.9531 17.348 16.1888 17.5837 16.1888 17.8745V27.2693ZM14.5418 16.4624C13.3659 16.4624 12.4127 15.5092 12.4127 14.3333C12.4127 13.1574 13.3659 12.2042 14.5418 12.2042C15.7176 12.2042 16.6709 13.1574 16.6709 14.3333C16.6709 15.5092 15.7177 16.4624 14.5418 16.4624ZM27.901 27.3117C27.901 27.5791 27.6843 27.7958 27.417 27.7958H25.012C24.7447 27.7958 24.528 27.5791 24.528 27.3117V22.905C24.528 22.2476 24.7208 20.0243 22.81 20.0243C21.3279 20.0243 21.0272 21.5461 20.9669 22.2291V27.3117C20.9669 27.5791 20.7502 27.7958 20.4828 27.7958H18.1568C17.8895 27.7958 17.6727 27.5791 17.6727 27.3117V17.8321C17.6727 17.5648 17.8895 17.348 18.1568 17.348H20.4828C20.7501 17.348 20.9669 17.5648 20.9669 17.8321V18.6517C21.5164 17.827 22.3332 17.1904 24.0722 17.1904C27.9231 17.1904 27.901 20.7881 27.901 22.7648V27.3117Z" fill="#0077B7"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7534">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">LinkedIn</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=&amp;caption=&amp;content={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;posttype=link&amp;shareSource=legacy&amp;title=&amp;url=http://www.google.com" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E8F4FF" height="40" rx="20" width="40"></rect>
                              <path d="M24.5182 25.9055C24.2919 26.1292 24.0227 26.3049 23.7268 26.422C23.431 26.5392 23.1145 26.5954 22.7964 26.5873C21.6691 26.5873 21.16 25.9055 21.16 24.8964V19.1782H24.7964V15.7236H21.16V10H18.4145C18.204 11.2457 17.6916 12.4208 16.922 13.4227C16.1525 14.4245 15.1492 15.2227 14 15.7473V19.1782H16.6782V25.7618C16.6782 26.6709 17.54 30 21.9309 30C24.5127 30 25.5818 28.3382 25.5818 28.3382L24.5182 25.9055Z" fill="#34526F"></path>
                            </svg>
                            <span class="sharing-operation">Tumblr</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://in.pinterest.com/pin/create/button/?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEEEF" height="40" rx="20" width="40"></rect>
                              <path d="M20 10C14.4828 10 10 14.4751 10 19.9828C10 24.0792 12.4483 27.5904 16 29.1395C15.9655 28.451 16 27.5904 16.1724 26.8331C16.3793 26.0069 17.4483 21.3942 17.4483 21.3942C17.4483 21.3942 17.1379 20.7402 17.1379 19.8107C17.1379 18.3305 18 17.2289 19.0689 17.2289C19.9655 17.2289 20.4138 17.9174 20.4138 18.7435C20.4138 19.6386 19.8276 21.0155 19.5172 22.2892C19.2759 23.3563 20.0345 24.2169 21.1034 24.2169C23 24.2169 24.2759 21.7728 24.2759 18.9156C24.2759 16.7125 22.7931 15.0946 20.1034 15.0946C17.0689 15.0946 15.1724 17.3666 15.1724 19.8795C15.1724 20.7401 15.4138 21.3597 15.8276 21.8416C16 22.0482 16.0345 22.1514 15.9655 22.3924C15.931 22.5645 15.7931 23.012 15.7586 23.1841C15.6896 23.4251 15.4827 23.5284 15.2414 23.4251C13.8276 22.8399 13.2069 21.3252 13.2069 19.6041C13.2069 16.7813 15.5862 13.3734 20.3448 13.3734C24.1724 13.3734 26.6896 16.1273 26.6896 19.0877C26.6896 23.012 24.5172 25.938 21.3104 25.938C20.2414 25.938 19.2069 25.3528 18.8621 24.6988C18.8621 24.6988 18.2759 27.0052 18.1724 27.4527C17.9655 28.21 17.5517 29.0017 17.1724 29.5869C18.069 29.8623 19.0345 30 20 30C25.5172 30 30 25.5249 30 20.0172C30 14.5094 25.5173 10 20 10Z" fill="#CB1F24"></path>
                            </svg>
                            <span class="sharing-operation">Pinterest</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.youtube.com/@photoadking" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEBEC" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7567)">
                                <path d="M29.5898 15.1908C29.3594 14.3356 28.6836 13.66 27.8281 13.4296C26.2617 13 20 13 20 13C20 13 13.7383 13 12.1758 13.41C11.3359 13.6404 10.6445 14.3317 10.4141 15.1869C10 16.7568 10 19.998 10 19.998C10 19.998 10 23.2589 10.4102 24.8053C10.6406 25.6605 11.3164 26.3361 12.1719 26.5665C13.7539 26.9961 19.9961 26.9961 19.9961 26.9961C19.9961 26.9961 26.2578 26.9961 27.8203 26.5861C28.6758 26.3556 29.3516 25.6801 29.582 24.8248C30 23.2589 30 20.0176 30 20.0176C30 20.0176 30.0156 16.7568 29.5898 15.1908Z" fill="#FF0000"></path>
                                <path d="M18.0078 22.9974L23.2148 19.9982L18.0078 17.0029V22.9974Z" fill="white"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7567">
                                  <rect fill="white" height="14" transform="translate(10 13)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">YouTube</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://web.whatsapp.com/send?text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing.%0D%0A{!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E0F5DD" height="40" rx="20" width="40"></rect>
                              <rect fill="#29A71A" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path d="M24.5065 15.3791C23.4428 14.3049 22.0304 13.6461 20.5238 13.5216C19.0172 13.397 17.5158 13.815 16.2902 14.7C15.0647 15.585 14.1957 16.8789 13.8401 18.3481C13.4845 19.8174 13.6657 21.3654 14.3509 22.7129L13.6783 25.9784C13.6713 26.0109 13.6711 26.0445 13.6777 26.0771C13.6843 26.1097 13.6975 26.1406 13.7166 26.1678C13.7445 26.2091 13.7844 26.2409 13.8309 26.259C13.8774 26.277 13.9283 26.2805 13.9768 26.2688L17.1773 25.5103C18.521 26.1781 20.0581 26.3476 21.515 25.9886C22.9719 25.6295 24.2542 24.7652 25.1337 23.5495C26.0132 22.3338 26.4329 20.8454 26.318 19.3493C26.2032 17.8532 25.5612 16.4463 24.5065 15.3791ZM23.5086 23.3437C22.7726 24.0775 21.825 24.5619 20.7992 24.7287C19.7734 24.8954 18.7211 24.736 17.7907 24.273L17.3446 24.0523L15.3825 24.517L15.3883 24.4926L15.7949 22.5177L15.5765 22.0867C15.1011 21.153 14.9334 20.0928 15.0974 19.058C15.2614 18.0232 15.7487 17.0668 16.4896 16.3259C17.4204 15.3953 18.6828 14.8725 19.9991 14.8725C21.3153 14.8725 22.5777 15.3953 23.5086 16.3259C23.5165 16.335 23.525 16.3435 23.5341 16.3514C24.4534 17.2844 24.9667 18.543 24.9619 19.8528C24.9571 21.1626 24.4347 22.4174 23.5086 23.3437Z" fill="white"></path>
                              <path d="M23.3343 21.895C23.0938 22.2737 22.7139 22.7372 22.2365 22.8523C21.4001 23.0544 20.1164 22.8592 18.519 21.3699L18.4993 21.3525C17.0948 20.0502 16.73 18.9664 16.8183 18.1067C16.8671 17.6188 17.2737 17.1774 17.6164 16.8893C17.6706 16.843 17.7348 16.8101 17.804 16.7931C17.8732 16.7761 17.9454 16.7756 18.0148 16.7915C18.0842 16.8074 18.149 16.8394 18.2039 16.8848C18.2587 16.9302 18.3022 16.9878 18.3308 17.0531L18.8478 18.2148C18.8814 18.2901 18.8938 18.3731 18.8838 18.455C18.8738 18.5369 18.8417 18.6144 18.7909 18.6794L18.5295 19.0186C18.4734 19.0887 18.4396 19.1739 18.4323 19.2633C18.4251 19.3528 18.4448 19.4423 18.4888 19.5205C18.6352 19.7772 18.986 20.1548 19.3752 20.5045C19.812 20.8994 20.2964 21.2607 20.6031 21.3839C20.6852 21.4174 20.7754 21.4256 20.8622 21.4074C20.9489 21.3891 21.0282 21.3453 21.0899 21.2816L21.3931 20.9761C21.4516 20.9184 21.5243 20.8773 21.6039 20.8569C21.6835 20.8365 21.7671 20.8375 21.8461 20.8599L23.0741 21.2085C23.1418 21.2292 23.2039 21.2652 23.2556 21.3137C23.3073 21.3621 23.3472 21.4218 23.3723 21.488C23.3974 21.5543 23.407 21.6254 23.4005 21.6959C23.3939 21.7665 23.3712 21.8346 23.3343 21.895Z" fill="white"></path>
                            </svg>
                            <span class="sharing-operation">WhatsApp</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://t.me/share/url?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing." rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E4F5FD" height="40" rx="20" width="40"></rect>
                              <rect fill="#25A2DF" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path d="M14.5588 19.1534L25.2008 15.0502C25.6947 14.8718 26.1261 15.1707 25.9661 15.9176L25.967 15.9167L24.155 24.4533C24.0207 25.0585 23.6611 25.2057 23.1579 24.9206L20.3985 22.8869L19.0676 24.1691C18.9204 24.3163 18.7963 24.4404 18.5111 24.4404L18.707 21.6323L23.8211 17.0121C24.0437 16.8162 23.7714 16.7059 23.478 16.9009L17.1581 20.8799L14.4337 20.03C13.8422 19.8424 13.8294 19.4386 14.5588 19.1534Z" fill="white"></path>
                            </svg>
                            <span class="sharing-operation">Telegram</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="mailto:?subject=https://photoadking.com/&amp;body=" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEDD8" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7512)">
                                <path d="M21.6714 22.2536C21.1739 22.5853 20.5959 22.7607 20 22.7607C19.4041 22.7607 18.8262 22.5853 18.3286 22.2536L10.1332 16.7898C10.0877 16.7595 10.0434 16.7279 10 16.6954V25.6484C10 26.6749 10.833 27.4896 11.8411 27.4896H28.1588C29.1853 27.4896 30 26.6565 30 25.6484V16.6954C29.9565 16.728 29.9121 16.7596 29.8665 16.79L21.6714 22.2536Z" fill="#FF961C"></path>
                                <path d="M10.7832 15.8148L18.9786 21.2786C19.2889 21.4854 19.6444 21.5888 20 21.5888C20.3555 21.5888 20.7111 21.4854 21.0214 21.2786L29.2168 15.8148C29.7072 15.488 30 14.9411 30 14.3509C30 13.336 29.1743 12.5104 28.1595 12.5104H11.8405C10.8257 12.5104 10 13.336 10 14.3519C10 14.9411 10.2928 15.488 10.7832 15.8148Z" fill="#FF961C"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7512">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Email</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://www.reddit.com/submit?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;=" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFE8E0" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7519)">
                                <path clip-rule="evenodd" d="M21.865 22.79C21.8819 22.8067 21.8953 22.8265 21.9044 22.8484C21.9136 22.8703 21.9183 22.8938 21.9183 22.9175C21.9183 22.9412 21.9136 22.9647 21.9044 22.9866C21.8953 23.0085 21.8819 23.0283 21.865 23.045C21.4775 23.43 20.87 23.6175 20.0058 23.6175L19.9992 23.6158L19.9925 23.6175C19.1292 23.6175 18.5208 23.43 18.1333 23.0442C18.1165 23.0276 18.1031 23.0078 18.094 22.986C18.0848 22.9642 18.0801 22.9407 18.0801 22.9171C18.0801 22.8934 18.0848 22.87 18.094 22.8482C18.1031 22.8264 18.1165 22.8066 18.1333 22.79C18.1675 22.7565 18.2134 22.7377 18.2612 22.7377C18.3091 22.7377 18.355 22.7565 18.3892 22.79C18.705 23.1042 19.2292 23.2575 19.9925 23.2575L19.9992 23.2592L20.0058 23.2575C20.7683 23.2575 21.2925 23.1042 21.6092 22.79C21.6433 22.7565 21.6892 22.7377 21.7371 22.7377C21.7849 22.7377 21.8309 22.7565 21.865 22.79ZM18.9983 20.775C18.9983 20.5711 18.9173 20.3756 18.7732 20.2314C18.629 20.0872 18.4335 20.0062 18.2296 20.0062C18.0257 20.0062 17.8302 20.0872 17.686 20.2314C17.5418 20.3756 17.4608 20.5711 17.4608 20.775C17.4608 21.1967 17.8058 21.54 18.23 21.54C18.3307 21.5402 18.4304 21.5206 18.5235 21.4823C18.6166 21.444 18.7013 21.3877 18.7726 21.3166C18.844 21.2456 18.9006 21.1612 18.9393 21.0683C18.9781 20.9753 18.9981 20.8757 18.9983 20.775ZM30 20C30 25.5225 25.5225 30 20 30C14.4775 30 10 25.5225 10 20C10 14.4775 14.4775 10 20 10C25.5225 10 30 14.4775 30 20ZM25.8333 19.8925C25.8325 19.6396 25.7574 19.3924 25.6173 19.1819C25.4771 18.9713 25.2782 18.8066 25.0451 18.7082C24.8121 18.6098 24.5553 18.5821 24.3067 18.6285C24.058 18.6749 23.8285 18.7933 23.6467 18.9692C22.7667 18.39 21.5758 18.0217 20.2583 17.9742L20.9792 15.7042L22.9317 16.1617L22.9292 16.19C22.9292 16.77 23.4033 17.2417 23.9858 17.2417C24.5683 17.2417 25.0417 16.77 25.0417 16.19C25.0415 15.944 24.9554 15.7057 24.7982 15.5165C24.6411 15.3272 24.4227 15.1988 24.1808 15.1536C23.939 15.1083 23.689 15.1489 23.474 15.2685C23.2589 15.3881 23.0925 15.579 23.0033 15.8083L20.8992 15.315C20.8548 15.3042 20.808 15.3107 20.7682 15.3331C20.7285 15.3556 20.6987 15.3923 20.685 15.4358L19.8808 17.9675C18.5008 17.9842 17.2517 18.3558 16.3325 18.9517C16.149 18.781 15.9197 18.6675 15.6726 18.6251C15.4256 18.5828 15.1716 18.6134 14.9417 18.7133C14.7118 18.8132 14.516 18.9779 14.3784 19.1874C14.2407 19.3968 14.1671 19.6419 14.1667 19.8925C14.1667 20.3642 14.4258 20.7725 14.8067 20.9967C14.7817 21.1333 14.765 21.2725 14.765 21.4133C14.765 23.3142 17.1025 24.8608 19.9758 24.8608C22.8492 24.8608 25.1867 23.3142 25.1867 21.4133C25.1867 21.28 25.1725 21.1492 25.15 21.02C25.555 20.8025 25.8333 20.3817 25.8333 19.8925ZM21.7733 20.01C21.5699 20.0098 21.3747 20.0903 21.2307 20.2339C21.0866 20.3775 21.0054 20.5724 21.005 20.7758C21.005 20.9796 21.0859 21.175 21.23 21.3191C21.3741 21.4632 21.5696 21.5442 21.7733 21.5442C21.9771 21.5442 22.1725 21.4632 22.3166 21.3191C22.4607 21.175 22.5417 20.9796 22.5417 20.7758C22.5412 20.5724 22.4601 20.3775 22.316 20.2339C22.1719 20.0903 21.9767 20.0098 21.7733 20.01Z" fill="#FF4500" fill-rule="evenodd"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7519">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Reddit</span>
                          </div>
                        </a>
                      </div>
                    </div>
                  </li>
              </div>
              <div class="explore-more-teg mt-4">
                <h3 class="mb-4">Explore more</h3>
                <div class="d-flex" style="flex-wrap: wrap;">`;
                search_category_arr.forEach((element,index) => {
                  if(index < 21){
                    template_data += `<div class="explore-sub-teg tab-explore" style="margin:0 10px 10px 0;" onclick="seachTemplateByTag('${element}')">${element}</div>`
                  }
                });
              template_data += `</div>
              </div>`;
    let template_heading = "";
    template_heading = "Recommended Templates";
    if(multiple_images_arr.length > 1){
      $("#template_slider").html(sliderRenderText);
      $(".sub-template-page").css("height","auto");
    }
    else{
      $("#template_slider").html('');
      $(".sub-template-page").css("height","0px");
    }

    $("#active_temp_id").html(active_template);
    // $("#active_temp_id").css("margin-top","36px");
    $(".Puzyyw").html(template_data);
    $(".template-heading").html(template_heading);
    $(".template-heading").css("margin-top","42px");
    $("#sliderImgLoad0").addClass("c-active-tmp");

    setTimeout(() => {
      if(multiple_images_arr.length > 1){
        if (document.getElementById("template_slider").clientWidth > document.getElementById("ttcw").clientWidth) {
          $("#ttcbright").show();
        }
      }
    }, 500);
    $(".sub-template-page").css("margin-top","");
    $(".Puzyyw-v").html('');
  }

  function renderVideoTemplates(results){
    setTimeout(() => {
      if(results.data.content_details[0].width < 120){
        $("#activeVideoTemplate").addClass('activeContentBox');
        $("#custom-seekbar-mdl").css("bottom","22px");
        $("#custom-seekbar-mdl").parent().css("margin-top","5px");
      }
      else{
        $("#activeVideoTemplate").removeClass('activeContentBox');
        $("#custom-seekbar-mdl").css("bottom","22px");
      }
    }, 100);
    setTimeout(() => {
      $("#activeVideoTemplateShimmer").css("display","none")
      $("#activeVideoTemplate").css({"display":"flex","margin":"auto"})
      $("#video").css("width","auto");
    }, 1000);

    let templateRenderText = "";
    template_response = results;

    template_list = results.data.content_details
    let editroUrl;
    if (template_list[0].content_type == 9) {
      editroUrl = "https://photoadking.com/app/#/video-editor/";
    } else if (template_list[0].content_type == 10) {
      editroUrl = "https://photoadking.com/app/#/intro-editor/";
    }
    let tempStr = "";
    $('.video-recommended-templates').html('');
    for (var k = 0; k < template_list.length; k++) {
      let overlay_class = 'crd-overlay-div';
      let templateName = template_list[k].template_name
      let isTemplateNameFound = template_list[k].template_name && template_list[k].template_name != undefined && template_list[k].template_name != null && template_list[k].template_name != "";
      let className = isTemplateNameFound == true?'template-name-found':'';
      let imageClassName = isTemplateNameFound == true?'marquee-image':'';
      let temp_param =  "'" + editroUrl+"','" + template_list[k].sub_category_id + "','" + template_list[k].catalog_id + "','" + template_list[k].content_id + "','" + template_list[k].content_id +"'";
      let _id = `dialogTemp${template_list[k].content_id}`;

      if(k != 0){
        if ($(document).width() > 768){
          let tempStr = '<div class="content-item card-ol-block cursor-pointer cust-temp-item-new template-preview-tab" style="border: 0 !important;border-radius:8px;"><div style="border: 1px solid #C4C4C4 !important;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;height:100%;display:flex;flex-direction:column;" onmouseenter="showVideo(\''+_id+'\')" onmouseleave="hideVideo(\''+_id+'\')"><div id = "card_top' + k + '" onclick="dialogVideoPreview(\''+ k +'\',\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].content_file +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\')" class="img_wrper cursor-pointer temp-img prograssive_img '+imageClassName+'"  style="display:flex;flex-grow:1;max-width:100%;width:100%;max-height:550px;"><img loading="lazy" style="display:block;margin-top:auto;margin-bottom:auto;max-width:100%;border-radius: 0px !important;border:0px;transition:.1s ease;" onload="removeShimmer(idialogTemp' +template_list[k].content_id + ')" draggable="false" id ="idialogTemp' + template_list[k].content_id + '" src="' +
              template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
              '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video"  style="" id = "vdialogTemp' + template_list[k].content_id + '" loop muted playsinline preload="metadata"><source draggable="false" type="video/mp4" src="' +
              template_list[k].content_file +
              '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldrdialogTemp' + template_list[k].content_id + '" style="top: 8px;left: 10px;"></div><div id = "playButtondialogTemp' + template_list[k].content_id + '" class= "play-btn-ic" style="display:block !important;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="right:10px;border-radius:0;"></div><div class= "seekbar-container" id="seekbardialogTemp' + template_list[k].content_id
              + '" style="width:100%;"><div class="custom-seekbar" id="custom-seekbardialogTemp' + template_list[k].content_id + '" ><span id="csdialogTemp' + template_list[k].content_id + '"></span></div></div></div><a class= "editVideo-txt" onclick="storeDetails(\'' +
              template_list[k].sub_category_id  + "', '" + template_list[k].catalog_id + "', '" + template_list[k]
                      .content_id +
              '\','+ template_list[0].content_type +')" href="' + editroUrl +
              template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
              '" target="_blank">';
          if(isTemplateNameFound == true){
            tempStr += '<div class="edit-video-button mx-auto marque-video '+ overlay_class+'"  id = "editButtondialogTemp' + template_list[k].content_id+ '" style="margin:0 !important;width: 100% !important;height:32px !important;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px;margin-right:10px;"><span>EDIT VIDEO</span></div></a><div class="template-name-title" style="height:30px;">'+ template_list[k].template_name +'</div></div>';
          }else{
            tempStr += '<div class="edit-video-button mx-auto '+ overlay_class+'"  id = "editButtondialogTemp' + template_list[k].content_id+ '" style="margin:0 !important;width: 100% !important;height:32px !important;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px; margin-right:10px;"><span>EDIT VIDEO</span></div></a></div>';
          }
          $('.video-recommended-templates').append(tempStr);
        }
        else{
          let tempStr = '<div class="content-item card-ol-block cursor-pointer cust-temp-item-new template-preview-tab" style="border: 0 !important;border-radius:8px;"><a class= "editVideo-txt" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/app/#/" target="_blank"><div style="border: 1px solid #C4C4C4 !important;border-radius: 8px 8px 8px 8px;overflow: hidden;width:100%;height:100%;display:flex;flex-direction:column;"><div id = "card_top' + k + '" class="img_wrper cursor-pointer temp-img prograssive_img '+imageClassName+'"  style="display:flex;flex-grow:1;max-width:100%;width:100%;max-height:550px;"><img loading="lazy" style="display:block;margin-top:auto;margin-bottom:auto;max-width:100%;border-radius: 0px !important;border:0px;transition:.1s ease;" onload="removeShimmer(idialogTemp' +template_list[k].content_id + ')" draggable="false" id ="idialogTemp' + template_list[k].content_id + '" src="' +
              template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
              '" alt="' + templateName + '" data-isLoaded="false" ><video class="mx-auto template-video"  style="" id = "vdialogTemp' + template_list[k].content_id + '" loop muted playsinline preload="metadata"><source draggable="false" type="video/mp4" src="' +
              template_list[k].content_file +
              '"  ></video><div><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Spinner1.gif" class="video-loader" id = "ldrdialogTemp' + template_list[k].content_id + '" style="top: 8px;left: 10px;"></div><div id = "playButtondialogTemp' + template_list[k].content_id + '" class= "play-btn-ic" style="display:block !important;" onclick="event.stopPropagation(); dialogVideoPreview(\''+ k +'\',\''+ template_list[k].sub_category_id +'\',\''+ template_list[k].content_id +'\',\''+ template_list[k].content_file +'\',\''+ template_list[k].height +'\',\''+ template_list[k].width +'\');"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/eyes.svg" alt="play icon" class="playButton-ic border-eyes" style="right:5px;top:5px;border-radius:0;width:26px !important;height:26px !important;"></div><div class= "seekbar-container" id="seekbardialogTemp' + template_list[k].content_id
              + '" style="width:100%;"><div class="custom-seekbar" id="custom-seekbardialogTemp' + template_list[k].content_id + '" ><span id="csdialogTemp' + template_list[k].content_id + '"></span></div></div></div>';
          if(isTemplateNameFound == true){
            tempStr += '<div class="edit-video-button mx-auto marque-video '+ overlay_class+'"  id = "editButtondialogTemp' + template_list[k].content_id+ '" style="margin:0 !important;width: 100% !important;height:32px !important;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px;margin-right:10px;"><span>EDIT VIDEO</span></div><div class="template-name-title" style="height:27px;">'+ template_list[k].template_name +'</div></div></a></div>';
          }else{
            tempStr += '<div class="edit-video-button mx-auto '+ overlay_class+'"  id = "editButtondialogTemp' + template_list[k].content_id+ '" style="margin:0 !important;width: 100% !important;height:32px !important;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><img loading="lazy" src="{!! config('constant.CDN_STATIC_WEB_ASSETS_PATH') !!}/Edit.svg" alt="edit icon" class="editButton-ic" style="border:0px; margin-right:10px;"><span>EDIT VIDEO</span></div></div></a></div>';
          }
          $('.video-recommended-templates').append(tempStr);


          $("#floatingBtn").addClass("fadeInDown");
          $("#floatingBtn").removeClass("fadeInUp");
        }
      }
    }
    const userAgent = window.navigator.userAgent;
    if (/Android/i.test(userAgent)) {
      $(".video-recommended-templates").css("margin-bottom","125px");
    } else if (/iPhone/i.test(userAgent)) {
      $(".video-recommended-templates").css("margin-bottom","125px");
    } else if (navigator.platform == 'Mac68K' || navigator.platform == 'MacPPC' || navigator.platform =='MacIntel') {
      $(".video-recommended-templates").css("margin-bottom","100px");
    }
    else if((navigator.appVersion).match(/OS (\d+)(\d+)?(\d+)?/)){
      $(".video-recommended-templates").css("margin-bottom","100px");
    }
    template_list.forEach((template, index) => {
      let card_width = $("#card_top"+index).width();
      let ratio = template.height / template.width;
      if( card_width >= template.width){
        card_height = template.height;
      }
      else{
        card_height = (card_width * ratio) < 551 ? card_width * ratio : 550;
      }
      $("#card_top"+index).css("height",card_height);
      $("#card_top"+index).children("img").css("height",card_height)
    });

    let active_template = "";
    results.data.content_details.forEach((template, index) => {
      if(index == 0){
        template_name = template.template_name;
        sub_category_name = template.sub_category_name;
        is_free = template.is_free;
        template_height = template.height * 2;
        template_width = template.width * 2;
        sub_cat_id = template.sub_category_id;
        catalog_id = template.catalog_id;
        content_id = template.content_id;
        content_type = template.content_type;
        search_category_arr = [];
        search_category_arr_with_empty_element = template.search_category.split(',');
        search_category_arr_with_empty_element.forEach(element => {
          if(element.trim() != ''){
            search_category_arr.push(element);
          }
        });
      }
    });

    let template_data = "";
    template_data = `
              <div>
                <div>
                  <div class="_8VoL_g d-flex" style="justify-content: space-between;">
                    <span class="USE2Rg CWAxhQ _H6mgQ p-0">`;

      if(is_free == 0){
        template_data += `
        <span aria-hidden="true" class="edzEug uRWxVA dkWypw">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_13011_38602)">
                          <path d="M16.9999 8.50001C17.0011 8.06531 16.8588 7.64235 16.5952 7.29672C16.5115 7.18677 16.454 7.05914 16.4272 6.92359C16.4003 6.78804 16.4048 6.64814 16.4402 6.51458C16.5505 6.0942 16.5203 5.64927 16.354 5.24771C16.1878 4.84615 15.8947 4.51 15.5196 4.29059C15.3999 4.22088 15.2978 4.12478 15.2209 4.0096C15.144 3.89442 15.0945 3.76322 15.076 3.62598C15.0171 3.19523 14.8187 2.79563 14.5112 2.48826C14.2037 2.1809 13.804 1.98267 13.3732 1.92388C13.2361 1.9055 13.1049 1.85606 12.9897 1.77931C12.8745 1.70257 12.7784 1.60056 12.7086 1.48104C12.4897 1.10577 12.154 0.81252 11.7527 0.646133C11.3513 0.479746 10.9066 0.449381 10.4864 0.559682C10.4109 0.579342 10.3332 0.589333 10.2552 0.589415C10.0562 0.589137 9.86272 0.524194 9.70389 0.404373C9.35803 0.140695 8.9348 -0.00144604 8.49989 1.10923e-05L6.50781 8.50001L8.49989 17C8.9346 17.0012 9.35756 16.859 9.70319 16.5953C9.86216 16.4752 10.0559 16.41 10.2552 16.4096C10.3328 16.4096 10.4102 16.4196 10.4853 16.4393C10.6507 16.4827 10.8209 16.5047 10.9918 16.5047C11.3392 16.5042 11.6804 16.4129 11.9817 16.24C12.2829 16.067 12.5337 15.8184 12.7093 15.5186C12.779 15.399 12.8751 15.2968 12.9903 15.22C13.1055 15.1431 13.2367 15.0936 13.3739 15.0751C13.8045 15.0162 14.204 14.8179 14.5114 14.5107C14.8187 14.2034 15.017 13.8039 15.076 13.3733C15.0944 13.2362 15.1439 13.105 15.2206 12.9898C15.2973 12.8747 15.3994 12.7785 15.5189 12.7087C15.8939 12.4896 16.1869 12.1538 16.3533 11.7526C16.5196 11.3513 16.5502 10.9067 16.4402 10.4865C16.4049 10.3528 16.4005 10.2128 16.4274 10.0771C16.4543 9.94148 16.5118 9.81375 16.5955 9.70366C16.8593 9.35795 17.0014 8.93482 16.9999 8.50001Z" fill="#0F9AF0"></path>
                          <path d="M7.29643 0.40472C7.13745 0.524866 6.94372 0.590058 6.74445 0.590461C6.66678 0.59042 6.58942 0.580428 6.51429 0.560728C6.34894 0.517371 6.17872 0.495387 6.00778 0.495317C5.66042 0.495824 5.31921 0.587076 5.01796 0.760034C4.71671 0.932992 4.46587 1.18165 4.2903 1.48139C4.22062 1.60067 4.12468 1.70251 4.00976 1.77918C3.89485 1.85585 3.76398 1.90533 3.62709 1.92388C3.19601 1.9824 2.79601 2.18061 2.48833 2.48816C2.18066 2.7957 1.98228 3.19563 1.92359 3.62668C1.90517 3.76388 1.85567 3.89507 1.77887 4.01025C1.70207 4.12542 1.59998 4.22154 1.4804 4.29129C1.10544 4.51048 0.812478 4.8463 0.64619 5.24753C0.479901 5.64876 0.449418 6.09336 0.559392 6.51353C0.59473 6.64724 0.599109 6.78724 0.572196 6.9229C0.545284 7.05855 0.487788 7.18628 0.404084 7.29637C0.141287 7.64254 -0.000976563 8.06522 -0.000976562 8.49984C-0.000976563 8.93446 0.141287 9.35714 0.404084 9.70331C0.487847 9.81323 0.545384 9.94084 0.5723 10.0764C0.599217 10.212 0.594802 10.3519 0.559392 10.4855C0.449087 10.9058 0.479362 11.3508 0.645596 11.7523C0.81183 12.1539 1.10489 12.49 1.48005 12.7094C1.59953 12.779 1.70157 12.8749 1.77843 12.9898C1.85529 13.1047 1.90493 13.2357 1.92359 13.3727C1.98211 13.8037 2.18032 14.2037 2.48787 14.5114C2.79541 14.8191 3.19534 15.0175 3.62639 15.0762C3.76359 15.0946 3.89478 15.1441 4.00995 15.2209C4.12513 15.2977 4.22125 15.3998 4.291 15.5193C4.50993 15.8946 4.84572 16.1877 5.24703 16.3541C5.64834 16.5204 6.09307 16.5507 6.51324 16.4404C6.64692 16.4054 6.78679 16.4012 6.92233 16.4281C7.05786 16.455 7.18553 16.5123 7.29573 16.5957C7.64159 16.8593 8.06481 17.0015 8.49972 17V7.56167e-06C8.06501 -0.00119497 7.64205 0.141062 7.29643 0.40472Z" fill="#13BDF7"></path>
                          <path d="M8.49999 2.02539L7.07178 8.50008L8.49999 14.9748C10.2172 14.9748 11.864 14.2926 13.0783 13.0784C14.2925 11.8641 14.9747 10.2173 14.9747 8.50008C14.9747 6.78289 14.2925 5.13602 13.0783 3.92178C11.864 2.70754 10.2172 2.02539 8.49999 2.02539Z" fill="#085AAE"></path>
                          <path d="M2.02539 8.50008C2.02539 10.2173 2.70754 11.8641 3.92178 13.0784C5.13603 14.2926 6.78289 14.9748 8.50009 14.9748V2.02539C6.78289 2.02539 5.13603 2.70754 3.92178 3.92178C2.70754 5.13602 2.02539 6.78289 2.02539 8.50008Z" fill="#0A77E8"></path>
                          <path d="M8.50012 5.44824L7.50391 8.93464L13.0303 7.93843L11.7466 5.44824H8.50012Z" fill="#B2F5FF"></path>
                          <path d="M8.49992 5.44824H5.25347L3.96973 7.93843L8.49992 8.93464V5.44824Z" fill="white"></path>
                          <path d="M8.50012 13.1459L13.0303 7.93848H8.50012L7.50391 10.542L8.50012 13.1459Z" fill="#18E0FF"></path>
                          <path d="M3.96973 7.93848L8.49992 13.1459V7.93848H3.96973Z" fill="#B2F5FF"></path>
                          </g>
                          <defs>
                          <clipPath id="clip0_13011_38602">
                          <rect width="17" height="17" fill="white"></rect>
                          </clipPath>
                          </defs>
                        </svg>
                      </span> PRO `;
      }
      template_data += `</span>
                  </div>
                </div>
                <div class="w9feWg mt-3">
                  <h1 class="explore-heading">${template_name}</h1>
                </div>
                <p class="sub-heading-template m-0 pt-2">${sub_category_name}  |  ${template_width} x ${template_height} px</p>
                <div class="d-flex mt-4 align-items-center position-relative">
                  <button onclick="redirectCustomize('${sub_cat_id}','${catalog_id}','${content_id}','${content_type}')" class="template-btn-customize">Customize this template</button>
                  <span class="ml-3 sharebtnHover" onclick="showPopup('#shareoption2')" onmouseenter="showShareBtnTooltip()" onmouseleave="hideShareBtnTooltip()" style="cursor: pointer;height: 44px;width: 48px;border-radius: 5px;display: flex;justify-content: center;align-items: center;">
                      <svg id="sharebtn2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M18.7033 0.0747833C18.3939 0.0911903 18.0564 0.149784 17.7095 0.250565C16.5494 0.583378 15.5298 1.42947 14.9861 2.51228C14.6767 3.12869 14.5267 3.7615 14.5267 4.45291C14.5267 4.86541 14.5525 5.08103 14.6486 5.47713C14.6837 5.62947 14.7119 5.75603 14.7095 5.76072C14.6908 5.77947 8.45874 9.18728 8.44233 9.18728C8.43296 9.18728 8.36733 9.124 8.29937 9.04432C7.87749 8.56619 7.15562 8.09275 6.50405 7.86775C5.97905 7.68728 5.5314 7.61228 4.9689 7.61228C3.4478 7.61463 2.05562 8.36697 1.21187 9.64431C0.438428 10.8162 0.29546 12.349 0.834522 13.6615C1.15562 14.4396 1.76733 15.1779 2.48452 15.6513C3.99624 16.6498 5.97671 16.6545 7.47671 15.6631C7.78843 15.4568 7.97358 15.3045 8.24077 15.0303L8.45405 14.8123L11.1939 16.3146C12.7009 17.1396 14.1142 17.9154 14.3345 18.0373L14.7353 18.2576L14.6673 18.5084C14.4869 19.1928 14.4892 19.931 14.6697 20.6412C14.883 21.4732 15.3845 22.2842 16.0314 22.8467C16.4181 23.1842 16.9876 23.5217 17.433 23.681C17.7025 23.7771 18.1033 23.8779 18.3869 23.9178C18.7126 23.9646 19.3384 23.967 19.6455 23.9201C20.6228 23.7701 21.4619 23.3599 22.1439 22.7013C23.3017 21.5834 23.7494 19.9943 23.3439 18.4474C23.1001 17.524 22.54 16.6873 21.7666 16.0967C20.4095 15.0584 18.5673 14.8732 17.0275 15.6185C16.5611 15.8435 16.0126 16.2467 15.7056 16.5888L15.565 16.7459L15.4759 16.6967C15.4267 16.6709 14.0134 15.8951 12.333 14.974C9.39624 13.3685 9.27905 13.3006 9.28843 13.242C9.29546 13.2092 9.32593 13.0685 9.35874 12.9303C9.51108 12.2834 9.49702 11.5803 9.3189 10.9076L9.26499 10.7084L9.45483 10.6053C9.5603 10.549 10.9712 9.77557 12.5931 8.88728L15.5439 7.27244L15.7923 7.52322C17.1001 8.83338 19.0267 9.21541 20.7517 8.50994C21.5298 8.19119 22.2705 7.58182 22.7509 6.86697C23.6134 5.5826 23.7189 3.9115 23.0275 2.53103C22.3666 1.21385 21.0986 0.309158 19.6291 0.107595C19.2986 0.0630646 19.0666 0.05369 18.7033 0.0747833ZM19.5822 1.83494C20.1072 1.94978 20.5876 2.21463 20.972 2.599C21.2603 2.88728 21.4666 3.21775 21.6025 3.60916C21.708 3.90916 21.7384 4.099 21.7384 4.46463C21.7384 4.83025 21.708 5.0201 21.6025 5.3201C21.2837 6.23182 20.5197 6.89275 19.5353 7.10603C19.4509 7.12244 19.2376 7.1365 19.008 7.1365C18.483 7.1365 18.183 7.06619 17.7376 6.83885C16.4462 6.1826 15.9119 4.6615 16.5142 3.35369C16.7556 2.82869 17.2173 2.35056 17.7423 2.08572C17.9791 1.96619 18.2791 1.86306 18.4923 1.82791C18.5884 1.81384 18.6845 1.79744 18.7033 1.79275C18.79 1.77166 19.4345 1.80213 19.5822 1.83494ZM5.47515 9.36306C6.3353 9.51072 7.06421 10.0545 7.45562 10.8396C7.81655 11.5662 7.82827 12.3795 7.48608 13.1201C7.32671 13.467 6.99858 13.8771 6.67983 14.1209C6.37046 14.3576 5.93687 14.5592 5.55483 14.6388C5.28062 14.6974 4.70405 14.6974 4.42983 14.6388C3.88608 14.524 3.40327 14.2545 3.01187 13.8467C2.77749 13.6053 2.67671 13.467 2.54312 13.2068C2.35796 12.8412 2.27827 12.5412 2.25718 12.117C2.22202 11.3763 2.49155 10.6826 3.02358 10.1482C3.43374 9.73338 3.93765 9.46385 4.48843 9.36541C4.74155 9.32088 5.21968 9.31853 5.47515 9.36306ZM19.5822 16.931C20.2806 17.0857 20.9158 17.5123 21.2884 18.0771C21.4478 18.3162 21.5158 18.4498 21.6025 18.7029C21.708 19.0029 21.7384 19.1928 21.7384 19.5584C21.7384 19.924 21.708 20.1138 21.6025 20.4138C21.3259 21.2084 20.7166 21.8084 19.8986 22.0943C19.5892 22.2045 19.4111 22.2303 18.9962 22.2303C18.6025 22.2279 18.4689 22.2092 18.1525 22.1084C17.1869 21.799 16.4462 20.9459 16.3009 19.9732C16.1509 18.9818 16.5447 18.0256 17.3509 17.4185C17.6533 17.1935 18.1291 16.9849 18.4806 16.924C18.558 16.9099 18.647 16.8935 18.6798 16.8888C18.8041 16.8654 19.4251 16.8959 19.5822 16.931Z" fill="#9A9A9A"></path>
                      </svg>
                      <div class="shareTooltip" style="display:none;">Share</div>
                  </span>
                  <li class="position-absolute" id="shareoption2" style="background-color: #ffffff !important;padding: 15px 16px !important;width: 342px;top: 50px;right: -25px;list-style: none;border-radius: 7px;box-shadow: 1px 3px 7px 0px rgba(0, 0, 0, 0.25);display:none;z-index:100000;">
                    <span class="mb-3 sharing-menu">Sharing Options</span>
                    <div class="d-flex mt-3" style="flex-wrap: wrap;">
                      <div onclick="copyTemplateURL('copyVideoTempURL','${sub_cat_id}','${content_id}')" class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover cursor-pointer">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#EFF0F1" height="40" rx="20" width="40"></rect>
                              <path clip-rule="evenodd" d="M27.7775 10H12.2225C11.6331 10 11.0678 10.2342 10.651 10.651C10.2342 11.0678 10 11.6331 10 12.2225V27.7775C10 28.3669 10.2342 28.9322 10.651 29.349C11.0678 29.7658 11.6331 30 12.2225 30H27.7775C28.3669 30 28.9322 29.7658 29.349 29.349C29.7658 28.9322 30 28.3669 30 27.7775V12.2225C30 11.6331 29.7658 11.0678 29.349 10.651C28.9322 10.2342 28.3669 10 27.7775 10ZM11.6667 12.2225C11.6667 11.915 11.915 11.6667 12.2225 11.6667H21.1108V28.3333H12.2225C12.1495 28.3333 12.0772 28.319 12.0098 28.291C11.9424 28.2631 11.8811 28.2221 11.8295 28.1705C11.7779 28.1189 11.7369 28.0576 11.709 27.9902C11.681 27.9228 11.6667 27.8505 11.6667 27.7775V12.2225ZM22.7775 17.7775V11.6667H27.7775C28.085 11.6667 28.3333 11.915 28.3333 12.2225V17.7775H22.7775ZM22.7775 19.4442V28.3333H27.7775C27.8505 28.3333 27.9228 28.319 27.9902 28.291C28.0576 28.2631 28.1189 28.2221 28.1705 28.1705C28.2221 28.1189 28.2631 28.0576 28.291 27.9902C28.319 27.9228 28.3333 27.8505 28.3333 27.7775V19.4442H22.7775Z" fill="#575D68" fill-rule="evenodd"></path>
                            </svg>
                            <span class="sharing-operation">Copy link</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://twitter.com/intent/tweet?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing.&amp;&amp;" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E4F5FD" height="40" rx="20" width="40"></rect>
                              <path d="M30 13.9238C29.2563 14.25 28.4637 14.4662 27.6375 14.5712C28.4875 14.0638 29.1363 13.2662 29.4412 12.305C28.6488 12.7775 27.7738 13.1113 26.8412 13.2975C26.0887 12.4963 25.0162 12 23.8462 12C21.5763 12 19.7487 13.8425 19.7487 16.1013C19.7487 16.4262 19.7762 16.7387 19.8438 17.0362C16.435 16.87 13.4188 15.2363 11.3925 12.7475C11.0387 13.3613 10.8313 14.0638 10.8313 14.82C10.8313 16.24 11.5625 17.4987 12.6525 18.2275C11.9937 18.215 11.3475 18.0238 10.8 17.7225C10.8 17.735 10.8 17.7512 10.8 17.7675C10.8 19.76 12.2212 21.415 14.085 21.7962C13.7512 21.8875 13.3875 21.9312 13.01 21.9312C12.7475 21.9312 12.4825 21.9163 12.2337 21.8612C12.765 23.485 14.2725 24.6788 16.065 24.7175C14.67 25.8088 12.8988 26.4662 10.9813 26.4662C10.645 26.4662 10.3225 26.4513 10 26.41C11.8162 27.5813 13.9688 28.25 16.29 28.25C23.835 28.25 27.96 22 27.96 16.5825C27.96 16.4012 27.9538 16.2263 27.945 16.0525C28.7588 15.475 29.4425 14.7538 30 13.9238Z" fill="#1DA1F2"></path>
                            </svg>
                            <span class="sharing-operation">Twitter</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.facebook.com/sharer/sharer.php?u={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E6EEFF" height="40" rx="20" width="40"></rect>
                              <rect fill="#323999" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path clip-rule="evenodd" d="M18.3798 20.0022V26.2518C18.3798 26.3465 18.4429 26.4097 18.5376 26.4097H20.8733C20.968 26.4097 21.0311 26.3465 21.0311 26.2518V19.9075H22.704C22.7987 19.9075 22.8619 19.8444 22.8619 19.7497L23.0197 17.8243C23.0197 17.7296 22.9565 17.6349 22.8619 17.6349H21.0311V16.3092C21.0311 15.9936 21.2837 15.741 21.5993 15.741H22.8934C22.9881 15.741 23.0512 15.6779 23.0512 15.5832V13.6578C23.0512 13.5631 22.9881 13.5 22.8934 13.5H20.7155C19.4214 13.5 18.3798 14.5416 18.3798 15.8357V17.6664H17.2119C17.1172 17.6664 17.0541 17.7296 17.0541 17.8243V19.7497C17.0541 19.8444 17.1172 19.9075 17.2119 19.9075H18.3798V20.0022Z" fill="white" fill-rule="evenodd"></path>
                            </svg>
                            <span class="sharing-operation">Facebook</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.instagram.com/photoadking/" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="url(#paint0_linear_1360_2071)" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1360_2071)">
                                <path d="M24.0436 10H15.9601C12.6715 10 10 12.6715 10 15.9601V24.0436C10 27.3285 12.6715 30 15.9601 30H24.0436C27.3285 30 30.0036 27.3285 30.0036 24.0399V15.9601C30 12.6715 27.3285 10 24.0436 10ZM27.9891 24.0436C27.9891 26.2214 26.2214 27.9891 24.0436 27.9891H15.9601C13.7822 27.9891 12.0145 26.2214 12.0145 24.0436V15.9601C12.0145 13.7822 13.7822 12.0145 15.9601 12.0145H24.0436C26.2214 12.0145 27.9891 13.7822 27.9891 15.9601V24.0436Z" fill="url(#paint1_linear_1360_2071)"></path>
                                <path d="M20 14.8271C17.147 14.8271 14.8276 17.1466 14.8276 19.9996C14.8276 22.8526 17.147 25.172 20 25.172C22.853 25.172 25.1724 22.8526 25.1724 19.9996C25.1724 17.1466 22.853 14.8271 20 14.8271ZM20 23.1611C18.2541 23.1611 16.8385 21.7455 16.8385 19.9996C16.8385 18.2536 18.2541 16.838 20 16.838C21.7459 16.838 23.1615 18.2536 23.1615 19.9996C23.1615 21.7455 21.7459 23.1611 20 23.1611Z" fill="url(#paint2_linear_1360_2071)"></path>
                                <path d="M25.1833 16.1054C25.8669 16.1054 26.4211 15.5512 26.4211 14.8676C26.4211 14.184 25.8669 13.6299 25.1833 13.6299C24.4997 13.6299 23.9456 14.184 23.9456 14.8676C23.9456 15.5512 24.4997 16.1054 25.1833 16.1054Z" fill="url(#paint3_linear_1360_2071)"></path>
                              </g>
                              <defs>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint0_linear_1360_2071" x1="40" x2="-2.38419e-06" y1="0" y2="40">
                                  <stop stop-color="#F1DBFA"></stop>
                                  <stop offset="0.494792" stop-color="#FDE8EB"></stop>
                                  <stop offset="1" stop-color="#FFFAE3"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint1_linear_1360_2071" x1="11.7952" x2="28.1258" y1="28.206" y2="11.8754">
                                  <stop stop-color="#F3CA5C"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint2_linear_1360_2071" x1="20.0006" x2="20.0006" y1="29.9426" y2="10.1549">
                                  <stop stop-color="#E09B3D"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <linearGradient gradientUnits="userSpaceOnUse" id="paint3_linear_1360_2071" x1="25.1837" x2="25.1837" y1="29.9431" y2="10.1555">
                                  <stop stop-color="#E09B3D"></stop>
                                  <stop offset="0.3" stop-color="#C74C4D"></stop>
                                  <stop offset="0.6" stop-color="#C21975"></stop>
                                  <stop offset="1" stop-color="#7024C4"></stop>
                                </linearGradient>
                                <clipPath id="clip0_1360_2071">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Instagram</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.linkedin.com/shareArticle?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;title=PhotoAdKing&amp;summary=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing." rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E0EFF6" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7534)">
                                <path d="M28.1908 10H11.8092C10.81 10 10 10.81 10 11.8092V28.1908C10 29.19 10.81 30 11.8092 30H28.1908C29.19 30 30 29.19 30 28.1908V11.8092C30 10.81 29.19 10 28.1908 10ZM16.1888 27.2693C16.1888 27.5601 15.9531 27.7958 15.6624 27.7958H13.4212C13.1304 27.7958 12.8947 27.5601 12.8947 27.2693V17.8745C12.8947 17.5837 13.1304 17.348 13.4212 17.348H15.6624C15.9531 17.348 16.1888 17.5837 16.1888 17.8745V27.2693ZM14.5418 16.4624C13.3659 16.4624 12.4127 15.5092 12.4127 14.3333C12.4127 13.1574 13.3659 12.2042 14.5418 12.2042C15.7176 12.2042 16.6709 13.1574 16.6709 14.3333C16.6709 15.5092 15.7177 16.4624 14.5418 16.4624ZM27.901 27.3117C27.901 27.5791 27.6843 27.7958 27.417 27.7958H25.012C24.7447 27.7958 24.528 27.5791 24.528 27.3117V22.905C24.528 22.2476 24.7208 20.0243 22.81 20.0243C21.3279 20.0243 21.0272 21.5461 20.9669 22.2291V27.3117C20.9669 27.5791 20.7502 27.7958 20.4828 27.7958H18.1568C17.8895 27.7958 17.6727 27.5791 17.6727 27.3117V17.8321C17.6727 17.5648 17.8895 17.348 18.1568 17.348H20.4828C20.7501 17.348 20.9669 17.5648 20.9669 17.8321V18.6517C21.5164 17.827 22.3332 17.1904 24.0722 17.1904C27.9231 17.1904 27.901 20.7881 27.901 22.7648V27.3117Z" fill="#0077B7"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7534">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">LinkedIn</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=&amp;caption=&amp;content={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;posttype=link&amp;shareSource=legacy&amp;title=&amp;url=http://www.google.com" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E8F4FF" height="40" rx="20" width="40"></rect>
                              <path d="M24.5182 25.9055C24.2919 26.1292 24.0227 26.3049 23.7268 26.422C23.431 26.5392 23.1145 26.5954 22.7964 26.5873C21.6691 26.5873 21.16 25.9055 21.16 24.8964V19.1782H24.7964V15.7236H21.16V10H18.4145C18.204 11.2457 17.6916 12.4208 16.922 13.4227C16.1525 14.4245 15.1492 15.2227 14 15.7473V19.1782H16.6782V25.7618C16.6782 26.6709 17.54 30 21.9309 30C24.5127 30 25.5818 28.3382 25.5818 28.3382L24.5182 25.9055Z" fill="#34526F"></path>
                            </svg>
                            <span class="sharing-operation">Tumblr</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://in.pinterest.com/pin/create/button/?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEEEF" height="40" rx="20" width="40"></rect>
                              <path d="M20 10C14.4828 10 10 14.4751 10 19.9828C10 24.0792 12.4483 27.5904 16 29.1395C15.9655 28.451 16 27.5904 16.1724 26.8331C16.3793 26.0069 17.4483 21.3942 17.4483 21.3942C17.4483 21.3942 17.1379 20.7402 17.1379 19.8107C17.1379 18.3305 18 17.2289 19.0689 17.2289C19.9655 17.2289 20.4138 17.9174 20.4138 18.7435C20.4138 19.6386 19.8276 21.0155 19.5172 22.2892C19.2759 23.3563 20.0345 24.2169 21.1034 24.2169C23 24.2169 24.2759 21.7728 24.2759 18.9156C24.2759 16.7125 22.7931 15.0946 20.1034 15.0946C17.0689 15.0946 15.1724 17.3666 15.1724 19.8795C15.1724 20.7401 15.4138 21.3597 15.8276 21.8416C16 22.0482 16.0345 22.1514 15.9655 22.3924C15.931 22.5645 15.7931 23.012 15.7586 23.1841C15.6896 23.4251 15.4827 23.5284 15.2414 23.4251C13.8276 22.8399 13.2069 21.3252 13.2069 19.6041C13.2069 16.7813 15.5862 13.3734 20.3448 13.3734C24.1724 13.3734 26.6896 16.1273 26.6896 19.0877C26.6896 23.012 24.5172 25.938 21.3104 25.938C20.2414 25.938 19.2069 25.3528 18.8621 24.6988C18.8621 24.6988 18.2759 27.0052 18.1724 27.4527C17.9655 28.21 17.5517 29.0017 17.1724 29.5869C18.069 29.8623 19.0345 30 20 30C25.5172 30 30 25.5249 30 20.0172C30 14.5094 25.5173 10 20 10Z" fill="#CB1F24"></path>
                            </svg>
                            <span class="sharing-operation">Pinterest</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn" style="margin-bottom: 16px;">
                        <a class="sharing-hover" href="https://www.youtube.com/@photoadking" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEBEC" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7567)">
                                <path d="M29.5898 15.1908C29.3594 14.3356 28.6836 13.66 27.8281 13.4296C26.2617 13 20 13 20 13C20 13 13.7383 13 12.1758 13.41C11.3359 13.6404 10.6445 14.3317 10.4141 15.1869C10 16.7568 10 19.998 10 19.998C10 19.998 10 23.2589 10.4102 24.8053C10.6406 25.6605 11.3164 26.3361 12.1719 26.5665C13.7539 26.9961 19.9961 26.9961 19.9961 26.9961C19.9961 26.9961 26.2578 26.9961 27.8203 26.5861C28.6758 26.3556 29.3516 25.6801 29.582 24.8248C30 23.2589 30 20.0176 30 20.0176C30 20.0176 30.0156 16.7568 29.5898 15.1908Z" fill="#FF0000"></path>
                                <path d="M18.0078 22.9974L23.2148 19.9982L18.0078 17.0029V22.9974Z" fill="white"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7567">
                                  <rect fill="white" height="14" transform="translate(10 13)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">YouTube</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://web.whatsapp.com/send?text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing.%0D%0A{!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}" rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E0F5DD" height="40" rx="20" width="40"></rect>
                              <rect fill="#29A71A" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path d="M24.5065 15.3791C23.4428 14.3049 22.0304 13.6461 20.5238 13.5216C19.0172 13.397 17.5158 13.815 16.2902 14.7C15.0647 15.585 14.1957 16.8789 13.8401 18.3481C13.4845 19.8174 13.6657 21.3654 14.3509 22.7129L13.6783 25.9784C13.6713 26.0109 13.6711 26.0445 13.6777 26.0771C13.6843 26.1097 13.6975 26.1406 13.7166 26.1678C13.7445 26.2091 13.7844 26.2409 13.8309 26.259C13.8774 26.277 13.9283 26.2805 13.9768 26.2688L17.1773 25.5103C18.521 26.1781 20.0581 26.3476 21.515 25.9886C22.9719 25.6295 24.2542 24.7652 25.1337 23.5495C26.0132 22.3338 26.4329 20.8454 26.318 19.3493C26.2032 17.8532 25.5612 16.4463 24.5065 15.3791ZM23.5086 23.3437C22.7726 24.0775 21.825 24.5619 20.7992 24.7287C19.7734 24.8954 18.7211 24.736 17.7907 24.273L17.3446 24.0523L15.3825 24.517L15.3883 24.4926L15.7949 22.5177L15.5765 22.0867C15.1011 21.153 14.9334 20.0928 15.0974 19.058C15.2614 18.0232 15.7487 17.0668 16.4896 16.3259C17.4204 15.3953 18.6828 14.8725 19.9991 14.8725C21.3153 14.8725 22.5777 15.3953 23.5086 16.3259C23.5165 16.335 23.525 16.3435 23.5341 16.3514C24.4534 17.2844 24.9667 18.543 24.9619 19.8528C24.9571 21.1626 24.4347 22.4174 23.5086 23.3437Z" fill="white"></path>
                              <path d="M23.3343 21.895C23.0938 22.2737 22.7139 22.7372 22.2365 22.8523C21.4001 23.0544 20.1164 22.8592 18.519 21.3699L18.4993 21.3525C17.0948 20.0502 16.73 18.9664 16.8183 18.1067C16.8671 17.6188 17.2737 17.1774 17.6164 16.8893C17.6706 16.843 17.7348 16.8101 17.804 16.7931C17.8732 16.7761 17.9454 16.7756 18.0148 16.7915C18.0842 16.8074 18.149 16.8394 18.2039 16.8848C18.2587 16.9302 18.3022 16.9878 18.3308 17.0531L18.8478 18.2148C18.8814 18.2901 18.8938 18.3731 18.8838 18.455C18.8738 18.5369 18.8417 18.6144 18.7909 18.6794L18.5295 19.0186C18.4734 19.0887 18.4396 19.1739 18.4323 19.2633C18.4251 19.3528 18.4448 19.4423 18.4888 19.5205C18.6352 19.7772 18.986 20.1548 19.3752 20.5045C19.812 20.8994 20.2964 21.2607 20.6031 21.3839C20.6852 21.4174 20.7754 21.4256 20.8622 21.4074C20.9489 21.3891 21.0282 21.3453 21.0899 21.2816L21.3931 20.9761C21.4516 20.9184 21.5243 20.8773 21.6039 20.8569C21.6835 20.8365 21.7671 20.8375 21.8461 20.8599L23.0741 21.2085C23.1418 21.2292 23.2039 21.2652 23.2556 21.3137C23.3073 21.3621 23.3472 21.4218 23.3723 21.488C23.3974 21.5543 23.407 21.6254 23.4005 21.6959C23.3939 21.7665 23.3712 21.8346 23.3343 21.895Z" fill="white"></path>
                            </svg>
                            <span class="sharing-operation">WhatsApp</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://t.me/share/url?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;text=Create%20awesome%20designs%20for%20your%20business%20using%20beautiful%20templates%20with%20PhotoAdKing." rel="noopener noreferrer" target="_blank">
                          <div class="text-center">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#E4F5FD" height="40" rx="20" width="40"></rect>
                              <rect fill="#25A2DF" height="20" rx="10" width="20" x="10" y="10"></rect>
                              <path d="M14.5588 19.1534L25.2008 15.0502C25.6947 14.8718 26.1261 15.1707 25.9661 15.9176L25.967 15.9167L24.155 24.4533C24.0207 25.0585 23.6611 25.2057 23.1579 24.9206L20.3985 22.8869L19.0676 24.1691C18.9204 24.3163 18.7963 24.4404 18.5111 24.4404L18.707 21.6323L23.8211 17.0121C24.0437 16.8162 23.7714 16.7059 23.478 16.9009L17.1581 20.8799L14.4337 20.03C13.8422 19.8424 13.8294 19.4386 14.5588 19.1534Z" fill="white"></path>
                            </svg>
                            <span class="sharing-operation">Telegram</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="mailto:?subject=https://photoadking.com/&amp;body=" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFEDD8" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7512)">
                                <path d="M21.6714 22.2536C21.1739 22.5853 20.5959 22.7607 20 22.7607C19.4041 22.7607 18.8262 22.5853 18.3286 22.2536L10.1332 16.7898C10.0877 16.7595 10.0434 16.7279 10 16.6954V25.6484C10 26.6749 10.833 27.4896 11.8411 27.4896H28.1588C29.1853 27.4896 30 26.6565 30 25.6484V16.6954C29.9565 16.728 29.9121 16.7596 29.8665 16.79L21.6714 22.2536Z" fill="#FF961C"></path>
                                <path d="M10.7832 15.8148L18.9786 21.2786C19.2889 21.4854 19.6444 21.5888 20 21.5888C20.3555 21.5888 20.7111 21.4854 21.0214 21.2786L29.2168 15.8148C29.7072 15.488 30 14.9411 30 14.3509C30 13.336 29.1743 12.5104 28.1595 12.5104H11.8405C10.8257 12.5104 10 13.336 10 14.3519C10 14.9411 10.2928 15.488 10.7832 15.8148Z" fill="#FF961C"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7512">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Email</span>
                          </div>
                        </a>
                      </div>
                      <div class="sharing-btn">
                        <a class="sharing-hover" href="https://www.reddit.com/submit?url={!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}&amp;=" rel="noopener noreferrer" target="_blank">
                          <div class="text-center px-3">
                            <svg class="sharing-icon" fill="none" height="40" viewBox="0 0 40 40" width="40" xmlns="http://www.w3.org/2000/svg">
                              <rect fill="#FFE8E0" height="40" rx="20" width="40"></rect>
                              <g clip-path="url(#clip0_1365_7519)">
                                <path clip-rule="evenodd" d="M21.865 22.79C21.8819 22.8067 21.8953 22.8265 21.9044 22.8484C21.9136 22.8703 21.9183 22.8938 21.9183 22.9175C21.9183 22.9412 21.9136 22.9647 21.9044 22.9866C21.8953 23.0085 21.8819 23.0283 21.865 23.045C21.4775 23.43 20.87 23.6175 20.0058 23.6175L19.9992 23.6158L19.9925 23.6175C19.1292 23.6175 18.5208 23.43 18.1333 23.0442C18.1165 23.0276 18.1031 23.0078 18.094 22.986C18.0848 22.9642 18.0801 22.9407 18.0801 22.9171C18.0801 22.8934 18.0848 22.87 18.094 22.8482C18.1031 22.8264 18.1165 22.8066 18.1333 22.79C18.1675 22.7565 18.2134 22.7377 18.2612 22.7377C18.3091 22.7377 18.355 22.7565 18.3892 22.79C18.705 23.1042 19.2292 23.2575 19.9925 23.2575L19.9992 23.2592L20.0058 23.2575C20.7683 23.2575 21.2925 23.1042 21.6092 22.79C21.6433 22.7565 21.6892 22.7377 21.7371 22.7377C21.7849 22.7377 21.8309 22.7565 21.865 22.79ZM18.9983 20.775C18.9983 20.5711 18.9173 20.3756 18.7732 20.2314C18.629 20.0872 18.4335 20.0062 18.2296 20.0062C18.0257 20.0062 17.8302 20.0872 17.686 20.2314C17.5418 20.3756 17.4608 20.5711 17.4608 20.775C17.4608 21.1967 17.8058 21.54 18.23 21.54C18.3307 21.5402 18.4304 21.5206 18.5235 21.4823C18.6166 21.444 18.7013 21.3877 18.7726 21.3166C18.844 21.2456 18.9006 21.1612 18.9393 21.0683C18.9781 20.9753 18.9981 20.8757 18.9983 20.775ZM30 20C30 25.5225 25.5225 30 20 30C14.4775 30 10 25.5225 10 20C10 14.4775 14.4775 10 20 10C25.5225 10 30 14.4775 30 20ZM25.8333 19.8925C25.8325 19.6396 25.7574 19.3924 25.6173 19.1819C25.4771 18.9713 25.2782 18.8066 25.0451 18.7082C24.8121 18.6098 24.5553 18.5821 24.3067 18.6285C24.058 18.6749 23.8285 18.7933 23.6467 18.9692C22.7667 18.39 21.5758 18.0217 20.2583 17.9742L20.9792 15.7042L22.9317 16.1617L22.9292 16.19C22.9292 16.77 23.4033 17.2417 23.9858 17.2417C24.5683 17.2417 25.0417 16.77 25.0417 16.19C25.0415 15.944 24.9554 15.7057 24.7982 15.5165C24.6411 15.3272 24.4227 15.1988 24.1808 15.1536C23.939 15.1083 23.689 15.1489 23.474 15.2685C23.2589 15.3881 23.0925 15.579 23.0033 15.8083L20.8992 15.315C20.8548 15.3042 20.808 15.3107 20.7682 15.3331C20.7285 15.3556 20.6987 15.3923 20.685 15.4358L19.8808 17.9675C18.5008 17.9842 17.2517 18.3558 16.3325 18.9517C16.149 18.781 15.9197 18.6675 15.6726 18.6251C15.4256 18.5828 15.1716 18.6134 14.9417 18.7133C14.7118 18.8132 14.516 18.9779 14.3784 19.1874C14.2407 19.3968 14.1671 19.6419 14.1667 19.8925C14.1667 20.3642 14.4258 20.7725 14.8067 20.9967C14.7817 21.1333 14.765 21.2725 14.765 21.4133C14.765 23.3142 17.1025 24.8608 19.9758 24.8608C22.8492 24.8608 25.1867 23.3142 25.1867 21.4133C25.1867 21.28 25.1725 21.1492 25.15 21.02C25.555 20.8025 25.8333 20.3817 25.8333 19.8925ZM21.7733 20.01C21.5699 20.0098 21.3747 20.0903 21.2307 20.2339C21.0866 20.3775 21.0054 20.5724 21.005 20.7758C21.005 20.9796 21.0859 21.175 21.23 21.3191C21.3741 21.4632 21.5696 21.5442 21.7733 21.5442C21.9771 21.5442 22.1725 21.4632 22.3166 21.3191C22.4607 21.175 22.5417 20.9796 22.5417 20.7758C22.5412 20.5724 22.4601 20.3775 22.316 20.2339C22.1719 20.0903 21.9767 20.0098 21.7733 20.01Z" fill="#FF4500" fill-rule="evenodd"></path>
                              </g>
                              <defs>
                                <clipPath id="clip0_1365_7519">
                                  <rect fill="white" height="20" transform="translate(10 10)" width="20"></rect>
                                </clipPath>
                              </defs>
                            </svg>
                            <span class="sharing-operation">Reddit</span>
                          </div>
                        </a>
                      </div>
                    </div>
                  </li>
              </div>
              <div class="explore-more-teg mt-4">
                <h3 class="mb-4">Explore more</h3>
                <div class="d-flex" style="flex-wrap: wrap;">`;
                search_category_arr.forEach((element,index) => {
                  if(index < 21){
                    template_data += `<div class="explore-sub-teg tab-explore" style="margin:0 10px 10px 0;" onclick="seachTemplateByTag('${element}')">${element}</div>`
                  }
                });
              template_data += `</div>
              </div>`;
    let template_heading = "";
    template_heading = "Recommended Templates";
    // $("#activeVideoTemplate").html(active_template);
    // $("#activeVideoTemplate").css("margin-top","36px");
    $(".Puzyyw").html(template_data);
    $(".template-heading").html(template_heading);
    $(".template-heading").css("margin-top","42px");
    $(".Puzyyw-i").html('');
  }

  function dialogVideoPreview(index,sub_category_id,content_id,content_file,selectedTempHeight,selectedTempWidth){
    event.stopPropagation();
    event.preventDefault();
    event.stopImmediatePropagation();
    $("#activeVideoTemplate").css("display","none");
    $("#activeVideoTemplateShimmer").css("display","block");
    $('#videoprevtemp').animate({
     scrollTop: $("#videoprevtemp").offset().top - 40
    });
    // if($("#activeVideoTemplate").width() < 120){
    //     $("#activeVideoTemplate").addClass('activeContentBox');
    //     $("#custom-seekbar-mdl").css("bottom","22px");
    //     $("#custom-seekbar-mdl").parent().css("margin-top","5px");
    //   }
    //   else{
    //     $("#activeVideoTemplate").removeClass('activeContentBox');
    //     $("#custom-seekbar-mdl").css("bottom","22px");
    //   }
    getTemplates(sub_category_id,content_id,selectedTempHeight,selectedTempWidth,"video");

    $("#video").attr('src',content_file);

    // setTimeout(() => {
    //   $("#activeVideoTemplate").css("display","block");
    //   $("#activeVideoTemplateShimmer").css("display","none");
    // }, 1500);
  }

  function loadTemplateImg(index){
    $('#img_card_container'+index).removeClass("prograssive_img");
  }

  function loadActiveTemplate(template_width){
    document.getElementById("activeTemplateShimmer").style.display = "none";
    document.getElementById("activeTemplateImg").style.display = "block";
    if(template_width < 120){
      $("#active_temp_id").children().css({
        "min-width": "120px",
        "background-color": "#EBECF0",
        "height": "100%",
        "display": "flex",
        "align-items": "center",
        "justify-content": "center",
        "border": "1px solid rgba(57,76,96,.15)",
        "padding": "10px"
      });
    }
  }

  function showPopup(popupId){
    $(".shareTooltip").hide();
    if($(popupId).css("display") === "none"){
      let element;
      if(popupId == "#shareoption"){
        element = $("#sharebtn").offset().top;
      }
      else{
        element = $("#sharebtn2").offset().top;
      }
      windowHeight = window.innerHeight / 2;

      if(element < windowHeight){
        if($(document).width() < 396){
          $(popupId).css("top","35px");
        }
        else{
          $(popupId).css("top","50px");
        }
      }
      else{
        if($(document).width() < 396){
          $(popupId).css("top","-295px");
        }
        else{
          $(popupId).css("top","-305px");
        }
      }
      $(popupId).fadeIn();
    }
    else{
      $(popupId).fadeOut();
    }
  }

  function loadSliderImg(index){
    document.getElementById("slider_shimmer"+index).style.width = 0;
    document.getElementById("slider_shimmer"+index).style.marginLeft = 0;
    document.getElementById("slider_shimmer"+index).style.marginRight = 0;
    document.getElementById("sliderImgLoad"+index).style.display = "grid";
  }

  function selectTemplate(index,webp_url){
    getSelectedId = "sliderImgLoad"+index;
    multiple_images_arr.forEach((element,index1) => {
      otherIds = "sliderImgLoad"+index1;
      if(index1 == index){
        $('#' + getSelectedId).addClass("c-active-tmp");
      }
      else{
        $('#' + otherIds).removeClass("c-active-tmp");
      }
    });
    $("#activeTemplateImg").attr("src",webp_url);
  }

  function dialogViewPreview(event,index,sub_category_id,content_id,selectedTempHeight,selectedTempWidth){
    event.stopPropagation();
    event.preventDefault();
    event.stopImmediatePropagation();
    $('#prevtemp').animate({
     scrollTop: $("#prevtemp").offset().top - 20
    });
    getTemplates(sub_cat_id, content_id,selectedTempHeight,selectedTempWidth);
  }

  function copyTemplateURL(id,sub_cat_id,content_id) {
    clearTimeout(timeoutId);
    let selBox = document.createElement('textarea');
    selBox.style.position = 'fixed';
    selBox.style.left = '0';
    selBox.style.top = '0';
    selBox.style.opacity = '0';
    selBox.value = `{!! config('constant.ACTIVATION_LINK_PATH') !!}/p/?q=${content_id}`;
    document.body.appendChild(selBox);
    selBox.focus();
    selBox.select();
    document.execCommand('copy');
    document.body.removeChild(selBox);

    $("#suces_msg").html("Share this template anywhere.");
    $("#suces_hding").html("Link copied");
    $("#suces_msgv").html("Share this template anywhere.");
    $("#suces_hdingv").html("Link copied");
    $(".error-wrapper").css("height","112px");
    $(".icon-wrapper").css("background-color","#1ED293");
    $(".icon-wrapper").html(`<svg class="err-icon" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <path d="M6.42,16L0,9.45l2.15-2.2L6,11.14l0.46,0.47L15.85,2,18,4.19,6.43,16h0Z" style="fill:#ffffff"></path>
      </svg>`);


    document.getElementById(id).classList.toggle("open-error-wrapper");
    var timeoutId = setTimeout(() => {
      document.getElementById(id).classList.remove("open-error-wrapper");
    },4000);
  }

  function dismissCopyURLPopup(id){
    clearTimeout();
    if($("#suces_hdingv").html() == "Error" || $("#suces_hding").html() == "Error"){
      $('#prevtemp').css('display','none')
      $('#prevtemp_parent').css('display','none')
      $('#prevtemp').parent().parent().removeClass('template-overlay');
      $('#prevImg').attr('src','https://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/62628570c6c66_json_image_1650623856.jpg');
      $('#videoprevtemp').css('display','none')
      $('#videoprevtemp_parent').css('display','none')
      $('#videoprevtemp').parent().parent().removeClass('template-overlay');
      $('#videoprevImg').attr('src','https://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/62628570c6c66_json_image_1650623856.jpg');
      $('#video-modal-container2').empty()
    }
    document.getElementById(id).classList.remove("open-error-wrapper");
  }

  function showShareBtnTooltip(){
    if(document.getElementById("shareoption")){
      if(document.getElementById("shareoption").style.display == "none"){
        $(".shareTooltip").show();
      }
      else{
        $(".shareTooltip").hide();
      }
    }
    if(document.getElementById("shareoption2")){
      if(document.getElementById("shareoption2").style.display == "none"){
        $(".shareTooltip").show();
      }
      else{
        $(".shareTooltip").hide();
      }
    }
  }

  function hideShareBtnTooltip(){
    $(".shareTooltip").hide();
  }

  function seachTemplateByTag(searchTag){
    searchTagURL = `{!! config('constant.ACTIVATION_LINK_PATH') !!}/search/?q=${searchTag}&sci=0&ct=0&p=1`;
    window.open(searchTagURL, '_blank');
  }

  function closebtntmp(id) {
  $('#prevtemp').css('display','none')
  $('#prevtemp_parent').css('display','none')
  $('#prevtemp').parent().parent().removeClass('template-overlay');
  $('#prevImg').attr('src','https://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/62628570c6c66_json_image_1650623856.jpg');
  $('#videoprevtemp').css('display','none')
  $('#videoprevtemp_parent').css('display','none')
  $('#videoprevtemp').parent().parent().removeClass('template-overlay');
  $('#videoprevImg').attr('src','https://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/62628570c6c66_json_image_1650623856.jpg');
  $('#video-modal-container2').empty();
  if(id == ""){
  }
  else{
    document.getElementById(id).classList.remove("open-error-wrapper");
  }
}

document.addEventListener("keydown", function(event) {
    if(event.keyCode === 27){
      if(document.getElementById('copyTempURL').classList.contains('open-error-wrapper')){
        closebtntmp('copyTempURL');
      }
      if(document.getElementById('copyVideoTempURL').classList.contains('open-error-wrapper')){
        closebtntmp('copyVideoTempURL');
      }
      closebtntmp("");
   }
});


$(document).ready(function() {
  $(".c-template-active").click(function () {
  $(".c-template-active").removeClass("c-active-tmp");
  $(this).addClass("c-active-tmp");
});
  // $("#sharebtn").mouseup(function () {
    window.addEventListener('mouseup',function(event){

    // $("#shareoption").css("display", "none")
    var pol = document.getElementById('shareoption');

    if(pol){
      if(event.target != pol && event.target.parentNode != pol){
            $("#shareoption").fadeOut();
        }
        // else if($('#shareoption').css('display') != 'none')
        // {
        //   pol.style.display = 'none';
        // }
    }
    var polv = document.getElementById('shareoption2');

    if(polv){
      if(event.target != polv && event.target.parentNode != polv){
            $("#shareoption2").fadeOut();
        }
    }

  })
  $(".explore-sub-teg").click(function () {
    $(".explore-sub-teg").removeClass("explore-active");
      $(this).addClass("explore-active");
  });
});

  $("#ttcw").scroll(function(){
    let rightPos = $(`#ttcw`).scrollLeft();
    if (rightPos == 0) {
      $(`#ttcbleft`).fadeOut();
    }
    if (rightPos >= 0) {
      $(`#ttcbright`).fadeIn();
    }

    let leftpos = $(`#ttcw`).scrollLeft();
    if (leftpos > 10) {
      $(`#ttcbleft`).fadeIn();
    }
    if (Math.round($(`#ttcw`).width() + leftpos) >= Math.round($(`#template_slider`).width())) {
      $(`#ttcbright`).fadeOut();
    }
  });
function scrollLeftSlider() {

let rightPos = $(`#ttcw`).scrollLeft();
$(`#ttcw`).animate({
  scrollLeft: rightPos - 600
}, 600);
setTimeout(() => {
  let rightPos = $(`#ttcw`).scrollLeft();

  if (rightPos == 0) {
    $(`#ttcbleft`).fadeOut();
  }
  if (rightPos >= 0) {
    $(`#ttcbright`).fadeIn();
  }
}, 600);
}

function scrollRightSlider() {
let leftpos = $(`#ttcw`).scrollLeft();
$(`#ttcw`).animate({
  scrollLeft: leftpos + 600
}, 600);
setTimeout(() => {
  let leftpos = $(`#ttcw`).scrollLeft();


  if (leftpos > 10) {

    $(`#ttcbleft`).fadeIn();
  }
  if (Math.round($(`#ttcw`).width() + leftpos) >= Math.round($(`#template_slider`).width() - 10)) {

    $(`#ttcbright`).fadeOut();
  }
}, 600);
}
</script>

</html>
