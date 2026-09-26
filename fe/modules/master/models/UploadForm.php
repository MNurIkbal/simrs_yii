<?php

// namespace Doco\Master\models;
namespace app\modules\master\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $obatalkes_id;
    public $obatalkes_nama;
    public $hn_last;
    public $harga_sugesstion;
    public $harganetto;
    public $ket_ubah_harga;
    public $upload_file;
    public function rules()
    {
        return [
            [['obatalkes_id', 'hn_last' ,'harga_sugesstion' ,'harganetto', 'ket_ubah_harga'], 'default', 'value' => null],
            [['obatalkes_id', 'hn_last' ,'harga_sugesstion' ,'harganetto', 'ket_ubah_harga'], 'integer'],
            [['obatalkes_nama'], 'string'],
            [['harganetto'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['upload_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx', 'maxSize'=>1024*1024*20, 'message' => \Yii::t('fe', '')],
        ];
    }
    
    public function upload()
    {
        $path = \Yii::getAlias('@webroot');
        if ($this->validate()) {
            $this->upload_file->saveAs($path. '/media/base-price/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
            return true;
        } else {
            return false;
        }
    }
}