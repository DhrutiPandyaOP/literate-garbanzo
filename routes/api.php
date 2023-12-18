<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Logs Viewer
Route::get('logs/{user_name}/{password}', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->where(['user_name' => Config::get('constant.LOG_USERNAME'), 'password' => Config::get('constant.LOG_PASSWORD')]);

//User login
Route::post('doLoginForContentUploader_v2', 'LoginController@doLoginForContentUploader_v2');

//Login for user/admin
Route::post('doLoginForUser', 'LoginController@doLoginForUser');
Route::post('doLoginForAdmin', 'LoginController@doLoginForAdmin');
Route::post('verify2faOTP', 'Google2faController@verify2faOTP');

//forgot Password for User
Route::post('forgotPassword', 'LoginController@forgotPassword');
Route::post('verifyResetPasswordLink', 'LoginController@verifyResetPasswordLink');
Route::post('resetPassword', 'LoginController@resetPassword');

//User SignUP
Route::post('checkSocialUserExist', 'LoginController@checkSocialUserExist');
Route::post('doLoginForSocialUser', 'LoginController@doLoginForSocialUser');
Route::post('userSignUp', 'RegisterController@userSignUp');
Route::post('resendVerificationLink', 'RegisterController@resendVerificationLink');
Route::post('verifyUser', 'RegisterController@verifyUser');

//Without token
Route::post('getStaticPageTemplateListById', 'StaticPageController@getStaticPageTemplateListById');
Route::post('getStaticPageTemplateListByTag', 'StaticPageController@getStaticPageTemplateListByTag');
Route::post('getWhatsNewHtmlBlocks', 'StaticPageController@getWhatsNewHtmlBlocks');
Route::post('getPostScheduleList', 'MarketingCalenderController@getPostScheduleList');
Route::post('getPostScheduleListForPreview', 'MarketingCalenderController@getPostScheduleListForPreview');
Route::post('getTemplatesByCatalogIdForDesignPage', 'UserController@getTemplatesByCatalogIdForDesignPage');
Route::any('monitorTransferStartApi', 'UserController@monitorTransferStartApi');

//Common api
Route::group(['prefix' => '', 'middleware' => ['ability:user|admin|crm|sub_admin|reviewer,user_permission|admin_permission|crm_permission|sub_admin_permission|reviewer_permission']], function () {
    //changePassword
    Route::post('changePassword', 'LoginController@changePassword');
    //setPassword
    Route::post('setPassword', 'LoginController@setPassword');
    //doLogout
    Route::post('doLogout', 'LoginController@doLogout');
    //Register device
    Route::post('registerUserDeviceByDeviceUdid', 'RegisterController@registerUserDeviceByDeviceUdid');
    Route::post('getAllTags', 'AdminController@getAllTags');

});

//api for admin and crm
Route::group(['prefix' => '', 'middleware' => ['ability:admin|crm,admin_permission|crm_permission']], function () {

    //all transaction api
    Route::post('getAllTransactionsForAdmin', 'AdminController@getAllTransactionsForAdmin');
    Route::post('searchTransaction', 'AdminController@searchTransaction');
    Route::post('verifyTransaction', 'AdminController@verifyTransaction');

    // //user api
    Route::post('getAllUsersByAdmin', 'AdminController@getAllUsersByAdmin');
    Route::post('searchUserForAdmin', 'AdminController@searchUserForAdmin');
    Route::post('changeUserRole', 'AdminController@changeUserRole');

    Route::post('getUserSessionInfo', 'AdminController@getUserSessionInfo');
    Route::post('doUserAllSessionLogout', 'LoginController@doUserAllSessionLogout');

    //User Design
    Route::post('getDesignFolderForAdmin', 'AdminController@getDesignFolderForAdmin');
    Route::post('getVideoDesignFolderForAdmin', 'AdminController@getVideoDesignFolderForAdmin');
    Route::post('getIntroDesignFolderForAdmin', 'AdminController@getIntroDesignFolderForAdmin');
    Route::post('getDesignByFolderIdForAdmin', 'AdminController@getDesignByFolderIdForAdmin');

});

//api for sub admin and crm
Route::group(['prefix' => '', 'middleware' => ['ability:admin|crm|sub_admin,admin_permission|crm_permission|sub_admin_permission']], function () {
    //all feedback api
    Route::post('getAllFeedback', 'AdminController@getAllFeedback');
    Route::post('searchFeedback', 'AdminController@searchFeedback');
});

Route::group(['prefix' => '', 'middleware' => ['ability:content_uploader,content_uploader_permission']], function () {
    //For auto upload module
    Route::post('getCatalogBySubCategoryIdForAutoUpload', 'AdminController@getCatalogBySubCategoryIdForAutoUpload');
    Route::post('getAllValidationsForAdminForAutoUpload', 'AdminController@getAllValidationsForAdminForAutoUpload');
    Route::post('getSubCategoryByAppIdForAutoUpload', 'AdminController@getSubCategoryByAppIdForAutoUpload');
    Route::post('autoUploadTemplate', 'AdminController@autoUploadTemplate');
    Route::post('autoUploadTemplateV2', 'AdminController@autoUploadTemplateV2');

    /* Master content */
    Route::post('autoUploadMCMContent', 'MasterContentAdminController@autoUploadMCMContent');
    Route::post('checkUploadStatus', 'MasterContentAdminController@checkUploadStatus');
    Route::post('getAppCatalogsWithContentUploadStatus', 'MasterContentAdminController@getAppCatalogsWithContentUploadStatus');
    Route::post('deleteMCMContent', 'MasterContentAdminController@deleteMCMContent');
    Route::post('updateMCMContent', 'MasterContentAdminController@updateMCMContent');
});

Route::group(['prefix' => '', 'middleware' => ['ability:reviewer,reviewer_permission']], function () {
    Route::post('getAllFeedbackForObArm', 'AdminController@getAllFeedbackForObArm');
});

////API for admin & sub_admin
Route::group(['prefix' => '', 'middleware' => ['ability:admin|sub_admin,admin_permission|sub_admin_permission']], function () {

    //user-download-report
    Route::post('getGenerateVideoReportForAdmin', 'AdminController@getGenerateVideoReportForAdmin');
    Route::post('getGenerateDesignReportForAdmin', 'ImageExportController@getGenerateDesignReportForAdmin');

    //marketing-calender-schedule
    Route::post('addPostSuggestionByAdmin', 'MarketingCalenderController@addPostSuggestionByAdmin');
    Route::post('addPostScheduleByAdmin', 'MarketingCalenderController@addPostScheduleByAdmin');
    Route::post('updatePostSuggestionByAdmin', 'MarketingCalenderController@updatePostSuggestionByAdmin');
    Route::post('deletePostSuggestionByAdmin', 'MarketingCalenderController@deletePostSuggestionByAdmin');
    Route::post('getPostSuggestionByAdmin', 'MarketingCalenderController@getPostSuggestionByAdmin');
    Route::post('setPostSuggestionRankOnTheTopByAdmin', 'MarketingCalenderController@setPostSuggestionRankOnTheTopByAdmin');
    Route::post('setPostScheduleByAdmin', 'MarketingCalenderController@setPostScheduleByAdmin');
    Route::post('deletePostScheduleByAdmin', 'MarketingCalenderController@deletePostScheduleByAdmin');
    Route::post('getPostScheduleDetailByAdmin', 'MarketingCalenderController@getPostScheduleDetailByAdmin');
    Route::post('getAllTemplateBySearchTag', 'MarketingCalenderController@getAllTemplateBySearchTag');
    Route::post('deleteRelatedTags', 'MarketingCalenderController@deleteRelatedTags');
    Route::post('updateRelatedTags', 'MarketingCalenderController@updateRelatedTags');

    //category
    Route::post('addCategory', 'AdminController@addCategory');
    Route::post('updateCategory', 'AdminController@updateCategory');
    Route::post('searchCategoryByName', 'AdminController@searchCategoryByName');
    Route::post('getAllCategory', 'AdminController@getAllCategory');

    //sub-category
    Route::post('addSubCategory', 'AdminController@addSubCategory');
    Route::post('updateSubCategory', 'AdminController@updateSubCategory');
    Route::post('searchSubCategoryByName', 'AdminController@searchSubCategoryByName');
    Route::post('getSubCategoryByCategoryIdForAdmin', 'AdminController@getSubCategoryByCategoryIdForAdmin');
    Route::post('getAllSubCategoryByCategoryIdForAdmin', 'AdminController@getAllSubCategoryByCategoryIdForAdmin');

    //catalog
    Route::post('addCatalog', 'AdminController@addCatalog');
    Route::post('updateCatalog', 'AdminController@updateCatalog');
    Route::post('searchCatalogByName', 'AdminController@searchCatalogByName');
    Route::post('getCatalogBySubCategoryId', 'AdminController@getCatalogBySubCategoryId');
    Route::post('getAllCatalogBySubCategoryId', 'AdminController@getAllCatalogBySubCategoryId');
    Route::post('linkCatalogToSubCategory', 'AdminController@linkCatalogToSubCategory');
    Route::post('unlinkLinkedCatalogFromSubCategory', 'AdminController@unlinkLinkedCatalogFromSubCategory');

    //Content
    Route::post('getContentByCatalogIdForAdmin', 'AdminController@getContentByCatalogIdForAdmin');

    //Normal Images which is used in json
    Route::post('addNormalImages', 'AdminController@addNormalImages');
    Route::post('updateNormalImage', 'AdminController@updateNormalImage');

    //Featured sample which has two images
    Route::post('addSampleImages', 'AdminController@addSampleImages');
    Route::post('updateSampleImages', 'AdminController@updateSampleImages');
    Route::post('getSampleImagesForAdmin', 'AdminController@getSampleImagesForAdmin');

    //Template
    Route::post('addTemplate', 'AdminController@addTemplate');
    Route::post('editTemplate', 'AdminController@editTemplate');
    Route::post('addTemplateImages', 'AdminController@addTemplateImages');

    //Text
    Route::post('addText', 'AdminController@addText');
    Route::post('editText', 'AdminController@editText');

    //3D Object
    Route::post('add3DObject', 'AdminController@add3DObject');
    Route::post('edit3DObject', 'AdminController@edit3DObject');

    //Link Catalog
    Route::post('getAllSubCategoryToLinkCatalog', 'AdminController@getAllSubCategoryToLinkCatalog');

    //Advertisement
    Route::post('getAllAdvertisements', 'AdminController@getAllAdvertisements');
    Route::post('updateLink', 'AdminController@updateLink');
    Route::post('addLink', 'AdminController@addLink');
    Route::post('getAllLink', 'AdminController@getAllLink');
    Route::post('getAllAdvertisementToLinkAdvertisement', 'AdminController@getAllAdvertisementToLinkAdvertisement');
    Route::post('linkAdvertisementWithSubCategory', 'AdminController@linkAdvertisementWithSubCategory');

    //Search Tag
    Route::post('addTag', 'AdminController@addTag');
    Route::post('updateTag', 'AdminController@updateTag');
    Route::post('setTagRankOnTheTopByAdmin', 'AdminController@setTagRankOnTheTopByAdmin');

    //remove duplicate tag
    Route::post('removeDuplicateTag', 'AdminController@removeDuplicateTag');

    //Image details
    Route::post('getImageDetails', 'AdminController@getImageDetails');

    //Feedback
    Route::post('getFeedbackForObArm', 'AdminController@getFeedbackForObArm');
    //  Route::post('getAllFeedback', 'AdminController@getAllFeedback');
    //  Route::post('searchFeedback', 'AdminController@searchFeedback');

    //Test S3_storage
    Route::post('storeFileIntoS3Bucket', 'AdminController@storeFileIntoS3Bucket');
    Route::post('saveImageIntoS3', 'AdminController@saveImageIntoS3');

    //Summary
    Route::post('getSummaryByAdmin', 'AdminController@getSummaryByAdmin');
    Route::post('searchSummaryByAdmin', 'AdminController@searchSummaryByAdmin');
    //catalogCountBy
    Route::post('getCatalogWithDetailBySubCategoryId', 'AdminController@getCatalogWithDetailBySubCategoryId');

    //Font
    Route::post('addFont', 'AdminController@addFont');
    Route::post('editFont', 'AdminController@editFont');
    Route::post('getAllFontsByCatalogIdForAdmin', 'AdminController@getAllFontsByCatalogIdForAdmin');
    Route::post('generateSearchTagByAI', 'AdminController@generateSearchTagByAI');
    Route::post('getSamplesOfNonCommercialFont', 'AdminController@getSamplesOfNonCommercialFont');
    Route::post('generatePreviewImageForUploadedFonts', 'AdminController@generatePreviewImageForUploadedFonts');

    //API to update json to update font_path fonts
    //Note: Don't use this API in live server because it will must effect on templates designed by user
    Route::post('editStaticFontPathInToJson', 'AdminController@editStaticFontPathInToJson');

    //Set rank of catalogs & templates
    Route::post('setCatalogRankOnTheTopByAdmin', 'AdminController@setCatalogRankOnTheTopByAdmin');
    Route::post('setContentRankOnTheTopByAdmin', 'AdminController@setContentRankOnTheTopByAdmin');
    Route::post('setMultipleContentRankByAdmin', 'AdminController@setMultipleContentRankByAdmin');
    //Route::post('setContentRankOnTheSpecifiedPositionByAdmin', 'AdminController@setContentRankOnTheSpecifiedPositionByAdmin');

    //API to generate all images for user_uploaded images via user_upload (original) url with pagination
    //Note: Don't use this API in live server because it will must effect on live images of uploaded by user
    Route::post('generateAllImagesForUserUploads', 'AdminController@generateAllImagesForUserUploads');
    Route::post('getUsersMaxDesign', 'AdminController@getUsersMaxDesign');
    Route::post('getUsersDailyDesign', 'AdminController@getUsersDailyDesign');
    Route::post('getUserSessionDetails', 'AdminController@getUserSessionDetails');

    //Move Template
    Route::post('moveTemplate', 'AdminController@moveTemplate');
    Route::post('getAllSubCategoryToMoveTemplate', 'AdminController@getAllSubCategoryToMoveTemplate');

    /* ===================| APIs of static page generator |=================== */

    //Static page
    Route::post('generateStaticPageByAdmin', 'StaticPageController@generateStaticPageByAdmin');
    Route::post('getStaticPageSubCategoryByAdmin', 'StaticPageController@getStaticPageSubCategoryByAdmin');
    Route::post('getStaticPageCatalogListByAdmin', 'StaticPageController@getStaticPageCatalogListByAdmin');
    Route::post('editStaticPageByAdmin', 'StaticPageController@editStaticPageByAdmin');
    Route::post('getAllSubCategoryListForStaticPage', 'StaticPageController@getAllSubCategoryListForStaticPage');
    Route::post('setStaticPageOnTheTopByAdmin', 'StaticPageController@setStaticPageOnTheTopByAdmin');
    Route::post('setStatusOfStaticPage', 'StaticPageController@setStatusOfStaticPage');
    Route::post('getWhatsNewContent', 'StaticPageController@getWhatsNewContent');
    Route::post('uploadMediaForWhatsNew', 'StaticPageController@uploadMediaForWhatsNew');
    Route::post('saveWhatNewHtml', 'StaticPageController@saveWhatNewHtml');
    Route::post('generateAllStaticPage', 'StaticPageController@generateAllStaticPage');
    Route::post('getAllStaticPageURL', 'StaticPageController@getAllStaticPageURL');

    Route::post('generateStaticPageByAdminV2', 'StaticPageController@generateStaticPageByAdminV2');
    Route::post('generateVideoStaticPageByAdminV2', 'StaticPageController@generateVideoStaticPageByAdminV2');
    Route::post('editStaticPageByAdminV2', 'StaticPageController@editStaticPageByAdminV2');
    Route::post('editVideoStaticPageByAdminV2', 'StaticPageController@editVideoStaticPageByAdminV2');
    Route::post('generateAllStaticPageV2', 'StaticPageController@generateAllStaticPageV2');
    Route::post('getTemplatesByCategoryId', 'StaticPageController@getTemplatesByCategoryId');

    Route::post('getStaticMainPageByAdmin', 'StaticPageController@getStaticMainPageByAdmin');
    Route::post('getAllTemplateListForMainStaticPage', 'StaticPageController@getAllTemplateListForMainStaticPage');
    Route::post('editStaticMainPageByAdmin', 'StaticPageController@editStaticMainPageByAdmin');
    Route::post('generateAllStaticPageV3', 'StaticPageController@generateAllStaticPageV3');

    //video templates static page
    Route::post('generateVideoStaticPageByAdmin', 'StaticPageController@generateVideoStaticPageByAdmin');
    Route::post('editVideoStaticPageByAdmin', 'StaticPageController@editVideoStaticPageByAdmin');
    Route::post('getVideoStaticPageTagListByAdmin', 'StaticPageController@getVideoStaticPageTagListByAdmin');

    //videoIntro template static page
    Route::post('getAllVideoStaticPageTagListByAdmin', 'StaticPageController@getAllVideoStaticPageTagListByAdmin');
    //set rank of video/intro & images
    Route::post('setStaticPageRankByAdmin', 'StaticPageController@setStaticPageRankByAdmin');

    //Similar templates static page
    Route::post('getPageFromTag', 'StaticPageController@getPageFromTag');
    Route::post('updateSimilarTemplatePage', 'StaticPageController@updateSimilarTemplatePage');
    Route::post('deleteSimilarTemplatePage', 'StaticPageController@deleteSimilarTemplatePage');

    //changed content in design pages
    Route::post('verifyURLForDesignPageCreation', 'AdminController@verifyURLForDesignPageCreation');
    Route::post('setChangesInDesignPage', 'AdminController@setChangesInDesignPage');
    Route::post('getDesignPageChangesList', 'AdminController@getDesignPageChangesList');
    Route::post('getDesignFileContent', 'AdminController@getDesignFileContent');

    Route::post('getDesignPageJsonList', 'AdminController@getDesignPageJsonList');
    Route::post('generateAllDesignPageByAdmin', 'AdminController@generateAllDesignPageByAdmin');
    Route::post('generateDesignPageByAdmin', 'AdminController@generateDesignPageByAdmin');
    Route::post('editDesignPageByAdmin', 'AdminController@editDesignPageByAdmin');
    Route::post('moveDesignPageByAdmin', 'AdminController@moveDesignPageByAdmin');
    Route::post('previewDesignPageByAdmin', 'AdminController@previewDesignPageByAdmin');

    //Normal Video collection
    Route::post('addNormalVideos', 'AdminController@addNormalVideos');
    Route::post('updateNormalVideo', 'AdminController@updateNormalVideo');

    //Upload json videos
    Route::post('uploadJsonVideos', 'AdminController@uploadJsonVideos');

    //json Video module
    Route::post('addJsonVideoByAdmin', 'AdminController@addJsonVideoByAdmin');
    Route::post('editJsonVideoByAdmin', 'AdminController@editJsonVideoByAdmin');
    Route::post('updateVideoTemplateDetail', 'AdminController@updateVideoTemplateDetail');
    Route::post('checkReadyToPreviewVideo', 'AdminController@checkReadyToPreviewVideo');
    Route::post('retryToGenerateVideo', 'AdminController@retryToGenerateVideo');
    Route::post('getPreviewVideoDetail', 'AdminController@getPreviewVideoDetail');
    Route::post('reducePreviewVideo', 'AdminController@reducePreviewVideo');

    //Audio collection
    Route::post('addNormalAudio', 'AdminController@addNormalAudio');
    Route::post('updateNormalAudio', 'AdminController@updateNormalAudio');

    //Get tag from image
    Route::post('getTagFromImage', 'AdminController@getTagFromImage');

    /* Get user publish design */
    //  Route::post('getUserPublishDesignForAdmin','AdminController@getUserPublishDesignForAdmin');
    //  Route::post('publishUserDesignByAdmin','AdminController@publishUserDesignByAdmin');

    /* Video page template */
    Route::post('getTemplateBySearchTag', 'AdminController@getTemplateBySearchTag');
    Route::post('updateSearchTagTemplates', 'AdminController@updateSearchTagTemplates');
    //get search tag analysis
    Route::post('getSearchAnalytics', 'SearchTagAnalyticsController@getSearchAnalytics');

    /** Intro Admin */
    Route::post('addIntrosVideoByAdmin', 'IntrosAdminController@addIntrosVideoByAdmin');
    Route::post('editIntrosVideoByAdmin', 'IntrosAdminController@editIntrosVideoByAdmin');
    Route::post('generateRowVideo', 'IntrosAdminController@generateRowVideo');

    /** Add cache control into s3 object */
    Route::post('addCacheControlInToS3Object', 'AdminController@addCacheControlInToS3Object');

    //search module
    Route::post('updateTemplateSearchingTagsByAdmin', 'AdminController@updateTemplateSearchingTagsByAdmin');

    Route::post('updateMultipleTemplateNameByAdmin', 'AdminController@updateMultipleTemplateNameByAdmin');
    Route::post('updateMultipleTemplateByAdmin', 'AdminController@updateMultipleTemplateByAdmin');
    Route::post('updateMultipleCatalogByAdmin', 'AdminController@updateMultipleCatalogByAdmin');
    Route::post('deleteMultipleTemplateByAdmin', 'AdminController@deleteMultipleTemplateByAdmin');
});

//Api for only admin
Route::group(['prefix' => '', 'middleware' => ['ability:admin,admin_permission']], function () {

    //all delete api acess to super admin
    //Advertisement
    Route::post('deleteLink', 'AdminController@deleteLink');
    //sub-category
    Route::post('deleteSubCategory', 'AdminController@deleteSubCategory');
    //catalog
    Route::post('deleteCatalog', 'AdminController@deleteCatalog');
    //Content
    Route::post('deleteContentById', 'AdminController@deleteContentById');
    //json Video module
    Route::post('deleteJsonVideoByAdmin', 'AdminController@deleteJsonVideoByAdmin');
    //Search Tag
    Route::post('deleteTag', 'AdminController@deleteTag');
    //font
    Route::post('deleteFont', 'AdminController@deleteFont');
    //category
    Route::post('deleteCategory', 'AdminController@deleteCategory');
    //Delete Catalog Item
    Route::post('deleteCatalogItem', 'AdminController@deleteCatalogItem');
    //Advertisement
    Route::post('deleteLinkedAdvertisement', 'AdminController@deleteLinkedAdvertisement');
    //Static page
    Route::post('deleteWhatsNewMedia', 'StaticPageController@deleteWhatsNewMedia');
    //Normal Video collection
    Route::post('deleteNormalVideoByAdmin', 'AdminController@deleteNormalVideoByAdmin');
    //Audio collection
    Route::post('deleteNormalAudioByAdmin', 'AdminController@deleteNormalAudioByAdmin');

    //operation in sitemap.xml
    Route::post('editDataInXMLFile', 'AdminController@editDataInXMLFile');
    Route::post('getDataFromXMLFile', 'AdminController@getDataFromXMLFile');

    //Redis
    Route::post('getRedisKeys', 'AdminController@getRedisKeys');
    Route::post('deleteRedisKeys', 'AdminController@deleteRedisKeys');
    Route::post('getRedisKeyDetail', 'AdminController@getRedisKeyDetail');
    Route::post('clearRedisCache', 'AdminController@clearRedisCache');

    /*Google 2fa API route*/
    Route::post('enable2faByAdmin', 'Google2faController@enable2faByAdmin');
    Route::post('disable2faByAdmin', 'Google2faController@disable2faByAdmin');

    //User
    //  Route::post('getAllUsersByAdmin', 'AdminController@getAllUsersByAdmin');
    //  Route::post('searchUserForAdmin', 'AdminController@searchUserForAdmin');
    //  Route::post('getUserSessionInfo', 'AdminController@getUserSessionInfo');
    //  Route::post('changeUserRole', 'AdminController@changeUserRole');
    //  Route::post('doUserAllSessionLogout', 'LoginController@doUserAllSessionLogout');

    //Transaction
    //  Route::post('getAllTransactionsForAdmin', 'AdminController@getAllTransactionsForAdmin');
    //  Route::post('searchTransaction', 'AdminController@searchTransaction');
    //  Route::post('verifyTransaction', 'AdminController@verifyTransaction');
    Route::post('confirmPaymentByAdmin', 'AdminController@confirmPaymentByAdmin');

    //File size validation
    Route::post('addValidation', 'AdminController@addValidation');
    Route::post('editValidation', 'AdminController@editValidation');
    Route::post('deleteValidation', 'AdminController@deleteValidation');
    Route::post('getAllValidationsForAdmin', 'AdminController@getAllValidationsForAdmin');

    //Payment discount module
    Route::post('createProduct', 'PaymentModuleController@createProduct');
    Route::post('updateProduct', 'PaymentModuleController@updateProduct');
    Route::post('deleteProduct', 'PaymentModuleController@deleteProduct');
    Route::post('getAllProducts', 'PaymentModuleController@getAllProducts');
    Route::post('addPricingToProduct', 'PaymentModuleController@addPricingToProduct');
    Route::post('updatePricingToProduct', 'PaymentModuleController@updatePricingToProduct');
    Route::post('deletePricingToProduct', 'PaymentModuleController@deletePricingToProduct');
    Route::post('getPricingByProduct', 'PaymentModuleController@getPricingByProduct');
    //  Route::post('getCouponFromStripe', 'PaymentModuleController@getCouponFromStripe');

    //Stripe
    Route::post('getAllPlansFromStripe', 'stripePaymentController@getAllPlansFromStripe');

});

//API for user
Route::group(['prefix' => '', 'middleware' => ['ability:user,user_permission']], function () {

    Route::post('updateUserProfile', 'RegisterController@updateUserProfile');
    Route::post('getUserProfile', 'RegisterController@getUserProfile');

    Route::post('setUserSessionOnSignup', 'UserController@setUserSessionOnSignup');

    //Feedback
    Route::post('giveFeedback', 'UserController@giveFeedback');
    Route::post('updateFeedback', 'UserController@updateFeedback');
    Route::post('getFeedback', 'UserController@getFeedback');
    Route::post('encryptStaticPageCTALink', 'UserController@encryptStaticPageCTALink');

    //Report a problem
    Route::post('submitReport', 'UserController@submitReport');

    Route::post('uploadImage', 'UserController@uploadImage');
    Route::post('uploadImageByUser', 'UserController@uploadImageByUser');
    Route::post('getMyUploadedImages', 'UserController@getMyUploadedImages');
    Route::post('deleteMyUploadedImageById', 'UserController@deleteMyUploadedImageById');

    //My designs
    Route::post('checkLimitExceededToSaveMyDesign', 'UserController@checkLimitExceededToSaveMyDesign');
    Route::post('saveMyDesign', 'UserController@saveMyDesign');
    Route::post('updateMyDesign', 'UserController@updateMyDesign');
    Route::post('getMyDesigns', 'UserController@getMyDesigns');
    Route::post('getContentDetailOfMyDesignById', 'UserController@getContentDetailOfMyDesignById');
    Route::post('deleteMyDesignsById', 'UserController@deleteMyDesignsById');
    Route::post('copyMyDesignsById', 'UserController@copyMyDesignsById');
    Route::post('renameMyDesignNameById', 'UserController@renameMyDesignNameById');

    //save module
    Route::post('saveMyTemplate', 'UserController@saveMyTemplate');
    Route::post('updateTemplate', 'UserController@updateTemplate');
    Route::post('updateIntrosTemplate', 'UserController@updateIntrosTemplate');
    Route::post('saveMyTemplateV2', 'UserController@saveMyTemplateV2');
    Route::post('updateTemplateV2', 'UserController@updateTemplateV2');
    Route::post('autoSaveTemplate', 'UserController@autoSaveTemplate');
    Route::post('autoSaveTemplatesFile', 'UserController@autoSaveTemplatesFile');

    Route::post('saveTemplateWithLimitCheck', 'UserController@saveTemplateWithLimitCheck');
    Route::post('saveMultiPageTemplateWithLimitCheck', 'UserController@saveMultiPageTemplateWithLimitCheck');
    //Route::post('saveMyTemplateTemporarily', 'UserController@saveMyTemplateTemporarily');

    //my design folder module
    Route::post('createMyDesignFolder', 'UserController@createMyDesignFolder');
    Route::post('editMyDesignFolder', 'UserController@editMyDesignFolder');
    Route::post('moveMyDesignInToFolder', 'UserController@moveMyDesignInToFolder');
    Route::post('removeMyDesignFolder', 'UserController@removeMyDesignFolder');
    Route::post('removeDesignLists', 'UserController@removeDesignLists');
    Route::post('getMyDesignFolder', 'UserController@getMyDesignFolder');
    Route::post('getMyVideoDesignFolder', 'UserController@getMyVideoDesignFolder');
    Route::post('getMyIntroDesignFolder', 'UserController@getMyIntroDesignFolder');
    Route::post('getMyDesignByFolderId', 'UserController@getMyDesignByFolderId');
    Route::post('getFolders', 'UserController@getFolders');

    //Catalog
    Route::post('getFeaturedCatalogsBySubCategoryId', 'UserController@getFeaturedCatalogsBySubCategoryId');
    Route::post('getFeaturedCatalogsWithTemplateBySubCategoryId', 'UserController@getFeaturedCatalogsWithTemplateBySubCategoryId');
    Route::post('getTemplateByCatalogId', 'UserController@getTemplateByCatalogId');
    Route::post('getTemplateByCatalogNameAsTag', 'UserController@getTemplateByCatalogNameAsTag');
    Route::post('searchTemplateBySubCategoryIdBackUp', 'UserController@searchTemplateBySubCategoryIdBackUp');
    Route::post('searchTemplateBySubCategoryId', 'UserController@searchTemplateBySubCategoryId');
    Route::post('getSuggestionTextsForTemplate', 'UserController@getSuggestionTextsForTemplate');
    Route::post('searchSticker', 'UserController@searchSticker');
    Route::post('searchTextArt', 'UserController@searchTextArt');
    Route::post('searchBackground', 'UserController@searchBackground');
    Route::post('getContentDetailById', 'UserController@getContentDetailById');
    Route::post('getContentDetailByIdV2', 'UserController@getContentDetailByIdV2');
    Route::post('getContentDetailByIdV3', 'UserController@getContentDetailByIdV3');
    Route::post('getContentByCatalogId', 'UserController@getContentByCatalogId');
    Route::post('searchAudioOrVideo', 'UserController@searchAudioOrVideo');

    Route::post('searchStickerMCM', 'UserController@searchStickerMCM');
    Route::post('searchBackgroundMCM', 'UserController@searchBackgroundMCM');

    //backgrounds & stickers
    Route::post('getNormalCatalogsBySubCategoryId', 'UserController@getNormalCatalogsBySubCategoryId');
    Route::post('getStickerCatalogsBySubCategoryId', 'UserController@getStickerCatalogsBySubCategoryId');

    //Sample backgrounds & stickers
    Route::post('getSampleContentBySubCategoryId', 'UserController@getSampleContentBySubCategoryId');

    //Dashboard
    Route::post('getDashboardData', 'UserController@getDashboardData');
    Route::post('getDashBoardDetails', 'UserController@getDashBoardDetails');

    //Fetch images from Pixabay
    Route::post('getImagesFromPixabay', 'PixabayController@getImagesFromPixabay');

    //Fetch images from Unsplash
    //Route::post('getImageFromUnsplash', 'UnsplashController@getImageFromUnsplash');

    //Fetch videos from Pixabay
    Route::post('getVideosFromPixabay', 'PixabayController@getVideosFromPixabay');

    Route::post('uploadPixabayVideo', 'PixabayController@uploadPixabayVideo');

    //Payment
    Route::post('setPaymentStatus', 'UserController@setPaymentStatus');
    Route::post('cancelSubscription', 'UserController@cancelSubscription');

    //Billing Address
    Route::post('setBillingAddress', 'UserController@setBillingAddress');
    Route::post('getBillingInfo', 'UserController@getBillingInfo');

    //edit3DShape by designer user
    Route::post('edit3DShape', 'UserController@edit3DShape');

    //Delete Account
    Route::post('deleteMyAccount', 'UserController@deleteMyAccount');

    //Fonts
    Route::post('uploadFontByUser', 'UserController@uploadFontByUser');
    Route::post('deleteMyUploadedFontById', 'UserController@deleteMyUploadedFontById');
    Route::post('getMyUploadedFonts', 'UserController@getMyUploadedFonts');
    Route::post('getAllFonts', 'UserController@getAllFonts'); //Get all fonts provided by admin

    //Manage 1st time login for user (disable tag of 1st time login)
    Route::post('setValueOfFirstTimeLogin', 'LoginController@setValueOfFirstTimeLogin');

    //share & like module
    Route::post('addSharedLinkDetailsByUser', 'UserController@addSharedLinkDetailsByUser');
    Route::post('getShareLinkContentForUser', 'UserController@getShareLinkContentForUser');

    //User uploaded video module
    Route::post('uploadVideo', 'UserController@uploadVideo');
    Route::post('uploadVideoByUser', 'UserController@uploadVideoByUser');
    Route::post('getMyUploadedVideos', 'UserController@getMyUploadedVideos');
    Route::post('deleteMyUploadedVideoById', 'UserController@deleteMyUploadedVideoById');

    //Video module
    Route::post('generateVideo', 'UserController@generateVideo');
    Route::post('checkReadyToDownload', 'UserController@checkReadyToDownload');

    Route::post('generateIntrosVideo', 'IntrosUserController@generateIntrosVideo');

    //User uploaded audio module
    Route::post('uploadAudio', 'UserController@uploadAudio');
    Route::post('uploadAudioByUser', 'UserController@uploadAudioByUser');
    Route::post('deleteMyUploadedAudioById', 'UserController@deleteMyUploadedAudioById');
    Route::post('getMyUploadedAudio', 'UserController@getMyUploadedAudio');

    Route::post('searchFontByUser', 'UserController@searchFontByUser');
    Route::post('searchFontByUserV2', 'UserController@searchFontByUserV2');
    Route::post('searchUploadedFontByUser', 'UserController@searchUploadedFontByUser');

    //Stripe Payment
    Route::post('CreateStripePaymentMethod', 'stripePaymentController@CreateStripePaymentMethod');
    Route::post('stripeUserPayment', 'stripePaymentController@stripeUserPayment');
    Route::post('cancelStripeSubscription', 'stripePaymentController@cancelStripeSubscription');
    Route::post('changeStripeSubscription', 'stripePaymentController@changeStripeSubscription');
    Route::post('resubscribeStripeSubscription', 'stripePaymentController@resubscribeStripeSubscription');
    Route::post('createUpcomingInvoice', 'stripePaymentController@createUpcomingInvoice');
    Route::post('getAllPlansForUser', 'stripePaymentController@getAllPlansForUser');
    Route::post('getPaymentStatusForUser', 'stripePaymentController@getPaymentStatusForUser');

    // FastSpring API in PAK
    Route::post('cancelFastSpringSubscription', 'fastSpringPaymentController@cancelFastSpringSubscription');
    Route::post('setFastSpringPaymentMethod', 'fastSpringPaymentController@setFastSpringPaymentMethod');
    Route::post('getFastSpringUserOrdersURL', 'fastSpringPaymentController@getFastSpringUserOrdersURL');

    /* publish design by user */
    //  Route::post('publishDesignByUser', 'UserController@publishDesignByUser');

    //get encrypted id for design template page
    Route::post('getEncryptIdForDesignTemplate', 'UserController@getEncryptIdForDesignTemplate');
    Route::post('getEncryptId', 'UserController@getEncryptId');

    //get encrypted id for design template page
    Route::post('getEncryptIdForDesignTemplate', 'UserController@getEncryptIdForDesignTemplate');
    Route::post('getEncryptId', 'UserController@getEncryptId');
    Route::post('addSubCategoryASTag', 'UserController@addSubCategoryASTag');
    Route::post('addCategoryASTag', 'UserController@addCategoryASTag');

    //discount payment module
    Route::post('getPricingByUser', 'PaymentModuleController@getPricingByUser');

    //for you page
    Route::post('getRecentMyDesign', 'UserController@getRecentMyDesign');
    Route::post('getUpcomingEvents', 'UserController@getUpcomingEvents');
    Route::post('getContentBySearchTag', 'UserController@getContentBySearchTag');
    Route::post('getContentBySearchTagWithCategory', 'UserController@getContentBySearchTagWithCategory');
    Route::post('getUserKeyword', 'UserController@getUserKeyword');
    Route::post('editUserKeyword', 'UserController@editUserKeyword');

    //image export
    Route::post('generateDesign', 'ImageExportController@generateDesign');
    Route::post('checkReadyToDownloadDesign', 'ImageExportController@checkReadyToDownloadDesign');
    Route::post('cancleLiveQueueJobForDesign', 'ImageExportController@cancleLiveQueueJobForDesign');

});

// PayPal IPN
Route::post('paypalIpn', 'PaypalIPNController@paypalIpn');

// FastSpring Webhook Events
Route::post('subscriptionActivatedEvent', 'fastSpringPaymentController@subscriptionActivatedEvent');
Route::post('orderCompletedEvent', 'fastSpringPaymentController@orderCompletedEvent');
Route::post('orderFailedEvent', 'fastSpringPaymentController@orderFailedEvent');
Route::post('subscriptionCanceledEvent', 'fastSpringPaymentController@subscriptionCanceledEvent');
Route::post('subscriptionUncanceledEvent', 'fastSpringPaymentController@subscriptionUncanceledEvent');
Route::post('subscriptionChargeCompletedEvent', 'fastSpringPaymentController@subscriptionChargeCompletedEvent');
Route::post('subscriptionChargeFailedEvent', 'fastSpringPaymentController@subscriptionChargeFailedEvent');
Route::post('returnCreatedEvent', 'fastSpringPaymentController@returnCreatedEvent');

// Below webhook api is only for debugging purpose
Route::post('orderCanceledEvent', 'fastSpringPaymentController@orderCanceledEvent');
Route::post('orderPaymentPendingEvent', 'fastSpringPaymentController@orderPaymentPendingEvent');
Route::post('orderApprovalPendingEvent', 'fastSpringPaymentController@orderApprovalPendingEvent');
Route::post('fulfillmentFailedEvent', 'fastSpringPaymentController@fulfillmentFailedEvent');
Route::post('subscriptionDeactivatedEvent', 'fastSpringPaymentController@subscriptionDeactivatedEvent');
Route::post('subscriptionUpdatedEvent', 'fastSpringPaymentController@subscriptionUpdatedEvent');
Route::post('subscriptionTrialReminderEvent', 'fastSpringPaymentController@subscriptionTrialReminderEvent');
Route::post('subscriptionPaymentReminderEvent', 'fastSpringPaymentController@subscriptionPaymentReminderEvent');
Route::post('subscriptionPaymentOverdueEvent', 'fastSpringPaymentController@subscriptionPaymentOverdueEvent');
Route::post('invoiceReminderEmailEvent', 'fastSpringPaymentController@invoiceReminderEmailEvent');
Route::post('mailingListEntryUpdatedEvent', 'fastSpringPaymentController@mailingListEntryUpdatedEvent');
Route::post('mailingListEntryAbandonedEvent', 'fastSpringPaymentController@mailingListEntryAbandonedEvent');
Route::post('mailingListEntryRemovedEvent', 'fastSpringPaymentController@mailingListEntryRemovedEvent');
Route::post('accountCreatedEvent', 'fastSpringPaymentController@accountCreatedEvent');
Route::post('accountUpdatedEvent', 'fastSpringPaymentController@accountUpdatedEvent');
Route::post('payoutEntryCreatedEvent', 'fastSpringPaymentController@payoutEntryCreatedEvent');
Route::post('quoteCreatedEvent', 'fastSpringPaymentController@quoteCreatedEvent');
Route::post('quoteUpdatedEvent', 'fastSpringPaymentController@quoteUpdatedEvent');

Route::post('emailNotifications', 'AdminController@emailNotifications');

// Stripe Webhook events
Route::post('stripePaymentEvents', 'stripeWebhookEventController@stripePaymentEvents');
Route::post('stripeSubscriptionUpdateEvents', 'stripeWebhookEventController@stripeSubscriptionUpdateEvents');
Route::post('stripeCustomerEvents', 'stripeWebhookEventController@stripeCustomerEvents');
Route::post('stripeInvoiceEvents', 'stripeWebhookEventController@stripeInvoiceEvents');
Route::post('getPricingForStaticPage', 'PaymentModuleController@getPricingForStaticPage');
// Add xml tag into svg file
Route::post('generateSVGWithXML', 'UserController@generateSVGWithXML');

// APIs of debug purpose
Route::post('getExpiry', 'UserController@getExpiry'); //Test function
Route::post('testMail', 'AdminController@testMail'); //Test mail
Route::post('runArtisanCommands', 'AdminController@runArtisanCommands');
//Route::post('runExecCommands', 'AdminController@runExecCommands');
Route::post('getDatabaseInfo', 'AdminController@getDatabaseInfo'); //Fetch table information from database
Route::post('getPhpInfo', 'AdminController@getPhpInfo'); //Get PhpInfo
Route::post('getUserRecords', 'AdminController@getUserRecords'); //call Warning mail schedular
Route::post('deleteAllRedisKeysByKeyName', 'AdminController@deleteAllRedisKeysByKeyName');
Route::post('getAllRedisKeysByKeyName', 'AdminController@getAllRedisKeysByKeyName');
Route::post('getRedisKeyValueByKeyName', 'AdminController@getRedisKeyValueByKeyName');
Route::post('getRandomRedisKeys', 'AdminController@getRandomRedisKeys');
//Route::post('insertNewRole', 'AdminController@insertNewRole');//Fetch table information from database

//check file exist
Route::post('checkFileExistWithFOpen', 'AdminController@checkFileExistWithFOpen');
Route::post('checkFileExistWithFileExist', 'AdminController@checkFileExistWithFileExist');
Route::post('getAllLocalFile', 'AdminController@getAllLocalFile');
Route::post('deleteLocalFileByFileName', 'AdminController@deleteLocalFileByFileName');
//Route::post('getAllFileListFromS3', 'AdminController@getAllFileListFromS3');
//Route::post('deleteFileListFromS3', 'AdminController@deleteFileListFromS3');

//delete account by admin
//Route::post('deleteAccount', 'UserController@deleteAccount');

//Route::post('subscribeUserByEmail', 'MailchimpController@subscribeUserByEmail');
//Route::post('setTagIntoList', 'MailchimpController@setTagIntoList');
//Route::post('deleteTagFromSubscriber', 'MailchimpController@deleteTagFromSubscriber');
//Route::post('runScheduler', 'VerificationController@runScheduler');
//Route::post('DbBackupJob', 'stripePaymentController@DbBackupJob');

Route::post('searchStaticPageTemplateBySubCategoryId', 'StaticPageController@searchStaticPageTemplateBySubCategoryId');
Route::post('getTemplatesByCategoryIdV2', 'StaticPageController@getTemplatesByCategoryIdV2');

Route::post('addCtaDetailInDesignPage', 'AdminController@addCtaDetailInDesignPage');
Route::post('addCatalogIdInDesignPage', 'AdminController@addCatalogIdInDesignPage');

Route::post('uploadFileInChunkMode', 'UserController@uploadFileInChunkMode');
Route::post('uploadFile', 'UserController@uploadFile');

/* For delete s3 object for MCM */
Route::post('deleteObjectsFromS3', 'MasterContentAdminController@deleteObjectsFromS3');

Route::post('getUrlFromNodeRoute', 'ImageExportController@getUrlFromNodeRoute');
Route::post('insertMyDesign', 'UserController@insertMyDesign');
Route::post('getTemplateDetailsBySubCategoryId', 'StaticPageController@getTemplateDetailsBySubCategoryId');
