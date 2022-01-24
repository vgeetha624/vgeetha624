<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ServiceRequest;
use App\Models\DeviceAlerts;
use App\Models\DeviceInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class DeviceAlertNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeviceAlertNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DeviceAlertNotification';

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
         $type = array('No Disk','Disk Error','Broken Network','IP Confict','Video Loss');

         $channel_type = array('CH01','CH02','CH03','CH04','CH05','CH06','CH07','CH08');



         $services = ServiceRequest::where('subject','like', '%' . "XVR ALERT" . '%')->where('alert_process',0)->get();

         foreach($services as $servicekey => $service)
         {

           $ticket_id = $service['id'];

           $created_at = $service['request_date'];

           $subject = $service['subject'];

           $sub =  explode("-",$subject);

           $serial_no = $sub[1];

          $serial_no = preg_replace('/\s+/', '', $serial_no);

           $device = DeviceInfo::where('serial_number',$serial_no)->first(); 

           $device_id = !empty($device) ? $device->id : "-";

           $description = $service['description'];

           $alert_type = ""; $channel = "-";

           foreach($type as $key => $searchvalue)
           { 

            if(preg_match("/{$searchvalue}/i", $description)) {
                    
                   $alert_type = $searchvalue;
                }
           }
           
           foreach($channel_type as $key => $channelvalue)
           { 

            if(preg_match("/{$channelvalue}/i", $description)) {
                    
                   $channel = $channelvalue;
                }
           }

           $devicealert = new DeviceAlerts;

           $devicealert->device_id = $device_id;

           $devicealert->ticket_id = $ticket_id;

           $devicealert->serial_no = $serial_no;

           $devicealert->alert_type = $alert_type;

           $devicealert->channel    = $channel;

           $devicealert->created_at = $created_at;

           $devicealert->save();

           $serviceprocess = ServiceRequest::where('id',$ticket_id)->first();

           $serviceprocess->alert_process = 1;

           $serviceprocess->save();

           if($devicealert)
           {  

                $customerdata =  explode("-",$service['name']);

                $email = $customerdata[1];

                $name  = $customerdata[0];
                
                $fromemail = "administrator@solstium.net";

                 $data = array('name'=>$name,'errors' => $alert_type);

                  // Mail::send('mail', $data, function($message) use ($subject){
                  //    $message->to('karunakarans@techaffinity.com')->subject
                  //       ($subject);
                  // });

           }

         }
         echo "Successfully updated.....";
    }

   
}
