<?php 

namespace app\components\Services\Contracts;


interface DiagnosaInterface {
    
    /**
     * @method : Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @param string $q
     * @param integer $page 
     * @param string $type jenis diagnosa 
     * @param integer $formatResponse  0 formatResponseId, 1 formatResponseCodeName , 2 formatResponseFull
     */
    public function getDiagnosa($q ,$page ,$type , $formatResponse );

    /**
     * @method : response format diagnosa dengan id
     * @param array $list
     */
    public function formatResponseId($list);

    /**
     * @method : response format diagnosa dengan code dan nama
     * @param array $list
     */
    public function formatResponseCodeName($list);

    
    /**
     * @method : response format diagnosa dengan id ,code dan nama
     * @param array $list
     */
    public function formatResponseFull($list);

    /**
     * @method : response format diagnosa dengan code 
     * @param array $list
     */
    public function formatResponseCode($list);
}