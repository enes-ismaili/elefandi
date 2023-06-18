<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use Illuminate\Console\Command;
use App\Models\VendorMembership;

class CheckMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check every day membership plans';

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
        $now = date('Y-m-d H:i');
        $memberships = VendorMembership::where([['active', '=', 1]])->get();
        foreach($memberships as $membership){
            if($membership->end_date < $now){
                $membership->active = 0;
                $membership->save();
                $cVendor = Vendor::findOrFail($membership->vendor_id);
                if($cVendor->amembership->count() == 0){
                    $cVendor->vstatus = 2;
                    $cVendor->save();
                    $cVendor->products()->update(['vstatus'=>2]);
                }
            }
        }
        $this->info('Membership Updated');
    }
}
