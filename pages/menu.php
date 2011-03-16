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
	
	session_start();
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once 'CCirculation.inc.php';
	require_once '../pages/version.inc.php';
	
	$Circulation 	= new CCirculation();
	$extensions 	= $Circulation->getExtensionsByHookId('CF_MENU');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<style>
	#menu 
	{
     	margin-top: 10px;
     	margin-left: 5px;
     	width: 200px;
   	}
   	
	.submenu 
   	{
     	background: #e6e6e6;
     	border: 1px solid #c8c8c8;
     	margin-top: 8px;
   	}

   	.subhead 
   	{
     	cursor: pointer; 
     	border-bottom: 3px solid #ffa000;
     	color: #FFF;
     	padding: 4px 4px 4px 10px;
     	background-color: #8e8f90;
     	font-weight: bold;
     	font-size: 9pt;
     	text-align: left;
   	}

    a
    {
     	font-size: 12px;
     	color: #000;
   	}
   	a:visited
   	{
   		color: #000;
   	}
   	a:hover
   	{
   		text-decoration: none;
   	}   	
   	a img 
   	{
     	border: 0;
     	vertical-align: middle;
   	}
   	
	.submain ul 
   	{
     	list-style: none;
     	margin: 0;
     	padding: 0;
   	}
   	.submain ul li img 
   	{
     	padding-right: 5px;
     	vertical-align: middle;
   	}
   	.submain ul li 
   	{
     	padding: 2px 2px 2px 10px;
     	font-size: 12px;
     	border-bottom: 1px solid #bdbdbd;
   	}
   	
   	.submain ul li#inactive 
   	{
     	color: #999;
   	}
	</style>
	<script language="javascript">
	<!--
		function changeStyle(objLi, strAction)
		{
			switch(strAction)
			{
				case 'over':
					objLi.style.background = '#ffc056';
					objLi.style.cursor = 'pointer';
					break;
				case 'out':
					objLi.style.background = '#e6e6e6';
					break;
			}
		}
	//-->
	</script>
</head>
<body style="margin: 0px; padding: 0px;">





