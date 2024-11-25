<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use DateTime;

class UpdateBM extends Command
{
    public $gkoord;
    public $bm;
    public $cent;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upbm:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function clustering(){
        //validasi koordinat dengan melihat klustering
        $data = $this->gkoord;
        $clust = [];
        foreach($data as $key=>$value){
            // $temp = [];
            foreach($data as $keys=>$values){
                $dist = $this->hitungJrk([$value->longitude,$value->latitude],[$values->longitude,$values->latitude]);
                if($dist == 0){
                    continue;
                }elseif($dist <= 10 ){
                    if(!array_key_exists($value->id,$clust)){
                        $clust[$value->id]= [];
                    }
                    array_push($clust[$value->id],$values->id);
                }
            }
        };
        foreach($clust as $key=>$value){
            if(count($value)<5){
                unset($clust[$key]);
            }
            // \Log::info($this->gkoord);
        };

        return $clust;
    }
    public function centroid(){
        $bm = json_decode($this->bm);
        $y =0;
        $x = 0;
        //calculate centroid
        foreach($bm as $key=>$value){
            $y += $value->latitude;
            $x += $value->longitude;
        }
        $cent = [$x/count($bm),$y/count($bm)];
        return $cent;
        
    }
    public function hitungJrk($koord1,$koord2){
        $longA = ($koord1[0]*3.14)/180;
        $longB = ($koord2[0]*3.14)/180;
        $latA = ($koord1[1]*3.14)/180;
        $latB = ($koord2[1]*3.14)/180;

        $deltaLat = sin(($latA-$latB)/2);
        $deltaLong = sin(($longA-$longB)/2);
        //hitung jarak dengan formula haversine
        $jarak = 6371 * (2 * sin( sqrt( pow( $deltaLat,2 ) + ( cos( $latA ) * cos( $latB ) * pow( $deltaLong,2 ) ) ) ) );

        return $jarak * 1000;
    }
    public function neighbour($kord){
        // foreach($this->gkoord as $key=>$value){
            // }
        $adj=[];
        $bm = DB::table('benchmark')->where('id','!=','Centroid')->get();
        $gkoord = $this->gkoord;
        foreach ($kord as $key => $value) {
            $adjDist =[];
            $id_koord = array_search($key,array_column($gkoord,'id'));
            $point = $gkoord[$id_koord];
            //mencari titik tetangga 
            foreach($bm as $keys =>$values){
                $dist = $this->hitungJrk([$point->longitude,$point->latitude],[$values->longitude,$values->latitude]);
                array_push($adjDist,['bm'=>$values->id,'dist'=>$dist]);
            }
            if(!array_key_exists($key,$adj)){
                $adj[$key]= [];
            }
            sort($adjDist);
            $temp = array_slice($adjDist,0,2);
            //menyimpan id bm tetangga
            array_push($adj[$key],$temp);
        }
        
        return $adj;
    }
    public function updateBM($comp){
        $gkoord = $this->gkoord;
        // \Log::info($comp);
        $cent = [$this->cent->longitude,$this->cent->latitude];
        foreach($comp as $key=>$value){
                $bm = json_decode(DB::table('benchmark')->where('id','!=','Centroid')->get());
                $point = array_search($key,array_column($gkoord,'id'));
                $now = new DateTime('now');
                if(count($bm)<10){
                    if(DB::table('benchmark')->where('id','!=','Centroid')->count() == 0){
                        $newid = 0;
                    }else{
                        $last = DB::table('benchmark')->where('id','!=','Centroid')->orderBy('id','desc')->first();
                        $newid = explode('-',$last->id)[1] + 1; 
                        // \Log::info(explode('-',$last->id)[1]);
                    }
                    $data = array(
                        'id' => 'BM-'.$newid,
                        'koord_id' =>$key,
                        'latitude' =>$gkoord[$point]->latitude,
                        'longitude' =>$gkoord[$point]->longitude,
                        'created_at' => $now->format('Y-m-d H:i:s')
        
                    );
                    DB::table('benchmark')->insert($data);
                    // if()
                    $log = array(
                        'new BM' => $data,
                        'time-stamp' => $now->format('Y-m-d H:i:s')
                    );
                    \Log::info($log);
                } else {
                    $adj = $this->neighbour($comp);
                    //mengambil koordinat bm berdasarkan ID
                    $dist = $this->hitungJrk($cent,[$gkoord[$point]->longitude,$gkoord[$point]->latitude]);
                    $adjDist =[];
                    // $dist = 0;
                    $adjBm = [];
                    foreach($adj as $i=>$val){
                        foreach ($val[0] as $j => $id) {
                            # code...
                            $adjPoint = array_search($id['bm'],array_column($bm,'id'));
                            $temp = $this->hitungJrk($cent,[$bm[$adjPoint]->longitude,$bm[$adjPoint]->latitude]);
                            array_push($adjDist,$temp);
                            array_push($adjBm,$id['bm']);
                        }
                    };
                    // \Log::info($adj);
                    if ($dist > max($adjDist)){
                        // \Log::info($dist);
                        // $id = IdGenerator::generate(['table' => 'benchmark', 'field'=>'id','length' => 5, 'prefix' => 'BM']);
                
                    
            
                        // $last = DB::table('benchmark')->where('id','!=','Centroid')->orderBy('id','desc')->first();
                        $last = DB::table('benchmark')->where('id','!=','Centroid')->latest()->first();
                        $newid = explode('-',$last->id)[1] + 1; 
                        $data = array(
                            'id' => 'BM-'.$newid,
                            'koord_id' =>$key,
                            'latitude' =>$gkoord[$point]->latitude,
                            'longitude' =>$gkoord[$point]->longitude,
                            'created_at' => $now->format('Y-m-d H:i:s')

                        );
                        DB::table('benchmark')->insert($data);
                        // if()
                        foreach ($adjBm as $key => $id) {
                            DB::table('benchmark')->where('id',$id)->delete();
                        }
                        $log = array(
                            'new BM' => $data,
                            'removed BM' => $adjBm,
                            'time-stamp' => $now->format('Y-m-d H:i:s')
                        );
                        \Log::info($log);
                        
                }else{
                }
            }
            foreach ($this->gkoord as $key => $value) {
                // \Log::info($value->id);
                DB::table('koord_point')->where('id',$value->id)->update(['status'=>'CHECKED']);
                # code...
            }
        }
        // \Log::info($cent);
    }
    public function handle()
    {   
        $this->bm = json_decode(DB::table('benchmark')->where('id','!=','Centroid')->get());
        $this->gkoord =json_decode(DB::table('koord_point')->where('status','not checked')->skip(0)->take(50)->get());
        $this->cent = DB::table('benchmark')->where('id','Centroid')->first();
        \Log::info($this->cent->id);
        $valpoint = $this->clustering();
        if(count($this->bm)>0){
            $neigh =$this->neighbour($valpoint);
            $upbm = $this->updateBM($neigh);
        }else{
            $upbm = $this->updateBM($valpoint);
        }
        // \Log::info($this->cent->latitude);
        // $rand = substr(str_shuffle(MD5(microtime())), 0, 10);
        // $koord = DB::table('koord_point')->get();
     
        // $x = 0;
        // $y = 0;
        
        // $clust = [];
        // foreach($koord as $key=>$value){
        //     $temp =[];
        //     foreach($koord as $j=>$val){

        //     }

        // }
        // \Log::info($cent);
        
        // DB::table('users')->insert(['name'=>'Test','email'=>$rand.'@asd.com','password'=>'test']);

        return Command::SUCCESS;
    }
}
