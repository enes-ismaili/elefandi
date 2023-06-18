<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use Illuminate\Console\Command;

class CheckMembershipTwiceMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:twicemonthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check twice Monthly membership plans';

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
        $vendors = Vendor::all();
        foreach($vendors as $vendor){
            if($vendor->amembership->count() == 0){
                $vendor->vstatus = 2;
                $vendor->save();
            }
        }
        $this->info('Membership Updated');
    }
}
