<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class UpdateBM extends Command
{
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
    public function htngJrk(){
        
    }
    public function handle()
    {   
        $rand = substr(str_shuffle(MD5(microtime())), 0, 10);
        $koord = DB::table('koord_point')->get();
        $bm = DB::table('benchmark')->get();
        $x = 0;
        $y = 0;
        foreach($bm as $key=>$value){
            $y += $value->latitude;
            $x += $value->longitude;
        }
        $cent = [$x/count($bm),$y/count($bm)];
        $clust = [];
        foreach($koord as $key=>$value){
            $temp =[];
            foreach($koord as $j=>$val){

            }

        }
        \Log::info($cent);
        
        // DB::table('users')->insert(['name'=>'Test','email'=>$rand.'@asd.com','password'=>'test']);

        return Command::SUCCESS;
    }
}
