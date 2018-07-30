<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	ctl = {};
	// general stores
	ctl.planilla_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getPlanillaList',
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
				Ext.getCmp('ctl_planilla_grid').getSelectionModel().selectAll();
			}
		}
	});

	ctl.plani_traba_ingreso_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getPlaniIngresoList',
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
				Ext.getCmp('ctl_plani_traba_ingreso_grid').getView().refresh();
				Ext.getCmp('ctl_plani_traba_ingreso_grid').getSelectionModel().selectAll();
			}
		}
	});

	ctl.plani_traba_descuento_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getPlaniDescuentoList',
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
				Ext.getCmp('ctl_plani_traba_descuento_grid').getView().refresh();
				Ext.getCmp('ctl_plani_traba_descuento_grid').getSelectionModel().selectAll();
			}
		}
	});

	ctl.plani_traba_aporte_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getPlaniAporteList',
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
				Ext.getCmp('ctl_plani_traba_aporte_grid').getView().refresh();
				Ext.getCmp('ctl_plani_traba_aporte_grid').getSelectionModel().selectAll();
			}
		}
	});

	ctl.servicio_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getServicioList',
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
				Ext.getCmp('ctl_servicio_grid').getSelectionModel().selectAll();
			}
		}
	});
	ctl.rkey = '';
	ctl.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ctl/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			},
			timeout: 300000
		},
		autoLoad: false,
		pageSize: 50,
		listeners: {
			load: function (ths, records, successful, operation, eOpts) {
				var data = Ext.JSON.decode(operation.getResponse().responseText);
				ctl.rkey = data.rkey;
				Ext.getCmp('ctl_main_grid').getSelectionModel().selectAll();
			},
			beforeLoad: function(store){
				params = {
					search_todos: Ext.getCmp('ctl_chb_search_todos').getValue(),
					search_activos: Ext.getCmp('ctl_chb_search_activos').getValue(),
				};
				store.getProxy().setExtraParam("search_todos", params.search_todos);
				store.getProxy().setExtraParam("search_activos", params.search_activos);
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_ctl_list'); ?>
<?php echo $this->load->view('v_ctl_plani_traba'); ?>
<?php echo $this->load->view('v_ctl_print'); ?>
<?php echo $this->load->view('v_ctl_print_detalle'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-ctl');
	tab.add(ctl.panel);
</script>