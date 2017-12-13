<?php
if(isset($_POST["form"])){
$form = $_POST["form"];
 echo ' <div style="display:none">'.$form.'</div>Please wait ... <script language="javascript">document.payment.submit();</script>';
}
else
{
echo"<div style='width:800px; height:800px;  margin:auto; font-size:55px; text-align:center; margin-top:200px; color:red;'> <font>!!!!!!!!خطای امنیتی</font></div>";
}
?>