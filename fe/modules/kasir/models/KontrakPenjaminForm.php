<?php

namespace app\modules\kasir\models;

use Yii;
use app\components\DocoBaseModel;

class KontrakPenjaminForm extends DocoBaseModel
{
    public $kontrakpenjamin_id;
    public $penjamin_id;
    public $no_kontrak;
    public $nama_kontrak;
    public $tgl_mulai;
    public $tgl_selesai;

    protected $xssProtected = [
        'no_kontrak',
        'nama_kontrak',
    ];
    


    public function rules()
    {
        return [
            [['penjamin_id','no_kontrak', 'nama_kontrak','tgl_mulai','tgl_selesai'], 'required'],
            [['no_kontrak'], 'string', 'max' => 20],
            [['nama_kontrak'], 'string', 'max' => 100],
            [['penjamin_id'], 'integer'],
            [['penjamin_id','no_kontrak', 'nama_kontrak','tgl_mulai','tgl_selesai'], 'safe'],
            // [['is_deleted', 'is_active'], 'boolean'],
            [['tgl_selesai', 'tgl_mulai'], 'date','format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kontrakpenjamin_id' => 'Kontrak Penjamin ID',
            'penjamin_id' => 'Nama Penjamin',
            'no_kontrak' => 'Nomor Kontrak',
            'nama_kontrak' => 'Nama Kontrak',
            'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai',
            
        ];
    }
}
