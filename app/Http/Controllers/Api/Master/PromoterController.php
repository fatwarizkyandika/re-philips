<?php

namespace App\Http\Controllers\Api\Master;

use App\Attendance;
use App\AttendanceDetail;
use App\DmArea;
use App\EmployeeStore;
use App\RsmRegion;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class PromoterController extends Controller
{
    public function getAttendanceForSupervisor(Request $request){

        $user = JWTAuth::parseToken()->authenticate();

        $storeIds = Store::where('user_id', $user->id)->pluck('id');
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');

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
        $promoterIds = EmployeeStore::whereIn('store_id', $storeIds)->pluck('user_id');

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
        $message = "";

        if($param == 1){

            if($attendances->status != 'Pending Sakit'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval sakit'], 500);
            }

            $attendances->update(['status' => 'Sakit']);
            $message = "Sakit";

        }else{

            if($attendances->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Promoter tidak membutuhkan approval izin'], 500);
            }

            $attendances->update(['status' => 'Izin']);
            $message = "Izin";
        }

        return response()->json(['status' => true, 'id_attendance' => $attendances->id, 'message' => 'Approval '.$message.' berhasil']);

    }

    public function checkAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $details = AttendanceDetail::where('attendances.status', 'Masuk')->where('attendances.user_id', $user->id)
                    ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                    ->join('stores', 'attendance_details.store_id', '=', 'stores.id')
                    ->whereMonth('attendances.date', '=', Carbon::now()->format('m'))
                    ->select('attendances.date as date', 'attendance_details.check_in as check_in', 'attendance_details.check_out as check_out',
                        'stores.store_name_1 as store_name')
                    ->get();

        return response()->json($details);

    }

    public function checkNotAttendance(){

        $user = JWTAuth::parseToken()->authenticate();

        $details = Attendance::where('attendances.user_id', $user->id)
                    ->where(function ($query) {
                        return $query->where('attendances.status', 'Pending Sakit')->orWhere('attendances.status', 'Pending Izin')
                                     ->orWhere('attendances.status', 'Sakit')->orWhere('attendances.status', 'Izin')
                                     ->orWhere('attendances.status', 'Alpha');
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
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
            })->with('stores.district.area.region')->get();

            return response()->json($this->getSupervisorCollection($supervisor));

        }else if($param == 2) { // BY REGION

            $regionIds = RsmRegion::where('user_id', $user->id)->pluck('region_id');

            $supervisor = User::where(function ($query) {
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area.region', function ($query) use ($regionIds){
                        return $query->whereIn('id', $regionIds);
                    })->get();

            return response()->json($this->getSupervisorCollection($supervisor));

        }else if($param == 3) { // BY AREA

            $areaIds = DmArea::where('user_id', $user->id)->pluck('area_id');

            $supervisor = User::where(function ($query) {
                return $query->where('role', 'Supervisor')->orWhere('role', 'Supervisor Hybrid');
                })->with('stores.district.area.region')
                    ->whereHas('stores.district.area', function ($query) use ($areaIds){
                        return $query->whereIn('id', $areaIds);
                    })->get();

            return response()->json($this->getSupervisorCollection($supervisor));

        }

    }

    public function getSupervisorCollection($supervisor){

        $result = new Collection();

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

        return $result;
    }
}
