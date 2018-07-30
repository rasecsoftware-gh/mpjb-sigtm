<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	asistencia = {};
	// general stores
	asistencia.marcacion_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'asistencia/getMarcacionList',
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
				Ext.getCmp('asistencia_marcacion_grid').getSelectionModel().selectAll();
			}
		}
	});

	asistencia.asistencia_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'asistencia/getAsistenciaList',
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
				//Ext.getCmp('asistencia_plani_traba_ingreso_grid').getView().refresh();
				//Ext.getCmp('asistencia_plani_traba_ingreso_grid').getSelectionModel().selectAll();
			}
		}
	});
	
	asistencia.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'asistencia/getList',
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
				Ext.getCmp('asistencia_main_grid').getSelectionModel().selectAll();
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_asistencia_list'); ?>
<?php //echo $this->load->view('v_asistencia_plani_traba'); ?>
<?php //echo $this->load->view('v_asistencia_print'); ?>
<?php //echo $this->load->view('v_asistencia_bien_search'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-asistencia');
	tab.add(asistencia.panel);
</script>