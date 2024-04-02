<?php

namespace App\Models;

use App\Traits\MongoDate;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;


class AdminLog extends Model
{
    use SoftDeletes, MongoDate;

    const        CREATE_ADMIN_USER_ACTION = "create_admin_user";
    const        UPDATE_ADMIN_USER_ACTION = "update_admin_user";
    const        DELETE_ADMIN_USER_ACTION = "delete_admin_user";
    const        CREATE_ANNOUNCEMENT = "create_announcement";
    const        UPDATE_ANNOUNCEMENT = "update_announcement";
    const        DELETE_ANNOUNCEMENT = "delete_announcement";
    const        CREATE_BANK_ACCOUNT = "create_bank_account";
    const        UPDATE_BANK_ACCOUNT = "update_bank_account";
    const        DELETE_BANK_ACCOUNT = "delete_bank_account";
    const        BLOG_REPLY_COMMENT = "blog_reply_comment";
    const        BLOG_APPROVE_COMMENT = "blog_approve_comment";
    const        CREATE_BLOG = "create_blog";
    const        UPDATE_BLOG = "update_blog";
    const        DELETE_BLOG = "delete_blog";
    const        DELETE_BLOG_COMMENT = "delete_blog_comment";
    const        UPDATE_BLOG_COMMENT = "update_blog_comment";
    const        CREATE_BUSINESS_PARTNER = "create_business_partner";
    const        CREATE_CAREER_JOB = "create_career_job";
    const        UPDATE_CAREER_APPLICATION = "update_career_application";
    const        UPDATE_CAREER_JOB = "update_career_job";
    const        CLIENT_DOMAIN_UPDATE_NS = "update_client_domain_ns";
    const        CLIENT_DOMAIN_UPDATE = "update_client_domain";
    const        CLIENT_DOMAIN_TRANSFER = "transfer_client_domain";
    const        CLIENT_DOMAIN_ADD_RECORD = "add_dns_record_client_domain";
    const        CLIENT_DOMAIN_DELETE_RECORD = "delete_dns_record_client_domain";
    const        CLIENT_DOMAIN_UPDATE_RECORD = "update_dns_record_client_domain";
    const        CREATE_CONFIG = "create_config";
    const        UPDATE_CONFIG = "update_config";
    const        CREATE_DNS_RECORD = "create_dns_record";
    const        CREATE_DNS_ZONE = "create_dns_zone";
    const        DELETE_DNS_ZONE = "delete_dns_zone";
    const        DELETE_DNS_RECORD = "delete_dns_record";
    const        UPDATE_DNS_RECORD = "update_dns_record";
    const        UPDATE_DNS_ZONE = "update_dns_zone";
    const        UPDATE_DOMAIN_INFORMATION = "update_domain_information";
    const        UPDATE_ENQUIRY = "update_enquiry";
    const        DELETE_ENQUIRY = "delete_enquiry";
    const        ADD_CREDIT = "add_credit";
    const        DECREASE_CREDIT = "decrease_credit";
    const        CREATE_INVOICE = "create_invoice";
    const        ADD_CREDIT_TO_INVOICE = "add_credit_to_invoice";
    const        ADD_INVOICE_ITEM = "add_invoice_item";
    const        ADD_INVOICE_TRANSACTION = "add_transaction_to_invoice";
    const        INVOICE_APPLY_CREDIT = "apply_credit_to_invoice";
    const        CANCEL_INVOICE = "cancel_invoice";
    const        UPDATE_INVOICE_STATUS = "update_invoice_status";
    const        DELETE_INVOICE_FROM_RAHKARAN = "delete_invoice_from_rahkaran";
    const        DELETE_INVOICE_ITEM = "delete_invoice_item";
    const        EDIT_INVOICE_ITEM = "edit_invoice_item";
    const        PAY_INVOICE = "pay_invoice";
    const        REFUNDED_INVOICE = "refunded_invoice";
    const        SEND_INVOICE_TO_RAHKARAN = "send_invoice_to_rahkaran";
    const        UNPAID_INVOICE = "unpaid_invoice";
    const        UPDATE_INVOICE = "update_invoice";
    public const SPLIT_INVOICE = 'split_invoice';
    const        DELETE_INVOICE_OFFICIAL_BILL = "delete_invoice_official_bill";
    const        DOWNLOAD_INVOICE_OFFICIAL_BILL = "download_invoice_official_bill";
    const        CREATE_OFFLINE_PAYMENT = "create_offline_payment";
    const        DELETE_OFFLINE_PAYMENT = "delete_offline_payment";
    const        REJECT_OFFLINE_PAYMENT = "reject_offline_payment";
    const        ROLLBACK_OFFLINE_PAYMENT = "rollback_offline_payment";
    const        UPDATE_OFFLINE_PAYMENT = "update_offline_payment";
    const        VERIFY_OFFLINE_PAYMENT = "verify_offline_payment";
    const        DELETE_HOSTIRAN_PROXY = "delete_hostiran_proxy";
    const        UPDATE_HOSTIRAN_PROXY = "update_hostiran_proxy";
    const        CREATE_INFORMATION_SYSTEM_CATEGORY = "create_information_system_category";
    const        DELETE_INFORMATION_SYSTEM_CATEGORY = "delete_information_system_category";
    const        UPDATE_INFORMATION_SYSTEM_CATEGORY = "update_information_system_category";
    const        CREATE_INFORMATION_SYSTEM = "create_information_system";
    const        DELETE_INFORMATION_SYSTEM = "delete_information_system";
    const        UPDATE_INFORMATION_SYSTEM = "update_information_system";
    const        CREATE_MANAGE_SERVICE_NOTE = "create_manage_service_note";
    const        DELETE_MANAGE_SERVICE_NOTE = "delete_manage_service_note";
    const        UPDATE_MANAGE_SERVICE_NOTE = "update_manage_service_note";
    const        CREATE_MANAGE_SERVICE_TRIGGER = "create_manage_service_trigger";
    const        DELETE_MANAGE_SERVICE_TRIGGER = "delete_manage_service_trigger";
    const        UPDATE_MANAGE_SERVICE_TRIGGER = "update_manage_service_trigger";
    const        CREATE_MANAGE_SERVICE = "create_manage_service";
    const        DELETE_MANAGE_SERVICE = "delete_manage_service";
    const        UPDATE_MANAGE_SERVICE = "update_manage_service";
    const        CREATE_MANAGE_SERVICE_PLAN = "create_manage_service_plan";
    const        UPDATE_MANAGE_SERVICE_PLAN = "update_manage_service_plan";
    const        DELETE_MANAGE_SERVICE_PLAN = "delete_manage_service_plan";
    const        ACCEPT_ORDER = "accept_order";
    const        APPLY_ORDER = "apply_order";
    const        CANCEL_ORDER = "cancel_order";
    const        ADD_ORDER_ITEM = "add_order_item";
    const        DELETE_ORDER_ITEM = "delete_order_item";
    const        EDIT_ORDER_ITEM = "edit_order_item";
    const        FRAUD_ORDER = "set_fraud_order";
    const        PENDING_ORDER = "set_pending_order";
    const        CREATE_PRODUCT_GROUP_ATTRIBUTE = "create_product_group_attribute";
    const        CREATE_PRODUCT_GROUP = "create_product_group";
    const        DELETE_PRODUCT_GROUP_ATTRIBUTE = "delete_product_group_attribute";
    const        UPDATE_PRODUCT_GROUP_ATTRIBUTE = "update_product_group_attribute";
    const        UPDATE_PRODUCT_GROUP = "update_product_group";
    const        CREATE_PRODUCT_TEMPLATE = "create_product_template";
    const        UPDATE_PRODUCT_TEMPLATE = "update_product_template";
    const        CREATE_PRODUCT_ATTRIBUTE = "create_product_attribute";
    const        CREATE_PRODUCT = "create_product";
    const        DELETE_PRODUCT_ATTRIBUTE = "delete_product_attribute";
    const        DELETE_PRODUCT = "delete_product";
    const        UPDATE_PRODUCT_ATTRIBUTE = "update_product_attribute";
    const        UPDATE_PRODUCT = "update_product";
    const        UPDATE_PRODUCT_SUGGESTIONS = "update_product_suggestions";
    const        CPANEL_SSO = "cpanel_single_sign_in";
    const        SERVICE_MODULE_CHANGE_PASSWORD = "service_module_change_password";
    const        SERVICE_CREATE_MODULE = "service_create_module";
    const        SERVICE_MODULE_ACTION = "service_module_action";
    const        SERVICE_SUSPEND_ACTION = "service_suspend_action";
    const        SERVICE_TERMINATE_ACTION = "service_terminate_action";
    const        SERVICE_UNSUSPEND_ACTION = "service_unsuspend_action";
    const        SERVICE_RENEW_ACTION = "renew_service_action";
    const        SERVICE_TRANSFER_OWNERSHIP = "service_transfer_ownership";
    const        SERVICE_UPDATE_SERVER = "update_service_server";
    const        TRANSFER_SERVER_OWNERSHIP = "transfer_server_ownership";
    const        UPDATE_SERVER_CONFIG = "update_server_config";
    const        SERVICE_UPDATE = "update_service";
    const        ADD_CLIENT_NOTE = "add_client_note";
    const        ADD_USER_TO_CLIENT = "add_user_to_client";
    const        TRANSFER_ALL_SERVICES = "transfer_all_services";
    const        CREATE_CLIENT = "create_client";
    const        REMOVE_USER_FROM_CLIENT = "remove_user_from_client";
    const        LOGIN_TO_CLOUD = "login_to_cloud";
    const        UPDATE_CLIENT = "update_client";
    const        UPDATE_CLIENT_GROUP = "update_client_group";
    const        UPDATE_USER_CLIENT_PERMISSIONS = "update_user_client_permissions";
    const        UPDATE_USER_DOCUMENTS = "update_user_documents";
    const        REGISTER_USER = "register_user";
    const        LOGIN_WITH_USER = "login_with_user";
    const        CHANGE_USER_PASSWORD = "change_user_password";
    const        UPDATE_USER = "update_user";
    const        CREATE_ORDER = "create_order";
    const        UPDATE_CLOUD_BUCKET = "update_cloud_bucket";
    const        HETZNER_STORE_SERVER = "hetzner_store_server";
    const        ACTION_ON_SERVER = "action_on_cloud_service";
    const        SNAPSHOT_CLOUD_SERVICE = "snapshot_cloud_service";
    const        CLOUD_TERMINATE_SERVER = "terminate_cloud_service";
    const        CREATE_GIFT_CODE = "create_gift_code";
    const        UPDATE_GIFT_CODE = "update_gift_code";
    const        DESTROY_GIFT_CODE = "delete_gift_code";
    const        CREATE_SERVICE_DISCOUNT = "create_service_discount";
    const        CREATE_DOMAIN_DISCOUNT = "create_domain_discount";
    const        UPDATE_DISCOUNT = "update_discount";
    const        DESTROY_DISCOUNT = "delete_discount";
    const        CREATE_SERVER_STATUS = "create_server_status";
    const        DELETE_SERVER_STATUS = "delete_server_status";
    const        UPDATE_SERVER_STATUS = "update_server_status";
    const        UPDATE_DATA_CENTER_ANNOUNCEMENT = "update_data_center_announcement";
    const        DELETE_DATA_CENTER_ANNOUNCEMENT = "delete_data_center_announcement";
    const        CREATE_DATA_CENTER_ANNOUNCEMENT = "create_data_center_announcement";
    const        UPDATE_SERVER_STATUS_MANUALLY = "update_server_manually";
    const        UPDATE_TLD_PRICING = "update_tld_pricing";
    const        DOMAIN_RENEW_ACTION = "renew_domain_action";
    public const RESTART_PROVISIONING_QUEUE = 'restart_provisioning_queue';
    public const SYNC_PROVISIONING_QUEUE = 'sync_provisioning_queue';
    public const UPDATE_DERAK_PLAN = 'update_derak_plan';
    public const CREATE_DERAK_PLAN = 'create_derak_plan';
    public const DELETE_DERAK_PLAN = 'delete_derak_plan';
    public const DEACTIVATE_CDN = 'deactivate_cdn';
    public const CANCEL_CDN = 'cancel_cdn';
    public const ACTIVATE_CDN = 'activate_cdn';
    public const PAUSE_CDN = 'pause_cdn';
    public const CREATE_TRIAL_CDN = 'create_trial_cdn';
    const        REGISTER_S3 = "register_s3_service";
    const        LOGIN_S3 = "login_to_s3";
    const        UNSUSPEND_S3 = "unsuspend_s3";
    const        TERMINATE_S3 = "terminate_s3";
    const        CREATE_CLIENT_NOTE = "create_client_note";
    const        DELETE_CLIENT_NOTE = "delete_client_note";
    const        UPDATE_CLIENT_NOTE = "update_client_note";
    const        PROVISIONING_MODULE_UPDATE = "update_provisioning_module";
    const        MANUAL_CHECK_INVOICE = "manual_check_invoice";
    const        MIGRATE_HOSTIRAN_POOL = "migrate_hostiran_pool";

