<?php

namespace App\Traits;

use App\Models\SupportTicket;
use App\Models\TourPackage;

/**
 * Shared by Admin\ManageUsersController and Admin\ManageAgenciesController -
 * both delete an account the same way (block if money/service commitments
 * are outstanding, then manually clean up dependents since none of these
 * FK columns have a real database-level cascade - see the delete-user/
 * agency investigation). Only the two genuinely duplicated, multi-level
 * cleanups live here; the simple one-line where(...)->delete() cleanups
 * stay inline in each controller since there's nothing to share.
 */
trait AccountDeletionService
{
    /**
     * Deletes every support ticket owned by this account, cascading through
     * its messages and their file attachments (removing the actual files
     * from disk, not just the DB rows).
     */
    protected function deleteSupportTickets(string $ownerColumn, int $ownerId): void
    {
        $tickets = SupportTicket::where($ownerColumn, $ownerId)->with('supportMessage.attachments')->get();

        foreach ($tickets as $ticket) {
            foreach ($ticket->supportMessage as $message) {
                foreach ($message->attachments as $attachment) {
                    fileManager()->removeFile(getFilePath('ticket') . '/' . $attachment->attachment);
                    $attachment->delete();
                }
                $message->delete();
            }
            $ticket->delete();
        }
    }

    /**
     * Deletes every tour package owned by this agency, plus its images
     * (file + DB row) - mirrors TourService::delete()'s own cleanup exactly,
     * minus that method's upcoming-booking check, since the caller here has
     * already confirmed there are no active bookings for the whole agency.
     * packagePrices/reviews/wishlists for these packages are left as-is,
     * matching TourService::delete()'s existing single-package behavior -
     * already handled defensively by the display layer wherever they're
     * rendered (see the orphaned-tour-package view fixes).
     */
    protected function deleteTourPackagesForAgency(int $agencyId): void
    {
        $tourPackages = TourPackage::where('user_id', $agencyId)->where('user_type', 'agency')->with('tour_package_images')->get();

        foreach ($tourPackages as $tourPackage) {
            foreach ($tourPackage->tour_package_images ?? [] as $item) {
                fileManager()->removeFile(getFilePath('tourPackageImage') . '/' . $item->image);
                if (file_exists(getFilePath('tourPackageImage') . '/thumb_' . $item->image)) {
                    fileManager()->removeFile(getFilePath('tourPackageImage') . '/thumb_' . $item->image);
                }
                $item->delete();
            }
            $tourPackage->delete();
        }
    }
}
