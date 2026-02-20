<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\BlogPost;
use App\Models\CashCollection;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\HomeCarouselItem;
use App\Models\HomeContentBlock;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayPalSetting;
use App\Models\PosPayment;
use App\Models\Rsvp;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TenantPurgeService
{
    /**
     * Purge all data for a tenant, optionally keeping the governing user.
     *
     * @return array<string, int> Counts of deleted records by type
     */
    public function purge(Tenant $tenant, bool $keepGoverningUser = true): array
    {
        $tenantId = $tenant->id;
        $stats = [];

        DB::transaction(function () use ($tenantId, $keepGoverningUser, &$stats) {
            // Delete in FK-safe order (children first)
            $stats['order_items'] = OrderItem::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['orders'] = Order::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['rsvps'] = Rsvp::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['cash_collections'] = CashCollection::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['pos_payments'] = PosPayment::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['ticket_types'] = TicketType::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['event_images'] = EventImage::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['events'] = Event::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['blog_posts'] = BlogPost::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();

            $memberQuery = Member::withoutGlobalScopes()->where('tenant_id', $tenantId);
            if ($keepGoverningUser) {
                $memberQuery->where('member_type', '!=', 'GOVERNING');
            }
            $stats['members'] = $memberQuery->delete();

            $stats['carousel_items'] = HomeCarouselItem::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['content_blocks'] = HomeContentBlock::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['paypal_settings'] = PayPalSetting::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            $stats['audit_logs'] = AuditLog::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();

            $tenantUserQuery = TenantUser::where('tenant_id', $tenantId);
            if ($keepGoverningUser) {
                $tenantUserQuery->where('role_in_tenant', '!=', 'TENANT_GOVERNING');
            }
            $stats['tenant_users'] = $tenantUserQuery->delete();
        });

        // Delete upload directory
        $uploadPath = storage_path("app/public/uploads/{$tenant->slug}");
        if (File::isDirectory($uploadPath)) {
            File::deleteDirectory($uploadPath);
        }

        return $stats;
    }
}
