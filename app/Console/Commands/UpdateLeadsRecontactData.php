<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class UpdateLeadsRecontactData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:update-recontact-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing leads data with recontact fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating existing leads with recontact data...');

        // Update all existing leads
        $leads = Lead::whereNull('last_contact_at')->get();
        
        $this->info("Found {$leads->count()} leads to update");

        $bar = $this->output->createProgressBar($leads->count());
        $bar->start();

        foreach ($leads as $lead) {
            // Set last_contact_at to created_at for existing leads
            $lead->update([
                'recontact_count' => 0,
                'last_contact_at' => $lead->created_at
            ]);

            $bar->advance();
        }

        $bar->finish();
        
        $this->newLine();
        $this->info('Recontact data updated successfully!');
        
        // Show statistics
        $this->newLine();
        $this->info('Current statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Leads', Lead::count()],
                ['Leads with Recontact', Lead::withRecontact()->count()],
                ['Leads without Recontact', Lead::withoutRecontact()->count()],
                ['Recent Recontacts (24h)', Lead::recentRecontact(24)->count()],
            ]
        );
    }
}