<?php
class M_CTL extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $search_ubig='all', $search_todos=true, $search_activos=true, $size=100, $start=0) {
		// calculamos los periodos a evaluar en $anio_mes_list
		$anio_desde = (date('Y')-2); // 3 años
		$total_meses = 13;
		$mes_hasta = date('n'); // mes actual del anio, pal anio anterior cambia a 12
		$anio_mes_list = array();
		$periodo_ini = '';
		$insert_tl = array();
		$insert_tl_header = array();
		for ($anio = date('Y'); $anio >= $anio_desde; $anio--) {
			for ($mes = $mes_hasta; $mes >= 1; $mes--) {
				$periodo = $anio.str_pad($mes, 2, '0', STR_PAD_LEFT);
				$anio_mes_list[] = $periodo;
				if (count($anio_mes_list) >= $total_meses) {
					$periodo_ini = $periodo;
					break 2;
				}
			}
			$mes_hasta = 12;
		}
		// obtenemos la lista de trab.
		$t_list = sys2009_trabajador_tl_list($periodo_ini, $search_ubig, $search_activos); // 0.33 s // periodo_ini: aaaamm
		//echo count($t_list); // 495 +o-
		//echo json_encode($t_list); die;
		
		$time = microtime(true);

		$list = array();
		foreach ($t_list as $i=>$t) {
			
			if (trim($t['reglab_cod']) == '') {
				continue; // no tienen regimen laboral
			}
			if (intval($t['planilla_count']) < 10) {
				//continue; 
			}
			if (!in_array(trim($t['reglab_cod']), array('02','15','21'))) { // 276, CAS, CC
				continue;
			} elseif ($t['reglab_cod']=='02' && $t['tipo_contrato_cod'] != 6) { // solo proy. inversion de la 276
				continue;
			}
			if (in_array(trim($t['sunat_tipo_contrato_cod']), array('01'))) { // Plazo indeterminado
				//echo "//{$t['traba_dni']}\n";
				continue;
			}
			//continue;
			
			$p_list = sys2009_planilla_s_list($t['traba_cod'], $periodo_ini);
			$s_list = sys2009_servicio_s_list($t['traba_dni'], $periodo_ini);

			$per_list = array();
			$activos = 0;
			$cortes = 0;
			$salir = false;
			
			foreach ($anio_mes_list  as $periodo) {
				$found = false;
				$p = array(
					'periodo'=>$periodo
				);
				foreach ($p_list as $p_list_index=>$r) {
					if ($r['plani_anio'].$r['plani_mes'] == $periodo) {
						// para verificar si es una remuneracion normal, verificamos que el bruto sea mayor al aguinaldo
						// planillas de aguinaldo no cuentan.
						if ($r['plani_traba_bruto'] > $r['plani_traba_aguinaldo']) {
							// puede haber mas de un registro de remuneraciones por mes
							$p['P'][] = $r;
							unset($p_list[$p_list_index]);
							$found = true;
							//var_dump($p_list);
						}
					}
				}
				
				foreach ($s_list as $r) {
					if ($r['os_anio'].str_pad($r['os_mes'], 2, '0', STR_PAD_LEFT) == $periodo && $r['os_anulado'] == 'NO') {
						$p['S'][] = $r;
						$found = true;
					}
				}

				// si no hay registros para el $periodo, entonces ha tenido un corte
				if ($found) {
					if ($cortes <= 1) {
						$activos++;
					}
					$p['activo'] = true;
					$per_list[] = $p;
				} else {
					$p['activo'] = false;
					$per_list[] = $p;
					$cortes++;
				}
			}

			//var_dump($per_list);
			foreach ($per_list as $pi=>$p) {
				$t['p'.($pi+1)] = '';
				if ($p['activo']) {
					if (isset($p['P'])) {
						$t['p'.($pi+1)] = $p['P'][0]['reglab_abrev'];
						if (isset($p['S'])) {
							$t['p'.($pi+1)] .= "\nO/S";
						}
					} elseif (isset($p['S'])) {
						$t['p'.($pi+1)] = "O/S";
					}
				}
			}
			$t['meses_lab'] = $activos;
      	
			if ($search_todos === true || $search_todos == 'true'){
				$list[] = $t;
			}
			else {
				// si tienes los activos permitidos
				if ($activos >= 9 && $activos <= 12) {
					$list[] = $t;
				}
			}
			

			if ($i>10) {
				//break;
			}
		}

		// ordenar por meses laborados
		function cmp($a, $b) {
			if ($a['meses_lab'] == $b['meses_lab']) {
				return 0;
			}
			return ($b['meses_lab'] < $a['meses_lab'])?-1:1;
		}

		usort($list, "cmp");

		// NO soporta PAGINATION
		/*$f_list = array();
		foreach ($list as $i=>$r) {
			if ($i >= $start) {
				if ($i < ($start+$size) ) {
					$f_list[] = $r;
				} else {
					break;
				}
			}
		}*/
		
