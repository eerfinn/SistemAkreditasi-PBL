<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateNotificationLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:update-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update dokumen notification links to point to kriteria page';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating notification links...');

        // Get all notifications with dokumen type that have /dokumen/ links
        $notifications = DB::table('notifications')
            ->where('type', 'dokumen')
            ->whereNotNull('kriteria_id')
            ->whereRaw("link LIKE '/dokumen/%'")
            ->get();

        $count = 0;
        foreach ($notifications as $notification) {
            if ($notification->kriteria_id) {
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'link' => "/kriteria/{$notification->kriteria_id}"
                    ]);
                $count++;
            }
        }

        $this->info("Updated {$count} notification links successfully.");
    }
}
