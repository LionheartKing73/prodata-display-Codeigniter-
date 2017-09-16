<?php
// Generated on 11/16/2015 7:01:10 AM

namespace BingAds\v10\Bulk
{
    use DateTime;

    final class BulkServiceSettings
    {
        const ServiceNamespace = 'https://bingads.microsoft.com/CampaignManagement/v10';
        const ProductionEndpoint = 'https://bulk.api.bingads.microsoft.com/Api/Advertiser/CampaignManagement/V10/BulkService.svc';
        const SandboxEndpoint = 'https://bulk.api.sandbox.bingads.microsoft.com/Api/Advertiser/CampaignManagement/V10/BulkService.svc';
    }

    /**
     * Defines the possible compression types for the file to download.
     * @link http://msdn.microsoft.com/en-us/library/mt179363(v=msads.100).aspx CompressionType Value Set
     * 
     * @used-by DownloadCampaignsByAccountIdsRequest
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class CompressionType
    {
        /** The file should be ZIP compressed. */
        const Zip = 'Zip';

        /** The file should be GZIP compressed. */
        const GZip = 'GZip';
    }

    /**
     * Defines the scope or types of data to download.
     * @link http://msdn.microsoft.com/en-us/library/dn249976(v=msads.100).aspx DataScope Value Set
     * 
     * @used-by DownloadCampaignsByAccountIdsRequest
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class DataScope
    {
        /** Download the entity attributes records. */
        const EntityData = 'EntityData';

        /** Download the performance data fields for the corresponding entity records. */
        const EntityPerformanceData = 'EntityPerformanceData';

        /** Download the quality score fields for the corresponding entity records. */
        const QualityScoreData = 'QualityScoreData';

        /** Download the bid suggestions records. */
        const BidSuggestionsData = 'BidSuggestionsData';
    }

    /**
     * Defines the file formats for a download request.
     * @link http://msdn.microsoft.com/en-us/library/jj919219(v=msads.100).aspx DownloadFileType Value Set
     * 
     * @used-by DownloadCampaignsByAccountIdsRequest
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class DownloadFileType
    {
        /** The file format is comma separated values (CSV). */
        const Csv = 'Csv';

        /** The file format is tab separated values (TSV). */
        const Tsv = 'Tsv';
    }

    /**
     * Defines the entities that may be downloaded in bulk.
     * @link http://msdn.microsoft.com/en-us/library/dn249982(v=msads.100).aspx BulkDownloadEntity Value Set
     * 
     * @used-by DownloadCampaignsByAccountIdsRequest
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class BulkDownloadEntity
    {
        /** Include campaigns in the download data. */
        const Campaigns = 'Campaigns';

        /** Include ad groups in the download data. */
        const AdGroups = 'AdGroups';

        /** Include ads in the download data. */
        const Ads = 'Ads';

        /** Include keywords in the download data. */
        const Keywords = 'Keywords';

        /** Include a campaign's negative keywords in the download data. */
        const CampaignNegativeKeywords = 'CampaignNegativeKeywords';

        /** Include an ad group's negative keywords in the download data. */
        const AdGroupNegativeKeywords = 'AdGroupNegativeKeywords';

        /** Include a campaign's targets in the download data. */
        const CampaignTargets = 'CampaignTargets';

        /** Include an ad group's targets in the download data. */
        const AdGroupTargets = 'AdGroupTargets';

        /** Include a campaign's negative site URLs in the download data. */
        const CampaignNegativeSites = 'CampaignNegativeSites';

        /** Include an ad group's negative site URLs in the download data. */
        const AdGroupNegativeSites = 'AdGroupNegativeSites';

        /** Include a record in the download that represents the association relationship between a campaign and a sitelink ad extension. */
        const CampaignSiteLinksAdExtensions = 'CampaignSiteLinksAdExtensions';

        /** Include a record in the download that represents the association relationship between a campaign and a location ad extension. */
        const CampaignLocationAdExtensions = 'CampaignLocationAdExtensions';

        /** Include a record in the download that represents the association relationship between a campaign and a call ad extension. */
        const CampaignCallAdExtensions = 'CampaignCallAdExtensions';

        /** Include a record in the download that represents the association relationship between an ad group and a sitelink ad extension. */
        const AdGroupSiteLinksAdExtensions = 'AdGroupSiteLinksAdExtensions';

        /** Include location ad extension library items in the download data. */
        const LocationAdExtensions = 'LocationAdExtensions';

        /** Include call ad extension library items in the download data. */
        const CallAdExtensions = 'CallAdExtensions';

        /** Include site links ad extension library items in the download data. */
        const SiteLinksAdExtensions = 'SiteLinksAdExtensions';

        /** Include negative keyword list library items in the download data. */
        const NegativeKeywordLists = 'NegativeKeywordLists';

        /** Include the negative keywords of a negative keyword list in the download data. */
        const SharedNegativeKeywords = 'SharedNegativeKeywords';

        /** Include a record in the download that represents the association relationship between a campaign and a negative keyword list. */
        const CampaignNegativeKeywordListAssociations = 'CampaignNegativeKeywordListAssociations';

        /** Include image ad extension library items in the download data. */
        const ImageAdExtensions = 'ImageAdExtensions';

        /** Include a record in the download that represents the association relationship between a campaign and an image ad extension. */
        const CampaignImageAdExtensions = 'CampaignImageAdExtensions';

        /** Include a record in the download that represents the association relationship between an ad group and an image ad extension. */
        const AdGroupImageAdExtensions = 'AdGroupImageAdExtensions';

        /** Include app ad extension library items in the download data. */
        const AppAdExtensions = 'AppAdExtensions';

        /** Include a record in the download that represents the association relationship between an ad group and an app ad extension. */
        const AdGroupAppAdExtensions = 'AdGroupAppAdExtensions';

        /** Include a record in the download that represents the association relationship between a campaign and an app ad extension. */
        const CampaignAppAdExtensions = 'CampaignAppAdExtensions';

        /** Include an ad group's product partitions in the download data. */
        const AdGroupProductPartitions = 'AdGroupProductPartitions';

        /** Include a campaign's product scopes in the download data. */
        const CampaignProductScopes = 'CampaignProductScopes';
    }

    /**
     * Defines the date range values for the requested performance data in a bulk download.
     * @link http://msdn.microsoft.com/en-us/library/dn249977(v=msads.100).aspx ReportTimePeriod Value Set
     * 
     * @used-by PerformanceStatsDateRange
     */
    final class ReportTimePeriod
    {
        /** Performance data for the current day. */
        const Today = 'Today';

        /** Performance data for the previous day. */
        const Yesterday = 'Yesterday';

        /** Performance data for the previous seven days, one row for each day. */
        const LastSevenDays = 'LastSevenDays';

        /** Performance data for the current calendar week. */
        const ThisWeek = 'ThisWeek';

        /** Performance data for the previous calendar week. */
        const LastWeek = 'LastWeek';

        /** Performance data for the four calendar weeks prior to today. */
        const LastFourWeeks = 'LastFourWeeks';

        /** Performance data for the current calendar month. */
        const ThisMonth = 'ThisMonth';

        /** Performance data for the previous calendar month. */
        const LastMonth = 'LastMonth';

        /** Performance data for the previous three calendar months. */
        const LastThreeMonths = 'LastThreeMonths';

        /** Performance data for the previous six calendar months. */
        const LastSixMonths = 'LastSixMonths';

        /** Performance data for the current calendar year. */
        const ThisYear = 'ThisYear';

        /** Performance data for the previous calendar year. */
        const LastYear = 'LastYear';
    }

    /**
     * Defines elements to specify whether the bulk service should return upload errors with their corresponding data.
     * @link http://msdn.microsoft.com/en-us/library/dn249983(v=msads.100).aspx ResponseMode Value Set
     * 
     * @used-by GetBulkUploadUrlRequest
     */
    final class ResponseMode
    {
        /** Return errors only in the bulk upload response file. */
        const ErrorsOnly = 'ErrorsOnly';

        /** Return errors and results in the bulk upload response file. */
        const ErrorsAndResults = 'ErrorsAndResults';
    }

    /**
     * Defines an error object that contains the details that explain why the service operation failed.
     * @link http://msdn.microsoft.com/en-us/library/dn169097(v=msads.100).aspx AdApiError Data Object
     * 
     * @used-by AdApiFaultDetail
     */
    final class AdApiError
    {
        /**
         * A numeric error code that identifies the error.
         * @var integer
         */
        public $Code;

        /**
         * A message that contains additional details about the error.
         * @var string
         */
        public $Detail;

        /**
         * A symbolic string constant that identifies the error.
         * @var string
         */
        public $ErrorCode;

        /**
         * A message that describes the error.
         * @var string
         */
        public $Message;
    }

    /**
     * Defines the base object from which all fault detail objects derive.
     * @link http://msdn.microsoft.com/en-us/library/dn169093(v=msads.100).aspx ApplicationFault Data Object
     */
    class ApplicationFault
    {
        /**
         * The identifier of the log entry that contains the details of the API call.
         * @var string
         */
        public $TrackingId;
    }

    /**
     * Defines a fault object that operations return when generic errors occur, such as an authentication error.
     * @link http://msdn.microsoft.com/en-us/library/dn169095(v=msads.100).aspx AdApiFaultDetail Data Object
     * 
     * @uses AdApiError
     */
    final class AdApiFaultDetail extends ApplicationFault
    {
        /**
         * An array of AdApiError objects that contains the details that explain why the service operation failed.
         * @var AdApiError[]
         */
        public $Errors;
    }

    /**
     * Defines a fault object that operations return when web service-specific errors occur, such as when the request message contains incomplete or invalid data.
     * @link http://msdn.microsoft.com/en-us/library/dn169094(v=msads.100).aspx ApiFaultDetail Data Object
     * 
     * @uses BatchError
     * @uses OperationError
     */
    final class ApiFaultDetail extends ApplicationFault
    {
        /**
         * An array of batch error objects that identifies the items in the batch of items in the request message that caused the operation to fail.
         * @var BatchError[]
         */
        public $BatchErrors;

        /**
         * An array of operation error objects that contains the reasons that explain why the service operation failed when the error is not related to a specific item in the batch of items.
         * @var OperationError[]
         */
        public $OperationErrors;
    }

    /**
     * Defines an error object that identifies the item within the batch of items in the request message that caused the operation to fail, and describes the reason for the failure.
     * @link http://msdn.microsoft.com/en-us/library/dn169096(v=msads.100).aspx BatchError Data Object
     * 
     * @uses KeyValuePairOfstringstring
     * @used-by ApiFaultDetail
     */
    class BatchError
    {
        /**
         * A numeric error code that identifies the error.
         * @var integer
         */
        public $Code;

        /**
         * A message that provides additional details about the batch error.
         * @var string
         */
        public $Details;

        /**
         * A symbolic string constant that identifies the error.
         * @var string
         */
        public $ErrorCode;

        /**
         * Reserved for future use.
         * @var string
         */
        public $FieldPath;

        /**
         * The list of key and value strings for forward compatibility.
         * @var KeyValuePairOfstringstring[]
         */
        public $ForwardCompatibilityMap;

        /**
         * The zero-based index of the item in the batch of items in the request message that failed.
         * @var integer
         */
        public $Index;

        /**
         * A message that describes the error.
         * @var string
         */
        public $Message;

        /**
         * Reserved for future use.
         * @var string
         */
        public $Type;
    }

    /**
     * Defines an object that identifies a campaign to download.
     * @link http://msdn.microsoft.com/en-us/library/jj134990(v=msads.100).aspx CampaignScope Data Object
     * 
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class CampaignScope
    {
        /**
         * The identifier of the campaign to download.
         * @var integer
         */
        public $CampaignId;

        /**
         * The identifier of the account that owns the campaign to download.
         * @var integer
         */
        public $ParentAccountId;
    }

    /**
     * Defines a calendar date by month, day, and year.
     * @link http://msdn.microsoft.com/en-us/library/jj134989(v=msads.100).aspx Date Data Object
     * 
     * @used-by PerformanceStatsDateRange
     */
    final class Date
    {
        /**
         * Specifies the day of the month.
         * @var integer
         */
        public $Day;

        /**
         * Specifies the month.
         * @var integer
         */
        public $Month;

        /**
         * Specifies the year.
         * @var integer
         */
        public $Year;
    }

    /**
     * Defines an error object that identifies the entity with the batch of entities that failed editorial review.
     * @link http://msdn.microsoft.com/en-us/library/dn249981(v=msads.100).aspx EditorialError Data Object
     */
    final class EditorialError extends BatchError
    {
        /**
         * Reserved for future use.
         * @var boolean
         */
        public $Appealable;

        /**
         * The text that caused the entity to be disapproved.
         * @var string
         */
        public $DisapprovedText;

        /**
         * The element or property of the entity that caused the entity to be disapproved.
         * @var string
         */
        public $Location;

        /**
         * The corresponding country or region for the flagged editorial issue.
         * @var string
         */
        public $PublisherCountry;

        /**
         * A numeric code that identifies the error.
         * @var integer
         */
        public $ReasonCode;
    }

    final class KeyValuePairOfstringstring
    {
        public $key;
        public $value;
    }

    /**
     * Defines an error object that contains the details that explain why the service operation failed.
     * @link http://msdn.microsoft.com/en-us/library/dn169098(v=msads.100).aspx OperationError Data Object
     * 
     * @used-by ApiFaultDetail
     * @used-by GetBulkDownloadStatusResponse
     * @used-by GetBulkUploadStatusResponse
     */
    final class OperationError
    {
        /**
         * A numeric error code that identifies the error
         * @var integer
         */
        public $Code;

        /**
         * A message that provides additional details about the error.
         * @var string
         */
        public $Details;

        /**
         * A symbolic string constant that identifies the error.
         * @var string
         */
        public $ErrorCode;

        /**
         * A message that describes the error.
         * @var string
         */
        public $Message;
    }

    /**
     * Defines the date range values for the requested performance data in a bulk download.
     * @link http://msdn.microsoft.com/en-us/library/dn249975(v=msads.100).aspx PerformanceStatsDateRange Data Object
     * 
     * @uses Date
     * @uses ReportTimePeriod
     * @used-by DownloadCampaignsByAccountIdsRequest
     * @used-by DownloadCampaignsByCampaignIdsRequest
     */
    final class PerformanceStatsDateRange
    {
        /**
         * The end date of the custom date range.
         * @var Date
         */
        public $CustomDateRangeEnd;

        /**
         * The start date of the custom date range.
         * @var Date
         */
        public $CustomDateRangeStart;

        /**
         * A predefined date range value.
         * @var ReportTimePeriod
         */
        public $PredefinedTime;
    }

    /**
     * Downloads an account's campaign data.
     * @link http://msdn.microsoft.com/en-us/library/jj885755(v=msads.100).aspx DownloadCampaignsByAccountIds Request Object
     * 
     * @uses CompressionType
     * @uses DataScope
     * @uses DownloadFileType
     * @uses BulkDownloadEntity
     * @uses PerformanceStatsDateRange
     * @used-by BingAdsBulkService::DownloadCampaignsByAccountIds
     */
    final class DownloadCampaignsByAccountIdsRequest
    {
        public $AccountIds;
        public $CompressionType;
        public $DataScope;
        public $DownloadFileType;
        public $Entities;
        public $FormatVersion;
        public $LastSyncTimeInUTC;
        public $PerformanceStatsDateRange;
    }

    /**
     * Downloads an account's campaign data.
     * @link http://msdn.microsoft.com/en-us/library/jj885755(v=msads.100).aspx DownloadCampaignsByAccountIds Response Object
     * 
     * @used-by BingAdsBulkService::DownloadCampaignsByAccountIds
     */
    final class DownloadCampaignsByAccountIdsResponse
    {
        public $DownloadRequestId;
    }

    /**
     * Downloads the specified campaigns' data.
     * @link http://msdn.microsoft.com/en-us/library/jj885756(v=msads.100).aspx DownloadCampaignsByCampaignIds Request Object
     * 
     * @uses CampaignScope
     * @uses CompressionType
     * @uses DataScope
     * @uses DownloadFileType
     * @uses BulkDownloadEntity
     * @uses PerformanceStatsDateRange
     * @used-by BingAdsBulkService::DownloadCampaignsByCampaignIds
     */
    final class DownloadCampaignsByCampaignIdsRequest
    {
        public $Campaigns;
        public $CompressionType;
        public $DataScope;
        public $DownloadFileType;
        public $Entities;
        public $FormatVersion;
        public $LastSyncTimeInUTC;
        public $PerformanceStatsDateRange;
    }

    /**
     * Downloads the specified campaigns' data.
     * @link http://msdn.microsoft.com/en-us/library/jj885756(v=msads.100).aspx DownloadCampaignsByCampaignIds Response Object
     * 
     * @used-by BingAdsBulkService::DownloadCampaignsByCampaignIds
     */
    final class DownloadCampaignsByCampaignIdsResponse
    {
        public $DownloadRequestId;
    }

    /**
     * Gets the status of a bulk download request.
     * @link http://msdn.microsoft.com/en-us/library/jj885754(v=msads.100).aspx GetBulkDownloadStatus Request Object
     * 
     * @used-by BingAdsBulkService::GetBulkDownloadStatus
     */
    final class GetBulkDownloadStatusRequest
    {
        public $RequestId;
    }

    /**
     * Gets the status of a bulk download request.
     * @link http://msdn.microsoft.com/en-us/library/jj885754(v=msads.100).aspx GetBulkDownloadStatus Response Object
     * 
     * @uses OperationError
     * @uses KeyValuePairOfstringstring
     * @used-by BingAdsBulkService::GetBulkDownloadStatus
     */
    final class GetBulkDownloadStatusResponse
    {
        public $Errors;
        public $ForwardCompatibilityMap;
        public $PercentComplete;
        public $RequestStatus;
        public $ResultFileUrl;
    }

    /**
     * Gets the status and completion progress of a bulk upload request.
     * @link http://msdn.microsoft.com/en-us/library/dn249979(v=msads.100).aspx GetBulkUploadStatus Request Object
     * 
     * @used-by BingAdsBulkService::GetBulkUploadStatus
     */
    final class GetBulkUploadStatusRequest
    {
        public $RequestId;
    }

    /**
     * Gets the status and completion progress of a bulk upload request.
     * @link http://msdn.microsoft.com/en-us/library/dn249979(v=msads.100).aspx GetBulkUploadStatus Response Object
     * 
     * @uses OperationError
     * @uses KeyValuePairOfstringstring
     * @used-by BingAdsBulkService::GetBulkUploadStatus
     */
    final class GetBulkUploadStatusResponse
    {
        public $Errors;
        public $ForwardCompatibilityMap;
        public $PercentComplete;
        public $RequestStatus;
        public $ResultFileUrl;
    }

    /**
     * Submits a request for a URL where a bulk upload file may be posted.
     * @link http://msdn.microsoft.com/en-us/library/dn249978(v=msads.100).aspx GetBulkUploadUrl Request Object
     * 
     * @uses ResponseMode
     * @used-by BingAdsBulkService::GetBulkUploadUrl
     */
    final class GetBulkUploadUrlRequest
    {
        public $ResponseMode;
        public $AccountId;
    }

    /**
     * Submits a request for a URL where a bulk upload file may be posted.
     * @link http://msdn.microsoft.com/en-us/library/dn249978(v=msads.100).aspx GetBulkUploadUrl Response Object
     * 
     * @used-by BingAdsBulkService::GetBulkUploadUrl
     */
    final class GetBulkUploadUrlResponse
    {
        public $RequestId;
        public $UploadUrl;
    }
}
