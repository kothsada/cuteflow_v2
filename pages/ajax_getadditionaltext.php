<?php
	$_REQUEST['language'] = strip_tags($_POST['language']);

	require_once '../config/config.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once '../language_files/language.inc.php';
	
	header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");
	
	$additionalTextId	= strip_tags($_REQUEST['additionalTextId']);
	$action				= strip_tags($_REQUEST['action']);
	
	$AdditionalText 	= new Database_AdditionalText();
	
	switch ($action)
	{
		case 'delete':
			$AdditionalText->getById($additionalTextId);
			$AdditionalText->delete();
			break;
		case 'setDefault':
			$AdditionalText->setDefault($additionalTextId);
			break;
		case 'showValue':
			$AdditionalText->getById($additionalTextId);
			echo $AdditionalText->content;
			break;
	}
	
	if ($action != 'showValue')
	{
		?>
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td align="left" valign="top" width="16" style="border-bottom: 1px solid #ccc;">
					#
				</td>
				<td align="left" valign="top" style="padding-right: 5px; border-bottom: 1px solid #ccc;">
					<?php echo $CIRCORDER_NAME ?>
				</td>
				<td align="center" valign="top" style="padding-right: 5px; border-bottom: 1px solid #ccc;" width="40">
					<?php echo $DEFAULT ?>
				</td>
				<td align="left" valign="top" width="60" style="border-bottom: 1px solid #ccc;">
					<?php echo $CIRCDETAIL_COMMANDS ?>
				</td>
			</tr>
			<tr><td height="2"></td></tr>
			<?php
			$additionalTexts = $AdditionalText->getByParams();
			$max = ($additionalTexts) ? sizeof($additionalTexts) : 0;
			
			for ($index = 0; $index < $max; $index++)
			{
				$additionalText = $additionalTexts[$index];
				
				$id 		= $additionalText['id'];
				$title 		= $additionalText['title'];
				$content	= $additionalText['content'];
				$is_default	= $additionalText['is_default'];
				?>
				<tr>
					<td align="left" valign="top" style="padding-right: 5px;">
						<?php echo ($index+1) ?>
					</td>
					<td align="left" valign="top" style="padding-right: 5px;">
						<a href="Javascript: editAdditionalText(<?php echo $id ?>, 'show');"><?php echo $title ?></a>
					</td>
					<td align="center" valign="top" style="padding-right: 5px;">
						<?php if ($is_default) echo '<img src="../images/state_ok.png">' ?>
					</td>
					<td align="center" valign="top" style="padding-right: 5px;">
						<img src="../images/edit.png" style="cursor: pointer;" title="edit" onClick="editAdditionalText(<?php echo $id ?>, 'edit');">
						<img src="../images/edit_remove.gif" style="cursor: pointer;" title="delete" onClick="deleteAdditionalText(<?php echo $id ?>);">
						<img src="../images/tag_red.gif" style="cursor: pointer;" title="make default" onClick="setDefaultAdditionalText(<?php echo $id ?>);">
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	?>