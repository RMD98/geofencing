<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class UpdateBM extends Command
{
    public $gkoord;
    public $bm;

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
        // foreach($clust as $key=>$value){
        //     if(count($value)<10){
        //         unset($clust[$key]);
        //     }
            // \Log::info($this->gkoord);
        // };

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
        $gkoord = json_decode($this->gkoord);
        foreach ($kord as $key => $value) {
            $adjDist =[];
            $id_koord = array_search($key,array_column($gkoord,'id'));
            $point = $gkoord[$id_koord];
            //mencari titik tetangga 
            foreach($this->bm as $keys =>$values){
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
        $gkoord = json_decode($this->gkoord);
        $bm = json_decode($this->bm);
        // \Log::info($comp);
        $cent = $this->centroid();
        foreach($comp as $key=>$value){
            $point = array_search($key,array_column($gkoord,'id'));
            //mengambil koordinat bm berdasarkan ID
            $dist = $this->hitungJrk($cent,[$gkoord[$point]->longitude,$gkoord[$point]->latitude]);
            $adjDist =[];
            // $dist = 0;
            foreach($value as $i=>$val){
                foreach ($val as $j => $id) {
                    # code...
                    $adjPoint = array_search($id['bm'],array_column($bm,'id'));
                    $temp = $this->hitungJrk($cent,[$bm[$adjPoint]->longitude,$bm[$adjPoint]->latitude]);
                    array_push($adjDist,$temp);
                }
            };
            if ($dist > max($adjDist)){
                // \Log::info($dist);
                $id = IdGenerator::generate(['table' => 'benchmark', 'field'=>'id','length' => 5, 'prefix' => 'BM']);
        
                $data = array(
                    'id' =>$id,
                    'koord_id' =>$key,
                    'latitude' =>$gkoord[$point]->latitude,
                    'longitude' =>$$gkoord[$point]->longitude,
                );
                DB::table('benchmark')->insert($data);
                // if()
            }else{
            }
            
        }
        // \Log::info($cent);
    }
    public function handle()
    {   
        $this->bm = DB::table('benchmark')->get();
        $this->gkoord =DB::table('koord_point')->get();
        // $valpoint = $this->clustering();
        // $neigh =$this->neighbour($valpoint);
        // $upbm = $this->updateBM($neigh);
        \Log::info($this->centroid());
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
