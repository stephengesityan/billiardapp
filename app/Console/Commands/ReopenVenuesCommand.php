<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Venue;
use Carbon\Carbon;

class ReopenVenuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'venues:reopen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reopen venues that have reached their reopen date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for venues to reopen...');

        $venuesReopened = 0;

        // Get all closed venues that should be reopened today
        $venues = Venue::where('status', 'close')
                      ->whereNotNull('reopen_date')
                      ->whereDate('reopen_date', '<=', Carbon::today())
                      ->get();

        foreach ($venues as $venue) {
            if ($venue->checkAutoReopen()) {
                $this->info("Venue '{$venue->name}' has been automatically reopened.");
                $venuesReopened++;
            }
        }

        if ($venuesReopened > 0) {
            $this->info("Successfully reopened {$venuesReopened} venue(s).");
        } else {
            $this->info('No venues to reopen today.');
        }

        return Command::SUCCESS;
    }
}