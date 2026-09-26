<?php

use yii\db\Migration;

/**
 * Class m220124_063144_migrate_detailreturresep_v
 */
class m220124_063144_migrate_detailreturresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.detailreturresep_v;');

        $this->execute("
            CREATE VIEW \"public\".\"detailreturresep_v\" AS  SELECT returresepdetail_t.returresepdetail_id,
    returresep_t.returresep_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    penjualanresep_t.carabayar_id,
    penjualanresep_t.penjamin_id,
    returresep_t.tgl_retur,
    returresep_t.no_returresep,
    penjualanresep_t.noresep,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    returresepdetail_t.qty_retur,
    returresepdetail_t.hargasatuan,
    returresepdetail_t.qty_retur * returresepdetail_t.hargasatuan AS total,
    obatalkespasien_t.qty_oa
   FROM returresepdetail_t
     JOIN returresep_t ON returresepdetail_t.returresep_id = returresep_t.returresep_id
     JOIN penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     JOIN obatalkespasien_t ON returresepdetail_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     LEFT JOIN stokobatalkes_t ON returresepdetail_t.returresepdetail_id = stokobatalkes_t.returresepdetail_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
  WHERE returresep_t.is_active = true AND returresep_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220124_063144_migrate_detailreturresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_063144_migrate_detailreturresep_v cannot be reverted.\n";

        return false;
    }
    */
}
