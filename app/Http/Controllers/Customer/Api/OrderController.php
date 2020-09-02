<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $orders=Order::with(['details.entity','details.clinic'])
            ->where('status', '!=','pending')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $lists=[];

        foreach($orders as $order) {
            //echo $order->id.' ';
            $total = count($order->details);
            $lists[] = [
                'id' => $order->id,
                'title' => ($order->details[0]->entity->name ?? '') . ' ' . ($total > 1 ? 'and ' . ($total - 1) . ' more' : ''),
                'booking_id' => $order->refid,
                'datetime' => date('D d M,Y', strtotime($order->created_at)),
                'total_price' => $order->total_cost,
                'image' => $order->details[0]->entity->image ?? ''
            ];
        }
        return [
            'status'=>'success',
            'data'=>$lists
        ];

    }


    /*
     * Product Purchase Or Therapy Book Start
     */
    public function initiateOrder(Request $request){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        switch($request->type){
            case 'clinic':
                return $this->initiateClinicBooking($request);
            case 'therapy':
                return $this->initiateTherapyBooking($request);
            case 'product':
                return $this->initiateProductPurchase($request);
            default:
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation Performed'
                ];
        }
    }

    private function initiateProductPurchase(Request $request){

        $cartitems=Cart::where('user_id', auth()->guard('customerapi')->user()->id)
            ->with(['product'])
            ->whereHas('product', function($product){
                $product->where('isactive', true);
            })->get();

        if(!$cartitems)
            return [
                'status'=>'failed',
                'message'=>'Cart is empty'
            ];

        $refid=env('MACHINE_ID').time();
        $total_cost=0;
        foreach($cartitems as $item) {
            $total_cost=$total_cost+($item->product->price??0)*$item->quantity;
        }
$refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);

        foreach($cartitems as $item){
            OrderDetail::create([
                'order_id'=>$order->id,
                'entity_type'=>'App\Models\Product',
                'entity_id'=>$item->product_id,
                'clinic_id'=>null,
                'cost'=>$item->product->price??0,
                'quantity'=>$item->quantity
            ]);
        }

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];

    }


    private function initiateTherapyBooking(Request $request){
        $request->validate([
            'therapy_id'=>'required|integer',
            'booking_type'=>'required|in:instant,schedule',
            //'num_sessions'=>'required_if:booking_type,schedule|integer',
            'grade'=>'required_if:booking_type,instant|integer|in:1,2,3,4',
            //'time'=>'required_if:booking_type,schedule|date_format:H:i',
            //'date'=>'required_if:booking_type,schedule|date_format:Y-m-d',
            'schedule_type'=>'required_if:booking_type,schedule|in:automatic,custom'
        ]);

        $therapy=Therapy::active()->find($request->therapy_id);

        if(!$therapy)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if($request->booking_type=='schedule'){
            return $this->initiateTherapyScheduleBooking($request, $therapy);
        }else{
            return $this->initiateTherapyInstantBooking($request, $therapy);
        }

    }

    private function initiateTherapyScheduleBooking(Request $request, $therapy){

        $refid=env('MACHINE_ID').time();

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>0,
            'is_instant'=>false,
            'schedule_type'=>$request->schedule_type,
            'order_place_state'=>'stage_1'
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);
        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$therapy->id,
            'clinic_id'=>null,
            'cost'=>0,
            'quantity'=>0,
            'grade'=>1
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }

    private function initiateTherapyInstantBooking(Request $request, $therapy){
        //return $clinic;
        $grade=$request->grade??1;
        $num_sessions=1;

        switch($grade){
            case 1:$cost=($therapy->grade1_price??0);
                break;
            case 2:$cost=($therapy->grade2_price??0);
                break;
            case 3:$cost=($therapy->grade3_price??0);
                break;
            case 4:$cost=($therapy->grade4_price??0);
                break;
        }

        $refid=env('MACHINE_ID').time();

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$cost*$num_sessions,
            'booking_date'=>($request->booking_type=='schedule')?$request->date:null,
            'booking_time'=>($request->booking_type=='schedule')?$request->time:null,
            'is_instant'=>($request->booking_type=='instant')?true:false
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);
        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$therapy->id,
            'clinic_id'=>null,
            'cost'=>$cost,
            'quantity'=>$num_sessions,
            'grade'=>$request->grade
        ]);

        HomeBookingSlots::create([
            'order_id'=>$order->id,
            'date'=>date('Y-m-d'),
            'grade'=>$request->grade,
            'time'=>null,
            'status'=>'pending',
            'is_instant'=>true
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }

    private function initiateClinicBooking(Request $request){

        $request->validate([
            'clinic_id'=>'required|integer',
            'therapy_id'=>'required|integer',
            'schedule_type'=>'required|in:automatic,custom'
        ]);

        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $request->therapy_id);
        }])->find($request->clinic_id);

        if(!$clinic || empty($clinic->therapies->toArray())){
            return [
                'status'=>'failed',
                'message'=>'Clinic Or Therapy No Longer Exists'
            ];
        }

        $refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'schedule_type'=>$request->schedule_type,
            'order_place_state'=>'stage_1'
            ]);
        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);

        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$clinic->therapies[0]->id,
            'clinic_id'=>$clinic->id,
            'cost'=>0,
            'quantity'=>0,
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }


    /*
     * Select Time Slots For Scheduled Bookings
     */

    public function setSchedule(Request $request, $order_id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('details')
        ->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Order Exists'
            ];


        if($order->status!='pending' || $order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Your Booking Cannot Be Updated'
            ];

        if($order->details[0]->clinic_id!=null){
            return $this->setScheduleForClinicTherapy($request, $order);
        }else if($order->is_instant==0){
            return $this->setScheduleForHomeTherapy($request, $order);
        }

    }

    private function setScheduleForClinicTherapy(Request $request, $order){

        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($order){
            $therapies->where('therapies.id', $order->details[0]->entity_id);
        }])->find($order->details[0]->clinic_id);


        if($order->schedule_type=='automatic'){
            $request->validate([
                'num_sessions'=>'required|integer|max:50',
                'slot'=>'required|integer',
                'grade'=>'required|in:1,2,3,4'
            ]);
            $slot=TimeSlot::find($request->slot);
            if(!$slot || $slot->date < date('Y-m-d'))
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation'
                ];

            BookingSlot::where('order_id', $order->id)
                ->delete();

            if(!BookingSlot::createAutomaticSchedule($order, $request->grade, $slot, $request->num_sessions, 'pending')){
                return [
                    'status'=>'failed',
                    'message'=>'Enough Slots Are Not Available'
                ];
            }
            //var_dump($clinic->toArray());die;
            switch($request->grade){
                case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
                    break;
                case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
                    break;
                case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
                    break;
                case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
                    break;
            }

            //$cost=$cost*$request->num_sessions;
            //$order->total_cost=$cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else if($order->schedule_type=='custom'){
            $request->validate([
                'slots'=>'required|array',
                'slots.*'=>'integer',
                'grade'=>'required|in:1,2,3,4'
            ]);

            $slots=TimeSlot::whereIn('id', $request->slots)->get();
            if(empty($slots->toArray()))
                return [
                    'status'=>'failed',
                    'message'=>'No Time Slot Selected'
                ];

            $alldateslots=TimeSlot::where('date', $slots[0]->date)->select('id')->get();

            $slotsarr=[];
            foreach($alldateslots as $s)
                $slotsarr[]=$s->id;
            if(count($slotsarr))
                BookingSlot::where('order_id', $order->id)
                    ->whereIn('slot_id', $slotsarr)->delete();

            $cost=0;

            foreach($slots as $slot){

                BookingSlot::create([
                    'order_id'=>$order->id,
                    'clinic_id'=>$order->details[0]->clinic_id,
                    'therapy_id'=>$order->details[0]->entity_id,
                    'slot_id'=>$slot->id,
                    'grade'=>$request->grade,
                    'status'=>'pending',
                ]);

                switch($request->grade){
                    case 1:$cost=$cost+($clinic->therapies[0]->pivot->grade1_price??0);
                        break;
                    case 2:$cost=$cost+($clinic->therapies[0]->pivot->grade2_price??0);
                        break;
                    case 3:$cost=$cost+($clinic->therapies[0]->pivot->grade3_price??0);
                        break;
                    case 4:$cost=$cost+($clinic->therapies[0]->pivot->grade4_price??0);
                        break;
                }

                //$order->total_cost=$order->total_cost+$cost;

            }

            $order->order_place_state='stage_2';
            $order->save();

        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Therapy Timings have been Saved'
        ];
    }

    private function setScheduleForHomeTherapy(Request $request, $order){

        $therapy=Therapy::find($order->details[0]->entity_id);

        if($order->schedule_type=='automatic'){
            $request->validate([
                'num_sessions'=>'required|integer|max:50',
                'slot'=>'required|integer',
                'grade'=>'required|in:1,2,3,4'
            ]);
            $slot=DailyBookingsSlots::find($request->slot);
            //if(!$slot || $slot->date < date('Y-m-d'))
            if(!$slot)
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation'
                ];

            HomeBookingSlots::where('order_id', $order->id)
                ->delete();

            if(!HomeBookingSlots::createAutomaticSchedule($order, $request->grade, $slot, $request->num_sessions, 'pending')){
                return [
                    'status'=>'failed',
                    'message'=>'Enough Slots Are Not Available'
                ];
            }
            //var_dump($clinic->toArray());die;
//            switch($request->grade){
//                case 1:$cost=($therapy->grade1_price??0);
//                    break;
//                case 2:$cost=($therapy->grade2_price??0);
//                    break;
//                case 3:$cost=($therapy->grade3_price??0);
//                    break;
//                case 4:$cost=($therapy->grade4_price??0);
//                    break;
//            }

            //$cost=$cost*$request->num_sessions;
            //$order->total_cost=$cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else if($order->schedule_type=='custom'){
            $request->validate([
                'slots'=>'required|array',
                'slots.*'=>'integer',
                'grade'=>'required|in:1,2,3,4'
            ]);

            $slots=DailyBookingsSlots::whereIn('id', $request->slots)->get();
            if(empty($slots->toArray()))
                return [
                    'status'=>'failed',
                    'message'=>'No Time Slot Selected'
                ];

            $alldateslots=HomeBookingSlots::where('date', $slots[0]->date)->select('id')->get();

            $slotsarr=[];
            foreach($alldateslots as $s)
                $slotsarr[]=$s->id;
            if(count($slotsarr))
                HomeBookingSlots::where('order_id', $order->id)
                    ->whereIn('slot_id', $slotsarr)->delete();

            $cost=0;

            foreach($slots as $slot){

                HomeBookingSlots::create([
                    'order_id'=>$order->id,
                    'slot_id'=>$slot->id,
                    'grade'=>$request->grade,
                    'status'=>'pending',
                ]);

//                switch($request->grade){
//                    case 1:$cost=$cost+($clinic->therapies[0]->pivot->grade1_price??0);
//                        break;
//                    case 2:$cost=$cost+($clinic->therapies[0]->pivot->grade2_price??0);
//                        break;
//                    case 3:$cost=$cost+($clinic->therapies[0]->pivot->grade3_price??0);
//                        break;
//                    case 4:$cost=$cost+($clinic->therapies[0]->pivot->grade4_price??0);
//                        break;
//                }
            }

            //$order->total_cost=$order->total_cost+$cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Therapy Timings have been Saved'
        ];
    }



    /*
     * Display Scheduled For Therapy Bookings
     */
    public function displaySchedule(Request $request, $order_id){

        $show_add_more_slots=0;
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('details')->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'success',
                'message'=>'Invalid Operation'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation'
            ];
        $clinic_id=$order->details[0]->clinic_id;
        $therapy_id=$order->details[0]->entity_id;

        if($order->details[0]->clinic_id){
            $bookings=BookingSlot::with('timeslot')
                ->where('order_id', $order->id)
                ->orderBy('slot_id', 'asc')
                ->get();
        }else{
            $bookings=HomeBookingSlots::with('timeslot')
                ->where('order_id',$order->id)
                ->orderBy('slot_id', 'asc')
                ->get();
        }

        if($order->status=='pending' && $order->schedule_type=='custom'){
            $show_add_more_slots=1;
        }

        if($order->status=='pending'){
            $continue_text='Continue';
        }else {
            $continue_text='Close';
        }



        $schedules=[];

        foreach($bookings as $schedule){
            $grade=$schedule->grade==1?'Diamond':($schedule->grade==2?'Platinum':$schedule->grade==3?'Silver':$schedule->grade==4?'Gold':'');

            //die('dd');
            $schedules[]=[
                'show_delete'=>1,
                'date'=>$schedule->timeslot->date??$schedule->date,
                'time'=>'1 Session at '.($schedule->timeslot->start_time??'Instant Booking'),
                'grade'=>$grade,
                'id'=>$schedule->id,
                'show_cancel'=>in_array($order->status,['confirmed'])?1:0,
                'show_reschedule'=>in_array($order->status,['confirmed'])?1:0
            ];
        }

        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('schedules','clinic_id', 'therapy_id', 'order_id', 'show_add_more_slots','continue_text')
        ];

    }


    /*
     * Delete a session only for non confirmed orders
     */
    public function deleteBooking(Request $request, $order_id, $booking_id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('order.details')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending'])
            ->find($order_id);

        if(!$order || $order->detail[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        if($order->details[0]->clinic_id){
            $booking=BookingSlot::find($booking_id);
            if(!in_array($booking->status, ['pending'])){
                return [
                    'status'=>'failed',
                    'message'=>'Booking Cannot Be Cancelled'
                ];
            }

            $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($order){
                $therapies->where('therapies.id', $order->details[0]->entity_id);
            }])->find($order->details[0]->clinic_id);

            switch($booking->grade){
                case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
                    break;
                case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
                    break;
                case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
                    break;
                case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
                    break;
            }
        }else{
            $booking=HomeBookingSlots::find($booking_id);
            if(!in_array($booking->status, ['pending'])){
                return [
                    'status'=>'failed',
                    'message'=>'Booking Cannot Be Cancelled'
                ];
            }

            $therapy=Therapy::find($order->details[0]->entity_id);

            switch($booking->grade){
                case 1:$cost=($therapy->grade1_price??0);
                    break;
                case 2:$cost=($therapy->grade2_price??0);
                    break;
                case 3:$cost=($therapy->grade3_price??0);
                    break;
                case 4:$cost=($therapy->grade4_price??0);
                    break;
            }
        }

        $order->total_cost=$order->total_cost-$cost;
        $order->save();
        $booking->delete();

        return [
            'status'=>'success',
            'message'=>'Session Has Been Deleted'
        ];
    }


    public function addContactDetails(Request $request, $id){

        $request->validate([
           'name'=>'required|max:60|string',
           'email'=>'email',
           'mobile'=>'required|digits:10',
            'address'=>'string|max:100|nullable',
            'lat'=>'numeric',
            'lang'=>'numeric'
        ]);

        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];
        $request->merge(['order_details_completed'=>true]);
        if($order->update($request->only('name','email','address', 'mobile','lat', 'lang'))){
            return [
                'status'=>'success',
                'message'=>'Address has been updated'
            ];
        }

    }

    public function orderdetails(Request $request, $id){

        $show_cancel_product=0;
        $show_cancel=0;
        $show_reschedule=0;
        $show_time_slots_button=0;

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with(['details.entity', 'details.clinic'])->where('user_id', $user->id)->find($id);


        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if($order->status=='pending') {
            $order->total_cost = Order::getTotal($order);
            $order->save();
        }

        $itemdetails=[];
        foreach($order->details as $detail){
            if($detail->entity instanceof Therapy){

                $order->booking_date='2020-08-31';
                $order->booking_time='08:00 PM';

                $itemdetails[]=[
                    'name'=>($detail->entity->name??'')." ( Grade $detail->grade )",
                    'small'=>$detail->quantity.(!empty($detail->clinic->name)?' sesions at '.$detail->clinic->name:' sessions'),
                    'price'=>$detail->cost,
                    'quantity'=>$detail->quantity,
                    'image'=>$detail->entity->image??'',
                    'booking_date'=>$order->booking_date,
                    'booking_time'=>$order->booking_time
                ];



            }
            else{
                $itemdetails[]=[
                    'name'=>$detail->entity->name??'',
                    'small'=>$detail->entity->company??'',
                    'price'=>$detail->cost,
                    'quantity'=>$detail->quantity,
                    'image'=>$detail->entity->image??'',
                    'booking_date'=>$order->booking_date,
                    'booking_time'=>$order->booking_time,
                    'show_review'=>$order->status=='completed'?1:0
                ];
            }
        }

        // options to be displayed
        if($order->status=='confirmed'){
            if($order->details[0]->entity instanceof Product){
                $show_cancel_product=1;
            }else{
                $show_cancel=1;
            }
            if($order->details[0]->entity instanceof Therapy  && $order->is_instant!=1){
                $show_reschedule=1;
            }

        }

        if($order->details[0]->entity instanceof Therapy  && ( $order->details[0]->clinic_id!=null || $order->is_instant==0))
            $show_time_slots_button=1;


        $date=date('Y-m-d');
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($date))),
                'value'=>$date
            ];
            $date=date('Y-m-d', strtotime('+'.$i.' days', strtotime($date)));
        }
        $date=date('Y-m-d h:i:s');
        for($i=9; $i<=17;$i++){
            $timings[]=[
                'text'=>date('h:i A', strtotime($date)),
                'value'=>date('H:i', strtotime($date))
            ];
            $date=date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
        }

        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('id', 'total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address','booking_date', 'booking_time','is_instant','status'),
                'itemdetails'=>$itemdetails,
                'balance'=>Wallet::balance($user->id),
                'points'=>Wallet::points($user->id),
                'show_cancel'=>$show_cancel??0,
                'show_reschedule'=>$show_reschedule??0,
                'show_cancel_product'=>$show_cancel_product??0,
                'dates'=>$dates,
                'timings'=>$timings,
                'show_time_slots_btn'=>$show_time_slots_button??0
            ]
        ];
    }

    public function rescheduleOrder(Request $request, $id){

        $request->validate([
            'time'=>'required|date_format:H:i',
            'date'=>'required|date_format:Y-m-d',
        ]);

        $therapy_reschedule_status=[
            'confirmed', 'in-process'
        ];

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with(['details.entity', 'details.clinic'])->where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if(!in_array($order->status, $therapy_reschedule_status)){
            return [
                'status'=>'failed',
                'message'=>'Order cannot be rescheduled now'
            ];
        }

        if($order->details[0]->entity instanceof Therapy && $order->is_instant != 1){
            $order->booking_date=$request->date;
            $order->booking_time=$request->time;
            $order->save();
            return [
                'status'=>'success',
                'message'=>'Your booking has been rescheduled'
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid operation performed'
            ];
        }

    }


    /*
     * Cancel Single Session
     */

    public function cancelBooking(Request $request, $id){

        $request->validate([
            'booking_id'=>'required|integer'
        ]);

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with(['details'])->where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if($order->details[0]->entity instanceof Therapy){

            if($order->is_instant){
                return $this->cancelInstantTherapyBooking($request, $order);
            }else{
                if($order->details[0]->clinic_id){
                    return $this->cancelClinicTherapyBooking($request, $order);
                }else{
                    return $this->cancelHomeTherapyBooking($request, $order);
                }
            }
        }


        return [
            'status'=>'failed',
            'message'=>'Unrecognized Request'
        ];

    }

    private function cancelInstantTherapyBooking(Request $request, $order){

        $booking=HomeBookingSlots::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled'
            ];

        $booking->status='cancelled';
        $booking->save();

        $order->status='cancelled';
        $order->save();

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
        ];
    }

    private function cancelClinicTherapyBooking(Request $request, $order){

        $booking=BookingSlot::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled'
            ];

        $booking->status='cancelled';
        $booking->save();

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
        ];
    }

    private function cancelHomeTherapyBooking(Request $request, $order){

        $booking=HomeBookingSlots::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Order cannot be cancelled now'
            ];

        $booking->status='cancelled';
        $booking->save();

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
        ];
    }


    /*
     * Get Available Slots For Booking
     */

    public function getAvailableSlots(Request $request, $order_id){

        $user=$request->user;
        $order=Order::with(['details'])->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];
        //dd($order);
        if($order->details[0]->entity_type=='App\Models\Therapy'){
            if($order->details[0]->clinic_id){
                return $this->getClinicAvailableSlots($order,$order->details[0]->clinic_id, $order->details[0]->entity_id, $request->date??date('Y-m-d'));
            }else{
                return $this->getTherapyAvailableSlots($order,$order->details[0]->entity_id, $request->date??date('Y-m-d'));
            }
        }

        return [
            'status'=>'failed',
            'message'=>'Unreconized Request'
        ];
    }

    private function getClinicAvailableSlots($order, $clinic_id, $therapy_id, $date){
        $date=date('Y-m-d', strtotime($date));
        $selected_date=$date;
        $today=date('Y-m-d');
        //var_dump($therapy_id);die;
        $clinic=Clinic::with(['therapies'=>function($therapies) use($therapy_id){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $therapy_id)->where('clinic_therapies.isactive', true);
        }])->find($clinic_id);
        //dd($clinic);
        if(!$clinic || empty($clinic->therapies->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No clinic found'
            ];

        $timeslots=TimeSlot::getTimeSlots($clinic, $date);

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];
        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id')
        ];
    }

    private function getTherapyAvailableSlots($order,$therapy_id, $date){
        $therapy=Therapy::active()->find($therapy_id);
        //dd($therapy);
        $timeslots=DailyBookingsSlots::getTimeSlots($therapy, $date);

        $selected_date=$date;

        $today=date('Y-m-d');

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }


        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];
        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id')
        ];

    }


    /*
     * Reschedule Functionality
     */
    public function getRescheduleSlots(Request $request, $order_id, $booking_id){

        $date=$request->date??date('Y-m-d');
        $selected_date=$date;
        $today=date('Y-m-d');

        $user=$request->user;

        $order=Order::with('details.clinic', 'details.entity')
            ->where('status', 'confirmed')
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Unrecognized Request'
            ];

        if($order->details[0]->clinic_id){
            $booking=BookingSlot::find($booking_id);
        }else{
            $booking=HomeBookingSlots::find($booking_id);
        }
        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->clinic_id){
            $availableslots=TimeSlot::getRescheduleTimeSlots($order->details[0]->clinic, $date, $booking);
        }else{
            $availableslots=DailyBookingsSlots::getRescheduleTimeSlots($order->details[0]->entity, $date,$booking);
        }

        $timeslots=[
            $availableslots
        ];

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id', 'booking_id')
        ];

    }


    public function rescheduleBooking(Request $request, $order_id, $booking_id){

        $request->validate([
            'slot_id'=>'required|integer'
        ]);

        $user=$request->user;

        $order=Order::with('details')
            ->where('status', 'confirmed')
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Unrecognized Request'
            ];

        if($order->is_instant){
            return $this->rescheduleHomeTherapyBooking($request, $order,$booking_id,$user);
        }else{
            if($order->details[0]->clinic_id){
                return $this->rescheduleClinicTherapyBooking($request, $order,$booking_id,$user);
            }else{
                return $this->rescheduleHomeTherapyBooking($request, $order,$booking_id,$user);
            }
        }
    }


    private function rescheduleHomeTherapyBooking(Request $request,$order, $booking_id, $user){
        $slot=DailyBookingsSlots::find($request->slot_id);
        if(!$slot)
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        $booking=HomeBookingSlots::with('timeslot')->where('status', 'confirmed')
               ->where('order_id', $order->id)
               ->find($booking_id);
           if(!$booking)
               return [
                   'status'=>'failed',
                   'message'=>'Booking Cannot Be Rescheduled'
               ];

           if($booking->is_instant){
               if($booking->date > date('Y-m-d')){

                   RescheduleRequest::where('order_id', $order->id)
                       ->where('is_paid', 0)->delete();

                   RescheduleRequest::create([
                       'refid'=>env('MACHINE_ID').time(),
                       'order_id'=>$order->id,
                       'booking_id'=>$booking_id,
                       'new_slot_id'=>$request->slot_id,
                       'total_cost'=>200
                   ]);

                   return [
                       'status'=>'success',
                       'data'=>[
                           'payment_status'=>'no',
                           'header'=>'Payment For Booking Reschedule',
                           'old_time'=>$booking->date.' Instant Booking',
                           'new_time'=>$slot->date.' '.$slot->start_time,
                           'amount'=>'20% deduction',
                           'wallet_balance'=>Wallet::balance($user->id)
                       ]
                   ];
               }else{

                   $booking->slot_id=$slot->id;
                   $booking->is_instant=0;
                   $booking->save();

                   $order->is_instant=0;
                   $order->save();

                   return [
                       'status'=>'success',
                       'date'=>[
                           'payment_status'=>'yes',
                           'header'=>'Booking Reschedule Successfull',
                           'old_time'=>'',
                           'new_time'=>'',
                           'amount'=>''
                       ]
                   ];
               }
           }else{
               if(date('Y-m-d H:i:s', strtotime('+2 hours')) > $booking->timeslot->date.' '.$booking->internal_start_time){

                   RescheduleRequest::where('order_id', $order->id)
                       ->where('is_paid', 0)->delete();

                   RescheduleRequest::create([
                       'refid'=>env('MACHINE_ID').time(),
                       'order_id'=>$order->id,
                       'booking_id'=>$booking_id,
                       'old_slot_id'=>$booking->slot_id,
                       'new_slot_id'=>$request->slot_id,
                       'total_cost'=>200
                   ]);

                   return [
                       'status'=>'success',
                       'data'=>[
                           'payment_status'=>'no',
                           'header'=>'Payment For Booking Reschedule',
                           'old_time'=>$booking->timeslot->date.' '.$booking->timeslot->start_time,
                           'new_time'=>$slot->date.' '.$slot->start_time,
                           'amount'=>'20% deduction',
                           'wallet_balance'=>Wallet::balance($user->id)
                       ]
                   ];
               }else{

                   $booking->slot_id=$slot->id;
                   $booking->save();

                   return [
                       'status'=>'success',
                       'date'=>[
                           'payment_status'=>'yes',
                           'header'=>'Booking Reschedule Successfull',
                           'old_time'=>'',
                           'new_time'=>'',
                           'amount'=>''
                       ]
                   ];
               }
           }

    }

    private function rescheduleClinicTherapyBooking(Request $request,$order, $booking_id, $user){
        $slot=TimeSlot::find($request->slot_id);
        if(!$slot)
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        $booking=BookingSlot::with('timeslot')->where('status', 'confirmed')
            ->where('order_id', $order->id)
            ->find($booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Rescheduled'
            ];

        if(date('Y-m-d H:i:s', strtotime('+2 hours')) > $booking->timeslot->date.' '.$booking->internal_start_time){

            RescheduleRequest::where('order_id', $order->id)
                ->where('is_paid', 0)->delete();

            RescheduleRequest::create([
                'refid'=>env('MACHINE_ID').time(),
                'order_id'=>$order->id,
                'booking_id'=>$booking_id,
                'old_slot_id'=>$booking->slot_id,
                'new_slot_id'=>$request->slot_id,
                'total_cost'=>200
            ]);

            return [
                'status'=>'success',
                'data'=>[
                    'payment_status'=>'no',
                    'header'=>'Payment For Booking Reschedule',
                    'old_time'=>$booking->timeslot->date.' '.$booking->timeslot->start_time,
                    'new_time'=>$slot->date.' '.$slot->start_time,
                    'amount'=>'20% deduction',
                    'wallet_balance'=>Wallet::balance($user->id)
                ]
            ];
        }else{

            $booking->slot_id=$slot->id;
            $booking->save();

            return [
                'status'=>'success',
                'date'=>[
                    'date'=>[
                        'payment_status'=>'yes',
                        'header'=>'Booking Reschedule Successfull',
                        'old_time'=>'',
                        'new_time'=>'',
                        'amount'=>''
                    ]
                ]
            ];
        }

    }


    /*
     * Cancellation Of Complete Order Sessions Or Product Purchase
     */
    public function cancelAll(Request $request, $order_id){
        $user=$request->user;

        $order=Order::with(['details', 'bookingslots', 'homebookingslots'])
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No record Found'
            ];

        if(!in_array($order->status, ['confirmed']))
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled Now'
            ];


        if($order->details[0]->entity instanceof Product)
            return $this->cancelProductsBooking($order);

        if($order->details[0]->entity instanceof Therapy){
            if($order->is_instant)
                return $this->cancelAllInstantTherapy($order);
            else{
                if($order->details[0]->clinic_id)
                    return $this->cancelAllClinicTherapy($order);
                else
                    return $this->cancelAllHomeTherapy($order);

            }
        }
    }

    private function cancelProductsBooking($order){

        $product_cancellation_status=[
            'confirmed'
        ];

        if(!in_array($order->status, $product_cancellation_status)){
            return [
                'status'=>'failed',
                'message'=>'Order cannot be cancelled now'
            ];
        }

        $order->status='cancelled';
        $order->save();
        return [
            'status'=>'success',
            'message'=>'Order has been cancelled. Refund process will be initiated shortly'
        ];

    }

    private function cancelAllInstantTherapy($order){
        $booking=$order->homebookingslots[0];
        $booking->status='cancelled';
        $booking->save();
        $order->status='cancelled';
        $order->save();

        /*
         * Put Deduction Calculation Here
         */

        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];
    }

    private function cancelAllClinicTherapy($order){
        $bookings=$order->bookingSlots;
        foreach($bookings as $booking){
            if($booking->status=='pending'){
                $booking->status='cancelled';
                $booking->save();
            }
        }

        $order->status='cancelled';
        $order->save();

        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];

    }

    private function cancelAllHomeTherapy($order){
        $bookings=$order->homebookingslots;
        foreach($bookings as $booking){
            if($booking->status=='pending'){
                $booking->status='cancelled';
                $booking->save();
            }
        }

        $order->status='cancelled';
        $order->save();

        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];
    }


    //    private function cancelTherapyBooking($order){
