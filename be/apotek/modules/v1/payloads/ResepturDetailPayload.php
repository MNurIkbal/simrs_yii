<?php

namespace app\modules\v1\payloads;

use Yii;
use Doco\components\DocoBaseModel;

class ResepturDetailPayload extends DocoBaseModel
{
    public $obatalkes_id;
    public $racikan_id;
    public $satuankecil_id;
    public $sumberdana_id;
    public $reseptur_id;
    public $r;
    public $rke;
    public $permintaan_reseptur;
    public $jmlkemasan_reseptur;
    public $kekuatan_reseptur;
    public $satuankekuatan;
    public $qty_reseptur;
    public $hargasatuan_reseptur;
    public $signa_reseptur;
    public $harganetto_reseptur;
    public $hargajual_reseptur;
    public $etiket;
    public $iter;
    public $signa_id;
    public $status_implementasi;
    public $tgl_resepturdetail;
    public $qty_konversi;
    public $nama_racikan;
    public $qty_racikan;
    public $satuan_racikan_id;
    public $qty_medis;
    public $det;
    public $det_konversi;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'racikan_id', 'reseptur_id', 'qty_reseptur','qty_konversi'], 'required'],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'reseptur_id', 'kekuatan_reseptur','status_implementasi','satuan_racikan_id','rke','iter','signa_id'], 'integer'],
            [['qty_reseptur','qty_konversi', 'hargasatuan_reseptur', 'harganetto_reseptur', 'hargajual_reseptur','qty_racikan','det','det_konversi'], 'number'],
            [['signa_id', 'qty_medis'], 'safe'],
            [['signa_id'], 'integer'],
            [['satuankekuatan'], 'string', 'max' => 20],
            [['etiket','nama_racikan'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'racikan_id' => 'Racikan ID',
            'satuankecil_id' => 'Satuankecil ID',
            'sumberdana_id' => 'Sumberdana ID',
            'reseptur_id' => 'Reseptur ID',
            'r' => 'R',
            'rke' => 'Rke',
            'permintaan_reseptur' => 'Permintaan Reseptur',
            'jmlkemasan_reseptur' => 'Jmlkemasan Reseptur',
            'kekuatan_reseptur' => 'Kekuatan Reseptur',
            'satuankekuatan' => 'Satuankekuatan',
            'qty_reseptur' => 'Qty Reseptur',
            'hargasatuan_reseptur' => 'Hargasatuan Reseptur',
            'signa_reseptur' => 'Signa Reseptur',
            'harganetto_reseptur' => 'Harganetto Reseptur',
            'hargajual_reseptur' => 'Hargajual Reseptur',
            'etiket' => 'Etiket',
            'iter' => 'Iter',
            'satuansediaan' => 'Satuansediaan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'status_implementasi' => 'Status Implementasi',
            'nama_racikan' => 'Nama Racikan',
            'qty_racikan' => 'Jumlah Racikan',
            'satuan_racikan_id' => 'Satuan Racikan'
        ];
    }
}
