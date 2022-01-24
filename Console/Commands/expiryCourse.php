<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\CourseUser;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class expiryCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expirycoursealert:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Course: 30 days : before the expiry need a email alert';

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
        Mail::raw('Hi, welcome user!', function ($message) {
        $message->to('sivaraj617@gmail.com')
            ->from('info@solstium.net')
            ->subject('Employee Course Expiry 1');
        });
        $msg     = 'Employee Course Expiry mail sent';
        $current = Carbon::now();
        $current->addDays(30);
        $expiryDate = $current->format("Y-m-d");

        $courseList = CourseUser::select('id','course_id','employee_code','course_enddate')
                        ->whereDate('course_enddate', '=', $expiryDate)
                        ->orderBy('id', 'desc')
                        ->get();
        if(count($courseList) > 0){
            foreach ($courseList as $key => $value) {
                $info = Employee::where('employee_code', $value->employee_code)->first();
                $courseinfo = Course::where('id', $value->course_id)->first();
                $msg  = 'Hi, Employee('.(($info)? $info->first_name : '').' - '.$value->employee_code.') course('.(($courseinfo)? $courseinfo->Course_name : '').') expiry in 30 days.';
                Mail::raw("Course Expiry Mail", function ($mail) use ($value) {
                    $mail->from('info@solstium.net');
                    $mail->to('admin@solstium.net')
                        ->subject('Employee Course Expiry');
                });
                //$this->info('Employee Course Expiry mail sent');
            }
        }
        $this->info($msg);
    }
}
