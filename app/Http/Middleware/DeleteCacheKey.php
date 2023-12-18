<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;


class DeleteCacheKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*$api = $request->getPathInfo();
        if (!str_contains($api, "/api/logs/")) {
            Log::info("apicall :", [$api]);
        }*/
        return $next($request);
    }

  public function terminate(Request $request)
  {
    try {
      $api = $request->getPathInfo();

      //Category
      if ($api == '/api/addCategory' or $api == '/api/updateCategory' or $api == '/api/deleteCategory') {

        //All Category Key
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllCategory*"),['']));

        //getSubCategoryByCategoryIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSubCategoryByCategoryIdForAdmin*"),['']));

        //getAllSubCategoryToLinkCatalog
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToLinkCatalog*"),['']));

      }

      //Sub-Category
      if ($api == '/api/addSubCategory' or $api == '/api/updateSubCategory' or $api == '/api/deleteSubCategory' or $api == '/api/linkCatalogToSubCategory' or $api == '/api/unlinkLinkedCatalogFromSubCategory') {

        //getCatalogWithDetailBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogWithDetailBySubCategoryId*"),['']));

        //getSubCategoryByCategoryIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSubCategoryByCategoryIdForAdmin*"),['']));

        //getCatalogBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogBySubCategoryId*"),['']));

        //getAllSubCategoryToLinkCatalog
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToLinkCatalog*"),['']));

        //getImageDetails
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getImageDetails*"),['']));

        //getAllSubCategoryToMoveTemplate
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToMoveTemplate*"),['']));

        //getAllSubCategoryListForStaticPage
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryListForStaticPage*"),['']));

        //getStaticPageCatalogListByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageCatalogListByAdmin*"),['']));

        /*======================| Users |======================*/

        //getDashboardData
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDashboardData*"),['']));

        //getFeaturedCatalogsBySubCategoryId
        //Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsBySubCategoryId*"),['']));

        //getFeaturedCatalogsWithTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsWithTemplateBySubCategoryId*"),['']));

        //getStickerCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStickerCatalogsBySubCategoryId*"),['']));

        //getStaticPageTemplateListById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListById*"),['']));

      }

      //Catalog-Category
      if ($api == '/api/addCatalog' or $api == '/api/updateCatalog' or $api == '/api/deleteCatalog' or $api == '/api/linkCatalogToSubCategory' or $api == '/api/unlinkLinkedCatalogFromSubCategory' or $api == '/api/setCatalogRankOnTheTopByAdmin') {

        //getCatalogWithDetailBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogWithDetailBySubCategoryId*"),['']));

        //getCatalogBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogBySubCategoryId*"),['']));

        //getAllSubCategoryToLinkCatalog
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToLinkCatalog*"),['']));

        //Delete Image details View Key
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getImageDetails*"),['']));

        //getAllSubCategoryToMoveTemplate
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToMoveTemplate*"),['']));

        //getAllSubCategoryListForStaticPage
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryListForStaticPage*"),['']));

        //getStaticPageCatalogListByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageCatalogListByAdmin*"),['']));

        /*======================| Users |======================*/

        //getDashboardData
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDashboardData*"),['']));

        //getFeaturedCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsBySubCategoryId*"),['']));

        //getFeaturedCatalogsWithTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsWithTemplateBySubCategoryId*"),['']));

        //getStickerCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStickerCatalogsBySubCategoryId*"),['']));

        //searchTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":".Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId*"),['']));
        //searchTemplateFromAllCategory
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchTemplateFromAllCategory*"),['']));

        //searchSticker
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchSticker*"),['']));

        //searchStickerMCM
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchStickerMCM*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchTextArt*"),['']));

        //searchBackground
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchBackground*"),['']));

        //searchBackgroundMCM
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchBackgroundMCM*"),['']));

        //getSampleContentBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSampleContentBySubCategoryId*"),['']));

        //getNormalCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getNormalCatalogsBySubCategoryId*"),['']));

        //getStaticPageTemplateListById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListById*"),['']));
      }

      //Suggestion texts
      if ($api == '/api/deleteContentById' or $api == '/api/addTemplate' or $api == '/api/editTemplate') {

        //getSuggestionTextsForTemplate
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSuggestionTextsForTemplate*"),['']));

        //getAllTags
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTags*"),['']));

        //searchTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":".Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId*"),['']));

      }

      //Sub Category Images
      if ($api == '/api/addNormalImages' or $api == '/api/updateNormalImage' or $api == '/api/deleteContentById' or $api == '/api/addSampleImages' or $api == '/api/updateSampleImages' or $api == '/api/addTemplate' or $api == '/api/editTemplate' or $api == '/api/addText' or $api == '/api/editText' or $api == '/api/add3DObject' or $api == '/api/edit3DObject' or $api == '/api/edit3DShape' or $api == '/api/setContentRankOnTheTopByAdmin' or $api == '/api/moveTemplate') {

        //getCatalogWithDetailBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogWithDetailBySubCategoryId*"),['']));

        //getSampleImagesForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSampleImagesForAdmin*"),['']));

        //getContentByCatalogIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentByCatalogIdForAdmin*"),['']));

        //Delete Image details View Key
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getImageDetails*"),['']));

        //getAllSubCategoryToMoveTemplate
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryToMoveTemplate*"),['']));

        /*======================| Users |======================*/

        //getDashboardData
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDashboardData*"),['']));

        //getFeaturedCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsBySubCategoryId*"),['']));

        //getFeaturedCatalogsWithTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsWithTemplateBySubCategoryId*"),['']));

        //getStickerCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStickerCatalogsBySubCategoryId*"),['']));

        //searchTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":".Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId*"),['']));

        //searchTemplateFromAllCategory
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchTemplateFromAllCategory*"),['']));

        //searchSticker
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchSticker*"),['']));

        //searchStickerMCM
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchStickerMCM*"),['']));

        //searchTextArt
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchTextArt*"),['']));

        //searchBackground
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchBackground*"),['']));

        //searchBackgroundMCM
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchBackgroundMCM*"),['']));

        //getTemplateByCatalogId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getTemplateByCatalogId*"),['']));

        //getContentDetailById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentDetailById*"),['']));

        //getSampleContentBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSampleContentBySubCategoryId*"),['']));

        //getNormalCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getNormalCatalogsBySubCategoryId*"),['']));

        //getContentByCatalogId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentByCatalogId*"),['']));

        //getMyDesigns
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesigns*"),['']));

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

        //getMyVideoDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyVideoDesignFolder*"),['']));

        //getMyIntroDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyIntroDesignFolder*"),['']));

        //getMyDesignByFolderId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));


        //getDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignFolderForAdmin*"),['']));

        //getVideoDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoDesignFolderForAdmin*"),['']));

        //getIntroDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getIntroDesignFolderForAdmin*"),['']));

        //getDesignByFolderIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignByFolderIdForAdmin*"),['']));

        //getStaticPageTemplateListById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListById*"),['']));

        //getAllTags
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTags*"),['']));

      }

      //Billing Address & Transaction
      if ($api == '/api/setBillingAddress' or $api == '/api/paypalIpn' or $api == '/api/setPaymentStatus' or $api == '/api/cancelSubscription' or $api == '/api/changeUserRole' or $api == '/api/confirmPaymentByAdmin' or $api == '/api/setFastSpringPaymentMethod' or $api == '/api/orderCompletedEvent' or $api == '/api/subscriptionChargeCompletedEvent' or $api == '/api/subscriptionChargeFailedEvent' or $api == '/api/cancelFastSpringSubscription' or $api == '/api/subscriptionCanceledEvent' or $api == '/api/returnCreatedEvent') {

        //getMyUploadedImages
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImages*"),['']));

        //getBillingInfo
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getBillingInfo*"),['']));

        //getAllTransactionsForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTransactionsForAdmin*"),['']));

        //getUserProfile
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserProfile*"),['']));

        //getAllUsersByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllUsersByAdmin*"),['']));

      }

      //Stripe Billing
      if ($api == '/api/stripeUserPayment' or $api == '/api/cancelStripeSubscription' or $api == '/api/changeStripeSubscription' or $api == '/api/runScheduler' or $api == '/api/resubscribeStripeSubscription' or $api == '/api/stripeSubscriptionUpdateEvents' or $api == '/api/stripeCustomerEvents') {

        //getMyUploadedImages
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImages*"),['']));

        //getBillingInfo
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getBillingInfo*"),['']));

        //getAllTransactionsForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTransactionsForAdmin*"),['']));

        //getUserProfile
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserProfile*"),['']));

        //getAllUsersByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllUsersByAdmin*"),['']));

      }

      //delete user account
      if ($api == '/api/deleteMyAccount' ) {

        //getUserProfile
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserProfile*"),['']));

        //getFeedback
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeedback*"),['']));

        //getMyUploadedImages
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImages*"),['']));

        //getMyUploadedImagesFromOriginal
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImagesFromOriginal*"),['']));

        //getMyDesigns
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesigns*"),['']));

        //getContentDetailOfMyDesignById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentDetailOfMyDesignById*"),['']));

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

        //getMyVideoDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyVideoDesignFolder*"),['']));

        //getMyIntroDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyIntroDesignFolder*"),['']));

        //getMyDesignByFolderId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));

        //getFolders
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFolders*"),['']));


        //getDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignFolderForAdmin*"),['']));

        //getVideoDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoDesignFolderForAdmin*"),['']));

        //getIntroDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getIntroDesignFolderForAdmin*"),['']));

        //getDesignByFolderIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignByFolderIdForAdmin*"),['']));


        //getBillingInfo
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getBillingInfo*"),['']));

        //getAllUsersByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllUsersByAdmin*"),['']));

        //getAllFeedback
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFeedback*"),['']));

        //getAllFeedbackForObArm
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFeedbackForObArm*"),['']));
      }

      //User
      if ($api == '/api/userSignUp' or $api == '/api/resendVerificationLink' or $api == '/api/verifyUser' or $api == '/api/updateUserProfile' or $api == '/api/changeUserRole' or $api == '/api/doLoginForSocialUser' or $api == '/api/doLoginForUser' or $api == '/api/checkSocialUserExist' or $api == '/api/doUserAllSessionLogout' or $api == '/api/doLogout') {

        //getUserProfile
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserProfile*"),['']));

        //getAllUsersByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllUsersByAdmin*"),['']));

        //getUserSessionInfo
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserSessionInfo*"),['']));
      }

      //Uploaded image by user
      if ($api == '/api/uploadImageByUser' or $api == '/api/deleteMyUploadedImageById' or $api == '/api/uploadImageByUserInOriginal' or $api == '/api/uploadImage') {

        //getMyUploadedImages
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImages*"),['']));

        //getMyUploadedImagesFromOriginal
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedImagesFromOriginal*"),['']));
      }

      //My Upload
      if ($api == '/api/giveFeedback' or $api == '/api/updateFeedback' or $api == '/api/saveMyDesign' or $api == '/api/updateMyDesign' or $api == '/api/deleteMyDesignsById' or $api == '/api/copyMyDesignsById' or $api == '/api/saveMyTemplate' or $api == '/api/updateTemplate' or $api == '/api/updateIntrosTemplate' or $api == '/api/saveMyTemplateV2' or $api == '/api/updateTemplateV2' or $api == '/api/generateDesign' or $api == '/api/generateIntrosVideo' or $api == '/api/renameMyDesignNameById') {

        //getFeedback
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeedback*"),['']));

        //getAllFeedback
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFeedback*"),['']));

        //getAllFeedbackForObArm
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFeedbackForObArm*"),['']));

      }

      //My Upload
      if ($api == '/api/saveMyDesign' or $api == '/api/updateMyDesign' or $api == '/api/updateIntrosTemplate' or $api == '/api/deleteMyDesignsById' or $api == '/api/copyMyDesignsById' or $api == '/api/saveMyTemplate' or $api == '/api/updateTemplate' or $api == '/api/saveMyTemplateV2' or $api == '/api/updateTemplateV2'  or $api == '/api/generateDesign' or $api == '/api/generateIntrosVideo' or $api == '/api/renameMyDesignNameById') {


        //getMyDesigns
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesigns*"),['']));

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

        //getMyVideoDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyVideoDesignFolder*"),['']));

        //getMyIntroDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyIntroDesignFolder*"),['']));

        //getMyDesignByFolderId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));


        //getDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignFolderForAdmin*"),['']));

        //getVideoDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoDesignFolderForAdmin*"),['']));

        //getIntroDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getIntroDesignFolderForAdmin*"),['']));

        //getDesignByFolderIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignByFolderIdForAdmin*"),['']));


        //getContentDetailOfMyDesignById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentDetailOfMyDesignById*"),['']));

        //getMyDesignByFolderId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

      }

      //Search tag
      if ($api == '/api/addTag' or $api == '/api/updateTag' or $api == '/api/deleteTag' or $api == '/api/setTagRankOnTheTopByAdmin') {
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTags*"),['']));
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDashBoardDetails:tag_details"),['']));
      }

      //Advertisement
      if ($api == '/api/addLink' or $api == '/api/updateLink' or $api == '/api/deleteLink' or $api == '/api/linkAdvertisementWithSubCategory' or $api == '/api/deleteLinkedAdvertisement') {

        //getAllAdvertisements
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllAdvertisements*"),['']));

        //getAllLink
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllLink*"),['']));

        //getAllAdvertisementToLinkAdvertisement
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllAdvertisementToLinkAdvertisement*"),['']));
      }

      //User fonts
      if ($api == '/api/uploadFontByUser' or $api == '/api/deleteMyUploadedFontById') {

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedFonts*"),['']));
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchUploadedFontByUser*"),['']));
      }

      //Font Module
      if ($api == '/api/addFont' or $api == '/api/editFont' or $api == '/api/deleteFont' or $api == '/api/generatePreviewImageForUploadedFonts') {

        //getAllFontsByCatalogIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFontsByCatalogIdForAdmin*"),['']));

        /*======================| Users |======================*/

        //getAllFonts
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllFonts*"),['']));

        //searchFontByUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchFontByUser*"),['']));


      }

      //Static page generator
      if ($api == '/api/generateStaticPageByAdminV2' or $api == '/api/generateStaticPageByAdmin' or $api == '/api/editStaticPageByAdminV2' or $api == '/api/editStaticPageByAdmin' or $api == '/api/setStaticPageOnTheTopByAdmin' or $api == '/api/setStatusOfStaticPage' or $api == '/api/setStaticPageRankByAdmin') {

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogWithDetailBySubCategoryId*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoStaticPageTagListByAdmin*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListByTag*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListById*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageSubCategoryByAdmin*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageCatalogListByAdmin*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllSubCategoryListForStaticPage*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getLeftNavigation*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":generateAllStaticPage*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllStaticPageURL*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPageFromTag*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSubPages*"),['']));

      }

      //Validations for file size
      if ($api == '/api/addValidation' or $api == '/api/editValidation' or $api == '/api/deleteValidation') {

        //getAllValidationsForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllValidationsForAdmin*"),['']));

        //getValidationFromCache
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getValidationFromCache*"),['']));
      }

      //share link by user
      if ($api == '/api/addSharedLinkDetailsByUser') {

        //getShareLinkContentForUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getShareLinkContentForUser*"),['']));
      }

      //whats new update
      if ($api == '/api/uploadMediaForWhatsNew' or $api == '/api/deleteWhatsNewMedia' or $api == '/api/saveWhatNewHtml' ) {
        //getWhatsNewContent
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getWhatsNewContent*"),['']));

        //getWhatsNewHtmlBlocks
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getWhatsNewHtmlBlocks*"),['']));
      }

      //folder structure module
      if ($api == '/api/createMyDesignFolder' or $api == '/api/editMyDesignFolder' or $api == '/api/moveMyDesignInToFolder' or $api == 'generateDesign' or $api == '/api/removeMyDesignFolder' or $api == '/api/removeDesignLists') {

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

        //getMyVideoDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyVideoDesignFolder*"),['']));

        //getMyIntroDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyIntroDesignFolder*"),['']));

        //getMyDesignByFolderId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));

        //getFolders
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFolders*"),['']));


        //getDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignFolderForAdmin*"),['']));

        //getVideoDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoDesignFolderForAdmin*"),['']));

        //getIntroDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getIntroDesignFolderForAdmin*"),['']));

        //getDesignByFolderIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignByFolderIdForAdmin*"),['']));

        //getContentDetailOfMyDesignById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentDetailOfMyDesignById*"),['']));

      }


      //Admin Video/Audio Module
      if ($api == '/api/addNormalVideos' or $api == '/api/updateNormalVideo' or $api == '/api/deleteNormalVideoByAdmin' or $api == '/api/addJsonVideoByAdmin' or $api == '/api/editJsonVideoByAdmin' or $api == '/api/deleteJsonVideoByAdmin' or '/api/addNormalAudio' or '/api/updateNormalAudio' or '/api/deleteNormalAudioByAdmin' or $api == '/api/addIntrosVideoByAdmin' or $api == '/api/editIntrosVideoByAdmin' or $api == '/api/updateVideoTemplateDetail' or $api == '/api/addSubCategoryASTag' or $api == '/api/autoUploadTemplate' or $api == '/api/autoUploadTemplateV2' or $api == '/api/deleteContentById') {

        //searchAudioOrVideo
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchAudioOrVideo*"),['']));

        //getCatalogWithDetailBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getCatalogWithDetailBySubCategoryId*"),['']));

        //getTemplateBySearchTag
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getTemplateBySearchTag*"),['']));

        //getAllTemplateBySearchTag
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllTemplateBySearchTag*"),['']));

        //getContentByCatalogIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentByCatalogIdForAdmin*"),['']));

        //getTemplateByCatalogId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getTemplateByCatalogId*"),['']));

        //searchAudioByUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchAudioByUser*"),['']));

        //searchVideoByUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":searchVideoByUser*"),['']));

        //user
        //getContentByCatalogId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentByCatalogId*"),['']));

        //searchTemplateBySubCategoryId

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").Config::get('constant.REDIS_PREFIX').":searchTemplateBySubCategoryId*"),['']));

        //getNormalCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getNormalCatalogsBySubCategoryId*"),['']));

        //getFeaturedCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsBySubCategoryId*"),['']));

        //getFeaturedCatalogsWithTemplateBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getFeaturedCatalogsWithTemplateBySubCategoryId*"),['']));

        //getStickerCatalogsBySubCategoryId
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStickerCatalogsBySubCategoryId*"),['']));

        //getContentDetailById
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getContentDetailById*"),['']));

        //getStaticPageTemplateListByTag
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListByTag*"),['']));

      }
      //User Video Module
      if ($api == '/api/uploadVideoByUser' or $api == '/api/deleteMyUploadedVideoById' or $api == '/api/uploadVideo') {

        //getMyUploadedVideos
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedVideos*"),['']));
      }
      //User Audio Module
      if ($api == '/api/uploadAudioByUser' or $api == '/api/deleteMyUploadedAudioById' or $api == '/api/uploadAudio') {

        //getMyUploadedVideos
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyUploadedAudio*"),['']));
      }

      //My design Module
      if ($api == '/api/moveMyDesignInToFolder' or $api == '/api/saveMyTemplate' or $api == '/api/generateDesign' or $api == '/api/updateTemplate' or $api == '/api/updateIntrosTemplate' or $api == '/api/saveMyTemplateV2' or $api == '/api/updateTemplateV2' or $api == '/api/generateIntrosVideo' or $api == '/api/removeMyDesignFolder' or $api == '/api/removeDesignLists') {

        //getMyUploadedVideos
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignByFolderId*"),['']));

        //getMyDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyDesignFolder*"),['']));

        //getMyVideoDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyVideoDesignFolder*"),['']));

        //getMyIntroDesignFolder
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getMyIntroDesignFolder*"),['']));


        //getDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignFolderForAdmin*"),['']));

        //getVideoDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoDesignFolderForAdmin*"),['']));

        //getIntroDesignFolderForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getIntroDesignFolderForAdmin*"),['']));

        //getDesignByFolderIdForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getDesignByFolderIdForAdmin*"),['']));


      }

      /*** DeleteCacheKey **/

      //generate video templates static page
      if ($api == '/api/generateVideoStaticPageByAdminV2' or $api == '/api/generateVideoStaticPageByAdmin' or $api == '/api/editVideoStaticPageByAdminV2' or $api == '/api/editVideoStaticPageByAdmin' or $api == '/api/setStaticPageRankByAdmin' ) {
        //getVideoStaticPageTagListByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoStaticPageTagListByAdmin*"),['']));

        //getStaticPageTemplateListByTag
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getStaticPageTemplateListByTag*"),['']));

        //getLeftNavigation
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getLeftNavigation*"),['']));

        //generateAllStaticPage
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":generateAllStaticPage*"),['']));

        //getAllVideoStaticPageTagListByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllVideoStaticPageTagListByAdmin*"),['']));

        //getAllStaticPageURL
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllStaticPageURL*"),['']));

        //getPageFromTag
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPageFromTag*"),['']));

        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSubPages*"),['']));
      }

      //User publish design
      if ($api == '/api/publishDesignByUser') {
        //getUserPublishDesignForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUserPublishDesignForAdmin*"),['']));
      }

      //Discount payment module [product]
      if ($api == '/api/createProduct' or $api == '/api/updateProduct' or $api == '/api/deleteProduct') {
        //getAllProducts
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getAllProducts*"),['']));

        //getPricingByUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingByUser*"),['']));

        //getPricingForStaticPage
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingForStaticPage*"),['']));

        //getPricingByProduct
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingByProduct*"),['']));
      }

      //Discount payment module [pricing]
      if ($api == '/api/addPricingToProduct' or $api == '/api/updatePricingToProduct' or $api == '/api/deletePricingToProduct') {
        //getPricingByProduct
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingByProduct*"),['']));

        //getPricingByUser
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingByUser*"),['']));

        //getPricingForStaticPage
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPricingForStaticPage*"),['']));
      }

      //Download Intro-Video module
      if ($api == '/api/checkReadyToDownload' or $api == '/api/generateVideo') {
        //getGenerateVideoReportForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getGenerateVideoReportForAdmin*"),['']));
      }

      if ($api == '/api/getStaticPageTemplateListByTag') {
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getVideoStaticPageTagListByAdmin*"),['']));
      }

      //Similar pages
      if ($api == '/api/deleteSimilarTemplatePage' or $api == '/api/updateSimilarTemplatePage') {
        //getGenerateVideoReportForAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getSimilarPages*"),['']));
      }

      //Marketing calender
      if ($api == '/api/addPostSuggestionByAdmin' or $api == '/api/updatePostSuggestionByAdmin' or $api == '/api/deletePostSuggestionByAdmin' or $api == '/api/setPostSuggestionRankOnTheTopByAdmin' or $api == '/api/setPostScheduleByAdmin' or $api == '/api/deletePostScheduleByAdmin' or $api == '/api/updateRelatedTags' or $api == '/api/deleteRelatedTags') {

        //getPostSuggestionByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPostSuggestionByAdmin*"),['']));

        //getPostScheduleDetailByAdmin
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPostScheduleDetailByAdmin*"),['']));

        //getPostScheduleList
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getPostScheduleList*"),['']));

        //getUpcomingEvents
        Redis::del(array_merge(Redis::keys(Config::get("constant.REDIS_KEY").":getUpcomingEvents*"),['']));
      }

    } catch (Exception $e) {
      Log::error("DeleteCacheKey Middleware : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete cache key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
  }
}
