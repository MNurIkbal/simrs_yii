<?php

use yii\db\Migration;

/**
 * Class m210303_093936_improvment_view_permintaanmakan_add_rj
 */
class m210303_093936_improvment_view_permintaanmakan_add_rj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
            ALTER TABLE permintaanmakan_t ADD IF NOT EXISTS catatan_diet TEXT;
        ');

    	$this->execute('
            DROP VIEW IF EXISTS "public"."infopermintaanmakan_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopermintaanmakan_v" AS  SELECT permintaanmakan_t.permintaaanmakan_id,
			    permintaanmakan_t.pendaftaran_id,
			    pendaftaran_t.no_pendaftaran,
			    pendaftaran_t.tgl_pendaftaran,
			    pasien_m.no_rekam_medik, 
			    pasien_m.nama_pasien,
			    lookup_m.lookup_name AS jenis_kelamin,
			    lookup_m.lookup_value AS jenis_kelamin_value,
			    pasien_m.tanggal_lahir,
			    pendaftaran_t.umur,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    permintaanmakan_t.no_permintaanmakan,
			    pegawai_m.nama_pegawai,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    permintaanmakan_t.tgl_permintaanmakan,
			    permintaanmakan_t.status AS status_permintaanmakan,
			        CASE
			            WHEN (permintaanmakan_t.status = 1) THEN \'PROSES\'::text
			            ELSE \'BATAL\'::text
			        END AS status_permintaan,
			    permintaanmakan_t.no_pembatalan,
			    permintaanmakan_t.waktu_pembatalan,
			    permintaanmakan_t.alasan_pembatalan,
			    ruangan_m.ruangan_nama,
			    kamarruangan_m.kamarruangan_nokamar,
			    ruangan_m.ruangan_id,
			    kamarruangan_m.kamarruangan_id,
			    carabayar_m.carabayar_id,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    dok_dpjp.nama_pegawai AS dok_dpjp,
			    kamartempattidur_m.no_tempattidur,
			    pemesan.nama_pegawai AS nama_pemesan,
			    permintaanmakan_t.catatan_diet
			   FROM ((((((((((((((permintaanmakan_t
			     JOIN pendaftaran_t ON ((permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
			     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     JOIN pegawai_m ON ((permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id)))
			     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN pegawai_m dok_dpjp ON ((pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id)))
			     JOIN pegawai_m pemesan ON ((permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id)))
			     LEFT JOIN lookup_m ON (((pasien_m.jeniskelamin)::integer = lookup_m.lookup_id)))
			UNION ALL
			 SELECT permintaanmakan_t.permintaaanmakan_id,
			    permintaanmakan_t.pendaftaran_id,
			    pendaftaran_t.no_pendaftaran,
			    pendaftaran_t.tgl_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    lookup_m.lookup_name AS jenis_kelamin,
			    lookup_m.lookup_value AS jenis_kelamin_value,
			    pasien_m.tanggal_lahir,
			    pendaftaran_t.umur,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    permintaanmakan_t.no_permintaanmakan,
			    pegawai_m.nama_pegawai,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    permintaanmakan_t.tgl_permintaanmakan,
			    permintaanmakan_t.status AS status_permintaanmakan,
			        CASE
			            WHEN (permintaanmakan_t.status = 1) THEN \'PROSES\'::text
			            ELSE \'BATAL\'::text
			        END AS status_permintaan,
			    permintaanmakan_t.no_pembatalan,
			    permintaanmakan_t.waktu_pembatalan,
			    permintaanmakan_t.alasan_pembatalan,
			    ruangan_m.ruangan_nama,
			    ruangan_m.ruangan_nama AS kamarruangan_nokamar,
			    ruangan_m.ruangan_id,
			    ruangan_m.ruangan_id AS kamarruangan_id,
			    carabayar_m.carabayar_id,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    dok_dpjp.nama_pegawai AS dok_dpjp,
			    \'\'::character varying AS no_tempattidur,
			    pemesan.nama_pegawai AS nama_pemesan,
			    permintaanmakan_t.catatan_diet
			   FROM (((((((((((permintaanmakan_t
			     JOIN pendaftaran_t ON ((permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     JOIN pegawai_m ON ((permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN pegawai_m dok_dpjp ON ((pendaftaran_t.pegawai_id = dok_dpjp.pegawai_id)))
			     JOIN pegawai_m pemesan ON ((permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id)))
			     LEFT JOIN lookup_m ON (((pasien_m.jeniskelamin)::integer = lookup_m.lookup_id)))
			  WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (pendaftaran_t.pasienadmisi_id IS NULL));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210303_093936_improvment_view_permintaanmakan_add_rj cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210303_093936_improvment_view_permintaanmakan_add_rj cannot be reverted.\n";

        return false;
    }
    */
}
