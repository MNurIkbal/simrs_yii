<?php

namespace app\modules\master\models;

use Yii;
use yii\db\Query;
use yii\validators\UniqueValidator;
use app\components\DocoBaseModel;

/**
 * This is the model class for table "kelaspelayanan_m".
 *
 * @property int $kelaspelayanan_id
 * @property int $jeniskelas_id
 * @property string $kelaspelayanan_nama
 * @property string $kelaspelayanan_namalainnya
 * @property double $persentasirujin
 * @property int $urutankelas
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
 *
 * @property AsuransipasienM[] $asuransipasienMs
 * @property BookingkamarT[] $bookingkamarTs
 * @property KamarruanganM[] $kamarruanganMs
 * @property JeniskelasM $jeniskelas
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property TanggunganpenjaminM[] $tanggunganpenjaminMs
 * @property TariftindakanM[] $tariftindakanMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanpelayananT[] $tindakanpelayananTs0
 * @property TipepaketM[] $tipepaketMs
 */
class KelasPelayananForm extends DocoBaseModel
{
    public $jeniskelas_id;
    public $dataKelasPelayanan;
    public $kelaspelayanan_nama;
    public $kelaspelayanan_namalainnya;
    public $list_kelas_pelayanan;
    public $is_active;
    protected $xssProtected = [
        'kelaspelayanan_nama',
        'kelaspelayanan_namalainnya'
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskelas_id','list_kelas_pelayanan'], 'required','on' => 'save-all'],
            [['jeniskelas_id', 'kelaspelayanan_nama'], 'required','on' => 'set-list'],
            [[
                'kelaspelayanan_nama', 
                'kelaspelayanan_namalainnya',
                'dataKelasPelayanan',
                'jeniskelas_id',
                'list_kelas_pelayanan',
                'is_active'
            ], 'safe'],
            [['kelaspelayanan_nama'],'checkUnique','on' => 'set-list'],
        ];
    }

    public function checkUnique($attribute, $params)
    {
        $request = Yii::$app->request;
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $jenis = $this->jeniskelas_id;
        $nama = $this->kelaspelayanan_nama;
        $lainnya = $this->kelaspelayanan_namalainnya;
        $dataPelayanan = Yii::$app->cache->get("data-pelayanan-{$instalasi}");
        if ($dataPelayanan !== false) {
            foreach ($dataPelayanan as $value) {
                if (($value['jeniskelas_id'] == $jenis) && ($value['kelaspelayanan_nama'] == $nama)) {
                    $this->addError('kelaspelayanan_nama','Nama Sudah Terpakai');
                    return false;
                }

                // if (preg_match("/\b({$lainnya})\b/i", @$value['kelaspelayanan_namalainnya'])) {
                //     $this->addError('kelaspelayanan_namalainnya','Nama Sudah Terpakai');
                //     return false;
                // }
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskelas_id' => 'Jenis Kelas',
            'kelaspelayanan_nama' => 'Kelas Pelayanan',
            'kelaspelayanan_namalainnya' => 'Nama Lainnya',
            'persentasirujin' => 'Persentasirujin',
            'urutankelas' => 'Urutankelas',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Status',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

}
