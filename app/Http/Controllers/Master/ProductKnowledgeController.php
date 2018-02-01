<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\StringTrait;
use App\Traits\UploadTrait;
use App\ProductKnowledge;
use App\Filters\ProductKnowledgeFilters;
use Auth;
use Carbon\Carbon;
use App\District;
use App\Store;
use App\User;
use App\ProductKnowledgeRead;
use File;

class ProductKnowledgeController extends Controller
{
    use StringTrait;
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.product-knowledge');
    }

     /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = ProductKnowledge::where('product_knowledges.deleted_at', null)
        			->join('users', 'product_knowledges.user_id', '=', 'users.id')
                    ->select('product_knowledges.*', 'users.name as user_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(ProductKnowledgeFilters $filters){
        $data = ProductKnowledge::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->editColumn('target_detail', function ($item) {

                    $result = "";
                    if($item->target_type == 'Area'){

                        $data = explode(',' , $item->target_detail);
                        foreach ($data as $dataSplit) {

                            $area = District::find(trim($dataSplit));
                            $result .= $area->name;
                            if($dataSplit != end($data)){
                                $result .= ", ";
                            }

                        }

                    }else if($item->target_type == 'Store'){

                        $data = explode(',' , $item->target_detail);
                        // foreach ($data as $dataSplit) { 

                            $store = Store::where('stores.deleted_at', null)
                                        ->whereIn('stores.id', $data)
                                        ->groupBy('store_id')
                                        ->select('stores.*')->get();
                            $idx = 0;
                            foreach ($store as $Store => $valueStore) {
                                if ($idx > 0) {
                                    $result .= ', '."(" . $valueStore->store_id . ") " . $valueStore->store_name_1;;
                                }else{
                                    $result = "(" . $valueStore->store_id . ") " . $valueStore->store_name_1;
                                }
                                $idx ++;
                            }

                    }else if($item->target_type == 'Promoter'){

                        $data = explode(',' , $item->target_detail);
                        foreach ($data as $dataSplit) {

                            $user = User::find(trim($dataSplit));
                            $result .= $user->name;
                            if($dataSplit != end($data)){
                                $result .= ", ";
                            }

                        }

                    }

                    return $result;

                })
                ->editColumn('file', function ($item) {
                    if($item->file != "") {
                        return "<a target='_blank' href='" . $item->file . "' class='btn btn-sm btn-danger'><i class='fa fa-file-pdf-o'></i> &nbsp; Download PDF</a>";
                    }else{
                        return "<label class='btn btn-sm btn-primary'>No File Uploaded</label>";
                    }

                })
                ->editColumn('total_read', function ($item) {
                    return
                    "<a class='open-read-who-modal' data-target='#read-who-modal' data-toggle='modal' data-total-read='".$item->total_read."' data-url='util/productread' data-title='Who`s read this Product Knowledge' data-id='".$item->id."'> ".$item->total_read." </a>";
                })
                ->addColumn('action', function ($item) {

                    return
                    "<a href='".url('product-knowledge/edit/'.$item->id)."' class='btn btn-sm btn-warning'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['file', 'total_read', 'action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.form.product-knowledge-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $this->validate($request, [
            'type' => 'required',
            'from' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
            ]);

        // Upload file process
        ($request->upload_file != null) ?
            $file_url = $this->getUploadPathNameFile($request->upload_file, "productknowledge/".$this->getRandomPath(), $request->filename) : $file_url = "";

        if($request->upload_file != null) $request['file'] = $file_url;

        // Admin
        $request['user_id'] = Auth::user()->id;

        // Date
        $request['date'] = Carbon::now();

        // Total Read
        $request['total_read'] = 0;

         /* Area Targets */
        if($request['target_type'] == 'Area'){
            $target = null;
            $data = $request['area'];
            foreach ($data as $area) {
                $target .= $area;

                if($area != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Store Targets */
        if($request['target_type'] == 'Store'){
            $target = null;
            $x = 0;
            $data = $request['store'];
            foreach ($data as $store) {
                $storess = Store::where('stores.id', $store)
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->join('stores as storeId', 'storeses.id', '=', 'storeId.id')->get();
                    foreach ($storess as $key => $value) {
                        $result[$x] = $value->id;
                        $x ++;
                    }
            }
                $target .= implode(", ",$result);
        }

        /* Employee Targets */
        if($request['target_type'] == 'Promoter'){
            $target = null;
            $data = $request['employee'];
            foreach ($data as $employee) {
                $target .= $employee;

                if($employee != end($data)){
                    $target .= ", ";
                }
            }
        }

        if($request['target_type'] != 'All'){
            $request['target_detail'] = $target;
        }else{
            $request['target_detail'] = null;
        }

        // dd($request->all());
        $productKnowledge = ProductKnowledge::create($request->all());

        if($request->upload_file != null){

            /* Upload updated image */
            $imagePath = explode('/', $productKnowledge->file);
            $count = count($imagePath);
            $imageFolder = "productknowledge/" . $imagePath[$count - 2];
            $imageName = $imagePath[$count - 1];

            $this->uploadFile($request->upload_file, $imageFolder, $imageName);

        }

        return response()->json(['url' => url('/product-knowledge')]);
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
        $data = ProductKnowledge::where('id', $id)->first();

            if ($data->target_type == 'Store') {
                    $StoreIds = explode(",", $data->target_detail);
                
                    $data->target_detail = Store::where('stores.deleted_at', null)
                                ->whereIn('stores.id', $StoreIds)
                                ->groupBy('store_id')
                                ->pluck('stores.id')
                                ->toArray();
            }
                $data->target_detail = implode(", ",$data->target_detail);

        return view('master.form.product-knowledge-form', compact('data'));
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
            'type' => 'required',
            'from' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
            ]);

        // Admin
        $request['user_id'] = Auth::user()->id;

         /* Area Targets */
        if($request['target_type'] == 'Area'){
            $target = null;
            $data = $request['area'];
            foreach ($data as $area) {
                $target .= $area;

                if($area != end($data)){
                    $target .= ", ";
                }
            }
        }

        /* Store Targets */
        if($request['target_type'] == 'Store'){
            $target = null;
            $x = 0;
            $data = $request['store'];
            foreach ($data as $store) {
                $storess = Store::where('stores.id', $store)
                                ->join('stores as storeses', 'stores.store_id', '=', 'storeses.store_id')
                                ->join('stores as storeId', 'storeses.id', '=', 'storeId.id')->get();
                    foreach ($storess as $key => $value) {
                        $result[$x] = $value->id;
                        $x ++;
                    }
            }
                $target .= implode(", ",$result);
        }

        /* Employee Targets */
        if($request['target_type'] == 'Promoter'){
            $target = null;
            $data = $request['employee'];
            foreach ($data as $employee) {
                $target .= $employee;

                if($employee != end($data)){
                    $target .= ", ";
                }
            }
        }

        if($request['target_type'] != 'All'){
            $request['target_detail'] = $target;
        }else{
            $request['target_detail'] = null;
        }

        $productKnowledge = ProductKnowledge::find($id);
        $oldFile = "";

        if($productKnowledge->file != null && $request->upload_file != null) {
            /* Save old file path */
            $oldFile = $productKnowledge->file;
        }

        // Upload file process
        ($request->upload_file != null) ?
            $file_url = $this->getUploadPathNameFile($request->upload_file, "productknowledge/".$this->getRandomPath(), $request->filename) : $file_url = "";

        if($request->upload_file != null) $request['file'] = $file_url;

        // Update data
    	$productKnowledge->update($request->all());

    	if($productKnowledge->file != null && $request->upload_file != null && $oldFile != "") {

            /* Delete File PDF */
            $filePath = explode('/', $oldFile);
            $count = count($filePath);
            $folderpath = $filePath[$count - 2];
            File::deleteDirectory(public_path() . "/file/productknowledge/" . $folderpath);

            /* Upload updated file */
            $filePath = explode('/', $productKnowledge->file);
            $count = count($filePath);
            $fileFolder = "productknowledge/" . $filePath[$count - 2];
            $fileName = $filePath[$count - 1];

            $this->uploadFile($request->upload_file, $fileFolder, $fileName);

        }

        return response()->json(
            [
                'url' => url('/product-knowledge'),
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
        /* Deleting related to product knowledge */
        // Product Knowledge Reads
        $productKnowledgeRead = ProductKnowledgeRead::where('productknowledge_id', $id);
        if($productKnowledgeRead->count() > 0){
            $productKnowledgeRead->delete();
        }

        $productKnowledge = ProductKnowledge::find($id);

        if($productKnowledge->file != "") {
            /* Delete File */
            $filePath = explode('/', $productKnowledge->file);
            $count = count($filePath);
            $folderpath = $filePath[$count - 2];
            File::deleteDirectory(public_path() . "/file/productknowledge/" . $folderpath);
        }

        $productKnowledge->destroy($id);

        return response()->json($id);
    }
}
