<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	contribuyente = {};
	// general stores
	contribuyente.tipo_persona_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contribuyente/getTipoPersonaList',
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

	contribuyente.tipo_doc_identidad_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contribuyente/getTipoDocIdentidadList',
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

	contribuyente.ubigeo_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contribuyente/getUbigeoList',
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

	contribuyente.form_editing = false;
	contribuyente.contribuyente_id_selected = 0;
	
	contribuyente.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contribuyente/getList',
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
				if (contribuyente.contribuyente_id_selected > 0) {
					Ext.getCmp('contribuyente_main_grid').getSelectionModel().select(
						contribuyente.main_store.getAt(
							contribuyente.main_store.find('contribuyente_id', contribuyente.contribuyente_id_selected)
						)
					);
				} else {
					Ext.getCmp('contribuyente_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_contribuyente_list'); ?>
<?php echo $this->load->view('v_contribuyente_new'); ?>
<?php echo $this->load->view('v_contribuyente_edit'); ?>
<?php //echo $this->load->view('v_contribuyente_trabajador_search'); ?>
<?php //echo $this->load->view('v_contribuyente_print'); ?>
<?php //echo $this->load->view('v_contribuyente_emitir'); ?>
<?php //echo $this->load->view('v_contribuyente_entregar'); ?>
<?php //echo $this->load->view('v_contribuyente_anular'); ?>
<?php //echo $this->load->view('v_contribuyente_cancelar_emitido'); ?>
<?php //echo $this->load->view('v_contribuyente_cancelar_entregado'); ?>
<?php //echo $this->load->view('v_contribuyente_cancelar_anulado'); ?>
<?php //echo $this->load->view('v_repre_contribuyente_list'); ?>
<?php //echo $this->load->view('v_plantilla_list'); ?>
<?php //echo $this->load->view('v_contribuyente_generar_pdfs'); ?>
<?php //echo $this->load->view('v_contribuyente_cancelar_generado'); ?>
<?php //echo $this->load->view('v_contribuyente_regenerar_pdf'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-contribuyente');
	tab.add(contribuyente.panel);
</script>