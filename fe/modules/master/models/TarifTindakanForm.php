<?php

namespace app\modules\master\models;
use Yii;
use app\components\DocoHelpers;
/**
 * This is the model class for table "tariftindakan_m".
 *
 * @property int $tariftindakan_id
 * @property int $kelaspelayanan_id
 * @property int $komponentarif_id
 * @property int $daftartindakan_id
 * @property int $jenistarif_id
 * @property int $perdatarif_id
 * @property double $harga_tariftindakan
 * @property int $persendiskon_tindakan
 * @property double $hargadiskon_tindakan
 * @property int $persencyto_tindakan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $tipepaket_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property array $list_komponen
 */
class TarifTindakanForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    const AKOMODASI = 'AKOMODASI';

    public $tindakanpaket;
    public $tipepaket_id;
    public $daftartindakan_id;
    public $carabayar_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $perdatarif_id;
    public $komponentarif_id;
    public $tariftindakan_id;
    public $persencyto_tindakan;
    public $persendiskon_tindakan;
    public $harga_tariftindakan;
    public $total_harga;
    public $is_active;
    public $is_deleted;
    public $list_komponen;
    public $kamar_ruangan_id;
    public $is_akomodasi;
    public $persen_penyulit;
    public $dokter_id;
    public $persentase;
    public $total_harga_tindakan;
    public $is_persentase;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelaspelayanan_id','carabayar_id','penjamin_id','tindakanpaket','perdatarif_id'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['kamar_ruangan_id','carabayar_id','penjamin_id','tindakanpaket','perdatarif_id'], 'required', 'on' => self::AKOMODASI, 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['kelaspelayanan_id', 'daftartindakan_id', 'perdatarif_id', 'persendiskon_tindakan', 'persencyto_tindakan','tipepaket_id', 'persen_penyulit'], 'default', 'value' => null],
            [['kelaspelayanan_id', 'daftartindakan_id', 'perdatarif_id', 'tipepaket_id', 'dokter_id'], 'integer'],
            [['tariftindakan_id','komponentarif_id','is_deleted','tindakanpaket', 'total_harga', 'total_harga_tindakan', 'persentase', 'list_komponen', 'kamar_ruangan_id', 'is_akomodasi', 'persenpenyulit_tindakan', 'dokter_id', 'is_persentase'], 'safe'],
            [['is_active', 'is_persentase'], 'boolean'],
            [['harga_tariftindakan'], 'multipleKomponen'],
            [['tindakanpaket'], 'cekPaket', 'on'=>'default', 'skipOnEmpty'=>false],
            
            // [['harga_tariftindakan', 'hargadiskon_tindakan'], 'number'],
            // [['additional_data'], 'string'],
            // [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            // [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
            // [['jenistarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenistarifM::className(), 'targetAttribute' => ['jenistarif_id' => 'jenistarif_id']],
            // [['kelaspelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelaspelayananM::className(), 'targetAttribute' => ['kelaspelayanan_id' => 'kelaspelayanan_id']],
            // [['komponentarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponentarifM::className(), 'targetAttribute' => ['komponentarif_id' => 'komponentarif_id']],
            // [['perdatarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => PerdatarifM::className(), 'targetAttribute' => ['perdatarif_id' => 'perdatarif_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tariftindakan_id' => 'Tariftindakan ID',
            'kelaspelayanan_id' => Yii::t('fe','Kelas Pelayanan'),
            'carabayar_id' => Yii::t('fe','Cara bayar'),
            'penjamin_id' => Yii::t('fe','Penjamin'),
            'komponentarif_id' => 'Komponentarif ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'jenistarif_id' => 'Jenistarif ID',
            'perdatarif_id' => 'Perda / SK',
            'harga_tariftindakan' => 'Komponen',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'hargadiskon_tindakan' => 'Hargadiskon Tindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => Yii::t('fe','Aktif'),
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'tipepaket_id' => 'Tipepaket ID',
            'tindakanpaket' => 'Tindakan Paket',
            'total_harga' => 'Total Harga',
            'kamar_ruangan_id' => 'Kamar',
        ];
    }

    public function multipleKomponen($param, $attribute)
    {
        // if(count($this->harga_tariftindakan) > 0){
            $deleted = [];
            foreach ($this->harga_tariftindakan as $key => $value) {
                if( $this->harga_tariftindakan[$key]<0){
                    $errMsg = 'Harga Komponen Tidak Boleh Kosong';
                    if(!empty($this->tipepaket_id)){
                        $errMsg = 'Nilai Tarif paket tidak boleh Nol';
                    }
                    DocoHelpers::multipleParseError($this, $errMsg, 'harga_tariftindakan', $key);
                }
                if(isset($this->is_deleted[$key])){
                    $deleted[] = $key;
                }
            }
            // echo "<pre>";var_dump( count($deleted)  );
            // die();
            // if(count($deleted) == count($this->harga_tariftindakan)){
            //     $this->addError('harga_tariftindakan', 'Komponen Tidak Boleh Kosong');
            // }
        // }else{
        //     $this->addError('harga_tariftindakan', 'Komponen Tidak Boleh Kosong');
        // }
        
    }
    public function cekPaket($param, $attribute){
        if(empty($this->tindakanpaket)){
            $this->addError('tindakanpaket', 'Jenis Tindakan / Paket Tidak Boleh Kosong');
        }else{
            if( $this->tindakanpaket == 'TINDAKAN' && empty($this->daftartindakan_id) ){
                $this->addError('tindakanpaket', 'Nama Tindakan Tidak Boleh Kosong');
            }
            if( $this->tindakanpaket == 'PAKET' && empty($this->tipepaket_id) ){
                $this->addError('tindakanpaket', 'Nama Paket Tidak Boleh Kosong');
            }
        }
    }
}
