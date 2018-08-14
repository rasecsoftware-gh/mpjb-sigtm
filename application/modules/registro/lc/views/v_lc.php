<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	lc = {};
	lc.title = 'Licencia de Conducir';
	// general stores
	lc.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getContribuyenteList',
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

	lc.plantilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getPlantillaList',
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

	lc.clase_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'B', desc: 'Clase B'}
		]
	});

	lc.categoria_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'I', desc: 'Categoria I'},
			{id: 'II-A', desc: 'Categoria II-A'},
			{id: 'II-B', desc: 'Categoria II-B'},
			{id: 'II-C', desc: 'Categoria II-C'},
		]
	});

	lc.estado_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getEstadoDocList',
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

	lc.doc_requisito_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getDocRequisitoList',
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

	lc.doc_estado_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getDocEstadoList',
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

	lc.form_editing = false;
	lc.lc_id_selected = 0;
	
	lc.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'lc/getList',
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
				if (lc.lc_id_selected > 0) {
					Ext.getCmp('lc_main_grid').getSelectionModel().select(
						lc.main_store.getAt(
							lc.main_store.find('lc_id', lc.lc_id_selected)
						)
					);
				} else {
					Ext.getCmp('lc_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_lc_list'); ?>
<?php echo $this->load->view('v_lc_new'); ?>
<?php echo $this->load->view('v_lc_edit'); ?>
<?php echo $this->load->view('v_lc_update'); ?>
<?php ////echo $this->load->view('v_lc_delete'); ?>
<?php echo $this->load->view('v_lc_doc_requisito_add'); ?>
<?php echo $this->load->view('v_lc_doc_requisito_edit'); ?>
<?php echo $this->load->view('v_lc_doc_requisito_delete'); ?>
<?php echo $this->load->view('v_lc_doc_estado_add'); ?>
<?php echo $this->load->view('v_lc_doc_estado_delete'); ?>
<?php echo $this->load->view('v_lc_plantilla_cambiar'); ?>
<?php echo $this->load->view('v_lc_pdf_generar'); ?>
<?php echo $this->load->view('v_lc_print'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-lc');
	tab.add(lc.panel);
</script>