    const        STORE_IP_POOL = "store_ip_pool";
    const        UPDATE_IP_POOL = "update_ip_pool";
    const        STORE_RATING = "STORE_RATING";
    const        DELETE_RATE_LOG = "DELETE_RATE_LOG";
    const        STORE_TEMPLATE = "store_template";
    const        UPDATE_TEMPLATE = "update_template";
    public const CREATE_DOMAIN_PRODUCT_SUGGESTION = "create_domain_product_suggestion";
    public const DELETE_DOMAIN_PRODUCT_SUGGESTION = "delete_domain_product_suggestion";
    public const UPDATE_DOMAIN_PRODUCT_SUGGESTION = "update_domain_product_suggestion";
    const        CLOUD_MANUAL_INVOICE = 'cloud_manual_invoice';
    const        CLOUD_MANUAL_CHARGE = 'cloud_manual_charge';
    const        CLOUD_RESTORE = 'cloud_restore';
    const        CLOUD_UPDATE_SERVER_INFO = 'update_server_info';
    const        CLOUD_TERMINATE_BACKUP = 'cloud_terminate_backup';
    const        CLOUD_RECOVERY_BACKUP = 'cloud_recovery_backup';
    const        CLOUD_STORE_BACKUP = 'cloud_store_backup';
    const        CLOUD_TERMINATE_FLOATING_IP = 'cloud_terminate_floating_ip';
    const        CLOUD_UPDATE_FLOATING_IP = 'cloud_update_floating_ip';
    const        CLOUD_REVERS_DNS_FLOATING_IP = 'cloud_revers_dns_floating_ip';
    const        CHANGE_IP_FLOATING_IP = 'change_ip_floating_ip';
    const        CLOUD_STORE_FLOATING_IP = 'cloud_store_floating_ip';
    const        CLOUD_TERMINATE_HETZNER_SNAPSHOT = 'cloud_terminate_hetzner_snapshot';
    const        CLOUD_TERMINATE_HOSTIRAN_SNAPSHOT = 'cloud_terminate_hostiran_snapshot';
    const        CLOUD_REVERT_HOSTIRAN_SNAPSHOT = 'cloud_revert_hostiran_snapshot';
    const        CLOUD_STORE_SNAPSHOT = 'cloud_store_snapshot';
    const        CLOUD_HOSTIRAN_UPGRADE = 'cloud_hostiran_upgrade';
    const        CLOUD_HETZNER_UPGRADE = 'cloud_hetzner_upgrade';
    const        CREATE_BUCKET_COUPON = 'create_bucket_coupon';
    const        UPDATE_BUCKET_COUPON = 'update_bucket_coupon';
    const        UPDATE_HOSTIRAN_NODE = 'update_hostiran_node';
    const        STORE_HOSTIRAN_NODE = 'store_hostiran_node';
    const        DELETE_HOSTIRAN_NODE = 'delete_hostiran_node';
    const        CREATE_CALL_HISTORY = 'create_call_history';
    const        CLOUD_LOGIN_WITH_USER = 'cloud_login_with_user';
    const        DELETE_CASHOUT = 'delete_cashout';
    const        ACTION_ON_CASHOUT = 'action_on_cashout';
    const        CREATE_CASHOUT = 'create_cashout';
    const        UPDATE_CASHOUT = 'update_cashout';
    const        STORE_HOSTIRAN_DATACENTER = 'STORE_HOSTIRAN_DATACENTER';
    const        UPDATE_HOSTIRAN_DATACENTER = 'UPDATE_HOSTIRAN_DATACENTER';
    const        STORE_BANDWIDTH = 'store_bandwidth';
    const        UPDATE_BANDWIDTH = 'update_bandwidth';
    const        DELETE_BANDWIDTH = 'delete_bandwidth';
    const        UPDATE_VM_BANDWIDTH = 'update_vm_bandwidth';
    const        DELETE_VM_BANDWIDTH = 'delete_vm_bandwidth';
    const        STORE_CLOUD_PACKAGE = 'store_cloud_package';
    const        UPDATE_CLOUD_PACKAGE = 'update_cloud_package';
    const        DELETE_CLOUD_PACKAGE = 'delete_cloud_package';
    const        STORE_VOLUME_PLAN = 'store_volume_plan';
    const        UPDATE_VOLUME_PLAN = 'update_volume_plan';
    const        DELETE_VOLUME_PLAN = 'delete_volume_plan';
    const        STORE_VM_VOLUME = 'store_vm_volume';
    const        RESET_VM_VOLUME = 'reset_vm_volume';
    const        RECALCULATE_VM_VOLUME = 'recalculate_vm_volume';
    const        UPDATE_VM_VOLUME = 'update_vm_volume';
    const        UPDATE_CREDENTIAL = 'update_credential';
    const        DELETE_CREDENTIAL = 'delete_credential';
    const        STORE_CREDENTIAL = 'store_credential';
    const        UPDATE_IP_ADDRESS = 'update_ip_address';
    const        STORE_IP_ADDRESS = 'store_ip_address';
    const        DELETE_IP_ADDRESS = 'delete_ip_address';
    const        ACTION_IP_ADDRESS = 'action_ip_address';
    const        DELETE_IP_CATEGORY = 'delete_ip_category';
    const        STORE_IP_CATEGORY = 'store_ip_category';
    const        UPDATE_IP_CATEGORY = 'update_ip_category';
    const        STORE_HOSTIRAN_VC_SERVER = 'STORE_HOSTIRAN_VC_SERVER';
    const        UPDATE_HOSTIRAN_VC_SERVER = 'UPDATE_HOSTIRAN_VC_SERVER';
    const        DELETE_HOSTIRAN_VC_SERVER = 'DELETE_HOSTIRAN_VC_SERVER';
    const        STORE_HOSTIRAN_VC_CLUSTER = 'STORE_HOSTIRAN_VC_CLUSTER';
    const        UPDATE_HOSTIRAN_VC_CLUSTER = 'UPDATE_HOSTIRAN_VC_CLUSTER';
    const        DELETE_HOSTIRAN_VC_CLUSTER = 'DELETE_HOSTIRAN_VC_CLUSTER';
    const        STORE_HOSTIRAN_VC_HOST = 'STORE_HOSTIRAN_VC_HOST';
    const        UPDATE_HOSTIRAN_VC_HOST = 'UPDATE_HOSTIRAN_VC_HOST';
    const        DELETE_HOSTIRAN_VC_HOST = 'DELETE_HOSTIRAN_VC_HOST';
    const        STORE_HOSTIRAN_VC_NETWORK = 'STORE_HOSTIRAN_VC_NETWORK';
    const        UPDATE_HOSTIRAN_VC_NETWORK = 'UPDATE_HOSTIRAN_VC_NETWORK';
    const        DELETE_HOSTIRAN_VC_NETWORK = 'DELETE_HOSTIRAN_VC_NETWORK';
    const        STORE_HOSTIRAN_VC_STORAGE = 'STORE_HOSTIRAN_VC_STORAGE';
    const        UPDATE_HOSTIRAN_VC_STORAGE = 'UPDATE_HOSTIRAN_VC_STORAGE';
    const        DELETE_HOSTIRAN_VC_STORAGE = 'DELETE_HOSTIRAN_VC_STORAGE';
    const        STORE_HOSTIRAN_VC_FOLDER = 'STORE_HOSTIRAN_VC_FOLDER';
    const        UPDATE_HOSTIRAN_VC_FOLDER = 'UPDATE_HOSTIRAN_VC_FOLDER';
    const        DELETE_HOSTIRAN_VC_FOLDER = 'DELETE_HOSTIRAN_VC_FOLDER';
    const        DELETE_HOSTIRAN_VC_DATACENTER = 'DELETE_HOSTIRAN_VC_DATACENTER';
    const        STORE_HOSTIRAN_VC_DATACENTER = 'DELETE_HOSTIRAN_VC_DATACENTER';
    const        UPDATE_HOSTIRAN_VC_DATACENTER = 'DELETE_HOSTIRAN_VC_DATACENTER';
    const        STORE_BULK_MESSAGE = 'store_bulk_message';
    const        UPDATE_BULK_MESSAGE = 'update_bulk_message';
    const        DELETE_BULK_MESSAGE = 'delete_bulk_message';
    const        TRY_BULK_MESSAGE = 'try_bulk_message';

