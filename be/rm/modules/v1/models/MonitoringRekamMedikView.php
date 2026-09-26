<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;

class MonitoringRekamMedikView extends \Doco\components\DocoActiveRecord {
    public static function tableName()
    {
        return 'monitoringrekammedik_v';
    }

    public function getDocumentData($payload)
    {
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = (isset($payload['limit']) ? $payload['limit'] : 20);
        $nama_norm_pendaftaran = isset($payload['nama_rm_pendaftaran']) ? $payload['nama_rm_pendaftaran'] : null;
        $query = self::find()->select([
            'permintaandokrekammedik_id',
            'tgl_permintaan',
            'tgl_dikembalikan',
            'pendaftaran_id',
            'pasienadmisi_id',
            'status_rekam_medik',
            'status_rekam_medik_nama',
            'no_pendaftaran',
            'nama_pasien',
            'ruangan_id',
            'ruangan_nama',
            'instalasi_id',
            'instalasi_nama',
            'no_rekam_medik',
            'case when instalasi_id=\''.DocoConstants::INST_ID_RI.'\' then concat(kamarruangan_nokamar, \' - \', no_tempattidur) else ruangan_nama end as status_ruangan'
        ]);
        if ( isset($payload['instalasi_id']) && !is_null($payload['instalasi_id']) ) {
            $query->andWhere([
                'instalasi_id' => $payload['instalasi_id']
            ]);
        }
        if ( isset($payload['ruangan_id']) && !is_null($payload['ruangan_id']) ) {
            $query->andWhere([
                'ruangan_id' => $payload['ruangan_id']
            ]);
        }
        if ( isset($payload['status_rekam_medik']) && !is_null($payload['status_rekam_medik']) ) {
            $query->andWhere([
                'status_rekam_medik' => $payload['status_rekam_medik']
            ]);
        }
        if ( isset($payload['nama_rm_pendaftaran']) && !empty(isset($payload['nama_rm_pendaftaran'])) ) {
            $query->andWhere([
                'or', 
                ['ILIKE', 'nama_pasien', $payload['nama_rm_pendaftaran']],
                ['ILIKE', 'no_pendaftaran', $payload['nama_rm_pendaftaran']],
                ['ILIKE', 'no_rekam_medik', $payload['nama_rm_pendaftaran']],
            ]);
        }
        if( isset($payload['tgl_pendaftaran']) && !empty($payload['tgl_pendaftaran']) ) {
            $explodeDate = explode(' - ',  $payload['tgl_pendaftaran']);
            $startDate = date('Y-m-d H:i:00', strtotime($explodeDate[0]));
            $endDate = date('Y-m-d H:i:00', strtotime($explodeDate[1]));
        
            $query->andWhere(['between', 'tgl_permintaan', $startDate, $endDate]);
        }
        $query->offset( ($page - 1) * $limit);
        $query->limit($limit + 1);
        $query->orderBy([
            'tgl_permintaan' => SORT_DESC
        ]);

        return $query->asArray()->all();
    }
}