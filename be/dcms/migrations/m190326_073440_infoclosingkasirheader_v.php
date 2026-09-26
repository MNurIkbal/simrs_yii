<?php

use yii\db\Migration;

/**
 * Class m190326_073440_infoclosingkasirheader_v
 */
class m190326_073440_infoclosingkasirheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE VIEW infoclosingkasirheader_v AS 
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
                closingkasir_t.total_setoran
               FROM closingkasir_t
                 JOIN pegawai_m ON closingkasir_t.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN shift_m ON closingkasir_t.shift_id = shift_m.shift_id
                 JOIN ruangan_m ON closingkasir_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
              WHERE closingkasir_t.is_deleted = false;
        ");
        $this->execute("
            ALTER TABLE infoclosingkasirheader_v
              OWNER TO postgres;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_073440_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_073440_infoclosingkasirheader_v cannot be reverted.\n";

        return false;
    }
    */
}
