<?php

namespace App\Console\Commands;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PesananExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $data_booking_list = Pesanan::where('status', 'baru');
            foreach ($data_booking_list->get() as $key => $value) {
                $tanggal = Carbon::parse($value->tanggal)->addHours(1); 
                if ($tanggal < now()) {
                    $value->status = 'batal';
                    $value->save();
                }
            }
            $this->info('Set booking to expired done!');
        } catch (\Throwable $th) {
            $this->info('On Error');
        }
    }
}
