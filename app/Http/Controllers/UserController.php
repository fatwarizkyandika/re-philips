<?php

namespace App\Http\Controllers;

use App\Store;
use App\TrainerArea;
use App\User;
use App\RsmRegion;
use App\DmArea;
use App\EmployeeStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use App\Traits\AttendanceTrait;
use Illuminate\Support\Collection;
use Auth;
use App\Filters\UserFilters;
use File;
use App\NewsRead;
use App\ProductKnowledgeRead;
use App\Reports\HistoryEmployeeStore;
use Carbon\Carbon;
use DB;


class UserController extends Controller
{
    use UploadTrait;
    use StringTrait;
    use AttendanceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.user');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(Request $request){
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::
            where('id', '<>', Auth::user()->id)
            ->whereNotIn('role',$roles);
//        $data = User::all();

        $filter = $data;

        /* If filter */
            if($request['byName']){
                $filter = $data->where('id', $request['byName']);
            }

            if($request['byNik']){
                $filter = $data->where('id', $request['byNik']);
            }

            if($request['byRole']){
                $filter = $data->where('role', $request['byRole']);
            }

    //     return $this->makeTable($filter);
    // }

    
    

    // // Datatable template
    // public function makeTable($data){


    //     // Datatables::of($filter->all())
    //     //     ->make(true);

