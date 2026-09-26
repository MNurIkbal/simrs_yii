<?php
/**
 * @Author: Iqbal@docotel.om
 * @Date:   2018-09-03 16:36:56
 * @Last Modified by: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Last Modified time: 2020-09-23 16:16:16
 * @Description: Change currency data from int to double
 */

namespace app\modules\master\models;
use yii\web\UploadedFile;
use Yii;

/**
 *
 * @property integer $obatalkes_id
 * @property double $hargaygdipakai
 * @property double $harganetto_ygdipakai
 * @property double $harganetto
 * @property string $obatalkes_nama
 */
class BasePrice extends \yii\base\Model
{
    const UPLOAD_FILE = 'upload_file';
    public $obatalkes_id;
    public $hn_last;
    public $last_harganetto;
    public $harga_sugesstion;
    public $harganetto;
    public $obatalkes_nama;
    public $ket_ubah_harga;
    public $upload_file;
    public $catatan;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'hn_last' ,'harga_sugesstion' ,'harganetto', 'ket_ubah_harga', 'catatan'], 'default', 'value' => null],
            [['obatalkes_id' , 'ket_ubah_harga'], 'integer'],
            [['harganetto', 'hn_last', 'last_harganetto', 'harga_sugesstion'], 'number'],
            [['obatalkes_nama', 'catatan'], 'string'],
            [['harganetto', 'catatan'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['upload_file'], 
                'file', 
                'skipOnEmpty' => false, 
                'extensions' => 'xls, xlsx', 
                'maxSize'=>1024*1024*20, 
                'message' => \Yii::t('fe', ''),
                'on' => self::UPLOAD_FILE
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => Yii::t('app', 'Obat Alkes ID'),
            'obatalkes_nama' => Yii::t('app', 'Nama Obat Alkes'),
            'hn_last' => Yii::t('app', 'Harga Netto Terakhir'),
            'harga_sugesstion' => Yii::t('app', 'Suggestion System'),
            'harganetto' => Yii::t('app', 'Harga Dasar Yang Digunakan'),
            'ket_ubah_harga' => Yii::t('app', 'Keterangan Ubah Harga'),
        ];
    }
}
