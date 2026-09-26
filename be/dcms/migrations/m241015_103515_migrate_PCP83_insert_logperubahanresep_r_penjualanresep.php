<?php

use yii\db\Migration;

/**
 * Class m241015_103515_migrate_PCP83_insert_logperubahanresep_r_penjualanresep
 */
class m241015_103515_migrate_PCP83_insert_logperubahanresep_r_penjualanresep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.insert_logperubahanresep_r_penjualanresep()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$
	declare
		var_reseptur_id integer;
		var_obatalkespasien_id integer;
		var_created_date_penjualan timestamp;
		var_jenisperubahan varchar;
		var_tanggalperubahan timestamp;
		var_obatalkesnama varchar;
		var_perubahansebelum varchar;
	    var_perubahansetelah varchar;
	    var_pegawai_id integer;
	    var_pegawainama varchar;
	   	var_signanama varchar;
	    var_satuanunitnama varchar;
		var_det float;
		var_returresepdetail_id integer;
	begin
		
		var_returresepdetail_id := null;
		select returresepdetail_id into var_returresepdetail_id from returresepdetail_t rt where obatalkespasien_id = new.obatalkespasien_id;
		
		if(var_returresepdetail_id is not null) 
		then
			return new;
		end if;
		
		var_pegawai_id := 1;
		var_pegawainama := 'Superadmin';
		-- THIS FN() HANDLING PENJUALAN RESEP!!!

		-- GENERAL FLOW
		-- 1. Check incoming transaction (new, update or delete).
		-- 2. Preparation.
		-- 3. Insert process.
		
		select reseptur_id into var_reseptur_id from penjualanresep_t where penjualanresep_id = new.penjualanresep_id;
		select obatalkespasien_id into var_obatalkespasien_id from obatalkespasien_t where obatalkespasien_id = new.obatalkespasien_id;
		select tglresep into var_created_date_penjualan from penjualanresep_t where penjualanresep_id = new.penjualanresep_id;
		select obatalkes_nama into var_obatalkesnama from obatalkes_m where obatalkes_id = new.obatalkes_id;
		select signa_nama into var_signanama from signaobat_m where signa_id = new.signa_oa::integer;
		select satuanunit_nama into var_satuanunitnama from satuanunit_m where satuanunit_id = new.satuankecil_id;
	
		if(var_signanama is null)
			then
				var_signanama := new.signa->>'text';
		end if;
	
		var_det := new.qty_konversi;
		if(new.det is not null) then var_det := new.det_konversi; end if;

		var_perubahansebelum := var_det || ' ' || var_satuanunitnama || ' | ' || var_signanama;
		var_perubahansetelah := var_det || ' ' || var_satuanunitnama || ' | ' || var_signanama;
	
		if(new.is_deleted = true)
			then 
				var_jenisperubahan := 'HAPUS';
				var_tanggalperubahan := new.last_modified_date;
				var_pegawai_id := new.last_modified_by;
		elseif(new.last_modified_by is not null)
			then 
				with obatalkespasien as (
					select * from obatalkespasien_t where obatalkespasien_id = var_obatalkespasien_id
				)
				select
					concat(
						coalesce(obatalkespasien.det_konversi, obatalkespasien.qty_oa),
						' ', satuanunit_m.satuanunit_nama,
						' | ', 
						coalesce(signaobat_m.signa_nama, signa->>'text') 
					) as perubahan_sebelum
				into var_perubahansebelum
				from obatalkespasien
				left join signaobat_m on obatalkespasien.signa_oa::integer = signaobat_m.signa_id
				left join satuanunit_m on obatalkespasien.satuankecil_id = satuanunit_m.satuanunit_id;

				var_jenisperubahan := 'PEMBAHARUAN';
				var_tanggalperubahan := new.last_modified_date;
				var_pegawai_id := new.last_modified_by;
		elseif(
			new.tglpelayanan > (var_created_date_penjualan + interval '30 Seconds')
			and new.last_modified_by is null
			and var_obatalkespasien_id is null
		)
			then
				var_jenisperubahan := 'TAMBAH';
				var_tanggalperubahan := new.created_date;
				var_pegawai_id := new.created_by;
		else var_jenisperubahan := null;
		end if;		
	
		if(
			var_jenisperubahan = 'TAMBAH'
			or var_jenisperubahan = 'HAPUS'
			or (var_jenisperubahan = 'PEMBAHARUAN' and var_perubahansebelum != var_perubahansetelah)
		) then
			select pegawai_m.nama_pegawai into var_pegawainama
			from loginpemakai_k
			join pegawai_m on loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
			where loginpemakai_k.loginpemakai_id = var_pegawai_id;
		
			if (var_pegawai_id is null) then var_pegawai_id := '1'; end if;
			if (var_pegawainama is null) then var_pegawainama := 'Super Admin'; end if;

			insert into logperubahanresep_r (
				reseptur_id, 
				resepturdetail_id,
				penjualanresep_id,
				obatalkespasien_id,
				tanggal_perubahan, 
				jenis_perubahan, 
				obatalkes_id, 
				obatalkes_nama, 
				perubahan_sebelum, 
				perubahan_setelah,
				pegawai_id,
				pegawai_nama)
			values (
				var_reseptur_id,
				new.resepturdetail_id,
				new.penjualanresep_id,
				new.obatalkespasien_id,
				date_trunc('second', now()::timestamp),
				var_jenisperubahan,
				new.obatalkes_id,
				var_obatalkesnama,
				var_perubahansebelum,
				var_perubahansetelah,
				var_pegawai_id,
				var_pegawainama
			);
		end if;
	
		return new;
	END;
\$function\$
;
");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241015_103515_migrate_PCP83_insert_logperubahanresep_r_penjualanresep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241015_103515_migrate_PCP83_insert_logperubahanresep_r_penjualanresep cannot be reverted.\n";

        return false;
    }
    */
}
