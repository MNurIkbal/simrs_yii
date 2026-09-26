<?php

namespace Doco\components;

use Yii;


class PelayananHelpers
{
    // to grouping same value diagnosa penyerta in array format 
    public function FormatDiagnosaPenyerta($array = [])
    {   
        $pieceDiagnose = [];
        if(!empty($array)){
            foreach ($array as $diagnose) {
                $a_diag_penyerta = !empty($diagnose['a_diag_penyerta']) ? $diagnose['a_diag_penyerta'] : [];
                if(!is_array($a_diag_penyerta)){
                    $a_diag_penyerta = json_decode($a_diag_penyerta,TRUE);
                }
                foreach($a_diag_penyerta as $x){
                    if(!empty($pieceDiagnose)){
                        if (isset($x['text'])) {
                            if(array_search($x['text'], array_column($pieceDiagnose, 'text')) === false){
                                $pieceDiagnose[] = $x;
                            }
                        }
                    }else {
                        $pieceDiagnose[] = $x;
                    }
                }
            }
        }
        return $pieceDiagnose;
    } 

    public function decryptId($id){
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }

        return $id;
    }
}
