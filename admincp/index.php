<?php
include("config.php");
include("classbarcode.php");
include("classSimpleImage.php");
access();
$uri = $_SERVER['DOCUMENT_ROOT'];

if($_POST['create_ticket']){
    if ($_POST['region'] <= '9') {$region = '0'.$_POST['region'];} else {$region = $_POST['region'];}
    if ($_POST['barnum'] <= '9') {$barnum = '0000'.$_POST['barnum'];}
    if ($_POST['barnum'] > '9' AND $_POST['barnum'] <= '99') {$barnum = '000'.$_POST['barnum'];}
    if ($_POST['barnum'] > '99' AND $_POST['barnum'] <= '999') {$barnum = '00'.$_POST['barnum'];}
    if ($_POST['barnum'] > '999' AND $_POST['barnum'] <= '9999') {$barnum = '0'.$_POST['barnum'];}
    if ($_POST['barnum'] > '9999') {$barnum = $_POST['barnum'];}
    $fam = $_POST['fam'];$name = $_POST['name'];$otch = $_POST['otch'];$month = $_POST['month'];$year = $_POST['year'];$barnum = '2000'.$region.'9'.$barnum;

//генерируем штрих-код
    $im     = imagecreatetruecolor(426, 106);
    $black  = ImageColorAllocate($im,0x00,0x00,0x00);
    $white  = ImageColorAllocate($im,0xff,0xff,0xff);
    imagefilledrectangle($im, 0, 0, 426, 106, $white);
    $data = Barcode::gd($im, $black, 203, 53, 0, "ean13", $barnum, 4.26, 106);

//преобразовываем числовой код в строку и расчитываем контрольный символ
    $barnum_array = str_split($barnum);
    $step1 = $barnum_array[1] + $barnum_array[3] + $barnum_array[5] + $barnum_array[7] + $barnum_array[9] + $barnum_array[11];
    $step2 = $step1 * 3;
    $step3 = $barnum_array[0] + $barnum_array[2] + $barnum_array[4] + $barnum_array[6] + $barnum_array[8] + $barnum_array[10];
    $step4 = $step2 + $step3;
    $step5 = $step4%10;
    $control_number = 10 - $step5;
    if ($control_number == 10) {$control_number = '0';}
    $barnum = $barnum.$control_number; // контрольное число дописываем к коду

//сохраняем штрих-код визображениие
    imagepng($im, 'barcode_'.$barnum.'.png');

//заносим данные из формы в базу данных
    $insert = mysql_query("INSERT INTO `ticket_people` (`id`, `fam`, `name`, `otch`, `month`, `year`, `barnum`)" . "VALUES ('NULL', '$fam', '$name', '$otch', '$month', '$year', '$barnum')");

//проверяем и загружаем фото
    $types = array('image/jpeg'); //проверяем тип
    $maxsize = 1024000; //максимальный размер
    $newFilename = $uri. '/ticket/admincp/photo_'.$barnum; //путь,где сохраняются фото
    $newFilename .= '.jpg';
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!in_array($_FILES['photo']['type'], $types)) die('Фотография обязана быть в формате JPG');
        if ($_FILES['photo']['size'] > $maxsize) die('Слишком большой размер файла');
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $newFilename)) echo 'Не удалось осуществить сохранение файла';
    }

//Меняем размер фото
    $image = new SimpleImage();
    $image->load($newFilename);
    $image->resize(337.5, 450);
    $image->save($newFilename);

//скопировали к общим файлам страницу для формирования, переименовали файл, с которого сформируется pdf, повысили счётчик
    $row = mysql_fetch_array(mysql_query("SELECT `count` FROM `ticket_config` AS `count`"));
    $count = $row['count'];
    copy($uri."/ticket/ticketscreen".$count.".php", $uri."/ticket/admincp/ticketscreen_".$barnum.".php");
    rename($uri."/ticket/ticketscreen".$count.".php", $uri."/ticket/ticketscreen".($count+1).".php");
    $count++;
    $update_count = mysql_query("UPDATE ticket_config SET count = $count");

//удаляем изображение старого билета перед созданием нового, если с таким номером уже существует
    if (file_exists("ticket_$barnum.png")) {unlink("ticket_$barnum.png");}

