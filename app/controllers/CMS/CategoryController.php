<?php

class CategoryController extends BaseController {

    /**
     *  Returns all categories.
     * 
     * @return JSON
     */
    public function listAll()
    {
        return Category::orderBy('name')->get();
    }
}
