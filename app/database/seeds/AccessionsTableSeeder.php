<?php

class AccessionsTableSeeder extends Seeder {
    
    public function run() {

        $i = 0;
        while ($i < 50) {
            $donation = new Donation();
            $donation->accession_number = $donation->getAcessionNumber();
            $donation->item_id          = rand(1, 87);
            $donation->user_id          = rand(1, 56);
            $donation->amount           = rand(1, 1000);
            $donation->checked_in_date  = Carbon::now();
            
            $donation->save();
            
            $i++; // increment iterator
        }
    }

}
