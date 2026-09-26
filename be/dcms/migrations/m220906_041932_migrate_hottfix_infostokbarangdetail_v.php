<?php

use yii\db\Migration;

/**
 * Class m220906_041932_migrate_hottfix_infostokbarangdetail_v
 */
class m220906_041932_migrate_hottfix_infostokbarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infostokbarangdetail_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.infostokbarangdetail_v AS  SELECT x.barang_id,
    x.stok_sistem,
    x.barang_nama,
    x.kelompokbarang_id,
    x.kelompok_barang,
    x.subkelompokbarang_id,
    x.subkelompok_barang,
    x.barang_harganetto,
    x.instalasi_nama,
    x.ruangan_nama,
    x.periodestokbarang_id,
    x.tglperiodestok_awal,
    x.tglperiodestok_akhir,
    x.ruangan_id,
    x.instalasi_id,
    x.sop_barang_id,
    x.sop_sopbarangdetail_id,
    x.satuankecil_id
   FROM ( SELECT proses.barang_id,
            sum(proses.qtystok_in) - sum(proses.qtystok_out) AS stok_sistem,
            sum(proses.qtystok_in) AS \"in\",
            sum(proses.qtystok_out) AS \"out\",
            proses.barang_nama,
            proses.kelompokbarang_id,
            proses.kelompok_barang,
            proses.subkelompokbarang_id,
            proses.subkelompok_barang,
            proses.barang_harganetto,
            proses.instalasi_nama,
            proses.ruangan_nama,
            proses.periodestokbarang_id,
            proses.tglperiodestok_awal,
            proses.tglperiodestok_akhir,
            proses.ruangan_id,
            proses.instalasi_id,
            proses.sop_barang_id,
            proses.sop_sopbarangdetail_id,
            proses.satuankecil_id
           FROM ( SELECT
                        CASE
                            WHEN stokbarang_t.stokbarangasal_id IS NULL THEN stokbarang_t.stokbarang_id
                            ELSE stokbarang_t.stokbarangasal_id
                        END AS id_stok,
                    stokbarang_t.barang_id,
                    stokbarang_t.qtystok_in,
                    stokbarang_t.qtystok_out,
                    barang_m.barang_nama,
                    barang_m.satuankecil_id,
                    subkelompokbarang_m.subkelompokbarang_id,
                    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
                    kelompokbarang_m.kelompokbarang_id,
                    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
                    barang_m.barang_harganetto,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_nama,
                    stokbarang_r.periodestokbarang_id,
                    periodestokbarang_m.tglperiodestok_awal,
                    periodestokbarang_m.tglperiodestok_akhir,
                    ruangan_m.ruangan_id,
                    instalasi_m.instalasi_id,
                    formsobarangdetail_t.barang_id AS sop_barang_id,
                    formsobarangdetail_t.stokopnamebarangdetail_id AS sop_sopbarangdetail_id
                   FROM stokbarang_t
                     JOIN ( SELECT a.barang_id,
                            a.barang_nama,
                            a.satuankecil_id,
                            a.barang_harganetto,
                            a.kelompokbarang_id,
                            a.subkelompokbarang_id
                           FROM barang_m a
                          WHERE a.is_active = true) barang_m ON stokbarang_t.barang_id = barang_m.barang_id
                     LEFT JOIN ( SELECT a.kelompokbarang_id,
                            a.kelompokbarang_nama
                           FROM kelompokbarang_m a) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                     LEFT JOIN ( SELECT a.subkelompokbarang_id,
                            a.subkelompok_nama
                           FROM subkelompokbarang_m a) subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON stokbarang_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.formsobarangdetail_id,
                            a.barang_id,
                            a.ruangan_id,
                            a.stokopnamebarangdetail_id
                           FROM formsobarangdetail_t a
                             JOIN ( SELECT a_1.formsobarang_id,
                                    a_1.is_deleted
                                   FROM formsobarang_t a_1) formsobarang_t ON a.formsobarang_id = formsobarang_t.formsobarang_id AND formsobarang_t.is_deleted = false
                          WHERE a.stokopnamebarangdetail_id IS NULL AND a.is_deleted = false) formsobarangdetail_t ON formsobarangdetail_t.barang_id = stokbarang_t.barang_id AND stokbarang_t.ruangan_id = formsobarangdetail_t.ruangan_id
                     JOIN ( SELECT a.barang_id,
                            a.ruangan_id,
                            a.is_periode,
                            a.stokbarangr_id,
                            a.periodestokbarang_id
                           FROM stokbarang_r a) stokbarang_r ON stokbarang_t.barang_id = stokbarang_r.barang_id AND stokbarang_t.ruangan_id = stokbarang_r.ruangan_id
                     LEFT JOIN ( SELECT a.periodestokbarang_id,
                            a.tglperiodestok_awal,
                            a.tglperiodestok_akhir
                           FROM periodestokbarang_m a) periodestokbarang_m ON stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id
                  WHERE formsobarangdetail_t.formsobarangdetail_id IS NULL) proses
          GROUP BY proses.barang_id, proses.barang_nama, proses.kelompokbarang_id, proses.kelompok_barang, proses.subkelompokbarang_id, proses.subkelompok_barang, proses.barang_harganetto, proses.instalasi_nama, proses.ruangan_nama, proses.periodestokbarang_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_barang_id, proses.sop_sopbarangdetail_id, proses.satuankecil_id
          ORDER BY proses.barang_nama) x
     LEFT JOIN ( SELECT stokbarang_t.barang_id,
            stokbarang_t.ruangan_id,
            count(stokbarang_t.barang_id) AS total_transaksi
           FROM stokbarang_t
          WHERE stokbarang_t.is_deleted = false AND stokbarang_t.created_date >= (CURRENT_DATE - '6 mons'::interval)
          GROUP BY stokbarang_t.barang_id, stokbarang_t.ruangan_id) riwayat_barang ON x.ruangan_id = riwayat_barang.ruangan_id AND x.barang_id = riwayat_barang.barang_id
  WHERE x.stok_sistem > 0::double precision OR riwayat_barang.total_transaksi <> 0
		 ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220906_041932_migrate_hottfix_infostokbarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220906_041932_migrate_hottfix_infostokbarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
