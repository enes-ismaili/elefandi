<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use Illuminate\Console\Command;
use App\Models\VendorMembership;

class CheckVendorStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendorstatus:twice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check twice a day vendor status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $vendors = Vendor::where([['vstatus', '=', 2]])->get();
        foreach($vendors as $vendor){
            if($vendor->amembership->count() == 0){
                $vendor->products()->where('vstatus', 1)->update(['vstatus'=>2]);
            }
        }
        $this->info('Vendors Status Updated');
    }
}