//
//
//
//        $therapy_cancellation_status=[
//            'confirmed'
//        ];
//
//        if(!in_array($order->status, $therapy_cancellation_status)){
//            return [
//                'status'=>'failed',
//                'message'=>'Order cannot be cancelled now'
//            ];
//        }
//
//        $order->status='cancelled';
//        $order->save();
//        return [
//            'status'=>'success',
//            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
//        ];
//
//    }

    //    public function initiateClinicBooking(Request $request){
//
//        $request->validate([
//            'clinic_id'=>'required|integer',
//            'therapy_id'=>'required|integer',
//            'num_sessions'=>'required|integer',
//            'grade'=>'required|integer|in:1,2,3,4',
//            'time'=>'required|date_format:H:i',
//            'date'=>'required|date_format:Y-m-d',
//        ]);
//
//        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
//            $therapies->where('therapies.isactive', true)->where('therapies.id', $request->therapy_id);
//        }])->find($request->clinic_id);
//
//        if(!$clinic || empty($clinic->therapies)){
//            return [
//                'status'=>'failed',
//                'message'=>'Invalid Operation Performed'
//            ];
//        }
//
//        //return $clinic;
//        $grade=$request->grade??1;
//        $num_sessions=$request->num_sessions??1;
//
//        switch($grade){
//            case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
//                break;
//            case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
//                break;
//            case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
//                break;
//            case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
//                break;
//        }
//
//        $refid=env('MACHINE_ID').time();
//        $order=Order::create([
//            'user_id'=>auth()->guard('customerapi')->user()->id,
//            'refid'=>$refid,
//            'status'=>'pending',
//            'total_cost'=>$cost*$num_sessions,
//            'booking_date'=>$request->date,
//            'booking_time'=>$request->time
//        ]);
//        OrderStatus::create([
//            'order_id'=>$order->id,
//            'current_status'=>$order->status
//        ]);
//        OrderDetail::create([
//            'order_id'=>$order->id,
//            'entity_type'=>'App\Models\Therapy',
//            'entity_id'=>$clinic->therapies[0]->id,
//            'clinic_id'=>$clinic->id,
//            'cost'=>$cost,
//            'quantity'=>$num_sessions,
//            'grade'=>$request->grade
//        ]);
//
//        return [
//            'status'=>'success',
//            'data'=>[
//                'order_id'=>$order->id
//            ]
//        ];
//    }

}