		$interval = microtime(true ) - $time; 


		// copy on report table
		$report_key = base64_encode(microtime(true));

		// row header
		$data = array(
			'report_key'=>$report_key,
		);
		$m = date('n'); // mes actual
		$meses = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic');
		$y = '';
		for ($mes=1; $mes<=$total_meses; $mes++) {
			if ($m<1) {
				$m = 12;
				$y = ((date('Y')-1)-2000);
			}
			$data['hm'.$mes] = $meses[$m].$y;
			$m--;
		}
		// insert HEADER
		//$this->db->insert('rpt.tl_header', $data);
		$insert_tl_header[] = $data;
		// insert DATA
		foreach ($list as $t) {
			$data = array(
				'report_key'=>$report_key,
				'traba_cod'=>$t['traba_cod'],
				'traba_dni'=>$t['traba_dni'],
				'traba_nomape'=>$t['traba_nomape'],
				'reglab_cod'=>$t['reglab_cod'],
				'traba_activo'=>$t['traba_activo'],
				'meses_lab'=>$t['meses_lab'],
				'traba_ubigeo'=>(strlen($t['traba_ubigeo']) > 0 ? substr($t['traba_ubigeo'],0,99) : ''),
			);

			for ($mes=1; $mes<=$total_meses; $mes++) {
				$data['m'.$mes] = $t['p'.$mes];
			}
			//$this->db->insert_batch('rpt.tl', $data);
			$insert_tl[] = $data;
		}
		$this->db->insert_batch('rpt.tl_header', $insert_tl_header);
		$this->db->insert_batch('rpt.tl', $insert_tl);

		$rows = $this->db->where('report_key', $report_key)->order_by('meses_lab', 'desc')->order_by('traba_nomape', 'asc')->get('rpt.tl')->result();

		$ret = array(
			'time'=>$interval,
			'rkey'=>$report_key,
			'data'=>$rows,
			'total'=>count($rows)
		);

		return $ret;
	}

	public function get_planilla_list ($traba_cod) {
		$rows = sys2009_planilla_list($traba_cod);
		$total_count = count($rows);
		
		$list = array();
		foreach ($rows as $r) {
			$list[] = $r;
		}
		$ret = array(
			'data'=>$list,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_ingreso_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_ingreso_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_descuento_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_descuento_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_aporte_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_aporte_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_servicio_list ($traba_cod) {
		$rows = sys2009_servicio_list($traba_cod);
		$total_count = count($rows);
		
		$list = array();
		foreach ($rows as $r) {
			$list[] = $r;
		}
		$ret = array(
			'data'=>$list,
			'total'=>$total_count
		);
		return $ret;
	}

	public function prepare_print_detalle ($report_key) {
		$tl_rows = $this->db->where('report_key', $report_key)->order_by('meses_lab', 'desc')->order_by('traba_nomape', 'asc')->get('rpt.tl')->result();
		
		$this->db->trans_begin();
		
		foreach ($tl_rows as $tl) {
			
			$this->db->where('tl_id', $tl->tl_id)->delete('rpt.tl_det');
			
			$p_list = sys2009_planilla_list($tl->traba_cod);
			$s_list = sys2009_servicio_list($tl->traba_dni);
			$anios = array();
			$am = array(); //anio_mes
			foreach ($p_list as $r) {
				if (!in_array($r['plani_anio'], $anios)) {
					$anios[] = $r['plani_anio'];
				}
				$am[$r['plani_anio']][intval($r['plani_mes'])]['P'][] = $r;
			}
			foreach ($s_list as $r) {
				if (!in_array($r['os_anio'], $anios)) {
					$anios[] = $r['os_anio'];
				}
				$am[$r['os_anio']][intval($r['os_mes'])]['S'][] = $r;
			}
			rsort($anios);

			foreach ($anios as $anio) {
				$data = array(
					'tl_id'=>$tl->tl_id,
					'traba_cod'=>$tl->traba_cod,
					'traba_dni'=>$tl->traba_dni,
					'anio'=>$anio
				);
				for ($mes=1; $mes<=12; $mes++) {
					$data['m'.$mes] = '';
					$plani_list = array();
					if (isset($am[$anio][$mes]['P'])) {
						foreach($am[$anio][$mes]['P'] as $p) {
							$info = str_replace(' ', '', $p['plani_num'])."\n({$p['reglab_abrev']})";
							if ($p['plani_traba_aguinaldo']>0) {
								$info .= "\nAGUINAL";
							}
							$plani_list[] = $info;
						}
					}
					$os_list = array();
					if (isset($am[$anio][$mes]['S'])) {
						foreach($am[$anio][$mes]['S'] as $p) {
							 $os_list[] = 'OS-'.intval($p['os_numero'])."";
						}
					}
					$final_list = array_merge($plani_list, $os_list);
					$data['m'.$mes] = implode("\n", $final_list);
				}
				$this->db->insert('rpt.tl_det', $data);
			}
		}
		
		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

}
?>