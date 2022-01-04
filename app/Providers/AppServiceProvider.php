<?php

namespace App\Providers;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       app()->bind('expire',function(){
        try {
            $data_booking_list = Pesanan::where('status', 'baru');
            foreach ($data_booking_list->get() as $key => $value) {
                $tanggal = Carbon::parse($value->tanggal)->addDay(1); 
                if ($tanggal < now()) {
                    $data = Pesanan::find($value->id);
                    $data->status = 'batal';
                    $data->save();
                }
            }
            //$this->info('Set booking to expired done!');
        } catch (\Throwable $th) {
            //$this->info('On Error');
        }   
        
        

       });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
