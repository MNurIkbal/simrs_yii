<?php

use yii\db\Migration;

/**
 * Class m210307_060718_migrate_20200703_invoiceri_v
 */
class m210307_060718_migrate_20200703_invoiceri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."invoiceri_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoiceri_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien AS alamat,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    penjamin_m.penjamin_nama AS penjamin,
    pasienadmisi_t.tgl_admisi,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    ruangan_m.ruangan_nama AS ruangan,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur AS bed,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    pembayaranmetode_t.metode_bayar,
    jenisnontunai_m.nama AS jenis_nontunai,
    bank_m.nama_bank AS bank,
    pendaftaran_t.tgl_stopakomodasi,
    pembayaran_t.total_dijamin
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
     LEFT JOIN jenisnontunai_m ON pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
     LEFT JOIN bank_m ON jenisnontunai_m.bank_id = bank_m.bank_id;");

        $this->execute('ALTER TABLE "public"."invoiceri_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210307_060718_migrate_20200703_invoiceri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210307_060718_migrate_20200703_invoiceri_v cannot be reverted.\n";

        return false;
    }
    */
}
