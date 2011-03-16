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

// checking user credentials
if ( ($_SESSION['SESSION_CUTEFLOW_USERID'] == '') || ($_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'] != 2) ) {
	header('location: missing_credentials.php?language='.$_REQUEST['language']);
}

require_once '../language_files/language.inc.php';
require_once 'CCirculation.inc.php';

$objCirculation = new CCirculation();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="format.css" type="text/css">
	<script language="JavaScript">
	<!--
		var arrSubstitutes;
		
		function changeSubstitute(strAction)
		{
			if ((arrSubstitutes.length == 1) && (strAction == 'remove'))
			{
				strDiv = '<table cellpadding="0" cellspacing="0">';
				strDiv += '	<tr>';
				strDiv += '		<td valign="top">';
				strDiv += '			<input id="SubstituteName_-99" Name="SubstituteName_-99" type="text" class="InputText" style="width: 160px;" value="-">';
				strDiv += '		</td>';
				strDiv += '		<td style="padding-left:3px;" valign="top">';
				strDiv += '			<a href="javascript:BrowseUser(\'-99\');"><img border="0" src="../images/browseuser.png"/></a>';
				strDiv += '			<img src="../images/edit_add.gif" onClick="changeSubstitute(\'add\');" style="cursor: pointer;">';
				strDiv += '			<img src="../images/edit_remove.gif" onClick="changeSubstitute(\'remove\');" style="cursor: pointer;">';
				strDiv += '		</td>';
				strDiv += '	</tr>';
				strDiv += '	<tr><td height="5"></td></tr>';
				strDiv += '</table>';

				arrSubstitutes[0] = -99;
				
				document.getElementById('substitute_div').innerHTML = strDiv;
			}
			else
			{
				arrNewSubstitutes = new Array();
				strDiv = '<table cellpadding="0" cellspacing="0">';
				
				if (strAction == 'add')
				{
					if (arrSubstitutes.length < 1)
					{
						nMax = arrSubstitutes.length + 2;
					}
					else
					{
						nMax = arrSubstitutes.length + 1;
					}
				}
				else
				{
					nMax = arrSubstitutes.length -1;
				}

				for (nIndex = 0; nIndex < nMax; nIndex++)
				{
					nSubstituteId = arrSubstitutes[nIndex];
					if (nSubstituteId > 0 || nSubstituteId == -3) {
						strSubsDiv = 'SubstituteName_' + nSubstituteId;
						strSubsName = document.getElementById(strSubsDiv).value;
					}
					else {
						strSubsName = '-';
						nSubstituteId = nIndex * (-1);
					}
					
					strDiv += '	<tr>';
					strDiv += '		<td valign="top">';
					strDiv += '			<input id="SubstituteName_' +  nSubstituteId + '" Name="SubstituteName_' +  nSubstituteId + '" type="text" class="InputText" style="width: 160px;" value="' + strSubsName + '">';
					strDiv += '		</td>';
					strDiv += '		<td style="padding-left:3px;" valign="top">';
					strDiv += '			<a href="javascript:BrowseUser(' + nSubstituteId + ');"><img border="0" src="../images/browseuser.png"/></a>';
					if (nIndex == 0)
					{
						strDiv += '			<img src="../images/edit_add.gif" onClick="changeSubstitute(\'add\');" style="cursor: pointer;">';
						strDiv += '			<img src="../images/edit_remove.gif" onClick="changeSubstitute(\'remove\');" style="cursor: pointer;">';
					}
					strDiv += '		</td>';
					strDiv += '	</tr>';
					strDiv += '	<tr><td height="5"></td></tr>';
					
					arrNewSubstitutes[nIndex] = nSubstituteId;
				}
				strDiv += '</table>';
				
				document.getElementById('substitute_div').innerHTML = strDiv;
				
				arrSubstitutes = arrNewSubstitutes;
			}
		}
		
		function BrowseUser()
		{
			url="selectuser.php?language=<?php echo $_REQUEST['language'] ?>&nId="+document.getElementById('current-substitute-id').value;
			open(url,"BrowseUser","width=300,height=190,status=yes,menubar=no,resizable=no,scrollbars=no");		
		}

		function SetUser(nId, strName, oldId)
		{
			var strDiv = 'SubstituteName_' + oldId;

			document.getElementById('current-substitute-id').value = nId;
			
			objSubstitude = document.getElementById(strDiv);
			
			objSubstitude.value = strName;
			objSubstitude.id 	= 'SubstituteName_' + nId;
			objSubstitude.name 	= 'SubstituteName_' + nId;
			
			for (index = 0; index < arrSubstitutes.length; index++) {
				if (arrSubstitutes[index] == oldId) {
					arrSubstitutes[index] = nId;
				}
			}
		}

		function validate(objForm)
		{
			var objForm = document.forms["EditUser"];
			objForm.strFirstName.required = 1;
			objForm.strFirstName.err = "<?php echo $EDIT_NEW_ERROR_FIRSTNAME;?>";
			objForm.strLastName.required = 1;
			objForm.strLastName.err = "<?php echo $EDIT_NEW_ERROR_LASTNAME;?>";
			
			objElementAdmin = document.getElementById("UserAccesslevelAdmin");
			objElementReadOnly = document.getElementById("UserAccesslevelReadOnly");
			if ( (objElementAdmin.checked == true) || (objElementReadOnly.checked == true) )
			{
				objForm.Password1.required = 1;
				objForm.Password1.err = "<?php echo $EDIT_NEW_ERROR_PASSWORD1;?>";
				objForm.Password2.required = 1;
				objForm.Password2.err = "<?php echo $EDIT_NEW_ERROR_PASSWORD2;?>";
			}
			else
			{
				objForm.Password1.required = 0;
				objForm.Password2.required = 0;
			}
			
			bResult = jsVal(objForm);
			
			if (bResult == true)
			{
				if (objForm.Password1.value != objForm.Password2.value)
				{
					alert ('<?php echo str_replace("'", "\'", $EDIT_NEW_ERROR_PASSWORD3);?>');
					bResult = false;
				}
			}
			
			return bResult	;
		}
		
		function checkValue()
		{
			var tempIndex, choiceVal, tempIndex2, choiceVal2
			tempIndex = document.EditUser.strIN_Email_Value.selectedIndex
			choiceVal = document.EditUser.strIN_Email_Value.options[tempIndex].text
			tempIndex2 = document.EditUser.strIN_Email_Format.selectedIndex
			choiceVal2 = document.EditUser.strIN_Email_Format[tempIndex2].text
			
			if ((tempIndex==2)&&(tempIndex2==0))
			{
				if ((choiceVal=="IFrame")&&(choiceVal2=="Text"))
				{
					window.alert("'IFrames' only works with HTML!")
					document.EditUser.strIN_Email_Value[2].selected = false;
					document.EditUser.strIN_Email_Value[0].selected = true;
				}
			}		
		}
		
		
		
		
		
		var arrSettings = new Array('userdata', 'userrights', 'userdetails');
		
		function showSettings(strSettings)
		{
			nMax = arrSettings.length;
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strCurSettings = arrSettings[nIndex];
				strDiv = strCurSettings + '_div';
				
				if (strCurSettings == strSettings)
				{
					document.getElementById(strDiv).style.display = 'block';
					document.getElementById(strCurSettings).style.background = '#8e8f90';
					document.getElementById(strCurSettings).style.cursor = 'auto';
				}
				else
				{
					document.getElementById(strDiv).style.display = 'none';
					document.getElementById(strCurSettings).style.background = '#bbb';
					document.getElementById(strCurSettings).style.cursor = 'pointer';
				}
			}

			if (strSettings == 'userdetails')
			{
				checkIndividual();
			}
			else
			{
				document.getElementById('indiLayer').style.display = 'none';
			}
		}
		
		function changeStyle(strTd, strAction)
		{
			strDiv = strTd + '_div';
			objDiv = document.getElementById(strDiv).style.display;
			
			if (objDiv == 'none')
			{
				objTd = document.getElementById(strTd);
				switch(strAction)
				{
					case 'over':
						objTd.style.background = '#ffc056';
						objTd.style.cursor = 'pointer';
						break;
					case 'out':
						objTd.style.background = '#bbb';
						break;
				}
			}
		}
		
		function checkIndividual()
		{
			var individualCheckbox = document.getElementById('IN_bIndividualEmail').checked;
			var indiLayer = document.getElementById('indiLayer');
			
			if (!individualCheckbox)
			{
				var top		= document.getElementById('individualDiv').offsetTop + document.getElementById('userdetails_div').offsetTop;
				var left	= document.getElementById('individualDiv').offsetLeft + document.getElementById('userdetails_div').offsetLeft;
				
				indiLayer.style.display = 'block';
				indiLayer.style.top		= top + 'px';
				indiLayer.style.left	= left + 'px';
				
				//alert(left);
			}
			else
			{
				indiLayer.style.display = 'none';
			}
		}
	//-->
	</script>
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>	
</head>
<?php
	$strFirstName = "";
	$strLastName = "";
	$strEMail = "";
	$nAccessLevel = 1;
	$nSubstitudeId = 0;
	
	if ($userid == -1)
	{
		$strPassword = "";
	}
	else
	{
		$strPassword = "unchanged";	
	}
	
	$arrSubstitutes[0]['name'] = '-';
	$arrSubstitutes[0]['id'] = 0;
	
	$CUR_SUBTITUTE_PERSON_UNIT	= $SUBTITUTE_PERSON_UNIT;
	$CUR_SUBTITUTE_PERSON_VALUE	= $SUBTITUTE_PERSON_VALUE;
	
	if (-1 != $userid)
	{
    	//--- open database
    	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
    	
    	if ($nConnection)
    	{
    		//--- get maximum count of users
    		if (mysql_select_db($DATABASE_DB, $nConnection))
    		{
    			//--- read the values of the user
				$strQuery = "SELECT * FROM cf_user WHERE nID = ".$_REQUEST["userid"];
				$nResult = mysql_query($strQuery, $nConnection);
        
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
        				while (	$arrRow = mysql_fetch_array($nResult))
        				{
        					$strFirstName 	= $arrRow["strFirstName"];
        					$strLastName 	= $arrRow["strLastName"];
        					$strEMail 		= $arrRow["strEMail"];
        					$nAccessLevel 	= $arrRow["nAccessLevel"];
        					$nSubstituteId 	= $arrRow["nSubstitudeId"];
                            $strUserId 		= $arrRow["strUserId"];
                            $EMAIL_FORMAT 	= $arrRow["strEmail_Format"];
							$EMAIL_VALUES 	= $arrRow["strEmail_Values"];
							$street			= $arrRow['strStreet'];
							$country		= $arrRow['strCountry'];
							$zipcode		= $arrRow['strZipcode'];
							$city			= $arrRow['strCity'];
							$phone_main1	= $arrRow['strPhone_Main1'];
							$phone_main2	= $arrRow['strPhone_Main2'];
							$phone_mobile	= $arrRow['strPhone_Mobile'];
							$fax			= $arrRow['strFax'];
							$organisation	= $arrRow['strOrganisation'];
							$department		= $arrRow['strDepartment'];
							$cost_center	= $arrRow['strCostCenter'];
							$userdefined1_value	= $arrRow['UserDefined1_Value'];
							$userdefined2_value	= $arrRow['UserDefined2_Value'];
							$CUR_SUBTITUTE_PERSON_UNIT	= $arrRow['strSubstituteTimeUnit'];
							$CUR_SUBTITUTE_PERSON_VALUE	= $arrRow['nSubstituteTimeValue'];
							$bIndividualSubsTime	= $arrRow['bUseGeneralSubstituteConfig'];
							$bIndividualEmail		= $arrRow['bUseGeneralEmailConfig'];
        				}		
        			}
        		}
        		
        		if ($nSubstituteId == -2)
        		{	// user has one or more substitutes - the DB table "cf_substitute" has to be checked
        			$arrResult = $objCirculation->getSubstitutes($_REQUEST['userid']);
        			
        			$nMax = sizeof($arrResult);
        			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
        			{
        				$nCurSubstituteId = $arrResult[$nIndex]['substitute_id'];
        				if ($nCurSubstituteId == -3)
        				{
        					$arrSubstitutes[$nIndex]['name']	= $SELF_DELEGATE_USER;
		        			$arrSubstitutes[$nIndex]['id']		= -3;
        				}
        				else
        				{
	        				$arrUser = $objCirculation->getUserById($nCurSubstituteId);
	        				
		        			$arrSubstitutes[$nIndex]['name'] 	= $arrUser['strLastName'].', '.$arrUser['strFirstName'];
		        			$arrSubstitutes[$nIndex]['id'] 		= $nCurSubstituteId;
        				}
        			}
        		}
    		}
    	}
	}
	else
	{
		$EMAIL_FORMAT = 'HTML';
		$EMAIL_VALUES = 'IFRAME';
	}
