<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 09:59:09
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-26 11:57:12
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class PemeriksaanMataForm extends \yii\base\Model
{
    public $ruangan_id;
    public $konsulpoli_id;
    public $pemeriksaan_spesialis;
    public $pemeriksaanfisik_id;
    public $pendaftaran_id;
    public $pegawaiperawat_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $tglperiksafisik;
    public $visusOD;
    public $visusOS;
    public $siliaOD;
    public $siliaOS;
    public $palpebraOS;
    public $palpebraOD;
    public $konjungtivaOD;
    public $konjungtivaOS;
    public $korneaOD;
    public $korneaOS;
    public $bmdOD;
    public $bmdOS;
    public $pupilOD;
    public $pupilOS;
    public $irisOD;
    public $irisOS;
    public $lensaOD;
    public $lensaOS;
    public $catatanPemeriksaanFisik;
    public $vitreousHumor;
    public $funduskopi;
    public $keluhanUtama;
    public $anamnesisLanjutan;
    public $fisikMataTambahanOD;
    public $bagMataTambahan;
    public $fisikMataTambahanOS;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [[
                'visusOD', 'visusOS', 'siliaOD','siliaOS','palpebraOS','palpebraOD', 
                'konjungtivaOD','konjungtivaOS','korneaOD','korneaOS','bmdOD', 'bmdOS', 'pupilOD', 
                'pupilOS','irisOD','irisOS','lensaOD','lensaOS','catatanPemeriksaanFisik', 'vitreousHumor',
                'funduskopi', 'keluhanUtama', 'anamnesisLanjutan', 'fisikMataTambahanOD', 'bagMataTambahan',
                'fisikMataTambahanOS', 'pegawaiperawat_id', 'ruangan_id', 'konsulpoli_id', 'pemeriksaan_spesialis', 'tglperiksafisik'], 
            'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
        ];
    }
}
