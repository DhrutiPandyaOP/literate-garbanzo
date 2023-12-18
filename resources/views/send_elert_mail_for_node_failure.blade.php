<!-- THIS EMAIL WAS BUILT AND TESTED WITH LITMUS http://litmus.com -->
<!-- IT WAS RELEASED UNDER THE MIT LICENSE https://opensource.org/licenses/MIT -->
<!-- QUESTIONS? TWEET US @LITMUSAPP -->
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8">
  <!-- utf-8 works for most cases -->
  <meta name="viewport" content="width=device-width">
  <!-- Forcing initial-scale shouldn't be necessary -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Use the latest (edge) version of IE rendering engine -->
  <meta name="x-apple-disable-message-reformatting">
  <!-- Disable auto-scale in iOS 10 Mail entirely -->
  <title>Welcome to PhotoADKing</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

  <!-- Web Font / @font-face : BEGIN -->
  <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->

  <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
  <!--[if mso]>
  <style>
    * {
      font-family: Arial, sans-serif !important;
    }
  </style>
  <![endif]-->

  <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
  <!--[if !mso]><!-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:300,500" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <!--<![endif]-->

  <!-- Web Font / @font-face : END -->

  <!-- CSS Reset -->
  <style>
    /* What it does: Remove spaces around the email design added by some email clients. */
    /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
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

    /* What it does: Centers email on Android 4.4 */
    div[style*="margin: 16px 0"] {
      margin: 0 !important;
    }

    /* What it does: Stops Outlook from adding extra spacing to tables. */
    table,
    td {
      mso-table-lspace: 0pt !important;
      mso-table-rspace: 0pt !important;
    }

    /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
    table {
      border-spacing: 0 !important;
      border-collapse: collapse !important;
      table-layout: fixed !important;
      margin: 0 auto !important;
    }

    table table table {
      table-layout: auto;
    }

    /* What it does: Uses a better rendering method when resizing images in IE. */
    img {
      -ms-interpolation-mode: bicubic;
    }

    a {
      text-decoration: none !important;
    }

    /* What it does: A work-around for email clients meddling in triggered links. */
    *[x-apple-data-detectors],
      /* iOS */
    .x-gmail-data-detectors,
      /* Gmail */
    .x-gmail-data-detectors *,
    .aBn {
      border-bottom: 0 !important;
      cursor: default !important;
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }

    /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
    .a6S {
      display: none !important;
      opacity: 0.01 !important;
    }

    /* If the above doesn't work, add a .g-img class to any image in question. */
    img.g-img + div {
      display: none !important;
    }

    /* What it does: Prevents underlining the button text in Windows 10 */
    .button-link {
      text-decoration: none !important;
    }

    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */
    /* Thanks to Eric Lepetit
    @ericlepetitsf)
    for help troubleshooting */
    @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {

      /* iPhone 6 and 6+ */
      .email-container {
        min-width: 375px !important;
      }
    }

    @media only screen and (max-width: 668px) {
      .email-table {
        max-width: 500px !important;
      }
    }

    @media only screen and (max-width: 532px) {
      .email-table {
        max-width: 400px !important;
      }
    }

    @media only screen and (max-width: 450px) {
      .email-table {
        max-width: 315px !important;
      }
    }

    .paragraph-email {
      color: var(--151515, #1F1F1F);
      font-family: Montserrat;
      font-size: 23px;
      font-style: normal;
      font-weight: 700;
      line-height: normal;
      text-align: start;
      margin: 0px;
    }

    .paragraph-email span {
      color: var(--digitalocean, #0069FF);
      font-family: Montserrat;
      font-size: 23px;
      font-style: normal;
      font-weight: 700;
      line-height: normal;
      text-decoration-line: underline;
    }

    .btn-email {
      display: flex;
      width: 295px;
      height: 46px;
      padding: 13.507px 23.638px;
      justify-content: center;
      align-items: center;
      gap: 11.256px;
      flex-shrink: 0;
      border-radius: 55.63px;
      background: var(--digitalocean, #0069FF);
      border: none;
      float: left;
      margin: 2.2rem auto;
      cursor: pointer;
      outline: none;
    }

    .btn-email p {
      color: var(--white, #FFF);
      text-align: center;
      font-family: Montserrat;
      font-size: 18.01px;
      font-style: normal;
      font-weight: 600;
      line-height: normal;
    }

    .image-class {
      text-align: start;
    }

    .image-class img {
      border-radius: 3px;
      border: 3px solid var(--9-cc-5-ff, #9CC5FF);
      background: url(<path-to-image>), lightgray 50% / cover no-repeat;
    }

    .img-fluid {
      width: 100%;
      height: auto;
    }
  </style>

  <!-- Progressive Enhancements -->
  <style>
    /* What it does: Hover styles for buttons */
    .button-td,
    .button-a {
      transition: all 100ms ease-in;
    }

    .button-td:hover,
    .button-a:hover {
      background: #1369d4 !important;
      border-color: #1369d4 !important;
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

    .header {
      -moz-box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      -webkit-box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      box-shadow: 0px 6px 6px -2px rgba(0, 0, 0, 0.5);
      background-color: #0069FF;
    }

    .icn {
      height: 40px;
    }

    /* Media Queries */

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
    }

    @media screen and (max-width: 480px) {

      /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
      .fluid {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        margin-left: auto !important;
        margin-right: auto !important;
      }

      /* What it does: Forces table cells into full-width rows. */
      .stack-column,
      .stack-column-center {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        direction: ltr !important;
      }

      /* And center justify these ones. */
      .stack-column-center {
        text-align: center !important;
      }

      /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
      .center-on-narrow {
        text-align: center !important;
        display: block !important;
        margin-left: auto !important;
        margin-right: auto !important;
        float: none !important;
      }

      table.center-on-narrow {
        display: inline-block !important;
      }

      /* What it does: Adjust typography on small screens to improve readability */
      .email-container p {
        font-size: 14px !important;
        line-height: 22px !important;
      }

      .email-container span {
        font-size: 14px !important;
        line-height: 22px !important;
      }
    }

    @media screen and (max-width: 413px) {
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

      .ftr-p {
        font-size: 10px !important;
      }
    }

  </style>

  <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
  <!--[if gte mso 9]>
  <xml>
    <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
  </xml>
  <![endif]-->

</head>

<body width="100%" bgcolor="#F1F1F1" style="margin: 0; mso-line-height-rule: exactly;">
<center style="width: 100%; background: #F1F1F1; text-align: left;">
  <div style="max-width: 680px; margin: 15px auto; background: #ffffff;" class="email-container">
    <!--[if mso]>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" align="center">
      <tr>
        <td>
    <![endif]-->

    <!-- Email Body : BEGIN -->
    <table role="presentation" cellspacing="0" cellpadding="0" bgcolor="#ffffff" border="0" align="center"
           width="100%" style="max-width: 680px;" class="email-container">

      <!-- HEADER : BEGIN -->
      <tr>
        <td align="center" valign="top" class="header">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tbody>
            <tr>
              <td align="center" valign="middle">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                  <tbody>
                  <tr>
                    <td align="center" valign="middle" style="padding-top:15px" class="hder-td">
                      <img
                        src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-hdr-logo.png"
                        width="55%" height="auto" border="0" style="display:inline-block"
                        alt="PhotoADKing"
                        title="PhotoADKing" class="hdr-logo"></td>
                  </tr>

                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td style="text-align: center">
                <table role="presentation" cellspacing="0" align="center" cellpadding="0" border="0"
                       width="100%">
                  <tbody>
                  <tr>
                    <td style="padding: 10px 25px 20px; text-align: center;" class="hd-td">
                      <table align="center" style="text-align: center;">
                        <tbody>
                        <tr>
                          <td style="padding-right: 15px;" class="hd-cret">
                            <a href="https://photoadking.com/app/#/login" target="_blank"
                               class="hd-txt">
                              Create
                              Design
                            </a>
                          </td>
                          <td class="hd-temp">
                            <a href="https://photoadking.com/templates/" target="_blank"
                               class="hd-txt">
                              Templates
                            </a>
                          </td>
                          <td style="padding-left: 15px" class="hd-cret">
                            <a href="https://blog.photoadking.com/" target="_blank"
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


      <!-- HEADER : END -->

      <!-- HERO : BEGIN -->
      <tr>
        <!-- Bulletproof Background Images c/o https://backgrounds.cm -->
        <td align="center" valign="top" style="text-align: center;">
          <!--[if gte mso 9]>
          <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false"
                  style="width:680px; height:380px; background-position: center center !important;">
            <v:fill type="tile" src="background.png" color="#222222"/>
            <v:textbox inset="0,0,0,0">
          <![endif]-->
          <div>
            <!--[if mso]>
            <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center"
                   width="500">
              <tr>
                <td align="center" valign="middle" width="500">
            <![endif]-->
            <table class="email-table" role="presentation" border="0" cellpadding="0" cellspacing="0"
                   align="left" width="100%"
                   style="max-width:600px; margin: auto;">

              <tr>
                <td height="20" style="font-size:20px; line-height:20px;">&nbsp;</td>
              </tr>

              <tr>
                <td align="left" valign="middle">

                  <table>
                    <tr>
                      <td valign="top" style="text-align: center; padding: 30px 0 10px 0;"
                          bgcolor="#ffffff">
                        <h2
                          style=" margin: 0; font-family: 'Montserrat', sans-serif; font-size: 16px; line-height: 36px; color: #0061d9; font-weight: bold;">
                        </h2>
                        <p>{!! $message_body['message'] !!}</p>
                      </td>

                    </tr>
                  </table>

                </td>
              </tr>
            </table>
            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->
          </div>
          <!--[if gte mso 9]>
          </v:textbox>
          </v:rect>
          <![endif]-->
        </td>
      </tr>
      <!-- HERO : END -->

      <!-- INTRO : BEGIN -->

      <tr>
        <td bgcolor="#dbdbdb">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" style=" margin: auto;">
            <tr>
              <td
                style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px;  text-align: center;">
                <!-- <p style="margin: 0; font-weight:bold; color: #757575 !important;">We are here if you need
                  support</p> -->

                <p
                  style="color: #56595C;
                    font-family: Roboto;
                    font-size: 15px;
                    font-style: normal;
                    font-weight: 400;
                    line-height: 141.188%;
                    text-align: start;
                    padding: 10px 25px 10px 25px;">
                  You’re receiving this email because you have a PhotoADKing account. This email is
                  not a marketing or promotional email. That is why this email does not contain an
                  unsubscribe link. You will receive this email even if you have unsubscribed from
                  PhotoADKing’s marketing emails.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- INTRO : END -->

      <!-- SOCIAL : BEGIN -->
      <!-- <tr>
        <td>
            <table role="presentation" cellspacing="0" bgcolor="#292828" cellpadding="0" border="0"
                   width="100%">
                <tr>
                    <td style="padding: 30px 30px; text-align: center;">
                        <table align="center" style="text-align: center;">
                            <tr>
                                <td>
                                    <a href="https://www.facebook.com/Photoadking-2194363387447211/" target="_blank">
                                        <img src="https://drive.google.com/uc?id=1igqhpc5lCD9WPi--DLMB3FezETPzMtDa"
                                             height="32" width="32" />
                                    </a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <a href="https://www.instagram.com/photoadking/" target="_blank">
                                        <img src="https://drive.google.com/uc?id=1ejbahZV2qV6xPq-xOzzYLfYrQXwfmGNY"
                                             height="32" width="32" />
                                    </a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <a href="https://www.pinterest.com/photoadking/" target="_blank">
                                        <img src="https://drive.google.com/uc?id=1Y7InY9okO2sCpuaU8UMSij_mm_L8Pemk"
                                             height="32" width="32" />
                                    </a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <a href="https://twitter.com/photoadking" target="_blank">
                                        <img src="https://drive.google.com/uc?id=14Mz6zImLy2EMZf6i3XFykDv5cSIjlb9G"
                                             height="32" width="32" />
                                    </a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg"
                                       target="_blank">
                                        <img src="https://drive.google.com/uc?id=1zgc9MRMhy6Bi96h8EUG4Mj7gVePXoHMl"
                                             height="32" width="32" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="font-family: sans-serif; margin-bottom: 0; font-size: 16px; line-height: 20px; color: #9e9e9e; font-weight: bold; text-align: center;">
                            Stay
                            connected with us</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr> -->
      <!-- SOCIAL : END -->

      <!-- FOOTER : BEGIN -->
      <!-- <tr>
        <td bgcolor="#292828">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="max-width:500px; margin: auto;">
                <tr>
                    <td style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;">
                        <p style="margin: 0;">This email was sent by </p>

                        <p style="margin: 0; font-weight:bold;">help.photoadking@gmail.com</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:bold;">
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px 40px 40px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;">
                        <p style="margin: 0;">Copyright &copy; 2018-2019 <b>PhotoADKing</b>, All Rights
                            Reserved.</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr> -->
      <!-- FOOTER : END -->


      <tr>
        <td bgcolor="#292828" class="txt-center" align="center" style="padding: 20px 15px">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                 style="max-width:240px; margin: auto;">
            <tbody>
            <tr>
              <td>
                <a href="https://www.facebook.com/Photoadking-2194363387447211/" target="_blank">
                  <img class="icn"
                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-icon.png"/>
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.instagram.com/photoadking/" target="_blank">
                  <img class="icn"
                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/i-icon.png"/>
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.pinterest.com/photoadking/" target="_blank">
                  <img class="icn"
                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/p-icon.png"/>
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://twitter.com/photoadking" target="_blank">
                  <img class="icn"
                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/t-icon.png"/>
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" target="_blank">
                  <img class="icn"
                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/y-icon.png"/>
                </a>
              </td>

            </tr>
            </tbody>
          </table>
          <p style="color: #9e9e9e;font-family: sans-serif; margin-bottom: 0">
            Stay connected with us</p>
        </td>
      </tr>
      <tr>
        <td bgcolor="#292828" class="txt-center" align="center">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                 style="max-width:500px; margin: auto;">
            <tr>
              <td
                style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a !important; font-weight:normal;text-align: center">
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
                style="padding: 0px 40px 20px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;text-align: center">
                <p style="margin: 0;">Copyright &copy; 2018-{{ date('Y') }}
                  <b>PhotoADKing</b>, All Rights
                  Reserved.</p>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
    <!-- Email Body : END -->

    <!--[if mso]>
    </td>
    </tr>
    </table>
    <![endif]-->
  </div>

</center>

</body>

</html>