?>
<body><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_USERMNG;?>
</span><br><br>


<form action="writeuser.php" id="EditUser" name="EditUser" onsubmit="return validate(this);">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" valign="top" onClick="showSettings('userdata');" style="padding: 2px 6px 2px 6px; background: #8e8f90; border: 1px solid #888; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('userdata', 'over');" onMouseOut="changeStyle('userdata', 'out');" id="userdata">
				<?php echo $USER_EDIT_FORM_HEADER ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('userrights');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #888; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('userrights', 'over');" onMouseOut="changeStyle('userrights', 'out');" id="userrights">
				<?php echo $USER_EDIT_FORM_HEADER_RIGHTS ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('userdetails');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #888; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('userdetails', 'over');" onMouseOut="changeStyle('userdetails', 'out');" id="userdetails">
				<?php echo $USER_EDIT_FORM_HEADER_DETAILS ?>
			</td>
		</tr>
	</table>
	
<?php // ############################### USERDATA :: START ############################### ?>
	
	<div id="userdata_div">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" width="550" cellspacing="0" cellpadding="3">
			<tr><td colspan="2" height="10px"></td></tr>
	        <tr>
				<td width="190"><?php echo $USER_EDIT_FIRSTNAME ?></td>
				<td>
					<input id="strFirstName" Name="strFirstName" type="text" class="InputText" style="width: 160px;" value="<?php echo $strFirstName;?>">
				</td>
			</tr>
	        <tr>
				<td><?php echo $USER_EDIT_LASTNAME;?></td>
				<td>
					<input id="strLastName" Name="strLastName" type="text" class="InputText" style="width: 160px;" value="<?php echo $strLastName;?>">
				</td>
			</tr>
	        <tr>
				<td><?php echo $USER_EDIT_EMAIL;?></td>
				<td>
					<input id="strEMail" Name="strEMail" type="text" class="InputText" style="width: 160px;" value="<?php echo $strEMail;?>">
				</td>
			</tr>		
			<tr><td colspan="2" height="10px"></td></tr>
	        <tr>
				<td><?php echo $USER_EDIT_USERID;?></td>
				<td>
					<input type="text" Name="UserName" id="UserName" class="InputText" style="width: 160px;" value="<?php echo $strUserId;?>">
				</td>
			</tr>
	        <tr>
				<td><?php echo $USER_EDIT_PWD;?></td>
				<td>
					<input type="password" Name="Password1" id="Password1" class="InputText" style="width: 160px;" value="<?php echo $strPassword;?>">
				</td>
			</tr>
	        <tr>
				<td><?php echo $USER_EDIT_PWDCHECK;?></td>
				<td>
					<input type="password" Name="Password2" id="Password2" class="InputText" style="width: 160px;" value="<?php echo $strPassword;?>">
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr>
				<td valign="top" align="left"><?php echo $USER_EDIT_SUBSTITUDE;?></td>
				<td valign="top" align="left">
					<div id="substitute_div">
					<table cellpadding="0" cellspacing="0">
						<?php
						$arrJSSubstitutes = '';
						$nMax = sizeof($arrSubstitutes);
						for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
						{
							$arrSubstitute = $arrSubstitutes[$nIndex];
							?>
							<tr>
								<td valign="top">
									<input id="SubstituteName_<?php echo $arrSubstitute['id'] ?>" Name="SubstituteName_<?php echo $arrSubstitute['id'] ?>" type="text" class="InputText" style="width: 160px;" value="<?php echo $arrSubstitute['name'] ?>">
									<input type="hidden" id="current-substitute-id" value="<?php echo $arrSubstitute['id'] ?>" />
								</td>
								<td style="padding-left:3px;" valign="top">
									<a href="javascript:BrowseUser();"><img border="0" src="../images/browseuser.png"/></a>
									<?php if ($nIndex == 0): ?>
									<img src="../images/edit_add.gif" onClick="changeSubstitute('add');" style="cursor: pointer;">
									<img src="../images/edit_remove.gif" onClick="changeSubstitute('remove');" style="cursor: pointer;">
									<?php endif ?>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<?php
							if ($nIndex == ($nMax-1))
							{
								$arrJSSubstitutes .= $arrSubstitute['id'];
							}
							else
							{
								$arrJSSubstitutes .= $arrSubstitute['id'].', ';
							}
						}
						?>
						<script language="javascript">
						arrSubstitutes = new Array (<?php echo $arrJSSubstitutes ?>, 50);
						// y pop and y ",50"?
						// this is needed to tell javascript that it's an array even if there's only one number in it
						// e.g.: test = new Array(500); is NO Array!!! test[0] will be "undefined" and test.length will return the value 500!!!!
						arrSubstitutes.pop();
						</script>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100"><?php echo $SUBSTITUTE_TIME ?>:</td>
				<td>
					<input name="strIN_Subtitute_Person_Value" type="text" class="InputText" style="width:50px;" value="<?php echo $CUR_SUBTITUTE_PERSON_VALUE ?>">
				
					<select name="strIN_Subtitute_Person_Unit" size="1" class="FormInput" style="width:100px;">			
						<option <?php if ($CUR_SUBTITUTE_PERSON_UNIT == 'DAYS') echo 'selected'; ?> value="DAYS"><?php echo $CONFIG_DAYS ?></option>
						<option <?php if ($CUR_SUBTITUTE_PERSON_UNIT == 'HOURS') echo 'selected'; ?> value="HOURS"><?php echo $CONFIG_HOURS ?></option>
						<option <?php if ($CUR_SUBTITUTE_PERSON_UNIT == 'MINUTES') echo 'selected'; ?> value="MINUTES"><?php echo $CONFIG_MINUTES ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo $USE_INDIVIDUAL_SUBSTITUTE_TIME ?></td>
				<td>
					<input type="checkbox" name="IN_bIndividualSubsTime" <?php if (!$bIndividualSubsTime) echo 'checked' ?> value="1">
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
	</div>

