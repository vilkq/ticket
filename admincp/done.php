<script type="text/javascript">
//alert ('Читательский билет создан!');
function index(){window.location = "index.php"}
</script>

<?
include("config.php");
access();
    $row = mysql_fetch_array(mysql_query("SELECT `homepage` FROM `ticket_config` AS `homepage`"));
    $homepage = $row['homepage'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ru">
<head>
<title>Административная панель</title>
<link rel="stylesheet" href="admin.css" media="all"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <div id="message">Читательский билет создан!</div>
    <input type="button" id="index_done" value="Вернуться обратно" onclick="return index();" />
    <input type="button" id="open_ticket_done" value="Открыть созданный билет" onclick="JavaScript:window.open('<?echo $homepage;?>','subwindow','height=626,width=880');" />
</body>
</html>