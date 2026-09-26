<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 17:45:47
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-16 17:48:44
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaanlab_t".
 *
 * @property int $hasilpemeriksaanlab_id
 * @property int $pasien_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property string $nohasilperiksalab
 * @property string $tgl_hasilpemeriksaanlab
 * @property string $tgl_pengambilanhasil
 * @property string $hasil_kelompokumur
 * @property string $hasil_jeniskelamin
 * @property string $status_periksahasil
 * @property string $catatan_labklinik
 * @property bool $printhasillab
 * @property string $kesimpulan
 * @property string $dokterpj_luarrs
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
 * @property DetailhasilpemeriksaanlabT[] $detailhasilpemeriksaanlabTs
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PasienmasukpenunjangT $pasienmasukpenunjang
 * @property PendaftaranT $pendaftaran
 */
class HasilPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanlab_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'nohasilperiksalab', 'tgl_hasilpemeriksaanlab', 'hasil_kelompokumur', 'hasil_jeniskelamin', 'status_periksahasil'], 'required'],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_hasilpemeriksaanlab', 'tgl_pengambilanhasil', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['catatan_labklinik', 'kesimpulan', 'additional_data'], 'string'],
            [['printhasillab', 'is_deleted', 'is_active'], 'boolean'],
            [['nohasilperiksalab'], 'string', 'max' => 20],
            [['hasil_kelompokumur', 'hasil_jeniskelamin', 'status_periksahasil'], 'string', 'max' => 50],
            [['dokterpj_luarrs'], 'string', 'max' => 100],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienM::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            [['pasienadmisi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienadmisiT::className(), 'targetAttribute' => ['pasienadmisi_id' => 'pasienadmisi_id']],
            [['pasienmasukpenunjang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PasienmasukpenunjangT::className(), 'targetAttribute' => ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => PendaftaranT::className(), 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'pasien_id' => 'Pasien ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nohasilperiksalab' => 'Nohasilperiksalab',
            'tgl_hasilpemeriksaanlab' => 'Tgl Hasilpemeriksaanlab',
            'tgl_pengambilanhasil' => 'Tgl Pengambilanhasil',
            'hasil_kelompokumur' => 'Hasil Kelompokumur',
            'hasil_jeniskelamin' => 'Hasil Jeniskelamin',
            'status_periksahasil' => 'Status Periksahasil',
            'catatan_labklinik' => 'Catatan Labklinik',
            'printhasillab' => 'Printhasillab',
            'kesimpulan' => 'Kesimpulan',
            'dokterpj_luarrs' => 'Dokterpj Luarrs',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetailhasilpemeriksaanlabTs()
    {
        return $this->hasMany(DetailhasilpemeriksaanlabT::className(), ['hasilpemeriksaanlab_id' => 'hasilpemeriksaanlab_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(PasienM::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisi()
    {
        return $this->hasOne(PasienadmisiT::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjang()
    {
        return $this->hasOne(PasienmasukpenunjangT::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(PendaftaranT::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }
    /**
     * This function will return all of result lab
     * 
     * @param String $pendaftaran_id
     * @param Array $paginationOption
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function resultByRegistration($pendaftaran_id, $paginationOption = [])
    {
        // get registration
        $registrationRecord = InfoKunjunganRajal::find()->select(['pendaftaran_id', 'umur'])->andWhere(compact('pendaftaran_id'))->asArray()->one();
        if (empty($registrationRecord)) {
            return [
                'message' => 'Data pendaftaran tidak ditemukan',
                'status' => 400
            ];
        }
        $additionalResponse = [];
        $data = [];
        $subQuery = HasilPemeriksaanLabDetail::find()->select(['hasilpemeriksaanlabdetail_id'])->where('hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id=hasilpemeriksaanlab_t.hasilpemeriksaanlab_id')->limit(1);
        $query = self::find()
            ->select(['hasilpemeriksaanlab_t.hasilpemeriksaanlab_id', 'hasilpemeriksaanlab_t.pendaftaran_id', 'hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab', 'hasilpemeriksaanlab_t.pasienmasukpenunjang_id', 'hasilpemeriksaanlab_t.samplelab_id', 'hasilpemeriksaanlab_t.nohasilperiksalab', 'hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab'])
            ->join('join', 'pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id=hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND pasienmasukpenunjang_t.tanggal_verifikasi IS NOT NULL')
            ->andWhere([
                'hasilpemeriksaanlab_t.pendaftaran_id' => $pendaftaran_id
            ])
            ->andWhere(['exists', $subQuery])
            ->orderBy(['hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab' => SORT_DESC]);
        if (isset($paginationOption['page']) && isset($paginationOption['limit'])) {
            $additionalResponse['total'] = $query->count();
            $query = $query->offset(($paginationOption['page'] - 1) * $paginationOption['limit'])->limit($paginationOption['limit']);
        }
        $resultLab = $query->asArray()
            ->all();
        if (!empty($resultLab)) {
            $pasienMasukPenunjangIds = [];
            foreach ($resultLab as $lab) {
                $data['sample-' . $lab['samplelab_id'] . '--pasienmasukpenunjang-' . $lab['pasienmasukpenunjang_id']] = $lab;
                $data['sample-' . $lab['samplelab_id'] . '--pasienmasukpenunjang-' . $lab['pasienmasukpenunjang_id']]['results'] = [];
                $pasienMasukPenunjangIds[] = $lab['pasienmasukpenunjang_id'];
            }
            $detailResult = NilaiPemeriksaanLabDetailView::find()
                ->select(['samplelab_id', 'pasienmasukpenunjang_id', 'daftartindakan_nama', 'nama_rujukan', 'hasil', 'satuanlab_nama', 'daftartindakan_id'])
                ->andWhere(['in', 'pasienmasukpenunjang_id', $pasienMasukPenunjangIds])
                ->andWhere(['IS NOT', 'hasil', null])
                ->orderBy(['pemeriksaanlab_id' => SORT_ASC, 'daftartindakan_id' => SORT_ASC, 'nilairujukan_id' => SORT_ASC])
                ->asArray()
                ->all();
            foreach ($detailResult as $detail) {
                if (isset($data['sample-' . $detail['samplelab_id'] . '--pasienmasukpenunjang-' . $detail['pasienmasukpenunjang_id']])) {
                    $data['sample-' . $detail['samplelab_id'] . '--pasienmasukpenunjang-' . $detail['pasienmasukpenunjang_id']]['results'][] = $detail;
                }
            }
            $data = array_values($data);
        }
        return array_merge([
            'data' => $data,
            'status' => 200
        ], $additionalResponse);
    }
}
