<?php

namespace app\modules\v1\payload;

use Yii;
use Doco\components\DocoBaseModel;
use Doco\components\DocoConstants;

class ParamModel extends DocoBaseModel
{
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $pendaftaran_id;
    public $username;
    public $password;
    public $alasan_batal;
    public $tgl_batal;
    public $pegawai_id;
    public $status_konsul;
    public $tgl_selesaikonsul;
    public $jawaban_konsul;

    protected $xssProtected = [
        'alasan_batal',
        'username',
        'password',
        'jawaban_konsul'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'alasan_batal', 'pendaftaran_id'], 'required', 'on' => 'batal-periksa'],
            [['pendaftaran_id', 'ruangan_id', 'pegawai_id', 'tgl_selesaikonsul', 'status_konsul', 'jawaban_konsul'], 'required', 'on' => 'permintaan-konsul'],

            [['tgl_batal', 'tgl_selesaikonsul'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [[
                'ruangan_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'pendaftaran_id',
                'status_konsul',
                'tgl_selesaikonsul',
                'jawaban_konsul',
            ],'safe'],
            [[
                'ruangan_id', 
                'kelaspelayanan_id', 
                'penjamin_id',
                'pendaftaran_id',
                'pegawai_id',
                'status_konsul',
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
