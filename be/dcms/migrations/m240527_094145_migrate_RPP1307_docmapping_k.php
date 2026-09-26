<?php

use yii\db\Migration;

/**
 * Class m240527_094145_migrate_RPP1307_docmapping_k
 */
class m240527_094145_migrate_RPP1307_docmapping_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM docmapping_k WHERE kode_doc = 'penerimaan-barang-manual';");
        $this->execute('INSERT INTO public.docmapping_k
        (docmapping_id, docheader_id, docbody_text, docfooter_id, controller, doc_key, nama_doc, kode_doc, kertas_id, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_active, deleted_date, deleted_by, is_deleted, additional_style)
        VALUES(375, 51, \'<p style="text-align:center"><strong>Transaksi Penerimaan Barang Manual</strong></p>
        
        <p style="text-align:center"><strong>Gudang Barang</strong></p>
        
        <hr />
        <table align="left" border="0" cellpadding="1" cellspacing="1" id="informasi-pasien" style="width:100%">
            <tbody>
                <tr>
                    <td style="width:30%">Tanggal Penerimaan</td>
                    <td style="width:5%">:</td>
                    <td>#tgl_penerimaan#</td>
                </tr>
                <tr>
                    <td style="width:30%">Nama Supplier</td>
                    <td style="width:5%">:</td>
                    <td>#nama_supplier#</td>
                </tr>
                <tr>
                    <td style="width:30%">No. Faktur&nbsp;</td>
                    <td style="width:5%">:</td>
                    <td>#nomor_faktur#&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:30%">Nomor Penerimaan</td>
                    <td style="width:5%">:</td>
                    <td>#no_penerimaan#</td>
                </tr>
                <tr>
                    <td style="width:30%">Nomor Surat Jalan&nbsp;</td>
                    <td style="width:5%">:</td>
                    <td>#no_suratjalan#</td>
                </tr>
                <tr>
                    <td style="width:30%">Tarif Pajak</td>
                    <td style="width:10%">:</td>
                    <td>#pajak_label#</td>
                </tr>
                <tr>
                    <td style="width:30%">Payment Term</td>
                    <td style="width:5%">:</td>
                    <td>#payterm_nama#</td>
                </tr>
            </tbody>
        </table>
        
        <p><br />
        <br />
        &nbsp;</p>
        
        <p>#datatable#</p>
        
        <p>&nbsp;</p>
        
        <table cellpadding="1" cellspacing="1" style="width:100%">
            <tbody>
                <tr>
                    <td style="text-align:center; vertical-align:middle; width:40%">Pegawai Mengetahui</td>
                    <td style="text-align:center; vertical-align:middle; width:20%">&nbsp;</td>
                    <td style="text-align:center; vertical-align:middle; width:40%">Pegawai Menyetujui</td>
                </tr>
                <tr>
                    <td style="height:80px; text-align:center; vertical-align:middle; width:40%">&nbsp;</td>
                    <td style="height:80px; text-align:center; vertical-align:middle; width:20%">&nbsp;</td>
                    <td style="height:80px; text-align:center; vertical-align:middle; width:40%">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center; vertical-align:middle; width:40%">#pegawai_mengetahui#</td>
                    <td style="text-align:center; vertical-align:middle; width:20%">&nbsp;</td>
                    <td style="text-align:center; vertical-align:middle; width:40%">#pegawai_menyetujui#</td>
                </tr>
            </tbody>
        </table>
        \', 11, NULL, \'gudang-PenerimaanBarangManualController-actionExportPdf\', \'Penerimaan barang manual\', \'penerimaan-barang-manual\', 19, NULL, \'2020-06-13 17:50:47.000\', NULL, 10, \'2024-05-27 10:49:38.000\', 1, true, NULL, NULL, false, NULL);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240527_094145_migrate_RPP1307_docmapping_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240527_094145_migrate_RPP1307_docmapping_k cannot be reverted.\n";

        return false;
    }
    */
}
