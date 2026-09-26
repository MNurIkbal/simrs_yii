<?php

namespace app\modules\v1\services\Contracts;

interface DiagnosaInterface 
{
    /**
     * @method : mendapatkan data diagnosa berdasarkan versi tabular list
     */
    public function getDiagnosa();

    
    /**
     * @method : set versi tabular list
     */
    public function setTabularlistVersi();

    
    /**
     * @method : mengganti versi tabularlist menjadi perawat jika kelompok nya perawat
     */
    public function kelompokPerawat();
}