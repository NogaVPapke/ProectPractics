<?
/*ИЗНАЧАЛЬНЫЙ КОД ЗАКОММЕНТИРОВАН ВНИЗУ*/

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Преподаватель");

require_once('functions-zayavki-na-praktiku.php');
$connect=connection();

if (isset($_GET['student_id'])) {
	$student_practic_id=$_GET['student_id'];
	$connect->query("UPDATE Practices.student_practic SET status='1' WHERE id = '$student_practic_id';");
}

if (isset($_GET['student_theme'])) {
	change_student_theme($connect,$_GET['student_theme'],$_GET['new_theme']);
}
/*$user_id = get_user_id($connect);
#$user_id=251;

$fio_resultset = $connect->query("SELECT NAME,LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
print("[debug] Пользователь: ".$fio_resultset["LAST_NAME"]." ".$fio_resultset["NAME"]." ".$fio_resultset["SECOND_NAME"]);
$groups=get_user_groups($connect,$user_id);

if (group_check($groups,18) == false){
	exit();
}
$teacher_id=get_teacher_id($connect,$user_id);

*/

$teacher = $connect->query("SELECT * FROM Practices.teachers Where id = 105")->Fetch();// get_user_fields($USER);
	$teacher_id = $teacher["id"];//$teacher["UF_MIRA_ID"];
/*if (checkIsTeacher($connect,$teacher["ID"]) == false){
			exit();
	}*/
	print("[debug] Пользователь: ".$teacher["LAST_NAME"]." ".$teacher["NAME"]." ". $teacher_id);

/*================= НАГРУЗКА НА ПРЕПОДАВАТЕЛЯ ==================*/

$teacher_work_load =  $teacher["work_load"];
print("///  ///".$teacher_work_load);

/*================= ОБРАБОТЧИК КНОПОК "ДЕЙСТВИЕ" ==================*/

if (isset($_GET['done'])) {
    $connect->query("UPDATE Practices.student_practic SET status = 1 WHERE id = ".$_GET['done']);
	//$teacher_work_load = work_load_decrement($connect, $_GET['done']);
	//work_load_decrement($connect, $_GET['done']);
	//$teacher_work_load--;
	//$teacher_work_load =  $teacher["work_load"];
}
if (isset($_GET['remake'])) {
    $connect->query("UPDATE Practices.student_practic SET status = 2 WHERE id = ".$_GET['remake']);
	//$teacher_work_load = work_load_increment($connect, $_GET['remake']);
	//work_load_increment($connect, $_GET['remake']);
	//$teacher_work_load++;
	//$teacher_work_load =  $teacher["work_load"];
}

/*================= ОБРАБОТЧИК КНОПКИ "ФАЙЛ" ==================*/

if (isset($_GET['download'])){  
	Download_Otchet($student_request['company_path']);
}

/*================= ОБРАБОТЧИК ЧЕКБОКСА "ПОКАЗАТЬ ОТМЕНЕННЫЕ" ==================*/

session_start();

if (isset($_POST['showUnchecked'])) {
    $_SESSION['showUnchecked'] = ($_POST['showUnchecked'] === '1') ? true : false;
} else {
    $_SESSION['showUnchecked'] = false;
}

$showUnchecked = isset($_SESSION['showUnchecked']) ? $_SESSION['showUnchecked'] : false;


