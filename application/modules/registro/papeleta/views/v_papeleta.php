<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	papeleta = {};
	papeleta.title = 'Papeleta';
	// general stores
	papeleta.contribuyente_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'papeleta/getContribuyenteList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
			}
		}
	});
	papeleta.tipo_infraccion_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'papeleta/getTipoInfraccionList',
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

	papeleta.medida_preventiva_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'papeleta/getMedidaPreventivaList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	papeleta.estado_papeleta_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'papeleta/getEstadoPapeletaList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function () {
			}
		}
	});

	
	papeleta.form_editing = false;
	papeleta.papeleta_id_selected = 0;
	
	papeleta.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'papeleta/getList',
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
				if (papeleta.papeleta_id_selected > 0) {
					Ext.getCmp('papeleta_main_grid').getSelectionModel().select(
						papeleta.main_store.getAt(
							papeleta.main_store.find('papeleta_id', papeleta.papeleta_id_selected)
						)
					);
				} else {
					Ext.getCmp('papeleta_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_papeleta_list'); ?>
<?php echo $this->load->view('v_papeleta_new'); ?>
<?php echo $this->load->view('v_papeleta_edit'); ?>
<?php echo $this->load->view('v_papeleta_delete'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-papeleta');
	tab.add(papeleta.panel);
</script>