<?php

use yii\db\Migration;

/**
 * Class m190326_080315_infoclosingkasirheader_v
 */
class m190326_080315_infoclosingkasirheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
             CREATE OR REPLACE VIEW "public"."infoclosingkasirheader_v" AS  
                SELECT closingkasir_t.closingkasir_id,
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
                setorbank_t.jumlah_setoran
               FROM (((((closingkasir_t
                 JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
                 JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
              WHERE (closingkasir_t.is_deleted = false);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_080315_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_080315_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }
    */
}
