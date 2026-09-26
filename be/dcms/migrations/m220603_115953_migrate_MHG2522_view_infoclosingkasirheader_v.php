<?php

use yii\db\Migration;

/**
 * Class m220603_115953_migrate_MHG2522_view_infoclosingkasirheader_v
 */
class m220603_115953_migrate_MHG2522_view_infoclosingkasirheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoclosingkasirheader_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoclosingkasirheader_v" AS  SELECT closingkasir_t.closingkasir_id,
                closingkasir_t.tgl_closingkasir,
                closingkasir_t.no_closingkasir,
                closingkasir_t.pegawai_id,
                pegawai_m.nama_pegawai,
                closingkasir_t.shift_id,
                shift_m.shift_nama,
                closingkasir_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                closingkasir_t.total_setoran,
                setorbank_t.setorbank_id,
                setorbank_t.no_struksetor,
                setorbank_t.tgl_disetor,
                setorbank_t.nama_bank,
                setorbank_t.no_rekening,
                setorbank_t.jumlah_setoran,
                closingkasir_t.closing_saldoawal,
                closingkasir_t.terima_uangpelayanan + closingkasir_t.terima_uangmuka AS terima_uangpelayanan
               FROM closingkasir_t
                 JOIN (
                            SELECT
                                pegawai_id,
                                nama_pegawai
                            FROM pegawai_m a
                     ) pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN (
                            SELECT
                                shift_id,
                                shift_nama
                            FROM shift_m a
                     ) shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                 JOIN (
                            SELECT
                                ruangan_id,
                                ruangan_nama,
                                instalasi_id
                            FROM ruangan_m a
                     ) ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN (
                            SELECT 
                                instalasi_id,
                                instalasi_nama
                            FROM instalasi_m a
                     ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN (
                            SELECT
                                setorbank_id,
                                no_struksetor,
                                tgl_disetor,
                                nama_bank,
                                no_rekening,
                                jumlah_setoran
                            FROM setorbank_t a
                     ) setorbank_t ON closingkasir_t.setorbank_id = setorbank_t.setorbank_id
              WHERE closingkasir_t.is_deleted = false;
        ');   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220603_115953_migrate_MHG2522_view_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220603_115953_migrate_MHG2522_view_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }
    */
}
