<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-11 15:00:41
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-17 14:33:20
 */

namespace app\modules\pendaftaran\models;

use Yii;

class IgdForm extends \yii\base\Model
{
	public $tgl_pendaftaran;
	public $ruangan_id;
	public $jenis_kasus_penyakit_id;
	public $kelaspelayanan_id;
	public $dokter_id;
	public $carabayar_id;
	public $penjamin_id;
	public $keadaan_masuk;
	public $transportasi;
	public $keterangan;
    public $rujukan_id;

	public function rules()
    {
         return [
            [['tgl_pendaftaran', 'ruangan_id', 'jenis_kasus_penyakit_id', 'kelaspelayanan_id', 'dokter_id', 'carabayar_id','penjamin_id','rujukan_id'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],            
            [['keadaan_masuk', 'transportasi','keterangan'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tgl_pendaftaran' => \Yii::t('fe', 'Tanggal pendaftaran'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'rujukan_id' => \Yii::t('fe', 'Asal Rujukan'),
            'jenis_kasus_penyakit_id' => \Yii::t('fe', 'Jenis kasus penyakit'),
            'kelaspelayanan_id' => \Yii::t('fe', 'Kelas pelayanan'),
            'dokter_id' => \Yii::t('fe', 'Dokter'),
            'carabayar_id' => \Yii::t('fe', 'Cara bayar'),
            'penjamin_id' => \Yii::t('fe', 'Penjamin'),
            'keadaan_masuk'=> \Yii::t('fe', 'Keadaan masuk'),
            'transportasi'=> \Yii::t('fe', 'Transportasi'),
            'keterangan'=> \Yii::t('fe', 'Keterangan pendaftaran'),
        ];
    }

}
