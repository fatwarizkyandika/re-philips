<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Filters\PriceFilters;
use App\Traits\StringTrait;
use DB;
use App\Price;

class PriceController extends Controller
{
    use StringTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.price');
    }

    /**
     * Data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $data = Price::where('prices.deleted_at', null)
        			->join('products', 'prices.product_id', '=', 'products.id')
                    ->join('global_channels', 'prices.globalchannel_id', '=', 'global_channels.id')
                    ->select('prices.*', 'products.name as product_name', 'global_channels.name as globalchannel_name')->get();

        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(PriceFilters $filters){
        $data = Price::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

           return Datatables::of($data)
           		->addColumn('action', function ($item) {

                   return
                    "<a href='#price' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-price'><i class='fa fa-pencil'></i></a>
                    <button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'product_id' => 'required',
            'globalchannel_id' => 'required',
            'price' => 'required|numeric'
            ]);

        $price = Price::where('product_id', $request['product_id'])
                    ->where('globalchannel_id', $request['globalchannel_id']);

        if($price->count() > 0){
            $price->update(['price'=>$request->price]);
        }else{
            Price::create($request->all());
        }

        return response()->json(['url' => url('/price')]);
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
        $data = Price::with('product', 'globalChannel')->where('id', $id)->first();

        return response()->json($data);
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
            'product_id' => 'required',
            'globalchannel_id' => 'required',
            'price' => 'required|numeric'
            ]);

        $price = Price::find($id)->update($request->all());

        return response()->json(
            ['url' => url('/price'), 'method' => $request->_method]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price = Price::destroy($id);

        return response()->json($id);
    }
}