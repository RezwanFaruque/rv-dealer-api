<?php

namespace App\Http\Controllers;

use App\RvBuyer;
use Illuminate\Http\Request;
use App\Event;
use App\FavoriteUnit;
use App\User;
use App\Rv;
use DateTime;
use Auth;
use Illuminate\Support\Facades\Hash;


class UserProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEvents(Request $request){

        $response_type = $request->format('json');
        $now = new DateTime();
        // dd($now);
        if($request->has('per_page')){
            $per_page = $request->per_page;
            
            if($per_page == 'all'){
                $per_page = Event::count();
            }
        } else {
            $per_page = 10;
        }
        
        $events = Event::where('date_time', '>=' , $now)->paginate($per_page);

        
        
        if(count($events)){
            $response = [
                'status'    =>      'success',
                'message'   =>      'Events Founds',
                'data'      =>      $events,
                'total'     =>      count($events),
            ];
        }else{
            $response = [
                'status'    => 'error',
                'message'   => 'Events Does Not Have Any Data',
            ];
        }

        return response()->$response_type($response);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function createFavoriteUnits(Request $request){

       
        $request->validate([
           
            'rv_id'     => 'required|unique:favorite_unites,rv_id,user_id|exists:rvs,id', 
            'user_id'   => 'required|unique:favorite_unites,user_id,rv_id|exists:users,id',
            
        ]);
        
        $response_type = $request->format('json');

        // create favorite unit object
        $favorite_unit          = new FavoriteUnit();
        $favorite_unit->rv_id   = $request->rv_id;
        $favorite_unit->user_id = $request->user_id;

        // save favorite unit data
        $favorite_unit->save();

        // return json response
        if($favorite_unit){
            $response = [
                'status'    => 'success',
                'message'   => 'Favorite Unit created successfully',
            ];
        }else{
            $response = [
                'status'    => 'error',
                'message'   => 'Favorite Unit creation failed',
            ];
        }

        // return response
        return response()->$response_type($response);
     }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function getFavoriteUnites(Request $request){

            $response_type = $request->format('json');

            if($request->has('per_page')){
                $per_page = $request->per_page;
                
                if($per_page == 'all'){
                    $per_page = FavoriteUnit::count();
                }
            } else {
                $per_page = 10;
            }

            // 
            $favorite_units = FavoriteUnit::with('rvs')->with('rv_Images')->paginate($per_page);
            
            if(count($favorite_units)){

                $response = [
                    'status'    =>  'success',
                    'message'   =>  'Favorite items founded',
                    'data'      =>  $favorite_units,
                    'total'     =>  count($favorite_units),
                ];
            }else{
                $response = [
                    'status'    =>   'error',
                    'message'   =>   'Data Empty',
                ];
            }


            return response()->$response_type($response);

     }


    /**
     * Delete a favorite unit from list of favorite unis.
     *
     * @return \Illuminate\Http\Response
     */

     public function deleteFavoriteUnit(Request $request){

        $response_type = $request->format('json');

        $rv_id = $request->rv_id;
        $user_id = $request->user_id;

        $favorite_unit = FavoriteUnit::where('rv_id',$rv_id)->where('user_id',$user_id)->first();
        
        if($favorite_unit){
            $favorite_unit->delete();
            $response = [
                'status'  => 'success',
                'message' => 'Favorite Units Deleted Successfully',
            ];
        }else{
            $response = [
                'status'  => 'error',
                'message' => 'Favorite Unit does not exist',
            ];

        }
        return response()->$response_type($response);

     }


    /**
     * Change  user Notification status on user table with this section
     *
     * @return \Illuminate\Http\Response
     */

     public function changeUserNotificationStatus(Request $request){

        $response_type = $request->format('json');

        
       $request->validate([
           'notification_status' =>     'required|boolean',
           'user_id'             =>     'required|exists:users,id'
       ]);

      
      
        $change_status = User::find($request->user_id);
       

        // after finding user change the notification status and return response
        if($change_status){

            $change_status->notification_status = $request->notification_status;

            // update user status
            $change_status->update();

            // response after update
            $response = [
            'status'        =>  'success',
            'message'       =>  'Notification status change successfully'
            ];
        }else{

            // if notification status update failed
            $response = [
                'status'    => 'error',
                'message'   => 'Notification status change failed',
                'user'      =>  'User Id does not exist'
            ];
        }

        return response()->$response_type($response);
     }


     
       /**
     * Fetch rv and rv-image  according to brought units
     *  and owner status with this section
     *
     * @return \Illuminate\Http\Response
     */

