<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	clit = {};
	// general stores
	clit.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getContribuyenteList',
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

	clit.plantilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getPlantillaList',
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

	clit.resultado_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'PENDIENTE', desc: 'Pendiente'},
			{id: 'SI', desc: 'Si'},
			{id: 'NO', desc: 'No'}
		]
	});

	clit.estado_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getEstadoDocList',
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

	clit.doc_requisito_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getDocRequisitoList',
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

	clit.doc_estado_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getDocEstadoList',
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

	clit.form_editing = false;
	clit.clit_id_selected = 0;
	
	clit.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getList',
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
				if (clit.clit_id_selected > 0) {
					Ext.getCmp('clit_main_grid').getSelectionModel().select(
						clit.main_store.getAt(
							clit.main_store.find('clit_id', clit.clit_id_selected)
						)
					);
				} else {
					Ext.getCmp('clit_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_clit_list'); ?>
<?php echo $this->load->view('v_clit_new'); ?>
<?php echo $this->load->view('v_clit_edit'); ?>
<?php //echo $this->load->view('v_clit_delete'); ?>
<?php echo $this->load->view('v_clit_doc_requisito_add'); ?>
<?php echo $this->load->view('v_clit_doc_requisito_edit'); ?>
<?php echo $this->load->view('v_clit_doc_requisito_delete'); ?>
<?php echo $this->load->view('v_clit_doc_estado_add'); ?>
<?php echo $this->load->view('v_clit_doc_estado_delete'); ?>
<?php echo $this->load->view('v_clit_plantilla_cambiar'); ?>
<?php echo $this->load->view('v_clit_pdf_generar'); ?>
<?php echo $this->load->view('v_clit_print'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-clit');
	tab.add(clit.panel);
</script>