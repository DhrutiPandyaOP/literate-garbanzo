<!-- THIS EMAIL WAS BUILT AND TESTED WITH LITMUS http://litmus.com -->
<!-- IT WAS RELEASED UNDER THE MIT LICENSE https://opensource.org/licenses/MIT -->
<!-- QUESTIONS? TWEET US @LITMUSAPP -->
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8"> <!-- utf-8 works for most cases -->
  <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
  <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
  <title>PhotoADKing</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

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
    img.g-img+div {
      display: none !important;
    }

    /* What it does: Prevents underlining the button text in Windows 10 */
    .button-link {
      text-decoration: none !important;
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

    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */
    /* Thanks to Eric Lepetit @ericlepetitsf) for help troubleshooting */
    @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {

      /* iPhone 6 and 6+ */
      .email-container {
        min-width: 375px !important;
      }
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
      background: #00b0e1 !important;
      border-color: #00b0e1 !important;
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
      .user_name{
        font-size: 20px !important;
        padding: 0 10px;
      }
    }
    @media screen and (max-width: 413px) {
      .td-pd{
        padding:0px 25px !important;
      }
    }
    .subscription td {
      border: 1px solid #eaeaed;
    }
  </style>
</head>

<body width="100%" bgcolor="#F1F1F1" style="margin: 0; mso-line-height-rule: exactly;">
<center style="width: 100%; background: #F1F1F1; text-align: left;">
  <div style=" max-width: 680px; margin: auto; background: #ffffff;" class="email-container">
    <!--[if mso]>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" align="center">
      <tr>
        <td>
    <![endif]-->
    <table role="presentation" cellspacing="0" cellpadding="0" bgcolor="#e4e4e4" border="0" align="center"
           width="100%" style="max-width: 680px;" class="email-container">
      <tr>
        <td align="center" valign="top" class="header">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tbody>
            <tr>
              <td align="center" valign="middle">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                  <tbody>
                  <tr>
                    <td align="center" valign="middle" style="padding-top:15px"
                        class="hder-td">
                      <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-hdr-logo.png"
                           width="55%" height="auto" border="0"
                           style="display:inline-block" alt="PhotoADKing"
                           title="PhotoADKing" class="hdr-logo"></td>
                  </tr>

                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td style="text-align: center">
                <table role="presentation" cellspacing="0" align="center" cellpadding="0"
                       border="0" width="100%">
                  <tbody>
                  <tr>
                    <td style="padding: 10px 25px 20px; text-align: center;" class="hd-td">
                      <table align="center" style="text-align: center;">
                        <tbody>
                        <tr>
                          <td style="padding-right: 15px;" class="hd-cret">
                            <a href="https://photoadking.com/app/#/login"
                               target="_blank" class="hd-txt">
                              Create
                              Design
                            </a>
                          </td>
                          <td class="hd-temp">
                            <a href="https://photoadking.com/templates/"
                               target="_blank" class="hd-txt">
                              Templates
                            </a>
                          </td>
                          <td style="padding-left: 15px" class="hd-cret">
                            <a href="https://blog.photoadking.com/"
                               target="_blank" class="hd-txt">
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
        <td align="center" valign="top" style="text-align: center;" bgcolor="#ffffff">
          <!--[if mso]>
          <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="500">
            <tr>
              <td align="center" valign="middle" width="500">
          <![endif]-->
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" width="100%"
                 style="max-width:600px; margin: auto;">
            <tr>
              <td height="40" style="font-size:20px; line-height:20px;">&nbsp;</td>
            </tr>

            <tr>
              <td align="center" valign="middle">
                <table>
                  <tr>
                    <td valign="top" style="text-align: center; padding: 30px 0 10px 0;"
                        bgcolor="#ffffff">
                      <h1 class="user_name" style="text-transform: uppercase; margin: 0; font-family: 'Montserrat', sans-serif; font-size: 24px; line-height: 36px; color: #0069FF; font-weight: bold;word-break: break-word">Hi Admin</h1>
                    </td>
                  </tr>
                  <tr>
                    <td class="td-pd" valign="top" bgcolor="#ffffff" style="text-align: center; padding: 20px 40px 15px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #757575;word-break: break-word">
                      <p>{!! $message_body['message'] !!}</p>
                    </td>
                  </tr>
                  <tr>
                    <td valign="top" bgcolor="#ffffff" style="text-align: center;width:800px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #757575;">
                      <table align="center" border="1" cellpadding="0" cellspacing="0" width="100%"
                             style="background:#f7f9fa;padding:16px;border-radius:2px;overflow:hidden;">
                        <tbody>
                        <tr>
                          <td align="left" valign="middle" style="border-collapse:collapse;padding:1px;border-bottom:1px solid #eaeaed;font-size:12px">
                            <table class="subscription" align="center" border="0" cellpadding="0"
                                   cellspacing="0" width="100%">
                              <tbody>
                              <tr>
                                <td width="35%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                    No
                                </td>
                                <td width="35%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                    User ID
                                </td>
                                <td width="100%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                  Name
                                </td>
                                <td width="100%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                  Email ID
                                </td>
                                <td width="100%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                    Txn .ID
                                </td>
                                <td width="100%" align="center" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif;padding:8px;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                    Purchase Date
                                </td>
                              </tr>
                              @if(!empty($message_body['subscription_list']))
                                @foreach($message_body['subscription_list'] as $key => $row)
                              <tr>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                  {!!  $key + 1 !!}
                                </td>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                   {!! $row['user_id'] !!}
                                </td>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                  {!! $row['user_name'] !!}
                                </td>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                  {!! $row['email_id'] !!}
                                </td>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                  {!! $row['txn_id'] !!}
                                </td>
                                <td align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;word-break: break-all;padding: 5px;">
                                  {!! $row['purchase_date'] !!}
                                </td>
                              </tr>
                                @endforeach
                              @else
                                <tr align="center" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif;border-bottom:1px solid #eaeaed; font-size: 12px;">
                                  <td colspan="6" style="padding:.50rem;vertical-align: middle;text-align: center;">No subscription for active.</td>
                                </tr>
                              @endif

                              </tbody>
                            </table>
                          </td>
                        </tr>
                   {{--     <tr>
                          <td align="left" valign="middle" style="border-collapse:collapse;padding:8px;border-bottom:1px solid #eaeaed;font-size:12px">
                            <table align="center" border="0" cellpadding="0"
                                   cellspacing="0" width="100%">
                              <tbody>
                              <tr>
                                <td width="35%" align="left" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif; font-size: 12px;">
                                  Txn. ID
                                </td>
                                <td width="16" align="left" valign="top"
                                    style="border-collapse:collapse;font-weight:normal;font-family: sans-serif; font-size: 12px;">:</td>
                                <td align="left" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif; font-size: 12px;">
                                  {!! $message_body['txn_id'] !!}
                                </td>
                              </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" style="border-collapse:collapse;padding:8px;border-bottom:1px solid #eaeaed;font-size:12px">
                            <table align="center" border="0" cellpadding="0"
                                   cellspacing="0" width="100%">
                              <tbody>
                              <tr>
                                <td width="35%" align="left" valign="top"
                                    style="border-collapse:collapse;text-transform:capitalize;font-family: sans-serif; font-size: 12px;">
                                  Purchase Date
                                </td>
                                <td width="16" align="left" valign="top"
                                    style="border-collapse:collapse;font-weight:normal;font-family: sans-serif; font-size: 12px;">:</td>
                                <td align="left" valign="top" style="border-collapse:collapse;font-weight:normal;font-family: sans-serif; font-size: 12px;">
                                  {!! $message_body['purchase_date']
                                  !!}
                                </td>
                              </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>--}}
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td bgcolor="#ffffff" style="font-family: 'Montserrat', sans-serif; text-align: left; padding: 40px 15px 30px 40px; font-size: 13px; color: gray;">
                      <p style="margin:0;">Thanks,</p>
                      <p style="margin:0;">The PhotoADKing Team</p>
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
        </td>
      </tr>
      <tr>
        <td style="padding: 40px 80px 40px 80px; font-family: sans-serif; font-size: 15px; line-height: 20px;  text-align: center;">
          <p style="margin: 0; font-weight:bold; color: #757575;">We are here if you need
            support</p>
          <p style="font-size: 12px !important; color:#909090; font-weight: 600; font-family: sans-serif;margin-bottom: 0">Email:
            help@photoadking.com</p>
        </td>
      </tr>
      <tr>
        <td bgcolor="#292828" class="txt-center" align="center" style="padding: 20px 15px">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                 style="max-width:240px; margin: auto;">
            <tbody>
            <tr>
              <td>
                <a href="https://www.facebook.com/Photoadking-2194363387447211/"
                   target="_blank">
                  <img class="icn" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-icon.png" alt="" />
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.instagram.com/photoadking/" target="_blank">
                  <img class="icn" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/i-icon.png" alt="" />
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.pinterest.com/photoadking/" target="_blank">
                  <img class="icn" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/p-icon.png" alt="" />
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://twitter.com/photoadking" target="_blank">
                  <img class="icn" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/t-icon.png" alt="" />
                </a>
              </td>
              <td width="10">&nbsp;</td>
              <td>
                <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg"
                   target="_blank">
                  <img class="icn" src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/y-icon.png" alt="" />
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
    <!--[if mso]>
    </td>
    </tr>
    </table>
    <![endif]-->
  </div>
</center>
</body>

</html>
