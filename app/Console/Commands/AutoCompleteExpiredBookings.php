<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Rental;
use Carbon\Carbon;

class AutoCompleteExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-complete active bookings that have passed their end_date and mark rentals as available again.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Find all active/confirmed bookings where end_date is before today
        $expiredBookings = Booking::whereIn('status', ['active', 'confirmed'])
            ->where('end_date', '<', $today)
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            // Mark booking as completed
            $booking->status = 'completed';
            $booking->save();

            // Mark the rental as available again
            Rental::where('id', $booking->rental_id)->update(['is_available' => true]);

            $count++;
            $this->info("Completed booking #{$booking->id} for rental #{$booking->rental_id}");
        }

        $this->info("Auto-completed {$count} expired booking(s).");

        return Command::SUCCESS;
    }
}