<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class Koordinat extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
       $data = DB::table('koord_point')->get();
       return response()->json($data);
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
        $kid = IdGenerator::generate(['table' => 'koord_point', 'field'=>'id','length' => 8, 'prefix' => 'ITN']);

        $last = DB::table('koord_point')->latest()->first();
        $id = explode('-',$last->id); 
        // dd($id);
        $now = new DateTime('now');
        // dd($now->format('Y-m-d H:i:s')); 
        // dd($now);
        $data = array(
            'id' => 'ITN-'.$id[1]+1,
            'user_id' => $request->id,
            'latitude'=> $request->lat,
            'longitude' => $request->long,
            'created_at' => $now->format('Y-m-d H:i:s')
        );
        DB::table('koord_point')->insert($data);
        return response()->json();
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