        return Datatables::of($filter->get())
                ->editColumn('role',function ($item) {
                    $dedicate = '';
                    $dmarea = DmArea::where('user_id', $item->id)->get();
                    foreach ($dmarea as $key => $value) {
                        $dedicate = $value->dedicate;
                    }
                    if ($item->role == 'DM') {
                        return $item->role.' - '.$dedicate;
                    }

                    return $item->role;                    
                    
                })
                ->addColumn('action', function ($item) {

                    return 
                    "<a href='".url('usernon/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
                    
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(UserFilters $filters){ 

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;       

        $data = User::filter($filters)->get();

        if ($userRole == 'RSM') {
            $region = RsmRegion::where('rsm_regions.user_id', $userId)
                        ->join('regions', 'rsm_regions.region_id', '=', 'regions.id')
                        ->join('areas', 'regions.id', '=', 'areas.region_id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $region);
        }

        if ($userRole == 'DM') {
            $area = DmArea::where('dm_areas.user_id', $userId)
                        ->join('areas', 'dm_areas.area_id', '=', 'areas.id')
                        ->join('districts', 'areas.id', '=', 'districts.area_id')
                        ->join('stores', 'districts.id', '=', 'stores.district_id')
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $area);
        }
            
        if (($userRole == 'Supervisor') or ($userRole == 'Supervisor Hybrid')) {
            $store = Store::where('stores.user_id', $userId)
                        ->join('employee_stores', 'stores.id', '=', 'employee_stores.store_id')
                        ->join('users', 'employee_stores.user_id', '=', 'users.id')
                        ->pluck('users.id');
            $data = $data->whereIn('id', $store);
        }

        return $data;
    }
    public function getDataPromoterWithFilters(UserFilters $filters){ 
        $data = User::filter($filters)->where('role','=','Promoter')->get();

        return $data;
    }
    public function getDataNonPromoterWithFilters(UserFilters $filters){ 
        $roles = ['Promoter','Promoter Additional','Promoter Event','Demonstrator MCC','Demonstrator DA','ACT','PPE','BDT','Salesman Explorer','SMD','SMD Coordinator','HIC','HIE','SMD Additional','ASC'];
        $data = User::filter($filters)->whereNotIn('role',$roles)->get();

        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.user-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'role' => 'required|string',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);



        $request['password'] = bcrypt($request['password']);

        // dd(public_path());        

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        $user = User::create($request->all());

        /* Insert user relation */
        if ($request['role'] == 'Supervisor' || $request['role'] == 'Supervisor Hybrid') {

            /* SPV Multiple Store */
            if($request['store_ids']){
                // return response()->json($request['store_ids']);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId); // id,store_id
                    // return response()->json($stores[1]);
                    $store = Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])->get();
                    $status = false;
                    $store_id   = '';
                    $store_name_1   = '';
                    $store_name_2   = '';
                    $latitude   = '';
                    $longitude  = '';
                    $address    = '';
                    $classification     = '';
                    $subchannel_id  = '';
                    $district_id    = '';
                    // return response()->json($store);
                    foreach ($store as $key => $value) {
                        /* ini masih foreach, harusnya cuma 1 kali aja untuk setiap store*/
                        if ( ($stores[2] == 'null' || $stores[2] == $request['dedicate']) && $status == false)
                        {
                            Store::where('id',$stores[0])
                            ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                            $status = true;
                        }
                        if ( ($stores[2] == 'DA' || $stores[2] == 'PC' || $stores[2] == 'HYBRID') && $status == false)
                        {
                            if ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC' || $request['dedicate'] == 'HYBRID')
                            {
                                Store::where('id',$stores[0])
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }
                        }

                        $store_id = $value->store_id;
                        $store_name_1 = $value->store_name_1;
                        $store_name_2 = $value->store_name_2;
                        $latitude = $value->latitude;
                        $longitude = $value->longitude;
                        $address = $value->address;
                        $classification = $value->classification;
                        $subchannel_id = $value->subchannel_id;
                        $district_id = $value->district_id;
                    }

                    if ($status == false) {
                        Store::create([
                            'store_id' => $store_id,
                            'store_name_1' => $store_name_1,
                            'store_name_2' => $store_name_2,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'address' => $address,
                            'classification' => $classification,
                            'subchannel_id' => $subchannel_id,
                            'district_id' => $district_id,
                            'user_id' => $user->id,
                            'dedicate' => $request['dedicate'],
                        ]);
                        $status = true;
                    }
                            
                            // $store = Store::where('id',$storeId)
                            // ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                }
            }
        }

        // If DM or Trainer
        if(isset($request->area)){
            if($request['role'] == 'DM') {
                $dmArea = DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 'dedicate' => $request->dedicate]);
            }elseif($request['role'] == 'Trainer') {
                $trainerArea = TrainerArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
            }
        }
        // If RSM
        if(isset($request->region)){
            $rsmRegion = RsmRegion::create(['user_id' => $user->id, 'region_id' => $request->region]);
        }

        if($request->photo_file != null){

            /* Upload updated image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $imageFolder = "user/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo_file, $imageFolder, $imageName);

        }


        /*
         * Generate attendance from day promoter works till end of month
         * (Just work for promoter group)
         */

        $promoterArray = ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'];

        if(in_array($user->role, $promoterArray)){
            $this->generateAttendace($user->id);
        }
        
        $userId = User::where('email', $request->email)->first();
        // echo response()->json($userId);

        return response()->json(['url' => url('usernon')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $data = User::where('id', $id)->first();
        $data = User::
            where('users.id', $id)
            // ->join('employee_stores','users.id','employee_stores.user_id')
            // ->join('stores','employee_stores.store_id','stores.id')
            ->select('users.*')//, 'stores.dedicate as dedicate')
            ->first();
            // return response()->json($data);

        return view('master.form.user-form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users'. ($id ? ",id,$id" : ''),
            'role' => 'required|string',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $user = User::find($id);
        $oldPhoto = "";
        



        if($user->photo != null && $request->photo_file != null) {
            /* Save old photo path */
            $oldPhoto = $user->photo;
        }

        /* Delete if any relation exist in employee store */
        // $empStore = EmployeeStore::where('user_id', $user->id);
        // if($empStore->count() > 0){
        //     $empStore->delete();
        // }

        if ($request['role'] == 'Supervisor' || $request['role'] == 'Supervisor Hybrid') {
            /* SPV Multiple Store */
            if($request['store_ids']){
                foreach ($request['store_ids'] as $storeId) {
                    $store = Store::find($storeId)->update(['user_id'=>null]);
                }
            }
        }else{
            $empStore = Store::where('user_id', $user->id);
            if($empStore->count() > 0){
                $empStore->update(['user_id'=>null]);
            }
        }
        

        // DM AREA 
        $dmArea = DmArea::where('user_id', $user->id);
        if($dmArea->count() > 0){
            $dmArea->delete();
        }

        // TRAINER AREA
        $trainerArea = TrainerArea::where('user_id', $user->id);
        if($trainerArea->count() > 0){
            $trainerArea->delete();
        }

        // RSM REGION 
        $rsmRegion = RsmRegion::where('user_id', $user->id);
        if($rsmRegion->count() > 0){
            $rsmRegion->delete();
        }
        /* ================================================= */

        // Upload file process
        ($request->photo_file != null) ? 
            $photo_url = $this->getUploadPathName($request->photo_file, "user/".$this->getRandomPath(), 'USER') : $photo_url = "";
        
        if($request->photo_file != null) $request['photo'] = $photo_url;

        // Create new request
        $requestNew = new Request;

        // Check if password empty
        if($request['password']){

            $requestNew['password'] = bcrypt($request['password']);

        }

        if($request->photo_file != null){

            $requestNew['photo'] = $request['photo'];

        }

        $requestNew['name'] = $request['name'];
        $requestNew['email'] = $request['email'];
        $requestNew['role'] = $request['role'];

        $requestNew['status'] = null;
        $requestNew['nik'] = null;

        if($request['status']){
            $requestNew['status'] = $request['status'];
        }

        if($request['nik']){
            $requestNew['nik'] = $request['nik'];
        }

        $user->update($requestNew->all()); 

        /* Insert user relation */
        if ($request['role'] == 'Supervisor' || $request['role'] == 'Supervisor Hybrid') {
            /* SPV Multiple Store */
            if($request['store_ids'])
            {
                Store::where('user_id',$user->id)
                            ->update(['user_id'=>null]);
                foreach ($request['store_ids'] as $storeId) {
                    /*
                    1. select all store with STORE ID selected
                    */
                    $stores = explode(',', $storeId); // id,store_id
                    $store = Store::where('deleted_at',null)
                                ->where('store_id',$stores[1])->get();
                    $status = false;
                    $store_id   = '';
                    $store_name_1   = '';
                    $store_name_2   = '';
                    $latitude   = '';
                    $longitude  = '';
                    $address    = '';
                    $classification     = '';
                    $subchannel_id  = '';
                    $district_id    = '';
                    foreach ($store as $key => $value) {
                        /* ini masih perlu di cek */
                        if ( ($stores[2] == 'null' || $stores[2] == $request['dedicate']) && $status == false)
                        {
                            Store::where('id',$value->id)
                            ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                            
                            $status = true;
                        }
                        
                        if ( ($stores[2] == 'DA' || $stores[2] == 'PC' || $stores[2] == 'HYBRID') && $status == false)
                        {
                            if ($request['dedicate'] == 'DA' || $request['dedicate'] == 'PC' || $request['dedicate'] == 'HYBRID')
                            {
                                Store::where('id',$value->id)
                                ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                                $status = true;
                            }
                        }

                        $store_id = $value->store_id;
                        $store_name_1 = $value->store_name_1;
                        $store_name_2 = $value->store_name_2;
                        $latitude = $value->latitude;
                        $longitude = $value->longitude;
                        $address = $value->address;
                        $classification = $value->classification;
                        $subchannel_id = $value->subchannel_id;
                        $district_id = $value->district_id;
                    }

                    if ($status == false) {
                        Store::create([
                            'store_id' => $store_id,
                            'store_name_1' => $store_name_1,
                            'store_name_2' => $store_name_2,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'address' => $address,
                            'classification' => $classification,
                            'subchannel_id' => $subchannel_id,
                            'district_id' => $district_id,
                            'user_id' => $user->id,
                            'dedicate' => $request['dedicate'],
                        ]);
                        $status = true;
                    }
                            
                            // $store = Store::where('id',$storeId)
                            // ->update(['user_id'=>$user->id,'dedicate'=>$request['dedicate']]);
                }
            }
        }

        // If DM
        if($request->area){
            if($request['role'] == 'DM') {
                $dmArea = DmArea::where('user_id', $user->id);
                if($dmArea->count() > 0){
                    $dmArea->first()->update(['area_id' => $request->area]);
                    $dmArea->first()->update(['dedicate' => $request->dedicate]);
                }else{
                    DmArea::create(['user_id' => $user->id, 'area_id' => $request->area, 'dedicate' => $request->dedicate]);
                }
            }elseif($request['role'] == 'Trainer') {
                $trainerArea = TrainerArea::where('user_id', $user->id);
                if($trainerArea->count() > 0){
                    $trainerArea->first()->update(['area_id' => $request->area]);
                }else{
                    TrainerArea::create(['user_id' => $user->id, 'area_id' => $request->area]);
                }
            }

            
        }
        // If RSM
        if($request->region){
            $rsmRegion = RsmRegion::where('user_id', $user->id);
        
            if($rsmRegion->count() > 0){
                $rsmRegion->first()->update(['region_id' => $request->region]);
            }else{
                RsmRegion::create(['user_id' => $user->id, 'region_id' => $request->region]);
            }
        }

        if($user->photo != null && $request->photo_file != null && $oldPhoto != "") {
            /* Delete Image */
            $imagePath = explode('/', $oldPhoto);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);

        }

        if($user->photo != null && $request->photo_file != null){
            /* Upload updated image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $imageFolder = "user/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->upload($request->photo_file, $imageFolder, $imageName);
        }

        

        return response()->json(
            [
                'url' => url('usernon'),
                'method' => $request->_method
            ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* Deleting related to user */

        /* Delete if any relation exist in employee store */
        $empStore = EmployeeStore::where('user_id', $id);
        if($empStore->count() > 0){
            $empStore->delete();
        }

        // DM AREA 
        $dmArea = DmArea::where('user_id', $id);
        if($dmArea->count() > 0){
            $dmArea->delete();
        }

        // TRAINER AREA
        $trainerArea = TrainerArea::where('user_id', $id);
        if($trainerArea->count() > 0){
            $trainerArea->delete();
        }

        // RSM REGION 
        $rsmRegion = RsmRegion::where('user_id', $id);
        if($rsmRegion->count() > 0){
            $rsmRegion->delete();
        }

        // News Reads
        $newsRead = NewsRead::where('user_id', $id);
        if($newsRead->count() > 0){
            $newsRead->delete();
        }

        // Product Knowledge Reads
        $productKnowledgeRead = ProductKnowledgeRead::where('user_id', $id);
        if($productKnowledgeRead->count() > 0){
            $productKnowledgeRead->delete();
        }

        $user = User::find($id);

        if($user->photo != "") {
            /* Delete Image */
            $imagePath = explode('/', $user->photo);
            $count = count($imagePath);
            $folderpath = $imagePath[$count - 2];
            File::deleteDirectory(public_path() . "/image/user/" . $folderpath);
        }

        $user->destroy($id);

        return response()->json($id);
    }
}
