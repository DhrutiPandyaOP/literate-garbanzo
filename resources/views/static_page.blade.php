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
  <meta charset="utf-8"/>
  <title>{!! $template['page_title'] !!}</title>
  <meta name="theme-color" content="#317EFB"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="Description" content="{!! $template['meta'] !!}"/>
  <meta name="COPYRIGHT" content="PhotoADKing"/>
  <meta name="AUTHOR" content="PhotoADKing"/>
  <link rel="canonical" href="{!! $template['canonical'] !!}"/>
  <link rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/slick.css?V3.6" as="style" async />

  <link async rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/bootstrap.min.css" as="style">
 <link async rel="preload" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/style.css?v4.33" as="style">

<link rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/slick.css?V3.6" type="text/css" media="all" async />
  <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/bootstrap.min.css" type="text/css" media="all"/>
  <link async rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/style.css?v4.33"
        type="text/css" media="all"/>
  <link rel="stylesheet" href="{!! config('constant.ACTIVATION_LINK_PATH') !!}/css/slick.css?V3.5" type="text/css" media="all" async />
  <meta property="og:image:height" content="462">
  <meta property="og:image:width" content="883">
  <meta property="og:locale" content="en_US"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="{!! $template['page_title'] !!}"/>
  <meta property="og:description" content="{!! $template['meta'] !!}"/>
  <meta property="og:image" content="https://photoadking.com/photoadking.png?v1.5"/>
  <meta property="og:url" content="{!! $template['canonical'] !!}"/>

  <meta name="twitter:title" content="{!! $template['page_title'] !!}"/>
  <meta name="twitter:description" content="{!! $template['meta'] !!}"/>
  <meta name="twitter:image" content="https://photoadking.com/photoadking.png?v1.5"/>
  <meta name="twitter:url" content="https://photoadking.com">
  <meta http-equiv="Expires" content="1"/>

  <link rel="apple-touch-icon" sizes="57x57" href="https://photoadking.com/images/favicon/apple-icon-57x57.png?v1.3">
  <link rel="apple-touch-icon" sizes="60x60" href="https://photoadking.com/images/favicon/apple-icon-60x60.png?v1.3">
  <link rel="apple-touch-icon" sizes="72x72" href="https://photoadking.com/images/favicon/apple-icon-72x72.png?v1.3">
  <link rel="apple-touch-icon" sizes="76x76" href="https://photoadking.com/images/favicon/apple-icon-76x76.png?v1.3">
  <link rel="apple-touch-icon" sizes="114x114"
        href="https://photoadking.com/images/favicon/apple-icon-114x114.png?v1.3">
  <link rel="apple-touch-icon" sizes="120x120"
        href="https://photoadking.com/images/favicon/apple-icon-120x120.png?v1.3">
  <link rel="apple-touch-icon" sizes="144x144"
        href="https://photoadking.com/images/favicon/apple-icon-144x144.png?v1.3">
  <link rel="apple-touch-icon" sizes="152x152"
        href="https://photoadking.com/images/favicon/apple-icon-152x152.png?v1.3">
  <link rel="apple-touch-icon" sizes="180x180"
        href="https://photoadking.com/images/favicon/apple-icon-180x180.png?v1.3">
  <link rel="icon" type="image/png" sizes="512x512"
        href="https://photoadking.com/images/favicon/android-icon-512x512.png?v1.3">
  <link rel="icon" type="image/png" sizes="192x192"
        href="https://photoadking.com/images/favicon/android-icon-192x192.png?v1.3">
  <link rel="icon" type="image/png" sizes="96x96"
        href="https://photoadking.com/images/favicon/favicon-96x96.png?v1.3">
  <link rel="icon" type="image/png" sizes="48x48"
        href="https://photoadking.com/images/favicon/android-icon-48x48.png?v1.3">
  <link rel="icon" type="image/png" sizes="32x32"
        href="https://photoadking.com/images/favicon/favicon-32x32.png?v1.3">
  <link rel="icon" type="image/png" sizes="16x16"
        href="https://photoadking.com/images/favicon/favicon-16x16.png?v1.3">
  <link rel="shortcut icon" type="image/icon" href="https://photoadking.com/images/favicon/favicon.ico?v1.3">
  <link rel="icon" type="image/icon" href="https://photoadking.com/images/favicon/favicon.ico?v1.3">
  <link rel="mask-icon" href="https://photoadking.com/images/favicon/safari-pinned-tab.svg" color="#1b94df">
  <link rel="manifest" href="https://photoadking.com/images/favicon/manifest.json?v1.3">
  <meta name="msapplication-TileColor" content="#ffffff">
  <link rel="preload" href="https://photoadking.com/fonts/Myriadpro-Regular.otf" as="font" crossorigin>
  <link rel="preload" href="https://photoadking.com/fonts/Myriadpro-Bold.otf" as="font" crossorigin>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" type="text/css" media="all" async />
  <link rel="preload" as="font" type="font/woff2" crossorigin href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0"/> -->
  <meta name="msapplication-TileImage" content="https://photoadking.com/images/favicon/ms-icon-144x144.png?v1.3">
  <meta name="msapplication-config" content="https://photoadking.com/images/favicon/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">

  {!! $template['analytic'] !!}

  @php
    $is_rating = 0;
    $faqs = $template['page_faqs'];
    $guide_steps = $template['guide_steps'];
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
           "name": "'.$faq->question.'",
           "acceptedAnswer": {
             "@type": "Answer",
             "text": "'.$faq->answer.'"
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
           "name": "'.$guide->heading.'",
           "itemListElement": {
             "@type": "HowToDirection",
             "text":"'.$guide->description.'"
           }
         }';
      }
      $guide_schema = '{
         "@context": "https://schema.org",
         "@type": "HowTo",
         "name": "'.$template['guide_heading'].'",
         "step": ['.$guide_schema.']
       }';
   }


   if($template['ratingName'] !='' && $template['ratingDescription'] !="" && $template['ratingValue'] !="" && $template['reviewCount'] !=""){
     $is_rating=1;
     $rating_schema = '{
      "@context": "https://schema.org",
      "@type": "Product",
      "brand": "PhotoADKing",
      "name": "'.$template['ratingName'].'",
      "description": "'.$template['ratingDescription'].'",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": '.$template['ratingValue'].',
        "bestRating": 5,
        "reviewCount": '.$template['reviewCount'].'
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

</head>

<body class="position-relative">
<div class="w-100">
    <div class="fchat-wrapper-div">
      <img src="https://photoadking.com/images/BG_shadow.png" id="chat_icon" width="90px" height="90px" onclick="initializeFeshchat()" alt="freshchat icon"
        class="fchat-bg-style">
      <img src="https://photoadking.com/images/chat.svg" id="chat_ic" class="fchat-chat-icon" height="24px" width="24px" alt="chat icon"
        onclick="initializeFeshchat()">

      <img src="https://photoadking.com/images/rolling.svg" id="loader_ic" class="disply-none fchat-loder-icon" height="24px" alt="loading icon"
        width="24px">
    </div>

  </div>
<div class="l-body-container" id="mainbody">
  <div class="w-100 privacy-header-bg l-blue-bg position-relative sec-first" id="top">
    <!-- <div id="header"></div> -->
    <div class="w-100">
      <ul class="l-mob-menu-container" id="mobmenu">
        <li><a href="https://photoadking.com/">Home</a></li>
        <li><a href="https://photoadking.com/#Features">Features</a></li>
        <li><a href="https://photoadking.com/templates/"> Templates </a></li>
        <li><a href="https://photoadking.com/go-premium/">Pricing </a></li>
        <li><a href="https://blog.photoadking.com/" target="_blank" rel="noreferrer">Learn</a></li>
        <li><a href="https://helpphotoadking.freshdesk.com/support/home" target="_blank"
               rel="noreferrer">Help</a>
        </li>
        <li id="hd-login"><a href="https://photoadking.com/app/#/login">Login</a></li>
        <li><a href="https://photoadking.com/app/#/sign-up" id="rlp-text-mob">Signup</a></li>
      </ul>
      <div class="overlay" style="display: none;"></div>

      <div class="l-transition-5 l-header-big" id="docHeader">
        <div class="col-12 col-xl-8 col-lg-8 col-md-11 col-sm-11 l-min-md-pd m-auto">
          <a href="https://photoadking.com/">
            <div class="l-logo-div float-left">
              <img src="https://photoadking.com/images/photoadking-white.png" width="180px" height="39px" loading="lazy"
                   data-src="https://photoadking.com/images/photoadking-white.png" class="l-blue-logo"
                   alt="image not found">
              <img src="https://photoadking.com/images/photoadking-white.png" width="180px" height="39px" loading="lazy"
                   data-src="https://photoadking.com/images/photoadking-white.png" class="l-wht-logo"
                   alt="image not found">
              <img src="https://photoadking.com/images/photoadking.png" width="180px" height="39px" loading="lazy"
                   data-src="https://photoadking.com/images/photoadking.png" class="l-white-logo"
                   alt="image not found">
            </div>
          </a>
          <div class="float-right l-menu align-items-center">
            <ul class="l-menu-container">
              <li><a href="https://photoadking.com/">Home </a></li>
              <li><a href="https://photoadking.com/#Features">Features</a></li>
              <li><a href="https://photoadking.com/templates/">Templates </a> </li>
              <li><a href="https://photoadking.com/go-premium/">Pricing</a></li>
              <li><a href="https://blog.photoadking.com/" target="_blank" rel="noreferrer">Learn</a>
              </li>
              <li><a href="https://helpphotoadking.freshdesk.com/support/home" target="_blank"
                     rel="noreferrer">Help</a></li>
              <li id="hd-logn"><a href="https://photoadking.com/app/#/login">Login</a></li>
            </ul>
            <a href="https://photoadking.com/app/#/sign-up" id="rlp-link">
              <button class="l-signup-btn" id="rlp-btn-txt">
                <span>Signup For Free</span>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="1024.000000pt"
                     height="1024.000000pt" viewBox="0 0 1024.000000 1024.000000"
                     preserveAspectRatio="xMidYMid meet"
                     style="width: 12px;height: 10px; margin-left: 4px;">
                  <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                     fill="#ffffff" stroke="none">
                    <path
                      d="M5836 9345 c-126 -35 -168 -66 -412 -310 -198 -197 -236 -240 -267 -301 -86 -168 -90 -342 -10 -514 34 -74 47 -88 1154 -1192 l1120 -1118 -3453 0 c-3811 0 -3524 5 -3656 -65 -136 -71 -237 -190 -286 -337 -26 -76 -26 -79 -26 -411 0 -305 2 -341 20 -398 53 -174 200 -318 385 -380 33 -12 640 -15 3512 -19 l3471 -5 -1102 -1110 c-1007 -1015 -1104 -1116 -1133 -1176 -85 -173 -83 -363 4 -533 31 -61 69 -104 267 -301 246 -246 286 -275 416 -310 143 -38 332 -9 454 69 90 57 3830 3808 3874 3884 52 91 72 170 72 281 0 111 -16 184 -61 274 -28 56 -225 256 -1907 1941 -1033 1033 -1893 1893 -1912 1911 -54 49 -112 83 -194 110 -62 21 -93 25 -178 24 -57 0 -125 -7 -152 -14z"/>
                  </g>
                </svg>
              </button>
            </a>
          </div>
          <div class="float-right l-mob-menu" id="mob-menu">
            <button class="l-transparent-button">
                                <span>
                                    <svg class="l-sm-white" xmlns="http://www.w3.org/2000/svg" version="1.0"
                                         width="1024.000000pt" height="1024.000000pt"
                                         viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet"
                                         style="height: 20px; width: 20px;">
                                        <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                           fill="#ffffff" stroke="none">
                                            <path
                                              d="M355 9703 c-159 -35 -273 -138 -325 -294 -29 -83 -25 -225 8 -309 44 -112 134 -200 249 -243 l58 -22 4730 -3 c3267 -2 4751 1 4799 8 142 22 250 100 313 228 38 76 38 76 38 201 0 122 -1 128 -32 192 -60 122 -155 202 -278 234 -53 13 -558 15 -4800 14 -2607 -1 -4749 -3 -4760 -6z" />
                                            <path
                                              d="M300 6921 c-75 -24 -96 -36 -153 -90 -94 -87 -137 -188 -137 -320 0 -186 89 -327 255 -403 l50 -23 4790 -3 c4703 -2 4791 -2 4847 17 110 37 192 113 245 227 26 55 28 68 28 184 0 114 -2 130 -26 180 -54 113 -146 196 -256 230 -65 20 -80 20 -4827 19 -4525 0 -4764 -1 -4816 -18z" />
                                            <path
                                              d="M315 4166 c-120 -38 -206 -115 -263 -234 -36 -75 -37 -80 -37 -192 0 -95 4 -124 23 -170 44 -111 141 -207 252 -253 l55 -22 4745 -3 c3360 -1 4760 1 4797 9 130 26 247 121 306 246 26 57 32 83 35 161 5 116 -15 198 -67 280 -48 73 -99 117 -181 156 l-65 31 -4780 2 c-3842 1 -4788 -1 -4820 -11z" />
                                            <path
                                              d="M340 1399 c-140 -28 -259 -139 -311 -291 -28 -83 -23 -224 10 -311 43 -110 132 -196 249 -241 l57 -21 4735 -3 c3384 -2 4754 0 4800 8 141 24 245 101 307 228 38 76 38 76 38 201 0 123 -1 128 -34 196 -60 123 -172 211 -300 235 -68 13 -9485 12 -9551 -1z" />
                                        </g>
                                    </svg>
                                </span>
              <span>
                                    <svg class="l-sm-blue" xmlns="http://www.w3.org/2000/svg" version="1.0"
                                         width="1024.000000pt" height="1024.000000pt"
                                         viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet"
                                         style="height: 20px; width: 20px;">
                                        <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                                           fill="#1eb2f6" stroke="none">
                                            <path
                                              d="M355 9703 c-159 -35 -273 -138 -325 -294 -29 -83 -25 -225 8 -309 44 -112 134 -200 249 -243 l58 -22 4730 -3 c3267 -2 4751 1 4799 8 142 22 250 100 313 228 38 76 38 76 38 201 0 122 -1 128 -32 192 -60 122 -155 202 -278 234 -53 13 -558 15 -4800 14 -2607 -1 -4749 -3 -4760 -6z" />
                                            <path
                                              d="M300 6921 c-75 -24 -96 -36 -153 -90 -94 -87 -137 -188 -137 -320 0 -186 89 -327 255 -403 l50 -23 4790 -3 c4703 -2 4791 -2 4847 17 110 37 192 113 245 227 26 55 28 68 28 184 0 114 -2 130 -26 180 -54 113 -146 196 -256 230 -65 20 -80 20 -4827 19 -4525 0 -4764 -1 -4816 -18z" />
                                            <path
                                              d="M315 4166 c-120 -38 -206 -115 -263 -234 -36 -75 -37 -80 -37 -192 0 -95 4 -124 23 -170 44 -111 141 -207 252 -253 l55 -22 4745 -3 c3360 -1 4760 1 4797 9 130 26 247 121 306 246 26 57 32 83 35 161 5 116 -15 198 -67 280 -48 73 -99 117 -181 156 l-65 31 -4780 2 c-3842 1 -4788 -1 -4820 -11z" />
                                            <path
                                              d="M340 1399 c-140 -28 -259 -139 -311 -291 -28 -83 -23 -224 10 -311 43 -110 132 -196 249 -241 l57 -21 4735 -3 c3384 -2 4754 0 4800 8 141 24 245 101 307 228 38 76 38 76 38 201 0 123 -1 128 -34 196 -60 123 -172 211 -300 235 -68 13 -9485 12 -9551 -1z" />
                                        </g>
                                    </svg>
                                </span>
            </button>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 l-min-md-pd p-0">
      <div class="header-content-padding sec-first-content-wrapper mb-5">
        <div class="s-header-content-container sec-first-heading-container">
          <h1 class="f-heading sec-first-heading">{!! $template['header_h1'] !!}</h1>
          <p class="s-sub-header sec-first-subheading">{!! $template['header_h2'] !!}</p>
          <a href="{!! $template['header_cta_link'] !!}" id="hdrnavbtn"
             class="btn sec-first-button">{!! $template['header_cta_text'] !!} <svg version="1.1" style="height: 19px;enable-background:new 0 0 31.49 31.49;" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 31.49 31.49"
                xml:space="preserve">
                <path fill="#ffffff" d="M21.205,5.007c-0.429-0.444-1.143-0.444-1.587,0c-0.429,0.429-0.429,1.143,0,1.571l8.047,8.047H1.111
             C0.492,14.626,0,15.118,0,15.737c0,0.619,0.492,1.127,1.111,1.127h26.554l-8.047,8.032c-0.429,0.444-0.429,1.159,0,1.587
             c0.444,0.444,1.159,0.444,1.587,0l9.952-9.952c0.444-0.429,0.444-1.143,0-1.571L21.205,5.007z" />
              </svg></a>
        </div>
      </div>
    </div>
    {{-- User rating--}}
  @if($is_rating)
  <div class="review-wrapper pt-1 pb-1">
    <span>
      <span class="desc">{{ $template['ratingDescription'] }}</span>
    </span>
    <br>
    <span class="Author whitespace-nowrap"><span>{{ $template['userName'] }}</span></span>
    <span class="Author whitespace-nowrap">Rating: <span>{{ $template['ratingValue'] }}</span> / <span>5</span></span>

      @if($template['ratingValue'] >= 3 && $template['ratingValue'] <= 4)
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
    @elseif($template['ratingValue'] > 4 && $template['ratingValue'] <= 5)
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      <svg xmlns="http://www.w3.org/2000/svg" class=" color-orange star activated" viewBox="0 0 24 24">
        <path fill="currentColor" fill-rule="evenodd"
              d="M12 18.76l-5.14 3.16a1.5 1.5 0 01-2.24-1.63l1.42-5.86-4.6-3.91a1.5 1.5 0 01.86-2.64l6.02-.46 2.3-5.57a1.5 1.5 0 012.77 0l2.3 5.57 6 .46a1.5 1.5 0 01.86 2.64l-4.59 3.9 1.42 5.87a1.5 1.5 0 01-2.24 1.63L12 18.76z">
        </path>
      </svg>
      @endif
    </div>
  @endif
  </div>
  <div class="modal fade-scale" id="my_modal" class="display-video-modal">
    <div class="modal-dialog  video-modal">
      <div class="modal-content vdo-modal-contnt">

        <!-- Modal Header -->
        <!-- <div class="modal-header vdo-modal-header">

        </div> -->

        <!-- Modal body -->
        <div class="modal-body p-0">
          <button type="button" class="close close-btn-style" data-dismiss="modal"
                  onclick="closeDialog()">&times;
          </button>
          <div id="video-modal-container" class="text-center"></div>
          <div class="seekbar-container" id="seekbar-mdl" style="bottom: -10px;">
            <div class="custom-seekbar" id="custom-seekbar-mdl"><span id="cs-mdl"></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="col-12 col-lg-10 col-md-11 col-sm-11 m-auto w-100 collection-container collection-container-1">
    <div class="item-container">

      {!! $template['left_nav'] !!}

      <div class="video-template-container row no-gutters">
        <div class="card-columns template-wrapper w-100 mb-0" id="card-item" style="min-height:800px">
          <div class="col col-sm-12 p-0 text-center card-ol-block card-item template-page-item mx-auto">
          </div>
        </div>
        <div class="template-wrapper-1 w-100 visibility-hidden py-0 my-0" id="temparary_wrapper">
          <div class="col col-sm-12 p-0 text-center temparary-item card-ol-block mx-auto">
          </div>
        </div>
      </div>
    </div>

  </div>

  @php
    $guide_steps = $template['guide_steps'];
    $faqs = $template['page_faqs'];
  @endphp

   <!--
  **********************************************************
  populat poster categories code started
  **********************************************************
  -->

  @php
    $similar_pages = $template['similar_pages'];
    $sub_pages = $template['sub_pages'];
  @endphp
  @if(count($similar_pages) > 0 || count($sub_pages) > 0 )
  <div style="background-color: #1980FF;">
  @if(count($sub_pages) > 0)
    <div
      class="col-12 col-xl-10 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container popular-category-container pb-0">
      <h2 class="heading text-center py-4 text-white font-weight-normal">Popular {{ isset($sub_pages[0]->sub_category_name) ? ucfirst($sub_pages[0]->sub_category_name) : ''}} Categories</h2>
      <div id="parent_tag_container">
        <div id="popular_tag_container" class="tag-container">
          @foreach($sub_pages as $page)
            @if($page->static_page_id != $template['static_page_id'])
              <a class="tag-style" target="_blank" href="{{ $page->page_url }}">{{ ucfirst($page->tag) }}</a>
            @endif
          @endforeach
        </div>
        <div id="btn-wrapper" class="text-center mb-2">
          <button id="explore_more" type="button" class="button-style"><span class="mr-2">Explore More</span>
            <svg class="down-icon " version="1.1" x="0px"
                 y="0px" width="13px" viewBox="0 0 16 8">
              <path fill="white" class="arrow-svg"
                    d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
              </path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  @endif
  @if(count($similar_pages) > 0)
    <div
      class="col-12 col-xl-10 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container popular-category-container pb-0">
      @if(count($similar_pages) > 0 && count($sub_pages) > 0 )
       <hr style="border-top: 1px solid #ffffff4d !important;" class="mt-0">
      @endif
        <h2 class="heading text-center py-4 text-white font-weight-normal">Similar Templates Recommendation</h2>
      <div class="pb-5">
        <div id="similar_tag_container" class="tag-container">
          @foreach($similar_pages as $page)
            <a class="tag-style" target="_blank" href="{{ $page->page_url }}">{{ ucfirst($page->tag_name) }}</a>
          @endforeach
        </div>
        <div id="btn-wrapper1" class="text-center mb-2">
          <button id="similar_explore_more" type="button" class="button-style"><span class="mr-2">Explore More</span>
            <svg class="down-icon " version="1.1" x="0px" y="0px"
                 width="13px" viewBox="0 0 16 8">
              <path fill="white" class="arrow-svg"
                    d="M8,5.5l5.2-5.2c0.4-0.4,1.1-0.4,1.5,0c0.4,0.4,0.4,1.1,0,1.5L8.7,7.7c0,0,0,0,0,0C8.5,7.9,8.3,8,8,8C7.7,8,7.5,7.9,7.3,7.7
c0,0,0,0,0,0L1.3,1.8c-0.4-0.4-0.4-1.1,0-1.5s1.1-0.4,1.5,0L8,5.5z">
              </path>
            </svg>
          </button>
        </div>
      </div>
    </div>
@endif
  </div>
@endif

  {{--  User guide step section --}}
  @if(count($guide_steps) > 0)
    <div class="row no-gutters bg-white">
      <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 w-100 ml-auto mr-auto collection-container pb-0">
        <h2 class="heading mb-5 sec-three-heading sec-three-special text-center">{{ isset($template['guide_heading']) && $template['guide_heading'] !=""  ? $template['guide_heading'] : ""}}</h2>

        <ol class="step-wrapper temp-step-wrapper row no-gutters mobile-hide">
          <div class="temp-step-first-col row">
            @foreach($guide_steps as $step)
              <li class="col-lg-6 col-md-12 col-sm-12">
                <p class="step-title mb-1">{{ $step->heading }}</p>
                <p class="step-content">{{ $step->description }}</p>
              </li>
            @endforeach
          </div>
        </ol>

        {{-- mobile view--}}
        <ol class="step-wrapper row no-gutters mobile-display">
          @foreach($guide_steps as $step)
            <li>
              <p class="step-title">{{ $step->heading }}</p>
              <p class="step-content">{{ $step->description }}</p>
            </li>
          @endforeach
        </ol>
        <div class="see-more-btn-wrapper sec-five-btn-wrapper">
          <a href="{!! $template['header_cta_link'] !!}" class="m-auto l-blue-bg color-white see-more-btn d-inline-block sec-four-button">{!! $template['guide_btn_text'] !!}  </a>
        </div>
      </div>
    </div>
  @endif
<!--
  **********************************************************
  Popular poster categories code ended
  Testimonial code started
  **********************************************************
  -->

    <div class="l-last-slider-container pt-0 m-0 testimonial-container l-last-slider-hide">
      <h2 class="l-last-slider-header py-2 pt-5">Let our <span
            class="blue-text">customers</span> speak for us!</h2>
      <div class=" col-12 col-lg-8 col-md-10 col-sm-12 m-auto mt-4 l-last-slider-hide template-review-slider">
      <div class="last-slider">
          <div class="template-review-container">
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="quote-style" alt="inverted commas">

            <div class="img-container tmplte-img-container">
            <img src="https://photoadking.com/images/Neha-Shah.webp" class="review_img" loading="lazy" width="135px" height="135px" onerror=" this.src='https://photoadking.com/images/Neha-Shah.jpg'" alt="user's photo">
              <p class="pt-1 mb-0 testimonial-name-style" >Neha Shah</p>
              <p class="mb-0 testimonial-name1-style">Business Owner</p>
            </div>
            <div class="l-review-content templt-review-content">
            I created social media ads with PhotoAdking to market my business. Just came across this website, and it works wonders. Created social media ads using the templates. Also, dimensionally too PhotoAdking speaks right. Amazed with the quality of templates they serve; that too in abundance! Thanks a bunch! I will reuse it again and again.
              <div class="w-100 mt-2">
              <div class="d-flex">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                </div>
              </div>
            </div>
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="second-quote-style" alt="inverted commas">
          </div>

          <div class="template-review-container">
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="quote-style" alt="inverted commas">
            <div class="img-container tmplte-img-container">
            <img src="https://photoadking.com/images/Marques-Stanley.webp" class="review_img" loading="lazy" width="135px" height="135px" onerror=" this.src='https://photoadking.com/images/Marques-Stanley.jpg'" alt="user's photo">
              <p class="pt-1 mb-0 testimonial-name-style" >Marques Stanley</p>
              <p class="mb-0 testimonial-name1-style">Non-Profit Organizer</p>
            </div>
            <div class="l-review-content templt-review-content">
            PhotoADKing is an excellent tool. It saves my time. I have created video ads and business banner for my non-profit organization. It is the best tool for creating brochures, flyers, business cards, ads, etc. They have a massive library of ready-made templates to select from. There are so many free options, and it's very intuitive and easy to use. Also, it's very cheap and easy to use!
              <div class="w-100 mt-2">
              <div class="d-flex">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/blank_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                </div>
              </div>
            </div>
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="second-quote-style" alt="inverted commas">
          </div>
          <div class="template-review-container">
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="quote-style" alt="inverted commas">
            <div class="img-container tmplte-img-container">
            <img src="https://photoadking.com/images/Wesley-Finch.webp" class="review_img" loading="lazy" width="135px" height="135px" onerror=" this.src='https://photoadking.com/images/Wesley-Finch.jpg'" alt="user's photo">
              <p class="pt-1 mb-0 testimonial-name-style" >Wesley Finch </p>
              <p class="mb-0 testimonial-name1-style">Business Owner</p>
            </div>
            <div class="l-review-content templt-review-content">
            PhotoADking, excellent application for designing and creating content Social media content. With this friendly intuitive interface software, I created infographics, posters, posters for Facebook and Instagram, documents, cards, banners, certificates, graphics, etc, With professional-looking templates pretty easily. I just loved it!!
              <div class="w-100 mt-2">
              <div class="d-flex">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/half_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                </div>
              </div>
            </div>
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="second-quote-style" alt="inverted commas">
          </div>
          <div class="template-review-container">
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="quote-style" alt="inverted commas">
            <div class="img-container tmplte-img-container">
            <img src="https://photoadking.com/images/Yash-Mehta.webp" class="review_img" loading="lazy" width="135px" height="135px" onerror=" this.src='https://photoadking.com/images/Yash-Mehta.jpg'" alt="user's photo">
              <p class="pt-1 mb-0 testimonial-name-style" >Yash Mehta </p>
              <p class="mb-0 testimonial-name1-style">Business Owner</p>
            </div>
            <div class="l-review-content templt-review-content">
            It is a fantastic social media video tool I have come across in a while. It has run exceptionally well. I created social media video ads post and product videos for my business, and it allows beautiful modifications to the existing design templates. My Favourite tool For Designing! Love @photoadking .....!!!!!
              <div class="w-100 mt-2">
              <div class="d-flex">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                </div>
              </div>
            </div>
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="second-quote-style" alt="inverted commas">
          </div>
          <div class="template-review-container">
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="quote-style" alt="inverted commas">
            <div class="img-container tmplte-img-container">
            <img src="https://photoadking.com/images/Henry-Smith.webp" class="review_img" loading="lazy" width="135px" height="135px" onerror=" this.src='https://photoadking.com/images/Henry-Smith.jpg'" alt="user's photo">
              <p class="pt-1 mb-0 testimonial-name-style" >Henry Smith </p>
              <p class="mb-0 testimonial-name1-style">Business Owner</p>
            </div>
            <div class="l-review-content templt-review-content">
            Excellent marketing templates these guys provide. I always needed a tool to save my time and money to create social media ads for my business. And I found PhotoADKing that best fit for me as I have been using their photo editor for the last six months, and they have absolutely nailed it. The best part about having a premium account is that my issues got solved pretty quickly. I like it a lot :-)
              <div class="w-100 mt-2">
              <div class="d-flex">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                  <img src="https://test.photoadking.com/images/full_star.svg" width="15px" height="15px" class="w-15px m-0" alt="rating star">
                </div>
              </div>
            </div>
            <img src="https://photoadking.com/images/inverted_commas.svg" width="170px" height="75" loading="lazy" class="second-quote-style" alt="inverted commas">
          </div>
        </div>
      </div>
    </div>

  <!--
  **********************************************************
  Testimonial code ended
  **********************************************************
  -->

  {{--  main heading and description section --}}
  @if($template['main_h2'] !="" && $template['main_description'] !="")
    <div class="row no-gutters bg-white">
      <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 collection-container">
        <h2 class="heading mb-4 mt-4 sec-three-heading sec-three-special text-center">{{ $template['main_h2'] }}</h2>
        <p class="sub-heading mb-4 sec-three-content">{!! $template['main_description'] !!} </p>
      <div class="text-center">
      <a class="Click to sign-up and get started" title="Click to try now!"
         href="https://photoadking.com/app/#/sign-up">
        <button
          class="temp-first-block-button l-blue-bg mt-3 mb-4">Try
          It Now - It's Free
        </button>
      </a>
    </div>
      </div>
    </div>
  @endif

  {{--  FAQs section --}}
  @if(count($faqs) > 0)
    <div class="row no-gutters temp-sec-four-wrapper">
      <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 pt-0 collection-container temp-sec-faq-wrapper">
        <h2 class="heading mt-4 mb-4 sec-three-heading sec-three-special text-center py-3">FAQs</h2>
        <div class="s-icons-wrapper faq-wrapper">
          <div class="panel-group ul-faq-list sec-accordian" id="accordion">
            <div class="accordian-col">

              @foreach($faqs as $key => $faq)
                @if(($key + 1) % 2 != 0)
                <div class="panel panel-default li-box-shadow p-0 sec-faq-item">
                  <div class="panel-heading p-0">
                    <li class="ul-faq-list-li">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" rel="noreferrer" class="faq-question-title">
                        <div>
                          <h4>{{$faq->question}}</h4>
                        </div>
                        <svg class="ul-faq-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                          <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                            <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z"></path>
                          </g>
                        </svg>
                      </a>
                    </li>
                  </div>
                  <div id="collapse{{$key}}" class="panel-collapse collapse in p-0">
                    <div class="panel-body panel-body-desc faq-content-wrapper">
                      <p class="m-0">{!! $faq->answer !!} </p>
                    </div>
                  </div>
                </div>
                @endif
              @endforeach
            </div>

            <div class="accordian-col">

              @foreach($faqs as $key => $faq)
                @if(($key + 1) % 2 == 0)
                <div class="panel panel-default li-box-shadow p-0 sec-faq-item">
                  <div class="panel-heading p-0">
                    <li class="ul-faq-list-li">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" rel="noreferrer" class="faq-question-title">
                        <div>
                          <h4>{{$faq->question}}</h4>
                        </div>
                        <svg class="ul-faq-list-img" xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 1024.000000 1024.000000" preserveAspectRatio="xMidYMid meet">
                          <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)" fill="#424d5c" stroke="none">
                            <path d="M2832 10229 c-287 -44 -523 -262 -593 -549 -19 -79 -17 -260 4 -340 22 -83 76 -197 122 -256 19 -26 909 -927 1978 -2001 1068 -1075 1942 -1958 1942 -1962 0 -4 -877 -889 -1948 -1966 -1173 -1179 -1965 -1982 -1989 -2017 -87 -129 -122 -245 -122 -408 0 -131 16 -206 68 -315 94 -197 260 -332 475 -386 119 -30 282 -24 396 15 174 59 34 -75 2456 2360 1226 1233 2246 2264 2265 2291 54 74 101 178 120 262 25 113 16 293 -20 398 -59 172 67 40 -2346 2467 -1226 1232 -2251 2256 -2277 2276 -63 45 -174 98 -246 116 -77 20 -208 27 -285 15z"></path>
                          </g>
                        </svg>
                      </a>
                    </li>
                  </div>
                  <div id="collapse{{$key}}" class="panel-collapse collapse in p-0">
                    <div class="panel-body panel-body-desc faq-content-wrapper">
                      <p class="m-0">{!! $faq->answer !!} </p>
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


  <div class="bg-light pt-5 pb-4">
    <div class="col-12 col-xl-8 col-lg-10 col-md-11 col-sm-11 m-auto w-100 template-footer-container">
      <h2 class="heading sec-three-heading template-footer-headaing text-center">{!! $template['sub_h2'] !!}</h2>
      <p class="sub-heading mb-4">{!! $template['sub_description'] !!}</p>
    </div>
  </div>

  <div class="pb-3 pt-3 bg-light position-relative l-footer-svg text-center">
    <div class="l-footer-icon">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
           width="400" height="400" viewBox="0 0 400 400" class="svg-h-w-50px">
        <defs xmlns="http://www.w3.org/2000/svg">
          <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" class="svg-grad-stop-1"/>
            <stop offset="100%" class="svg-grad-stop-2"/>
          </linearGradient>
        </defs>
        <g id="svgg">
          <path id="path0"
                d="M191.761 99.569 C 186.168 101.358,181.934 106.373,181.366 111.880 L 181.148 113.996 176.929 114.127 C 169.179 114.367,168.986 114.755,168.817 130.469 C 168.652 145.822,168.761 146.889,170.712 148.981 L 172.210 150.586 196.255 150.586 L 220.299 150.586 221.771 149.115 L 223.242 147.644 223.353 132.884 L 223.464 118.124 222.564 116.777 C 221.145 114.653,219.543 114.064,215.181 114.063 L 211.417 114.063 211.141 112.222 C 209.729 102.804,200.494 96.775,191.761 99.569 M199.883 108.448 C 201.443 109.612,202.336 110.980,202.600 112.607 L 202.837 114.063 196.145 114.063 C 188.586 114.063,188.771 114.157,190.268 111.063 C 191.915 107.661,196.983 106.283,199.883 108.448 M134.375 129.395 C 132.744 130.374,132.011 131.143,131.169 132.759 L 130.078 134.855 130.078 213.143 C 130.078 282.552,130.146 291.594,130.676 292.863 C 131.674 295.250,133.203 296.911,135.306 297.890 L 137.305 298.820 195.853 298.824 C 238.602 298.827,254.803 298.708,255.890 298.382 C 257.870 297.789,259.981 295.997,261.046 294.005 L 261.914 292.383 262.021 263.672 C 262.112 239.164,262.048 235.075,261.588 235.742 C 236.684 271.827,233.506 276.160,231.078 277.328 C 228.688 278.478,215.357 282.031,213.432 282.031 C 206.444 282.031,203.097 277.553,202.157 266.943 C 201.290 257.165,201.347 255.616,202.669 252.994 L 203.776 250.799 180.628 250.693 C 157.941 250.588,157.457 250.570,156.400 249.781 C 152.427 246.814,152.528 241.530,156.610 238.779 L 157.928 237.891 185.507 237.886 L 213.086 237.881 219.016 229.301 L 224.946 220.720 191.477 220.614 L 158.008 220.508 156.626 219.648 C 152.274 216.942,152.602 210.249,157.188 208.166 C 158.713 207.473,160.565 207.439,196.374 207.430 L 233.960 207.422 235.460 205.371 C 236.285 204.243,242.626 195.098,249.551 185.048 L 262.142 166.775 262.028 150.477 L 261.914 134.180 260.818 132.315 C 260.216 131.290,258.998 129.972,258.113 129.386 L 256.504 128.320 244.291 128.207 L 232.078 128.094 231.957 138.559 L 231.836 149.023 230.759 151.216 C 229.474 153.832,226.159 157.093,223.551 158.308 L 221.680 159.180 196.289 159.180 L 170.898 159.180 168.673 158.137 C 162.021 155.018,160.777 151.995,160.621 138.574 L 160.500 128.125 148.495 128.125 L 136.489 128.125 134.375 129.395 M299.144 128.881 C 298.152 129.662,222.528 239.063,222.375 239.939 C 222.292 240.418,238.707 252.148,239.461 252.148 C 239.778 252.148,308.487 152.950,314.510 143.798 C 317.785 138.820,317.492 138.233,308.640 132.049 C 302.511 127.767,301.141 127.310,299.144 128.881 M235.415 177.824 C 240.168 180.425,239.852 187.630,234.894 189.702 C 232.782 190.584,159.037 190.481,157.166 189.593 C 151.793 187.044,152.629 178.702,158.388 177.386 C 161.637 176.644,234.010 177.056,235.415 177.824 M212.549 253.392 C 209.805 257.366,209.846 257.080,210.692 266.290 C 211.464 274.690,210.646 274.462,225.013 270.290 C 227.401 269.596,232.452 264.095,232.416 262.228 C 232.409 261.904,216.447 250.532,215.475 250.158 C 215.114 250.020,214.119 251.120,212.549 253.392 "
                stroke="none" fill="#fbfbfc" fill-rule="evenodd"/>
          <path id="path1"
                d="M183.789 1.230 C 136.936 4.935,92.447 25.523,58.985 58.985 C -18.823 136.793,-18.823 263.207,58.985 341.015 C 136.788 418.818,263.212 418.818,341.015 341.015 C 372.019 310.011,391.990 269.633,397.633 226.546 C 414.190 100.116,310.807 -8.814,183.789 1.230 M199.908 99.385 C 205.749 100.940,210.214 106.043,211.141 112.222 L 211.417 114.063 215.181 114.063 C 219.543 114.064,221.145 114.653,222.564 116.777 L 223.464 118.124 223.353 132.884 L 223.242 147.644 221.771 149.115 L 220.299 150.586 196.255 150.586 L 172.210 150.586 170.712 148.981 C 168.761 146.889,168.652 145.822,168.817 130.469 C 168.986 114.755,169.179 114.367,176.929 114.127 L 181.148 113.996 181.366 111.880 C 182.258 103.234,191.350 97.107,199.908 99.385 M192.710 108.381 C 191.668 109.020,190.790 109.984,190.268 111.063 C 188.771 114.157,188.586 114.063,196.145 114.063 L 202.837 114.063 202.600 112.607 C 201.896 108.267,196.591 106.000,192.710 108.381 M160.621 138.574 C 160.777 151.995,162.021 155.018,168.673 158.137 L 170.898 159.180 196.289 159.180 L 221.680 159.180 223.551 158.308 C 226.159 157.093,229.474 153.832,230.759 151.216 L 231.836 149.023 231.957 138.559 L 232.078 128.094 244.291 128.207 L 256.504 128.320 258.113 129.386 C 258.998 129.972,260.216 131.290,260.818 132.315 L 261.914 134.180 262.028 150.477 L 262.142 166.775 249.551 185.048 C 242.626 195.098,236.285 204.243,235.460 205.371 L 233.960 207.422 196.374 207.430 C 160.565 207.439,158.713 207.473,157.188 208.166 C 152.602 210.249,152.274 216.942,156.626 219.648 L 158.008 220.508 191.477 220.614 L 224.946 220.720 219.016 229.301 L 213.086 237.881 185.507 237.886 L 157.928 237.891 156.610 238.779 C 152.528 241.530,152.427 246.814,156.400 249.781 C 157.457 250.570,157.941 250.588,180.628 250.693 L 203.776 250.799 202.669 252.994 C 201.347 255.616,201.290 257.165,202.157 266.943 C 203.097 277.553,206.444 282.031,213.432 282.031 C 215.357 282.031,228.688 278.478,231.078 277.328 C 233.506 276.160,236.684 271.827,261.588 235.742 C 262.048 235.075,262.112 239.164,262.021 263.672 L 261.914 292.383 261.046 294.005 C 259.981 295.997,257.870 297.789,255.890 298.382 C 254.803 298.708,238.602 298.827,195.853 298.824 L 137.305 298.820 135.306 297.890 C 133.203 296.911,131.674 295.250,130.676 292.863 C 130.146 291.594,130.078 282.552,130.078 213.143 L 130.078 134.855 131.169 132.759 C 132.011 131.143,132.744 130.374,134.375 129.395 L 136.489 128.125 148.495 128.125 L 160.500 128.125 160.621 138.574 M308.640 132.049 C 317.492 138.233,317.785 138.820,314.510 143.798 C 308.487 152.950,239.778 252.148,239.461 252.148 C 238.707 252.148,222.292 240.418,222.375 239.939 C 222.528 239.063,298.152 129.662,299.144 128.881 C 301.141 127.310,302.511 127.767,308.640 132.049 M158.388 177.386 C 152.629 178.702,151.793 187.044,157.166 189.593 C 159.037 190.481,232.782 190.584,234.894 189.702 C 239.852 187.630,240.168 180.425,235.415 177.824 C 234.010 177.056,161.637 176.644,158.388 177.386 M224.215 256.141 C 228.722 259.316,232.412 262.055,232.416 262.228 C 232.452 264.095,227.401 269.596,225.013 270.290 C 213.566 273.614,212.780 273.740,211.691 272.427 C 211.209 271.846,210.188 262.653,210.177 258.789 C 210.170 256.543,214.353 249.728,215.475 250.158 C 215.775 250.274,219.709 252.966,224.215 256.141 "
                stroke="none" fill="url(#grad2)" fill-rule="evenodd"/>
        </g>
      </svg>
    </div>
    <p class="l-footer-heading mt-5">Get Started For Free</p>
    <p class="l-footer-content">Easily customize any design and give your images a fresh new look at any
      moment! NO
      design skills or technical knowledge required.</p>
    <div class="text-center">
      <a class="Click to sign-up and get started" title="Click to try now!"
         href="https://photoadking.com/app/#/sign-up">
        <button
          class="l-first-block-button l-blue-bg mt-3 mb-4">Try
          It Now - It's Free
        </button>
      </a>
    </div>
  </div>
  <div class="l-footer-container row no-gutters">
    <div
      class="col-12 col-xl-10 col-lg-12 col-md-12 col-sm-12 m-auto l-footer-content-container justify-content-around">
      <div class="l-footer-logo-container mr-5">
        <img src="https://photoadking.com/images/photoadking_footer.png" loading="lazy"
             data-src="https://photoadking.com/images/photoadking_footer.png" class="footer-logo height-init"
             alt="image not found">
        <ul class="justify-content-around l-footer-icon-container">
          <li><a href="https://www.facebook.com/Photoadking/" target="_blank"
                 rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                  <path
                    d="M13.81,26V16.92H10.75V13.21h3.06V10.1A4.1,4.1,0,0,1,17.91,6h3.2V9.33H18.82a1.3,1.3,0,0,0-1.3,1.3v2.58H21l-0.49,3.71h-3V26"
                    fill="#e1e1e1"></path>
                </svg>
              </div>
            </a></li>
          <li><a href="https://www.instagram.com/photoadking/" target="_blank" rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="1024.000000pt"
                     height="1024.000000pt" viewBox="0 0 1024.000000 1024.000000"
                     preserveAspectRatio="xMidYMid meet" style="height: 26px;width: 26px;">
                  <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                     fill="#e1e1e1" stroke="none">
                    <path
                      d="M3868 7350 c-128 -27 -210 -56 -328 -116 -344 -176 -581 -495 -654 -883 -14 -73 -16 -229 -16 -1231 0 -1249 -2 -1208 57 -1394 28 -89 96 -233 145 -305 87 -128 221 -262 348 -348 72 -49 218 -118 306 -146 185 -59 144 -57 1394 -57 1249 0 1208 -2 1394 57 105 33 273 119 360 185 251 189 421 464 480 777 14 73 16 229 16 1231 0 1249 2 1208 -57 1394 -33 105 -119 273 -185 360 -189 251 -464 421 -777 480 -73 14 -228 16 -1240 15 -1083 0 -1162 -2 -1243 -19z m2457 -408 c310 -87 529 -306 617 -617 l23 -80 0 -1125 0 -1125 -23 -80 c-88 -311 -306 -529 -617 -617 l-80 -23 -1125 0 -1125 0 -80 23 c-311 88 -529 306 -617 617 l-23 80 0 1125 0 1125 23 80 c97 345 371 589 713 634 35 5 552 8 1149 7 l1085 -1 80 -23z">
                    </path>
                    <path
                      d="M6240 6611 c-154 -47 -242 -216 -196 -373 23 -79 97 -159 174 -188 152 -57 320 11 384 158 31 69 28 176 -6 247 -33 66 -101 128 -163 149 -48 17 -149 20 -193 7z">
                    </path>
                    <path
                      d="M4880 6259 c-222 -47 -417 -153 -581 -318 -154 -154 -250 -318 -307 -529 -24 -89 -26 -113 -26 -292 0 -179 2 -203 26 -292 57 -211 153 -375 307 -529 154 -154 318 -250 529 -307 89 -24 113 -26 292 -26 179 0 203 2 292 26 211 57 375 153 529 307 154 154 250 318 307 529 24 89 26 113 26 292 0 179 -2 203 -26 292 -57 211 -153 375 -307 529 -166 167 -360 272 -588 318 -136 28 -339 28 -473 0z m421 -409 c153 -37 303 -133 405 -260 222 -274 222 -666 0 -940 -262 -325 -733 -377 -1056 -116 -325 262 -377 733 -116 1056 187 232 476 330 767 260z">
                    </path>
                  </g>
                </svg>
              </div>
            </a></li>
          <li><a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" target="_blank"
                 rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                  <path
                    d="M23,9H9a3,3,0,0,0-3,3v8a3,3,0,0,0,3,3H23a3,3,0,0,0,3-3V12A3,3,0,0,0,23,9ZM14,19.5v-7l6,3.58Z"
                    fill="#e1e1e1"></path>
                </svg>
              </div>
            </a></li>
          <li><a href="https://www.pinterest.com/photoadking/" target="_blank" rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="1024.000000pt"
                     height="1024.000000pt" viewBox="0 0 1024.000000 1024.000000"
                     preserveAspectRatio="xMidYMid meet" style="height: 26px; width: 26px;">
                  <g transform="translate(0.000000,1024.000000) scale(0.100000,-0.100000)"
                     fill="#e1e1e1" stroke="none">
                    <path
                      d="M4833 7664 c-938 -113 -1594 -673 -1764 -1504 -21 -100 -24 -143 -24 -335 0 -248 9 -307 75 -506 45 -137 101 -251 178 -369 128 -194 382 -390 475 -366 22 5 29 17 46 74 52 176 94 359 88 382 -4 14 -27 52 -52 85 -130 172 -208 420 -208 665 -3 642 461 1179 1130 1310 154 31 401 37 556 16 192 -27 372 -87 522 -173 327 -188 531 -487 590 -868 32 -199 6 -535 -56 -749 -115 -398 -339 -683 -631 -804 -144 -60 -326 -79 -452 -46 -231 60 -384 275 -347 489 14 81 43 181 132 460 129 403 161 566 140 706 -22 142 -92 250 -198 306 -260 138 -590 -8 -729 -323 -49 -111 -65 -191 -71 -344 -6 -168 9 -282 58 -422 l30 -87 -195 -788 c-107 -434 -200 -818 -206 -853 -18 -109 -23 -375 -11 -545 14 -191 49 -473 63 -498 6 -12 18 -17 36 -15 21 2 41 25 100 113 172 255 346 576 415 765 19 52 81 264 138 471 l102 376 81 -82 c70 -71 96 -89 196 -138 135 -66 225 -93 371 -108 202 -22 471 19 690 106 580 231 994 812 1084 1524 24 184 16 502 -14 643 -84 387 -298 736 -606 988 -290 237 -660 391 -1068 445 -146 19 -505 19 -664 -1z"/>
                  </g>
                </svg>
              </div>
            </a></li>
          <li><a href="https://twitter.com/photoadking" target="_blank" rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                  <path
                    d="M6.15,22.21a11.54,11.54,0,0,0,17.7-9.75q0-.24,0-0.47a9.07,9.07,0,0,0,2-2.06,9.36,9.36,0,0,1-2.29.59h0a4.29,4.29,0,0,0,1.76-2.25,14,14,0,0,1-2,.82l-0.59.18A4,4,0,0,0,15.86,13a12.19,12.19,0,0,1-8.31-4.2S5.62,11.4,8.71,14.12A4.16,4.16,0,0,1,7,13.6a3.85,3.85,0,0,0,3.2,4,3.7,3.7,0,0,1-1.79.07,4,4,0,0,0,3.72,2.83,7.77,7.77,0,0,1-5.94,1.7h0Zm0,0"
                    fill="#e1e1e1"></path>
                </svg>
              </div>
            </a></li>
            <li><a href="https://in.linkedin.com/showcase/photoadking" target="_blank" rel="noreferrer nofollow">
              <div class="footer-s-ic">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="ln-icon">
                  <path
                    d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"
                    fill="#e1e1e1"></path>
                </svg>
              </div>
            </a></li>
        </ul>
      </div>
      <ul class="l-photoadking-footer-menu-container text-uppercase">
        <li>PhotoAdKing</li>
        <li class="text-uppercase"><a href="https://photoadking.com/">Home</a></li>
        <li class="text-uppercase"><a href="https://photoadking.com/#Features">Features</a></li>
        <li class="text-uppercase"><a href="https://photoadking.com/templates/">Templates</a></li>
        <li class="text-uppercase"><a href="https://photoadking.com/go-premium/">Pricing</a></li>
        <li class="text-uppercase"><a href="https://blog.photoadking.com/" target="_blank"
                                      rel="noreferrer">Learn</a>
        </li>
        <li class="text-uppercase"><a href="https://helpphotoadking.freshdesk.com/support/home"
                                      target="_blank" rel="noreferrer">Help</a></li>
        <li class="text-uppercase"><a href="https://photoadking.com/whats-new/" target="_blank" >what's new</a></li>
      </ul>
      <ul class="l-photoadking-footer-menu-container l-mob-hide">
        <li>Products</li>
        <li><a href="https://photoadking.com/design/online-flyer-maker/">Flyers</a></li>
        <li><a href="https://photoadking.com/design/brochures/">Brochures</a></li>
        <li><a href="https://photoadking.com/design/logo-design/">Logo Design</a></li>
        <li><a href="https://photoadking.com/design/resumes/">Resumes</a></li>
        <li><a href="https://photoadking.com/design/business-cards/">Business Cards</a></li>
        <li><a href="https://photoadking.com/design/invitation-cards/">Invitation Cards</a></li>
        <li><a href="https://photoadking.com/design/certificates/">Certificates</a></li>
        <li><a href="https://photoadking.com/design/product-ads/">Product Ads</a></li>
        <li><a href="https://photoadking.com/design/poster-maker/">Posters</a></li>
        <li><a href="https://photoadking.com/design/biodata/">Biodata</a></li>
        <li><a href="https://photoadking.com/design/announcements/">Announcements</a></li>
        <li><a href="https://photoadking.com/design/covers/">Covers</a></li>
      </ul>
      <ul class="l-photoadking-footer-menu-container l-xs-hide">
        <li>Social Media</li>
        <li><a href="https://photoadking.com/design/facebook-post/">Facebook Post</a></li>
        <li><a href="https://photoadking.com/design/instagram-post/">Instagram Post</a></li>
        <li><a href="https://photoadking.com/design/youtube-thumbnail/">YouTube Thumbnail</a></li>
        <li><a href="https://photoadking.com/design/mobile-fb-cover/">Facebook Cover</a></li>
        <!-- <li><a href="https://photoadking.com/design/desktop-fb-cover/">Desktop Facebook Cover</a></li> -->
        <li><a href="https://photoadking.com/design/pinterest-post/">Pinterest Post</a></li>
        <li><a href="https://photoadking.com/design/twitter-post/">Twitter Post</a></li>
        <li><a href="https://photoadking.com/design/tumblr-post/">Tumblr Post</a></li>
        <li><a href="https://photoadking.com/design/small-post/">Small Cards</a></li>

      </ul>
      <ul class="l-photoadking-footer-menu-container l-xs-hide">
        <li>Banners</li>
        <li><a href="https://photoadking.com/design/leaderboards/">Leaderboards</a></li>
        <li><a href="https://photoadking.com/design/skyscrapper/">Skyscraper</a></li>
        <li><a href="https://photoadking.com/design/inline-rectangle/">Inline Rectangle</a></li>
        <li><a href="https://photoadking.com/design/roll-up-banner/">Roll Up Banner</a></li>
        <li><a href="https://photoadking.com/design/facebook-ads/">Facebook Ads</a></li>
        <li><a href="https://photoadking.com/design/google-ads/">Google Ads</a></li>
      </ul>
      <ul class="l-early-access">
        <li>Get Early Access!</li>
        <li class="mb-0">Subscribe now for PhotoADKing newsletter</li>
        <li>
          <!-- Begin Mailchimp Signup Form -->
          <div id="mc_embed_signup">
            <form action="https://photoadking.us19.list-manage.com/subscribe/post" method="post"
                  id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
                  target="_blank" rel="noreferrer" novalidate>
              <div id="mc_embed_signup_scroll">
                <label for="mce-EMAIL" style="margin: 0; height: 0; width: 0; font-size: 0;">Email
                  Address <span class="asterisk">*</span>
                </label>
                <input type="email" value="" name="EMAIL" class="required email l-footer-textbox"
                       id="mce-EMAIL" placeholder="email address" required>
                <input type="hidden" name="u" value="feb0af46e67edefbcae1dd666">
                <input type="hidden" name="id" value="33bef8f75a">
                <div id="mce-responses" class="clear">
                  <div class="response" id="mce-error-response"></div>
                  <div class="response" id="mce-success-response"></div>
                </div>
                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input
                    type="text" name="b_feb0af46e67edefbcae1dd666_33bef8f75a" tabindex="-1"
                    value=""></div>
                <div class="clear mt-3">
                  <input type="submit" value="Subscribe" name="subscribe"
                         id="mc-embedded-subscribe" mat-raised-button class="l-footer-btn"></div>
              </div>
            </form>
          </div>
          <!-- The Modal -->
          <div class="modal fade" id="myModal">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title" id="modalTtl">Error!</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body" id="modalMsg" style="text-transform: initial !important;">
                  Modal body..
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal"
                          id="modalbtnName">Close
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!--End mc_embed_signup-->
        </li>
      </ul>
    </div>
    <div class="col-12 col-lg-10 col-md-12 col-sm-12 ml-auto mr-auto mt-4 l-last-div">
                <span class="copyright-content">
                    &copy; {{ date('Y') }} PHOTOADKING. ALL Rights Reserved.
                </span>
      <ul class="l-footer-last-menu">
        <li><a href="https://photoadking.com/legal-information/terms-of-service/">Terms Of Service</a></li>
        <li><a href="https://photoadking.com/legal-information/refund-policy/"> Refunds</a></li>
        <li><a href="https://photoadking.com/legal-information/privacy-policy/">Privacy</a></li>
        <li><a href="https://photoadking.com/legal-information/cookie-policy/">Cookie Policy</a></li>
        <li><a href="https://photoadking.com/legal-information/contact/">Contact</a></li>
      </ul>
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
    var pathString = window.location.pathname

    var visitArr = pathString.substring(1, pathString.length - 1).split("/");
    if (visitArr[1] == "templates" || visitArr[1] == "design")
    {
        visitArr.shift();
    }
    localStorage.setItem("userVisited", visitArr);;
    // When the user scrolls down 50px from the top of the document, resize the header's font size
    function scrollFunction() {
        if (document.getElementById("mainbody").scrollTop > 50 || document.getElementById("mainbody").scrollTop > 50) {
            document.getElementById("docHeader").classList.remove("l-header-big");
            document.getElementById("docHeader").classList.add("l-header-small");
        } else {
            document.getElementById("docHeader").classList.remove("l-header-small");
            document.getElementById("docHeader").classList.add("l-header-big");
        }
    }

    function init() {
        var imgDefer = document.getElementsByTagName('img');
        for (var i = 0; i < imgDefer.length; i++) {
            if (imgDefer[i].getAttribute('data-src')) {
                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
            }
        }
    }

    window.onload = init;
    let card_width = Math.floor($(".card-item:first-child").width());
    let temp_width = Math.floor($(".temparary-item:first-child").width());
    var cat_list;
    var requestdata;
    $(document).ready(function ($) {

      $('.panel-collapse').on('show.bs.collapse', function () {
      $(this).siblings('.panel-heading').addClass('active-ul-faq-li-img');
    });

    $('.panel-collapse').on('hide.bs.collapse', function () {
      $(this).siblings('.panel-heading').removeClass('active-ul-faq-li-img');
    });

        // $('#header').load('../header.html');
       /*  try {
            window.fcWidget.init({
                token: "ef3bb779-2dd8-4a3c-9930-29d90fca9224",
                host: "https://wchat.freshchat.com"
            });
        } catch (error) {
            console.log(error);
        } */
        $('.overlay').hide();
        // mobile menu open close js
        $('#mob-menu').click(function () {
            $(".l-mob-menu-container").css("transform", "scaleX(1)");
            $('.overlay').show();
        });
        $('.overlay').click(function () {
            $('.overlay').hide();
            $(".l-mob-menu-container").css("transform", "scaleX(0)");
        });
        $('#mobmenu li').click(function () {
            $('.overlay').hide();
            $(".l-mob-menu-container").css("transform", "scaleX(0)");
        });

        var l_r = localStorage.getItem('l_r');
        var ut = localStorage.getItem('ut');
        if (l_r != null && l_r != '' && typeof l_r != 'undefined' && ut != '' && ut != null && typeof ut !=
            'undefined') {
            $('#hd-login').hide();
            $('#hd-logn').hide();
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
        }

        // $('#footer').load('../footer.html');
        validateEmail = function (email) {
            var re =
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
        let catList = $('.sub_cat_list .categorylist');
        for (let i = 0; i < catList.length; i++) {
            let category = $(catList[i]).children('ul').children('li');
            let subcategories = $(category).find('a');
            for (let j = 0; j < subcategories.length; j++) {
                let subcategory = subcategories[j];
                if ($(subcategory).attr('href').replace(/^.*\/\/[^\/]+/, '') == (window.location.pathname)) {
                    $("#nav-images-tab").addClass(" active");
                    if($("#nav-videos-tab").hasClass("active")){
                        $("#nav-videos-tab").removeClass('active');
                    }
                    $(subcategory).parents('li').addClass(" active");
                    $(catList[i]).addClass(" active");
                    $(catList[i]).addClass(" activeSubCategory");
                    $(catList[i]).addClass(" activePageSubCategory");
                   /*  if ($(window).width() >= 1151) {
                        $(catList[i]).children('ul').animate({"height": $(catList[i]).children('ul')[0].scrollHeight}, 50);
                        $(catList[i]).children('a').children('svg').css("transform", "rotate(-180deg)");
                        // let catList = $('.sub_cat_list .categorylist');
                        $(catList[i]).children('ul').css("margin", "5px 0px");
                    }else */if ($(window).width() <= 1151){
                        $(catList[i]).children('ul').addClass(" active");
                        $(catList[i]).children('ul').css("display", "flex");
                        $(subcategory).addClass(" active");
                    }
                    $("#nav-videos").removeClass('active');
                    $('#nav-images').addClass("active");
                }
            }
        }
        let videoCatList = $('.sub_cat_list_video .categorylist');
        for (let i = 0; i < videoCatList.length; i++) {
            let category = $(videoCatList[i]).children('a');
            if ($(category).attr('href').replace(/^.*\/\/[^\/]+/, '') ==  (window.location.pathname)) {
                $("#nav-videos-tab").addClass(" active");
                if($("#nav-images-tab").hasClass("active")){
                    // $("#sub_cat_list_video").css("display","block");
                    // $("#sub_cat_list").css("display","none");
                    $("#nav-images-tab").removeClass('active');

                }
                $('#nav-images').removeClass("active");
                $("#nav-videos").addClass('active');
                $(videoCatList[i]).addClass(" active");
                $(videoCatList[i]).addClass(" activeVideoCategory");
            }
        }

        var tabs = $('.dashboard-tabs');
        var selector = $('.dashboard-tabs').find('a').length;
        var activeItem = tabs.find('.active');
        var activeWidth = activeItem.innerWidth();
        $(".selector").css({
            "left": activeItem.position.left + "px",
            "width": activeWidth + "px"
        });

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
            if (this.id == "nav-videos-tab") {
                $('#nav-images').removeClass("active");
                $("#nav-videos").addClass('active');
            } else if (this.id == "nav-images-tab") {
                $("#nav-videos").removeClass('active');
                $('#nav-images').addClass("active");
            }
            var activeItem = tabs.find('.active');
            if (activeItem[0].id == "nav-videos-tab") {
                if ($(window).width() <= 1150){
                    $("#sub_cat_list_video").css("display","flex");
                    $("#sub_cat_list").css("display","none");
                }else{
                    $("#sub_cat_list_video").css("display","block");
                    $("#sub_cat_list").css("display","none");
                }
                showVideoTemplate();
            } else if (activeItem[0].id == "nav-images-tab") {
                if ($(window).width() <= 1150){
                    $("#sub_cat_list").css("display","flex");
                    $("#sub_cat_list_video").css("display","none");
                }else{
                    $("#sub_cat_list").css("display","block");
                    $("#sub_cat_list_video").css("display","none");
                }
                showTemplate();
            }
        });

        if (activeItem[0].id == "nav-videos-tab") {
            if ($(window).width() <= 1150){
                $("#sub_cat_list_video").css("display","flex");
                $("#sub_cat_list").css("display","none");
            }else{
                $("#sub_cat_list_video").css("display","block");
                $("#sub_cat_list").css("display","none");
            }

            showVideoTemplate();
        } else if (activeItem[0].id == "nav-images-tab") {
            if ($(window).width() <= 1150){
                $("#sub_cat_list").css("display","flex");
                $("#sub_cat_list_video").css("display","none");
            }else{
                $("#sub_cat_list").css("display","block");
                $("#sub_cat_list_video").css("display","none");
            }

            showTemplate();
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
        } */
        function showTemplate() {
            $('#card-item').empty();
            $.ajax({
                    type: "POST",
                    // url: "http://192.168.0.116/photoadking_testing/api/public/api/getStaticPageTemplateListById",
                    url: "{!! $template['API_getStaticPageTemplateListById'] !!}",
                    data: JSON.stringify({
                        "static_page_id": JSON.parse({!! $template['static_page_id'] !!})
                    }),
                    dataType: 'json',
                    contentType: 'application/json',
                    error: function (err) {
                        alert("Could not connect to the server.");
                    },
                    success: function (result) {
                        template_list = result.data.template_list;
                        let defaultURL;
                        let catList = $('.sub_cat_list .categorylist');
                        for (let i = 0; i < catList.length; i++) {
                            let category = $(catList[0]).children('ul').children('li').children('a');
                            defaultURL = $(category).attr('href');
                        }

                        // cat_list = result.data.tag_list;
                        var tmpRedURL = "{!! $template['static_page_id'] !!}";
                        tmpRedURL == 0 ? window.location.href = defaultURL : '';
                        let getImageSize = new Promise(function (resolve, reject) {
                        if (template_list[0].sample_image) {
                            var img = new Image();
                            img.src = template_list[0].sample_image;
                            img.onload = function () {
                                if (this.width > this.height + (this.width / 6)) {
                                    $('#card-item').removeClass('template-wrapper');
                                    $('#card-item').addClass('template-wrapper-1');
                                    card_width = temp_width;
                                    resolve();
                                } else {
                                    resolve();
                                }
                            };
                          }else{
                            $('#card-item').css('min-height','50px');
                          }
                        });
                        // if(template_list[0].sample_image){
                        //   getImageSize(template_list[0].sample_image);
                        // }
                        // setTimeout(() => {
                        getImageSize.then(
                          function (result) {
                            for (var k = 0; k < template_list.length; k++) {
                              let ratio = template_list[k].height / template_list[k].width;
                              let card_height = (card_width * ratio) < 551 ? (Math.floor(card_width * ratio) >= 40 ? Math.floor(card_width * ratio) :40 ): 550;
                                if ($(document).width() > 768) {
                                    $('#card-item').append(
                                        '<div class="col col-sm-12 p-0 text-center card-ol-block card-item template-page-item "><div class="img_wrper prograssive_img "><a onclick="storeDetails(' +
                                        template_list[k].sub_category_id + ", " + template_list[k].catalog_id + ", " + template_list[k]
                                            .content_id +
                                        ')" href="https://photoadking.com/app/#/editor/' + template_list[k].sub_category_id + '/' +
                                        template_list[k].catalog_id + '/' + template_list[k].content_id +
                                        '" style="text-decoration:none;"><img id="imge' + k + '" loading="lazy" onload="removeShimmer(imge' +k + ')" draggable="false" src="' +
                                        template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                        '" alt="' + template_list[k].catalog_name + " " + template_list[k].sub_category_name + " Template" + '" data-isLoaded = "false"><div class="crd-overlay"></div><button class="crd-ol-btn">Start from this</button></a></div></div>'
                                    );
                                  } else if($(document).width() <= 768 && k<10){
                                    $('#card-item').append(
                                      '<div class="col col-sm-12 p-0 text-center card-item template-page-item"><div class="img_wrper prograssive_img "><a href="https://photoadking.com/app/#/" style="text-decoration:none;"><img loading="lazy" onload="removeShimmer(imge' +k + ')" draggable="false"  id="imge' + k + '" src="' +
                                      template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                      '" alt="' + template_list[k].catalog_name + " " + template_list[k].sub_category_name + " Template" + '" data-isLoaded = "false"></a></div></div>');
                                    }
                                    let img_id = '#imge' + k;
                                 if(card_width >= template_list[k].width){
                                   card_height = template_list[k].height;
                                   $(img_id).css('width', template_list[k].width + 'px');
                                   // $(img_id).parents('div.img_wrper').css('line-height', template_list[k].height + 'px');
                                 }else{
                                   $(img_id).css('width', card_width + 'px');
                                   // $(img_id).parents('div.img_wrper').css('line-height', card_height + 'px');
                                 }
                                 $(img_id).css('height', card_height + 'px');
                                     // $(img_id).css('width', card_width + 'px');
                                     // $(img_id).attr('height', card_height + 'px');
                                     // $(img_id).attr('width', card_width + 'px');
                                     // let wrapper_height= card_height+3;
                                     $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                                     $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                            }
                            // $('img').on('load', function () {
                                // if ($(this).attr('data-isLoaded') == 'false') {
                                //     $(this).attr('src', $(this).attr('data-src'));
                                //     $(this).attr('data-isLoaded', 'true');
                                // }
                                // $(img_id).parents('div.img_wrper').removeClass('prograssive_img');
                            // });
                            $('#card-item').css('min-height','100px');
                            }
                           
                        );
                        // }, 1500);

                    }
                }

            );
        }

        function showVideoTemplate() {
            $('#card-item').empty();
            requestdata =
                JSON.stringify({
                    "search_category": "{!! isset($template['search_category']) && $template['search_category'] !='' ? $template['search_category']: "" !!}",
                    "content_type": "{!! isset($template['content_type']) && $template['content_type'] !='' ? $template['content_type']: "" !!}",
                    "page": 1
                });
            $.ajax({
                type: "POST",
                // url: "http://192.168.0.116/photoadking_testing/api/public/api/getStaticPageTemplateListByTag",
                url: "{!! $template['API_getStaticPageTemplateListByTag'] !!}",
                data: requestdata,
                dataType: 'json',
                contentType: 'application/json',
                error: function (err) {
                    alert("Could not connect to the server.");
                },
                success: function (result) {
                    // cat_list = result.data.sub_category_list;
                    // sub_cat_list = result.data.catalog_list;
                    template_list = result.data.template_list;
                    var activated_cat_id;
                    let videoCatList = $('.sub_cat_list_video .categorylist');
                    let defaultURL = $(videoCatList[0]).children('a').attr('href');
                    let getImageSize = new Promise(function (resolve, reject) {
                        if (template_list[0].sample_image) {
                          var img = new Image();
                          img.src = template_list[0].sample_image;
                          img.onload = function () {
                              if (this.width > this.height + (this.width / 6)) {
                                  $('#card-item').removeClass('template-wrapper');
                                  $('#card-item').addClass('template-wrapper-1');
                                  card_width = temp_width;
                                  resolve();
                              } else {
                                  resolve();
                              }
                          };
                        }else{
                            $('#card-item').css('min-height','50px');
                          }
                    });
                    // cat_list = result.data.tag_list;
                    var tmpRedURL = "{!! isset($template['search_category']) && $template['search_category'] !='' ? $template['search_category']: "" !!}";
                    tmpRedURL == '' ? window.location.href = defaultURL : '';
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
                    for (var k = 0; k < template_list.length; k++) {
                      let ratio = template_list[k].height / template_list[k].width;
                      let card_height = (card_width * ratio) < 551 ? card_width * ratio : 550;
                        if ($(document).width() > 768) {
                            $('#card-item').append(
                                '<div class="col col-sm-12 p-0 text-center card-ol-block card-item template-page-item video-card-item-1 mx-auto" onmouseenter="showVideo(' + k + ')" onmouseleave="hideVideo(' + k + ')" ><div  class="img_wrper prograssive_img " onclick="playVideoInModel(' + k + ')" ><img loading="lazy" onload="removeShimmer(i' +k + ')" draggable="false" id ="i' + k + '" src="' +
                                template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                                '" alt="' + template_list[k].catalog_name + " " + template_list[k].sub_category_name + " Template" + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + k + '" loop muted><source draggable="false" type="video/mp4" src="' +
                                template_list[k].content_file +
                                '"  ></video><div><img loading="lazy" src="../../images/Spinner1.gif" class="video-loader" id = "ldr' + k + '" ></div><div id = "playButton' + k + '" class= "play-btn-ic"><img loading="lazy" src="../../images/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' + k
                                + '"><div class="custom-seekbar" id="custom-seekbar' + k + '"><span id="cs' + k + '"></span></div></div></div><a class= "editVideo-txt" href="' + editroUrl +
                                template_list[k].sub_category_id + '/' + template_list[k].catalog_id + '/' + template_list[k].content_id +
                                '"><div class="edit-video-button mx-auto"  id = "editButton' + k + '"><img loading="lazy" src="../../images/Edit.svg" alt="edit icon" class="editButton-ic">EDIT VIDEO</div></a></div>'
                            );
                          } else if($(document).width() <= 768 && k<10){
                            $('#card-item').append(
                              '<div class="col col-sm-12 p-0 text-center card-ol-block card-item template-page-item video-card-item-1 mx-auto" onmouseenter="showVideo(' + k + ')" onmouseleave="hideVideo(' + k + ')" ><div class="img_wrper prograssive_img "><img loading="lazy" onload="removeShimmer(i' +k + ')" draggable="false" id ="i' + k + '" src="' +
                              template_list[k].webp_thumbnail + '" onerror=" this.src=' + "'" + template_list[k].sample_image + "'" +
                              '" alt="' + template_list[k].catalog_name + " " + template_list[k].sub_category_name + " Template" + '" data-isLoaded="false" ><video class="mx-auto template-video" id = "v' + k + '" loop muted><source draggable="false" type="video/mp4" src="' +
                              template_list[k].content_file +
                              '"  ></video><div><img loading="lazy" src="../../images/Spinner1.gif" alt="loader icon" class="video-loader" id = "ldr' + k + '" ></div><div id = "playButton' + k + '" class= "play-btn-ic"><img loading="lazy" src="../../images/play.svg" alt="play icon" class="playButton-ic" ></div><div class= "seekbar-container" id="seekbar' + k
                              + '"><div class="custom-seekbar"  id="custom-seekbar' + k + '"><span id="cs' + k + '"></span></div></div></div><a class= "editVideo-txt" href="https://photoadking.com/app/#/"><div class="edit-video-button mx-auto" id = "editButton' + k + '">EDIT VIDEO</div></a></div>'
                              );
                            }
                            let img_id = '#i' + k;
                            if(card_width >= template_list[k].width){
                            card_height = template_list[k].height;
                                  $(img_id).css('width', template_list[k].width + 'px');
                                }else{
                                  $(img_id).css('width', card_width + 'px');
                                }
                            $(img_id).css('height', card_height + 'px');
                            
                            $(img_id).parents('div.img_wrper').css('width', card_width + 'px');
                            $(img_id).parents('div.img_wrper').css('height', card_height + 'px');
                          }
                          // $('img').on('load', function () {
                            // if ($(this).attr('data-isLoaded') == 'false') {
                              //     $(this).attr('src', $(this).attr('data-src'));
                        //     $(this).attr('data-isLoaded', 'true');
                        // }
                      //  let img_id = '#i' + k;
                        // $(img_id).parents('div.img_wrper').removeClass('prograssive_img');

                        setTimeout(function () {
                            $('.play-btn-ic').css('display', 'block');
                            $('#card-item').css('min-height','100px');
                        }, 5000);
                    // });
                    // }, 1500);
                    }
                  );
                }
            });
        }
      if(document.getElementById("popular_tag_container") != null) {
      if($("#popular_tag_container").height() < '90') {
            $("#btn-wrapper").addClass('disply-none');
            $("#popular_tag_container").addClass('text-center');
        } else if($("#popular_tag_container").height() >= '86') {
            $("#popular_tag_container").css("height", "85px");
        }
      }
      if(document.getElementById("similar_tag_container") != null) {
        if($("#similar_tag_container").height() < '90') {
            $("#btn-wrapper1").addClass('disply-none');
            $("#similar_tag_container").addClass('text-center');
        }else if($("#similar_tag_container").height() >= '86'){
            $("#similar_tag_container").css("height", "85px");
        }
      }

      if((document.getElementById("popular_tag_container") != null)  && (document.getElementById("similar_tag_container") == null)){
        $("#parent_tag_container").addClass('pb-5');
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

        // this function is use for slider
        $(".last-slider").slick({
      dots: true,
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      arrows: false,
      responsive: [{
        breakpoint: 1440,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      }
      ]
    });
    init();

    })
    ;

    function storeDetails(sub_cat_id, catalog_id, template_id) {
        localStorage.setItem("sub_cat_id", JSON.stringify(sub_cat_id));
        localStorage.setItem("catalog_id", JSON.stringify(catalog_id));
        localStorage.setItem("template_id", JSON.stringify(template_id));
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
            $(str)[0].preload = "auto";
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

    function playVideoInModel(i) {
        var str = "#v" + i;
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
                    $(video)[0].onloadeddata =  function () {
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


//this code is use to show and himde tags pn popular tag categories and similar tag categories
  const explore_more = document.getElementById('explore_more');
  const similar_explore_more = document.getElementById('similar_explore_more');
  const buttonUp = document.getElementById('up');
  if(explore_more != null){
  explore_more.onclick = function () {
        if ($("#popular_tag_container").css("height") == $("#popular_tag_container")[0].scrollHeight + 'px') {
            $("#popular_tag_container").animate({ "height": "85px" }, 600);
            $("#explore_more").children('svg').css("transform", "rotate(0deg)");
            $("#explore_more").children('svg').css("transition", "0.5s");
            $("#explore_more").children('span').text("Explore More");

        } else {
            $("#popular_tag_container").animate({ "height": $("#popular_tag_container")[0].scrollHeight }, 600);
            $("#explore_more").children('svg').css("transform", "rotate(-180deg)");
            $("#explore_more").children('svg').css("transition", "0.5s");
            $("#explore_more").children('span').text("Explore Less");
        }
    };
  }

  if(similar_explore_more != null){
    similar_explore_more.onclick = function () {

        if ($("#similar_tag_container").css("height") == $("#similar_tag_container")[0].scrollHeight + 'px') {
            $("#similar_tag_container").animate({ "height": "85px" }, 600);
            $("#similar_explore_more").children('svg').css("transform", "rotate(0deg)");
            $("#similar_explore_more").children('svg').css("transition", "0.5s");
            $("#similar_explore_more").children('span').text("Explore More");
        } else  {
            $("#similar_tag_container").animate({ "height": $("#similar_tag_container")[0].scrollHeight }, 600);
            $("#similar_explore_more").children('svg').css("transform", "rotate(-180deg)");
            $("#similar_explore_more").children('svg').css("transition", "0.5s");
            $("#similar_explore_more").children('span').text("Explore Less");
        }
    };
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

  function removeShimmer(id) {
    if($(id).closest('div').hasClass('prograssive_img')){
        $(id).closest('div').removeClass("prograssive_img");
    }
  }
  </script>

</html>
