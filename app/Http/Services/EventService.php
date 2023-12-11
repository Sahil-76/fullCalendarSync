<?php 
namespace App\Http\Services;

use App\Models\Event;
use Illuminate\Support\Carbon;

class EventService
{
    protected $user;
    public function __construct($user)
    {
        $this->user=$user;
    }
    public function create($data)
    {
        $event=new Event($data);
        $event->save();
        return $event;
    }
     public function update($id, $data)
     {
        $event=Event::find($id);
        $event->file($data);
        $event->save();
        return $event;
     }

    public function allEvents($filters)
    {
        $eventQuery=Event::query();
        $eventQuery->where('user_id',$this->user);
        if($filters['start']){
            $eventQuery->where('start','>=',$filters['start']);
        }
        if($filters['end']){
            $eventQuery->where('end','>=',$filters['end']);
        }
        $events=$eventQuery->get();
        $data=[];
        foreach($events as $event){
            if(!(int)$event['is_all_day']){
                $event['allDay'] = false;
                $event['start']=Carbon::createFromTimestamp
                (strtotime($event['start']))->toDateTimeString();
                $event['end']=Carbon::createFromTimestamp(strtotime($event['end']))->toDateTimeString();
                $event['endDay']=$event['end'];
                $event['startDay']=$event['start'];
            }else{
                $event['allDay']=true;
                $event['endDay']=Carbon::createFromTimestamp(strtotime($event['end']))->addDays(value:-1)->toDateString();
                $event['startDay']=$event['start'];  
            }
            $event['eventid']=$event['id'];
            array_push($data,$event);
        }
        return $data;
    }
}