     public function getRvByBroughtUnit(Request $request,$id){

        $response_type = $request->format('json');
        
        if($request->has('per_page')){
            $per_page = $request->per_page;
            
            if($per_page == 'all'){
                $per_page = RvBuyer::count();
            }
        } else {
            $per_page = 10;
        }

        $user = User::find($id);
        $user_email = $user->email;
       
        $brought_units = RvBuyer::select('rv_id')->groupBy('rv_id')
                                    ->where('email', '=', $user_email)
                                    ->where('owner_status','=','1')
                                    ->with('rv')
                                    ->with('rvImages')
                                    ->paginate($per_page);

                    // dd($brought_units);
        
        if(count($brought_units)){
            $response = [
                'status'    =>      'success',
                'message'   =>      'Brought Unit Founded',
                'data'      =>      $brought_units,
                'total'     =>      count($brought_units),
            ];
        }else{
            
            $response = [
                'status'   =>   'error',
                'message'  =>   'Brought unit does not have any data'
            ];
        }
       
        return response()->$response_type($response);
     }
     


    /**
     * Basic dashboard
     *
     * @return \Illuminate\Http\Response
     */

     public function getDashboardBasicInfo(Request $request,$id){

        $response_type = $request->format('json');

        $user = User::find($id);

        

        if($user){

            $notification_status            = $user->notification_status;
            $number_of_favorite_units       = FavoriteUnit::where('user_id','=',$id)->count();
            $number_of_brought_units        = RvBuyer::select('rv_id')->groupBy('rv_id')->where('email', '=' , $user->email)->where('owner_status','1')->get();
            // dd(count($number_of_brought_units));


            $response = [
                'status'                    =>  'success',
                'message'                   =>  'Basic Dashboard',
                'notification_status'       =>  $notification_status,
                'number_of_favorite_units'  =>  $number_of_favorite_units,
                'number_of_brought_units'   =>  count($number_of_brought_units),   
            ];
        }else{
            $response = [
                'status'                    =>  'error',
                'message'                   =>  'Unauthenticated User does not exist',
                   
            ];
        }
        

        return response()->$response_type($response);
     }


    /**
     * change user password 
     *
     * @return \Illuminate\Http\Response
     */

     public function changePassword(Request $request){

        $response_type = $request->format('json');

        // validating the password
        $request->validate([
        'old_password'         =>     'required',
        'new_password'         =>      'min:6|required_with:confirm_password|same:confirm_password',
        'confirm_password'     =>      'min:6|same:confirm_password'
       
        ]);

        // find Authenticate user
        $user_id = Auth::user()->id;

        // find user id in user table
        $user = User::find($user_id);


        // change password
        if(Hash::check($request->old_password, $user->password)){
            $user->password = Hash::make($request->new_password);
            $user->save();
            
            // if password change  success 
            $response = [
                'status' => 'success',
                'message' => 'Password changed successfully'
            ];
        } else {

            //if password change failed 
            $response = [
                'status' => 'error',
                'message' => 'Old password did not match'
            ];
        }

        

        return response()->$response_type($response);

     }



     /**
     * Register user  
     *
     * @return \Illuminate\Http\Response
     */
     public function registerUser(Request $request){


        $response_type = $request->format('json');

        // validate the user
        $request->validate([

            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'phone'         => 'required',
            
            
        ]);

        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->name = $request->first_name.' '.$request->last_name;

        $user->password = Hash::make($request->password);

        $user->save();

        if($user){
            $response = [
                'status' => 'success',
                'message' => 'User Registered Successfully',
            ];
        }else{
            $response = [
                'status' => 'Error',
                'message' => 'User Registration Failed',
            ];
        }



        return response()->$response_type($response);


     }

    

   
     public function updateUserInfo(Request $request){

        $response_type = $request->format('json');

        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email|unique:users,email,'.Auth::user()->id, //unique without auth update column  
            'phone'             => 'required',
            'secondary_phone'   =>  'required',
        ]);


        $user_id = Auth::user()->id;

        $user = User::find($user_id);

        if($user){

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->secondary_phone = $request->secondary_phone;

            $user->name = $request->first_name.' '.$request->last_name;

            $user->date_of_birth= date('Y-m-d H:i:s', strtotime($request->date_of_birth));
            
            $user->address = $request->address;

            $user->save();

            if($user){
                $response = [
                    'status' => 'success',
                    'message' => 'User information updated successfully'
                ];
            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'User information updated failed'
                ];
            }


        }
        return response()->$response_type($response);
       
     }



}
