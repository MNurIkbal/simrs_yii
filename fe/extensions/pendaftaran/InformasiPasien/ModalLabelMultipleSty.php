<?php

namespace app\extensions\pendaftaran\InformasiPasien;

use Yii;
use app\modules\pendaftaran\processes\ModalLabelMultipleProcess;

class ModalLabelMultipleSty extends ModalLabelMultipleProcess
{
    /**
     * @inheritance
     */
    protected function processFlow($controller)
    {
        $attributes = $this->getAttributes();
        $config = $this->getKonfig();
        $listOpt = [];
        for ($i=0; $i < $config['jumlahCetakan']; $i++) {
            if ($i) {
                if (empty($i%2)) {
                    $listOpt[$i] = $i;
                }
            } else {
                $listOpt[''] = 'Pilih';
            }
        }

        $config['listOpt'] = $listOpt;
        $config['jenisTemplate'] = 'sty';
        $config['htmlMode'] = ((($attributes['jenis'] == 'ranap') || ($attributes['jenis'] == 'igd')) ? false : true );
        
        return $controller->renderPartial('_modal_jumlah_cetakan', array_merge($attributes, $config));
    }
}