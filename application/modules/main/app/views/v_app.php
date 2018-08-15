<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
	<title>Sistema de Informacion y Gestion de Transporte Municipal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="shortcut icon" href="sigtm.ico"/>
	<link rel="stylesheet" type="text/css" href="../ext-6/build/classic/theme-neptune/resources/theme-neptune-all.css">
	<link rel="stylesheet" type="text/css" href="../ext-6/build/packages/charts/classic/neptune/resources/charts-all.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('tools/css/icons.css')?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('tools/css/app.css')?>">
	<script type="text/javascript" src="../ext-6/build/ext-all-debug.js"></script>
	<script type="text/javascript" src="<?php echo base_url('tools/jquery/jquery-2.1.4.js')?>"></script>
	<script type="text/javascript" src="../ext-6/build/classic/theme-neptune/theme-neptune.js"></script>
	<script type="text/javascript" src="../ext-6/build/packages/charts/classic/charts.js"></script>
	<script type="text/javascript" src="../ext-6/build/classic/locale/locale-es-debug.js"></script>
	<style>
	/* toolbar */
	#app-mainmenu {
	}
	
	#app-mainmenu .x-btn-default-toolbar-small {
		background: transparent!important;
		border: none;
	}

	#app-mainmenu .x-btn .x-btn-inner-default-toolbar-small {
		color: white!important;
	}

	#app-mainmenu .x-btn-inner-default-toolbar-small {
		font-weight: normal!important;
	}

	#app-mainmenu label {
		color: white!important;
	}

	</style>
	<script type ="text/javascript">
		Ext.onReady(function(){
			Ext.tip.QuickTipManager.init();
			Ext.create('Ext.container.Viewport',{
				layout: 'border',
				renderTo: Ext.getBody(),
				items:[{
					id: 'app-mainmenu',
					xtype: 'toolbar',
					region:'north',
					style: 'background-color: #157fcc!important;',
					items: [{
						xtype: 'label',
						text: 'Sistema de Informacion y Gestion de Transporte Municipal',
						style: 'color: white; font-weight: 600;'
					},' ',{
						text: 'Sistema',
						menu: [{
							text: 'Control de Cuentas de Usuario', 
							module: {
								title: 'Control de Cuentas de Usuario',
								name: 'ccu',
								url: 'ccu/ccu'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('sys.ccu'); ?>
						},{
							text: 'Salir', 
							handler: function () {
								window.location.href = 'session/Logout';
							}
						}]
					},{
						text: 'Mantenimiento',
						menu: [{
							text: 'Requisitos por Tipo de Documento', 
							module: {
								title: 'Requisitos por Tipo de Documento',
								name: 'tdr',
								url: 'tdr/tdr'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('mantenimiento.tdr'); ?>
						},{
							text: 'Estados por Tipo de Documento', 
							module: {
								title: 'Estados por Tipo de Documento',
								name: 'estado_doc',
								url: 'estado_doc/estado_doc'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('mantenimiento.estado_doc'); ?>
						},{
							text: 'Plantillas de Documento', 
							module: {
								title: 'Plantillas de Documento',
								name: 'plantilla',
								url: 'plantilla/plantilla'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('mantenimiento.plantilla'); ?>
						}]
					},{
						text: 'Registro', 
						menu:[{
							text: 'Contribuyentes &nbsp;', 
							module: {
								title: 'Contribuyentes',
								name: 'contribuyente',
								url: 'contribuyente/contribuyente'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('registro.contribuyente'); ?>
						},'-',{
							text: 'Constancia de Libre Infraccion de Transito &nbsp;', 
							module: {
								title: 'Constancia de Libre Infraccion de Transito',
								name: 'clit',
								url: 'clit/clit'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('registro.clit'); ?>
						},{
							text: 'Permiso de Servicio Publico &nbsp;', 
							module: {
								title: 'Permiso de Servicio Publico',
								name: 'psp',
								url: 'psp/psp'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('registro.psp'); ?>
						},{
							text: 'Licencias de Conducir &nbsp;', 
							module: {
								title: 'Licencias de Conducir',
								name: 'lc',
								url: 'lc/lc'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('registro.lc'); ?>
						},{
							text: 'Constancia de Autorizaciones Temporales &nbsp;', 
							module: {
								title: 'Constancia de Autorizaciones Temporales',
								name: 'cat',
								url: 'cat/cat'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('registro.at'); ?>
						}]
					},{
						text: 'Reportes', 
						menu:[{
							text: 'Reporte de Ficha Tecnica de Transporte&nbsp;', 
							module: {
								name: 'rep_ftt',
								url: '<?php echo base_url('rep_ftt')?>',
								open_as: 'window'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('reporte.con_ftt'); ?>
						},'-',{
							text: 'Reporte de Contribuyentes &nbsp;', 
							module: {
								title: 'Reporte de Contribuyentes',
								name: 'rep_contribuyente',
								url: 'rep_contribuyente/rep_contribuyente'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('cr.rep_contribuyente'); ?>
						},{
							text: 'Reporte de Constancias de Libre Infraccion de Transito &nbsp;', 
							module: {
								title: 'Reporte de Constancias de Libre Infraccion de Transito',
								name: 'rep_clit',
								url: 'rep_clit/rep_clit'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('cr.rep_clit'); ?>
						},{
							text: 'Reporte de Permisos de Servicio Publico &nbsp;', 
							module: {
								title: 'Reporte de Permisos de Servicio Publico',
								name: 'rep_psp',
								url: 'rep_psp/rep_psp'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('cr.rep_psp'); ?>
						},{
							text: 'Reporte de Licencias de Conducir &nbsp;', 
							module: {
								title: 'Reporte de Licencias de Conducir',
								name: 'rep_lc',
								url: 'rep_lc/rep_lc'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('cr.rep_lc'); ?>
						},{
							text: 'Reporte de Autorizaciones Temporales &nbsp;', 
							module: {
								title: 'Reporte de Autorizaciones Temporales',
								name: 'rep_at',
								url: 'rep_at/rep_at'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('cr.rep_at'); ?>
						}]
					},{
						text: '?'
					},'->',{
			        	text: '<?php echo $this->session->userdata("username");?>', //plain: true,
			        	xtype: 'button', 
			        	menu:[{
			        		text:'Informacion' //, handler: sys_menu_item_handler
			        	},{
			        		text:'Cerrar sesion', handler: function () {
			        			window.location.href = "session/Logout";
			        		}
			        	}]
			        }]
				},{
					xtype: 'tabpanel',
					id: 'tab-main',
					region: 'center',
					items: {
						title:'Inicio',
						bodyPadding: 20,
        				html : 'Bienvenido!'
					}
				}]
			});
		});
		
		function sys_add_tab (title, name, url) {
			var tab = Ext.getCmp('tab-main');
            
            if (Ext.ComponentManager.get('tab-'+name) == undefined){
                tab.add({
                    xtype:'panel',
                    title: title,
                    id: 'tab-' + name,
                    closable: true,
                    layout: 'border',
                    listeners: {
                        'render': function(t,eOpts) {
                            $.post(url, function(data) {
                                $('#appLoader').html(data);
                            });
                        }
                    }
                });
            }
            tab.setActiveTab('tab-' + name);
		};

		function sys_menu_item_handler (item) {
	        var m = item.module;
	        var open_as = Ext.isDefined(m.open_as)?m.open_as:'tab';
	        switch (open_as) {
	        	case 'tab':
	        		sys_add_tab(m.title, m.name, m.url);
	        		break;
	        	case 'window':
	        		window.open(m.url, '_blank');
	        		break;
	        }
	        
	        //Ext.Msg.alert('Item', item.text);
	    };

	    function sys_focus(target_id) {
	    	if (Ext.isString(target_id)) {
	    		var target = Ext.getCmp(target_id);
				if (Ext.isDefined(target)) {
					target.focus();
					console.log(target_id+' focused!');
					return true;
				} 
			} 
			return false;
	    };
	    function sys_storeLoadMonitor (storeList, loadHandler) {
			var _loadCheck = function () {
				console.log('check store loading!');
				var count = 0;
				for (var i=0; i<storeList.length; i++) {
					var _store = storeList[i];
					if (_store.isLoading()) {
						count++;
						console.log('load_count: '+count);
					} else {
						console.log('loaded!');
					}
				}
				if (count == 0) {
					loadHandler();
				} else {
					Ext.defer(_loadCheck, 500);
				}
			};
			Ext.defer(_loadCheck, 500);
		};
	</script>
</head>
<body>
	<div id="appLoader"></div>
</body>
</html>