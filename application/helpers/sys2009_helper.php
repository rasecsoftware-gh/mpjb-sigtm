<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');

	define('SYS2009_DB_PATH', 'D:\\sys2009$');

	if (!function_exists('sys2009_dbconnect')) {
	    //formateamos la fecha y la hora, función de cesarcancino.com
		function sys2009_dbconnect()
		{
			$conn = new COM("ADODB.Connection");
			$conn->Open('Provider=vfpoledb.1;Data Source='.SYS2009_DB_PATH.'\\dataa\\dataa.dbc;Collating Sequence=Machine');
			$conn->Execute("SET EXCLUSIVE OFF;");
			return $conn;
		}
	}

	if (!function_exists('sys2009_row')) {
		function sys2009_row($fields) {
			$row = array();
			foreach ($fields as $f) {
				try {
					if ($f->name == 'anulad') {
						//var_dump($f->value);
					}
					if (gettype($f->value) == 'string') {
						$row[$f->name] = trim(utf8_encode($f->value));
					} elseif(is_null($f->value)) {
						$row[$f->name] = NULL;
					} elseif(is_bool($f->value)) {
						$row[$f->name] = $f->value;
					} else {
						switch (variant_get_type($f->value)) {
							case VT_DECIMAL: case VT_R4: case VT_R8:
								$row[$f->name] = floatval($f->value);
								break;
							case VT_I1: case VT_I2: case VT_I4: case VT_INT: 
							case VT_UI1: case VT_UI2: case VT_UI4: case VT_UINT:
								$row[$f->name] = intval($f->value);
								break;
							case VT_BOOL:
								$row[$f->name] = (boolean) $f->value;
								break;
							case VT_NULL:
								$row[$f->name] = NULL;
								break;
							default:
								$row[$f->name] = (string) $f->value;
						}
					}
				} catch (Exception $e) {
					$row[$f->name] = '<!--ERROR-->';
					//echo "error: ".$e;
				}
			}
			return $row;
		}
	}
	/**
	 * RecordSet to Array
	 * @param $rs recordSet
	 */
	if (!function_exists('sys2009_to_array')) {
		function sys2009_to_array($rs) {
			$a = array();
			if (!$rs->BOF)
				$rs->MoveFirst();
			while (!$rs->EOF) {
				$a[] = sys2009_row($rs->Fields);
				$rs->MoveNext();
			}
			return $a;
		}
	}

	if (!function_exists('sys2009_det_oc_list')) {
		function sys2009_det_oc_list ($oc_anio, $oc_numero, $filter) {		
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			
			$bs_version = ($oc_anio>='2014')?'2014':'2009';
			
			$rs = $conn->Execute("
			SELECT 
				d.cano AS oc_anio,
				d.cordcom AS oc_numero,
				d.cbienes AS bs_cod,
				d.ncantid AS cantidad,
				d.obs AS obs,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			JOIN {$db_path}\\data\\mbienes.dbf b ON b.version_cb='{$bs_version}' AND b.cgrubie+b.csubbie+b.cbienes=d.cbienes
			WHERE 
				d.cano = '$oc_anio' 
				AND d.cordcom = '$oc_numero'
				AND b.ddescri LIKE '%{$filter}%'
			ORDER BY d.cano, d.cordcom, b.ddescri, d.obs;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_det_oc_by_meta_list')) {
		function sys2009_det_oc_by_meta_list ($ctarea, $filter) {		
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$anio = substr($ctarea, 0, 4);
			$bs_version = (intval($anio)>=2014)?'2014':'2009';
			
			$rs = $conn->Execute("
			SELECT 
				d.cano AS oc_anio,
				d.cordcom AS oc_numero,
				d.cbienes AS bs_cod,
				d.ncantid AS cantidad,
				d.npreuni AS precio,
				d.ntotal AS total,
				d.obs AS oc_det_obs,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed,
				o.ffecha AS oc_fecha
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			JOIN {$db_path}\\dataa\\mordcom.dbf AS o ON o.cano = d.cano AND o.cordcom = d.cordcom
			JOIN {$db_path}\\data\\mbienes.dbf b ON b.version_cb='{$bs_version}' AND b.cgrubie+b.csubbie+b.cbienes=d.cbienes
			WHERE 
				d.ctareas LIKE '$ctarea' 
				AND b.ddescri LIKE '%{$filter}%'
				AND NOT o.lanulad
			ORDER BY d.cano, d.cordcom, b.ddescri, d.obs;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_det_oc_row')) {
		function sys2009_det_oc_row ($oc_anio, $oc_numero, $bs_cod, $obs) {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			
			$bs_version = ($oc_anio>='2014')?'2014':'2009';

			//$obs = pg_escape_string(utf8_decode(trim($obs)));
			$obs = pg_escape_string((trim($obs)));
			$sql = "
			SELECT 
				d.cano AS orden_anio,
				d.cordcom AS orden_numero,
				d.cbienes AS bs_cod,
				d.ncantid AS cantidad,
				d.npreuni AS precio,
				d.ntotal AS total,
				d.obs AS obs,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			JOIN {$db_path}\\data\\mbienes.dbf b ON b.version_cb='{$bs_version}' AND b.cgrubie+b.csubbie+b.cbienes=d.cbienes
			WHERE 
				d.cano = '$oc_anio' 
				AND d.cordcom = '$oc_numero'
				AND d.cbienes = '$bs_cod'
				
			"; // usa LIKE pote el operador = funciona como like '%obs%' o.O
			// AND ALLTRIM(d.obs) LIKE '{$obs}'
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = sys2009_to_array($rs);
			// la observacion se compara en PHP por motivos de codificacion
			if (count($rows)>0) {
				foreach ($rows as $r) {
					if (trim($r['obs']) == $obs) {
						return $r;
					}
				}
				return null;
			}
			return null;
		}
	}

	if (!function_exists('sys2009_oc_list')) {
		function sys2009_oc_list ($ctareas, $filter) {
			if (trim($filter)!='') {
				$orden_numero = str_pad(trim($filter), 8, '0', STR_PAD_LEFT);
			} else {
				$orden_numero = '';
			}
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				d.cano AS oc_anio,
				d.cordcom AS oc_numero,
				d.ctareas
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			WHERE 
				STRTRAN(ALLTRIM(d.ctareas), ' ', '', 1, 10) = '$ctareas' 
				AND d.cordcom LIKE '%{$orden_numero}%'
			GROUP BY d.cano, d.cordcom, d.ctareas
			ORDER BY d.cano, d.cordcom;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_nemo_list')) {
		function sys2009_nemo_list ($nemo_anio, $filter) {
			$sql_filter_list = array();
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$sql_filter_list[] = "(m.anyo+' '+m.nemonico+' '+m.descripcio LIKE '%".strtoupper($t)."%')";
				}
			}
			$sql_filter = '1=1';
			if (count($sql_filter_list) > 0) {
				$sql_filter = implode(' AND ', $sql_filter_list);
			}

			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				m.anyo AS nemo_anio, 
				m.nemonico AS nemo_cod, 
				SUBSTR(m.secfun, 1, 4) AS nemo_secfun, 
				m.descripcio AS nemo_desc,
				IIF(
					m.anyo >= '2014',
					m.prog_ppto+m.proyact+m.componente+m.funcion+m.programa+m.subprogram+m.secfun,
					m.funcion+m.programa+m.subprogram+SUBSTR(m.proyact, 1, 1)+'.'+SUBSTR(m.proyact, 2, 6)+SUBSTR(m.componente, 1, 1)+'.'+SUBSTR(m.componente, 2, 6)+m.nemonico
				) AS nemo_meta
			FROM {$db_path}\\presu\\meta.dbf m
			WHERE m.anyo IN ('2009','2010','2011','2012','2013','2014','2015','2016')
			AND LEN(ALLTRIM(m.nemonico)) > 0
			AND ({$sql_filter})
			ORDER BY m.anyo DESC, m.nemonico;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_area_list')) {
		function sys2009_area_list () {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				id_ent AS area_cod, 
				des_ent AS area_desc
			FROM {$db_path}\\bd\\entidad.dbf
			ORDER BY des_ent;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_bien_list')) {
/*
*	Devuelve un listado de bienes segun el catalogo del sistema sys2009.
*	@filter:	texto a filtrar, es posible especificar palabras completas o parciales concatenadas por espacios sin importar el orden.
*/
		function sys2009_bien_list ($filter) {
			$sql_filter_list = array();
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$sql_filter_list[] = "(b.cgrubie+b.csubbie+b.cbienes+' '+b.ddescri+' '+b.dunimed LIKE '%".strtoupper($t)."%')";
				}
			}
			$sql_filter = implode(' AND ', $sql_filter_list);
			if (trim($filter)=='') {
				$sql_filter = '1=1';
			}

			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			
			$sql = "
			SELECT TOP 300
				b.cgrubie+b.csubbie+b.cbienes AS bs_cod,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed
			FROM {$db_path}\\data\\mbienes.dbf AS b 
			WHERE 
				b.version_cb LIKE '2014'
				AND b.cgrubie LIKE 'B%'
				AND LEN(ALLTRIM(b.ddescri))>0
				AND ({$sql_filter})
			ORDER BY b.ddescri
			"; // usa LIKE pote el operador = funciona como like '%obs%' o.O
			//echo $sql;
			$rs = $conn->Execute($sql);
			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_bien_row')) {
/*
*	Devuelve un listado de bienes segun el catalogo del sistema sys2009.
*	@filter:	texto a filtrar, es posible especificar palabras completas o parciales concatenadas por espacios sin importar el orden.
*/
		function sys2009_bien_row ($bs_cod) {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			
			$sql = "
			SELECT
				b.cgrubie+b.csubbie+b.cbienes AS bs_cod,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed
			FROM {$db_path}\\data\\mbienes.dbf AS b 
			WHERE 
				b.version_cb LIKE '2014'
				AND b.cgrubie+b.csubbie+b.cbienes LIKE '$bs_cod'
			"; 
			//echo $sql;
			$rs = $conn->Execute($sql);
			$rows = sys2009_to_array($rs);
			if (count($rows)>0) {
				return $rows[0];
			}
			return null;
		}
	}

	if (!function_exists('sys2009_trabajador_list')) {
		function sys2009_trabajador_list ($filter_by, $filter_text, $filter_ubig = 'all') {
			$sql_filter = $sql_filter_ext = '1=1';
			$order_by = 'traba_nomape';
			switch ($filter_by) {
				case 'all': case 'activos':
					$sql_filter_list = array();
					$terms = explode(' ', $filter_text);
					foreach ($terms as $i=>$t) {
						if (trim($t)!='') {
							$sql_filter_list[] = "(f.doc_emp+' '+f.p_nomb_em+' '+f.s_nomb_em+' '+f.ap_pate_em+' '+f.ap_mate_em LIKE '%".strtoupper($t)."%')";
						}
					}
					$sql_filter = implode(' AND ', $sql_filter_list);
					if ($filter_by == 'activos') {
						$sql_filter_ext = "traba_activo = 'SI'";
					}
				break;
				case 'traba_dni':
					$sql_filter = "f.doc_emp LIKE '{$filter_text}'";
				break;
				case 'traba_cod':
					$sql_filter = "f.cod_empl LIKE '{$filter_text}'";
				break;
				case 'onomastico':
					list($inicio, $fin) = explode('-', $filter_text);
					$md_list = array(0);
					for ($dt=$inicio; $dt<=$fin; $dt=strtotime('+1 day', $dt)) {
						$md_list[] = date('nd', $dt);
					}
					$md_strlist = implode(',', $md_list);
					$sql_filter = "MONTH(f.fec_naci)*100+DAY(f.fec_naci) IN ({$md_strlist})";
					$sql_filter_ext = "traba_activo = 'SI'";
					$order_by = "md_naci, traba_nomape ASC";
				break;
			}
			switch ($filter_ubig) {
				case 'locumba':
					if (trim($sql_filter)!='') { $sql_filter .= "AND f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di = '230301'"; }
					else { $sql_filter .= "f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di = '230301'"; }
				break;
				case 'notLocumba':
					if (trim($sql_filter)!='') { $sql_filter .= "AND f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di <> '230301'"; }
					else { $sql_filter .= "f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di <> '230301'"; }
				break;
			}
			
			if (trim($sql_filter)=='') {
				$sql_filter = '1=1';
			}
			

			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			/*
			IIF((
					SELECT COUNT(*) 
					FROM {$db_path}\\bases\\periodo_laboral 
					WHERE 
						YEAR(fecha_fin) = 0 
						AND tipo_cese = '' 
						AND fecha_ini <= DATE() 
						AND cod_trab = f.cod_empl
				) > 0, 'SI','NO') AS traba_activo,
			*/
			$sql = "
			SELECT 
				f.cod_empl AS traba_cod, 
				f.doc_emp AS traba_dni, 
				ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em)+' '+ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em) AS traba_nomape,
				ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em)+', '+ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em) AS traba_apenom,
				f.ruc AS traba_ruc,
				f.fec_naci AS traba_fecha_naci,
				f.sexo AS traba_sexo,
				f.depa_naci,
				f.prov_naci,
				f.dist_naci,
				f.dir_dom_em AS traba_direccion,
				f.telf_em AS traba_telefono,
				f.reg_lab AS reglab_cod,
				f.tipo_emp AS tipo_emp_cod,
				f.cod_tip_co AS tipo_contrato_cod,
				f.cont_sunat AS sunat_tipo_contrato_cod,
				IIF(f.situ_emp = '11', 'SI', 'NO') AS traba_activo,
				(
					SELECT 
						COUNT(*)
					FROM {$db_path}\\bases\\emp_plani.dbf
					WHERE cod_emp = f.cod_empl
				) AS planilla_count,
				(
					SELECT COUNT(*) AS valor
					FROM {$db_path}\\dataa\\mordser.dbf AS o 
					JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
					WHERE 
						p.druc LIKE '10%'
						AND SUBSTR(p.druc, 3, 8) = f.doc_emp
						AND NOT o.lanulad
				) AS servicio_count,
				(
					SELECT UPPER(u.nombre)
					FROM {$db_path}\\bases\\ubigeo.dbf AS u
					WHERE
						u.coddpto = f.ubigeo_de
						AND u.codprov = ubigeo_pr
						AND u.coddist = ubigeo_di
				) AS traba_ubigeo,
				MONTH(f.fec_naci)*100+DAY(f.fec_naci) AS md_naci
			FROM {$db_path}\\bases\\fic_ingr.dbf f
			WHERE ALLTRIM(f.doc_emp)<>'' AND ({$sql_filter})
			HAVING ({$sql_filter_ext})
			ORDER BY {$order_by} 
			";
			$rs = $conn->Execute($sql);
			$rows = sys2009_to_array($rs);
			//var_dump($rows);

			/*foreach ($rows as $i=>$r) {
				$rs2 = $conn->Execute("
				SELECT COUNT(*) AS valor
				FROM {$db_path}\\dataa\\mordser.dbf AS o 
				JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
				WHERE 
					p.druc LIKE '10{$r['traba_dni']}%'
				");
				$servicioCount = sys2009_to_array($rs2);
				$rows[$i]['servicio_count'] = $servicioCount[0]['valor'];
			}*/

			return $rows;
		}
	}

	if (!function_exists('sys2009_planilla_list')) {
		function sys2009_planilla_list ($traba_cod) {
			$ci =& get_instance();
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				ep.cod_emp AS traba_cod,
				p.cod_plani AS plani_cod,
				p.anio_plani AS plani_anio,
				p.mes_plani AS plani_mes,
				p.num_plani AS plani_num,
				p.titulo AS plani_titulo,
				p.glosa AS plani_glosa,
				p.siaf AS plani_siaf,
				f.doc_emp AS traba_dni, 
				ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em)+' '+ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em) AS traba_nomape,
				ep.dias_tot AS plani_traba_diaslab,
				ep.nivel AS plani_traba_nivel,
				ep.basico AS plani_traba_basico,
				tc.des_tip_co AS tipo_contrato_desc,
				tc.reg_lab AS reglab_cod,
				des_tip_co AS reglab_desc,
				des_tip_co AS reglab_abrev,
				p.cen_plani AS nemo_cod,
				SUBSTR(m.secfun, 1, 4) AS nemo_secfun,
				m.descripcio AS nemo_desc,
				'' AS plani_traba_cargo,
				0.00 AS plani_traba_bruto,
				0.00 AS plani_traba_neto,
				0.00 AS plani_traba_aguinaldo
			FROM {$db_path}\\bases\\emp_plani.dbf AS ep
			JOIN {$db_path}\\bases\\fic_ingr.dbf f ON f.cod_empl = ep.cod_emp
			JOIN {$db_path}\\bases\\enc_plani.dbf AS p ON p.cod_plani = ep.cod_plani
			JOIN {$db_path}\\bases\\tip_cont.dbf AS tc ON tc.cod_tip_co = p.tipo_cont
			LEFT JOIN {$db_path}\\presu\\meta.dbf AS m ON m.anyo = p.anio_plani AND m.nemonico = p.cen_plani
			WHERE ep.cod_emp LIKE '{$traba_cod}'
			ORDER BY p.anio_plani DESC, p.mes_plani DESC;
			");

			$rows = sys2009_to_array($rs);

			foreach ($rows AS $i=>$r) {
				// sql para obtener el monto bruto
				$sql = "
				SELECT SUM(ie.monto) AS valor
				FROM {$db_path}\\bases\\ing_emp.dbf AS ie
				WHERE 
					ie.anio = '{$r['plani_anio']}'
					AND ie.cod_plani = {$r['plani_cod']}
					AND ie.cod_emp = '{$r['traba_cod']}'
				";
				// AND c_concepto <> '063'
				// 063: Conpemsacion Vacacional
				$rs_bruto = $conn->Execute($sql);
				$bruto = sys2009_to_array($rs_bruto);
				if (count($bruto)>0) {
					$rows[$i]['plani_traba_bruto'] = $bruto[0]['valor'];
					$rows[$i]['plani_traba_neto'] = $bruto[0]['valor'];
					// sql para obtener el monto del descuento
					$sql = "
					SELECT SUM(monto) AS valor
					FROM {$db_path}\\bases\\des_emp.dbf 
					WHERE 
						anio = '{$r['plani_anio']}'
						AND cod_plani = {$r['plani_cod']}
						AND cod_emp = '{$r['traba_cod']}'
					";
					$rs_descuento = $conn->Execute($sql);
					$descuento = sys2009_to_array($rs_descuento);
					// si el descuento es > 0, calculamos el NETO
					if (count($descuento)>0) {
						$rows[$i]['plani_traba_neto'] = $bruto[0]['valor'] - $descuento[0]['valor'];
					}
				}

				// obtener el CARGO segun el periodo laboral
				$sql = "
				SELECT TOP 1 
					cargo AS cargo_desc
				FROM {$db_path}\\bases\\periodo_laboral.dbf 
				WHERE 
					cod_trab = '{$r['traba_cod']}'
					AND (
						YEAR(fecha_ini) <= {$r['plani_anio']} AND MONTH(fecha_ini) <= {$r['plani_mes']}
					)
					AND (
						( {$r['plani_anio']} <= YEAR(fecha_fin) AND {$r['plani_mes']} <= MONTH(fecha_fin) ) 
						OR YEAR(fecha_fin) = 0
					)
				ORDER BY fecha_ini DESC
				";
				$rs_cargo = $conn->Execute($sql);
				$cargo = sys2009_to_array($rs_cargo);
				if (count($cargo)>0) {
					$rows[$i]['plani_traba_cargo'] = $cargo[0]['cargo_desc'];
				}

				// obtener si tiene AGUINALDO en la planilla
				$sql = "
				SELECT SUM(monto) AS valor
				FROM {$db_path}\\bases\\ing_emp.dbf
				WHERE 
					anio = '{$r['plani_anio']}'
					AND cod_plani = {$r['plani_cod']}
					AND cod_emp = '{$r['traba_cod']}'
					AND cod_ing IN (75,80,82,83,156)
					AND monto > 0
				";
				// 75,80,82,83,156: aguinaldos
				$rs_aguinaldo = $conn->Execute($sql);
				$aguinaldo = sys2009_to_array($rs_aguinaldo);				
				if (count($aguinaldo)>0) {
					$rows[$i]['plani_traba_aguinaldo'] = is_null($aguinaldo[0]['valor'])?0:$aguinaldo[0]['valor'];
				}

				// obtener informacion de REGimen LABoral, de la tabla rh.reglab (pg) para le descripcion y abreviacion personalizada
				$reglab = $ci->db->where('reglab_cod', $r['reglab_cod'])->get('rh.reglab')->row();
				if (!is_null($reglab)) {
					$rows[$i]['reglab_desc'] = $reglab->reglab_desc;
					$rows[$i]['reglab_abrev'] = $reglab->reglab_abrev2;
				}
			}
			
			return $rows;
		}
	}

	if (!function_exists('sys2009_plani_ingreso_list')) {
		function sys2009_plani_ingreso_list($plani_anio, $plani_cod, $traba_cod) {
			$ci =& get_instance();
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				i.anio AS plani_anio,
				i.cod_plani AS plani_cod,
				i.cod_emp AS traba_cod,
				i.cod_tc,
				i.c_concepto,
				i.cod_ing,
				i.monto AS plani_traba_monto,
				t.descripcio AS tipo_ingreso_desc,
				t.abr AS tipo_ingreso_abrev
			FROM {$db_path}\\bases\\ing_emp.dbf AS i
			JOIN {$db_path}\\bases\\tipo_ingreso.dbf AS t ON t.cod_ing = i.cod_ing
			WHERE 
				i.anio LIKE '{$plani_anio}'
				AND i.cod_plani = {$plani_cod}
				AND i.cod_emp LIKE '{$traba_cod}'
			ORDER BY i.cod_ing;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_plani_descuento_list')) {
		function sys2009_plani_descuento_list($plani_anio, $plani_cod, $traba_cod) {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				de.anio AS plani_anio,
				de.cod_plani AS plani_cod,
				de.cod_emp AS traba_cod,
				de.cod_tc,
				de.c_concepto,
				de.cod_des,
				de.monto AS plani_traba_monto,
				t.descripcio AS tipo_descuento_desc,
				t.abr AS tipo_descuento_abrev
			FROM {$db_path}\\bases\\des_emp.dbf AS de
			JOIN {$db_path}\\bases\\tipo_descuento.dbf AS t ON t.cod_des = de.cod_des
			WHERE 
				de.anio LIKE '{$plani_anio}'
				AND de.cod_plani = {$plani_cod}
				AND de.cod_emp LIKE '{$traba_cod}'
				AND de.monto > 0
			ORDER BY de.cod_des;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_plani_aporte_list')) {
		function sys2009_plani_aporte_list($plani_anio, $plani_cod, $traba_cod) {
			$ci =& get_instance();
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				a.anio AS plani_anio,
				a.cod_plani AS plani_cod,
				a.cod_emp AS traba_cod,
				a.cod_tc,
				a.c_concepto,
				a.cod_aporte,
				a.monto AS plani_traba_monto,
				t.descripcio AS tipo_aporte_desc,
				t.abr AS tipo_aporte_abrev
			FROM {$db_path}\\bases\\aport_emp.dbf AS a
			JOIN {$db_path}\\bases\\tipo_aporte.dbf AS t ON t.cod_aporte = a.cod_aporte
			WHERE 
				a.anio LIKE '{$plani_anio}'
				AND a.cod_plani = {$plani_cod}
				AND a.cod_emp LIKE '{$traba_cod}'
				AND a.monto > 0
			ORDER BY a.cod_aporte;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_servicio_list')) {
		function sys2009_servicio_list ($traba_dni) {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				o.cano AS os_anio,
				o.cordser AS os_numero,
				o.cprovee AS prove_cod,
				p.druc AS prove_ruc,
				p.dnomemp AS prove_desc,
				o.ffecha AS os_fecha,
				MONTH(o.ffecha) AS os_mes,
				IIF(o.lanulad, 'SI', 'NO') AS os_anulado,
				o.drefere AS os_referencia,
				o.mglosa AS os_glosa,
				'' AS os_meta_cod,
				'' AS nemo_anio,
				'' AS nemo_cod,
				'' AS nemo_secfun,
				'' AS nemo_desc,
				0 AS os_total
			FROM {$db_path}\\dataa\\mordser.dbf AS o 
			JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
			WHERE 
				p.druc LIKE '10{$traba_dni}%'
			ORDER BY o.cano DESC, o.cordser DESC;
			");

			$rows = sys2009_to_array($rs);

			foreach($rows as $i=>$r) {
				$rs2 = $conn->Execute("
				SELECT 
					STRTRAN(ALLTRIM(d.ctareas), ' ', '', 1, 10) AS os_meta_cod,
					SUM(d.ntotal) AS os_total
				FROM {$db_path}\\dataa\\pbieser.dbf AS d
				WHERE 
					d.cano LIKE '{$r['os_anio']}'
					AND d.cordser LIKE '{$r['os_numero']}'
				GROUP BY os_meta_cod;
				");
				$rows2 = sys2009_to_array($rs2);
				if (count($rows2)>0) {
					$r2 = $rows2[0];
					$rows[$i]['os_meta_cod'] = $r2['os_meta_cod'];
					$rows[$i]['os_total'] = $r2['os_total'];

					$rs3 = $conn->Execute("
					SELECT 
						m.anyo AS nemo_anio, 
						m.nemonico AS nemo_cod, 
						SUBSTR(m.secfun, 1, 4) AS nemo_secfun, 
						m.descripcio AS nemo_desc
					FROM {$db_path}\\presu\\meta.dbf m 
					WHERE
						m.anyo+IIF(
							m.anyo >= '2014',
							m.prog_ppto+m.proyact+m.componente+m.funcion+m.programa+m.subprogram+m.secfun,
							m.funcion+m.programa+m.subprogram+SUBSTR(m.proyact, 1, 1)+'.'+SUBSTR(m.proyact, 2, 6)+SUBSTR(m.componente, 1, 1)+'.'+SUBSTR(m.componente, 2, 6)+m.nemonico
						) LIKE '{$r2['os_meta_cod']}' 
					");
					$rows3 = sys2009_to_array($rs3);
					if (count($rows3)>0) {
						$r3 = $rows3[0];
						$rows[$i]['nemo_anio'] = $r3['nemo_anio'];
						$rows[$i]['nemo_cod'] = $r3['nemo_cod'];
						$rows[$i]['nemo_secfun'] = $r3['nemo_secfun'];
						$rows[$i]['nemo_desc'] = $r3['nemo_desc'];
					}
				}
			}
			return $rows;
		}
	}

	if (!function_exists('sys2009_trabajador_tl_list')) {
		function sys2009_trabajador_tl_list ($periodo_ini, $filter_opcion, $filter_estado, $filter_ubigeo) {
			// 201612
			$anio = substr($periodo_ini, 0, 4);
			$mes = intval(substr($periodo_ini, 4, 2));

			$anio_3s = date('Y');
			$mes_3s = date('n') - 3 + 1;
			if ($mes_3s <= 0) {
				$mes_3s = 12 - $mes_3s;
				$anio_3s = $anio_3s - 1;
			}

			//echo $anio_3s; echo $mes_3s; die;

			$anio_gestion = 2015;

			$sql_filter = '1=1';

			$sql_having_opcion = "planilla_count > 0"; // all
			switch ($filter_opcion) {
				case 'pe':
					$sql_having_opcion = "(planilla_count > 0 OR servicio_count > 0)";
				break;
				case 'gestion':
					$sql_having_opcion = "planilla_gestion_count > 0";
				break;
				case 'gestion3s':
					$sql_having_opcion = "(planilla_gestion_count > 0 AND servicio3_count > 0)";
				break;
			}
			
			$sql_having_estado = "traba_activo IN ('SI','NO')"; // all
			switch ($filter_estado) {
				case 'activo':
					$sql_having_estado = "traba_activo IN ('SI')";
				break;
				case 'noactivo':
					$sql_having_estado = "traba_activo IN ('NO')";
				break;
			}

			switch ($filter_ubigeo) {
				case 'locumba':
					$sql_filter = " f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di = '230301'";
				break;
				case 'nolocumba':
					$sql_filter = " f.ubigeo_de + f.ubigeo_pr + f.ubigeo_di <> '230301'";
				break;
			}


			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				f.cod_empl AS traba_cod, 
				f.doc_emp AS traba_dni, 
				ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em)+' '+ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em) AS traba_nomape,
				ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em)+', '+ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em) AS traba_apenom,
				f.fec_naci AS traba_fecha_naci,
				f.sexo AS traba_sexo,
				f.depa_naci,
				f.prov_naci,
				f.dist_naci,
				f.dir_dom_em AS traba_direccion,
				f.telf_em AS traba_telefono,
				f.reg_lab AS reglab_cod,
				f.tipo_emp AS tipo_emp_cod,
				f.cod_tip_co AS tipo_contrato_cod,
				f.cont_sunat AS sunat_tipo_contrato_cod,
				IIF((
					SELECT COUNT(*) 
					FROM {$db_path}\\bases\\periodo_laboral 
					WHERE 
						YEAR(fecha_fin) = 0 
						AND tipo_cese = '' 
						AND fecha_ini <= DATE() 
						AND cod_trab = f.cod_empl
				) > 0, 'SI','NO') AS traba_activo,
				(
					SELECT 
						COUNT(*)
					FROM {$db_path}\\bases\\emp_plani.dbf
					WHERE 
						cod_emp = f.cod_empl
						AND anio_plani+mes_plani > '{$periodo_ini}'
				) AS planilla_count,
				(
					SELECT 
						COUNT(*)
					FROM {$db_path}\\bases\\emp_plani.dbf
					WHERE 
						cod_emp = f.cod_empl
						AND anio_plani >= '{$anio_gestion}'
				) AS planilla_gestion_count,
				(
					SELECT COUNT(*) AS valor
					FROM {$db_path}\\dataa\\mordser.dbf AS o 
					JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
					WHERE 
						p.druc LIKE '10%'
						AND SUBSTR(p.druc, 3, 8) = f.doc_emp
						AND NOT o.lanulad
						AND o.ffecha >= DATE($anio, $mes, 1)
				) AS servicio_count,
				(
					SELECT COUNT(*) AS valor
					FROM {$db_path}\\dataa\\mordser.dbf AS o 
					JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
					WHERE 
						p.druc LIKE '10%'
						AND SUBSTR(p.druc, 3, 8) = f.doc_emp
						AND NOT o.lanulad
						AND o.ffecha >= DATE($anio_3s, $mes_3s, 1)
				) AS servicio3_count,
				(
					SELECT UPPER(u.nombre)
					FROM {$db_path}\\bases\\ubigeo.dbf AS u
					WHERE
						u.coddpto = f.ubigeo_de
					AND u.codprov = ubigeo_pr
					AND u.coddist = ubigeo_di
				) AS traba_ubigeo
			FROM {$db_path}\\bases\\fic_ingr.dbf f
			WHERE ALLTRIM(f.doc_emp)<>'' AND {$sql_filter}
			HAVING 
				{$sql_having_opcion} 
				AND {$sql_having_estado} 
			ORDER BY traba_nomape ASC;
			");

			$rows = sys2009_to_array($rs);

			return $rows;
		}
	}

	if (!function_exists('sys2009_planilla_s_list')) {
		// single list
		function sys2009_planilla_s_list ($traba_cod, $periodo_ini) {
			$ci =& get_instance();
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				ep.cod_emp AS traba_cod,
				p.cod_plani AS plani_cod,
				p.anio_plani AS plani_anio,
				p.mes_plani AS plani_mes,
				p.num_plani AS plani_num,
				tc.reg_lab AS reglab_cod,
				'' AS reglab_abrev,
				0 AS plani_traba_bruto,
				0 AS plani_traba_aguinaldo
			FROM {$db_path}\\bases\\emp_plani.dbf AS ep
			JOIN {$db_path}\\bases\\enc_plani.dbf AS p ON p.cod_plani = ep.cod_plani
			JOIN {$db_path}\\bases\\tip_cont.dbf AS tc ON tc.cod_tip_co = p.tipo_cont
			WHERE 
				ep.cod_emp LIKE '{$traba_cod}'
				AND p.anio_plani+p.mes_plani >= '$periodo_ini'
			ORDER BY p.anio_plani DESC, p.mes_plani DESC;
			");

			$rows = sys2009_to_array($rs);

			foreach ($rows AS $i=>$r) {
				// sql para obtener el monto bruto
				$sql = "
				SELECT SUM(ie.monto) AS valor
				FROM {$db_path}\\bases\\ing_emp.dbf AS ie
				WHERE 
					ie.anio = '{$r['plani_anio']}'
					AND ie.cod_plani = {$r['plani_cod']}
					AND ie.cod_emp = '{$r['traba_cod']}'
				";
				$rs_bruto = $conn->Execute($sql);
				$bruto = sys2009_to_array($rs_bruto);
				if (count($bruto)>0) {
					$rows[$i]['plani_traba_bruto'] = $bruto[0]['valor'];
					// obtener si tiene AGUINALDO en la planilla
					$sql = "
					SELECT SUM(monto) AS valor
					FROM {$db_path}\\bases\\ing_emp.dbf
					WHERE 
						anio = '{$r['plani_anio']}'
						AND cod_plani = {$r['plani_cod']}
						AND cod_emp = '{$r['traba_cod']}'
						AND cod_ing IN (75,80,82,83,156)
						AND monto > 0
					";
					// 75,80,82,83,156: aguinaldos
					$rs_aguinaldo = $conn->Execute($sql);
					$aguinaldo = sys2009_to_array($rs_aguinaldo);				
					if (count($aguinaldo)>0) {
						$rows[$i]['plani_traba_aguinaldo'] = is_null($aguinaldo[0]['valor'])?0:$aguinaldo[0]['valor'];
					}
				}
				// obtener informacion de REGimen LABoral, de la tabla rh.reglab (pg) para le descripcion y abreviacion personalizada
				$reglab = $ci->db->where('reglab_cod', $r['reglab_cod'])->get('rh.reglab')->row();
				if (!is_null($reglab)) {
					$rows[$i]['reglab_abrev'] = $reglab->reglab_abrev2;
				}
			}
			return $rows;
		}
	}

	if (!function_exists('sys2009_servicio_s_list')) {
		// sigle list
		function sys2009_servicio_s_list ($traba_dni, $periodo_ini) {
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;

			$anio = substr($periodo_ini, 0, 4);
			$mes = intval(substr($periodo_ini, 4, 2));

			$rs = $conn->Execute("
			SELECT 
				o.cano AS os_anio,
				o.cordser AS os_numero,
				o.cprovee AS prove_cod,
				p.druc AS prove_ruc,
				p.dnomemp AS prove_desc,
				o.ffecha AS os_fecha,
				MONTH(o.ffecha) AS os_mes,
				IIF(o.lanulad, 'SI', 'NO') AS os_anulado
			FROM {$db_path}\\dataa\\mordser.dbf AS o 
			JOIN {$db_path}\\data\\mprovee.dbf AS p ON p.cprovee = o.cprovee
			WHERE 
				p.druc LIKE '10{$traba_dni}%'
				AND o.ffecha >= DATE($anio, $mes, 1)
			ORDER BY o.cano DESC, o.cordser DESC;
			");

			$rows = sys2009_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('sys2009_trabajador_c_list')) { // for Contrato
		function sys2009_trabajador_c_list ($filter_by, $filter_text) {
			$sql_filter = $sql_filter_ext = '1=1';
			switch ($filter_by) {
				case 'all': case 'activos':
					$sql_filter_list = array();
					$terms = explode(' ', $filter_text);
					foreach ($terms as $i=>$t) {
						if (trim($t)!='') {
							$sql_filter_list[] = "(f.doc_emp+' '+f.p_nomb_em+' '+f.s_nomb_em+' '+f.ap_pate_em+' '+f.ap_mate_em LIKE '%".strtoupper($t)."%')";
						}
					}
					$sql_filter = implode(' AND ', $sql_filter_list);
					if ($filter_by == 'activos') {
						$sql_filter_ext = "traba_activo = 'SI'";
					}
				break;
				case 'traba_dni':
					$sql_filter = "f.doc_emp LIKE '{$filter_text}'";
				break;
				case 'traba_cod':
					$sql_filter = "f.cod_empl LIKE '{$filter_text}'";
				break;
			}
			if (trim($sql_filter)=='') {
				$sql_filter = '1=1';
			}
			$conn = sys2009_dbconnect();
			$db_path = SYS2009_DB_PATH;
			$rs = $conn->Execute("
			SELECT 
				f.cod_empl AS traba_cod, 
				f.doc_emp AS traba_dni, 
				ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em)+' '+ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em) AS traba_nomape,
				ALLTRIM(f.ap_pate_em)+' '+ALLTRIM(f.ap_mate_em)+', '+ALLTRIM(f.p_nomb_em)+' '+ALLTRIM(f.s_nomb_em) AS traba_apenom,
				f.ruc AS traba_ruc,
				f.fec_naci AS traba_fecha_naci,
				f.sexo AS traba_sexo,
				f.depa_naci,
				f.prov_naci,
				f.dist_naci,
				f.dir_dom_em AS traba_direccion,
				f.telf_em AS traba_telefono,
				f.reg_lab AS reglab_cod,
				f.tipo_emp AS tipo_emp_cod,
				f.cod_tip_co AS tipo_contrato_cod,
				f.cont_sunat AS sunat_tipo_contrato_cod,
				IIF((
					SELECT COUNT(*) 
					FROM {$db_path}\\bases\\periodo_laboral 
					WHERE 
						YEAR(fecha_fin) = 0 
						AND tipo_cese = '' 
						AND fecha_ini <= DATE() 
						AND cod_trab = f.cod_empl
				) > 0, 'SI','NO') AS traba_activo
			FROM {$db_path}\\bases\\fic_ingr.dbf f
			WHERE ALLTRIM(f.doc_emp)<>'' AND ({$sql_filter})
			HAVING ({$sql_filter_ext})
			ORDER BY traba_nomape;
			");

			$rows = sys2009_to_array($rs);

			return $rows;
		}
	}

	if (!function_exists('sys2009_upper')) {
		function sys2009_upper ($str) {
			$from = 'áéíóúñ';
			$to = 'ÁÉÍÓÚÑ';
			$result = strtr(strtoupper($str), $from, $to);
			return $result;
		}
	}

	if (!function_exists('num2letras')) {

		/*! 
		  @function num2letras () 
		  @abstract Dado un n?mero lo devuelve escrito. 
		  @param $num number - N?mero a convertir. 
		  @param $fem bool - Forma femenina (true) o no (false). 
		  @param $dec bool - Con decimales (true) o no (false). 
		  @result string - Devuelve el n?mero escrito en letra. 

		*/ 
		function num2letras($num, $fem = false, $dec = true) {
		   $matuni[2]  = "dos"; 
		   $matuni[3]  = "tres"; 
		   $matuni[4]  = "cuatro"; 
		   $matuni[5]  = "cinco"; 
		   $matuni[6]  = "seis"; 
		   $matuni[7]  = "siete"; 
		   $matuni[8]  = "ocho"; 
		   $matuni[9]  = "nueve"; 
		   $matuni[10] = "diez"; 
		   $matuni[11] = "once"; 
		   $matuni[12] = "doce"; 
		   $matuni[13] = "trece"; 
		   $matuni[14] = "catorce"; 
		   $matuni[15] = "quince"; 
		   $matuni[16] = "dieciseis"; 
		   $matuni[17] = "diecisiete"; 
		   $matuni[18] = "dieciocho"; 
		   $matuni[19] = "diecinueve"; 
		   $matuni[20] = "veinte"; 
		   $matunisub[2] = "dos"; 
		   $matunisub[3] = "tres"; 
		   $matunisub[4] = "cuatro"; 
		   $matunisub[5] = "quin"; 
		   $matunisub[6] = "seis"; 
		   $matunisub[7] = "sete"; 
		   $matunisub[8] = "ocho"; 
		   $matunisub[9] = "nove"; 

		   $matdec[2] = "veint"; 
		   $matdec[3] = "treinta"; 
		   $matdec[4] = "cuarenta"; 
		   $matdec[5] = "cincuenta"; 
		   $matdec[6] = "sesenta"; 
		   $matdec[7] = "setenta"; 
		   $matdec[8] = "ochenta"; 
		   $matdec[9] = "noventa"; 
		   $matsub[3]  = 'mill'; 
		   $matsub[5]  = 'bill'; 
		   $matsub[7]  = 'mill'; 
		   $matsub[9]  = 'trill'; 
		   $matsub[11] = 'mill'; 
		   $matsub[13] = 'bill'; 
		   $matsub[15] = 'mill'; 
		   $matmil[4]  = 'millones'; 
		   $matmil[6]  = 'billones'; 
		   $matmil[7]  = 'de billones'; 
		   $matmil[8]  = 'millones de billones'; 
		   $matmil[10] = 'trillones'; 
		   $matmil[11] = 'de trillones'; 
		   $matmil[12] = 'millones de trillones'; 
		   $matmil[13] = 'de trillones'; 
		   $matmil[14] = 'billones de trillones'; 
		   $matmil[15] = 'de billones de trillones'; 
		   $matmil[16] = 'millones de billones de trillones'; 
		   
		   //Zi hack
		   $float=explode('.', $num);
		   $num=$float[0];

		   $num = trim((string)@$num); 
		   if ($num[0] == '-') { 
		      $neg = 'menos '; 
		      $num = substr($num, 1); 
		   }else 
		      $neg = ''; 
		   while ($num[0] == '0') $num = substr($num, 1); 
		   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
		   $zeros = true; 
		   $punt = false; 
		   $ent = ''; 
		   $fra = ''; 
		   for ($c = 0; $c < strlen($num); $c++) { 
		      $n = $num[$c]; 
		      if (! (strpos(".,'''", $n) === false)) { 
		         if ($punt) break; 
		         else{ 
		            $punt = true; 
		            continue; 
		         } 

		      }elseif (! (strpos('0123456789', $n) === false)) { 
		         if ($punt) { 
		            if ($n != '0') $zeros = false; 
		            $fra .= $n; 
		         }else 

		            $ent .= $n; 
		      }else 

		         break; 

		   } 
		   $ent = '     ' . $ent; 
		   if ($dec and $fra and ! $zeros) { 
		      $fin = ' coma'; 
		      for ($n = 0; $n < strlen($fra); $n++) { 
		         if (($s = $fra[$n]) == '0') 
		            $fin .= ' cero'; 
		         elseif ($s == '1') 
		            $fin .= $fem ? ' una' : ' un'; 
		         else 
		            $fin .= ' ' . $matuni[$s]; 
		      } 
		   }else 
		      $fin = ''; 
		   if ((int)$ent === 0) return 'Cero ' . $fin; 
		   $tex = ''; 
		   $sub = 0; 
		   $mils = 0; 
		   $neutro = false; 
		   while ( ($num = substr($ent, -3)) != '   ') { 
		      $ent = substr($ent, 0, -3); 
		      if (++$sub < 3 and $fem) { 
		         $matuni[1] = 'una'; 
		         $subcent = 'as'; 
		      }else{ 
		         $matuni[1] = $neutro ? 'un' : 'uno'; 
		         $subcent = 'os'; 
		      } 
		      $t = ''; 
		      $n2 = substr($num, 1); 
		      if ($n2 == '00') { 
		      }elseif ($n2 < 21) 
		         $t = ' ' . $matuni[(int)$n2]; 
		      elseif ($n2 < 30) { 
		         $n3 = $num[2]; 
		         if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
		         $n2 = $num[1]; 
		         $t = ' ' . $matdec[$n2] . $t; 
		      }else{ 
		         $n3 = $num[2]; 
		         if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
		         $n2 = $num[1]; 
		         $t = ' ' . $matdec[$n2] . $t; 
		      } 
		      $n = $num[0]; 
		      if ($n == 1) { 
		         $t = ' ciento' . $t; 
		      }elseif ($n == 5){ 
		         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
		      }elseif ($n != 0){ 
		         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
		      } 
		      if ($sub == 1) { 
		      }elseif (! isset($matsub[$sub])) { 
		         if ($num == 1) { 
		            $t = 'un mil'; 
		         }elseif ($num > 1){ 
		            $t .= ' mil'; 
		         } 
		      }elseif ($num == 1) { 
		         $t .= ' ' . $matsub[$sub] . 'un'; 
		      }elseif ($num > 1){ 
		         $t .= ' ' . $matsub[$sub] . 'ones'; 
		      }   
		      if ($num == '000') $mils ++; 
		      elseif ($mils != 0) { 
		         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
		         $mils = 0; 
		      } 
		      $neutro = true; 
		      $tex = $t . $tex; 
		   } 
		   $tex = $neg . substr($tex, 0) . $fin; 
		   //Zi hack --> return ucfirst($tex);
		   if (isset($float[1])) {
		   		$deci = substr(intval($float[1]).'00', 0, 2);
		   } else {
		   	  $deci = '00';
		   }

		   $end_num = strtoupper($tex).' CON '.$deci.'/100 SOLES';
		   return $end_num; 
		}
	}
?>