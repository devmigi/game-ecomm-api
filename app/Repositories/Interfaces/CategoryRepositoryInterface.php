<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function all();

    public function getById($id);

    public function products($id); // get category products
}