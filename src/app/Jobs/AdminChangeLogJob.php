<?php

namespace App\Jobs;

use App\Models\AdminLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Class AdminChangeLogJob
 * @package App\Jobs
 * @method static PendingDispatch dispatch($admin_user_id, $action, Model $model = null, array $validated_request = [], array $changes = [], array $old = [])
 */
class AdminChangeLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Model $model;
    private string $action;
    private ?Collection $old;
    private array $validated_request;
    private array $changes;
    private int $admin_user_id;


    /**
     * AdminChangeLogJob constructor.
     * @param $admin_user_id
     * @param $action
     * @param Model|null $model
     * @param array $validated_request
     * @param array $changes
     * @param array $old
     */
    public function __construct($admin_user_id, $action, Model $model = null, array $validated_request = [], array $changes = [], array $old = [])
    {
        $this->model = $model;
        $this->action = $action;
        $this->old = collect($old);
        $this->validated_request = $validated_request;
        $this->changes = $changes;
        $this->admin_user_id = $admin_user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admin_user_id = $this->admin_user_id;
        $before = null;
        $after = null;
        $loggable_model = get_class($this->model);
        $loggable_id = $this->model->getKey();

        switch ($this->action) {
            case AdminLog::UPDATE_ADMIN_USER_ACTION:
            case AdminLog::BLOG_APPROVE_COMMENT:
            case AdminLog::CREATE_DNS_RECORD:
            case AdminLog::UPDATE_DNS_RECORD:
            case AdminLog::UPDATE_HOSTIRAN_PROXY:
            case AdminLog::UPDATE_INFORMATION_SYSTEM_CATEGORY:
            case AdminLog::UPDATE_INFORMATION_SYSTEM:
            case AdminLog::ACCEPT_ORDER:
            case AdminLog::APPLY_ORDER:
            case AdminLog::CANCEL_ORDER:
            case AdminLog::EDIT_ORDER_ITEM:
            case AdminLog::FRAUD_ORDER:
            case AdminLog::PENDING_ORDER:
            case AdminLog::UPDATE_PRODUCT_GROUP_ATTRIBUTE:
            case AdminLog::UPDATE_PRODUCT_GROUP:
            case AdminLog::UPDATE_PRODUCT_TEMPLATE:
            case AdminLog::SERVICE_RENEW_ACTION:
            case AdminLog::TRANSFER_ALL_SERVICES:
            case AdminLog::UPDATE_CLIENT:
            case AdminLog::UPDATE_CLIENT_GROUP:
            case AdminLog::UPDATE_USER_CLIENT_PERMISSIONS:
            case AdminLog::UPDATE_USER_DOCUMENTS:
            case AdminLog::UPDATE_USER:
            case AdminLog::INVOICE_APPLY_CREDIT:
            case AdminLog::REFUNDED_INVOICE:
            case AdminLog::PAY_INVOICE:
            case AdminLog::CANCEL_INVOICE:
                $after = $this->getChangesWithCasts($this->changes, $this->model);
                break;
            case AdminLog::UPDATE_ANNOUNCEMENT:
            case AdminLog::UPDATE_BANK_ACCOUNT:
            case AdminLog::UPDATE_CAREER_APPLICATION:
            case AdminLog::UPDATE_CAREER_JOB:
            case AdminLog::CLIENT_DOMAIN_UPDATE:
            case AdminLog::UPDATE_CONFIG:
            case AdminLog::UPDATE_DNS_ZONE:
            case AdminLog::UPDATE_DOMAIN_INFORMATION:
            case AdminLog::UPDATE_ENQUIRY:
            case AdminLog::UPDATE_INVOICE_STATUS:
            case AdminLog::UPDATE_MANAGE_SERVICE_NOTE:
            case AdminLog::UPDATE_MANAGE_SERVICE_TRIGGER:
            case AdminLog::UPDATE_MANAGE_SERVICE:
            case AdminLog::UPDATE_MANAGE_SERVICE_PLAN:
            case AdminLog::UPDATE_PRODUCT:
            case AdminLog::UPDATE_PRODUCT_ATTRIBUTE:
            case AdminLog::UPDATE_INVOICE:
            case AdminLog::CLOUD_TERMINATE_SERVER:
            case AdminLog::UNPAID_INVOICE:
            case AdminLog::SEND_INVOICE_TO_RAHKARAN:
            case AdminLog::DELETE_INVOICE_FROM_RAHKARAN:
            case AdminLog::REJECT_OFFLINE_PAYMENT:
            case AdminLog::ROLLBACK_OFFLINE_PAYMENT:
            case AdminLog::UPDATE_SERVER_STATUS:
            case AdminLog::UPDATE_SERVER_STATUS_MANUALLY:
            case AdminLog::UPDATE_DATA_CENTER_ANNOUNCEMENT:
            case AdminLog::UNSUSPEND_S3:
            case AdminLog::TERMINATE_S3:
            case AdminLog::UPDATE_CLIENT_NOTE:
            case AdminLog::MANUAL_CHECK_INVOICE:
                $before = $this->old->diff($this->model->toArray())->toArray();
                $after = $this->getChangesWithCasts($this->changes, $this->model);
                break;
            case AdminLog::UPDATE_BLOG:
            case AdminLog::ADD_INVOICE_ITEM:
            case AdminLog::EDIT_INVOICE_ITEM:
            case AdminLog::SERVICE_UPDATE:
            case AdminLog::ADD_INVOICE_TRANSACTION:
            case AdminLog::DELETE_INVOICE_ITEM:
            case AdminLog::UPDATE_OFFLINE_PAYMENT:
            case AdminLog::VERIFY_OFFLINE_PAYMENT:
            case AdminLog::CLIENT_DOMAIN_TRANSFER:
            case AdminLog::UPDATE_CLOUD_BUCKET:
            case AdminLog::UPDATE_TLD_PRICING:
            case AdminLog::PROVISIONING_MODULE_UPDATE:
                $old = $this->old->toArray();
                $model = $this->model->toArray();
                $before = array_diff_assoc_recursive($old, $model);
                $after = array_diff_assoc_recursive($model, $old);
                break;
            case AdminLog::CREATE_BUSINESS_PARTNER:
            case AdminLog::CREATE_CAREER_JOB:
            case AdminLog::BLOG_REPLY_COMMENT:
            case AdminLog::CREATE_CONFIG:
            case AdminLog::CREATE_ANNOUNCEMENT:
            case AdminLog::CREATE_DNS_ZONE:
            case AdminLog::ADD_CREDIT:
            case AdminLog::CREATE_INVOICE:
            case AdminLog::ADD_CREDIT_TO_INVOICE:
            case AdminLog::DECREASE_CREDIT:
            case AdminLog::CREATE_OFFLINE_PAYMENT:
            case AdminLog::CREATE_INFORMATION_SYSTEM:
            case AdminLog::CREATE_INFORMATION_SYSTEM_CATEGORY:
            case AdminLog::ADD_ORDER_ITEM:
            case AdminLog::CREATE_PRODUCT_GROUP_ATTRIBUTE:
            case AdminLog::CREATE_PRODUCT_GROUP:
            case AdminLog::CREATE_PRODUCT_TEMPLATE:
            case AdminLog::CREATE_PRODUCT_ATTRIBUTE:
            case AdminLog::CREATE_PRODUCT:
            case AdminLog::CREATE_CLIENT:
            case AdminLog::REGISTER_USER:
            case AdminLog::CREATE_ORDER:
            case AdminLog::HETZNER_STORE_SERVER:
            case AdminLog::CREATE_GIFT_CODE:
            case AdminLog::CREATE_SERVICE_DISCOUNT:
            case AdminLog::CREATE_DOMAIN_DISCOUNT:
            case AdminLog::CREATE_SERVER_STATUS:
            case AdminLog::CREATE_DATA_CENTER_ANNOUNCEMENT:
            case AdminLog::DOMAIN_RENEW_ACTION:
            case AdminLog::REGISTER_S3:
            case AdminLog::CREATE_CLIENT_NOTE:
            case AdminLog::CREATE_DOMAIN_PRODUCT_SUGGESTION:
                $after = $this->model->toArray();
                break;
        }

        $cloudActions = [
            AdminLog::MIGRATE_HOSTIRAN_POOL,
            AdminLog::UPDATE_IP_POOL,
            AdminLog::STORE_TEMPLATE,
            AdminLog::UPDATE_TEMPLATE,
            AdminLog::CLOUD_MANUAL_INVOICE,
            AdminLog::CLOUD_MANUAL_CHARGE,
            AdminLog::CLOUD_RESTORE,
            AdminLog::CLOUD_UPDATE_SERVER_INFO,
            AdminLog::CLOUD_TERMINATE_BACKUP,
            AdminLog::CLOUD_RECOVERY_BACKUP,
            AdminLog::CLOUD_STORE_BACKUP,
            AdminLog::CLOUD_TERMINATE_FLOATING_IP,
            AdminLog::CLOUD_UPDATE_FLOATING_IP,
            AdminLog::CLOUD_REVERS_DNS_FLOATING_IP,
            AdminLog::CHANGE_IP_FLOATING_IP,
            AdminLog::CLOUD_STORE_FLOATING_IP,
            AdminLog::CLOUD_TERMINATE_HETZNER_SNAPSHOT,
            AdminLog::CLOUD_TERMINATE_HOSTIRAN_SNAPSHOT,
            AdminLog::CLOUD_REVERT_HOSTIRAN_SNAPSHOT,
            AdminLog::CLOUD_STORE_SNAPSHOT,
            AdminLog::CLOUD_HOSTIRAN_UPGRADE,
            AdminLog::CLOUD_HETZNER_UPGRADE,
            AdminLog::UPDATE_HOSTIRAN_NODE,
            AdminLog::STORE_HOSTIRAN_NODE,
            AdminLog::DELETE_HOSTIRAN_NODE,
            AdminLog::STORE_HOSTIRAN_DATACENTER,
            AdminLog::UPDATE_HOSTIRAN_DATACENTER,
            AdminLog::STORE_BANDWIDTH,
            AdminLog::UPDATE_BANDWIDTH,
            AdminLog::DELETE_BANDWIDTH,
            AdminLog::UPDATE_VM_BANDWIDTH,
            AdminLog::DELETE_VM_BANDWIDTH,
            AdminLog::STORE_CLOUD_PACKAGE,
            AdminLog::UPDATE_CLOUD_PACKAGE,
            AdminLog::DELETE_CLOUD_PACKAGE,
            AdminLog::STORE_VOLUME_PLAN,
            AdminLog::UPDATE_VOLUME_PLAN,
            AdminLog::DELETE_VOLUME_PLAN,
            AdminLog::STORE_VM_VOLUME,
            AdminLog::RESET_VM_VOLUME,
            AdminLog::RECALCULATE_VM_VOLUME,
            AdminLog::UPDATE_VM_VOLUME,
            AdminLog::UPDATE_CLOUD_BUCKET,
            AdminLog::HETZNER_STORE_SERVER,
            AdminLog::ACTION_ON_SERVER,
            AdminLog::SNAPSHOT_CLOUD_SERVICE,
            AdminLog::CLOUD_TERMINATE_SERVER,
            AdminLog::TRANSFER_SERVER_OWNERSHIP,
            AdminLog::UPDATE_CREDENTIAL,
            AdminLog::DELETE_CREDENTIAL,
            AdminLog::STORE_CREDENTIAL,
            AdminLog::STORE_HOSTIRAN_VC_SERVER,
            AdminLog::UPDATE_HOSTIRAN_VC_SERVER,
            AdminLog::DELETE_HOSTIRAN_VC_SERVER,
            AdminLog::STORE_HOSTIRAN_VC_CLUSTER,
            AdminLog::UPDATE_HOSTIRAN_VC_CLUSTER,
            AdminLog::DELETE_HOSTIRAN_VC_CLUSTER,
            AdminLog::STORE_HOSTIRAN_VC_HOST,
            AdminLog::UPDATE_HOSTIRAN_VC_HOST,
            AdminLog::DELETE_HOSTIRAN_VC_HOST,
            AdminLog::STORE_HOSTIRAN_VC_NETWORK,
            AdminLog::UPDATE_HOSTIRAN_VC_NETWORK,
            AdminLog::DELETE_HOSTIRAN_VC_NETWORK,
            AdminLog::STORE_HOSTIRAN_VC_STORAGE,
            AdminLog::UPDATE_HOSTIRAN_VC_STORAGE,
            AdminLog::DELETE_HOSTIRAN_VC_STORAGE,
            AdminLog::STORE_HOSTIRAN_VC_FOLDER,
            AdminLog::UPDATE_HOSTIRAN_VC_FOLDER,
            AdminLog::DELETE_HOSTIRAN_VC_FOLDER,
            AdminLog::DELETE_HOSTIRAN_VC_DATACENTER,
            AdminLog::STORE_HOSTIRAN_VC_DATACENTER,
            AdminLog::UPDATE_HOSTIRAN_VC_DATACENTER,
            AdminLog::STORE_IP_ADDRESS,
            AdminLog::UPDATE_IP_ADDRESS,
            AdminLog::DELETE_IP_ADDRESS,
            AdminLog::ACTION_IP_ADDRESS,
            AdminLog::DELETE_IP_CATEGORY,
            AdminLog::STORE_IP_CATEGORY,
            AdminLog::UPDATE_IP_CATEGORY,
            AdminLog::STORE_VM_DISK,
            AdminLog::EXPORT_VM_DISK,
            AdminLog::EXTEND_VM_DISK,
            AdminLog::DELETE_VM_DISK,
            AdminLog::STORE_CLOUD_CONFIG_RULE,
            AdminLog::UPDATE_CLOUD_CONFIG_RULE,
            AdminLog::DELETE_CLOUD_CONFIG_RULE,
        ];

        if (in_array($this->action, $cloudActions)) {
            $admin_user_id = $loggable_id;
            $loggable_id = $this->admin_user_id;
        }

        admin_log(
            $this->action,
            $loggable_model,
            $this->model->getChanges(),
            $before,
            $this->validated_request,
            $admin_user_id
        );
    }

    private function getChangesWithCasts(array $change_keys = [], Model $model): array
    {
        $change_keys = collect($change_keys)->keys()->toArray();
        $changes = collect($model->only($change_keys) ?? [])->mapWithKeys(function ($value, $key) {
            $data[$key] = $value;
            if ($value instanceof Carbon) {
                $data[$key] = $value->toDateTimeString();
            }
            return $data;
        })->toArray();

        return $changes ?? [];
    }
}
