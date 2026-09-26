<?php

namespace Doco\payload;

use Yii;
use Doco\components\DocoBaseModel;

class Reseptur extends DocoBaseModel
{
    public $ruangan_id;
    public $pendaftaran_id;
    public $tglreseptur;
    public $ruanganreseptur_id;
    public $racikan_id;
    public $resepturDetail;
    public $kategori_resep;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'ruangan_id', 
                'pendaftaran_id',
                'ruanganreseptur_id',
            ], 'required'],
            [['tglreseptur'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [[
                'ruangan_id', 
                'pendaftaran_id', 
                'tglreseptur', 
                'ruanganreseptur_id',
                'racikan_id',
                'resepturDetail',
                'kategori_resep'
            ],'safe'],
            [[
                'ruangan_id', 
                'pendaftaran_id', 
                'ruanganreseptur_id'
            ], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl_batal' => 'Tanggal Batal',
            'tgl_selesaikonsul' => 'Tanggal Selesai Konsul'
        ];
    }
}
