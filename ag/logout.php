<?php
/*
 *
 * @copyright (c) 2010 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */
setcookie("name",$name,time()+1); 
setcookie("passwort",$pw,time()+1); 
  
header ("Location: index.php?as=login"); 
?>