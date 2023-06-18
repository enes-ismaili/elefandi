<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\ProductReports;

class ReportProduct extends Component
{
    public $openReport = false;
    public $productId;
    public $isLoggin = false;
    public $name;
    public $email;
    public $reason;
    public $user_id = null;
    public $errorss = [];
    public $errorName;
    public $errorProduct = '';
    public $errorEmail = '';
    public $errorReason = '';
    public $successReport = '';

    protected $listeners = [
        'closeReportModal' => 'closeReportSuccess',
    ];

    public function mount()
    {
        if(current_user()){
            $this->isLoggin = true;
            $this->name = current_user()->first_name . ' ' . current_user()->last_name;
            $this->email = current_user()->email;
            $this->user_id = current_user()->id;
        }
    }

    public function render()
    {
        return view('livewire.products.report-product');
    }

    public function openReport(){
        $this->openReport = true;
    }

    public function closeReport(){
        $this->openReport = false;
        if(!$this->isLoggin){
            $this->name = '';
            $this->email = '';
        }
        $this->reason = '';
        $this->errorss = [];
        $this->successReport = '';
    }

    public function SendRequest()
    {
        $hasError = false;
        $this->errorss = [];
        if(!$this->isLoggin && !$this->name){
            $this->errorss['name'] = 'Emri është i detyruar';
            $hasError = true;
        }
        if(!$this->productId){
            $this->errorss['product'] = 'Ka ndodhur një gabim. Ju lutem riprovoni sërisht';
            $hasError = true;
        }
        if(!$this->email){
            $this->errorss['email'] = 'Emaili është i detyruar';
            $hasError = true;
        }
        if($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            $this->errorss['email'] = 'Kontrolloni emailin nëse e keni shkruar saktë';
            $hasError = true;
        }
        if(!$this->reason){
            $this->errorss['reason'] = 'Informacioni dhe Arsyeja janë të detyruara';
            $hasError = true;
        }
        if(!$hasError){
            $this->successReport = 'Raportimi u dërgua me sukses';
            $report = new ProductReports();
            $report->product_id = $this->productId;
            $report->name = $this->name;
            $report->email = $this->email;
            $report->reason = $this->reason;
            $report->user_id = $this->user_id;
            $report->save();
            $this->emitSelf('closeReportModal', "true");
        }
    }

    public function closeReportSuccess()
    {
        sleep(1);
        $this->closeReport();
    }
}