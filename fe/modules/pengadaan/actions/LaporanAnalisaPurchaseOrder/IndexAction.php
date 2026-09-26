<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;

class IndexAction extends Action {
    public function run() {
        $module = $this->controller->_module;
        $columns = $this->getColumns();
        $dropdownStatus = $this->dropdownStatus();
        $titleMenu = DHtml::getTitleMenu();
        $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title;
        return $this->controller->render('index', get_defined_vars());
    }

    public function getColumns()
    {
        return [
            Yii::t('fe', 'No'),
            Yii::t("fe", "Kode Obat"),
            Yii::t("fe", "Nama Obat"),
            Yii::t("fe", "Manufaktur"),
            Yii::t("fe", "Jenis Obat"),
            Yii::t("fe", "Nomor PR"),
            Yii::t("fe", "Tanggal PR"),
            Yii::t("fe", "Tanggal Approve PR"),
            Yii::t("fe", "Qty PR"),
            Yii::t("fe", "UoM PR"),
            Yii::t("fe", "Catatan"),
            Yii::t("fe", "Nomor PO"),
            Yii::t("fe", "Cito "),
            Yii::t("fe", "Admin"),
            Yii::t("fe", "Consignment"),
            Yii::t("fe", "Tanggal PO"),
            Yii::t("fe", "Tanggal Validasi PO"),
            Yii::t("fe", "Qty PO"),
            Yii::t("fe", "UoM PO"),
            Yii::t("fe", "Harga (Rp.)"),
            Yii::t("fe", "Diskon (%)"),
            Yii::t("fe", "PPn (%)"),
            Yii::t("fe", "Sub Total (Rp.)"),
            Yii::t("fe", "Total Harga (Rp.)"),
            Yii::t("fe", "Status PO"),
            Yii::t("fe", "Tanggal Batal PO"),
            Yii::t("fe", "Catatan Batal PO"),
            Yii::t("fe", "Kode Supplier"),
            Yii::t("fe", "Nama Supplier"),
            Yii::t("fe", "Tanggal Penerimaan"),
            Yii::t("fe", "Qty Penerimaan"),
            Yii::t("fe", "UoM Penerimaan"),
            Yii::t("fe", "Sisa Penerimaan"),
            Yii::t("fe", "UoM Sisa Penerimaan"),
            Yii::t("fe", "PR diapprove ke PO dibuat"),
            Yii::t("fe", "PR diapprove ke PO divalidasi"),
            Yii::t("fe", "PO di buat ke PO di validasi"),
            Yii::t("fe", "PR diapprove ke penerimaan"),
            Yii::t("fe", "PO di validasi ke Penerimaan"),
        ];
    }

    public function dropdownStatus()
    {
        return [
            [
                "id" => "Expired", 
                "text" => "Expired"
            ],
            [
                "id" => "Dibatalkan", 
                "text" => "Dibatalkan"
            ],
            [
                "id" => "Sudah Semua Diterima", 
                "text" => "Sudah Semua Diterima"
            ],
            [
                "id" => "Belum Diterima", 
                "text" => "Belum Diterima"
            ],
            [
                "id" => "Belum Semua Diterima", 
                "text" => "Belum Semua Diterima"
            ]
        ];
    }
}
