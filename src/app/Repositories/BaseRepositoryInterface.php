<?php


namespace App\Repositories;


use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{

    public function all(): Collection;

    public function update(int $id, array $attributes): bool;

}