//проверяем наличие даты и выбираем соответствующий шаблон рисунка
    if(isset($_REQUEST['cardunlim']) AND $_POST['cardunlim'] == 'cardunlim') { //если "бессрочно" поставлено
        $file = "bckgr_cardunlim.png"; //исходный рисунок "бессрочно"
        $newfile = "ticket_$barnum.png"; //расположение нового рисунка
        copy($file, $newfile); //копируем из папки для дальнейшей с ним работы
        $pic = ImageCreateFrompng("ticket_$barnum.png"); //открываем рисунок и начинаем формировать билет
        $color_date = ImageColorAllocate($pic, 24, 52, 140);
        //координаты размещения текста
        $w_fam = 8; $h_fam = 165;
        $w_name = 8; $h_name = 235;
        $w_otch = 8; $h_otch = 305;
        $w_barnum = 30; $h_barnum = 525;
        $w_cardunlim = 180; $h_cardunlim = 576;
        ImageTTFtext($pic, 44, 0, $w_fam, $h_fam, 0, "../font/arialreg.ttf", $fam);
        ImageTTFtext($pic, 44, 0, $w_name, $h_name, 0, "../font/arialreg.ttf", $name);
        ImageTTFtext($pic, 44, 0, $w_otch, $h_otch, 0, "../font/arialreg.ttf", $otch);
        ImageTTFtext($pic, 37.5, 0, $w_barnum, $h_barnum, 0, "../font/arialreg.ttf", $barnum);
        ImageTTFtext($pic, 21, 0, $w_cardunlim, $h_cardunlim, $color_date, "../font/arialreg.ttf", "бессрочно");
        $photo = ImageCreateFromjpeg("photo_$barnum.jpg"); //что накладываем
                 ImageAlphaBlending($photo, true);
        ImageCopy($pic, $photo, 555, 130, 0, 0, imagesx($photo), imagesy($photo)); //размещаем координаты и копируем
        $barcode = ImageCreateFrompng("barcode_$barnum.png"); //что накладываем
                   ImageAlphaBlending($barcode, true);
        ImageCopy($pic, $barcode, 10, 370, 0, 0, imagesx($barcode), imagesy($barcode)); //размещаем координаты и копируем
        Imagepng($pic, "ticket_$barnum.png", 0); //сохраняем рисунок
        ImageDestroy($pic); //освобождаем память
    }
    else if ($_POST['month'] AND $_POST['year'] <> ""){ //если месяц и дата заполнены
        if ($month <= '9'){$month = '0'.$month;}
        $file = "bckgr_date.png"; //исходный рисунок без надписей
        $newfile = "ticket_$barnum.png"; //расположение нового рисунка
        copy($file, $newfile); //копируем из папки для дальнейшей с ним работы
        $pic = ImageCreateFrompng("ticket_$barnum.png"); //открываем рисунок и начинаем формировать билет
        $color_date = ImageColorAllocate($pic, 24, 52, 140);
        //координаты размещения текста
        $w_fam = 8; $h_fam = 165;
        $w_name = 8; $h_name = 235;
        $w_otch = 8; $h_otch = 305;
        $w_barnum = 30; $h_barnum = 525;
        $w_month = 190; $h_month = 576;
        $w_year = 232; $h_year = 576;
        ImageTTFtext($pic, 44, 0, $w_fam, $h_fam, 0, "../font/arialreg.ttf", $fam);
        ImageTTFtext($pic, 44, 0, $w_name, $h_name, 0, "../font/arialreg.ttf", $name);
        ImageTTFtext($pic, 44, 0, $w_otch, $h_otch, 0, "../font/arialreg.ttf", $otch);
        ImageTTFtext($pic, 37.5, 0, $w_barnum, $h_barnum, 0, "../font/arialreg.ttf", $barnum);
        ImageTTFtext($pic, 21, 0, $w_month, $h_month, $color_date, "../font/arialreg.ttf", "$month/");
        ImageTTFtext($pic, 21, 0, $w_year, $h_year, $color_date, "../font/arialreg.ttf", $year);
        $photo = ImageCreateFromjpeg("photo_$barnum.jpg"); //что накладываем
                 ImageAlphaBlending($photo, true);
        ImageCopy($pic, $photo, 555, 130, 0, 0, imagesx($photo), imagesy($photo)); //размещаем координаты и копируем
        $barcode = ImageCreateFrompng("barcode_$barnum.png"); //что накладываем
                   ImageAlphaBlending($barcode, true);
        ImageCopy($pic, $barcode, 10, 370, 0, 0, imagesx($barcode), imagesy($barcode)); //размещаем координаты и копируем
        Imagepng($pic, "ticket_$barnum.png", 0); //сохраняем рисунок
        ImageDestroy($pic); //освобождаем память
    }
    else if ($_POST['month'] OR $_POST['year'] == ""){ //если месяц или дата не заполнены
        $file = "bckgr_nodate.png"; //исходный рисунок без надписей
        $newfile = "ticket_$barnum.png"; //расположение нового рисунка
        copy($file, $newfile); //копируем из папки для дальнейшей с ним работы
        $pic = ImageCreateFrompng("ticket_$barnum.png"); //открываем рисунок и начинаем формировать билет
        //координаты размещения текста
        $w_fam = 8; $h_fam = 165;
        $w_name = 8; $h_name = 235;
        $w_otch = 8; $h_otch = 305;
        $w_barnum = 30; $h_barnum = 525;
        ImageTTFtext($pic, 44, 0, $w_fam, $h_fam, 0, "../font/arialreg.ttf", $fam);
        ImageTTFtext($pic, 44, 0, $w_name, $h_name, 0, "../font/arialreg.ttf", $name);
        ImageTTFtext($pic, 44, 0, $w_otch, $h_otch, 0, "../font/arialreg.ttf", $otch);
        ImageTTFtext($pic, 37.5, 0, $w_barnum, $h_barnum, 0, "../font/arialreg.ttf", $barnum);
        $photo = ImageCreateFromjpeg("photo_$barnum.jpg"); //что накладываем
                 ImageAlphaBlending($photo, true);
        ImageCopy($pic, $photo, 555, 130, 0, 0, imagesx($photo), imagesy($photo)); //размещаем координаты и копируем
        $barcode = ImageCreateFrompng("barcode_$barnum.png"); //что накладываем
                   ImageAlphaBlending($barcode, true);
        ImageCopy($pic, $barcode, 10, 370, 0, 0, imagesx($barcode), imagesy($barcode)); //размещаем координаты и копируем
        Imagepng($pic, "ticket_$barnum.png", 0); //сохраняем рисунок
        ImageDestroy($pic); //освобождаем память
    }

