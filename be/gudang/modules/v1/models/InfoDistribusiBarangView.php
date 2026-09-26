<?php

namespace app\modules\v1\models;

use Yii;

class InfoDistribusiBarangView extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infodistribusibarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_pesanbarang' => Yii::t('app', 'Tgl Pemesanan'),
            'ruangan_tujuan' => Yii::t('app', 'Ruangan Tujuan'),
            'instalasi_tujuan' => Yii::t('app', 'Instalasi Tujuan'),
            'no_pemesanan' => Yii::t('app', 'No Pemesanan'),
            'ruangan_pemesan' => Yii::t('app', 'Ruangan Pemesan'),
            'instalasi_pemesan' => Yii::t('app', 'Instalasi Pemesan'),
            'status_pengiriman' => Yii::t('app', 'Status pengiriman')
        ];
    }
}
