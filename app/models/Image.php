<?php

class Image extends Eloquent {

    public $timestamps = false;

    /**
     *  Sets item filename.
     */
    public function setFilename($relationId, $extension = 'png') 
    {
        // If a category has been set
        if(isset($this->category))
        {
            // Sequential Number (according to its category)
            $result = DB::select(DB::raw('SELECT filename FROM (SELECT * FROM `images` '
                . '                       WHERE relation_id = ' . $relationId . ' ORDER BY id DESC) as imagesN GROUP BY relation_id'));

            if (empty($result))
                $seq_number = 1;
            else
                $seq_number = substr(explode ('_', $result[0]->filename)[2], 0, -4) + 1;

            // Assemble name
            $this->filename = strtolower($this->category) . '_' . 
                              $this->relation_id . '_' . 
                              str_pad($seq_number, 9, "0", STR_PAD_LEFT) . '.' . 
                              $extension;
        } else
            $this->filename = str_random() . '_' . $this->relation_id . '.' . $extension;
    }

    /**
     * Returns item filename.
     * 
     * @return String
     */
    public function getFilename() {
        return $this->filename;
    }

    public function relation() 
    {
        switch ($this->category) 
        {
            case 'ITEM':
                return $this->belongsTo('Item', 'relation_id');

            default:
                return null;
        }
    }

}
