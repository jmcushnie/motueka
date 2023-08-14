<?php
// function to clean input but not validate type and content
function cleanInput($data)
{  
  return htmlspecialchars(stripslashes(trim($data)));
}
?>