<?php

use yii\db\Migration;

/**
 * Class m231026_143857_migrate_returresep_per_pendaftaran_improve_pasien_detailreturresep_v
 */
class m231026_143857_migrate_returresep_per_pendaftaran_improve_pasien_detailreturresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.detailreturresep_v");

        $this->execute("
        CREATE OR REPLACE VIEW public.detailreturresep_v
        AS  SELECT returdetail.returresepdetail_id,
    retur.returresep_id,
    pasien.pasien_id,
    oapasien.obatalkespasien_id,
    retur.no_returresep,
    retur.tgl_retur,
    resep.noresep,
    resep.tglpenjualan AS tglresep,
    pasien.nama_pasien,
    obat.obatalkes_nama,
    returdetail.qty_retur,
    stok.tglkadaluarsa,
    returdetail.hargasatuan,
    returdetail.qty_retur * returdetail.hargasatuan AS total,
    oapasien.qty_oa,
    jenispenjualan.lookup_name AS jenis_penjualan,
    pendaftaran.no_pendaftaran,
        CASE
            WHEN retur.penjualanresep_id IS NOT NULL THEN false
            ELSE true
        END AS is_retur_pendaftaran,
    COALESCE(resep_detail.noresep, 'BMHP'::character varying) AS noresep_detail
   FROM returresepdetail_t returdetail
     LEFT JOIN returresep_t retur ON retur.returresep_id = returdetail.returresep_id
     LEFT JOIN obatalkespasien_t oapasien ON oapasien.obatalkespasien_id = returdetail.obatalkespasien_id
     LEFT JOIN obatalkes_m obat ON obat.obatalkes_id = oapasien.obatalkes_id
     LEFT JOIN penjualanresep_t resep ON resep.penjualanresep_id = retur.penjualanresep_id
     LEFT JOIN penjualanresep_t resep_detail ON resep_detail.penjualanresep_id =
        CASE
            WHEN retur.penjualanresep_id IS NOT NULL THEN retur.penjualanresep_id
            ELSE oapasien.penjualanresep_id
        END
     LEFT JOIN pendaftaran_t pendaftaran ON pendaftaran.pendaftaran_id =
        CASE
            WHEN retur.penjualanresep_id IS NOT NULL THEN resep.pendaftaran_id
            ELSE retur.pendaftaran_id
        END
     LEFT JOIN pasien_m pasien ON pasien.pasien_id = pendaftaran.pasien_id
     LEFT JOIN stokobatalkes_t stok ON stok.returresepdetail_id = returdetail.returresepdetail_id
     LEFT JOIN lookup_m jenispenjualan ON jenispenjualan.lookup_id = resep.jenispenjualan::integer
  WHERE retur.is_active = true AND retur.is_deleted = false AND retur.status_retur = 2119 ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_143857_migrate_returresep_per_pendaftaran_improve_pasien_detailreturresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_143857_migrate_returresep_per_pendaftaran_improve_pasien_detailreturresep_v cannot be reverted.\n";

        return false;
    }
    */
}
