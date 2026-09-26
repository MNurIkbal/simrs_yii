<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;
use Doco\models\ExcelColumn;
use Doco\components\DocoPostgreFunctionAR;

class LaporanResponTimeAnalisisFn extends DocoPostgreFunctionAR {
    /**
     * @inheritdoc
     */
    public static function functionName() {
        return 'laporanrespontimeanalis_fn';
    }

    public static function getData($tipe, $start_date, $end_date){
        $start_date = !empty($start_date) ? date('Y-m-d',strtotime($start_date)) : date('Y-m-d');
        $end_date = !empty($end_date) ? date('Y-m-d',strtotime($end_date)) : date('Y-m-d');
        return (new LaporanResponTimeAnalisisFn([
                    'extParam' => [
                        $tipe,
                        $start_date,
                        $end_date
                    ]
                ]))->find();
    }

    public function attributeLabels() {
        return
        [
            'item_code' => 'Item Code',
            'item_name' => 'Item Name',
            'manufacturer' => 'Manufacturer',
            'category' => 'Category',
            'pr_no' => 'PR No.',
            'create_date_pr' => 'PR Create Date',
            'approval_date_pr' => 'PR Approval Date',
            'qty_pr' => 'Qty PR',
            'uom_pr' => 'UoM PR',
            'remarks' => 'Remarks',
            'po_no' => 'PR No.',
            'create_date_po' => 'PO Create Date',
            'approval_date_po' => 'PO Approval Date',
            'qty_po' => 'Qty PO',
            'uom_po' => 'UoM PO',
            'price' => 'Price',
            'ded_persen' => 'Ded (%)',
            'add_persen' => 'Add (%)',
            'gross_amount' => 'Gross Amount',
            'nett_amount' => 'Nett Amount',
            'status_po' => 'Status PO',
            'reject_date' => 'Reject Date',
            'reject_remarks' => 'Reject Remarks',
            'supplier_code' => 'Supplier Code',
            'supplier' => 'Supplier',
            'do_no' => 'DO No.',
            'receive_date' => 'Qty Date',
            'receive_qty' => 'Qty Receive',
            'uom_terima' => 'UoM Receive',
            'outstanding_qty' => 'Outstanding Qty',
            'uom_grn' => 'UoM GRN',
            'grn_no' => 'GRN No.',
            'grn_date' => 'GRN Date',
            'pr_created_to_po_created' => 'PR Created To PO Created',
            'po_created_to_do_received' => 'PO Created To DO Received',
            'pr_created_to_do_received' => 'PR Created To DO Received',
            'pr_approved_to_po_created' => 'PR Approved To PO Created',
            'pr_approved_to_po_approved' => 'PR Approved To PO Approved',
            'po_created_to_po_approved' => 'PO Created To PO Approved',
            'po_approved_to_do_received' => 'PO Approved To DO Received',
            'pr_approved_to_do_received' => 'PR Approved To DO Received',
            'additional_remarks' => 'Additional Remarks',
            'remarks_date' => 'Remarks Date'
        ];
    }

    public function excelColumns() {
        return [
            ['name' => 'item_code'],
            ['name' => 'item_name'],
            ['name' => 'manufacturer'],
            ['name' => 'category'],
            ['name' => 'pr_no'],
            ['name' => 'create_date_pr'],
            ['name' => 'approval_date_pr'],
            ['name' => 'qty_pr'],
            ['name' => 'uom_pr'],
            ['name' => 'remarks'],
            ['name' => 'po_no'],
            ['name' => 'create_date_po'],
            ['name' => 'approval_date_po'],
            ['name' => 'qty_po'],
            ['name' => 'uom_po'],
            ['name' => 'price'],
            ['name' => 'ded_persen'],
            ['name' => 'add_persen'],
            ['name' => 'gross_amount'],
            ['name' => 'nett_amount'],
            ['name' => 'status_po'],
            ['name' => 'reject_date'],
            ['name' => 'reject_remarks'],
            ['name' => 'supplier_code'],
            ['name' => 'supplier'],
            ['name' => 'do_no'],
            ['name' => 'receive_date'],
            ['name' => 'receive_qty'],
            ['name' => 'uom_terima'],
            ['name' => 'outstanding_qty'],
            ['name' => 'uom_grn'],
            ['name' => 'grn_no'],
            ['name' => 'grn_date'],
            ['name' => 'pr_created_to_po_created'],
            ['name' => 'po_created_to_do_received'],
            ['name' => 'pr_created_to_do_received'],
            ['name' => 'pr_approved_to_po_created'],
            ['name' => 'pr_approved_to_po_approved'],
            ['name' => 'po_created_to_po_approved'],
            ['name' => 'po_approved_to_do_received'],
            ['name' => 'pr_approved_to_do_received'],
            ['name' => 'additional_remarks'],
            ['name' => 'remarks_date']
        ];
    }

    public function toExcel($data) {
        $cols = $this->excelColumns();
        $labels = $this->attributeLabels();
        $result = [];

        foreach($data->asArray()->all() as $key => $value)
        {
            $newValue = [];

            foreach($cols as $col)
            {
                $name = $col['name'];
                $type = isset($col['type']) ? $col['type'] : '-';
                $rowData = isset($value[$name]) ? $value[$name] : '-';

                if($type == 'date' && $rowData != '-') {
                    $rowData = date('d M Y', strtotime($rowData));
                }


                $newValue[\Yii::t('app', $labels[$name])] = $rowData;
            }
            $result[$key] = $newValue;
        }
        return $result;
    }
}
