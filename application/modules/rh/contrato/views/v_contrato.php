<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	contrato = {};
	// general stores
	contrato.tipo_contrato_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getTipoContratoList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 50,
		listeners: {
			load: function () {
			}
		}
	});

	contrato.repre_contrato_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getRepreContratoList',
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

	contrato.tipo_contrato_parent_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getTipoContratoParentList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 50,
		listeners: {
			load: function () {
			}
		}
	});

	contrato.adenda_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getAdendaList',
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

	contrato.tipo_adenda_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getTipoAdendaList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {}
		}
	});

	contrato.plantilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getPlantillaList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		pageSize: 50,
		listeners: {
			load: function (sender, records, successful, eOpts) {
				var c = Ext.getCmp('contrato_form_plantilla_id_field');
				if (records.length > 0 && Ext.isDefined(c)) {
					var record = records[0];
					c.setValue(record.get('plantilla_id'));
				}
			}
		}
	});

	contrato.select_contrato_id = 0;
	
	contrato.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getList',
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
				if (contrato.select_contrato_id > 0) {
					Ext.getCmp('contrato_main_grid').getSelectionModel().select(
						contrato.main_store.getAt(
							contrato.main_store.find('contrato_id', contrato.select_contrato_id)
						)
					);
				} else {
					Ext.getCmp('contrato_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_contrato_list'); ?>
<?php echo $this->load->view('v_contrato_new'); ?>
<?php echo $this->load->view('v_contrato_edit'); ?>
<?php echo $this->load->view('v_contrato_trabajador_search'); ?>
<?php echo $this->load->view('v_contrato_print'); ?>
<?php echo $this->load->view('v_contrato_emitir'); ?>
<?php echo $this->load->view('v_contrato_entregar'); ?>
<?php echo $this->load->view('v_contrato_anular'); ?>
<?php echo $this->load->view('v_contrato_cancelar_emitido'); ?>
<?php echo $this->load->view('v_contrato_cancelar_entregado'); ?>
<?php echo $this->load->view('v_contrato_cancelar_anulado'); ?>
<?php echo $this->load->view('v_repre_contrato_list'); ?>
<?php echo $this->load->view('v_plantilla_list'); ?>
<?php echo $this->load->view('v_contrato_generar_pdfs'); ?>
<?php echo $this->load->view('v_contrato_cancelar_generado'); ?>
<?php echo $this->load->view('v_contrato_regenerar_pdf'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-contrato');
	tab.add(contrato.panel);
</script>