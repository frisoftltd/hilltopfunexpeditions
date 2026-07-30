<?php

namespace App\Constants;

class BookingStatus
{
    // tour_bookings.status - payment lifecycle
    const UNPAID = 0;
    const PAID = 1;
    const PENDING_MANUAL = 2;
    const REJECTED = 3;
    const CANCELLED_BY_TRAVELER = 4;
    const RESERVED = 5; // pay-on-arrival: confirmed, no money collected through the platform
    const EXPIRED = 6; // system auto-cancelled after prolonged inactivity - distinct from CANCELLED_BY_TRAVELER, which is the traveler's own action

    // tour_bookings.agency_status - agency's own review layer, independent
    // of the payment status above. Pending review is stored as NULL, not 0.
    const AGENCY_APPROVED = 1;
    const AGENCY_DECLINED = 2;
}