//заменяем на главной странице на строку с последним скрином
    $filename = $uri.'/ticket/ticketscreen'.$count.'.php';
    file_put_contents($filename, "<img src=admincp/ticket_$barnum.png>");

//создаём файл со скрином в папке admincp на будущее для показа в общем списке people
    $str1 = <<<'EOD'
<?
include("config.php");
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$dompdf = new Dompdf($options);
$filename = 'ticketscreen_
EOD;
    $str2 = <<<'EOD'
.php';
$html = file_get_contents($filename);
$dompdf->loadHtml($html);
$dompdf->setPaper('ticket', 'landscape');
$dompdf->render();
$dompdf->stream("_",array("Attachment"=>false));
EOD;
    $str =$str1.$barnum.$str2;
    $filename = $uri.'/ticket/admincp/ticket_'.$barnum.'.php';
    file_put_contents($filename, $str);
    $filename = $uri.'/ticket/admincp/ticketscreen_'.$barnum.'.php';
    file_put_contents($filename, "<img src=ticket_$barnum.png>");
    header("Location: done.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ru">
<head>
<title>Административная панель</title>
<link rel="stylesheet" href="admin.css" media="all"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
function hideDate(f) {
    if (f.cardunlim.checked){
        document.getElementById("month").disabled = "disabled";
        document.getElementById("year").disabled = "disabled";
        document.getElementById("month").value = "";
        document.getElementById("year").value = "";
    }
    else {
        document.getElementById("month").disabled = "";
        document.getElementById("year").disabled = "";
    }
}
function validatefam(input) {if (input.value.length > 48) {input.setCustomValidity("Фамилия слишком длинная!");}else {input.setCustomValidity("");}}
function validatename(input) {if (input.value.length > 32) {input.setCustomValidity("Имя слишком длинное!");}else {input.setCustomValidity("");}}
function validateotch(input) {if (input.value.length > 48) {input.setCustomValidity("Отчество слишком длинное!");}else {input.setCustomValidity("");}}
</script>
</head>
<body>
<div id="create">
<h2>Новый читательский билет</h2>
<hr />

<form action="" method="post" name="form" id="form" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4">
	<tr>
		<td width="100">Фамилия:</td>
			<td width="300"><input type="text" name="fam" id="fam" size="40" autocomplete="off" required pattern="[а-яА-Я]+" oninput="validatefam(this)" value="<? echo $_POST['fam']; ?>" /></td>
	</tr>
	<tr>
		<td width="100">Имя:</td>
			<td width="300"><input type="text" name="name" id="name" size="40" autocomplete="off" required pattern="[а-яА-Я]+" oninput="validatename(this)" value="<? echo $_POST['name']; ?>" /></td>
	</tr>
	<tr>
		<td width="100">Отчество:</td>
			<td width="300"><input type="text" name="otch" id="otch" size="40" autocomplete="off" required pattern="[а-яА-Я]+" oninput="validateotch(this)" value="<? echo $_POST['otch']; ?>" /></td>
	</tr>
	<tr><td colspan="2"><hr /></td></tr>
    <tr>
    	<td width="100">Месяц:</td>
            <td width="50"><input type="number" min="1" max="12" name="month" id="month" size="2" autocomplete="off" value="<? echo $_POST['month']; ?>" /></td>
    <tr>
        <td width="100">Год:</td>
            <td width="50"><input type="number" min="18" max="99" name="year" id="year" size="2" autocomplete="off" value="<? echo $_POST['year']; ?>" /></td>
    </tr>
        <td></td>
        <td><input type="checkbox" name="cardunlim" value="cardunlim" onclick="hideDate(this.form)" />Бессрочно</td>
    </tr>

	<tr><td colspan="2"><hr /></td></tr>
	<tr>
		<td width="100">Номер:</td>
			<td width="30"><input type="number" min="1" max="99999" name="barnum" id="barnum" size="5" autocomplete="off" required value="<? echo $_POST['barnum']; ?>" /></td>
	</tr>

    <tr><td colspan="2"><hr /></td></tr>
    <tr>
        <td width="100">Регион:</td>
            <td width="30">
            <select name="region">
                <?
                    $res = mysql_query('SELECT `region`, `code` FROM `ticket_region`');
                    while($row = mysql_fetch_assoc($res)){
                ?>
                        <option <?php if($row['code'] == '78'){echo("selected");}?> value="<?=$row['code']?>"><?=$row['region']?></option>
                <?
                    }
                ?>
            </select>
            </td>
    </tr>

	<tr><td colspan="2"><hr /></td></tr>
	<tr>
		<td width="130">Фотография:</td>
			<td width="30"><input type="file" name="photo" size="12" required /></td>
	</tr>
	</table>

	<tr><td colspan="2"><hr /></td></tr>
	<br />
<?  $row = mysql_fetch_array(mysql_query("SELECT * FROM `ticket_config`"));
	$count = $row['count'];
	$homepage = $row['homepage'];
?>
	<input type="submit" id="create_ticket" value="Создать билет" name="create_ticket" />
    <input type="button" id="open_ticket" value="Открыть последний билет" onclick="JavaScript:window.open('<?echo $homepage;?>','subwindow','height=626,width=880');" />
</form>

</div>

<div id="people">
    <h2>Зарегистрированные билеты</h2>
    <hr />
<?
    //Выводим список ticket_people
        $getcurrent = mysql_query("SELECT * FROM `ticket_people` ORDER BY `id` DESC");
        while($r=mysql_fetch_array($getcurrent)){
            extract($r);
            $barnum_array = str_split($barnum);
            if ($month <= '9'){$month = '0'.$month;}
            if ($month == '0'){$month = 'бессрочно';$year='';} else {$month = $month.'/';}
?>
            <table id="peoplelist">
                <tr onclick="JavaScript:window.open('ticket_<?echo $barnum?>.php','subwindow','width=748,width=1054');">
                    <td width="550" style="text-indent:40px;">— <strong><?echo $fam?> <?echo $name?> <?echo $otch?></strong></td>
                    <td width="100"><?echo $month?><?echo $year?></td>
                    <td width="60"><?echo $barnum_array[7].$barnum_array[8].$barnum_array[9].$barnum_array[10].$barnum_array[11]?></td>
                </tr>
            </table>
        <?}?>
<hr />
</div><div class="error-box"></div>
</body>
</html>