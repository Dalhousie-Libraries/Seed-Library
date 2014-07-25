<?php

class PacketsTableSeeder extends Seeder {

    public function run() {

        $i = 0;
        while ($i < 100) {
            $packet = new Packet();
            $packet->accession_id = rand(1, 50);
            
            if($packet->accession_id != 15 && $packet->accession_id != 18)
                if ($packet->accession_id > 6 && $packet->accession_id < 21 )
                    continue;
            
            $packet->date_harvest = '2013-04-05';
            $packet->grow_location = 'Halifax';
            $packet->germination_ratio = rand(70, 100);
            $packet->physical_location  = 'New Desk';
            $packet->amount = rand(1, 500);            
            
            $packet->save();
            
            $i++; // increment iterator
        }
    }

}
