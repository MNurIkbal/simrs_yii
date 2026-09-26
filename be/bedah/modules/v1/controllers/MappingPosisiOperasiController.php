<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\MappingPosisiOperasi;
use app\modules\v1\models\MappingPosisiOperasiView;
use Yii;
use Doco\components\DocoActiveController;
use Doco\Libraries\DocoDatatable;

class MappingPosisiOperasiController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * retrieve datatable
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $lookupData = $this->lookupIn(['tim_operasi']);
        return isset($lookupData['tim_operasi']) ? $lookupData['tim_operasi'] : [];
    }

    /**
     * API to create new mapping
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionCreate()
    {
        return $this->createOrUpdate();
    }

    /**
     * API to update mapping
     * 
     * @param String $daftartindakan_id
     * @param String $timoperasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdate($daftartindakan_id, $timoperasi_id)
    {
        return $this->createOrUpdate($daftartindakan_id, $timoperasi_id);
    }

    /**
     * Method to create or update mapping
     * 
     * @param String $daftartindakan_id
     * @param String $timoperasi_id
     * @return Array/Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function createOrUpdate($daftartindakan_id = null, $timoperasi_id = null)
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'daftartindakan_id' => 'required',
                'timoperasi_id' => 'required',
                'prosentase' => 'required',
                'is_active' => 'safe',
            ]
        ]);
        if (isset($payload['errors'])) {
            $response = $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            $resultProcess = MappingPosisiOperasi::createOrUpdate($payload, $daftartindakan_id, $timoperasi_id);
            if ($resultProcess['result']) {
                $response = $this->responseJson(200, 'Pertanyaan berhasil disimpan');
            } else {
                $response = $this->responseJson(400, isset($resultProcess['message']) ? $resultProcess['message'] : 'Proses simpan pertanyaan gagal');
            }
        }
        return $response;
    }

    /**
     * API to update status active
     * 
     * @param String $daftartindakan_id
     * @param String $timoperasi_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateStatus($daftartindakan_id, $timoperasi_id)
    {
        $record = MappingPosisiOperasi::find(true)
            ->andWhere(compact('daftartindakan_id', 'timoperasi_id'))
            ->one();
        if (empty($record)) {
            return $this->responseJson(400, 'Data tidak ditemukan');
        } else {
            $record->is_active = !$record->is_active;
            $record->save();
            return $this->responseJson(200, 'Status berhasil diperbarui');
        }
    }

    /**
     * retrieve datatable
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDatatable()
    {
        $query = MappingPosisiOperasiView::find()
            ->select([
                'timoperasi_id',
                'timoperasi_nama',
                'daftartindakan_id',
                'daftartindakan_nama',
                'persentase',
                'is_active',
                'created_date'
            ])
            ->orderBy(['created_date' => SORT_DESC]);
        return (new DocoDatatable($query))
            ->setAdditionalColumn([
                'action' => function ($record) {
                    return '<input type="checkbox" data-on-color="success" data-off-color="danger" data-size="mini" data-on-text="Aktif" data-off-text="Tidak Aktif" class="status-box" ' . ($record['is_active'] ? 'checked' : '') . ' data-url="/bedah/mapping-posisi-operasi/update-status?daftartindakan_id=' . $record['daftartindakan_id'] . '&timoperasi_id=' . $record['timoperasi_id'] . '">';
                }
            ])
            ->make();
    }
}
