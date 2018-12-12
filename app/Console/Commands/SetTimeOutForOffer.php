<?php

namespace App\Console\Commands;

use App\Enums\OfferStatus;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetTimeOutForOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:set_timeout_for_offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'When order offer timeout';

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
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now()->second(0);
        $offers = Offer::where('status', OfferStatus::ACTIVE);

        foreach ($offers->cursor() as $offer) {
            $startHourFrom = (int) Carbon::parse($offer->start_time_from)->format('H');
            $startHourTo = (int) Carbon::parse($offer->start_time_to)->format('H');

            if ($startHourTo < $startHourFrom) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . '00:00:00')->addDay();
                if ($now->gte($date)) {
                    $offer->status = OfferStatus::TIMEOUT;
                }
            } else {
                $startTimeTo = Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . $offer->start_time_to)->subMinutes(30);
                if ($now->gte($startTimeTo)) {
                    $offer->status = OfferStatus::TIMEOUT;
                }
            }
            
            $offer->save();
        }
    }
}
