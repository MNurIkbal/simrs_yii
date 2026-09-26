<?php
// InfoPasienRiView
namespace app\modules\v1\models;

use yii\helpers\ArrayHelper;

class HasilPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanlab_t';
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
        $registrationRecord = InfoPasienRiView::find()->select(['pendaftaran_id', 'umur', 'pasienadmisi_id', 'prev_pendaftaran_id'])->andWhere(compact('pendaftaran_id'))->asArray()->one();
        if (empty($registrationRecord)) {
            return [
                'message' => 'Data pendaftaran tidak ditemukan',
                'status' => 400
            ];
        }
        $lookPrevRegId = ArrayHelper::getValue($registrationRecord, 'prev_pendaftaran_id');
        $listRegId[] = $pendaftaran_id;
        if(!empty($lookPrevRegId)) {
            array_push($listRegId, $lookPrevRegId);
        }
        $additionalResponse = [];
        $data = [];
        $subQuery = HasilPemeriksaanLabDetail::find()->select(['hasilpemeriksaanlabdetail_id'])->where('hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id=hasilpemeriksaanlab_t.hasilpemeriksaanlab_id')->limit(1);
        $query = self::find()
            ->select(['hasilpemeriksaanlab_t.hasilpemeriksaanlab_id', 'hasilpemeriksaanlab_t.pendaftaran_id', 'hasilpemeriksaanlab_t.pasienadmisi_id', 'hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab', 'hasilpemeriksaanlab_t.pasienmasukpenunjang_id', 'hasilpemeriksaanlab_t.samplelab_id', 'hasilpemeriksaanlab_t.nohasilperiksalab', 'hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab'])
            ->join('join', 'pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id=hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND pasienmasukpenunjang_t.tanggal_verifikasi IS NOT NULL')
            // ->andWhere([
            //     'hasilpemeriksaanlab_t.pendaftaran_id' => $pendaftaran_id
            // ])
            ->andWhere(['IN', 'hasilpemeriksaanlab_t.pendaftaran_id', $listRegId])
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
