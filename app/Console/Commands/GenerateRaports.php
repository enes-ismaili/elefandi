<?php

namespace App\Console\Commands;

use App\Models\MembershipInvoice;
use App\Models\Vendor;
use Illuminate\Console\Command;
use App\Models\VendorMembership;

class GenerateRaports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raport:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Raport each date 1';

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
        $memberships = VendorMembership::where([['active', '=', 1],['type', '=', 2]])->get();
        $monthNum = date('n');
        //$monthNum = 11;
        $thisYear = date('Y');
        $lastMonth = $monthNum-1;
        if($monthNum == 1){
            $lastMonth = 12;
            $thisYear = $thisYear - 1;
        }
        $monthNamesArr = ['Dhjetor', 'Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor', 'Korrik', 'Gusht', 'Shtator', 'Tetor', 'Nëntor', 'Dhjetor'];
        $reportName = $monthNamesArr[$monthNum - 1].' '.$thisYear;
        foreach($memberships as $membership){
            $vendor = Vendor::findOrFail($membership->vendor_id);
            $thisMonthStart = date('Y-'.$lastMonth.'-01 00:00:05');
            $thisMonthEnd = date('Y-'.$lastMonth.'-35 23:59:55');
            $ordersMonthSum = $vendor->orders->where('created_at', '>', $thisMonthStart)->where('created_at', '<', $thisMonthEnd)->sum('value');
            $newMembership = new MembershipInvoice();
            $newMembership->vendor_id = $membership->vendor_id;
            $newMembership->membership_id = $membership->id;
            $newMembership->total = $ordersMonthSum;
            $newMembership->comission = $membership->amount;
            $newMembership->paid = 0;
            if($membership->amount){
                $newMembership->amount = ($ordersMonthSum * $membership->amount)/100;
            } else {
                $newMembership->amount = 0;
            }
            if($newMembership->amount == 0){
                $newMembership->paid = 1;
            }
            $newMembership->name = $reportName;
            $newMembership->save();
        }
        
        // $this->info($monthNamesArr[$monthNum - 1].' '.$thisYear);
        $this->info('Raport Generated');
    }
}
