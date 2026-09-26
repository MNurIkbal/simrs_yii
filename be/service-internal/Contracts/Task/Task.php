<?php

namespace Integrasi\Contracts\Task;

interface Task 
{
    /**
     * Untuk Menjadi execute connector
     *
     * @return string
     */
    public function execute();
}