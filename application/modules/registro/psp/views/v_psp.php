<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	psp = {};
	// general stores
	psp.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getContribuyenteList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		pageSize: 50,
		listeners: {
			load: function () {
			}
		}
	});

	psp.plantilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getPlantillaList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	/*psp.resultado_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'PENDIENTE', desc: 'Pendiente'},
			{id: 'SI', desc: 'Si'},
			{id: 'NO', desc: 'No'}
		]
	});*/

	psp.estado_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getEstadoDocList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	psp.doc_requisito_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getDocRequisitoList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
			}
		}
	});

	psp.doc_estado_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getDocEstadoList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
			}
		}
	});

	psp.form_editing = false;
	psp.psp_id_selected = 0;
	
	psp.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 100,
		listeners: {
			load: function () {
				if (psp.psp_id_selected > 0) {
					Ext.getCmp('psp_main_grid').getSelectionModel().select(
						psp.main_store.getAt(
							psp.main_store.find('psp_id', psp.psp_id_selected)
						)
					);
				} else {
					Ext.getCmp('psp_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_psp_list'); ?>
<?php echo $this->load->view('v_psp_new'); ?>
<?php echo $this->load->view('v_psp_edit'); ?>
<?php ////echo $this->load->view('v_psp_delete'); ?>
<?php //echo $this->load->view('v_psp_doc_requisito_add'); ?>
<?php //echo $this->load->view('v_psp_doc_requisito_edit'); ?>
<?php //echo $this->load->view('v_psp_doc_requisito_delete'); ?>
<?php //echo $this->load->view('v_psp_doc_estado_add'); ?>
<?php //echo $this->load->view('v_psp_doc_estado_delete'); ?>
<?php //echo $this->load->view('v_psp_plantilla_cambiar'); ?>
<?php //echo $this->load->view('v_psp_pdf_generar'); ?>
<?php //echo $this->load->view('v_psp_print'); ?>
<?php //echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-psp');
	tab.add(psp.panel);
</script>