?>
<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
    <style>

		/*========================= ОСНОВА =========================*/
        /*========================= ОСНОВА =========================*/

		.table {
			background: #ffffff !important;
			border-collapse: collapse;
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
			font-size: 14px;
			text-align: left;
			max-width: 1450px;
			min-width: 800px;
			width: 100%;
			margin: 0 auto;
			color: #1E8EC2;
			font-family: Helvetica Neue OTS, sans-serif;
		}

		.thead {
			border-bottom: 1px solid black;
		}

		.th {
			text-align: center;
			font-family: inherit;
		}

		.td {
			text-align: center;
			font-family: inherit;
			text-align: center;
    		vertical-align: middle;
		}

		.td-status{
			width: 150px;
		}

		.block-div{
			display: flex;
            justify-content: center;
            align-items: center;
			text-align: center;
		}

		/*========================= КНОПКИ =========================*/
        /*========================= КНОПКИ =========================*/

        .btn {
            background: none;
            color: inherit;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            outline: inherit;
			color: #1E8EC2;
        }

		.btn:hover{
			text-decoration: underline;
			color: #1E8EC2;
		}

		/*======================== ДЕЙСТВИЕ ========================*/
        /*======================== ДЕЙСТВИЕ ========================*/

		.action {
            list-style-type: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0;
			margin: 0;
        }

		.dropdown-item1 {
			background: url(https://cdn-icons-png.flaticon.com/512/5629/5629189.png) 50% 50% no-repeat; /*(https://cdn-icons-png.flaticon.com/512/8832/8832098.png)*/
			background-size: contain;
			border-radius: 100%;
			width: 30px;
			height: 30px;
		}

		.dropdown-item1:hover {
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
		}

		.dropdown-item2 {
			background: url(https://cdn-icons-png.flaticon.com/512/10727/10727988.png) 50% 50% no-repeat; /*(https://cdn-icons-png.flaticon.com/512/179/179386.png)*/
			background-size: contain;
			border-radius: 100%;
			width: 30px;
			height: 30px;
		}

		.dropdown-item2:hover {
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
		}

		/*========================= СТАТУС =========================*/
        /*========================= СТАТУС =========================*/

		.block-status_ok {
			display: inline-block; 
			background-color: #b1f0ad; 
			padding: 7px; 
			border-radius: 15px;
			text-align: center;
		}

		.block-status_fail {
			display: inline-block; 
			background-color: #fadadd; 
			padding: 7px; 
			border-radius: 15px;
			text-align: center;
		}

		.status-check_fail {
            color: #f23a11;
        }

        .status-check_ok {
            color: #1F9254;
        }

		/*======================= НАД ТАБЛИЦЕЙ =======================*/
        /*======================= НАД ТАБЛИЦЕЙ =======================*/

        .remote {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            font-size: 14px;
            flex-basis: 100px;
        }

        .remote-rigth {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mar-off {
            margin: 0;
        }

		.checkbox {
			margin: 2px 0 0 3px;
		}

		.label-check{
			display: flex;
			align-items: center;
			justify-content: center;
		}

    </style>

</head>

<body class="body">
    <table class="table">
		<div class="remote">
			<div class="remote-left">
				<? echo'<p class="mar-off">Осталось мест: '.$teacher_work_load.'</p>'; ?>
			</div>
			<div class="remote-rigth">
				<p class="mar-off">Показать отмененные</p>
				<form method="post">
					<label class="label-check">
						<input class="checkbox" type="checkbox" name="showUnchecked" value="1" <? if ($showUnchecked) echo 'checked'; ?> onchange="this.form.submit()">
					</label>
				</form>
			</div>
		</div>
        <thead class="thead">
            <tr class="tr">
                <th class="th">ФИО студента</th>
                <th class="th">Компания</th>
                <th class="th">Тема практики</th>
                <th class="th">Договор</th>
                <th class="th">Статус</th>
                <th class="th">Действие</th>
            </tr>
        </thead>
        <tbody class="tbody">
				<!-- <tr class="tr">
                        <td class="td"><strong class="strong"> '.$student_fio.' </strong></td>
                        <td class="td"> '.$company_name.' </td>
                        <td class="td"> '.$theme.' </td>
						<td class="td">
                            <div class="block-file">
                                <button class="btn-file">Файл</button>
                            </div>
						</td>
						<td class="td status-check_fail"> Не проверено! </td>
                        <td class="td">
                            <ul class="action" aria-labelledby="btnGroupDrop1">
                                <form>
                                    <li><button type="sumbit" name="done" value="'.$sp['id'].'" class="btn dropdown-item1" href="#"></button></li>
                                </form>
                                <form>
                                    <li><button type="sumbit" name="noShow" value="'.$sp['id'].'" class="btn dropdown-item2" href="#"></button></li>
                                </form>
                            </ul>
                        </td>
                    </tr> -->
            <?php 
                    //$student_practics=get_student_practics($connect,$teacher_id);
					$student_practics=$connect->query('SELECT * from Practices.student_practic WHERE teacher_id = '.$teacher['id'].';');
                    // foreach($student_practics as $student_practic){
						while($sp = $student_practics->Fetch()){

							if ($showUnchecked && $sp["status"] != 2) {
								continue; // Пропускаем, если нужно показать только не принятых
							}
							/*if ($sp["status"] == 1){
                            	continue;
							}*/

                        $student_id   = $sp["student_id"];
							//print_r($student_id.'<br>');
												$student_fio  = $connect->query("SELECT fio FROM Practices.students WHERE id = '$student_id';")->Fetch()["fio"];
							//print_r($student_fio.'<br>');
												$company_id   = $sp["company_id"];
							//print_r($company_id.'<br>');
												$company_name = $connect->query("SELECT name FROM Practices.companies WHERE id = '$company_id';")->Fetch()["name"];
							//print_r($company_name.'<br>');
												$theme        = $sp["theme"];
							//print_r($theme.'<br>');
												$student_practic_id = $sp["id"];
							//print_r($student_practic_id.'<br>');

						/*================= СБОРКА ТАБЛИЦЫ ==================*/
						/*================= СБОРКА ТАБЛИЦЫ ==================*/
						
						echo '
							<tr class="tr">
							<td class="td">
								<div class="block-div">
									<strong class="strong"> '.$student_fio.' </strong> 
								</div>
							</td>';
						if($company_name != "")
							echo '
								<td class="td"> 
									<div class="block-div"> '.$company_name.' </div>
								</td>';
						else
							echo '
								<td class="td"> 
									<div class="block-div"> <p class="mar-off">Своя компания</p> </div>
								</td>';
						echo '
							<td class="td"> 
								<div class="block-div"> '.$theme.' </div">
							</td>
							<td class="td"> 
								<form class="block-div">
									<button name="download" value="'.$sp['name'].'" class="btn">Файл</button>
								</form>
							</td>';
						if($sp["status"] == 1)
							echo ' 
								<td class="td td-status">
									<div class="block-status_ok">
										<span class="status-check_ok">Принят</span>
									</div>
								</td>';
						else
							echo ' 
								<td class="td td-status">
									<div class="block-status_fail">
										<span class="status-check_fail">Не принят</span>
									</div>
								</td>';
						echo '<td class="td">
								<ul class="action" aria-labelledby="btnGroupDrop1">
									<form>
										<li><button type="sumbit" name="done" value="'.$sp['id'].'" class="btn dropdown-item1" href="#"></button></li>
									</form>
									<form>
										<li><button type="sumbit" name="remake" value="'.$sp['id'].'" class="btn dropdown-item2" href="#"></button></li>
									</form>
								</ul>
							</td>
						</tr>';
                ?>
        </tbody>
        <?php
			}
		?>
    </table>
</body>

</html>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>


<?php /*

/*----------------------- BACKUP -----------------------
/*----------------------- BACKUP -----------------------

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Преподаватель");

require_once('../../../studentu/praktika/functions.php');
$connect=connection();

if (isset($_GET['student_id'])) {
	$student_practic_id=$_GET['student_id'];
	$connect->query("UPDATE Practices.student_practic SET status='1' WHERE id = '$student_practic_id';");
}

if (isset($_GET['student_theme'])) {
	change_student_theme($connect,$_GET['student_theme'],$_GET['new_theme']);
}
function change_student_theme($connect,$student_practic_id,$new_theme){
	$connect->query("UPDATE Practices.student_practic SET theme='$new_theme' WHERE id = '$student_practic_id';");
}
function get_teacher_id($connect,$user_id){
	$user = $connect->query("SELECT NAME, LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
	$fio= $user["LAST_NAME"]." ".$user["NAME"]." ".$user["SECOND_NAME"];
	$teacher_id = $connect->query("SELECT * FROM Practices.teachers WHERE fio = '$fio';")->Fetch()["id"];
	return $teacher_id;
}

function get_student_practics($connect,$teacher_id){
	$student_practics = $connect->query("SELECT * FROM Practices.student_practic WHERE teacher_id = '$teacher_id';");
	return $student_practics;
}
$user_id = get_user_id($connect);
#$user_id=251;

$fio_resultset = $connect->query("SELECT NAME,LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
print("[debug] Пользователь: ".$fio_resultset["LAST_NAME"]." ".$fio_resultset["NAME"]." ".$fio_resultset["SECOND_NAME"]);

$groups=get_user_groups($connect,$user_id);

if (group_check($groups,18) == false){
	exit();
}

$teacher_id=get_teacher_id($connect,$user_id);


?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			.table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 80%;
            border: 0;
            background-color: #eeeeee;
         }
         .td{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
         }
         .th_1,. th_2,. th_3, th_4{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            width: 25%;
         }
		 .th_1{
			width: 25%;
		 }
         .th_2{
			width: 25%;
         }
		.th_3{
            width: 35%;
         }
		 .th_4{
            width: 15%;
         }
         .tr{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;

         }
         .tr:hover{
            background-color: #555;
            color: white;
         }
		 .theme_text_box{
		 	width: 75%;
			font-size: 15px;
			padding: 5px 2px; 
  			justify-content: left;
		 }
		 .btn{
		 }
		 .btn_change{
		 	display: inline-block;
			height: 40px;
			width: 20%;
		 }
		</style>
	</head>
	<body>
		<table class="table">
			<tr class="tr">
				<th class="th_1">ФИО</th>
				<th class="th_2">Компания</th>
				<th class="th_3">Тема</th>
			    <th class="th_4">Статус</th>
			</tr>
			<?php 
				$student_practics=get_student_practics($connect,$teacher_id);
				foreach($student_practics as $student_practic){
					if ($student_practic["status"] == 1){
						continue;
					}
					$student_id   = $student_practic["student_id"];
					$student_fio  = $connect->query("SELECT fio FROM Practices.students WHERE id = '$student_id';")->Fetch()["fio"];
					$company_id   = $student_practic["company_id"];
					$company_name = $connect->query("SELECT name FROM Practices.companies WHERE id = '$company_id';")->Fetch()["name"];
					$theme        = $student_practic["theme"];
					$student_practic_id = $student_practic["id"];
			?>
					<tr class="tr">
						<td class="td"> <? echo $student_fio  ?> </td>
						<td class="td"> <? echo $company_name ?> </td>
						<td class="td"> 
							<form>
								<input name="new_theme" type="text" class="theme_text_box" value="<? echo $theme ?>"> 
								<button name="student_theme" class="btn_change" value="<? echo $student_practic_id ?>" type="submit">Принять изменения</button>
							</form>
						</td>
						<td class="td"> <form> <button name="student_id" class="btn" value="<? echo $student_practic_id ?>" type="submit">Принять заявку</button> </form> </td>
					</tr>
			<?php
				}
			?>
		</table>


	</body>
</html>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");*/?> 