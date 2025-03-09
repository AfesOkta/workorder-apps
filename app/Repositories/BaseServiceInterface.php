<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    public function create(array $attributes) : Model;

	public function all() : ? Collection;

	public function find($id, $columns) : ? Model;

	public function where($column, $value, $option);

    public function whereNotIn($column, $value, $option);

	public function delete($id);

	public function update($input, $id);

	public function makeModel();
}
