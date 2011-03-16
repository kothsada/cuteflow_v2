/* FILE HEADER **************************************************
** JS Validate
** Author: Karl Seguin
** Homepage: http://www.openmymind.net/
** Version: 0.1
** Copyright 2003 Karl Seguin

    This file is part of JS Validate.

    JS Validate is free software; you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    JS Validate is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
**
** END HEADER ***************************************************/

function jsVal(form){
  for (var i = 0; i < form.length; ++i){
    var obj = form.elements[i];
    switch(obj.type){
    case "text":
    case "password":
      if ((obj.required && !obj.value) || (obj.value && (obj.minlength && obj.value.length < obj.minlength)) || (obj.regexp && !checkRegExp(obj.regexp, obj.value))){
        throwError(obj.type, obj);
        return false;
      }
      break;
    case "select-one":
      if (obj.required && (obj.options[obj.options.selectedIndex].value == obj.exclude)){
        throwError(obj.type, obj);
        return false;
      }
      break;
    case "radio":  
      if (form.elements[obj.name].required && !radioChecked(form.elements[obj.name])){
        throwError(obj.type, form.elements[obj.name]);
        return false;
      }
      break;    
    case "checkbox":       
      if (form.elements[obj.name].required && !checkboxChecked(form.elements[obj.name])){
        throwError(obj.type, form.elements[obj.name]);
        return false;
      }
      break;    
    case "textarea":
      if ((obj.required && !obj.value) || (obj.value && ((obj.minlength && obj.value.length < obj.minlength) || (obj.maxlength && obj.value.length > obj.maxlength)))){
        throwError(obj.type, obj);
        return false;
      }      
      break;
    }
  }
  return true;
}

function radioChecked(rad){
  for (var i; i < rad.length; ++i)
    if (rad[i].checked)
      return true;
  return false;
}


function checkboxChecked(rad){
  var cnt = 0;
  for (var i=0; i < rad.length; ++i)
    if (rad[i].checked) 
      ++cnt;
  
  return ((!rad.min || (cnt >= rad.min)) && (!rad.max || (cnt <= rad.max)))
}

function throwError(tpe, obj){
  switch (tpe){
    case "text":
    case "password":
    case "textarea":
      alert(getError(obj, "enter"));
      obj.focus();
      break;
    case "select-one":
    case "radio":  
    case "checkbox":       
      alert(getError(obj, "select"));
      break;
  }
}

function getError(obj, str){
  return (obj.err) ? obj.err : "Please " + str + " a valid \"" + ((obj.id) ? obj.id : obj.name) + "\"";
}

function checkRegExp(regx, value){
  switch (regx){
  case "email":
    return ((/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/).test(value));
  case "tel":
    return ((/^1?[\- ]?\(?\d{3}\)?[\- ]?\d{3}[\- ]?\d{4}$/).test(value));
  case "pc":
    return ((/^[a-z]\d[a-z] ?\d[a-z]\d$/i).test(value));
  case "zip":
    return ((/^\d{5}$/).test(value));
  case "money":
    return ((/^\d+([\.]\d\d)?$/).test(value));
  case "creditcard":
    return (!isNaN(value));   
  case "postalzip":
    if(value.length == 6 || value.length == 7)
      return((/^[a-zA-Z]\d[a-zA-Z] ?\d[a-zA-Z]\d$/).test(value));
    if(value.length == 5 || value.length == 10)
      return((/^\d{5}(\-\d{4})?$/).test(value));
    break;
  default:
    return (regx.test(value));

  }
}

function isValidCardNumber (strNum){
  if (!strNum)
    return false;
  var nCheck = 0;
  var nDigit = 0;
  var bEven  = false; 
  for (n = strNum.length - 1; n >= 0; n--){
    var cDigit = strNum.charAt (n);
    if (isDigit (cDigit)){
      var nDigit = parseInt(cDigit, 10);
      if (bEven){
        if ((nDigit *= 2) > 9)
          nDigit -= 9;
        }
        nCheck += nDigit;
        bEven = ! bEven;
      }else if (cDigit != ' ' && cDigit != '.' && cDigit != '-'){
      return false;
    }
  }
  return (nCheck % 10) == 0;
}

function isDigit (c){
  var strAllowed = "1234567890";
    return (strAllowed.indexOf (c) != -1);
}

function isCardTypeCorrect (strNum, type){
  var nLen = 0;
  for (n = 0; n < strNum.length; n++){
    if (isDigit (strNum.substring (n,n+1)))
      ++nLen;
  }
  if (type == 'Visa')
    return ((strNum.substring(0,1) == '4') && (nLen == 13 || nLen == 16));
  else if (type == 'Amex')
    return ((strNum.substring(0,2) == '34' || strNum.substring(0,2) == '37') && (nLen == 15));
  else if (type == 'Master Card')
    return ((strNum.substring(0,2) == '51' || strNum.substring(0,2) == '52' || strNum.substring(0,2) == '53' || strNum.substring(0,2) == '54' || strNum.substring(0,2) == '55') && (nLen == 16));
  else
    return false;
}
