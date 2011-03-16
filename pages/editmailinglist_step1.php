<?php
	/** Copyright (c) Timo Haberkern. All rights reserved.
	*
	* Redistribution and use in source and binary forms, with or without 
	* modification, are permitted provided that the following conditions are met:
	* 
	*  o Redistributions of source code must retain the above copyright notice, 
	*    this list of conditions and the following disclaimer. 
	*     
	*  o Redistributions in binary form must reproduce the above copyright notice, 
	*    this list of conditions and the following disclaimer in the documentation 
	*    and/or other materials provided with the distribution. 
	*     
	*  o Neither the name of Timo Haberkern nor the names of 
	*    its contributors may be used to endorse or promote products derived 
	*    from this software without specific prior written permission. 
	*     
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
	* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
	* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
	* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
	* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
	* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
	* OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
	* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
	* OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
	* EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	*/

	include_once ("../language_files/language.inc.php");
	include_once ("../config/config.inc.php");
	
    function getMaxProcessId($nFormId, $Connection)
    {
        $query = "SELECT MAX(nID) FROM `cf_circulationprocess` WHERE `nCirculationFormId`=".$nFormId;
		$nResult = mysql_query($query, $Connection);

        if ($nResult)
        {
            if (mysql_num_rows($nResult) > 0)
            {
                $arrRow = mysql_fetch_array($nResult);
                
                if ($arrRow)
                {
                    $nMaxId = $arrRow[0];
                    return $nMaxId;
                }           
            }   
        }
    }

    function getProcessInformation($nMaxId, $Connection)
    {
        $query = "SELECT * FROM `cf_circulationprocess` WHERE `nID`=".$nMaxId;
        $nResult = mysql_query($query, $Connection);

        if ($nResult)
        {
            if (mysql_num_rows($nResult) > 0)
            {
                $arrRow = mysql_fetch_array($nResult);
                
                if ($arrRow)
                {
                    return $arrRow;
                }           
            }   
        }        
    }

  	//--- load data from database
	$strTemplateName = "&nbsp;";
	$templateid = -1;
	$bHasRunningCirculations = false;
	
	//--- open database
    $nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
    
    if ($nConnection)
    {
    	if (mysql_select_db($DATABASE_DB, $nConnection))
    	{
    		//--- get all possible sender
	        $all_user = User::create()->findAll();
	        
	     	if ($listid != -1)
			{
				// get all current allowed senders of this mailing list
	    		$allowed_sender = MailingList::create()->getAllowedSender($_REQUEST["listid"]);
	    		$available_user = array_diff_key($all_user, $allowed_sender);
	    		
    			//--- read the values of the user
				$strQuery = "SELECT * FROM cf_mailinglist WHERE nID = ".$_REQUEST["listid"];
				$nResult = mysql_query($strQuery, $nConnection);
        
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
        				$arrRow = mysql_fetch_array($nResult);
        				$templateid = $arrRow["nTemplateId"];
						$strName = $arrRow["strName"];	
						$list_permissions = $arrRow['permissions'];					
					}
				}
				
				$strQuery = "SELECT * FROM cf_formtemplate WHERE nID = ".$templateid;
				$nResult = mysql_query($strQuery, $nConnection);
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
        				$arrRow = mysql_fetch_array($nResult);
        				$strTemplateName = $arrRow["strName"];
					}
				}
				
    			$strQuery = "SELECT * FROM cf_formtemplate WHERE bDeleted=0 ORDER BY strName ASC";
                $nResult = mysql_query($strQuery, $nConnection);
                            
                if ($nResult) {
              		if (mysql_num_rows($nResult) > 0) {
                    	while (	$arrRow = mysql_fetch_array($nResult)) {
                        	$templates[] = $arrRow;
                        }		
                    }
                }

				$strQuery = "SELECT * FROM cf_circulationform WHERE nMailingListId = ".$_REQUEST["listid"]." AND bDeleted = 0";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
						while (	$arrRow = mysql_fetch_array($nResult))
						{
							$arrCirculations[] = $arrRow;
						}
					}
				}
				
				if (isset($arrCirculations))
				{
					foreach ($arrCirculations as $arrCirculation)
					{
						$nMaxId = getMaxProcessId($arrCirculation["nID"], $nConnection);
						$arrProcessInformation = getProcessInformation($nMaxId, $nConnection);
	                    
						//echo "State:".$arrProcessInformation["nDecissionState"]."<br>";
						if ( ($arrProcessInformation["nDecissionState"] != 16) &&
							 ($arrProcessInformation["nDecissionState"] != 1) )
						{
							$bHasRunningCirculations = true;
						}
					}
				}
			}
			else {
				$allowed_sender = array();
	    		$available_user = $all_user;
			}
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="../lib/extjs/css/ext-all.css" type="text/css">
		<link rel="stylesheet" href="format.css" type="text/css">
	<script src="../lib/extjs/ext-base.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/extjs/ext-all.js" type="text/javascript" language="JavaScript"></script>	
	
	<script language="JavaScript">
	<!--
		function setProps()
		{
			var objForm = document.forms["EditMailingList"];
			
			objForm.strName.required = 1;
			objForm.strName.err = "<?php echo $MAILLIST_NEW_ERROR_NAME;?>";
		}
		
		function validate(objForm)
		{
			bResult = jsVal(objForm);
			
			//--- additional validation
			if (bResult == true)
			{
				if (objForm.templateid.value == -1)
				{
					alert ('<?php echo str_replace("'", "\'", $MAILLIST_NEW_ERROR_TEMPLATE);?>');
					bResult = false;
				}
			}
						
			return bResult	;
		}

		function refreshAllowedSender(store) {
			allowed_sender_string = "";
			for (i=0; i< store.getCount(); i++) {
				record = store.getAt(i);

				allowed_sender_string += record.id+"&&&";
			}

			document.getElementById('allowed-sender-string').value = allowed_sender_string;	

			return true;
		}

		Ext.onReady(function(){
			Ext.QuickTips.init();
			
			var availableUser = {
					records : [
						<?php $i = 0; ?>
						<?php foreach ($available_user as $user): ?>
							<?php echo $i++ != 0 ? ',':''; ?>
							{ name: "<?php echo $user['strFirstName'].' '.$user['strLastName'];?>", id: <?php echo $user['nID']; ?> }
						<?php endforeach; ?>
					]
				};

			var allowedUser = {
					records : [
						<?php $i = 0; ?>
						<?php foreach ($allowed_sender as $user): ?>
							<?php echo $i++ != 0 ? ',':''; ?>
							{ name: "<?php echo $user['strFirstName'].' '.$user['strLastName'];?>", id: <?php echo $user['nID']; ?> }
						<?php endforeach; ?>
					]
				};

			// Generic fields array to use in both store defs.
			var fields = [
				{name: 'name', mapping : 'name'}
			];

		    // create the data store
		    var availableUserGridStore = new Ext.data.JsonStore({
		        fields : fields,
				data   : availableUser,
				root   : 'records'
		    });

		 	// create the data store
		    var allowedUserGridStore = new Ext.data.JsonStore({
		        fields : fields,
				data   : allowedUser,
				root   : 'records',
				listeners: {
					'add' : function(store) {
						refreshAllowedSender(store);
					},
					'remove' : function(store) {
						refreshAllowedSender(store);
					}
		    	}
		    });


			// Column Model shortcut array
			var cols = [
				{ id : 'name', header: "<?php echo $MENU_USERMNG; ?>", width: 160, sortable: true, dataIndex: 'name'},
			];

			// declare the source Grid
		    var availableUserGrid = new Ext.grid.GridPanel({
				ddGroup          : 'secondGridDDGroup',
		        store            : availableUserGridStore,
		        columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '<?php echo $MAILINGLIST_EDIT_AVAILABLE_USER;?>'
		    });

		    // create the destination Grid
		    var allowedUserGrid = new Ext.grid.GridPanel({
			ddGroup          : 'firstGridDDGroup',
		        store            : allowedUserGridStore,
		        columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '<?php echo $ALLOWED_SENDER;?>'
		    });


			//Simple 'border layout' panel to house both grids
			var displayPanel = new Ext.Panel({
				width        : 420,
				height       : 250,
				layout       : 'hbox',
				renderTo     : 'allowed-sender',
				defaults     : { flex : 1 }, //auto stretch
				layoutConfig : { align : 'stretch' },
				items        : [
					availableUserGrid,
					allowedUserGrid
				],
				bbar    : [
					'->', // Fill
					{
						text    : 'Reset both grids',
						handler : function() {
							//refresh source grid
							availableUserGridStore.loadData(allUser);

							//purge destination grid
							allowedUserGridStore.removeAll();
						}
					}
				]
			});

			// used to add records to the destination stores
			var blankRecord =  Ext.data.Record.create(fields);

	        /****
	        * Setup Drop Targets
	        ***/
	        // This will make sure we only drop to the  view scroller element
	        var firstGridDropTargetEl =  availableUserGrid.getView().scroller.dom;
	        var firstGridDropTarget = new Ext.dd.DropTarget(firstGridDropTargetEl, {
	                ddGroup    : 'firstGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                        var records =  ddSource.dragData.selections;
	                        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        availableUserGrid.store.add(records);
	                        availableUserGrid.store.sort('name', 'ASC');
	                        return true
	                }
	        });


	        // This will make sure we only drop to the view scroller element
	        var secondGridDropTargetEl = allowedUserGrid.getView().scroller.dom;
	        var secondGridDropTarget = new Ext.dd.DropTarget(secondGridDropTargetEl, {
	                ddGroup    : 'secondGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                        var records =  ddSource.dragData.selections;
	                        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        allowedUserGrid.store.add(records);
	                        allowedUserGrid.store.sort('name', 'ASC');
	                        return true
	                }
	        });
						
		});
	-->
	</script>
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>	
</head>

