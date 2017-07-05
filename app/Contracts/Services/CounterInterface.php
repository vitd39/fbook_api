<?php

namespace App\Contracts\Services;

interface CounterInterface
{
    public function show($identifier, $id = null);

    public function showAndCount($identifier, $id = null);
}
