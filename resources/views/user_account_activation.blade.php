<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8"> <!-- utf-8 works for most cases -->
  <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
  <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
  <title>PhotoADKing</title> <!-- The title tag shows in email notifications, like Android 4.4. -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:300,500" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap');

    @font-face {
      font-family: robotoRegular;
      src: url(https://photoadking.com/images/ml-img/font/roboto_regular.ttf) format("opentype");
    }

    @font-face {
      font-family: arialRoundedMTBold;
      src: url("https://photoadking.com/images/ml-img/font/Arial Rounded MT Bold.TTF") format("opentype");
    }

    @font-face {
      font-family: openSansSemibold;
      src: url(https://photoadking.com/images/ml-img/font/OpenSans-Semibold.ttf) format("opentype");
    }

    html,
    body {
      margin: 0 auto !important;
      padding: 0 !important;
      height: 100% !important;
      width: 100% !important;
    }

    /* What it does: Stops email clients resizing small text. */
    * {
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }

    .header {
      -moz-box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      -webkit-box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      background-color: #0069FF;
    }

    .hd-txt {
      /* font-family: robotoRegular, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      font-size: 20px;
      margin: auto;
      text-decoration: none;
      color: white !important;
    }

    .hd-temp {
      padding: 0 15px;
      border-left: 1px solid white;
      border-right: 1px solid white;
    }

    .warm-text-h1 {
      font-weight: bold;
      color: black !important;
      font-size: 40px;
      /* font-family: arialRoundedMTBold, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
    }

    .warm-text-p {
      color: black !important;
      font-size: 20px;
      /* font-family: openSansSemibold, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      margin-top: 0;
    }

    .get-strt-btn {
      box-shadow: 1px 2px 5px 0px rgba(0, 0, 0, 0.44) !important;
      text-decoration: none !important;
      line-height: 40px;
      cursor: pointer;
      /* font-family: robotoRegular, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      font-size: 20px;
      width: 170px;
      display: block;
      border: none;
      color: white !important;
      background-color: #0069FF;
    }

    .desc-txt-h1 {
      color: black !important;
      font-size: 50px;
      /* font-family: robotoRegular, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      font-weight: 500;
      margin: 0;
    }

    .desc-txt-p1 {
      color: black !important;
      font-size: 15px;
      /* font-family: openSansSemibold, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      padding: 0 32px;
      text-align: justify;
    }

    .line-div {
      width: 20%;
      border: 1px solid black;
      display: block;
    }

    .desc-txt-p2 {
      color: black !important;
      padding: 10px 40px;
      font-size: 14px;
      /* font-family: robotoRegular, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
    }

    .cat-name {
      color: black !important;
      font-size: 20px;
      /* font-family: openSansSemibold, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
      text-align: left !important;
      margin: auto;
      padding-left: 25px;
    }

    .fb-img,
    .inst-img,
    .fly-img,
    .broch-img {
      width: 90%;
    }

    .end-p {
      color: black !important;
      font-size: 25px;
      /* font-family: robotoRegular, 'open sans', 'helvetica neue', 'sans-serif'; */
      font-family: 'Josefin Sans', sans-serif;
    }

    .icn {
      height: 40px;
    }

    @media screen and (max-width: 576px) {
      .hdr-logo {
        max-width: 50%;
      }

      .hd-temp,
      .hd-cret {
        padding: 0 10px !important;
      }

      .warm-text-h1,
      .desc-txt-h1 {
        font-size: 30px;
      }

      .warm-text-p,
      .cat-name {
        font-size: 15px;
      }

      .get-strt-btn {
        width: 120px;
        line-height: 30px;
        font-size: 15px;
      }

      .desc-txt-p1 {
        font-size: 15px;
      }

      .try-this {
        font-size: 23px !important;
      }

      .fb-td,
      .in-td,
      .fly-td {
        max-width: 50%;
        display: inline-block;
      }

      .pl-50 {
        padding-right: 50%;
      }
    }

    @media screen and (max-width: 476px) {
      .end-p {
        font-size: 20px;
      }

      .brch-td {
        max-width: 85%;
        display: inline-block;
      }
    }

    @media screen and (max-width: 413px) {

      /* iPhone 6 and 6+ */
      /* .email-container {
          width: 100% !important;
      } */
      .pl-50 {
        padding-right: 0 !important;
      }

      .hder-td {
        padding-top: 20px !important;
      }

      .hd-td {
        padding: 10px 0px 20px 0px !important;
        text-align: center !important;
      }

      .hd-txt {
        font-size: 15px !important;
      }

      .warm-text-h1 {
        font-size: 22px !important;
      }

      .warm-text-p,
      .cat-name {
        font-size: 15px !important;
        font-weight: bold !important;
      }

      .warm-text-p {
        padding: 0 10px;
      }

      .try-this,
      .end-p {
        font-size: 18px !important;
        margin: 30px 0;
      }

      .fb-td,
      .in-td,
      .fly-td {
        display: contents;
      }

      .get-strt-btn {
        font-size: 13px !important;
      }

      /* .icn{
          height: 30px !important;
      } */
      .ftr-p {
        font-size: 10px !important;
      }
    }
  </style>
</head>

<body>
<center style="width: 100%; background: #e9e9e9; text-align: left;">
  <div style="margin:0 auto;padding:0px" bgcolor="#ffffff">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
           style="max-width:100%;width:680px;">
      <tbody>
      <tr>
        <td align="center" valign="top" bgcolor="#ffffff">
          <table border="0" cellpadding="0" cellspacing="0" align="center"
                 style="max-width:100%;width:680px;" class="email-container">
            <tbody>
            <tr>
              <td align="center" valign="top">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                  <tbody>
                  <tr>
                    <td align="center" valign="top" class="header">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding-top:30px"
                                    class="hder-td">
                                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-hdr-logo.png"
                                       width="60%"
                                       height="auto" border="0"
                                       style="display:inline-block"
                                       alt="PhotoADKing"
                                       title="PhotoADKing"
                                       class="hdr-logo"></td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: center">
                            <table role="presentation" cellspacing="0"
                                   align="center" cellpadding="0"
                                   border="0" width="100%">
                              <tbody>
                              <tr>
                                <td style="padding: 30px 30px; text-align: center;"
                                    class="hd-td">
                                  <table align="center"
                                         style="text-align: center;">
                                    <tbody>
                                    <tr>
                                      <td style="padding-right: 15px;"
                                          class="hd-cret">
                                        <a href="https://photoadking.com/app/#/login"
                                           target="_blank"
                                           class="hd-txt">
                                          Create
                                          Design
                                        </a>
                                      </td>
                                      <td
                                        class="hd-temp">
                                        <a href="https://photoadking.com/templates/"
                                           target="_blank"
                                           class="hd-txt">
                                          Templates
                                        </a>
                                      </td>
                                      <td style="padding-left: 15px"
                                          class="hd-cret">
                                        <a href="https://blog.photoadking.com/"
                                           target="_blank"
                                           class="hd-txt">
                                          Learn
                                        </a>
                                      </td>
                                    </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle">
                                  <p class="warm-text-h1">A
                                    warm
                                    welcome
                                    to<br>PhotoADKing.
                                  </p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%"
                                   align="center">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle">
                                  <p class="warm-text-p">
                                    We can't wait to see the
                                    designs you create
                                  </p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding: 10px 0 23px 0;">
                                  <a href="https://photoadking.com/app/#/login"
                                     target="_blank"
                                     class="get-strt-btn">Get
                                    started</a>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding-bottom: 15px">
                                  <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-img-1.jpg"
                                       alt=""
                                       style="max-width: 95%;">
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle">
                                  <p class="desc-txt-h1">Hi {!! $message_body['user_name'] !!}
                                  </p>
                                  <p class="desc-txt-p1">First
                                    off, Congratulations! We
                                    are
                                    so glad to have you as
                                    part
                                    of our
                                    growing community.</p>
                                  <p class="desc-txt-p1"
                                     style="margin: 20px 0 30px 0">
                                    There
                                    are
                                    thousands of designers,
                                    marketers,
                                    entrepreneurs,
                                    and
                                    managers that use
                                    PhotoADKing every day to
                                    create stunning designs
                                    to communicate with
                                    their
                                    audience. We can’t wait
                                    to
                                    see the designs
                                    you create.</p>
                                  <div class="line-div">
                                  </div>
                                  <p class="desc-txt-p2">We
                                    are
                                    committed for your
                                    success
                                    in designs creation and
                                    marketing. To
                                    start, I
                                    wanted to offer you some
                                    tutorials to help you
                                    get
                                    the most out of
                                    PhotoADKing.
                                  </p>
                                  <a href="https://www.youtube.com/watch?v=ZW9YC5xe98s"
                                     target="_blank"><img
                                      src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-img-1.png"
                                      alt="Unavailable"
                                      style="max-width: 80%; padding-bottom: 20px"></a>
                                  <div class="line-div">
                                  </div>
                                  <p style="font-size: 30px; font-family: 'Josefin Sans', sans-serif;color: black; font-weight: 500"
                                     class="try-this">Try out
                                    these templates</p>
                                </td>
                              </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle">
                                  <p class="cat-name">Facebook
                                    Post</p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle"
                              style="padding: 0 10px;    text-align: center;">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%"
                                   align="center">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    class="fb-td">
                                  <a href="https://photoadking.com/app/#/editor/23bdun0cbeffda/ruhqe555a26661/z29odga126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-pst-ml-1.png"
                                         alt=""
                                         class="fb-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="fb-td">
                                  <a href="https://photoadking.com/app/#/editor/23bdun0cbeffda/ruhqe555a26661/pixnc4a126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-pst-ml-2.png"
                                         alt=""
                                         class="fb-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="fb-td pl-50">
                                  <a href="https://photoadking.com/app/#/editor/23bdun0cbeffda/ruhqe555a26661/my0rqta126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-pst-ml-3.png"
                                         alt=""
                                         class="fb-img">
                                  </a>
                                </td>

                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding-top:35px;">
                                  <p class="cat-name">
                                    Instagram
                                    Post</p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle"
                              style="padding: 0 10px;    text-align: center;">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%"
                                   align="center">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    class="in-td">
                                  <a href="https://photoadking.com/app/#/editor/mmdme10cbeffda/mxsvub55a26661/gyy2mpa126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ist-pst-ml-1.png"
                                         alt=""
                                         class="inst-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="in-td">
                                  <a href="https://photoadking.com/app/#/editor/mmdme10cbeffda/mxsvub55a26661/7tyo0oa126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ist-pst-ml-2.png"
                                         alt=""
                                         class="inst-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="in-td pl-50">
                                  <a href="https://photoadking.com/app/#/editor/mmdme10cbeffda/mxsvub55a26661/pvdxc1a126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ist-pst-ml-3.png"
                                         alt=""
                                         class="inst-img">
                                  </a>
                                </td>

                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding-top:35px;">
                                  <p class="cat-name">Flyer
                                  </p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle"
                              style="padding: 0 10px;    text-align: center;">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%"
                                   align="center">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    class="fly-td">
                                  <a href="https://photoadking.com/app/#/editor/hl903t0cbeffda/vifbt355a26661/1czwtaa126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/flyr-ml-1.png"
                                         alt=""
                                         class="fly-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="fly-td">
                                  <a href="https://photoadking.com/app/#/editor/hl903t0cbeffda/adop2c55a26661/zbwmg3a126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/flyr-ml-2.png"
                                         alt=""
                                         class="fly-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="fly-td pl-50">
                                  <a href="https://photoadking.com/app/#/editor/hl903t0cbeffda/sg2ndq55a26661/75fefwa126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/flyr-ml-3.png"
                                         alt=""
                                         class="fly-img">
                                  </a>
                                </td>

                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top" bgcolor="#f6f6f6">
                      <table border="0" cellspacing="0" cellpadding="0"
                             width="100%">
                        <tbody>
                        <tr>
                          <td align="center" valign="middle">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    style="padding-top:35px;">
                                  <p class="cat-name">Broucher
                                  </p>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle"
                              style="padding: 0 10px 30px 10px; text-align: center">
                            <table border="0" cellspacing="0"
                                   cellpadding="0" width="100%"
                                   align="center">
                              <tbody>
                              <tr>
                                <td align="center"
                                    valign="middle"
                                    class="brch-td">
                                  <a href="https://photoadking.com/app/#/editor/xjzjjj0cbeffda/s5xh1f55a26661/sc7o6ha126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/brchr-ml-1.png"
                                         alt=""
                                         class="broch-img">
                                  </a>
                                </td>
                                <td align="center"
                                    valign="middle"
                                    class="brch-td">
                                  <a href="https://photoadking.com/app/#/editor/xjzjjj0cbeffda/ntlfy455a26661/auq1bna126f192"
                                     target="_blank">
                                    <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/brchr-ml-2.png"
                                         alt=""
                                         class="broch-img">
                                  </a>
                                </td>
                              </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="left" valign="top" bgcolor="#dbdbdb">
                      <table border="0" cellspacing="0" cellpadding="0"
                             align="center">
                        <tbody>
                        <tr>
                          <td align="center" valign="top"
                              style="padding-bottom: 34px">
                            <p class="end-p">Start making better
                              designs<br>for your brand.</p>
                            <a href="https://photoadking.com/app/#/login"
                               target="_blank"
                               class="get-strt-btn">Create
                              Design</a>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td bgcolor="#292828" class="txt-center" align="center"
                        style="padding: 30px 15px">
                      <table role="presentation" cellspacing="0" cellpadding="0"
                             border="0" style="max-width:240px; margin: auto;">
                        <tbody>
                        <tr>
                          <td>
                            <a href="https://www.facebook.com/Photoadking-2194363387447211/"
                               target="_blank">
                              <img class="icn"
                                   src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-icon.png" alt=""/>
                            </a>
                          </td>
                          <td width="10">&nbsp;</td>
                          <td>
                            <a href="https://www.instagram.com/photoadking/"
                               target="_blank">
                              <img class="icn"
                                   src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/i-icon.png" alt=""/>
                            </a>
                          </td>
                          <td width="10">&nbsp;</td>
                          <td>
                            <a href="https://www.pinterest.com/photoadking/"
                               target="_blank">
                              <img class="icn"
                                   src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/p-icon.png" alt=""/>
                            </a>
                          </td>
                          <td width="10">&nbsp;</td>
                          <td>
                            <a href="https://twitter.com/photoadking"
                               target="_blank">
                              <img class="icn"
                                   src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/t-icon.png" alt=""/>
                            </a>
                          </td>
                          <td width="10">&nbsp;</td>
                          <td>
                            <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg"
                               target="_blank">
                              <img class="icn"
                                   src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/y-icon.png" alt=""/>
                            </a>
                          </td>

                        </tr>
                        </tbody>
                      </table>
                      <p
                        style="color: #9e9e9e;font-family: sans-serif; margin-bottom: 0">
                        Stay connected with us</p>
                    </td>
                  </tr>
                  <tr>
                    <td bgcolor="#292828" class="txt-center" align="center">
                      <table role="presentation" cellspacing="0" cellpadding="0"
                             border="0" width="100%"
                             style="max-width:500px; margin: auto;">
                        <tr>
                          <td
                            style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;text-align: center">
                            <p style="margin: 0;">This email was sent by
                            </p>
                            <p style="margin: 0; font-weight:bold;">
                              no-reply@photoadking.com</p>
                          </td>
                        </tr>
                        <tr>
                          <td
                            style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:bold;">
                          </td>
                        </tr>
                        <tr>
                          <td
                            style="padding: 0px 40px 40px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;text-align: center">
                            <p style="margin: 0;">Copyright &copy; 2018-{{ date('Y') }}
                              <b>PhotoADKing</b>, All Rights
                              Reserved.</p>
                          </td>
                        </tr>

                      </table>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            </tbody>
          </table>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</center>
</body>


</html>
