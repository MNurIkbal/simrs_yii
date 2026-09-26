<?php

namespace Doco\models;

use Yii;

class InfoKunjunganRiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infokunjunganri_v';
    }

    public static function unfinishedRm($pegawai_id)
    {
		$start = date('Y-m-d 00:00:00', strtotime("-30 Days"));
		$end   = date('Y-m-d 23:59:59', strtotime("today"));

        return self::find()->andWhere(['pegawai_id' => $pegawai_id])
            ->andWhere([
                'is_deleted' => false,
                'resumemedisri_id' => null,
                'status_periksa_id' => [441,487]
            ])
            ->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
    }
}