    const CREATE_PAYMENT_GATEWAY = 'create_payment_gateway';
    const UPDATE_PAYMENT_GATEWAY = 'update_payment_gateway';
    const DELETE_PAYMENT_GATEWAY = 'delete_payment_gateway';
    const STORE_VM_DISK = 'store_vm_disk';
    const EXPORT_VM_DISK = 'export_vm_disk';
    const EXTEND_VM_DISK = 'extend_VM_DISK';
    const DELETE_VM_DISK = 'delete_VM_DISK';
    const STORE_CLOUD_CONFIG_RULE = 'store_cloud_config_rule';
    const UPDATE_CLOUD_CONFIG_RULE = 'update_cloud_config_rule';
    const DELETE_CLOUD_CONFIG_RULE = 'delete_cloud_config_rule';

    const CREATE_AFFILIATION_PLAN = "create_affiliation_plan";
    const UPDATE_AFFILIATION_PLAN = "update_affiliation_plan";
    const DELETE_AFFILIATION_PLAN = "delete_affiliation_plan";
    const UPDATE_AFFILIATION = 'update_affiliation';
    const DELETE_AFFILIATION = 'delete_affiliation';
    const CREATE_SURVEY = "create_survey";
    const UPDATE_SURVEY = "update_survey";
    const DELETE_SURVEY = "delete_survey";