<?php // ############################### USERDATA :: END ############################### ?>
<?php // ############################### USERRIGHTS :: START ############################### ?>

	<div id="userrights_div" style="display: none;">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" width="550" cellspacing="0" cellpadding="3">
			<tr><td height="10px"></td></tr>
			<tr>
				<td valign="top" width="190"><?php echo $USER_EDIT_ACCESSLEVEL;?></td>
				<td>
					<input type="radio" id="UserAccesslevelAdmin" name="UserAccessLevel" value="2" <?php echoCheckedAllowed($nAccessLevel, 2);?>> <?php echo $USER_ACCESSLEVEL_ADMIN;?><br>
					<input type="radio" id="UserAccesslevelSender" name="UserAccessLevel" value="8" <?php echoCheckedAllowed($nAccessLevel, 8);?>> <?php echo $USER_ACCESSLEVEL_SENDER;?><br>
					<input type="radio" id="UserAccesslevelReadOnly" name="UserAccessLevel" value="4" <?php echoCheckedAllowed($nAccessLevel, 4);?>> <?php echo $USER_ACCESSLEVEL_READONLY;?><br>
					<input type="radio" id="UserAccesslevelReceiver" name="UserAccessLevel" value="1" <?php if (($nAccessLevel == 0) || ($nAccessLevel == 1)) echo "CHECKED";?>> <?php echo $USER_ACCESSLEVEL_RECEIVER;?>						
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
		</table>
	</div>
	
