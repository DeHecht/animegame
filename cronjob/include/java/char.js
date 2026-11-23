/*
 *
 * @copyright (c) 2009 animegame.eu
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public Licence
 *
 */

<script type="text/javascript"> 
<!-- 
function formSubmit()
{
  document.forms[0].elements['item_id'].value=itemid;
  document.forms[0].submit();
}


function showText() 
{
     if(document.all)
     {
          CharBox.style.visibility='visible';
          CharBox.style.top=window.event.clientY+document.body.scrollTop;
          CharBox.style.left=window.event.x+20; 

	

     }
     else if(document.getElementById)
     {
          document.getElementById('CharBox').style.visibility='visible';
          window.captureEvents(Event.MOUSEOVER);
          window.onmouseover=NSshowText;



     }
}

function NSshowText(e)
{
     document.getElementById('CharBox').style.top=e.pageY;
     document.getElementById('CharBox').style.left=e.pageX+20;
}

function hideText()
{
     if(document.all)
     {
          CharBox.style.visibility='hidden';
     }
     else if(document.getElementById)
     {
          document.getElementById('CharBox').style.visibility='hidden';
     }
}
-->
</script>