<?php

namespace App\Http\Controllers;

use Exception;
use FontLib\Font;
use Log;
use Response;

class UserVerificationController extends Controller
{
    //

    //verify image
    public function verifyImage($image_array)
    {
        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $extension = isset($fileData['extension']) ? strtolower($fileData['extension']) : 'jpg';
        //    $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyImage : ",['image_size' => $image_size, 'extension' => $extension]);

        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        //    if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
        if (! ($extension == 'png' || $extension == 'jpeg' || $extension == 'jpg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG/JPG file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File Size is greater then 5MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    public function verifyImageForUser($image_array)
    {
        $extension = strtolower($image_array->getClientOriginalExtension());
        $image_size = $image_array->getSize();

        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        //if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
        if (! ($extension == 'png' || $extension == 'jpeg' || $extension == 'jpg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG/JPG file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File Size is greater then 5MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    // Verify Font File
    public function verifyFontFile($file_array)
    {

        $file_type = $file_array->getMimeType();
        $file_size = $file_array->getSize();
        //Log::info("Font file : ", ['type' => $file_type, 'size' => $file_size]);
        $MAXIMUM_FILESIZE = 2 * 1024 * 1024;

        //there is no specific mimetype for otf & ttf so here we used 2 popular type

        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/vnd.ms-opentype'))
        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype'))
        //Galada_Regular.ttf file extension is font/sfnt mime in php 7.4 & application/font-sfnt in php <= 7.3 that's why we add one more condition.
        if (! ($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'font/sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select TTF or OTF file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($file_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then 2MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    // verify audio
    public function verifyAudio($audio_array)
    {

        $audio_type = $audio_array->getMimeType();
        $audio_size = $audio_array->getSize();

        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        /*octet-stream ==>.3gp
        quicktime ==>.mov*/

        if (! ($audio_type == 'audio/mpeg' || $audio_type == 'application/octet-stream')) {
            return $response = Response::json(['code' => 201, 'message' => 'Please select mp3 audio file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($audio_size > $MAXIMUM_FILESIZE) {
            return $response = Response::json(['code' => 201, 'message' => 'File size is greater then 5MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    // verify video file
    public function verifyVideo($video_array)
    {

        $video_type = $video_array->getMimeType();
        $video_size = $video_array->getSize();

        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        /*x-ms-asf ==>.asf or .asx
        ap4 ==>.mp4
        webm ==>.webm (upload mkv file)
        quicktime ==>.mov*/

        if (! ($video_type == 'video/x-ms-asf' || $video_type == 'video/mp4' || $video_type == 'video/webm' || $video_type == 'video/quicktime' || $video_type == 'application/octet-stream')) {
            return $response = Response::json(['code' => 201, 'message' => 'Please select asf or mp4 or webm(mkv) or mov file.', 'cause' => '', 'data' => json_decode('{}')]);

        } elseif ($video_size > $MAXIMUM_FILESIZE) {
            return $response = Response::json(['code' => 201, 'message' => 'File size is greater then 5MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    // Fetch country code from country name
    public function getCountryCode($country_name)
    {
        try {
            $contry_code_array = ['Afghanistan' => 'AF',
                'Aland Islands' => 'AX',
                'Albania' => 'AL',
                'Algeria' => 'DZ',
                'American Samoa' => 'AS',
                'Andorra' => 'AD',
                'Angola' => 'AO',
                'Anguilla' => 'AI',
                'Antarctica' => 'AQ',
                'Antigua and Barbuda' => 'AG',
                'Argentina' => 'AR',
                'Armenia' => 'AM',
                'Aruba' => 'AW',
                'Asia-Pacific' => 'AP',
                'Australia' => 'AU',
                'Austria' => 'AT',
                'Azerbaijan' => 'AZ',
                'Bahamas' => 'BS',
                'Bahrain' => 'BH',
                'Bangladesh' => 'BD',
                'Barbados' => 'BB',
                'Belarus' => 'BY',
                'Belgium' => 'BE',
                'Belize' => 'BZ',
                'Benin' => 'BJ',
                'Bermuda' => 'BM',
                'Bhutan' => 'BT',
                'Bolivia' => 'BO',
                'Bosnia and Herzegovina' => 'BA',
                'Botswana' => 'BW',
                'Bouvet Island' => 'BV',
                'Brazil' => 'BR',
                'British Indian Ocean Territory' => 'IO',
                'Brunei Darussalam' => 'BN',
                'Bulgaria' => 'BG',
                'Burkina Faso' => 'BF',
                'Burundi' => 'BI',
                'Cambodia' => 'KH',
                'Cameroon' => 'CM',
                'Canada' => 'CA',
                'Cape Verde' => 'CV',
                'Cayman Islands' => 'KY',
                'Central African Republic' => 'CF',
                'Chad' => 'TD',
                'Chile' => 'CL',
                'China' => 'CN',
                'Christmas Island' => 'CX',
                'Cocos (Keeling) Islands' => 'CC',
                'Colombia' => 'CO',
                'Comoros' => 'KM',
                'Congo' => 'CG',
                'Congo, the Democratic Republic of the' => 'CD',
                'Cook Islands' => 'CK',
                'Costa Rica' => 'CR',
                "Cote D'Ivoire" => 'CI',
                'Croatia' => 'HR',
                'Cuba' => 'CU',
                'Cyprus' => 'CY',
                'Czech Republic' => 'CZ',
                'Denmark' => 'DK',
                'Djibouti' => 'DJ',
                'Dominica' => 'DM',
                'Dominican Republic' => 'DO',
                'Ecuador' => 'EC',
                'Egypt' => 'EG',
                'El Salvador' => 'SV',
                'Equatorial Guinea' => 'GQ',
                'Eritrea' => 'ER',
                'Estonia' => 'EE',
                'Ethiopia' => 'ET',
                'Europe' => 'EU',
                'Falkland Islands (Malvinas)' => 'FK',
                'Faroe Islands' => 'FO',
                'Fiji' => 'FJ',
                'Finland' => 'FI',
                'France' => 'FR',
                'French Guiana' => 'GF',
                'French Polynesia' => 'PF',
                'French Southern Territories' => 'TF',
                'Gabon' => 'GA',
                'Gambia' => 'GM',
                'Georgia' => 'GE',
                'Germany' => 'DE',
                'Ghana' => 'GH',
                'Gibraltar' => 'GI',
                'Greece' => 'GR',
                'Greenland' => 'GL',
                'Grenada' => 'GD',
                'Guadeloupe' => 'GP',
                'Guam' => 'GU',
                'Guatemala' => 'GT',
                'Guinea' => 'GN',
                'Guinea-Bissau' => 'GW',
                'Guyana' => 'GY',
                'Haiti' => 'HT',
                'Heard Island and Mcdonald Islands' => 'HM',
                'Holy See (Vatican City State)' => 'VA',
                'Honduras' => 'HN',
                'Hong Kong' => 'HK',
                'Hungary' => 'HU',
                'Iceland' => 'IS',
                'India' => 'IN',
                'Indonesia' => 'ID',
                'Iran, Islamic Republic of' => 'IR',
                'Iraq' => 'IQ',
                'Ireland' => 'IE',
                'Israel' => 'IL',
                'Italy' => 'IT',
                'Jamaica' => 'JM',
                'Japan' => 'JP',
                'Jordan' => 'JO',
                'Kazakhstan' => 'KZ',
                'Kenya' => 'KE',
                'Kiribati' => 'KI',
                "Korea, Democratic People's Republic of" => 'KP',
                'Korea, Republic of' => 'KR',
                'Kuwait' => 'KW',
                'Kyrgyzstan' => 'KG',
                "Lao People's Democratic Republic" => 'LA',
                'Latvia' => 'LV',
                'Lebanon' => 'LB',
                'Lesotho' => 'LS',
                'Liberia' => 'LR',
                'Libyan Arab Jamahiriya' => 'LY',
                'Liechtenstein' => 'LI',
                'Lithuania' => 'LT',
                'Luxembourg' => 'LU',
                'Macao' => 'MO',
                'Macedonia, the Former Yugoslav Republic of' => 'MK',
                'Madagascar' => 'MG',
                'Malawi' => 'MW',
                'Malaysia' => 'MY',
                'Maldives' => 'MV',
                'Mali' => 'ML',
                'Malta' => 'MT',
                'Marshall Islands' => 'MH',
                'Martinique' => 'MQ',
                'Mauritania' => 'MR',
                'Mauritius' => 'MU',
                'Mayotte' => 'YT',
                'Mexico' => 'MX',
                'Micronesia, Federated States of' => 'FM',
                'Moldova, Republic of' => 'MD',
                'Monaco' => 'MC',
                'Mongolia' => 'MN',
                'Montenegro' => 'ME',
                'Montserrat' => 'MS',
                'Morocco' => 'MA',
                'Mozambique' => 'MZ',
                'Myanmar' => 'MM',
                'Namibia' => 'NA',
                'Nauru' => 'NR',
                'Nepal' => 'NP',
                'Netherlands' => 'NL',
                'Netherlands Antilles' => 'AN',
                'Neutral Zone' => 'NT',
                'New Caledonia' => 'NC',
                'New Zealand' => 'NZ',
                'Nicaragua' => 'NI',
                'Niger' => 'NE',
                'Nigeria' => 'NG',
                'Niue' => 'NU',
                'Norfolk Island' => 'NF',
                'Northern Mariana Islands' => 'MP',
                'Norway' => 'NO',
                'Oman' => 'OM',
                'Pakistan' => 'PK',
                'Palau' => 'PW',
                'Palestinian Territory, Occupied' => 'PS',
                'Panama' => 'PA',
                'Papua New Guinea' => 'PG',
                'Paraguay' => 'PY',
                'Peru' => 'PE',
                'Philippines' => 'PH',
                'Pitcairn' => 'PN',
                'Poland' => 'PL',
                'Portugal' => 'PT',
                'Private' => '01',
                'Puerto Rico' => 'PR',
                'Qatar' => 'QA',
                'Republic of Serbia' => 'RS',
                'Reunion' => 'RE',
                'Romania' => 'RO',
                'Russian Federation' => 'RU',
                'Rwanda' => 'RW',
                'Saint Helena' => 'SH',
                'Saint Kitts and Nevis' => 'KN',
                'Saint Lucia' => 'LC',
                'Saint Pierre and Miquelon' => 'PM',
                'Saint Vincent and the Grenadines' => 'VC',
                'Samoa' => 'WS',
                'San Marino' => 'SM',
                'Sao Tome and Principe' => 'ST',
                'Saudi Arabia' => 'SA',
                'Senegal' => 'SN',
                'Serbia and Montenegro' => 'CS',
                'Seychelles' => 'SC',
                'Sierra Leone' => 'SL',
                'Singapore' => 'SG',
                'Slovakia' => 'SK',
                'Slovenia' => 'SI',
                'Solomon Islands' => 'SB',
                'Somalia' => 'SO',
                'South Africa' => 'ZA',
                'South Georgia and the South Sandwich Islands' => 'GS',
                'Spain' => 'ES',
                'Sri Lanka' => 'LK',
                'Sudan' => 'SD',
                'Suriname' => 'SR',
                'Svalbard and Jan Mayen' => 'SJ',
                'Swaziland' => 'SZ',
                'Sweden' => 'SE',
                'Switzerland' => 'CH',
                'Syrian Arab Republic' => 'SY',
                'Taiwan, Province of China' => 'TW',
                'Tajikistan' => 'TJ',
                'Tanzania, United Republic of' => 'TZ',
                'Thailand' => 'TH',
                'Timor-Leste' => 'TL',
                'Togo' => 'TG',
                'Tokelau' => 'TK',
                'Tonga' => 'TO',
                'Trinidad and Tobago' => 'TT',
                'Tunisia' => 'TN',
                'Turkey' => 'TR',
                'Turkmenistan' => 'TM',
                'Turks and Caicos Islands' => 'TC',
                'Tuvalu' => 'TV',
                'Uganda' => 'UG',
                'Ukraine' => 'UA',
                'United Arab Emirates' => 'AE',
                'United Kingdom' => 'GB',
                'United States' => 'US',
                'United States Minor Outlying Islands' => 'UM',
                'Uruguay' => 'UY',
                'Uzbekistan' => 'UZ',
                'Vanuatu' => 'VU',
                'Venezuela' => 'VE',
                'Viet Nam' => 'VN',
                'Virgin Islands, British' => 'VG',
                'Virgin Islands, U.s.' => 'VI',
                'Wallis and Futuna' => 'WF',
                'Western Sahara' => 'EH',
                'Yemen' => 'YE',
                'Yugoslavia' => 'YU',
                'Zambia' => 'ZM',
                'Zimbabwe' => 'ZW'];

            if (! isset($contry_code_array[$country_name])) {
                return $country_name;
            } else {
                return $contry_code_array[$country_name];
            }
        } catch (Exception $e) {
            (new ImageController())->logs('getCountryCode', $e);
            //      Log::error("getCountryCode : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return '';
        }
    }
}
