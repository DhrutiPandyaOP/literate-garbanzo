<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
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
      src: url(https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/font/roboto_regular.ttf) format("opentype");
    }

    @font-face {
      font-family: arialRoundedMTBold;
      src: url("https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/font/Arial_Rounded_MT_Bold.TTF") format("opentype");
    }

    @font-face {
      font-family: openSansSemibold;
      src: url(https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/font/OpenSans-Semibold.ttf) format("opentype");
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

    .hdr-logo {
      width: 35%;
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
        width: 50%;
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
    <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="max-width:100%;width:100%;">
      <tbody>
        <tr>
          <td align="center" valign="top" bgcolor="#ffffff">
            <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:100%;width:100%;" class="email-container">
              <tbody>
                <tr>
                  <td align="center" valign="top">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tbody>
                      <tr>
                        <td align="center" valign="top" class="header">
                          <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tbody>
                            <tr>
                              <td align="center" valign="middle">
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                  <tbody>
                                  <tr>
                                    <td align="center" valign="middle" style="padding-top:30px" class="hder-td">
                                      <img src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/ml-hdr-logo.png"
                                           height="auto" border="0"
                                           style="display:inline-block"
                                           alt="PhotoADKing"
                                           title="PhotoADKing"
                                           class="hdr-logo">
                                    </td>
                                  </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td style="text-align: center">
                                <table role="presentation" cellspacing="0" align="center" cellpadding="0" border="0" width="100%">
                                  <tbody>
                                  <tr>
                                    <td style="padding: 30px 30px; text-align: center;" class="hd-td">
                                      <table align="center" style="text-align: center;">
                                        <tbody>
                                        <tr>
                                          <td style="padding-right: 15px;" class="hd-cret">
                                            <a href="https://photoadking.com/app/#/login" target="_blank" class="hd-txt">Create Design</a>
                                          </td>
                                          <td class="hd-temp">
                                            <a href="https://photoadking.com/templates/" target="_blank" class="hd-txt">Templates</a>
                                          </td>
                                          <td style="padding-left: 15px" class="hd-cret">
                                            <a href="https://blog.photoadking.com/" target="_blank" class="hd-txt">Learn</a>
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
                      <div style="padding:10px;">
                        <h1 style="text-align: center;text-transform: uppercase; margin: 0; font-family: 'Montserrat', sans-serif; font-size: 24px; line-height: 36px; color: #0069FF; font-weight: bold;">Hi, Admin</h1>
                        <h2 style="text-align: center;">Weekly template search tag report </h2>
                        <h3>Application name : {{$data['app_name']}} </h3>
                        <p>This is search tag report of last week from date <span style="font-weight: bold;">{{$data['start_date']}}</span> to date <span style="font-weight: bold;">{{$data['end_date']}}</span>.</p>
                        <table border=1 style="display: table;border-collapse: collapse;border-color: black;width: 100%;color: #212529;">
                          <thead style="height: 40px;">
                            <tr style="padding:.50rem;vertical-align: middle;border-bottom-width: 2px;border-top-width: 2px;">
                              <th>No</th>
                              <th>Search query</th>
                              <th style="width: 80px;">Search count</th>
                              <th style="width: 80px;">Content count</th>
                              <th>Sub category name</th>
                              <th style="width: 100px;">Content type</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($data['tags']))
                              @foreach($data['tags'] as $key => $row)
                                @if($key%2 == 0)
                                <tr style="border: 1px solid black; background-color: #d3d3d3;">
                                  <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{  $key + 1 }}</td>
                                  <td
                                    style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->tag) ? $row->tag :'' }}</td>
                                  <td
                                    style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->search_count) ? $row->search_count : '' }}</td>
                                  <td
                                    style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->content_count) ? $row->content_count :'' }}</td>
                                  <td
                                    style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->sub_category_name) && $row->sub_category_name !='' ? $row->sub_category_name :'-' }}</td>
                                  <td
                                    style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->content_type) ? $row->content_type :'' }}</td>
                                  @if(isset($row->is_success) && $row->is_success == 1)
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: center;color: green !important;">
                                      Success
                                    </td>
                                  @else
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: center;color: #dc3545!important;">
                                      Fail
                                    </td>
                                  @endif
                                </tr>
                                @else
                                  <tr style="border: 1px solid black; background-color: #FFFFFf;">
                                    <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{  $key + 1 }}</td>
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->tag) ? $row->tag :'' }}</td>
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->search_count) ? $row->search_count : '' }}</td>
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->content_count) ? $row->content_count :'' }}</td>
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->sub_category_name) && $row->sub_category_name !='' ? $row->sub_category_name :'-' }}</td>
                                    <td
                                      style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->content_type) ? $row->content_type :'' }}</td>
                                    @if(isset($row->is_success) && $row->is_success == 1)
                                      <td
                                        style="padding:.50rem;vertical-align: middle;text-align: center;color: green !important;">
                                        Success
                                      </td>
                                    @else
                                      <td
                                        style="padding:.50rem;vertical-align: middle;text-align: center;color: #dc3545!important;">
                                        Fail
                                      </td>
                                    @endif
                                  </tr>
                                @endif
                              @endforeach
                            @else
                              <tr style="border: 1px solid black;">
                                <td colspan="7" style="padding:.50rem;vertical-align: middle;text-align: center;">No search
                                  tag found for last week.
                                </td>
                              </tr>
                            @endif
                          </tbody>
                        </table>
                      </div>

                      <tr>
                        <td bgcolor="#292828" class="txt-center" align="center" style="padding: 30px 15px">
                          <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="max-width:240px; margin: auto;">
                            <tbody>
                            <tr>
                              <td>
                                <a href="https://www.facebook.com/Photoadking-2194363387447211/" target="_blank">
                                  <img class="icn"
                                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/f-icon.png" alt="" />
                                </a>
                              </td>
                              <td width="10">&nbsp;</td>
                              <td>
                                <a href="https://www.instagram.com/photoadking/" target="_blank">
                                  <img class="icn"
                                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/i-icon.png" alt="" />
                                </a>
                              </td>
                              <td width="10">&nbsp;</td>
                              <td>
                                <a href="https://www.pinterest.com/photoadking/" target="_blank">
                                  <img class="icn"
                                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/p-icon.png" alt="" />
                                </a>
                              </td>
                              <td width="10">&nbsp;</td>
                              <td>
                                <a href="https://twitter.com/photoadking" target="_blank">
                                  <img class="icn"
                                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/t-icon.png" alt="" />
                                </a>
                              </td>
                              <td width="10">&nbsp;</td>
                              <td>
                                <a href="https://www.youtube.com/channel/UCySoQCLtZI23JVqnsCyPaOg" target="_blank">
                                  <img class="icn"
                                       src="https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email/y-icon.png" alt="" />
                                </a>
                              </td>
                            </tr>
                            </tbody>
                          </table>
                          <p style="color: #9e9e9e;font-family: sans-serif; margin-bottom: 0">Stay connected with us</p>
                        </td>
                      </tr>
                      <tr>
                        <td bgcolor="#292828" class="txt-center" align="center">
                          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width:500px; margin: auto;">
                            <tr>
                              <td style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;text-align: center">
                                <p style="margin: 0;">This email was sent by
                                </p>
                                <p style="margin: 0; font-weight:bold;"> no-reply@photoadking.com</p>
                              </td>
                            </tr>
                            <tr>
                              <td style="padding: 0px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:bold;">
                              </td>
                            </tr>
                            <tr>
                              <td style="padding: 0px 40px 40px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: #8a8a8a; font-weight:normal;text-align: center">
                                <p style="margin: 0;">Copyright &copy; 2018-{{ date('Y') }}
                                  <b>PhotoADKing</b>, All Rights Reserved.</p>
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