    const CREATE_SURVEY_QUESTION = "create_survey_question";
    const UPDATE_SURVEY_QUESTION = "update_survey_question";
    const DELETE_SURVEY_QUESTION = "delete_survey_question";
    const UPDATE_OBJECT_STORAGE_INFRA = 'update_object_storage_infra';
    const STORE_OBJECT_STORAGE_INFRA = 'store_object_storage_infra';
    const DELETE_OBJECT_STORAGE_INFRA = 'delete_object_storage_infra';
    const UPDATE_OBJECT_STORAGE_NODE = 'update_object_storage_node';
    const STORE_OBJECT_STORAGE_NODE = 'store_object_storage_node';
    const DELETE_OBJECT_STORAGE_NODE = 'delete_object_storage_node';
    const STORE_OBJECT_STORAGE = "store_object_storage";
    const UPDATE_OBJECT_STORAGE = "update_object_storage";
    const TERMINATE_OBJECT_STORAGE = "terminate_object_storage";
    const ACTION_OBJECT_STORAGE = 'action_object_storage';
    const RESERVE_IP_POOL = 'reserve_ip_pool';
    const CLOUD_CHANGE_DATACENTER = 'change_datacenter';
    const CLOUD_CHANGE_NETWORK = 'cloud_change_network';
    const STORE_CLOUD_NETWORK = 'store_cloud_network';
    const UPDATE_CLOUD_NETWORK = 'update_cloud_network';
    const DELETE_CLOUD_NETWORK = 'delete_cloud_network';
    const STORE_CLOUD_PROXY = 'store_cloud_proxy';
    const UPDATE_CLOUD_PROXY = 'update_cloud_proxy';
    const DELETE_CLOUD_PROXY = 'delete_cloud_proxy';
    const TERMINATE_IP = "terminate_ip";
    const STORE_ADMIN_ANYCAST = 'store_admin_anycast';
    const UPDATE_ADMIN_ANYCAST = 'update_admin_anycast';
    const DELETE_ADMIN_ANYCAST = 'delete_admin_anycast';
    const STORE_TEMPLATE_SCRIPT = 'store_template_script';
    const UPDATE_TEMPLATE_SCRIPT = 'update_template_script';
    const DELETE_TEMPLATE_SCRIPT = 'delete_template_script';
    const STORE_DATACENTER_TEMPLATE = 'store_datacenter_template';
    const UPDATE_DATACENTER_TEMPLATE = 'update_datacenter_template';
    const DELETE_DATACENTER_TEMPLATE = 'delete_datacenter_template';


    protected $connection = "mongodb";

    protected $collection = 'admin_changes';

    protected $fillable = [
        "logable_type",
        "logable_id",
        "request",
        "before",
        "after",
        "admin_user_id",
        "action",
        "created_at"
    ];

    protected $casts = [
        "created_at" => 'datetime',
        "updated_at" => 'datetime'
    ];

    public function logable()
    {
        return $this->morphTo();
    }
    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