<body onload="setProps()"><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_MAILINGLIST;?>
</span><br><br>

		<?php
			if ($bHasRunningCirculations == true)
			{
		?>
				<div width="820px" class="flash error">
					<?php echo $MAILLIST_EDIT_ERROR;?>
				</div>
				<br>
				<br>
		<?php
			}
		?>
	
	<form action="editmailinglist_step2.php" id="EditMailingList" name="EditMailingList" onsubmit="return validate(this);">
		<table width="820" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3">
			<tr>
				<td class="table_header" colspan="2">
					<?php echo $MAILLIST_EDIT_FORM_HEADER;?> <?php if ($strName) echo '"'.$strName.'"' ?>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
            <tr>
				<td width="180"><?php echo $MAILLIST_MNGT_NAME;?></td>
				<td><input id="strName" Name="strName" type="text" class="InputText" style="width:150px;" value="<?php echo $strName;?>"></td>
			</tr>
         	<tr>
				<td colspan="2" height="10px"></td>
			</tr>
	        <tr>
				<td><?php echo $MAILLIST_EDIT_FORM_TEMPLATE;?></td>
				<td>
					<input type="hidden" value="<?php echo $templateid;?>" >
					<select id="templateid" name="templateid">
						<?php foreach ($templates as $template): ?>
							<option style="width:146px;" value="<?php echo $template['nID']; ?>" <?php $templateid == $template['nID']?'selected="selected"':'';?>><?php echo $template['strName']?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr><td height="10" colspan="2"><hr size=1"/></td></tr>
			<tr>
				<td valign="top"><?php echo $ALLOWED_SENDER;?>:</td>
				<td >
					<div id="allowed-sender"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr>
				<td valign="top"><?php echo $PERMISSIONS;?>:</td>
				<td >

					<table cellpadding="2" cellspacing="0" style="border: 1px solid rgb(200, 200, 200);" width="90%">
						<thead>
							<tr>
								<th class="table_header">&nbsp;</th>
								<th class="table_header" align="center"><?php echo $ADMIN; ?></th>
								<th class="table_header" align="center"><?php echo $ALLOWED_SENDER; ?></th>
								<th class="table_header" align="center"><?php echo $ALL_SENDER; ?></th>
								<th class="table_header" align="center"><?php echo $USER_ACCESSLEVEL_RECEIVER; ?></th>
								<th class="table_header">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr class="rowEven">
								<td><?php echo $RIGHT_DELETE_CIRCULATION;?></td>
								<td align="center"><input type="checkbox" name="permission[delete][admin]" value="1" <?php echo ($list_permissions & 1) == 1 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[delete][allowedsender]" value="1" <?php echo ($list_permissions & 16) == 16 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[delete][allsender]" value="1" <?php echo ($list_permissions & 256) == 256 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[delete][receiver]" value="1" <?php echo ($list_permissions & 4096) == 4096 ? 'checked="checked"' : '';?>/></td>
								<td><a href="#" onclick="checkAll('delete')"><?php echo $SELECT_ALL; ?></a></td>
							</tr>
							<tr class="rowUneven">
								<td><?php echo $RIGHT_ARCHIVE_CIRCULATION;?></td>
								<td align="center"><input type="checkbox" name="permission[archive][admin]" value="1" <?php echo ($list_permissions & 2) == 2 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[archive][allowedsender]" value="1" <?php echo ($list_permissions & 32) == 32 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[archive][allsender]" value="1" <?php echo ($list_permissions & 512) == 512 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[archive][receiver]" value="1" <?php echo ($list_permissions & 8192) == 8192 ? 'checked="checked"' : '';?>/></td>
								<td><a href="#" onclick="checkAll('archive')"><?php echo $SELECT_ALL; ?></a></td>
							</tr>
							<tr class="rowEven">
								<td><?php echo $RIGHT_RESTARTSTOP_CIRCULATION;?></td>
								<td align="center"><input type="checkbox" name="permission[stop][admin]" value="1" <?php echo ($list_permissions & 4) == 4 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[stop][allowedsender]" value="1" <?php echo ($list_permissions & 64) == 64 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[stop][allsender]" value="1" <?php echo ($list_permissions & 1024) == 1024 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[stop][receiver]" value="1" <?php echo ($list_permissions & 16384) == 16384 ? 'checked="checked"' : '';?>/></td>
								<td><a href="#" onclick="checkAll('stop')"><?php echo $SELECT_ALL; ?></a></td>
							</tr>
							<tr class="rowUneven">
								<td><?php echo $RIGHT_SHOWDETAILS_CIRCULATION;?></td>
								<td align="center"><input type="checkbox" name="permission[details][admin]" value="1" <?php echo ($list_permissions & 8) == 8 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[details][allowedsender]" value="1" <?php echo ($list_permissions & 128) == 128 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[details][allsender]" value="1" <?php echo ($list_permissions & 2048) == 2048 ? 'checked="checked"' : '';?>/></td>
								<td align="center"><input type="checkbox" name="permission[details][receiver]" value="1" <?php echo ($list_permissions & 32768) == 32768 ? 'checked="checked"' : '';?>/></td>
								<td><a href="#" onclick="checkAll('details')"><?php echo $SELECT_ALL; ?></a></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
		</table>
    		
    	<table cellspacing="0" cellpadding="3" align="left" width="820">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_CANCEL;?>" onclick="history.back()">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $BTN_NEXT;?> >>" class="Button">
			</td>
		</tr>
		</table>
    		
	<input type="hidden" value="<?php echo $_REQUEST["listid"];?>" id="listid" name="listid">
	<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
	<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sort" name="sort">
	<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">
	
	<?php 
		$allowed_sender_string = "";
		foreach ($allowed_sender as $user) {
			$allowed_sender_string .= "{$user['nID']}&&&";
		}	
	?>
	<input type="hidden" value="<?php echo $allowed_sender_string;?>" id="allowed-sender-string" name="allowed_sender"/>
	</form>

</body>
</html>