<?php // ############################### USERRIGHTS :: END ############################### ?>
<?php // ############################### USERDETAILS :: START ############################### ?>
	
	<div id="userdetails_div" style="display: none;">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" width="550" cellspacing="0" cellpadding="3">
			<tr><td height="10px"></td></tr>
			<tr>
				<td nowrap style="padding-right: 6px;" width="190">
					<?php echo $USER_EDIT_GENERAL_EMAIL_FORMAT ?>
				</td>
				<td>
					<input type="checkbox" name="IN_bIndividualEmail" id="IN_bIndividualEmail" <?php if (!$bIndividualEmail) echo 'checked' ?> value="1" onClick="checkIndividual();">
				</td>
			</tr>
	        <tr>
				<td nowrap style="padding-right: 6px;" width="190">
					<?php echo $CFG_EMAIL_FORMAT; ?>:
				</td>		
				<td id="individualDiv">
					<select name="strIN_Email_Format" size="1" class="FormInput" style="width:100px;" onChange="checkValue();">
						<option <?php if ($EMAIL_FORMAT == 'PLAIN') echo 'selected' ?> value="TEXT"><?php echo $EMAIL_FORMAT_TEXT ?></option>
						<option <?php if ($EMAIL_FORMAT == 'HTML') echo 'selected' ?> value="HTML">HTML</option>
					</select>
					<select name="strIN_Email_Value" size="1" class="FormInput" style="margin-left: 3px; width:100px;" onChange="checkValue();">
						<option <?php if ($EMAIL_VALUES == 'NONE') echo 'selected' ?> value="NONE"><?php echo $EMAIL_VALUES_NONE ?></option>
						<option <?php if ($EMAIL_VALUES == 'VALUES') echo 'selected' ?> value="VALUES"><?php echo $EMAIL_VALUES_VALUES ?></option>
						<option <?php if ($EMAIL_VALUES == 'IFRAME') echo 'selected' ?> value="IFRAME">IFrame</option>
					</select>
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
			<tr>
				<td><?php echo $USER_EDIT_STREET;?></td>
				<td>
					<input type="text" id="IN_street" name="IN_street" class="InputText" style="width: 160px;" value="<?php echo $street ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_ZIPCODE ?> / <?php echo $USER_EDIT_CITY ?></td>
				<td>
					<input type="text" id="IN_zipcode" name="IN_zipcode" class="InputText" style="width: 60px;" value="<?php echo $zipcode ?>">
					<input type="text" id="IN_city" name="IN_city" class="InputText" style="width: 160px;" value="<?php echo $city ?>">
				</td>
			</tr>
	        <tr>
				<td><?php echo $USER_EDIT_COUNTRY ?></td>
				<td>
					<input type="text" id="IN_country" name="IN_country" class="InputText" style="width: 160px;" value="<?php echo $country ?>">
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
			<tr>
				<td><?php echo $USER_EDIT_PHONE_MAIN1 ?></td>
				<td>
					<input type="text" id="IN_phone_main1" name="IN_phone_main1" class="InputText" style="width: 160px;" value="<?php echo $phone_main1 ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_PHONE_MAIN2 ?></td>
				<td>
					<input type="text" id="IN_phone_main2" name="IN_phone_main2" class="InputText" style="width: 160px;" value="<?php echo $phone_main2 ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_PHONE_MOBILE ?></td>
				<td>
					<input type="text" id="IN_phone_mobile" name="IN_phone_mobile" class="InputText" style="width: 160px;" value="<?php echo $phone_mobile ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_FAX ?></td>
				<td>
					<input type="text" id="IN_fax" name="IN_fax" class="InputText" style="width: 160px;" value="<?php echo $fax ?>">
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
			<tr>
				<td><?php echo $USER_EDIT_ORGANISATION ?></td>
				<td>
					<input type="text" id="IN_organisation" name="IN_organisation" class="InputText" style="width: 160px;" value="<?php echo $organisation ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_DEPARTMENT ?></td>
				<td>
					<input type="text" id="IN_department" name="IN_department" class="InputText" style="width: 160px;" value="<?php echo $department ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USER_EDIT_COST_CENTER ?></td>
				<td>
					<input type="text" id="IN_cost_center" name="IN_cost_center" class="InputText" style="width: 160px;" value="<?php echo $cost_center ?>">
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
			<tr>
				<td><?php echo $USERDEFINED_TITLE1 ?></td>
				<td>
					<input type="text" id="IN_userdefined1_value" name="IN_userdefined1_value" class="InputText" style="width: 160px;" value="<?php echo $userdefined1_value ?>">
				</td>
			</tr>
			<tr>
				<td><?php echo $USERDEFINED_TITLE2 ?></td>
				<td>
					<input type="text" id="IN_userdefined2_value" name="IN_userdefined2_value" class="InputText" style="width: 160px;" value="<?php echo $userdefined2_value ?>">
				</td>
			</tr>
			<tr><td height="10px"></td></tr>
		</table>
	</div>

<?php // ############################### USERDETAILS :: END ############################### ?>

	<table width="550">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_CANCEL;?>" onclick="history.back()">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $USER_EDIT_ACTION;?>" class="Button">
			</td>
		</tr>
	</table>
	
	
<input type="hidden" value="<?php echo $_REQUEST["userid"];?>" id="userid" name="userid">
<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sort" name="sort">
<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">

</form>
	
	<div id="indiLayer" style="position: absolute; display: none; background: url(../images/layer.gif); height: 25px; width: 230px;">
	</div>
	
</body>
</html>

<?php
	function echoCheckedAllowed($nAccessLevel, $nRequestLevel)
	{
		if ( ($nAccessLevel & $nRequestLevel) == $nRequestLevel)
		{
			echo "CHECKED";	
		}	
	}
?>