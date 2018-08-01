<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	clit = {};
	// general stores
	clit.tipo_persona_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getTipoPersonaList',
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

	clit.tipo_doc_identidad_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getTipoDocIdentidadList',
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

	clit.ubigeo_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'clit/getUbigeoList',
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
<?php echo $this->load->view('v_clit_delete'); ?>
<?php echo $this->load->view('v_clit_activar'); ?>
<?php echo $this->load->view('v_clit_inactivar'); ?>
<?php //echo $this->load->view('v_clit_entregar'); ?>
<?php //echo $this->load->view('v_clit_anular'); ?>
<?php echo $this->load->view('syslog/v_syslog'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-clit');
	tab.add(clit.panel);
</script>