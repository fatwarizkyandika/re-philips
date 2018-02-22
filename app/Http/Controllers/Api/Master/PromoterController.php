<?php

namespace App\Http\Controllers\Api\Master;

use App\Attendance;
use App\AttendanceDetail;
use App\DmArea;
use App\EmployeeStore;
use App\Reports\SummaryTargetActual;
use App\RsmRegion;
use App\SpvDemo;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JWTAuth;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class PromoterController extends Controller
{
    public function getAttendanceForSupervisor(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        if(count($spvDemoIds) > 0){
            $storeIds = $spvDemoIds;
        }        

        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role_group', '<>', 'Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');

        if(count($spvDemoIds) > 0){
            $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role', 'Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');
        }

        $attendances = Attendance::whereIn('user_id', $promoterIds)->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                     ->join('users', 'attendances.user_id', '=', 'users.id')
                     ->join('roles','roles.id','users.role_id')
                     ->select('attendances.id as attendance_id', 'users.name as name', 'users.nik as nik', 'roles.role as role', 'users.photo as photo', 'attendances.status as status', 'attendances.reject as reject')->get();

        foreach($attendances as $attendance){

            $detail = AttendanceDetail::where('attendance_id', $attendance->attendance_id)
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->select('attendance_details.check_in', 'attendance_details.check_in_longitude', 'attendance_details.check_in_latitude', 'attendance_details.check_in_location',
                        'attendance_details.check_out', 'attendance_details.check_out_longitude', 'attendance_details.check_out_latitude', 'attendance_details.check_out_location', 'attendance_details.detail as keterangan',
                        'stores.store_id', 'stores.store_name_1', 'stores.store_name_2')
                    ->get();

            if($attendance->status == 'Masuk'){
                $attendance['detail'] = $detail;
            }else{
                if($attendance->reject == '1'){
                    $attendance['detail'] = $detail;
                }else {
                    $attendance['detail'] = [];
                }
            }

        }

        return response()->json($attendances);
    }

    public function getAttendanceForSupervisorWithParam(Request $request, $id){

        $user = User::where('id', $id)->first();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $spvDemoIds = SpvDemo::where('user_id', $user->id)->pluck('store_id');

        if(count($spvDemoIds) > 0){
            $storeIds = $spvDemoIds;
        }

        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role', '<>', 'Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');

        if(count($spvDemoIds) > 0){
            $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)
                        ->whereHas('user', function ($query){
                            return $query->whereHas('role', function($query2){
                                return $query2->where('role', 'Demonstrator DA');
                            });
                        })
                        ->pluck('user_id');
        }

        $attendances = Attendance::whereIn('user_id', $promoterIds)->where('date', Carbon::parse($request->date)->format('Y-m-d'))
                     ->join('users', 'attendances.user_id', '=', 'users.id')
                     ->join('roles','roles.id','users.role_id')
                     ->select('attendances.id as attendance_id', 'users.name as name', 'users.nik as nik', 'roles.role as role', 'users.photo as photo', 'attendances.status as status', 'attendances.reject as reject')->get();

        foreach($attendances as $attendance){

            $detail = AttendanceDetail::where('attendance_id', $attendance->attendance_id)
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->select('attendance_details.check_in', 'attendance_details.check_in_longitude', 'attendance_details.check_in_latitude', 'attendance_details.check_in_location',
                        'attendance_details.check_out', 'attendance_details.check_out_longitude', 'attendance_details.check_out_latitude', 'attendance_details.check_out_location', 'attendance_details.detail as keterangan',
                        'stores.store_id', 'stores.store_name_1', 'stores.store_name_2')
                    ->get();

            if($attendance->status == 'Masuk'){
                $attendance['detail'] = $detail;
            }else{
                if($attendance->reject == '1'){
                    $attendance['detail'] = $detail;
                }else {
                    $attendance['detail'] = [];
                }
            }

        }

        return response()->json($attendances);
    }

    public function reject(Request $request){

        $attendance = Attendance::where('id', $request->id);

        try{

            $attendance->update([
                'status' => 'Alpha',
                'reject' => '1'
            ]);

        }catch (\Exception $exception){
            return response()->json(['status' => false, 'message' => 'Gagal melakukan reject'], 500);
        }

        return response()->json(['status' => false, 'message' => 'Berhasil melakukan reject'], 200);
    }

    public function undoReject(Request $request){

        $attendance = Attendance::where('id', $request->id);

        try{

            $attendance->update([
                'status' => 'Masuk',
                'reject' => '0'
            ]);

        }catch (\Exception $exception){
            return response()->json(['status' => false, 'message' => 'Gagal melakukan undo reject'], 500);
        }

        return response()->json(['status' => false, 'message' => 'Berhasil melakukan undo reject'], 200);
    }

    public function approval(Request $request, $param){

        $attendances = Attendance::where('id', $request->attendance_id)->first();
        if(!$attendances){
            return response()->json(['status' => false, 'message' => 'Data absen tidak ditemukan'], 500);
        }
        $message = "";

        if($param == 1){

            if($attendances->status != 'Pending Sakit'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval sakit'], 200);
            }

            $attendances->update(['status' => 'Sakit']);
            $message = "Sakit";

        }else if($param == 2){

            if($attendances->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval izin'], 200);
            }

            $attendances->update(['status' => 'Izin']);
            $message = "Izin";
        }else if($param == 3){

            if($attendances->status != 'Pending Off'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval off'], 200);
            }

            $attendances->update(['status' => 'Off']);
            $message = "Off";

            /* Change Weekly Target */
            $target = SummaryTargetActual::where('user_id', $attendances->user_id)->get();

            if($target){ // If Had

                foreach ($target as $data){

                    /* Change Weekly Target */
                    $total['da'] = $data['target_da'];
                    $total['pc'] = $data['target_pc'];
                    $total['mcc'] = $data['target_mcc'];

                    $this->changeWeekly($data, $total);

                }

            }
        }

        return response()->json(['status' => true, 'id_attendance' => $attendances->id, 'message' => 'Approval '.$message.' berhasil']);

    }

    public function checkAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $result = new Collection();

        $details = AttendanceDetail::where('attendances.status', 'Masuk')->where('attendances.user_id', $user->id)
                    ->where('attendance_details.is_store', 1)
                    ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->whereMonth('attendances.date', '=', Carbon::now()->format('m'))
                    ->select('attendances.date as date', 'attendance_details.check_in as check_in', 'attendance_details.check_out as check_out',
                        'stores.store_name_1 as store_name')
                    ->get();

        foreach ($details as $data){
            $result->push([
                'date' => $data->date,
                'check_in' => $data->check_in,
                'check_out' => $data->check_out,
                'store_name' => $data->store_name,
            ]);
        }

        $details2 = AttendanceDetail::where('attendances.status', 'Masuk')->where('attendances.user_id', $user->id)
                    ->where('attendance_details.is_store', 0)
                    ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                    ->join('places', 'attendance_details.store_id', '=', 'places.id')
                    ->whereMonth('attendances.date', '=', Carbon::now()->format('m'))
                    ->select('attendances.date as date', 'attendance_details.check_in as check_in', 'attendance_details.check_out as check_out',
                        'places.name as store_name')
                    ->get();

        foreach ($details2 as $data){
            $result->push([
                'date' => $data->date,
                'check_in' => $data->check_in,
                'check_out' => $data->check_out,
                'store_name' => $data->store_name,
            ]);
        }

        return response()->json($result);

    }

    public function checkNotAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $details = Attendance::where('attendances.user_id', $user->id)
                    ->where(function ($query) {
                        return $query->where('attendances.status', 'Pending Sakit')->orWhere('attendances.status', 'Pending Izin')->orWhere('attendances.status', 'Pending Off')
                                     ->orWhere('attendances.status', 'Sakit')->orWhere('attendances.status', 'Izin')
                                     ->orWhere('attendances.status', 'Alpha')->orWhere('attendances.status', 'Off');
                    })
                    ->where('date', '<=', Carbon::now())
                    ->whereMonth('date', '=', Carbon::now()->format('m'))
                    ->select('date', 'status')
                    ->get();

        return response()->json($details);

    }

    public function getSupervisor($param){

        $user = JWTAuth::parseToken()->authenticate();

        if($param == 1) { // BY NATIONAL

            $supervisor = User::where(function ($query) {
                return $query->whereHas('role', function($query2){
                    return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                });
            })->with('stores.district.area.region')->get();

//            return response()->json($supervisor);

            return response()->json($this->getSupervisorCollection($supervisor));

        }else if($param == 2) { // BY REGION

            $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');

            $supervisor = User::where(function ($query) {
                    return $query->whereHas('role', function($query2){
                        return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                    });
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area.region', function ($query) use ($regionIds){
                        return $query->whereIn('id', $regionIds);
                    })->get();

            $demoStoreIds = SpvDemo::whereHas('store.district.area.region', function ($query) use ($regionIds){
                                return $query->whereIn('id', $regionIds);
                           })->pluck('user_id');
            $spvdemo = User::with('spvDemos.store.district.area.region')->whereIn('id', $demoStoreIds)->get();

            return response()->json($this->getSupervisorCollection($supervisor, $spvdemo));

        }else if($param == 3) { // BY AREA

            $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');

            $supervisor = User::where(function ($query) {
                    return $query->whereHas('role', function($query2){
                        return $query2->where('role_group', 'Supervisor')->orWhere('role_group', 'Supervisor Hybrid');
                    });
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })->get();

            $demoStoreIds = SpvDemo::whereHas('store.district.area', function ($query) use ($areaIds){
                                return $query->whereIn('id', $areaIds);
                           })->pluck('user_id');
            $spvdemo = User::with('spvDemos.store.district.area.region')->whereIn('id', $demoStoreIds)->get();

//            return response()->json($spvdemo);

            return response()->json($this->getSupervisorCollection($supervisor, $spvdemo));

        }

    }

    public function getSupervisorCollection($supervisor, $spvdemo = null){

        $result = new Collection();
        $resultDemo = new Collection();

        foreach ($supervisor as $data) {

            $arr_area = [];
            $arr_region = [];
            $collection = new Collection();

            foreach ($data->stores as $detail) {
                if (!in_array($detail->district->area->name, $arr_area)) {
                    array_push($arr_area, $detail->district->area->name);
                }

                if (!in_array($detail->district->area->region->name, $arr_region)) {
                    array_push($arr_region, $detail->district->area->region->name);
                }
            }

            if(count($data->stores) == 0){
                $spvDemoIds = SpvDemo::where('user_id', $data->id)->pluck('store_id');

                if(count($spvDemoIds) > 0){
                    $stores = Store::whereIn('id', $spvDemoIds)->get();

                    foreach ($stores as $detail){
                        if (!in_array($detail->district->area->name, $arr_area)) {
                            array_push($arr_area, $detail->district->area->name);
                        }

                        if (!in_array($detail->district->area->region->name, $arr_region)) {
                            array_push($arr_region, $detail->district->area->region->name);
                        }
                    }
                }
            }

            for ($i = 0; $i < count($arr_area); $i++) {
                $data['area'] .= $arr_area[$i];

                if ($i != count($arr_area) - 1) {
                    $data['area'] .= ', ';
                }
            }

            for ($i = 0; $i < count($arr_region); $i++) {
                $data['region'] .= $arr_region[$i];

                if ($i != count($arr_region) - 1) {
                    $data['region'] .= ', ';
                }
            }

            $collection['id'] = $data['id'];
            $collection['nik'] = $data['nik'];
            $collection['name'] = $data['name'];
            $collection['area'] = $data['area'];
            $collection['region'] = $data['region'];

            $result->push($collection);

        }

        if($spvdemo != null) {

            foreach ($spvdemo as $data) {

                $arr_area = [];
                $arr_region = [];
                $collection = new Collection();

                foreach ($data->spvDemos as $detail) {
                    if (!in_array($detail->store->district->area->name, $arr_area)) {
                        array_push($arr_area, $detail->store->district->area->name);
                    }

                    if (!in_array($detail->store->district->area->region->name, $arr_region)) {
                        array_push($arr_region, $detail->store->district->area->region->name);
                    }
                }

                for ($i = 0; $i < count($arr_area); $i++) {
                    $data['area'] .= $arr_area[$i];

                    if ($i != count($arr_area) - 1) {
                        $data['area'] .= ', ';
                    }
                }

                for ($i = 0; $i < count($arr_region); $i++) {
                    $data['region'] .= $arr_region[$i];

                    if ($i != count($arr_region) - 1) {
                        $data['region'] .= ', ';
                    }
                }

                $collection['id'] = $data['id'];
                $collection['nik'] = $data['nik'];
                $collection['name'] = $data['name'];
                $collection['area'] = $data['area'];
                $collection['region'] = $data['region'];

                $resultDemo->push($collection);

            }

            $result = $result->merge($resultDemo);

        }

        return $result;
    }

    public function getPromoterPartner(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $userIds = EmployeeStore::where('store_id', $request->store_id)->pluck('user_id');

        $promoterGroup = ['Promoter', 'Promoter Additional', 'Promoter Event', 'ACT', 'PPE', 'BDT', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        $partner = User::whereIn('users.id', $userIds)
//                    ->whereHas('role', function($query) use ($promoterGroup){
//                        return $query->whereIn('role_group', $promoterGroup);
//                    })
                    ->join('roles','roles.id','users.role_id')
                    ->where('roles.role_group', $promoterGroup)
                    ->where('users.id', '<>', $user->id)
                    ->select('users.id', 'users.nik', 'users.name', 'roles.role')
                    ->get();

        return response()->json($partner);

    }
}
