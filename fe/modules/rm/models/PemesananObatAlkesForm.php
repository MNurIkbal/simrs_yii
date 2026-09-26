<?php
namespace app\modules\rm\models;

use Yii;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 14:43:17
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-15 16:05:08
 */

class PemesananObatAlkesForm extends \yii\base\Model
{
	public $instalasi_tujuan;
	public $ruangan_tujuan;
	public $tanggal_kirim;
	public $kode_obat;
    public $qty;

	public function rules()
    {
        return [
            [['instalasi_tujuan','ruangan_tujuan','tanggal_kirim','kode_obat','qty'], 'required'],            
            [['qty'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_tujuan' => \Yii::t('fe','instalasi tujuan'),
            'ruangan_tujuan' => \Yii::t('fe','ruangan tujuan'),
            'tanggal_kirim' => \Yii::t('fe','tanggal kirim'),
            'kode_obat' => \Yii::t('fe','Obat alkes'),
            'qty'=>'Qty',
        ];
    }
}