<div id="menu" align="left">
	<div class="submenu">
		<div class="subhead">
			<?php echo $GROUP_CIRCULATION;?>
		</div>
		<div class="submain">
		<ul>
			<?php
			if ($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] != 1)
			{
				?>
				<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<?php
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&archivemode=0&bFirstStart=true';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showcirculation.php?key='.$strEncyrptedParams;
					?>
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/circulate.png" hspace="2">
					<?php echo $MENU_CIRCULATION;?>
					</a>
				</li>
				<?php
			}
			
			if ($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] != 1)
			{
				?>
				<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<?php
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&archivemode=1&bFirstStart=true';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showcirculation.php?key='.$strEncyrptedParams;
					?>
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/new_archive2.gif" hspace="2">
					<?php echo $MENU_ARCHIVE;?>
					</a>
				</li>
				<?php
			}
			
			
			$strParams				= 'language='.$_REQUEST["language"].'&start=1&archivemode=0&bFirstStart=true&bOwnCirculations=1';
			$strEncyrptedParams		= $objURL->encryptURL($strParams);
			$strEncryptedLinkURL	= 'showcirculation.php?key='.$strEncyrptedParams;
			?>
			<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
			<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
			<img src="../images/own_circulations.gif" hspace="2">
			<?php echo $MENU_OWN_CIRCULATIONS ?>
			</a>
			</li>
			
			<?php
			// insert the extensions if exist
			$menuGroupExtensions = $Circulation->getMenuGroupExtensions('CF_GROUP_CIRCULATIONS', $extensions);
			
			if ($menuGroupExtensions)
			{
				$max = sizeof($menuGroupExtensions);
				for ($index = 0; $index < $max; $index++)
				{
					$path				= $menuGroupExtensions[$index]['path'];
					$MenuGroupExtension = $menuGroupExtensions[$index]['Extension'];
					$hooks 				= $MenuGroupExtension->hook;
					
					$max2 = sizeof($hooks);
					for ($index2 = 0; $index2 < $max2; $index2++)
					{
						$hook 				= $hooks[$index2];
						$group				= $hook->group;
						
						if ($group == 'CF_GROUP_CIRCULATIONS')
						{
							$title 				= $hook->title;
							$requiredAccesslevel= $hook->requiredAccesslevel;
							$destination		= $path.$hook->destination;
							$destination		.= $Circulation->getExtensionParams($hook);
							$icon				= $path.$hook->icon;
							
							if ($requiredAccesslevel & $_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'])
							{
								?>
								<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
									<a href="<?php echo $destination ?>" target="frame_details">
									<img src="<?php echo $icon ?>" hspace="2">
									<?php echo $title ?>
									</a>
								</li>
								<?php
							}
						}
					}					
				}
			}
			?>
			
		</ul>
		</div>
	</div>
	
	<div class="submenu">
		<div class="subhead">
			<?php echo $GROUP_MANAGEMENT;?>
		</div>
		<div class="submain">
		<ul>
				<?php 
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&sortby=name';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showfields.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/textfield_rename.gif" hspace="2">
					<?php echo $MENU_FIELDS;?>
					</a>
					</li>
					<?php
				}
				 
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&sortby=name';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showtemplates.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/template_type.gif" hspace="2">
					<?php echo $MENU_TEMPLATE;?>
					</a>
					</li>
					<?php
				}
				
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&sortby=name';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showmaillist.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/maillist.png" hspace="2">
					<?php echo $MENU_MAILINGLIST;?>
					</a>
					</li>
				<?php
				}
				
				
				
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					$strParams				= 'language='.$_REQUEST["language"];
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showstatistic.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/chart_bar.gif" hspace="2">
					<?php echo $MENU_STATISTIC;?>
					</a>
					</li>
					<?php
				}
				?>
				
				
				
				<?php
				// insert the extensions if exist
				$menuGroupExtensions = $Circulation->getMenuGroupExtensions('CF_GROUP_MANAGEMENT', $extensions);
				
				if ($menuGroupExtensions)
				{
					$max = sizeof($menuGroupExtensions);
					for ($index = 0; $index < $max; $index++)
					{
						$path				= $menuGroupExtensions[$index]['path'];
						$MenuGroupExtension = $menuGroupExtensions[$index]['Extension'];
						$hooks 				= $MenuGroupExtension->hook;
						
						$max2 = sizeof($hooks);
						for ($index2 = 0; $index2 < $max2; $index2++)
						{
							$hook 				= $hooks[$index2];
							$group				= $hook->group;
							
							if ($group == 'CF_GROUP_MANAGEMENT')
							{
								$title 				= $hook->title;
								$requiredAccesslevel= $hook->requiredAccesslevel;
								$destination		= $path.$hook->destination;
								$destination		.= $Circulation->getExtensionParams($hook);
								$icon				= $path.$hook->icon;
								
								if ($requiredAccesslevel & $_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'])
								{
									?>
									<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
										<a href="<?php echo $destination ?>" target="frame_details">
										<img src="<?php echo $icon ?>" hspace="2">
										<?php echo $title ?>
										</a>
									</li>
									<?php
								}
							}
						}					
					}
				}
				?>
		</ul>
		</div>
	</div>
	
	<div class="submenu">
		<div class="subhead">
			<?php echo $GROUP_ADMINISTRATION;?>
		</div>
		<div class="submain">
		<ul>
				<?php 
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2))
				{
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&sortby=strLastName';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'showuser.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/user_group.gif" hspace="2">
					<?php echo $MENU_USERMNG;?>
					</a>
					</li>
					<?php
				}
				
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2))
				{
					$strParams				= 'language='.$_REQUEST["language"];
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'editconfig.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/settings.gif" hspace="2">
					<?php echo $MENU_CONFIG;?>
					</a>
					</li>
					<?php
				}
				
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2))
				{
					$strParams				= 'language='.$_REQUEST["language"].'&userid='.$_SESSION["SESSION_CUTEFLOW_USERID"];
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'notification.edit.php?key='.$strEncyrptedParams;
					?>
					<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
					<a href="<?php echo $strEncryptedLinkURL ?>" target="frame_details">
					<img src="../images/icon_email.gif" hspace="2">
					<?php echo $MENU_NOTIFICATION;?>
					</a>
					</li>
					<?php
				}
				?>
				
				<?php
				// insert the extensions if exist
				$menuGroupExtensions = $Circulation->getMenuGroupExtensions('CF_GROUP_ADMINISTRATION', $extensions);
				
				if ($menuGroupExtensions)
				{
					$max = sizeof($menuGroupExtensions);
					for ($index = 0; $index < $max; $index++)
					{
						$path				= $menuGroupExtensions[$index]['path'];
						$MenuGroupExtension = $menuGroupExtensions[$index]['Extension'];
						$hooks 				= $MenuGroupExtension->hook;
						
						$max2 = sizeof($hooks);
						for ($index2 = 0; $index2 < $max2; $index2++)
						{
							$hook 				= $hooks[$index2];
							$group				= $hook->group;
							
							if ($group == 'CF_GROUP_ADMINISTRATION')
							{
								$title 				= $hook->title;
								$requiredAccesslevel= $hook->requiredAccesslevel;
								$destination		= $path.$hook->destination;
								$destination		.= $Circulation->getExtensionParams($hook);
								$icon				= $path.$hook->icon;
								
								if ($requiredAccesslevel & $_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'])
								{
									?>
									<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
										<a href="<?php echo $destination ?>" target="frame_details">
										<img src="<?php echo $icon ?>" hspace="2">
										<?php echo $title ?>
										</a>
									</li>
									<?php
								}
					
							}
						}					
					}
				}
				?>
		</ul>
		</div>
	</div>
	
	<?php
	// insert the extensions if exist (MENUGROUP)
	$menuGroupExtensions 	= $Circulation->getMenuGroupExtensions('CF_GROUP_USERDEFINED', $extensions);
	$userDefinedGroups		= $Circulation->getUserdefinedGroups($menuGroupExtensions);
	
	if ($menuGroupExtensions)
	{
		$max3 = sizeof($userDefinedGroups);
		for ($index3 = 0; $index3 < $max3; $index3++)
		{
			$userDefinedGroup = $userDefinedGroups[$index3];
			
			?>
			<div class="submenu">
				<div class="subhead">
					<?php echo $userDefinedGroup ?>
				</div>
				<div class="submain">
					<ul>
						<?php
						$max = sizeof($menuGroupExtensions);
						for ($index = 0; $index < $max; $index++)
						{
							$path				= $menuGroupExtensions[$index]['path'];
							$MenuGroupExtension = $menuGroupExtensions[$index]['Extension'];
							$hooks 				= $MenuGroupExtension->hook;
							
							$max2 = sizeof($hooks);
							for ($index2 = 0; $index2 < $max2; $index2++)
							{
								$hook 				= $hooks[$index2];
								$group				= ucwords($hook->group);
								
								if ($group == $userDefinedGroup)
								{	
									$title 				= $hook->title;
									$requiredAccesslevel= $hook->requiredAccesslevel;
									$destination		= $path.$hook->destination;
									$destination		.= $Circulation->getExtensionParams($hook);
									$icon				= $path.$hook->icon;
									
									if ($requiredAccesslevel & $_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'])
									{
										?>
										<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">
											<a href="<?php echo $destination ?>" target="frame_details">
											<img src="<?php echo $icon ?>" hspace="2">
											<?php echo $title ?>
											</a>
										</li>
										<?php
									}
								}
							}					
						}
						?>
					</ul>
				</div>
			</div>
		<?php
		}
	}
	?>
	
	<div class="submenu">
		<div class="subhead">
			<?php echo $GROUP_LOGOUT;?>
		</div>
		<div class="submain">
		<ul>
			<li onMouseOver="changeStyle(this, 'over');" onMouseOut="changeStyle(this, 'out');">				
				<?php
				$strParams				= 'language='.$_REQUEST["language"];
				$strEncyrptedParams		= $objURL->encryptURL($strParams);
				$strEncryptedLinkURL	= 'logout.php?key='.$strEncyrptedParams;
				?>
				<a href="<?php echo $strEncryptedLinkURL ?>" target="_top">
				<img src="../images/logout.gif" hspace="2">
				<?php echo $MENU_LOGOUT;?> (<?php echo $_SESSION["SESSION_CUTEFLOW_USERNAME"];?>)
				</a>					
			</li>
		</ul>
		</div>
	</div>
</div>


<div align="center" style="margin-top: 40px;">
	<strong style="font-size:8pt;font-weight:normal">powered by</strong><br>
	<a href="http://cuteflow.org" target="_blank"><img width="95" height="37" src="../images/cuteflow_logo_small.png" border="0" /></a><br>
	<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION;?></strong><br> 
</div>

</body>
</html>