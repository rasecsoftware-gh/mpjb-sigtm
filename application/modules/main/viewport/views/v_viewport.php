<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
	<title>Sistema de Informacion y Gestion de Transporte Municipal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
						text: 'Registro', 
						menu:[{
							text: 'Contribuyentes &nbsp;', 
							module: {
								title: 'Contribuyentes',
								name: 'contribuyente',
								url: 'contribuyente/contribuyente'
							},
							handler: sys_menu_item_handler,
							hidden: !<?php echo sys_session_hasRoleToString('contribuyente'); ?>
						},'-',{
							text: 'Constancia de Libre Infraccion de Transito &nbsp;', 
							module: {
								title: 'Constancia de Libre Infraccion de Transito',
								name: 'clit',
								url: 'clit/clit'
							},
							handler: sys_menu_item_handler
						},{
							text: 'Permiso de Servicio Publico &nbsp;', 
							module: {
								title: 'Permiso de Servicio Publico',
								name: 'psp',
								url: 'psp/psp'
							},
							handler: sys_menu_item_handler
						},{
							text: 'Permiso de Servicio Publico &nbsp;', 
							module: {
								title: 'Permiso de Servicio Publico',
								name: 'psp',
								url: 'psp/psp'
							},
							handler: sys_menu_item_handler
						}]
					},{
						text: 'Consultas y Reportes', 
						menu:[{
							text: 'Reporte de Contribuyentes &nbsp;', 
							module: {
								title: 'Reporte de Contribuyentes',
								name: 'rep_contribuyente',
								url: 'rep_contribuyente/rep_contribuyente'
							},
							handler: sys_menu_item_handler
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
	        sys_add_tab(m.title, m.name, m.url);
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