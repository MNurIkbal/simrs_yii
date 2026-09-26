<?php

use yii\db\Migration;

/**
 * Class m210126_131116_migrate_20200126_infotagihanobatdetail_v
 */
class m210126_131116_migrate_20200126_infotagihanobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infotagihanobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotagihanobatdetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.penjualanresep_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan::integer AS tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto::integer AS tarif_cyto,
    tagihan.sub_total::integer AS sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.discount
   FROM ( SELECT penjualanresep_t.penjualanresep_id,
            obatalkespasien_t.pendaftaran_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.det
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            penjualanresep_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            penjualanresep_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.tarif_diskon AS discount
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id
  WHERE tagihan.tindakansudahbayar_id IS NULL AND tagihan.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."infotagihanobatdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_131116_migrate_20200126_infotagihanobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_131116_migrate_20200126_infotagihanobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
