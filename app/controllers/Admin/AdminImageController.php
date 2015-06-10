<?php

class AdminImageController extends BaseController {

        /**
        * Image Model
        * @var Image
        */
        protected $image;
    
	/**
        * Inject the models.
        * @param Image $image
        */
       public function __construct(Image $image)
       {
           $this->image = $image;
       }
       
       /**
        * Remove the specified resource from storage.
        *
        * @param $id
        * @return JSON Response
        */
        public function delete($id)
        {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
           
           // Find image in the database
            $image = Image::find($id);
           
           // Check if image exists
            if (is_null($image)) {
                $response['success'] = false;
                $response['message'] = 'Image already deleted.';
            } else {
               // Delete it
                if ($image->delete()) 
                {
                    // Destination folder
                    $destinationPath = 'uploads/items';
                    
                    // Get files inside folder
                    if(is_dir($destinationPath)) 
                    {
                        $files = scandir($destinationPath);
                        $relatedFiles = array();
                        
                        // This operation is performed in order to delete thumbnails
                        foreach($files as $file) { 
                            // Add all files with same prefix to deletion list (excludes extension)
                            if (str_contains($file, substr($image->filename, 0, -4)))
                                $relatedFiles[] = $file;
                        }
                        
                        // Delete files
                        foreach($relatedFiles as $file)
                        {
                            $filename = $destinationPath . '/' . $file;
                            if(file_exists($filename))
                            {
                                if (!unlink($filename))
                                {
                                    $response['success'] = false;
                                    $response['message'] = 'Image deleted from database but file could not be deleted.';
                                }
                            }
                        }
                    }
                    
                } else // In case it does not work...
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the image could not be deleted.';
                }
            }           
           
            return $response;
        }
}