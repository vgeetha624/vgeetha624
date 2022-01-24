<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class expiryPassportIC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expirypassportalert:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Passport/IC: 90 days : before the expiry need a email alert';

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

        $current = Carbon::now();
        $current->addDays(90);
        $expiryDate = $current->format("Y-m-d");

        $employeeList = Employee::select('id','first_name','employee_code','email','user_name')
                        ->whereDate('passport_end_date', '=', $expiryDate)
                        ->orderBy('id', 'desc')
                        ->get();
        if(count($employeeList) > 0){
            foreach ($employeeList as $key => $value) {
                $msg = 'Hi, Employee('.$value->first_name.' - '.$value->employee_code.') passport expiry in 90 days.';
                Mail::raw("{$msg}", function ($mail) use ($value) {
                    $mail->from('info@solstium.net');
                    $mail->to('sivaraj617@gmail.com')
                        ->subject('Employee Passport Expiry');
                });
            }
        }
        $employeeList2 = Employee::select('id','first_name','employee_code','email','user_name')
                        ->whereDate('fin_end_date', '=', $expiryDate)
                        ->orderBy('id', 'desc')
                        ->get();
        if(count($employeeList2) > 0){
            foreach ($employeeList2 as $key => $value2) {
                $msg = 'Hi, Employee('.$value2->first_name.' - '.$value2->employee_code.') IC expiry in 90 days.';
                Mail::raw("{$msg}", function ($mail) use ($value2) {
                    $mail->from('info@solstium.net');
                    $mail->to('sivaraj617@gmail.com')
                        ->subject('Employee IC Expiry');
                });
            }
        }
        $this->info('Employee Passport/IC Expiry mail sent');
    }
}
