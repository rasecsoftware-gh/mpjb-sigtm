<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	crls = {};
	// general stores
	crls.planilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getPlanillaList',
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
				Ext.getCmp('crls_planilla_grid').getSelectionModel().selectAll();
			}
		}
	});

	crls.plani_traba_ingreso_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getPlaniIngresoList',
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
				Ext.getCmp('crls_plani_traba_ingreso_grid').getView().refresh();
				Ext.getCmp('crls_plani_traba_ingreso_grid').getSelectionModel().selectAll();
			}
		}
	});

	crls.plani_traba_descuento_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getPlaniDescuentoList',
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
				Ext.getCmp('crls_plani_traba_descuento_grid').getView().refresh();
				Ext.getCmp('crls_plani_traba_descuento_grid').getSelectionModel().selectAll();
			}
		}
	});

	crls.plani_traba_aporte_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getPlaniAporteList',
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
				Ext.getCmp('crls_plani_traba_aporte_grid').getView().refresh();
				Ext.getCmp('crls_plani_traba_aporte_grid').getSelectionModel().selectAll();
			}
		}
	});

	crls.servicio_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getServicioList',
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
				Ext.getCmp('crls_servicio_grid').getSelectionModel().selectAll();
			}
		}
	});
	
	crls.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'crls/getList',
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
				Ext.getCmp('crls_main_grid').getSelectionModel().selectAll();
			},
			beforeLoad: function(store){
				params = {
					search_by: Ext.getCmp('crls_cb_search_by').getValue(),
					search_text: Ext.getCmp('crls_search_text').getValue(),
					search_ubig: Ext.getCmp('crls_cb_search_ubig').getValue(),
				};
				store.getProxy().setExtraParam("search_by", params.search_by);
				store.getProxy().setExtraParam("search_text", params.search_text);
				store.getProxy().setExtraParam("search_ubig", params.search_ubig);
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_crls_list'); ?>
<?php echo $this->load->view('v_crls_plani_traba'); ?>
<?php echo $this->load->view('v_crls_print'); ?>
<?php //echo $this->load->view('v_crls_bien_search'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-crls');
	tab.add(crls.panel);
</script>