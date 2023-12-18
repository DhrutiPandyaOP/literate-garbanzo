<?php
/**
 * Created by Optimumbrew.
 * User: admin
 * Date: 20-Sep-18
 * Time: 1:46 PM
 */
return [

    ////////////////////////////////////////////////////////Change Server Configuration////////////////////////////////////////////////////////

    'SERVER_NAME' => env('SERVER_NAME'),
    'ACTIVATION_LINK_PATH' => env('ACTIVATION_LINK_PATH_PHOTOADKING'),

    'XMPP_HOST' => 'https://www.photoadking.com/', //live
    //'XMPP_HOST' => '192.168.0.113', //local

    'EXCEPTION_ERROR' => 'PhotoADKing is unable to ',
    'DATE_FORMAT' => 'Y-m-d H:i:s',

    /* For local server */
    'SUPER_ADMIN_EMAIL_ID' => env('SUPER_ADMIN_EMAIL_ID'),
    'ADMIN_EMAIL_ID' => env('ADMIN_EMAIL_ID'),
    'SUB_ADMIN_EMAIL_ID' => env('SUB_ADMIN_EMAIL_ID'),
    'CRM_EMAIL_ID' => env('CRM_EMAIL_ID'),
    'CRM_USER_ROLE_ID' => env('CRM_USER_ROLE_ID'),

    'IS_USER_SESSION_ANALYTICS_ENABLE' => env('IS_USER_SESSION_ANALYTICS_ENABLE'),

    'IS_MAIL_DEBUG_PROCESS_ENABLE' => env('IS_MAIL_DEBUG_PROCESS_ENABLE'),
    'MAIL_X_SES_CONFIGURATION_SET_HEADER' => env('MAIL_X_SES_CONFIGURATION_SET_HEADER'),

    'RESPONSE_HEADER_CACHE' => 'max-age=2592000',
    'ROLE_FOR_ADMIN' => 'admin',
    'ROLE_FOR_USER' => 'user',
    'ROLE_ID_FOR_USER' => 2,
    'OTP_EXPIRATION_TIME' => '60', //for live set 60 minutes
    'RESET_PASSWORD_LINK_EXPIRATION_TIME' => '60',
    'DOWNLOAD_URL_EXPIRE_TIME' => env('DOWNLOAD_URL_EXPIRE_TIME'),
    'MAX_AGE' => env('MAX_AGE'),

    'GUEST_USER_UD' => 'guest@gmail.com',
    'GUEST_PASSWORD' => 'demo@123',
    'PROJECT_NAME' => 'photoadking',

    'ADMIN_ID' => env('ADMIN_ID'),
    'SUB_ADMIN_ID' => env('SUB_ADMIN_ID'),

    'DEFAULT_RANDOM_COLOR_VALUE' => '#21353d',

    /* Path to get/store files from local storage */
    'DATABASE_BACKUP_DIRECTORY' => '/DB_updates/',
    'COMPRESSED_IMAGES_DIRECTORY' => '/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/thumbnail/',
    'RESOURCE_IMAGES_DIRECTORY' => '/image_bucket/resource/',
    'WEBP_ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/webp_original/',
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/webp_thumbnail/',
    '3D_OBJECTS_DIRECTORY' => '/image_bucket/3d_object/',
    'USER_UPLOAD_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_video_thumbnail/',
    'REPORT_ATTACHMENT_IMAGES_DIRECTORY' => '/image_bucket/report_attachment/',
    'MY_DESIGN_IMAGES_DIRECTORY' => '/image_bucket/my_design/',
    '3D_OBJECT_IMAGES_DIRECTORY' => '/image_bucket/object_images/',
    'SVG_IMAGES_DIRECTORY' => '/image_bucket/svg/',
    'STOCK_PHOTOS_IMAGES_DIRECTORY' => '/image_bucket/stock_photos/',
    'STOCK_VIDEOS_IMAGES_DIRECTORY' => '/image_bucket/stock_videos/',
    'EXTRA_IMAGES_DIRECTORY' => '/image_bucket/extra/',
    'USER_UPLOAD_FONTS_DIRECTORY' => '/image_bucket/user_uploaded_fonts/',
    'FONT_FILE_DIRECTORY' => '/image_bucket/fonts/',
    'FONT_PREVIEW_IMAGE_FILE_DIRECTORY' => '/image_bucket/font_preview_image/',
    'USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_original/',
    'USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_compressed/',
    'USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_thumbnail/',
    'USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_webp_original/',
    'USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/user_uploaded_webp_thumbnail/',
    'STATIC_PAGE_DIRECTORY' => '/templates/',
    'DESIGN_PAGE_DIRECTORY' => 'design',
    'INPUT_AUDIO_DIRECTORY' => '/image_bucket/input_audio/',
    /*Video path */
    'ORIGINAL_VIDEO_DIRECTORY' => '/image_bucket/video/',
    'THUMBNAIL_VIDEO_DIRECTORY' => '/image_bucket/thumbnail_video/',
    'TEMP_DIRECTORY' => '/image_bucket/temp/',
    'CHUNKS_DIRECTORY' => '/image_bucket/chunks/',
    'USER_UPLOAD_VIDEOS_DIRECTORY' => '/image_bucket/user_uploaded_video/',
    'USER_UPLOAD_AUDIOS_DIRECTORY' => '/image_bucket/user_uploaded_audio/',
    'TEMP_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING' => env('TEMP_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'ORIGINAL_AUDIO_DIRECTORY' => '/image_bucket/audio/',
    'WHATS_NEW_STATIC_PAGE_DIRECTORY' => '/whats-new/',
    'FONT_JSON_FILE_DIRECTORY' => '/image_bucket/font_json/',
    'JSON_FILE_DIRECTORY' => '/image_bucket/json/',

    /* Path to get files from cdn */
    'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    '3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN' => env('3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOAD_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOAD_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'REPORT_ATTACHMENT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('REPORT_ATTACHMENT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    '3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'STOCK_PHOTOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('STOCK_PHOTOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'STOCK_VIDEOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('STOCK_VIDEOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'EXTRA_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('EXTRA_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'THUMBNAIL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN' => env('THUMBNAIL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'USER_UPLOAD_AUDIOS_DIRECTORY_OF_DIGITAL_OCEAN' => env('USER_UPLOAD_AUDIOS_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN' => env('ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),
    'JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN' => env('JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING'),

    /* Path to process files from s3 */
    'JSON_FILE_DIRECTORY_OF_S3' => env('JSON_FILE_DIRECTORY_OF_S3_PHOTOADKING'),
    'MY_DESIGN_IMAGES_DIRECTORY_OF_S3' => env('MY_DESIGN_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),
    'THUMBNAIL_VIDEO_DIRECTORY_OF_S3' => env('THUMBNAIL_VIDEO_DIRECTORY_OF_S3_PHOTOADKING'),
    'ORIGINAL_VIDEO_DIRECTORY_OF_S3' => env('ORIGINAL_VIDEO_DIRECTORY_OF_S3_PHOTOADKING'),
    'USER_UPLOAD_AUDIOS_DIRECTORY_OF_S3' => env('USER_UPLOAD_AUDIOS_DIRECTORY_OF_S3_PHOTOADKING'),
    'ORIGINAL_AUDIO_DIRECTORY_OF_S3' => env('ORIGINAL_AUDIO_DIRECTORY_OF_S3_PHOTOADKING'),
    'USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_S3' => env('USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),
    'USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_S3' => env('USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),
    'RESOURCE_IMAGES_DIRECTORY_OF_S3' => env('RESOURCE_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),
    'ORIGINAL_IMAGES_DIRECTORY_OF_S3' => env('ORIGINAL_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),
    'STOCK_VIDEOS_IMAGES_DIRECTORY_OF_S3' => env('STOCK_VIDEOS_IMAGES_DIRECTORY_OF_S3_PHOTOADKING'),

    'THUMBNAIL_HEIGHT' => 240,
    'THUMBNAIL_WIDTH' => 320,
    'PREVIEW_VIDEO_THUMBNAIL_HEIGHT' => 280,
    'ROW_VIDEO_THUMBNAIL_WIDTH' => 720,
    'ROW_VIDEO_THUMBNAIL_HEIGHT' => 720,

    'PAGINATION_ITEM_LIMIT' => '15',
    'ITEM_COUNT_OF_SAMPLE_JSON' => '10',
    'DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT' => 20,
    'DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES' => 6, //set default sub_category_id to get recommended templates. use Instagram Post for this
    'DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES' => '37,41,39,44,125,128,109,127,141,142,143,144', //set default sub category ids to get featured templates. (Flyer, Business, Brochure, Invitation)
    //'DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES' => "37,41,39,44,137,111,138,135,146,147,148,149", //set default sub category ids to get featured templates. (Flyer, Business, Brochure, Invitation)
    'EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES' => 1440, //time to expire key of caching in minutes (1440 = 24 hours)
    'DEFAULT_ITEM_COUNT_TO_GET_FEATURED_TEMPLATES' => 5, //set default item_count to get featured templates in searching api.
    'SUB_CATEGORY_UUID_OF_BRANDKIT' => env('SUB_CATEGORY_UUID_OF_BRANDKIT'),

    'GCM_NOTIFICATION_URL' => 'https://fcm.googleapis.com/fcm/send',

    /* Test credential of FCM */
    /*'GCM_SERVER_KEY' => '',
  'GCM_SENDER_ID' => '',
  'GCM_TITLE_FOR_CATALOG' => '',*/

    /* Live credential of FCM */
    'GCM_SERVER_KEY' => '',
    'GCM_SENDER_ID' => '',
    'GCM_TITLE_FOR_CATALOG' => '',

    /* For local server */
    /*'receipt_validator_endpoint' => 'https://sandbox.itunes.apple.com/verifyReceipt',*/

    /* For live server */
    'receipt_validator_endpoint' => 'https://buy.itunes.apple.com/verifyReceipt',

    /* Quality of image compression */
    'QUALITY' => '75',

    /* Path to used in command */
    'IMAGE_BUCKET_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_WEBP_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH' => env('IMAGE_BUCKET_WEBP_THUMBNAIL_IMG_PATH'),
    'IMAGE_BUCKET_USER_UPLOADED_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_USER_UPLOADED_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_USER_UPLOADED_WEBP_ORIGINAL_IMG_PATH' => env('IMAGE_BUCKET_USER_UPLOADED_WEBP_ORIGINAL_IMG_PATH'),
    'IMAGE_BUCKET_USER_UPLOADED_WEBP_THUMBNAIL_IMG_PATH' => env('IMAGE_BUCKET_USER_UPLOADED_WEBP_THUMBNAIL_IMG_PATH'),
    'IMAGE_BUCKET_CHUNKS_PATH' => env('IMAGE_BUCKET_CHUNKS_PATH'),
    'PATH_OF_CWEBP' => env('PATH_OF_CWEBP'),
    'FFMPEG_PATH' => env('FFMPEG_PATH'),
    'FFPROBE_PATH' => env('FFPROBE_PATH'),

    'CONTENT_TYPE_OF_IMAGE' => 1,
    'CONTENT_TYPE_OF_VIDEO' => 2,
    'CONTENT_TYPE_OF_AUDIO' => 3,
    'CONTENT_TYPE_OF_CARD_JSON' => 4,
    'CONTENT_TYPE_OF_TEXT_JSON' => 5,
    'CONTENT_TYPE_OF_3D_TEXT_JSON' => 6,
    'CONTENT_TYPE_OF_3D_SHAPE' => 7,
    'CONTENT_TYPE_OF_SVG' => 8,
    'CONTENT_TYPE_OF_VIDEO_JSON' => 9,
    'CONTENT_TYPE_OF_INTRO_VIDEO' => 10,

    /* Subscription type */
    'ROLE_ID_FOR_FREE_USER' => 2,
    'ROLE_ID_FOR_MONTHLY_STARTER' => 3,
    'ROLE_ID_FOR_YEARLY_STARTER' => 4,
    'ROLE_ID_FOR_MONTHLY_PRO' => 5,
    'ROLE_ID_FOR_YEARLY_PRO' => 6,
    'ROLE_ID_FOR_PREMIUM_USER' => 7,
    'ROLE_ID_FOR_LIFETIME_PRO' => 11,

    /* Static categories */
    'CATEGORY_ID_OF_TEMPLATES' => 4,

    /* Api key of pixabay*/
    'PIXABAY_API_KEY' => '9366777-8d4a537113175da05770fd05e,9420810-0fe767598d152bba00a385ade,9420912-5544c439271da9cb408f85869',  //old api keys
    //'PIXABAY_API_KEY' => '9366777-b8aa7aa82a3b295e4ab1ad737,9420810-69c0a4c7c6e146093bd975f5f,9420912-b7634d7bd0d3ff36d18a644b0',
    'PIXABAY_API_URL' => 'https://pixabay.com/api/',

    /* api key to run google API like: translate,detect language */
    'GOOGLE_API_KEY' => env('GOOGLE_API_KEY'),

    /* Local url */
    'RESET_PASSWORD_LINK_REDIRECT_URL' => env('ACTIVATION_LINK_PATH_PHOTOADKING').'/app/#/reset-password',
    'USER_VERIFICATION_LINK_URL' => env('ACTIVATION_LINK_PATH_PHOTOADKING').'/app/#/login',

    'USER_CUSTOM_TEMPLATE_URL' => env('ACTIVATION_LINK_PATH_PHOTOADKING').'/app/#/editor',

    'CDN_STATIC_WEB_ASSETS_PATH' => 'https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/web',
    'CDN_STATIC_EMAIL_ASSETS_PATH' => 'https://d3jmn01ri1fzgl.cloudfront.net/photoadking/static_assets/email',

    'MONTHLY_STARTER' => 1,
    'YEARLY_STARTER' => 2,
    'MONTHLY_PRO' => 3,
    'YEARLY_PRO' => 4,
    'LIFETIME_PRO' => 9,
    /*1 United States Dollar equals
  71.64 Indian Rupee*/
    'INDIAN_MONTHLY_STARTER' => 5,
    'INDIAN_YEARLY_STARTER' => 6,
    'INDIAN_MONTHLY_PRO' => 7,
    'INDIAN_YEARLY_PRO' => 8,

    /* Intro out video option*/
    'NORMAL_VIDEO' => 1,
    'FULL_HD_VIDEO' => 2,

    /* PayPal credential */
    'PAYPAL_API_URL' => env('PAYPAL_API_URL'),
    'PAYPAL_API_USER' => env('PAYPAL_API_USER'),
    'PAYPAL_API_PASSWORD' => env('PAYPAL_API_PASSWORD'),
    'PAYPAL_API_SIGNATURE' => env('PAYPAL_API_SIGNATURE'),
    'USE_SANDBOX' => env('USE_SANDBOX'),

    /* FastSpring Payment Module */
    'FASTSPRING_API_URL' => env('FASTSPRING_API_URL'),
    'FASTSPRING_USER_ACCOUNT_URL' => env('FASTSPRING_USER_ACCOUNT_URL'),
    'FASTSPRING_API_USER_NAME' => env('FASTSPRING_API_USER_NAME'),
    'FASTSPRING_API_PASSWORD' => env('FASTSPRING_API_PASSWORD'),
    'FASTSPRING_SUBSCRIPTIONS_API_NAME' => env('FASTSPRING_SUBSCRIPTIONS_API_NAME'),
    'FASTSPRING_ORDERS_API_NAME' => env('FASTSPRING_ORDERS_API_NAME'),
    'FASTSPRING_ACCOUNTS_API_NAME' => env('FASTSPRING_ACCOUNTS_API_NAME'),
    'FASTSPRING_WEBHOOK_AUTH_KEY' => env('FASTSPRING_WEBHOOK_AUTH_KEY'),

    'PAYMENT_TYPE_OF_PAYPAL' => 1,
    'PAYMENT_TYPE_OF_STRIPE' => 2,
    'PAYMENT_TYPE_OF_FASTSPRING' => 3,

    'PRODUCT_ID_OF_MONTHLY_STARTER_DISCOUNTED' => 'monthly-starter-discounted',
    'PRODUCT_ID_OF_MONTHLY_STARTER_DISCOUNTED_STAGING' => 'monthly-starter-discounted-staging',
    'PRODUCT_ID_OF_YEARLY_STARTER_DISCOUNTED' => 'yearly-starter-discounted',
    'PRODUCT_ID_OF_YEARLY_STARTER_DISCOUNTED_STAGING' => 'yearly-starter-discounted-staging',
    'PRODUCT_ID_OF_MONTHLY_PRO_DISCOUNTED' => 'monthly-pro-discounted',
    'PRODUCT_ID_OF_MONTHLY_PRO_DISCOUNTED_STAGING' => 'monthly-pro-discounted-staging',
    'PRODUCT_ID_OF_YEARLY_PRO_DISCOUNTED' => 'yearly-pro-discounted',
    'PRODUCT_ID_OF_YEARLY_PRO_DISCOUNTED_STAGING' => 'yearly-pro-discounted-staging',
    'DEFAULT_PRODUCT_ID_DISCOUNTED' => 'photoadking-pro-discounted',

    'PRODUCT_ID_OF_MONTHLY_STARTER' => 'monthly-starter',
    'PRODUCT_ID_OF_YEARLY_STARTER' => 'yearly-starter',
    'PRODUCT_ID_OF_MONTHLY_PRO' => 'monthly-pro',
    'PRODUCT_ID_OF_YEARLY_PRO' => 'yearly-pro',
    'PRODUCT_ID_OF_LIFETIME_PRO' => 'life-time-pro',
    'DEFAULT_PRODUCT_ID' => 'photoadking-pro',

    'MY_DESIGN_COUNT_FOR_FREE_USER' => 10, //10 cards for lifetime
    'MY_DESIGN_COUNT_FOR_MONTHLY_STARTER' => 30, //30 cards per month
    'MY_DESIGN_COUNT_FOR_YEARLY_STARTER' => 30, //30 cards per month
    'MY_DESIGN_COUNT_FOR_MONTHLY_PRO' => 'unlimited',
    'MY_DESIGN_COUNT_FOR_YEARLY_PRO' => 'unlimited',
    'MY_DESIGN_COUNT_FOR_PREMIUM_USER' => 1000, //1000 cards per month

    /* For video design */
    'MY_VIDEO_DESIGN_COUNT_FOR_FREE_USER' => 3, //3 cards for lifetime
    'MY_VIDEO_DESIGN_COUNT_FOR_MONTHLY_STARTER' => 10, //10 cards per month
    'MY_VIDEO_DESIGN_COUNT_FOR_YEARLY_STARTER' => 10, //10 cards per month
    'MY_VIDEO_DESIGN_COUNT_FOR_MONTHLY_PRO' => 'unlimited',
    'MY_VIDEO_DESIGN_COUNT_FOR_YEARLY_PRO' => 'unlimited',
    'MY_VIDEO_DESIGN_COUNT_FOR_PREMIUM_USER' => 1000, //100 cards per month

    'UPLOAD_IMAGE_COUNT_FOR_FREE_USER' => 10, //10 images for lifetime
    'UPLOAD_IMAGE_COUNT_FOR_MONTHLY_STARTER' => 'unlimited',
    'UPLOAD_IMAGE_COUNT_FOR_YEARLY_STARTER' => 'unlimited',
    'UPLOAD_IMAGE_COUNT_FOR_MONTHLY_PRO' => 'unlimited',
    'UPLOAD_IMAGE_COUNT_FOR_YEARLY_PRO' => 'unlimited',
    'UPLOAD_IMAGE_COUNT_FOR_PREMIUM_USER' => 'unlimited',

    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_FREE_USER' => 25, //25MB
    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_STARTER' => 1024, //1024MB
    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_STARTER' => 1024, //1024MB
    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_PRO' => 10240, //10240MB
    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_PRO' => 10240, //10240MB
    'UPLOAD_IMAGE_SIZE_LIMIT_FOR_PREMIUM_USER' => 1024, //1024MB

    'UPLOAD_FONT_COUNT_FOR_FREE_USER' => 0,
    'UPLOAD_FONT_COUNT_FOR_MONTHLY_STARTER' => 5,
    'UPLOAD_FONT_COUNT_FOR_YEARLY_STARTER' => 5,
    'UPLOAD_FONT_COUNT_FOR_MONTHLY_PRO' => 'unlimited',
    'UPLOAD_FONT_COUNT_FOR_YEARLY_PRO' => 'unlimited',
    'UPLOAD_FONT_COUNT_FOR_PREMIUM_USER' => 'unlimited',

    /* Api key to detect tag from image */
    'CLARIFAI_API_KEY' => env('CLARIFAI_API_KEY'),

    /* Mailchimp api credential */
    'MAILCHIMP_API_KEY' => '2b21a8e2da5cbc3356c0e7c77fc35724-us19',
    'MAILCHIMP_LIST_ID' => 'a658037610',
    'MAILCHIMP_API_URL' => 'https://us19.api.mailchimp.com/3.0/lists/',

    // Env variables
    'STORAGE' => env('STORAGE'),
    'APP_ENV' => env('APP_ENV'),
    'AWS_BUCKET' => env('AWS_BUCKET'),
    'AWS_REGION' => env('AWS_REGION'),
    'AWS_BUCKET_LINK_PATH_PHOTOADKING' => env('AWS_BUCKET_LINK_PATH_PHOTOADKING'),
    'AWS_KEY' => env('AWS_KEY'),
    'AWS_SECRET' => env('AWS_SECRET'),
    'SES_KEY' => env('SES_KEY'),
    'SES_SECRET' => env('SES_SECRET'),
    'CDN_DISTRIBUTION_ID' => env('CDN_DISTRIBUTION_ID'),

    'APP_HOST_NAME' => env('APP_HOST_NAME'),

    /* Static page */
    'DEFAULT_ITEM_COUNT_TO_STATIC_PAGE_SUB_CATEGORY_LIST' => 20,
    'LIVE_SERVER_NAME' => 'photoadking.com',

    /* Calender Schedule Module*/
    'MAX_LENGTH_FOR_POST_SUGGESTION_TITLE' => 250,
    'MAX_LENGTH_FOR_POST_SUGGESTION_NAME' => 250,
    'DEFAULT_ITEM_COUNT_TO_POST_SUGGESTION_TEMPLATE_LIST' => 20,
    'DEFAULT_ITEM_COUNT_TO_ADD_EVENT_TO_SCHEDULE' => 5,
    'NO_OF_DAYS_TO_SHOW_EVENT_TO_USER' => 6, //total item and day

    /* Non-commercial fonts */
    'OFFLINE_CATALOG_IDS_OF_FONT' => env('OFFLINE_CATALOG_IDS_OF_FONT'), //Misc catalog Id for old fonts(non-commercial)

    'MAXIMUM_FILE_SIZE_OF_SAMPLE_IMAGE' => 200,

    /*radis key parameter*/
    'REDIS_KEY' => env('REDIS_KEY'),

    //prefix issue api
    'REDIS_PREFIX' => env('REDIS_PREFIX'),

    'QUEUE_VIDEO_LIMIT' => 6,
    'QUEUE_IMAGE_LIMIT' => 10,

    /*Content type for My design */
    'IMAGE' => 1,
    'VIDEO' => 2,
    'INTRO_VIDEO' => 3,

    /*Stripe configurations*/
    'STRIPE_API_KEY' => env('STRIPE_API_KEY'),
    'STRIPE_API_VERSION' => env('STRIPE_API_VERSION'),

    /*Stripe webhook events configurations*/
    'PAYMENT_WEBHOOK_SECRET_KEY' => env('PAYMENT_WEBHOOK_SECRET_KEY'),
    'UPDATE_PAYMENT_WEBHOOK_SECRET_KEY' => env('UPDATE_PAYMENT_WEBHOOK_SECRET_KEY'),
    'CUSTOMER_WEBHOOK_SECRET_KEY' => env('CUSTOMER_WEBHOOK_SECRET_KEY'),
    'INVOICE_WEBHOOK_SECRET_KEY' => env('INVOICE_WEBHOOK_SECRET_KEY'),

    /*List out all discount coupons of stripe payment gateway*/
    'PHOTOADKING_APPLIED_DISCOUNT' => 2,
    /* 0 = No discount
     1 = 50% discount for one time(1 is Stripe Dashboard coupon unique ID)
     2 = 50% discount for forever(1 is Stripe Dashboard coupon unique ID)
  */

    /*Feedback is sent to the following address*/
    'PHOTOADKING_FEEDBACK_SENT_TO_ADDRESS' => 'optimumbrewcominfo@helpphotoadking.freshdesk.com',
    'HOW_TO_UPDATE_PAYMENT_METHOD_URL' => 'https://helpphotoadking.freshdesk.com/support/solutions/articles/43000671678-how-to-update-payment-method',
    'PAYMENT_METHOD_MESSAGE' => 'We need you to <b>update your billing information</b> with your state and zip code.',

    'ANIM_ZOOM_IN' => 'Zoom In',
    'ANIM_ZOOM_OUT' => 'Zoom Out',
    'ANIM_RIGHT_TO_LEFT' => 'Right To Left',
    'ANIM_LEFT_TO_RIGHT' => 'Left To Right',
    'ANIM_TOP_TO_BOTTOM' => 'Top To Bottom',
    'ANIM_BOTTOM_TO_TOP' => 'Bottom To Top',
    'ANIM_BLINK' => 'Blink',
    'ANIM_NONE' => 'None',
    'ANIM_FAD_IN' => 'Fad In',
    'Zoom_In_H_W' => '3',
    'Zoom_Out_H_W' => '2.5f',
    'Others_H_W' => '2',
    'WATERMARK_LOGO' => env('ACTIVATION_LINK_PATH_PHOTOADKING').'/app/assets/images/king_watermark1.png',

    /*If an MS or YS is purchased "after this date" the user will not be able to create a folder, chart, 4 page multi-page design.*/
    'DATE_OF_NEW_RULES' => env('DATE_OF_NEW_RULES'),

    /* BackEnd Logs credential */
    'LOG_USERNAME' => env('LOG_USERNAME'),
    'LOG_PASSWORD' => env('LOG_PASSWORD'),

    //For mail status subject match
    'VERIFICATION_MAIL_SUBJECT' => 'PhotoADKing: Verification Link',
    'ACCOUNT_ACTIVATION_MAIL_SUBJECT' => 'PhotoADKing: Account Activation',

    // For API Caching
    'CACHE_TIME_1_HOUR' => '60',
    'CACHE_TIME_6_HOUR' => '360',
    'CACHE_TIME_24_HOUR' => '1440',
    'CACHE_TIME_48_HOUR' => '2880',
    'CACHE_TIME_7_DAYS' => '10080',

    //Expiration time of 2fa cookie for 30 days
    'EXPIRATION_TIME_OF_2FA_COOKIE' => env('EXPIRATION_TIME_OF_2FA_COOKIE'),

    'INACTIVITY_DAYS_FOR_WARNING_MAIL' => env('INACTIVITY_DAYS_FOR_WARNING_MAIL'),
    'DATABASE_TABLES' => 'audio_master catalog_master category content_master design_page_json_master font_master preview_video_jobs similar_template_page static_page_master static_page_sub_category_master sub_category_master sub_category_catalog',

    'TEST_AWS_BUCKET' => env('TEST_AWS_BUCKET'),
    'TEST_AWS_KEY' => env('TEST_AWS_KEY'),
    'TEST_AWS_SECRET' => env('TEST_AWS_SECRET'),
    'TEST_AWS_REGION' => env('TEST_AWS_REGION'),

    'API_KEY' => '6a2b65c9-d0ca-40c2-99ae-817c53f1496f',

    //image export
    'IS_RENDER_SERVER_WORKING' => env('IS_RENDER_SERVER_WORKING'),
    'WEBHOOK_IP_ADDRESS' => env('WEBHOOK_IP_ADDRESS'),
    'NODE_API_URL_IP' => env('NODE_API_URL_IP'),
    'QUEUE_AGE_LIMIT' => env('QUEUE_AGE_LIMIT'),
    'AWS_FOLDER_FOR_OUTPUT_FILE_EXPORT' => env('AWS_FOLDER_FOR_OUTPUT_FILE_EXPORT'),

    'ALLOWED_IPS' => env('ALLOWED_IPS'),

    'OPENAI_API_KEY' => env('OPENAI_API_KEY'),

];
