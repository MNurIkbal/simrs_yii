<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-30 15:08:17
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-31 14:58:09
 */


namespace Doco\radiologi\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class InputExpertiseForm extends Model
{
    const Scenario_SkipExpertise = 'skipexpertise';
    const Scenario_default = 'default';

    public $tgl_hasilrad;
    public $expertise_id;
    public $hasilpemeriksaanrad_id;
    public $hasil_expertise;
    public $kesimpulan;
    public $kesan;
    public $is_hasilkritis;
    public $no_hasilrad;

    public function rules()
    {
        return [
            [['tgl_hasilrad', 'hasil_expertise','kesan', 'kesimpulan'], 'required', 'message'=>"{attribute} Tidak boleh kosong", 'on' => self::Scenario_default],
            [['tgl_hasilrad'], 'required', 'message'=>"{attribute} Tidak boleh kosong", 'on' => self::Scenario_SkipExpertise],
            [['hasilpemeriksaanrad_id','expertise_id','is_hasilkritis','no_hasilrad', 'kesan', 'kesimpulan'], 'safe']
        ];
    }
    public function attributeLabels()
    {
        return [
            'tgl_hasilrad'=>Yii::t('fe', 'Tanggal'),
            'no_hasilrad'=>Yii::t('fe', 'No. Hasil'),
            'hasil_expertise'=>Yii::t('fe', 'Hasil expertise'),
            'kesan'=>Yii::t('fe', 'Deskripsi'),
            'kesimpulan'=>Yii::t('fe', 'Kesan'),
            'is_hasilkritis'=>Yii::t('fe', 'Hasil kritis'),
        ];
    }

}