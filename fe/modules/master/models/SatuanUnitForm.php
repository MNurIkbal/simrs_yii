<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "satuanunit_m".
 *
 * @property int $satuanunit_id
 * @property string $satuanunit_nama
 * @property string $satuanunit_singkatan
 * @property string $satuan_jenis 0=kecil , 1=sedang, 2=besar
 *
 * @property FakturpenerimaanobatdetailT[] $fakturpenerimaanobatdetailTs
 * @property FakturpenerimaanobatdetailT[] $fakturpenerimaanobatdetailTs0
 * @property ObatalkesM[] $obatalkesMs
 * @property ObatalkesM[] $obatalkesMs0
 * @property ObatsupplierM[] $obatsupplierMs
 * @property ObatsupplierM[] $obatsupplierMs0
 * @property PaketbmhpM[] $paketbmhpMs
 * @property PembelianobatdetailT[] $pembelianobatdetailTs
 * @property PembelianobatdetailT[] $pembelianobatdetailTs0
 * @property PenerimaanbarangdetailT[] $penerimaanbarangdetailTs
 * @property PenerimaanbarangdetailT[] $penerimaanbarangdetailTs0
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs0
 */
class SatuanUnitForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $satuanunit_id;
    public $satuanunit_nama;
    public $satuanunit_namalain;
    public $satuanunit_singkatan;
    public $satuan_jenis;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuanunit_nama', 'satuanunit_namalain'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['satuanunit_id'], 'integer'],
            [['satuanunit_nama', 'satuanunit_singkatan'], 'string'],
            [['satuan_jenis'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuanunit_id' => 'Satuanunit ID',
            'satuanunit_nama' => 'Nama Satuan',
            'satuanunit_namalain' => 'Nama Lainnya',
            'satuanunit_singkatan' => 'Satuanunit Singkatan',
            'satuan_jenis' => 'Satuan Jenis',
        ];
    }
}
