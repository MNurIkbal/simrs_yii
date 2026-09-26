<?php

namespace app\modules\master\models;

use app\components\DocoBaseModel;

class PaketFisioForm extends DocoBaseModel
{
    public $parent_id;
    public $kode_paket;
    public $nama_paket;
    public $namalainya_paket;
    public $frekuensi;
    public $jumlah;
    public $is_active;
    public $list_tindakan;
    public $catatan;

    protected $xssProtected = [
        'kode_paket',
        'nama_paket',
        'namalainya_paket',
        'catatan'
    ];

    public function rules()
    {
        return [
            [
                [
                    'kode_paket',
                    'nama_paket',
                    'frekuensi',
                    'jumlah'
                ], 'required',
                'message' => '{attribute} Harus Diisi'
            ],
            [
                [
                    'parent_id',
                    'kode_paket',
                    'nama_paket',
                    'namalainya_paket',
                    'frekuensi',
                    'jumlah',
                    'is_active',
                    'catatan',
                    'list_tindakan'
                ], 'safe'
            ],
            [
                [
                    'jumlah',
                    'frekuensi'
                ], 'integer'
            ],
            [
                ['catatan'],
                'string',
                'min' => 0,
                'max' => 200
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'kode_paket' => 'Kode Paket',
            'nama_paket' => 'Nama Paket',
            'namalainya_paket' => 'Nama Paket Lainnya',
            'frekuensi' => 'Frekuensi',
            'jumlah' => 'Jumlah Pilihan',
            'is_active' => 'Is Aktif',
            'catatan' => 'Catatan',
        ];
    }
}
