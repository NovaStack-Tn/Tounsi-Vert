<?php

namespace App\Observers;

use App\Models\Donation;
use App\Monitoring\Metrics;

/**
 * Donation Observer
 * Automatically tracks donation metrics when donations are created
 */
class DonationObserver
{
    /**
     * Handle the Donation "created" event.
     */
    public function created(Donation $donation): void
    {
        // Track donation metric in Prometheus
        Metrics::incDonation($donation->organization_id);
    }
}
