<?php

class ItemsTableSeeder extends Seeder {

    /**
     * Run the items's table seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the array that will hold data
        $data = array();
        
        // Open the file
        $file = fopen('app/database/seeds/items.txt', 'r');
        
        // Read the file and store its data in an array
        if ($file)
        {
            $i = 0;
            while (($line = fgets($file)) !== false)
            {
                $data[$i] = explode(',', $line); // splits the line by its delimiter
                $i++; // increment iterator
            }
            
            // Remove quotes from each field
            $i = 0;
            foreach ($data as $k => $v)
            {
                $data[$k]['id'] = $i + 1;
                $data[$k]['category'] = $data[$k][1];
                $data[$k]['family'] = $data[$k][2];
                $data[$k]['species'] = $data[$k][3];
                $data[$k]['variety'] = $data[$k][4];
                $data[$k]['description'] = $data[$k][5];
                $data[$k]['initial_inventory'] = $data[$k][7];
                $data[$k]['seed_sav_level'] = $data[$k][8];
                $data[$k]['harvest_date'] = $data[$k][9];
                unset($data[$k][0]); unset($data[$k][1]);
                unset($data[$k][2]); unset($data[$k][3]);
                unset($data[$k][4]); unset($data[$k][5]);
                unset($data[$k][6]); unset($data[$k][7]);
                unset($data[$k][8]); unset($data[$k][9]);
                $i++;
            }
            
            // Insert data
            DB::table('items')->insert($data);
        }
        
        // Close the file
        fclose($file);
    }

}
