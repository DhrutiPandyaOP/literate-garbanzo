<?php

namespace App\Http\Controllers;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Exception;
use FFMpeg;
use File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;
use PHP_Typography\Settings;
use Response;

class ImageController extends Controller
{
    // get base url
    public function getBaseUrl()
    {

        // get base url in local/live server
        return Config::get('constant.ACTIVATION_LINK_PATH');
    }

    //verify image
    public function verifyImage($image_array, $category_id, $is_featured, $is_catalog)
    {
        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $extension = strtolower($fileData['extension']);
        //$image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyImage : ",['image_size' => $image_size, 'extension' => $extension]);

        /*
         * check size into kb
         * here 100 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        //if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
        if (! ($extension == 'png' || $extension == 'jpeg' || $extension == 'jpg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG/JPG file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //verify svg/png/jpg image
    public function verifyStickerImage($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //Log::info("verifyStickerImage : ",['image_size' => $image_size, 'image_type' => $image_type]);

        /*
         * check size into kb
         * here 100 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 1 * 1024 * 1024;

        if (! ($image_type == 'image/png' || $image_type == 'image/jpeg' || $image_type == 'image/svg+xml' || $image_type == 'image/svg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG or svg file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //verify sample image of cards
    public function verifySampleImage($image_array, $category_id, $is_featured, $is_catalog)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        /*
         * check size into kb
         * here 200 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 200 * 1024;

        if (! ($image_type == 'image/png' || $image_type == 'image/jpeg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG file', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //verify images array
    public function verifyImagesArray($images_array, $image_type, $category_id, $is_featured, $is_catalog)
    {

        /* Explanation of image_type
         * 1 = resource images
         * 2 = sticker images (It allows svg/png/jpg)
         * 3 = normal images (It allows only jpg/png)
         * */

        $files_array = [];
        if ($image_type == 1) {
            foreach ($images_array as $key) {
                if (($response = $this->verifySampleImage($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = ['file_name' => $file_name, 'error_message' => $message];
                }
            }
        } elseif ($image_type == 2) {
            foreach ($images_array as $key) {
                if (($response = $this->verifyStickerImage($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = ['file_name' => $file_name, 'error_message' => $message];
                }
            }
        } else {
            foreach ($images_array as $key) {
                if (($response = $this->verifyImage($key, $category_id, $is_featured, $is_catalog)) != '') {
                    $file_name = $key->getClientOriginalName();
                    $data = (json_decode(json_encode($response), true));
                    $message = $data['original']['message'];
                    $files_array[] = ['file_name' => $file_name, 'error_message' => $message];
                }
            }
        }

        if (count($files_array) > 0) {
            $array = ['error_list' => $files_array];
            $result = json_decode(json_encode($array), true);

            return $response = Response::json(['code' => 436, 'message' => 'File is not verified by size or format. Please check error list.', 'cause' => '', 'data' => $result]);
        } else {
            return $response = '';
        }
    }

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

    // verify audio
    public function verifyAudio($audio_array, $category_id, $is_featured, $is_catalog)
    {

        $audio_type = $audio_array->getMimeType();
        $audio_size = $audio_array->getSize();

        //    $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //    Log::info('verifyImage : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 1 * 1024 * 1024;
        //    Log::info('$MAXIMUM_FILESIZE : ', ['MAXIMUM_FILESIZE' => $MAXIMUM_FILESIZE]);

        /*octet-stream ==>.3gp
        quicktime ==>.mov*/

        if (! ($audio_type == 'audio/mpeg' || $audio_type == 'application/octet-stream')) {
            return $response = Response::json(['code' => 201, 'message' => 'Please select 3gp or mp3 audio file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($audio_size > $MAXIMUM_FILESIZE) {
            return $response = Response::json(['code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //verifyStlFile
    public function verifyStlFile($file_array)
    {

        $file_type = $file_array->getMimeType();
        $file_size = $file_array->getSize();
        //Log::info("File Info : ", ['file_size' => $file_size, 'file_type' => $file_type]);

        /*
         * check size into kb
         * here 10 is mb & 1024 is bytes
         * 1mb = 1024 * 1024 bytes
         * */

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        if (! ($file_type == 'application/octet-stream')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select stl file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($file_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then 10MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    // Verify Font File
    public function verifyFontFile($file_array, $category_id, $is_featured, $is_catalog)
    {

        $file_type = $file_array->getMimeType();
        $file_size = $file_array->getSize();
        //Log::info("verifyFontFile : ", ['type' => $file_type, 'size' => $file_size]);

        /*
         * check size into kb
         * here 200 is kb & 1024 is bytes
         * 1kb = 1024 bytes
         * */

        $validations = $this->getValidationFromCache($category_id, $is_featured, $is_catalog);
        //Log::info('verifyFontFile : ', ['validations' => $validations]);

        $MAXIMUM_FILESIZE = $validations * 1024;
        //$MAXIMUM_FILESIZE = 2 * 1024 * 1024;

        /*Here special characters are restricted & only allow underscore & alphabetic values*/
        $fileData = pathinfo(basename($file_array->getClientOriginalName()));
        $file_name = str_replace(' ', '', strtolower($fileData['filename']));
        $string_array = str_split($file_name);
        //    foreach ($string_array as $key) {
        //      $is_valid = preg_match('/[[:alpha:]_]+/', $key);
        //      if ($is_valid == 0) {
        //        return $response = Response::json(array('code' => 201, 'message' => 'Special characters (except underscore) & numeric value are not allowed into the file name.', 'cause' => '', 'data' => json_decode("{}")));
        //      }
        //    }

        //there is no specific mimetype for otf & ttf so here we used 2 popular type

        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/vnd.ms-opentype'))
        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype'))
        //if (!($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype'))
        //Galada_Regular.ttf file extension is font/sfnt mime in php 7.4 & application/font-sfnt in php <= 7.3 that's why we add one more condition.
        if (! ($file_type == 'application/x-font-ttf' || $file_type == 'application/font-sfnt' || $file_type == 'font/sfnt' || $file_type == 'application/vnd.ms-opentype' || $file_type == 'application/x-font-opentype')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select TTF or OTF file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($file_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then '.$validations.'KB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    public function validateAllFilesToCreateDesign()
    {
        try {

            $all_files = Input::file();
            foreach ($all_files as $i => $files) {
                if (is_array($files)) {
                    foreach ($files as $j => $file) {
                        if (($response = (new UserVerificationController())->verifyImage($file)) != '') {
                            Log::error('File did not verified successfully.', ['key_name' => $i, 'key_index' => $j]);

                            return $response;
                        }
                    }
                } else {
                    if (($response = (new UserVerificationController())->verifyImage($files)) != '') {
                        Log::error('File did not verified successfully.', ['key_name' => $i]);

                        return $response;
                    }
                }
            }
            $response = '';

        } catch (Exception $e) {
            $response = Response::json(['code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
            Log::error('validateAllFilesToCreateDesign : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

        return $response;
    }

    //generate image new name
    public function generateNewFileName($image_type, $image_array)
    {

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.strtolower($fileData['extension']);
        $path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$new_file_name;
        if (File::exists($path)) {
            $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.strtolower($fileData['extension']);
        }

        return $new_file_name;
    }

    //generate user uploaded image new name
    public function generateNewFileNameForUser($image_type, $image_array)
    {
        try {
            $ext = pathinfo($image_array->getClientOriginalName(), PATHINFO_EXTENSION);
            $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.strtolower($ext);
            //      return $new_file_name;
            return ['code' => 200, 'msg' => $new_file_name];
        } catch (Exception $e) {
            (new ImageController())->logs('generateNewFileNameForUser', $e);
            //      Log::error("generateNewFileNameForUser : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            Log::info('generateNewFileNameForUser Catch: ', ['Filename' => $image_array->getClientOriginalName()]);

            return ['code' => 201, 'msg' => $e->getMessage()];
        }
    }

    //generateNewFileNameForPNG
    public function generateNewFileNameForPNG($image_type)
    {
        $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.'png';
        $path = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY').$new_file_name;
        if (File::exists($path)) {
            $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.'png';
        }

        return $new_file_name;
    }

    // Save original Image
    public function saveOriginalImage($img)
    {
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            Input::file('file')->move($original_path, $img);
            $path = $original_path.$img;
            //Log::info('save original image : ',['path' => $path]);
            $this->saveImageDetails($path, 'original');
        } catch (Exception $e) {
            (new ImageController())->logs('saveOriginalImage', $e);
            //      Log::error("saveOriginalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Webp Original Image
    public function saveWebpOriginalImage($img)
    {

        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        $path = $original_path.$img;

        //convert image into .webp format
        $file_data = pathinfo(basename($path));
        $webp_name = $file_data['filename'];

        /*
             *  -q Set image quality
             *  -o Output file name
         */

        $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH').$webp_name.'.webp';
        $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH').$img;
        $quality = Config::get('constant.QUALITY');
        $libwebp = Config::get('constant.PATH_OF_CWEBP');

        $cmd = "$libwebp -q $quality $org_path -o $webp_path";

        if (Config::get('constant.APP_ENV') != 'local') {
            $result = (! shell_exec($cmd));
        } else {
            $result = (! exec($cmd));
        }

        return $webp_name.'.webp';
    }

    // Save User Uploaded Image
    public function saveUserUploadedImage($file_array, $file_name)
    {
        try {

            $original_path = '../..'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY');
            //      Input::file('file')->move($original_path, $file_name);
            $file_array->move($original_path, $file_name);
            $source_file_path = $original_path.$file_name;
            $file_data = pathinfo(basename($source_file_path));
            $file_name_without_ext = $file_data['filename'];

            /*-----------------| generate webp_original image |-----------------*/
            /*
             *  -q Set image quality
             *  -o Output file name
             */

            $destination_file_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_ORIGINAL_IMG_PATH').$file_name_without_ext.'.webp';
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            $cmd = "$libwebp -q $quality $source_file_path -o $destination_file_path";

            if (Config::get('constant.APP_ENV') != 'local') {
                $result = (! shell_exec($cmd));
            } else {
                $result = (! exec($cmd));
            }

            /*-----------------| generate compressed image |-----------------*/
            $compressed_path = '../..'.Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY').$file_name;
            $img = Image::make($source_file_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($source_file_path);
            $compress_img_size = filesize($compressed_path);

            if ($compress_img_size >= $original_img_size) {
                //save original image in compressed directory
                File::delete($compressed_path);
                File::copy($source_file_path, $compressed_path);
            }

            /*-----------------| generate thumbnail image |-----------------*/
            $array = $this->getThumbnailWidthHeightFromS3($source_file_path);
            $width = $array['width'];
            $height = $array['height'];
            $thumbnail_path = '../..'.Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY').$file_name;
            $img = Image::make($source_file_path)->resize($width, $height);
            $img->save($thumbnail_path);

            /*-----------------| generate webp_thumbnail image |-----------------*/
            $image_size = getimagesize($source_file_path);
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;

            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_thumbnail_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_THUMBNAIL_IMG_PATH').$file_name_without_ext.'.webp';
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $source_file_path -resize $width $height -o $webp_thumbnail_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }
                $file_info = ['height' => $height, 'width' => $width, 'file_name' => $file_name_without_ext.'.webp'];
            } else {

                $cmd = "$libwebp -q $quality $source_file_path -resize $width_orig $height_orig -o $webp_thumbnail_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }
                $file_info = ['height' => $height_orig, 'width' => $width_orig, 'file_name' => $file_name_without_ext.'.webp'];
            }

            //use for Image Details
            $this->saveImageDetails($source_file_path, 'user_uploaded_original');
            $this->saveImageDetails($compressed_path, 'user_uploaded_compressed');
            $this->saveImageDetails($thumbnail_path, 'user_uploaded_thumbnail');

            return ['code' => 200, 'msg' => $file_info];
            //      return $file_info;

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedImage', $e);
            //      Log::error("saveUserUploadedImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return ['code' => 201, 'msg' => $e->getMessage()];
        }
    }

    // Save User Uploaded Fonts
    public function saveUserUploadedFont($file_name)
    {

        $source_dir = Config::get('constant.EXTRA_IMAGES_DIRECTORY');
        $destination_dir = Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY');
        $source_file_path = '../..'.$source_dir.$file_name;
        $destination_file_path = '../..'.$destination_dir.$file_name;

        //move file from extra to user_upload directory
        rename($source_file_path, $destination_file_path);

        $font_name = (new VerificationController())->getFontName($destination_dir, $file_name);

        return $font_name;

    }

    // Save Fonts Uploaded by Admin
    public function saveFontFile($file_name, $is_replace)
    {
        try {

            $source_dir = Config::get('constant.EXTRA_IMAGES_DIRECTORY');
            $destination_dir = Config::get('constant.FONT_FILE_DIRECTORY');

            if ($is_replace == 0) {

                $source_file_path = '../..'.$source_dir.$file_name;
                $destination_file_path = '../..'.$destination_dir.$file_name;

                //move file from temp to fonts directory
                rename($source_file_path, $destination_file_path);
            } else {

                $destination_path = '../..'.$destination_dir;
                Input::file('file')->move($destination_path, $file_name);

            }

            $font_name = (new VerificationController())->getFontName($destination_dir, $file_name);

            return $font_name;

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontFile', $e);
            //      Log::error("saveFontFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    public function saveFontPreviewImageFile($file_name)
    {
        try {
            $destination_path = '../..'.config('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY');
            Input::file('font_preview_image')->move($destination_path, $file_name);

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontPreviewImageFile', $e);
        }
    }

    // Save Report Attachment
    public function saveReportAttachment($img)
    {
        $original_path = '../..'.Config::get('constant.REPORT_ATTACHMENT_IMAGES_DIRECTORY');
        Input::file('file')->move($original_path, $img);
        $path = $original_path.$img;
        $this->saveImageDetails($path, 'report_attachment');
    }

    // Save My Design
    public function saveMyDesign($img)
    {
        $original_path = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY');
        Input::file('file')->move($original_path, $img);
        $path = $original_path.$img;
        $this->saveImageDetails($path, 'my_design');
    }

    // Save My Design with image array
    public function saveMyDesignWithFileArray($img, $img_array)
    {
        $original_path = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY');
        $img_array->move($original_path, $img);
        $path = $original_path.$img;
        //    $this->saveImageDetails($path, 'my_design');
    }

    // Save SVG Image
    public function saveSvgImage($image_array, $img)
    {
        $original_path = '../..'.Config::get('constant.SVG_IMAGES_DIRECTORY');
        $image_array->move($original_path, $img);
    }

    // Save Original Image From Array
    public function saveOriginalImageFromArray($image_array, $img)
    {
        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        $image_array->move($original_path, $img);
        $path = $original_path.$img;
        $this->saveImageDetails($path, 'original');
    }

    // Save Encoded Image
    public function saveEncodedImage($image_array, $professional_img)
    {
        $path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
        file_put_contents($path, $image_array);
    }

    // Save Compressed Image
    public function saveCompressedImage($cover_img)
    {
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $compressed_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);

            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);

            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }
            //use for Image Details
            //Log::info(["compressed_path :" => $compressed_path]);

            $this->saveImageDetails($compressed_path, 'compressed');

        } catch (Exception $e) {
            (new ImageController())->logs('saveCompressedImage', $e);
            //      Log::error("saveCompressedImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $dest2 = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;

            //File::copy($dest1, $dest2);
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::error('saveCompressedImage into foreach : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            }
        }
    }

    // Get Thumbnail Width Height
    public function getThumbnailWidthHeight($professional_img)
    {

        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
        $image_size = getimagesize($original_path);
        $width_orig = $image_size[0];
        $height_orig = $image_size[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig) {
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }

        $array = ['width' => $width, 'height' => $height];

        return $array;
    }

    // Save Thumbnail Image
    public function saveThumbnailImage($professional_img)
    {
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path, 'thumbnail');

        } catch (Exception $e) {
            (new ImageController())->logs('saveThumbnailImage', $e);
            //      Log::error("saveThumbnailImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }

            return '1';
        }
    }

    // Get Thumbnail Width Height From S3
    public function getThumbnailWidthHeightFromS3($file_path)
    {
        $image_size = getimagesize($file_path);
        $width_orig = $image_size[0];
        $height_orig = $image_size[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig) {
            $width = (int) $height * $ratio_orig;
        }
        if ($width % 2 != 0) {
            $width = (int) $width + 1;
        } else {
            $height = $width / $ratio_orig;
        }
        if ($height % 2 != 0) {
            $height = (int) $height + 1;
        }

        $array = ['width' => $width, 'height' => $height];

        return $array;
    }

    // Save Thumbnail Image From S3
    public function generateAllImagesFromOriginalImage($file_name)
    {
        try {

            $source_file_path_of_aws = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$file_name;
            $source_file_path = '../..'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY').$file_name;

            if (Config::get('constant.APP_ENV') != 'local') {

                /*-----------------| generate original image |-----------------*/
                copy($source_file_path_of_aws, $source_file_path);
            }

            $file_data = pathinfo(basename($source_file_path));
            $file_name_without_ext = $file_data['filename'];

            /*-----------------| generate webp_original image |-----------------*/
            /*
             *  -q Set image quality
             *  -o Output file name
             */

            $destination_file_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_ORIGINAL_IMG_PATH').$file_name_without_ext.'.webp';
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            $cmd = "$libwebp -q $quality $source_file_path -o $destination_file_path";

            if (Config::get('constant.APP_ENV') != 'local') {
                $result = (! shell_exec($cmd));
            } else {
                $result = (! exec($cmd));
            }

            /*-----------------| generate compressed image |-----------------*/
            $compressed_path = '../..'.Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY').$file_name;
            $img = Image::make($source_file_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($source_file_path);
            $compress_img_size = filesize($compressed_path);

            if ($compress_img_size >= $original_img_size) {
                //save original image in compressed directory
                File::delete($compressed_path);
                File::copy($source_file_path, $compressed_path);
            }

            /*-----------------| generate thumbnail image |-----------------*/
            $array = $this->getThumbnailWidthHeightFromS3($source_file_path);
            $width = $array['width'];
            $height = $array['height'];
            $thumbnail_path = '../..'.Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY').$file_name;
            $img = Image::make($source_file_path)->resize($width, $height);
            $img->save($thumbnail_path);

            /*-----------------| generate webp_thumbnail image |-----------------*/
            $image_size = getimagesize($source_file_path);
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;

            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_thumbnail_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_THUMBNAIL_IMG_PATH').$file_name_without_ext.'.webp';
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $source_file_path -resize $width $height -o $webp_thumbnail_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }
                $file_info = ['height' => $height, 'width' => $width, 'file_name' => $file_name_without_ext.'.webp'];
            } else {

                $cmd = "$libwebp -q $quality $source_file_path -resize $width_orig $height_orig -o $webp_thumbnail_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }
                $file_info = ['height' => $height_orig, 'width' => $width_orig, 'file_name' => $file_name_without_ext.'.webp'];
            }

            //use for Image Details
            $this->saveImageDetails($compressed_path, 'user_uploaded_compressed');
            $this->saveImageDetails($thumbnail_path, 'user_uploaded_thumbnail');

            return $file_info;

        } catch (Exception $e) {
            (new ImageController())->logs('generateAllImagesFromOriginalImage', $e);
            //      Log::error("generateAllImagesFromOriginalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save Thumbnail Image
    public function saveWebpThumbnailImage($professional_img)
    {
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;

            $file_data = pathinfo(basename($thumbnail_path));
            //convert image into .webp format
            $webp_name = $file_data['filename'];
            $image_size = getimagesize($original_path);
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;

            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH').$webp_name.'.webp';
            $org_path = Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH').$professional_img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }

                return ['height' => $height, 'width' => $width];
            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }

                return ['height' => $height_orig, 'width' => $width_orig];
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveWebpThumbnailImage', $e);
            //      Log::error("saveWebpThumbnailImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }

            return '';
        }
    }

    // Save Compressed and Thumbnail Image
    public function saveCompressedThumbnailImage($source_url, $destination_url, $thumbnail_path)
    {

        $info = getimagesize($source_url);
        $width_orig = $info[0];
        $height_orig = $info[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig) {
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }

        if ($info['mime'] == 'image/jpeg') {
            // save compress image
            $image = imagecreatefromjpeg($source_url);
            imagejpeg($image, $destination_url, 75);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($tmp_img, $thumbnail_path);

        } elseif ($info['mime'] == 'image/png') {
            // save compress image
            $image = imagecreatefrompng($source_url);
            imagepng($image, $destination_url, 9);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
            $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
            imagefilledrectangle($tmp_img, 0, 0, $width_orig, $height_orig, $transparent);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagepng($tmp_img, $thumbnail_path);
        }
    }

    //Check Compress image >= original image ? save original image : compress
    public function CompressImageCheck($img_type, $image_array)
    {
        try {
            //Create Image Name
            $img_name = $this->generateNewFileName($img_type, $image_array);

            //create Original Image Path
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');

            //Create Compress Image Path
            $compress_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY');

            //get Original Image Size
            $original_img_size = filesize($image_array);
            //Log::info(["Original Img Size"=>$original_img_size]);

            //Move Image in User decide Folder(Save)
            Input::file('file')->move($original_path, $img_name);

            //Compressed Image
            $img = Image::make($original_path.$img_name);
            $img->save($compress_path.$img_name, 75);
            $compress_img_size = filesize($compress_path.$img_name);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            //compress image size >= original image size
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compress_path.$img_name);
                File::copy($original_path.$img_name, $compress_path.$img_name);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('CompressImageCheck', $e);
            //      Log::error("CompressImageCheck : ", ["Exception", $e->getMessage(), "\nTraceAsString", $e->getTraceAsString()]);
        }

    }

    //Delete Images In Directory
    public function deleteImage($image_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($image_name, 'original');
                $this->deleteObjectFromS3($image_name, 'compressed');
                $this->deleteObjectFromS3($image_name, 'thumbnail');

            } else {

                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.COMPRESSED_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY'));

            }
            DB::beginTransaction();
            /* Image Details delete */
            DB::delete('DELETE
                        FROM
                          image_details
                        WHERE
                          name = ?', [$image_name]);
            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('deleteImage', $e);
            //      Log::error("deleteImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();

            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    //image Analysis
    public function saveImageDetails($image_path, $image_directory)
    {
        try {
            //Log::info('image_path:',['path' =>$image_path]);
            $file_info = pathinfo($image_path);
            $name = $file_info['basename'];
            $type = $file_info['extension'];
            $image_detail = getimagesize($image_path); //Image::make($image_path)->width();
            $width = $image_detail[0];
            $height = $image_detail[1]; //Image::make($image_path)->height();
            $size = filesize($image_path);
            //$size = $bytes/(1024 * 1024);
            //Log::info('file details:',[$name,$type,$height,$width,$size]);
            $pixel = 0;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('INSERT INTO image_details
                            (name,directory_name,type,size,height,width,pixel,create_time)
                            values (?,?,?,?,?,?,?,?)',
                [$name, $image_directory, $type, $size, $height, $width, $pixel, $create_at]);

            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('saveImageDetails', $e);
            //      Log::error("saveImageDetails : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();

            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save image details.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    // Save original Image
    public function saveMultipleOriginalImage($img, $file_name)
    {

        $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        Input::file($file_name)->move($original_path, $img);

        //use for Image Details
        $path = $original_path.$img;
        $this->saveImageDetails($path, 'original');
    }

    public function saveMultipleCompressedImage($cover_img, $file_name)
    {
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $compressed_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                //Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }
            //use for Image Details
            $this->saveImageDetails($compressed_path, 'compressed');

        } catch (Exception $e) {
            (new ImageController())->logs('saveMultipleCompressedImage', $e);
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$cover_img;
            $dest2 = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$cover_img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::error('saveMultipleCompressedImage : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            }
        }

    }

    public function saveMultipleThumbnailImage($professional_img, $file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $thumbnail_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);

            //use for Image Details
            $this->saveImageDetails($thumbnail_path, 'thumbnail');

        } catch (Exception $e) {
            (new ImageController())->logs('saveMultipleThumbnailImage', $e);
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
        }
    }

    public function saveResourceImage($image_array)
    {
        $image = $image_array->getClientOriginalName();
        $resource_path = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
        $this->unlinkFileFromLocalStorage($image, $resource_path);
        $original_path = '../..'.$resource_path;
        $image_array->move($original_path, $image);
    }

    public function save3DObjectImage($image_array)
    {
        $image = $image_array->getClientOriginalName();
        $directory_path = Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY');
        $this->unlinkFileFromLocalStorage($image, $directory_path);
        $original_path = '../..'.$directory_path;
        $image_array->move($original_path, $image);
    }

    public function saveTransparentImage($image_array)
    {
        $image = $image_array->getClientOriginalName();
        $directory_path = Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY');
        $this->unlinkFileFromLocalStorage($image, $directory_path);
        $original_path = '../..'.$directory_path;
        $image_array->move($original_path, $image);
    }

    public function saveStockPhotos($image_array)
    {
        $image = $image_array->getClientOriginalName();
        $original_path = '../..'.Config::get('constant.STOCK_PHOTOS_IMAGES_DIRECTORY');
        $image_array->move($original_path, $image);
    }

    public function saveStlFile($parameter_name, $file_name)
    {
        $stl_file_path = '../..'.Config::get('constant.3D_OBJECTS_DIRECTORY');
        Input::file($parameter_name)->move($stl_file_path, $file_name);
    }

    public function saveJsonData($json_array, $json_name)
    {
        $directory_path = Config::get('constant.JSON_FILE_DIRECTORY');
        $this->unlinkFileFromLocalStorage($json_name, $directory_path);
        $original_path = '../..'.$directory_path;
        $json_array->move($original_path, $json_name);
    }

    // Save Json File InTo S3
    public function saveJsonDataInToS3($json_file)
    {
        try {

            $json_file_path = '../..'.Config::get('constant.JSON_FILE_DIRECTORY').$json_file;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($json_file, 'json');

            if (($is_exist = ($this->checkFileExist($json_file_path)) != 0)) {
                $resource_targetFile = "$aws_bucket/json/".$json_file;
                $disk->put($resource_targetFile, file_get_contents($json_file_path), 'public');
            } else {
                Log::info('saveJsonDataInToS3 : file not exist ', [$json_file_path]);
            }

            $this->unlinkFileFromLocalStorage($json_file, Config::get('constant.JSON_FILE_DIRECTORY'));

        } catch (Exception $e) {
            Log::error('saveJsonDataInToS3 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    public function unlinkImage($image_array)
    {
        try {
            $image = $image_array->getClientOriginalName();

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('unlinkImage', $e);
            //      Log::error("unlinkImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function unlinkStlFile($file_array)
    {
        try {
            $file = $file_array->getClientOriginalName();

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file, '3d_object');
            } else {

                $this->unlinkFileFromLocalStorage($file, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('unlinkStlFile', $e);
            //      Log::error("unlinkStlFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function unlink3DObjectImage($image_array)
    {
        try {
            $image = $image_array->getClientOriginalName();

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('unlink3DObjectImage', $e);
            //      Log::error("unlink3DObjectImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }

    // Unlink Image from image_bucket
    public function unlinkFile($image)
    {
        try {

            $original_image_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$image;

            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
            }

            $compressed_image_path = '../..'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$image;

            if (($is_exist = ($this->checkFileExist($compressed_image_path)) != 0)) {
                unlink($compressed_image_path);
            }

            $thumbnail_image_path = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$image;

            if (($is_exist = ($this->checkFileExist($thumbnail_image_path)) != 0)) {
                unlink($thumbnail_image_path);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('unlinkFile', $e);
            //      Log::error("unlinkFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save Image InTo S3
    public function saveImageInToS3($image)
    {
        try {

            $original_directory = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            $compressed_directory = Config::get('constant.COMPRESSED_IMAGES_DIRECTORY');
            $thumbnail_directory = Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY');

            $original_sourceFile = '../..'.$original_directory.$image;
            $compressed_sourceFile = '../..'.$compressed_directory.$image;
            $thumbnail_sourceFile = '../..'.$thumbnail_directory.$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/original/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                //Log::info('save original image : ',['bucket_name' => $aws_bucket, 'target' => $original_targetFile, 'source' => $original_sourceFile]);

            }

            if (($is_exist = ($this->checkFileExist($compressed_sourceFile)) != 0)) {
                $compressed_targetFile = "$aws_bucket/compressed/".$image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');
            }

            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
                $thumbnail_targetFile = "$aws_bucket/thumbnail/".$image;
                //        $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');
                $disk->put(
                    $thumbnail_targetFile,
                    file_get_contents($thumbnail_sourceFile),
                    [
                        'CacheControl' => Config::get('constant.MAX_AGE'),
                        'visibility' => 'public',
                    ]
                );
            }

            $this->unlinkFileFromLocalStorage($image, $original_directory);
            $this->unlinkFileFromLocalStorage($image, $compressed_directory);
            $this->unlinkFileFromLocalStorage($image, $thumbnail_directory);

        } catch (Exception $e) {
            (new ImageController())->logs('saveImageInToS3', $e);
            //      Log::error("saveImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save video InTo S3
    public function saveVideoInToS3($video)
    {
        try {

            $original_directory = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');

            $original_sourceFile = '../..'.$original_directory.$video;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/video/".$video;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                //Log::info('save original image : ',['bucket_name' => $aws_bucket, 'target' => $original_targetFile, 'source' => $original_sourceFile]);
            }

            $this->unlinkFileFromLocalStorage($video, $original_directory);

        } catch (Exception $e) {
            (new ImageController())->logs('saveImageInToS3', $e);
            //      Log::error("saveImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save My Design InTo S3
    public function saveMyDesignInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();

            //$original_sourceFile = $base_url . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY') . $image;
            $original_sourceFile = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/my_design/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('saveMyDesignInToS3', $e);
            //      Log::error("saveMyDesignInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save User Uploaded Image InTo S3
    public function saveUserUploadedImageInToS3($image, $webp_file_name)
    {
        try {

            $original_sourceFile = '../..'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY').$image;
            $compressed_sourceFile = '../..'.Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY').$image;
            $thumbnail_sourceFile = '../..'.Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY').$image;
            $webp_original_sourceFile = '../..'.Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY').$webp_file_name;
            $webp_thumbnail_sourceFile = '../..'.Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY').$webp_file_name;
            $orginal_video_thumbnail_image = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/user_uploaded_original/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                //        $disk->put(
                //          $original_targetFile,
                //          file_get_contents($original_sourceFile),
                //          [
                //            'CacheControl'  => 'max-age=2622000',
                //            'visibility' => 'public',
                //          ]
                //        );
                unlink($original_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($compressed_sourceFile)) != 0)) {
                $compressed_targetFile = "$aws_bucket/user_uploaded_compressed/".$image;
                $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');
                unlink($compressed_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
                $thumbnail_targetFile = "$aws_bucket/user_uploaded_thumbnail/".$image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');
                unlink($thumbnail_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($webp_original_sourceFile)) != 0)) {
                $webp_org_targetFile = "$aws_bucket/user_uploaded_webp_original/".$webp_file_name;
                $disk->put($webp_org_targetFile, file_get_contents($webp_original_sourceFile), 'public');
                unlink($webp_original_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($webp_thumbnail_sourceFile)) != 0)) {
                $webp_thumb_targetFile = "$aws_bucket/user_uploaded_webp_thumbnail/".$webp_file_name;
                $disk->put($webp_thumb_targetFile, file_get_contents($webp_thumbnail_sourceFile), 'public');
                unlink($webp_thumbnail_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($orginal_video_thumbnail_image)) != 0)) {
                $video_thumbnail_image_targetFile = "$aws_bucket/user_uploaded_video_thumbnail/".$image;
                $disk->put($video_thumbnail_image_targetFile, file_get_contents($orginal_video_thumbnail_image), 'public');
                unlink($orginal_video_thumbnail_image);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedImageInToS3', $e);
            //      Log::error("saveUserUploadedImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $e->getMessage();
            //      return array('code' => 201, 'msg' => $e->getMessage());
        }

    }

    // Save User Uploaded Font InTo S3
    public function saveUserUploadedFontInToS3($font_file, $preview_img_name)
    {
        try {

            $preview_directory = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY');
            $font_directory = Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY');
            $font_sourceFile = '../..'.$font_directory.$font_file;
            $preview_sourceFile = '../..'.$preview_directory.$preview_img_name;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($font_sourceFile)) != 0)) {
                $font_targetFile = "$aws_bucket/user_uploaded_fonts/".$font_file;
                $disk->put($font_targetFile, file_get_contents($font_sourceFile), 'public');
            }

            if (($is_exist = ($this->checkFileExist($preview_sourceFile)) != 0)) {
                $preview_targetFile = "$aws_bucket/user_uploaded_original/".$preview_img_name;
                $disk->put($preview_targetFile, file_get_contents($preview_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($font_file, $font_directory);
            $this->unlinkFileFromLocalStorage($preview_img_name, $preview_directory);

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedFontInToS3', $e);
            //      Log::error("saveUserUploadedFontInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save SVG Image InTo S3
    public function saveSvgImageInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();

            $original_sourceFile = '../..'.Config::get('constant.SVG_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/svg/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.SVG_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('saveSvgImageInToS3', $e);
            //      Log::error("saveSvgImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Image InTo S3
    public function saveResourceImageInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();

            //$resource_sourceFile = $base_url . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image;
            $resource_sourceFile = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($image, 'resource');

            if (($is_exist = ($this->checkFileExist($resource_sourceFile)) != 0)) {
                $resource_targetFile = "$aws_bucket/resource/".$image;
                $disk->put($resource_targetFile, file_get_contents($resource_sourceFile), 'public');
            } else {
                Log::info('saveResourceImageInToS3 : file not exist ', [$resource_sourceFile]);
            }

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('saveResourceImageInToS3', $e);
            //      Log::error("saveResourceImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Image 3D Object InTo S3
    public function save3DObjectImageInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();

            //$resource_sourceFile = $base_url . Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY') . $image;
            $resource_sourceFile = '../..'.Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($image, 'object_images');

            if (($is_exist = ($this->checkFileExist($resource_sourceFile)) != 0)) {
                $resource_targetFile = "$aws_bucket/object_images/".$image;
                $disk->put($resource_targetFile, file_get_contents($resource_sourceFile), 'public');

                unlink($resource_sourceFile);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('save3DObjectImageInToS3', $e);
            //      Log::error("save3DObjectImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // saveTransparentImageInToS3
    public function saveTransparentImageInToS3($image)
    {
        try {

            $resource_sourceFile = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($image, 'my_design');

            if (($is_exist = ($this->checkFileExist($resource_sourceFile)) != 0)) {
                $resource_targetFile = "$aws_bucket/my_design/".$image;
                $disk->put($resource_targetFile, file_get_contents($resource_sourceFile), 'public');

                unlink($resource_sourceFile);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveTransparentImageInToS3', $e);
            //      Log::error("saveTransparentImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Stock Photos InTo S3
    public function saveStockPhotosInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();
            //$resource_sourceFile = $base_url . Config::get('constant.STOCK_PHOTOS_IMAGES_DIRECTORY') . $image;
            $resource_sourceFile = '../..'.Config::get('constant.STOCK_PHOTOS_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($image, 'stock_photos');

            if (($is_exist = ($this->checkFileExist($resource_sourceFile)) != 0)) {
                $resource_targetFile = "$aws_bucket/stock_photos/".$image;
                $disk->put($resource_targetFile, file_get_contents($resource_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($image, Config::get('constant.STOCK_PHOTOS_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('saveStockPhotosInToS3', $e);
            //      Log::error("saveStockPhotosInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Webp Image InTo S3
    public function saveWebpImageInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();

            $original_sourceFile = '../..'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY').$image;
            $thumbnail_sourceFile = '../..'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY').$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                //if (fopen($original_sourceFile, "r")) {

                $original_targetFile = "$aws_bucket/webp_original/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                $this->unlinkFileFromLocalStorage($image, Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY'));

            }
            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
                //if (fopen($thumbnail_sourceFile, "r")) {

                $thumbnail_targetFile = "$aws_bucket/webp_thumbnail/".$image;
                //        $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');
                $disk->put(
                    $thumbnail_targetFile,
                    file_get_contents($thumbnail_sourceFile),
                    [
                        'CacheControl' => Config::get('constant.MAX_AGE'),
                        'visibility' => 'public',
                    ]
                );
                $this->unlinkFileFromLocalStorage($image, Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveWebpImageInToS3', $e);
            //      Log::error("saveWebpImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save Stl File InTo S3
    public function saveStlFileInToS3($image)
    {
        try {
            //$base_url = (new ImageController())->getBaseUrl();
            $url = Config::get('constant.3D_OBJECTS_DIRECTORY');
            $original_sourceFile = '../..'.$url.$image;
            //$original_sourceFile = Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN') . $image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/3d_object/".$image;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($image, $url);

        } catch (Exception $e) {
            (new ImageController())->logs('saveStlFileInToS3', $e);
            //      Log::error("saveStlFileInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Save Font InTo S3
    public function saveFontInToS3($font_file, $preview_img)
    {
        try {
            $original_source_file = '../..'.Config::get('constant.FONT_FILE_DIRECTORY').$font_file;
            $preview_img_source_file = '../..'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY').$preview_img;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($font_file, 'fonts');
            $this->deleteObjectFromS3($preview_img, 'font_preview_image');

            if (($is_exist = ($this->checkFileExist($original_source_file)) != 0)) {

                $original_targetFile = "$aws_bucket/fonts/".$font_file;
                $disk->put($original_targetFile, file_get_contents($original_source_file), 'public');

                unlink($original_source_file);

            }

            if (($is_exist = ($this->checkFileExist($preview_img_source_file)) != 0)) {

                $original_targetFile = "$aws_bucket/font_preview_image/".$preview_img;
                $disk->put($original_targetFile, file_get_contents($preview_img_source_file), 'public');

                unlink($preview_img_source_file);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontInToS3', $e);
            //      Log::error("saveFontInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function saveFontPreviewImageInToS3($preview_img)
    {
        try {
            $preview_img_source_file = '../..'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY').$preview_img;
            $aws_bucket = config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($preview_img_source_file)) != 0)) {
                $original_targetFile = "$aws_bucket/font_preview_image/".$preview_img;
                $disk->put($original_targetFile, file_get_contents($preview_img_source_file), 'public');

                unlink($preview_img_source_file);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontPreviewImageInToS3', $e);
        }
    }

    //verify zip file
    public function verifyZipFile($image_array)
    {

        $file_type = $image_array->getMimeType();
        $file_size = $image_array->getSize();
        //Log::info('extension : ', ['extension' => $file_type]);
        //Log::info("Image Size",[$image_size]);
        $MAXIMUM_FILESIZE = 12 * 1024 * 1024;

        if (! ($file_type == 'application/zip')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select zip file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($file_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then 10MB', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    public function saveZipFile($file_data)
    {
        $file = $file_data->getClientOriginalName();
        $file_path = '../..'.Config::get('constant.ZIP_FILE_DIRECTORY');
        $file_data->move($file_path, $file);

    }

    //checkIsImageExist
    public function checkIsImageExist($image_array)
    {
        try {
            $exist_files_array = [];
            foreach ($image_array as $key) {

                $image = $key->getClientOriginalName();
                $image_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$image;

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $disk = Storage::disk('s3');
                    $value = "$aws_bucket/resource/".$image;
                    if ($disk->exists($value)) {
                        $exist_files_array[] = ['url' => Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image, 'name' => $image];
                    }
                } else {
                    if (($is_exist = ($this->checkFileExist($image_path)) != 0)) {
                        $exist_files_array[] = ['url' => Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image, 'name' => $image];
                    }
                }
            }
            if (count($exist_files_array) > 0) {
                $array = ['existing_files' => $exist_files_array];
                $result = json_decode(json_encode($array), true);

                return $response = Response::json(['code' => 420, 'message' => 'File already exists.', 'cause' => '', 'data' => $result]);
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkIsImageExist Exception', $e);
            //      Log::debug("checkIsImageExist Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }

    }

    //checkStlIsExist
    public function checkStlIsExist($image_array)
    {
        try {
            $exist_files_array = [];

            $file = $image_array->getClientOriginalName();

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/3d_object/".$file;
                if ($disk->exists($value)) {

                    $exist_files_array[] = ['url' => Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').$file, 'name' => $file];
                }
            } else {

                $file_path = '../..'.Config::get('constant.3D_OBJECTS_DIRECTORY').$file;

                if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                    $exist_files_array[] = ['url' => Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').$file, 'name' => $file];
                }

            }

            if (count($exist_files_array) > 0) {
                $array = ['existing_files' => $exist_files_array];

                $result = json_decode(json_encode($array), true);

                return $response = Response::json(['code' => 420, 'message' => 'File already exists.', 'cause' => '', 'data' => $result]);
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkStlIsExist Exception', $e);
            //      Log::debug("checkStlIsExist Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }

    }

    //getRandomColor
    public function getRandomColor($file)
    {
        try {
            $size = getimagesize($file);
            if (! $size) {
                return false;
            }
            switch ($size['mime']) {
                case 'image/jpeg':
                    $img = imagecreatefromjpeg($file);
                    break;
                case 'image/png':
                    $img = imagecreatefrompng($file);
                    break;
                case 'image/gif':
                    $img = imagecreatefromgif($file);
                    break;
                case 'image/webp':
                    $img = imagecreatefromwebp($file);
                    break;
                default:
                    return false;
            }
            if ($img) {
                /*http://php.net/manual/en/function.imagecolorat.php*/
                $rgb = imagecolorat($img, 10, 15);

                if (! (! $rgb)) {
                    $result = '#'.dechex($rgb);

                } else {
                    $result = '#21353d';
                }

            } else {
                $result = '#21353d';
            }

            return $result;
        } catch (Exception $e) {
            (new ImageController())->logs('getRandomColor', $e);
            //      Log::error("getRandomColor : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    //get image type
    public function getImageType($image_array)
    {
        try {
            $image_type = $image_array->getMimeType();

            if ($image_type == 'image/svg+xml') {
                $content_type = Config::get('constant.CONTENT_TYPE_OF_SVG');
            } else {
                $content_type = Config::get('constant.CONTENT_TYPE_OF_IMAGE');
            }

            return $content_type;
        } catch (Exception $e) {
            (new ImageController())->logs('getImageType', $e);
            //      Log::error("getImageType : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Delete json data From S3
    public function deleteCDNCache($files_array)
    {
        try {

            //verify credentials
            $cloudFrontClient = CloudFrontClient::factory([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => Config::get('constant.AWS_KEY'),
                    'secret' => Config::get('constant.AWS_SECRET'),
                ],
            ]);

            //create new invalidation for delete cache
            $result = $cloudFrontClient->createInvalidation([
                'DistributionId' => Config::get('constant.CDN_DISTRIBUTION_ID'),
                'InvalidationBatch' => [
                    'CallerReference' => time(),
                    'Paths' => [
                        'Items' => $files_array,
                        'Quantity' => count($files_array),
                    ],
                ],
            ]);

        } catch (Exception $e) {
            Log::error('deleteCDNCache Exception: ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $result = '';
        } catch (AwsException $e) {
            Log::error('deleteCDNCache AwsException: ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $result = '';
        }

        return $result;
    }

    //Delete all resource that used in user's design that is (sample image, object image, transparent image & stock photos)
    public function deleteAllFilesUsedInDesign($deleted_file_list)
    {
        try {

            foreach ($deleted_file_list as $file_detail) {
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    $this->deleteObjectFromS3($file_detail['name'], $file_detail['path']);
                } else {
                    unlink('../../image_bucket/'.$file_detail['path'].'/'.$file_detail['name']);
                }
            }

        } catch (Exception $e) {
            Log::error('deleteAllFilesUsedInDesign : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Delete json data From S3
    public function deleteResourceImages($file_name)
    {
        try {
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $this->deleteObjectFromS3($file_name, 'resource');
            } else {
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
            }
        } catch (Exception $e) {
            Log::error('deleteResourceImages : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Delete json data From S3
    public function deleteJsonData($file_name)
    {
        try {
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $this->deleteObjectFromS3($file_name, 'json');
            } else {
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.JSON_FILE_DIRECTORY'));
            }
        } catch (Exception $e) {
            Log::error('deleteJsonData : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Delete My Design From S3
    public function deleteMyDesign($file_name)
    {
        try {
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'my_design');

            } else {

                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY'));
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyDesign', $e);
            //      Log::error("deleteMyDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Delete Stock Photo From S3
    public function deleteStockPhotos($file_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'stock_photos');

            } else {

                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.STOCK_PHOTOS_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteStockPhotos', $e);
            //      Log::error("deleteStockPhotos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Delete Uploaded Fonts From S3
    public function deleteUploadedFonts($font_file_name, $preview_img)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                //Log::info('deleteUploadedFonts S3_BUCKET : ',['font_file_name' => $font_file_name, 'preview_img' => $preview_img]);

                $this->deleteObjectFromS3($font_file_name, 'user_uploaded_fonts');
                $this->deleteObjectFromS3($preview_img, 'user_uploaded_original');
            } else {
                //Log::info('deleteUploadedFonts LOCAL : ',['font_file_name' => $font_file_name, 'preview_img' => $preview_img]);
                $this->unlinkFileFromLocalStorage($font_file_name, Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($preview_img, Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY'));
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUploadedFonts', $e);
            //      Log::error("deleteUploadedFonts : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    // Delete 3D Object Image From S3
    public function delete3DObjectImage($file_name)
    {
        try {
            //Log::debug('delete3DObjectImage : ',['images'=>$file_name]);
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'object_images');

            } else {

                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('delete3DObjectImage', $e);
            //      Log::error("delete3DObjectImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }

    // Delete Transparent Image From S3
    public function deleteTransparentImage($file_name)
    {
        try {
            //Log::debug('deleteTransparentImage : ',['images'=>$file_name]);
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'my_design');

            } else {

                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteTransparentImage', $e);
            //      Log::error("deleteTransparentImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }

    // Delete user profile From S3
    public function deleteUserProfile($file_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'original');
                $this->deleteObjectFromS3($file_name, 'compressed');
                $this->deleteObjectFromS3($file_name, 'thumbnail');

            } else {

                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.COMPRESSED_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUserProfile', $e);
            //      Log::error("deleteUserProfile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // unlinkFileFromLocalStorage
    public function unlinkFileFromLocalStorage($file, $path)
    {
        try {

            $original_image_path = '../..'.$path.$file;

            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('unlinkFileFromLocalStorage', $e);
            //      Log::debug("unlinkFileFromLocalStorage Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }

    }

    public function deleteObjectFromLocal($file, $path)
    {
        try {
            $original_image_path = $path.$file;

            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteObjectFromLocal', $e);
            //Log::error("deleteObjectFromLocal Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function deleteObjectFromS3($file, $directory)
    {
        try {

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            $original = "$aws_bucket/$directory/".$file;
            if ($disk->exists($original)) {
                $disk->delete($original);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteObjectFromS3 Exception', $e);
            //      Log::debug("deleteObjectFromS3 Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    public function checkFileExist($file_path)
    {
        try {

            //if (fopen($original_sourceFile, "r")) {
            if (File::exists($file_path)) {
                //Log::info('file exist : ',['path' => $file_path]);
                $response = 1;
            } else {
                $response = 0;
                //Log::info('file does not exist : ', ['path' => $file_path]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('checkFileExist Exception', $e);
            //      Log::debug("checkFileExist Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = 0;
        }

        return $response;
    }

    public function generatePreviewImage($font_file_name, $preview_img_name, $preview_img_text, $is_from_admin)
    {
        try {

            // Set the content-type
            header('Content-Type: image/png');

            /* 0=upload font from user,
             * 1=upload font from admin/generate preview image of uploaded font from image_bucket,
             * */

            if ($is_from_admin == 1) {
                //Source path of font file
                $font = '../..'.Config::get('constant.FONT_FILE_DIRECTORY').$font_file_name;

                //Destination path to save preview image
                $path = '../..'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY').$preview_img_name;

            } else {
                //Source path of font file
                $font = '../..'.Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY').$font_file_name;

                //Destination path to save preview image
                $path = '../..'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY').$preview_img_name;

            }

            // Create the image
            $im = imagecreatetruecolor(400, 40);

            // Create some colors
            imagesavealpha($im, true); //Set the flag to save full alpha channel information (as opposed to single-color transparency) when saving PNG images
            $color = imagecolorallocatealpha($im, 0, 0, 0, 127); //set transparent image background
            imagefill($im, 0, 0, $color);

            $font_text_color = imagecolorallocate($im, 0, 0, 0); //Allocate a color for an image

            // $preview_img_text is a text to draw

            /* following function is not working in php >=7.1.12
             * imagettftext()
             * ref : https://bugs.php.net/bug.php?id=75656
             * */

            // Add the text
            imagettftext($im, 16, 0, 10, 27, $font_text_color, $font, $preview_img_text);

            // Using imagepng() results in clearer text compared with imagejpeg()
            (imagepng($im, $path));

        } catch (Exception $e) {
            (new ImageController())->logs('generatePreviewImages', $e);
            //      Log::error("generatePreviewImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate preview image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    // Save Single Image InTo S3
    public function saveSingleImageInToS3($file, $file_dir_path, $file_dir_name)
    {
        try {

            $source_file = '../..'.$file_dir_path.$file;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            $original_targetFile = "$aws_bucket/$file_dir_name/".$file;
            $disk->put($original_targetFile, file_get_contents($source_file), 'public');

            //delete file from local storage
            unlink($source_file);

        } catch (Exception $e) {
            (new ImageController())->logs('saveSingleImageInToS3', $e);
            //      Log::error("saveSingleImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save Single File InTo Local From S3
    public function saveSingleFileInToLocalFromS3($file_name, $source_file_path, $destination_file_path)
    {
        try {

            $source_file_path_of_aws = $source_file_path.$file_name;
            $destination_file_path = '../..'.$destination_file_path.$file_name;

            copy($source_file_path_of_aws, $destination_file_path);

        } catch (Exception $e) {
            (new ImageController())->logs('saveSingleFileInToLocalFromS3', $e);
            //      Log::error("saveSingleFileInToLocalFromS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //get validations from settings_master (from database)
    public function getValidationFromCache($category_id, $is_featured, $is_catalog)
    {
        try {
            $this->category_id = $category_id;
            $this->is_featured = $is_featured;
            $this->is_catalog = $is_catalog;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog" && Cache::get('getValidationFromCache') == [])) {
                $result = Cache::rememberforever("getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog", function () {

                    $validation_info = DB::select('SELECT
                                          id AS setting_id,
                                          category_id,
                                          validation_name,
                                          max_value_of_validation,
                                          is_featured,
                                          is_catalog,
                                          update_time
                                        FROM
                                          settings_master
                                        WHERE
                                          is_active = ? AND
                                          category_id = ? AND
                                          is_featured = ? AND
                                          is_catalog = ?', [1, $this->category_id, $this->is_featured, $this->is_catalog]);

                    if (count($validation_info) == 0) {
                        $validation_info = DB::select('SELECT
                                          id AS setting_id,
                                          category_id,
                                          validation_name,
                                          max_value_of_validation,
                                          is_featured,
                                          is_catalog,
                                          update_time
                                        FROM
                                          settings_master
                                        WHERE
                                          category_id = ? AND
                                          is_active = ?', [0, 1]);
                    }

                    return $validation_info[0]->max_value_of_validation;

                });
            }

            $redis_result = Cache::get("getValidationFromCache$this->category_id:$this->is_featured:$this->is_catalog");

            if (! $redis_result) {
                $redis_result = [];
            }

            return $redis_result;

        } catch (Exception $e) {
            (new ImageController())->logs('getValidationFromCache', $e);
            //      Log::error("getValidationFromCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get validations from cache.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    /* Video module */
    // Save original Video
    public function saveOriginalVideo($video, $folder_name, $original_path, $image_array)
    {
        try {
            $image_array->move($original_path, $video);
            $video_info = $this->getVideoInformation($original_path.$video);
            if ($video_info == '') {
                return Response::json(['code' => 201, 'message' => 'We couldn\'t get video information.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            //Log::info('saveOriginalVideo', [$response]);

            $this->saveVideoInformation($video_info, $video, $folder_name, null);

        } catch (Exception $e) {
            (new ImageController())->logs('saveOriginalVideo', $e);
            //      Log::error("saveOriginalVideo : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    // Save Video in image bucket
    public function saveVideo($video, $original_path, $video_array)
    {
        try {
            $video_array->move($original_path, $video);

        } catch (Exception $e) {
            (new ImageController())->logs('saveOriginalVideo', $e);
            //      Log::error("saveOriginalVideo : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    //Generate new thumbnail image name
    public function generateThumbnailFileName($image_type, $image_array)
    {

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.'png';
        $path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$new_file_name;
        if (File::exists($path)) {
            $new_file_name = uniqid().'_'.$image_type.'_'.time().'.'.'png';
        }

        return $new_file_name;
    }

    //Get(extract) original image from video and save in image_bucket/Original
    public function getAndSaveOriginalImageFromVideo($videoFile, $originalFilePath)
    {

        /*
        * -i Input file name
        * -an Disable audio
        * -ss Get image from X second in the video
        * -s Size of the image
        *
        */

        // For windows
        // $thumbnailFile='C:\wamp64\www\woc\image_bucket\thumbnail\\'.$thumbnailFileName;
        // $thumbnailSize="120*90";
        // $thumbnailSizeHD="240*180";
        // $getFromSecond=1;
        // $ffmpeg= "C:\\ffmpeg\\bin\\ffmpeg";
        // $cmd="$ffmpeg -i $videoFile -an -ss $getFromSecond -y -s $thumbnailSizeHD $thumbnailFile";
        // return (!exec($cmd));

        //For Linux
        /*$thumbnailFile='..\\image_bucket\\thumbnail\\".$thumbnailFileName';
        $cmd="ffmpeg -i $videoFile -an -ss $getFromSecond -y -s $thumbnailSize $thumbnailFile";
        return (!shell_exec($cmd));*/

        //From Composer Lib

        try {

            $ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries' => Config::get('constant.FFMPEG_PATH'),
                'ffprobe.binaries' => Config::get('constant.FFPROBE_PATH'),
            ]);

            // $d = "$ffmpeg -i $thumbnailFile1 2>&1 | grep 'Duration'";

            $video = $ffmpeg->open($videoFile);
            //log::info($video);

            //$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0.05));
            $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0.5));
            //log::info($frame);

            //$original_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $originalFilePath;

            $frame->save($originalFilePath);
            if (file_exists($originalFilePath)) {
                //Log::info('add_image_thumbnail', ['thumbnail_name' => $originalFilePath]);
                $response = '';
            } else {
                //$thumbnailFile1 = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$video_file_name;
                unlink($videoFile);
                //        Log::info('image_thumbnail_not_add', ['thumbnail_name' => $originalFilePath]);
                //        $response = 1;
                $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload this video file.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('getAndSaveOriginalImageFromVideo', $e);
            //      Log::error("getAndSaveOriginalImageFromVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
        }

        return $response;
    }

    //Delete Video In Directory
    public function deleteVideo($video_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($video_name, 'video');

            } else {

                $this->unlinkFileFromLocalStorage($video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteVideo', $e);
            //      Log::error("deleteVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    //Check is videos exist
    public function checkIsVideosExist($file_array)
    {
        try {
            $exist_files_array = [];

            foreach ($file_array as $key) {

                $file = $key->getClientOriginalName();
                $file_url = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$file;
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $disk = Storage::disk('s3');
                    $value = "$aws_bucket/video/".$file;
                    if ($disk->exists($value)) {

                        $exist_files_array[] = ['url' => $file_url, 'name' => $file];
                    }
                } else {
                    $file_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$file;
                    if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                        $exist_files_array[] = ['url' => $file_url, 'name' => $file];
                    }

                }

            }
            if (count($exist_files_array) > 0) {
                $array = ['existing_files' => $exist_files_array];
                $result = json_decode(json_encode($array), true);

                return $response = Response::json(['code' => 420, 'message' => 'File already exists.', 'cause' => '', 'data' => $result]);
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkIsVideoExist', $e);
            //      Log::error("checkIsVideoExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    //unlink video from image_bucket
    public function unlinkVideo($file_name)
    {
        try {

            //$file_path =  Config::get('constant.IMAGE_BUCKET_PATH') .Config::get('constant.ORIGINAL_VIDEO_DIRECTORY') . $file_name;
            $file_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$file_name;

            if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                unlink($file_path);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('unlinkVideo', $e);
            //      Log::error("unlinkVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Save Json Video In ToS3
    public function saveJsonVideoInToS3($video)
    {
        try {

            $video_dir = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
            $video_sourceFile = '../..'.$video_dir.$video;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($video, 'video');
            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = $aws_bucket.'/video/'.$video;
                $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

            /*$this->unlinkFileFromLocalStorage($video, $video_dir);*/

        } catch (Exception $e) {
            (new ImageController())->logs('saveJsonVideoInToS3', $e);
            //      Log::error("saveJsonVideoInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Check is video exist
    public function checkIsVideoExist($video_name)
    {
        try {
            $exist_files_array = [];
            $file = $video_name;

            $file_url = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$file;
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/video/".$file;
                if ($disk->exists($value)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            } else {
                $file_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$file;
                if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            }
            if (count($exist_files_array) > 0) {
                return $response = '';

            } else {
                return $response = Response::json(['code' => 434, 'message' => 'File does not exist. File name : '.$video_name, 'cause' => '', 'data' => json_decode('{}')]);

            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkIsVideoExist', $e);
            //      Log::error("checkIsVideoExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    //Verify Sample Image of cards
    public function verifySampleImageForVideo($image_array)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        /*
        * check size into kb
        * here 150 is kb & 1024 is bytes
        * 1kb = 1024 bytes
        */

        //$MAXIMUM_FILESIZE = 200 * 1024 * 1024;
        $MAX_SIZE_IN_KB = Config::get('constant.MAXIMUM_FILE_SIZE_OF_SAMPLE_IMAGE');
        $MAXIMUM_FILESIZE = $MAX_SIZE_IN_KB * 1024;
        //Log::info('Sample_image_size',[$image_size]);

        if (! ($image_type == 'image/png' || $image_type == 'image/jpeg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG file', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => "Sample image is greater then $MAX_SIZE_IN_KB KB", 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;

    }

    //verify Multiple Image ( Currently working only for transparent image)
    public function verifyMultipleImage($image_array, $file_name)
    {
        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //    Log::info("Image Size",[$image_size]);
        $MAXIMUM_FILESIZE = 150 * 1024;

        if (! ($image_type == 'image/png' || $image_type == 'image/jpeg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG file', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => ucfirst($file_name).' size is greater then 150KB, Please adjust size by zooming out with zoom adjustment slider.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //verify sample image's height and width
    public function validateHeightWidthOfSampleImage($image_array, $json_data)
    {
        // Open image as a string
        $data = file_get_contents($image_array);

        // getimagesizefromstring function accepts image data as string & return file info
        $file_info = getimagesizefromstring($data);
        // Display the image content
        $width = $file_info[0];
        $height = $file_info[1];

        //Log::info('validateHeightWidthOfSampleImage height & width : ',['height_from_img' => $height, 'width_from_img' => $width, 'height_from_json' => $json_data->height, 'width_from_json' => $json_data->width]);

        if ($json_data->height == $height && $json_data->width == $width) {
            $response = '';
        } else {
            return $response = Response::json(['code' => 201, 'message' => 'Height & width of the sample image doesn\'t match with height & width given in json.', 'cause' => '', 'data' => json_decode('{}')]);
        }

        return $response;
    }

    // Save multipart temp file
    public function saveMultipartTempFile($file_name, $file_array)
    {
        try {
            $original_path = '../..'.Config::get('constant.TEMP_DIRECTORY');
            $file_array->move($original_path, $file_name);
        } catch (Exception $e) {
            (new ImageController())->logs('saveMultipartTempFile', $e);
            //      Log::error("saveMultipartTempFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Generate height & width for preview video
    public function generatePreviewVideoHeightWidth($width_orig, $height_orig)
    {
        try {

            $ratio_orig = $width_orig / $height_orig;

            $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
            $height = $height_orig < Config::get('constant.PREVIEW_VIDEO_THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.PREVIEW_VIDEO_THUMBNAIL_HEIGHT');

            if ($width / $height > $ratio_orig) {
                $width = $height * $ratio_orig;
            } else {
                $height = $width / $ratio_orig;
            }

            $array = ['width' => $width, 'height' => $height];

            return $array;
        } catch (Exception $e) {
            (new ImageController())->logs('generatePreviewVideoHeightWidth', $e);
            //      Log::error("generatePreviewVideoHeightWidth : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Delete Webp Images In Directory
    public function deleteWebpImage($image_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($image_name, 'webp_original');
                $this->deleteObjectFromS3($image_name, 'webp_thumbnail');

            } else {

                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteWebpImage', $e);
            //      Log::error("deleteWebpImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete webp image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    /* User video */
    // Save User Uploaded Video
    public function saveUserUploadedVideo($video, $video_array)
    {
        try {
            $original_path = '../..'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY');
            $folder_name = 'user_uploaded_video';
            $video_array->move($original_path, $video);
            $path = $original_path.$video;
            $video_info = $this->getVideoInformation($path);
            $this->saveVideoInformation($video_info, $video, $folder_name, null);
        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedVideo', $e);
            //      Log::error("saveUserUploadedVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Get video information by path
    public function getVideoInformation($filepath)
    {
        try {

            $video_detail = (new ImageController())->getAudioInformation($filepath);
            $video_dimension = (new ImageController())->getVideoDimension($filepath);
            $detail = [];

            //dd($audio_detail->all()); //get ffprobe response into array

            $result = json_decode(json_encode($video_detail->all()), true);

            $detail['format_name'] = isset($result['format_name']) ? $result['format_name'] : null;
            $detail['duration'] = isset($result['duration']) ? date('H:i:s', intval($result['duration'])) : null;
            $detail['size'] = isset($result['size']) ? str_replace(',', '', number_format($result['size'] / 1024, 2)) : null;
            $detail['bit_rate'] = isset($result['bit_rate']) ? $result['bit_rate'] : null;
            $detail['title'] = isset($result['tags']['title']) ? $result['tags']['title'] : null;
            $detail['genre'] = isset($result['tags']['genre']) ? $result['tags']['genre'] : null;
            //$tag = isset($result['tags']['tag']) ? $result['tags']['tag'] : NULL;
            $detail['artist'] = isset($result['tags']['artist']) ? $result['tags']['artist'] : null;
            $detail['width'] = $video_dimension->getWidth();
            $detail['height'] = $video_dimension->getHeight();
            //Log::info([$detail]);
            $response = $detail;
        } catch (Exception $e) {
            (new ImageController())->logs('getVideoInformation', $e);
            //      Log::error("getVideoInformation : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = '';
        }

        return $response;
    }

    public function saveVideoInformation($video_info, $file_name, $file_path, $content_id)
    {
        try {

            $format_name = $video_info['format_name'];
            $duration = $video_info['duration'];
            $width = $video_info['width'];
            $height = $video_info['height'];
            $size = $video_info['size'];
            $bit_rate = $video_info['bit_rate'];
            $title = $video_info['title'];
            $genre = $video_info['genre'];
            $artist = $video_info['artist'];

            if ($content_id) {
                $is_exist = DB::select('SELECT * FROM video_details WHERE content_id = ?', [$content_id]);
                if (count($is_exist) > 0) {
                    DB::update('UPDATE video_details
                                SET format_name = ?,
                                    file_name = ?,
                                    file_path = ?,
                                    duration = ?,
                                    width=?,
                                    height = ?,
                                    size = ?,
                                    bit_rate = ?,
                                    genre = ?,
                                    title = ?,
                                    artist = ?
                                WHERE content_id = ?',
                        [
                            $format_name,
                            $file_name,
                            $file_path,
                            $duration,
                            $width,
                            $height,
                            $size,
                            $bit_rate,
                            $genre,
                            $title,
                            $artist,
                            $content_id,
                        ]);
                } else {
                    $create_time = date('Y-m-d H:i:s');
                    DB::insert('INSERT INTO video_details
                        (content_id,format_name, file_name, file_path, duration, width, height, size, bit_rate, genre, title,artist, is_active, create_time)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                        [$content_id, $format_name, $file_name, $file_path, $duration, $width, $height, $size, $bit_rate, $genre, $title, $artist, 1, $create_time]);
                }
            } else {
                $is_exist = DB::select('SELECT * FROM video_details WHERE file_name = ? AND file_path = ?', [$file_name, $file_path]);
                if (count($is_exist) > 0) {
                    DB::update('UPDATE video_details
                                SET format_name = ?,
                                    duration = ?,
                                    width = ?,
                                    height = ?,
                                    size = ?,
                                    bit_rate = ?,
                                    genre = ?,
                                    title = ?,
                                    artist = ?
                                WHERE file_name = ? AND file_path = ?',
                        [
                            $format_name,
                            $duration,
                            $width,
                            $height,
                            $size,
                            $bit_rate,
                            $genre,
                            $title,
                            $artist,
                            $file_name,
                            $file_path,
                        ]);
                } else {
                    $create_time = date('Y-m-d H:i:s');
                    DB::beginTransaction();
                    DB::insert('insert into video_details
                        (file_name,format_name, file_path, duration, width, height, size, bit_rate, genre, title,artist, is_active, create_time)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                        [$file_name, $format_name, $file_path, $duration, $width, $height, $size, $bit_rate, $genre, $title, $artist, 1, $create_time]);
                    DB::commit();
                }
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveVideoInformation', $e);
            //      Log::error("saveVideoInformation : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function getAudioInformation($filename)
    {
        try {

            /*if (env('APP_ENV') != 'local') {*/
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                /* Linux */
                //        Log::info('ffmpeg path : ',[Config::get('constant.FFMPEG_PATH')]);
                //        Log::info('ffprobe path : ',[Config::get('constant.FFPROBE_PATH')]);
                $ffprobe = FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries' => Config::get('constant.FFMPEG_PATH'),
                    'ffprobe.binaries' => Config::get('constant.FFPROBE_PATH')]);

                $data = $ffprobe->getFFProbe()
                    ->format($filename); // extracts file information*/
            } else {
                /* Windows */
                $ffprobe = FFMpeg\FFProbe::create([
                    'ffmpeg.binaries' => Config::get('constant.FFMPEG_PATH'),
                    'ffprobe.binaries' => Config::get('constant.FFPROBE_PATH')]);

                $data = $ffprobe
                    ->format($filename); // extracts file information
            }

            return $data;

        } catch (Exception $e) {
            (new ImageController())->logs('getAudioInformation', $e);
            //      Log::error("getAudioInformation : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function getVideoDimension($filename)
    {
        try {

            /*if (env('APP_ENV') != 'local') {*/
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                //Log::info('APP_ENV',['APP_ENV'=>'production']);
                /* Linux */
                $ffprobe = FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries' => Config::get('constant.FFMPEG_PATH'),
                    'ffprobe.binaries' => Config::get('constant.FFPROBE_PATH')]);

                $data = $ffprobe
                    ->getFFProbe()
                    ->streams($filename)// extracts streams informations
                    ->videos()// filters video streams
                    ->first()// returns the first video stream
                    ->getDimensions();              // returns a FFMpeg\Coordinate\Dimension object
            } else {
                //Log::info('APP_ENV',['APP_ENV'=>'local']);
                /* Windows */
                $ffprobe = FFMpeg\FFProbe::create([
                    'ffmpeg.binaries' => Config::get('constant.FFMPEG_PATH'),
                    'ffprobe.binaries' => Config::get('constant.FFPROBE_PATH')]);

                $data = $ffprobe
                    ->streams($filename)// extracts streams informations
                    ->videos()// filters video streams
                    ->first()// returns the first video stream
                    ->getDimensions();              // returns a FFMpeg\Coordinate\Dimension object
            }
            //Log::info([$data]);
            return $data;

        } catch (Exception $e) {
            (new ImageController())->logs('getVideoDimension', $e);
            //      Log::error("getVideoDimension : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    // Save user uploaded Webp Original Image
    public function saveUserUploadedWebpOriginalImage($img)
    {
        try {
            $original_path = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY');
            $path = $original_path.$img;

            //convert image into .webp format
            $file_data = pathinfo(basename($path));
            $webp_name = $file_data['filename'];

            /*
                 *  -q Set image quality
                 *  -o Output file name
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_ORIGINAL_IMG_PATH').$webp_name.'.webp';
            //    $org_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_ORIGINAL_IMG_PATH') . $img;
            $org_path = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            $cmd = "$libwebp -q $quality $org_path -o $webp_path";
            if (Config::get('constant.APP_ENV') != 'local') {
                $result = (! shell_exec($cmd));
            } else {
                $result = (! exec($cmd));
            }

            return $webp_name.'.webp';
        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedWebpOriginalImage', $e);
            //      Log::error("saveUserUploadedWebpOriginalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return '';
        }
    }

    // Save Thumbnail Image
    public function saveUserUploadedWebpThumbnailImage($professional_img)
    {
        try {
            $original_path = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$professional_img;
            $array = $this->getThumbnailWidthHeight($original_path);
            $width = $array['width'];
            $height = $array['height'];

            $file_data = pathinfo(basename($original_path));
            //convert image into .webp format
            $webp_name = $file_data['filename'];
            $image_size = getimagesize($original_path);
            $width_orig = ($image_size[0] * 50) / 100;
            $height_orig = ($image_size[1] * 50) / 100;

            /*
             *  -q Set image quality
             *  -o Output file name
             *  -resize  Resize the image
             */

            $webp_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_WEBP_THUMBNAIL_IMG_PATH').$webp_name.'.webp';
            //      $org_path = Config::get('constant.IMAGE_BUCKET_USER_UPLOADED_ORIGINAL_IMG_PATH') . $professional_img;
            $org_path = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$professional_img;
            $quality = Config::get('constant.QUALITY');
            $libwebp = Config::get('constant.PATH_OF_CWEBP');

            if ($width_orig < 200 or $height_orig < 200) {

                $cmd = "$libwebp -q $quality $org_path -resize $width $height -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }

                return ['height' => $height, 'width' => $width];
            } else {

                $cmd = "$libwebp -q $quality $org_path -resize $width_orig $height_orig -o $webp_path";
                if (Config::get('constant.APP_ENV') != 'local') {
                    //For Linux
                    $result = (! shell_exec($cmd));
                } else {
                    // For windows
                    $result = (! exec($cmd));
                }

                return ['height' => $height_orig, 'width' => $width_orig];
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedWebpThumbnailImage', $e);
            //      Log::error("saveUserUploadedWebpThumbnailImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $dest1 = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$professional_img;
            $dest2 = '../..'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$professional_img;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }

            return '';
        }
    }

    // Save Webp Image InTo S3
    //  public function saveUserUploadedWebpImageInToS3($image)
    //  {
    //    try {
    //      //$base_url = (new ImageController())->getBaseUrl();
    //
    //      $original_sourceFile = '../..' . Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY') . $image;
    //      $thumbnail_sourceFile = '../..' . Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY') . $image;
    //
    //      $aws_bucket = Config::get('constant.AWS_BUCKET');
    //      $disk = Storage::disk('s3');
    //      if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
    //        //if (fopen($original_sourceFile, "r")) {
    //
    //        $original_targetFile = "$aws_bucket/user_uploaded_webp_original/" . $image;
    //        $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
    //
    //        $this->unlinkFileFromLocalStorage($image, Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY'));
    //
    //      }
    //      if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
    //        //if (fopen($thumbnail_sourceFile, "r")) {
    //
    //        $thumbnail_targetFile = "$aws_bucket/user_uploaded_webp_thumbnail/" . $image;
    //        $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');
    //
    //        $this->unlinkFileFromLocalStorage($image, Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY'));
    //
    //      }
    //
    //    } catch (Exception $e) {
    //      Log::error("saveUserUploadedWebpImageInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    //    }
    //  }
    // Save User Uploaded video InTo S3
    public function saveUserUploadedVideoInToS3($video, $thum_video_file_name)
    {
        try {

            $video_sourceFile = '../..'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY').$video;
            $original_sourceFile = '../..'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY').$thum_video_file_name;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/user_uploaded_video/".$video;
                $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $thumbnail_targetFile = $aws_bucket.'/user_uploaded_video_thumbnail/'.$thum_video_file_name;
                $disk->put($thumbnail_targetFile, file_get_contents($original_sourceFile), 'public');

                //delete file from local storage
                unlink($original_sourceFile);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedVideoInToS3', $e);
            //      Log::error("saveUserUploadedVideoInToS3 : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);

        }
    }

    //saveJsonPreviewVideoInToS3
    public function saveJsonPreviewVideoInToS3($video, $is_location)
    {
        //    1=from Job,2=from controller
        try {
            if ($is_location == 1) {
                $video_sourceFile = './..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$video;
            } else {
                $video_sourceFile = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$video;
            }

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($video, 'video');
            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = $aws_bucket.'/video/'.$video;
                $disk->put(
                    $original_targetFile,
                    file_get_contents($video_sourceFile),
                    [
                        'CacheControl' => Config::get('constant.MAX_AGE'),
                        'visibility' => 'public',
                    ]
                );
                //        $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

            /*$this->unlinkFileFromLocalStorage($video, $video_dir);*/

        } catch (Exception $e) {
            (new ImageController())->logs('saveJsonPreviewVideoInToS3', $e);
            //      Log::error("saveJsonPreviewVideoInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //save user generate video in to s3 bucket
    public function saveOutputVideoInToS3($video, $is_from_job)
    {
        //    1=from Job,2=from controller
        try {
            if ($is_from_job == 1) {
                $video_sourceFile = './..'.Config::get('constant.TEMP_DIRECTORY').$video;
            } else {
                $video_sourceFile = '../..'.Config::get('constant.TEMP_DIRECTORY').$video;
            }

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = $aws_bucket.'/temp/'.$video;
                $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveOutputVideoInToS3', $e);
            //      Log::error("saveOutputVideoInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //saveJsonPreviewVideoInToS3
    public function saveThumbnailVideoInToS3($video, $is_from_job = 0)
    {
        //    1=from Job,2=from controller
        try {
            if ($is_from_job == 1) {
                $video_sourceFile = './..'.Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY').$video;
            } else {
                $video_sourceFile = '../..'.Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY').$video;
            }

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($video, 'thumbnail_video');
            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = $aws_bucket.'/thumbnail_video/'.$video;
                $disk->put(
                    $original_targetFile,
                    file_get_contents($video_sourceFile),
                    [
                        'CacheControl' => Config::get('constant.MAX_AGE'),
                        'visibility' => 'public',
                    ]
                );
                //        $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

            /*$this->unlinkFileFromLocalStorage($video, $video_dir);*/

        } catch (Exception $e) {
            (new ImageController())->logs('saveThumbnailVideoInToS3', $e);
            //      Log::error("saveThumbnailVideoInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Delete Video In Job
    public function deleteVideoFromJob($video_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($video_name, 'video');

            } else {
                $file_path = './..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$video_name;
                $this->unlinkLocalStorageFileFromFilePath($file_path);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteVideoFromJob', $e);
            //      Log::error("deleteVideoFromJob : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete video from job.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    public function unlinkLocalStorageFileFromFilePath($file_path)
    {
        try {
            if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                unlink($file_path);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('unlinkLocalStorageFileFromFilePath', $e);
            //      Log::error("unlinkLocalStorageFileFromFilePath : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Delete user Uploaded video
    public function deleteUserUploadedVideo($file_name, $thumbnail_image, $web_image)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($file_name, 'user_uploaded_video');
                $this->deleteObjectFromS3($thumbnail_image, 'user_uploaded_video_thumbnail');
                $this->deleteObjectFromS3($web_image, 'user_uploaded_webp_original');
                $this->deleteObjectFromS3($web_image, 'user_uploaded_webp_thumbnail');

            } else {
                $this->unlinkFileFromLocalStorage($file_name, Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($thumbnail_image, Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($web_image, Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY'));
                $this->unlinkFileFromLocalStorage($web_image, Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY'));
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUserUploadedVideo', $e);
            //      Log::error("deleteUserUploadedVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //verify image
    public function verifyTransparentImage($image_array)
    {

        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();
        //    Log::info("Image Size",[$image_size]);
        $MAXIMUM_FILESIZE = 2 * 1024 * 1024;

        if (! ($image_type == 'image/png' || $image_type == 'image/jpeg')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select PNG or JPEG file.', 'cause' => '', 'data' => json_decode('{}')]);
        } elseif ($image_size > $MAXIMUM_FILESIZE) {
            $response = Response::json(['code' => 201, 'message' => 'File size is greater then 2MB.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //Trim audio
    public function trimAudioByJob($audio_path, $trim_audio_name, $start_time, $offset_time, $download_id = '')
    {
        try {
            $output = '';
            $result = '';
            $trim_audio_path = './..'.Config::get('constant.INPUT_AUDIO_DIRECTORY');
            $trim_audio = $trim_audio_path.$trim_audio_name;
            if (file_get_contents($audio_path)) {
                $ffmpeg = Config::get('constant.FFMPEG_PATH');
                $cmd = "$ffmpeg -i $audio_path -ss $start_time -t $offset_time -y $trim_audio 2>&1";
                //        Log::info($cmd);
                exec($cmd, $output, $result);
            }
            if (file_exists($trim_audio) && $result == 0) {
                //        if (file_exists($audio_path)) {
                //          Log::info('audio_unlink', [$audio_path]);
                //          unlink($audio_path);
                //        }
                $response = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.INPUT_AUDIO_DIRECTORY').$trim_audio_name;
                //Log::info('trimAudio response', [$response]);
            } else {
                Log::error('audio_trim_output', [$output]);
                $fail_reason = json_encode(['download_id' => $download_id, 'audio_trim_output' => $output]);
                (new userController())->addVideoGenerateHistory($fail_reason, null, $download_id, null, null, null, 2);
                $response = '';
            }

        } catch (Exception $e) {
            (new ImageController())->logs('trimAudioByJob', $e);
            //      Log::error("trimAudioByJob : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $fail_reason = json_encode(['Exception' => $e->getMessage()]);
            (new userController())->addVideoGenerateHistory($fail_reason, null, $download_id, null, null, null, 2);
            $response = '';
        }

        return $response;
    }

    // Save original Audio
    public function saveOriginalAudio($audio, $array_of_audio)
    {
        try {
            $original_path = '../..'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY');
            $array_of_audio->move($original_path, $audio);
        } catch (Exception $e) {
            (new ImageController())->logs('saveOriginalAudio', $e);
            //      Log::error("saveOriginalAudio : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function saveAudioInToS3Bucket($audio)
    {
        try {
            $audio_dir = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY');

            $audio_sourceFile = '../..'.$audio_dir.$audio;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($audio_sourceFile)) != 0)) {

                $original_targetFile = $aws_bucket.'/audio/'.$audio;
                $disk->put($original_targetFile, file_get_contents($audio_sourceFile), 'public');

                //delete file from local storage
                unlink($audio_sourceFile);

            }

            /*$this->unlinkFileFromLocalStorage($audio, $audio_dir);*/

        } catch (Exception $e) {
            (new ImageController())->logs('saveAudioInToS3Bucket', $e);
            //      Log::error("saveAudioInToS3Bucket : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Delete audio from Directory
    public function deleteAudio($audio_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($audio_name, 'audio');

            } else {

                $this->unlinkFileFromLocalStorage($audio_name, Config::get('constant.ORIGINAL_AUDIO_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteVideo', $e);
            //      Log::error("deleteVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    // Save User Uploaded audio
    public function saveUserUploadedAudio($audio, $audio_array)
    {
        try {
            $original_path = '../..'.Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY');
            $audio_array->move($original_path, $audio);
        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedAudio', $e);
            //      Log::error("saveUserUploadedAudio : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save user uploaded audio into s3
    public function saveUserUploadedAudioInToS3($audio)
    {
        try {

            $original_sourceFile = '../..'.Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY').$audio;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $original_targetFile = "$aws_bucket/user_uploaded_audio/".$audio;
                $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
            }

            $this->unlinkFileFromLocalStorage($audio, Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('saveUserUploadedAudioInToS3', $e);
            //      Log::error("saveUserUploadedAudioInToS3 : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);

        }
    }

    //Delete user uploaded audio from Directory
    public function deleteUserUploadedAudio($audio_name)
    {
        try {

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $this->deleteObjectFromS3($audio_name, 'user_uploaded_audio');

            } else {
                $this->unlinkFileFromLocalStorage($audio_name, Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY'));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUserUploadedAudio', $e);
            //      Log::error("deleteUserUploadedAudio : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }

    //Save normal video into S3_Bucket
    public function saveVideoInToS3Bucket($video, $image)
    {
        try {

            $video_dir = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
            $thumbnail_dir = Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY');
            $original_dir = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');

            $video_sourceFile = '../..'.$video_dir.$video;
            $thumbnail_sourceFile = '../..'.$thumbnail_dir.$image;
            $original_sourceFile = '../..'.$original_dir.$image;

            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ($this->checkFileExist($video_sourceFile)) != 0)) {
                $original_targetFile = $aws_bucket.'/video/'.$video;
                $disk->put($original_targetFile, file_get_contents($video_sourceFile), 'public');

                //delete file from local storage
                unlink($video_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($thumbnail_sourceFile)) != 0)) {
                $thumbnail_targetFile = $aws_bucket.'/thumbnail/'.$image;
                $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                //delete file from local storage
                unlink($thumbnail_sourceFile);
            }

            if (($is_exist = ($this->checkFileExist($original_sourceFile)) != 0)) {
                $thumbnail_targetFile = $aws_bucket.'/original/'.$image;
                $disk->put($thumbnail_targetFile, file_get_contents($original_sourceFile), 'public');

                //delete file from local storage
                unlink($original_sourceFile);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveVideoInToS3Bucket', $e);
            //      Log::error("saveVideoInToS3Bucket : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Verify Font json File
    public function verifyFontJsonFile($file_array)
    {
        $file_type = $file_array->getMimeType();
        //    Log::info("verifyFontJsonFile : ", ['type' => $file_type]);

        if (! ($file_type == 'application/json' || $file_type == 'text/plain' || $file_type == 'application/octet-stream')) {
            $response = Response::json(['code' => 201, 'message' => 'Please select json file.', 'cause' => '', 'data' => json_decode('{}')]);
        } else {
            $response = '';
        }

        return $response;
    }

    //generateFontJsonFileName
    public function generateFontJsonFileName($filename)
    {

        //    $path ='../..'. Config::get('constant.FONT_FILE_DIRECTORY') . $filename;
        //    if (File::exists($path)) {
        $parts = explode('.', $filename);
        $new_file_name = $parts[0].'.'.'json';

        return $new_file_name;
        //    }else{
        //      $new_file_name ='';
        //      return $new_file_name;
        //    }
    }

    // Save json Fonts Uploaded by Admin
    public function saveFontJsonFile($file_name)
    {
        try {
            $destination_path = '../..'.Config::get('constant.FONT_JSON_FILE_DIRECTORY');
            $font_json_sourceFile = $destination_path.$file_name;
            if (($is_exist = ($this->checkFileExist($font_json_sourceFile)) != 0)) {
                //delete file from local storage
                unlink($font_json_sourceFile);
            }
            Input::file('font_json_file')->move($destination_path, $file_name);

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontJsonFile', $e);
            //      Log::error("saveFontJsonFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    // Save Font json file InTo S3
    public function saveFontJsonFileInToS3($font_json_file)
    {
        try {
            $original_source_file = '../..'.Config::get('constant.FONT_JSON_FILE_DIRECTORY').$font_json_file;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $this->deleteObjectFromS3($font_json_file, 'font_json');

            if (($is_exist = ($this->checkFileExist($original_source_file)) != 0)) {

                $original_targetFile = "$aws_bucket/font_json/".$font_json_file;
                $disk->put($original_targetFile, file_get_contents($original_source_file), 'public');

                unlink($original_source_file);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveFontJsonFileInToS3', $e);
            //      Log::error("saveFontJsonFileInToS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //For check file exist in s3 bucket
    public function checkFileExistInS3($folder_name, $file_name)
    {
        try {
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $value = "$aws_bucket/$folder_name/$file_name";
            if ($disk->exists($value)) {
                $response = 1;
            } else {
                $response = 0;
            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkFileExistInS3', $e);
            //      Log::debug("checkFileExistInS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = 0;
        }

        return $response;
    }

    //Check is audio exist
    public function checkIsAudioExist($audio_name)
    {
        try {
            $exist_files_array = [];
            $file = $audio_name;

            $file_url = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').$file;
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/audio/".$file;
                if ($disk->exists($value)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            } else {
                $file_path = '../..'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY').$file;
                if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            }
            if (count($exist_files_array) > 0) {
                return $response = '';

            } else {
                return $response = Response::json(['code' => 434, 'message' => 'File does not exist. File name : '.$audio_name, 'cause' => '', 'data' => json_decode('{}')]);

            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkIsAudioExist', $e);
            //      Log::error("checkIsAudioExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    //Check is resource image exist
    public function checkIsResourceImageExist($image_name)
    {
        try {
            $exist_files_array = [];
            $file = $image_name;

            $file_url = Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$file;
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/resource/".$file;
                if ($disk->exists($value)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            } else {
                $file_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$file;
                if (($is_exist = ($this->checkFileExist($file_path)) != 0)) {
                    $exist_files_array = ['url' => $file_url, 'name' => $file];
                }
            }
            if (count($exist_files_array) > 0) {
                return $response = '';

            } else {
                return $response = Response::json(['code' => 434, 'message' => 'File does not exist. File name : '.$image_name, 'cause' => '', 'data' => json_decode('{}')]);

            }
        } catch (Exception $e) {
            (new ImageController())->logs('checkIsResourceImageExist', $e);
            //      Log::error("checkIsResourceImageExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    public function saveSingleFileInToLocal($file_name, $image_name, $folder_path)
    {

        $original_path = '../..'.$folder_path;
        Input::file($file_name)->move($original_path, $image_name);

        //use for Image Details
        //    $path = $original_path . $img;
        //    $this->saveImageDetails($path, 'original');
    }

    /**Download and store font in fonts folder*/
    public function downloadFont($font_name, $is_font_user_uploaded)
    {
        try {
            if ($is_font_user_uploaded) {
                $font_path = Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN').$font_name;
            } else {
                $font_path = Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN').$font_name;
            }

            $font_store_path = './..'.Config::get('constant.FONT_FILE_DIRECTORY').$font_name;
            set_time_limit(0);
            $fp = fopen($font_store_path, 'w+');
            $ch = curl_init($font_path);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            fclose($fp);
        } catch (Exception $e) {
            (new ImageController())->logs('downloadFont', $e);
            //      Log::error("downloadFont: ", ["\nerror_msg" => $e->getMessage(), "\ngetTraceAsString" => $e->getTraceAsString()]);
        }
    }

    /** Download audio */
    public function downloadAudio($audio_name, $new_audio_name)
    {
        try {
            $audio_path = 'https://videoadking.s3.us-east-2.amazonaws.com/videoadking/audio/'.$audio_name;
            $audio_store_path = '../..'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY').$new_audio_name;
            set_time_limit(0);
            $fp = fopen($audio_store_path, 'w+');
            $ch = curl_init($audio_path);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            fclose($fp);
        } catch (Exception $e) {
            (new ImageController())->logs('downloadAudio', $e);
            //      Log::error("downloadAudio: ", ["\nerror_msg" => $e->getMessage(), "\ngetTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function generateRowVideoHeightWidth($width_orig, $height_orig)
    {
        try {

            $ratio_orig = $width_orig / $height_orig;

            $width = $width_orig < Config::get('constant.ROW_VIDEO_THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.ROW_VIDEO_THUMBNAIL_WIDTH');
            $height = $height_orig < Config::get('constant.ROW_VIDEO_THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.ROW_VIDEO_THUMBNAIL_HEIGHT');

            if ($width / $height > $ratio_orig) {
                $width = (int) $height * $ratio_orig;
            }
            if ($width % 2 != 0) {
                $width = (int) $width + 1;
            } else {
                $height = (int) $width / $ratio_orig;
            }
            if ($height % 2 != 0) {
                $height = (int) $height + 1;
            }

            $array = ['width' => $width, 'height' => $height];

            return $array;
        } catch (Exception $e) {
            (new ImageController())->logs('generateRowVideoHeightWidth', $e);
            //      Log::error("generateRowVideoHeightWidth : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    /* generate random string(uuid)*/
    public function generateUUID()
    {
        try {
            $res = DB::SELECT('SELECT LOWER(CONCAT(LPAD(CONV(FLOOR(RAND()*POW(36,6)),10, 36), 6, 0),LEFT(UUID(),8))) AS uuid');
            if (count($res) >= 0) {
                $uuid = $res[0]->uuid;
            } else {
                $uuid = substr(base_convert(mt_rand() * pow(36, 6), 10, 36), 0, 6).substr(md5(uniqid()), 0, 8);
                Log::error('generateUUID : Uuid is not generated. ', ['php uuid' => $uuid]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('generateUUID', $e);
            //Log::error("generateUUID : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $uuid = substr(base_convert(mt_rand() * pow(36, 6), 10, 36), 0, 6).substr(md5(uniqid()), 0, 8);
        }

        return $uuid;
    }

    public function generateDownloadURL($video_name, $directory, $filename)
    {
        try {
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
                'Bucket' => $aws_bucket,
                'Key' => "$aws_bucket/$directory/$video_name",
                'ResponseContentDisposition' => 'attachment;filename="'.$filename.'"', //for download
            ]);

            $expire_time = '+'.Config::get('constant.DOWNLOAD_URL_EXPIRE_TIME').' hours';
            $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, $expire_time);

            return (string) $request->getUri();

        } catch (Exception $e) {
            (new ImageController())->logs('generateDownloadURL', $e);
            //      Log::debug("generateDownloadURL Exception :", ['Error : ' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return '';
        }
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir.'/'.$object)) {
                        $this->rrmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function removeEmoji($input_string)
    {

        $string = str_replace('?', '{%}', $input_string);
        $string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
        $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        $string = str_replace(['?', '? ', ' ?'], [''], $string);

        return trim(str_replace('{%}', '?', $string));
    }

    /** Convert utc datetime into country local datetime **/
    public function convertUTCDateTimeInToLocal($datetime, $country_code)
    {
        try {

            /** Get time zone by country code**/
            if ($country_code) {
                $timezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $country_code);
                $dt = new \DateTime(date('Y-m-d H:i:s', strtotime($datetime)));
                $dt->setTimeZone(new \DateTimeZone($timezone[0]));
                $datetime = $dt->format('M d, Y H:i:s T');
            }

            return $datetime;
        } catch (Exception $e) {
            (new ImageController())->logs('convertUTCDateTimeInToLocal', $e);
            //      Log::error("convertUTCDateTimeInToLocal : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $datetime;
        }
    }

    public function generateName($image_array, $image_type)
    {
        try {
            if (! empty($image_array)) {
                return uniqid().'_'.$image_type.'_'.time().'.'.strtolower($image_array->getClientOriginalExtension());
            }
        } catch (Exception $e) {
            Log::error('generateName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function saveFileByPath($image_array, $img, $original_path, $dir_name)
    {
        try {
            $image_array->move($original_path, $img);
            //$path = $original_path . $img;
            //$this->saveImageDetails($path, $dir_name);

        } catch (Exception $e) {
            Log::error('saveFileByPath : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function saveFileToS3ByPath($file_path, $file_name, $folder_name)
    {
        try {
            $aws_bucket = config('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($this->checkFileExist($file_path.$file_name)) != 0) {
                $resource_targetFile = "$aws_bucket/$folder_name/".$file_name;
                $disk->put($resource_targetFile, file_get_contents($file_path.$file_name));

                unlink($file_path.$file_name);

            } else {
                Log::info('saveFileToS3ByPath : file not exist ', [$file_path.$file_name]);
            }

        } catch (Exception $e) {
            Log::error('saveFileToS3ByPath : ', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function logs($routeName, $e)
    {
        Log::error("$routeName : ", ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        //$sentry = app('sentry');
        //app('log')->error('Possible send error: ' . $sentry->captureException($e));
        app('sentry')->captureException($e);
    }
}
