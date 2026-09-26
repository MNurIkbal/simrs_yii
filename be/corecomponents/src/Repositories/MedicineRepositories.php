<?php

/**
 * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Repositories;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\ObatAlkesMaster;
use Doco\models\SatuanKonversiView;
use Doco\models\Lookup;

class MedicineRepositories {

    /**
     * Function getMedicine
     * 
     * param untuk kebutuhan pencarian obat
     * @param Array $payload['obatalkes_id'] => kalo di set buat ng return 1 obat sesuai dengan obatalkes_id, ex: 1 / [1, 2, 3]
     * @param Array $payload['obatalkes_nama'] => kalo di set bisa digunakan buat cari obat sesuai dengan nama obat yg dikirim
     * 
     * param untuk kebutuhan pagination
     * @param Array $payload['pagination'] => kalo di set berarti result dari akan di return sesuai dengan limit serta pagination
     * @param Array $payload['pagination']['limit'] => untuk menentukan limit dari result yg dihasilkan
     * @param Array $payload['pagination']['page'] => untuk menentukan page keberapa yg akan dikeluarkan hasilnya
     * 
     * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
     * A product of PT. Citraraya Nusatama
     * Powered by Sirs
     */
    public function getMedicine($payload = [])
    {
        $model = ObatAlkesMaster::find()->select([
            'obatalkes_id',
            'obatalkes_nama',
            'obatalkes_kode'
        ])->where([
            'jenisobatalkes_id' => DocoConstants::JENIS_OBATALKES_OBAT
        ]);

        if ( isset($payload['obatalkes_id']) && !empty($payload['obatalkes_id']) ) {
            $model->andWhere([
                'obatalkes_id' => $payload['obatalkes_id']
            ]);

            return $model->asArray()->one();
        }

        if ( isset($payload['obatalkes_nama']) && !empty($payload['obatalkes_nama']) ) {
            $model->andWhere([
                'ILIKE', 'obatalkes_nama', $payload['obatalkes_nama']
            ]);
        }

        if ( isset($payload['pagination']) && !empty($payload['pagination']) ) {
            if ( isset($payload['pagination']['page']) && !empty($payload['pagination']['page']) ) {
                $offset = ($payload['pagination']['page'] - 1) * $payload['pagination']['limit'];
                $model->offset($offset);
            }

            if ( isset($payload['pagination']['limit']) && !empty($payload['pagination']['limit']) ) {
                $model->limit($payload['pagination']['limit']);
            }
        }
        
        return $model->asArray()->all();
    }

    /**
     * Function getUnit
     * 
     * @param Array $payload['obatalkes_id'] => di set untuk memunculkan satuan sesuai dengan obat yang dipilih
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi.fitrianto@sirs.co.id)
     * A product of PT. Citraraya Nusatama
     * Powered by Sirs
     */
    public function getUnit($payload = [])
    {
        $model = SatuanKonversiView::find()->select([
            'satuankonversi_id',
            'obatalkes_id',
            'satuanbesar_id',
            'satuan_besar',
            'satuankecil_id',
            'satuan_kecil',
        ]);
        if ( isset($payload['obatalkes_id']) && !empty($payload['obatalkes_id']) ) {
            $model->andWhere([
                'obatalkes_id' => $payload['obatalkes_id']
            ]);
        }
        return $model->asArray()->all();
    }

    public function getRute($payload = [])
    {
        $model = Lookup::find()->select([
            'lookup_id',
            'lookup_name',
            'lookup_value',
        ]);
        if ( isset($payload['lookup_id']) && !empty($payload['lookup_id']) ) {
            $model->andWhere([
                'lookup_id' => $payload['lookup_id']
            ]);
        }
        if ( isset($payload['lookup_name']) && !empty($payload['lookup_name']) ) {
            $model->andWhere([
                'ILIKE', 'lookup_name', $payload['lookup_name']
            ]);
        }
        $model->andWhere([
            'lookup_type' => 'rute_obat'
        ]);
        return $model->asArray()->all();
    }
}