<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	cat = {};
	cat.title = 'Constancia de Autorizacion Temporal'
	// general stores
	cat.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getContribuyenteList',
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

	cat.plantilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getPlantillaList',
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

	/*
	cat.resultado_store = Ext.create("Ext.data.Store", {
		data : [
			{id: 'PENDIENTE', desc: 'Pendiente'},
			{id: 'SI', desc: 'Si'},
			{id: 'NO', desc: 'No'}
		]
	});
	*/
	cat.estado_doc_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getEstadoDocList',
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

	cat.doc_requisito_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getDocRequisitoList',
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

	cat.doc_estado_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getDocEstadoList',
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

	cat.form_editing = false;
	cat.cat_id_selected = 0;
	
	cat.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getList',
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
				if (cat.cat_id_selected > 0) {
					Ext.getCmp('cat_main_grid').getSelectionModel().select(
						cat.main_store.getAt(
							cat.main_store.find('cat_id', cat.cat_id_selected)
						)
					);
				} else {
					Ext.getCmp('cat_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_cat_list'); ?>
<?php echo $this->load->view('v_cat_new'); ?>
<?php echo $this->load->view('v_cat_edit'); ?>
<?php //echo $this->load->view('v_cat_delete'); ?>
<?php echo $this->load->view('v_cat_doc_requisito_add'); ?>
<?php echo $this->load->view('v_cat_doc_requisito_edit'); ?>
<?php echo $this->load->view('v_cat_doc_requisito_delete'); ?>
<?php echo $this->load->view('v_cat_doc_estado_add'); ?>
<?php echo $this->load->view('v_cat_doc_estado_delete'); ?>
<?php echo $this->load->view('v_cat_vehiculo_list'); ?>
<?php echo $this->load->view('v_cat_plantilla_cambiar'); ?>
<?php echo $this->load->view('v_cat_pdf_generar'); ?>
<?php echo $this->load->view('v_cat_print'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-cat');
	tab.add(cat.panel);
</script>