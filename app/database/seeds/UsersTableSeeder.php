<?php

class UsersTableSeeder extends Seeder {

    /**
     * Run the people's table seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the array that will hold data
        $data = array();
        
        // Open the file
        $file = fopen('app/database/seeds/patrons.txt', 'r');
        
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
                $data[$k]['name'] = $data[$k][2] . ' ' . $data[$k][1];
                $data[$k]['assumption_risk'] = $data[$k][3];
                $data[$k]['email'] = $data[$k][4];
                $data[$k]['address'] = $data[$k][5];
                $data[$k]['city'] = $data[$k][6];
                $data[$k]['province'] = $data[$k][7];
                $data[$k]['postal_code'] = $data[$k][8];
                $data[$k]['home_phone'] = $data[$k][9];
                $data[$k]['work_phone'] = $data[$k][10];
                $data[$k]['cell_phone'] = $data[$k][11];
                $data[$k]['mentor'] = $data[$k][12];
                $data[$k]['volunteer'] = $data[$k][13];
                $data[$k]['gardening_exp'] = $data[$k][14];
                $data[$k]['seedsaving_exp'] = $data[$k][15];
                $data[$k]['created_at'] = $data[$k][16];
                $data[$k]['updated_at'] = $data[$k][16];
                unset($data[$k][0]); unset($data[$k][1]);
                unset($data[$k][2]); unset($data[$k][3]);
                unset($data[$k][4]); unset($data[$k][5]);
                unset($data[$k][6]); unset($data[$k][7]);
                unset($data[$k][8]); unset($data[$k][9]);
                unset($data[$k][10]); unset($data[$k][11]);
                unset($data[$k][12]); unset($data[$k][13]);
                unset($data[$k][14]); unset($data[$k][15]);
                unset($data[$k][16]);
                $i++;
            }

            // Insert data
            DB::table('users')->insert($data);
        }
        
        // Close the file
        fclose($file);
    }

}
