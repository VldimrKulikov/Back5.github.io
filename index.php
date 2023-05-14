<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
		
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('password', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['password']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
	$errors['email'] = !empty($_COOKIE['email_error']);
	$errors['year'] = !empty($_COOKIE['year_error']);
	$errors['biography'] = !empty($_COOKIE['biography_error']);
	$errors['limbs'] = !empty($_COOKIE['limbs_error']);
	$errors['gender'] = !empty($_COOKIE['gender_error']);
	$errors['ability'] = !empty($_COOKIE['ability_error']);
	$errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  // Выдаем сообщения об ошибках.
	if ($errors['fio']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('fio_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
  // TODO: тут выдать сообщения об ошибках в других полях.
	if($errors['email']){
		setcookie('email_error','',100000);
		$messages[]='<div class="error">set email.</div>';
	}
  if($errors['year']){
		setcookie('year_error','',100000);
		$messages[]='<div class="error">Выберите год.</div>';
	}
	if($errors['biography']){
		setcookie('biography_error','',100000);
		$messages[]='<div class="error">set bio</div>';
	}
	if($errors['limbs']){
		setcookie('limbs_error','',100000);
		$messages[]='<div class="error">выбери конеч</div>';
	}
	if($errors['ability']){
		setcookie('ability','',100000);
		$messages[]='<div class="error">добавь способности</div>';
	}
	if($errors['checkbox']){
		setcookie('checkbox_error');
		$messages[]='<div class="error">поставь галку</div>';
	}
  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  // TODO: аналогично все поля.
	$values['email'] = empty($_COOKIE['email_value']) ? '' :strip_tags($_COOKIE['email_value']);
	$values['year'] = empty($_COOKIE['year_value']) ? '' : (int)$_COOKIE['year_value'];
	$values['biography'] = empty($_COOKIE['biography_value']) ? '' : strip_tags($_COOKIE['biography_value']);
	$values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : (int)$_COOKIE['limbs_value'];
	$values['ability'] = empty($_COOKIE['ability_value']) ? [] : json_decode($_COOKIE['ability_value']);
	$values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : (int)$_COOKIE['checkbox_value'];

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
		$login = $_SESSION['login'];
    try {
			$stmt = $db->prepare("SELECT name, year, email, limbs, gender, biography, checkbox FROM users WHERE id = ?");
      $stmt->execute([$person_id]);
      $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $stmt = $db->prepare("SELECT ability_id FROM user_ab WHERE user_id = ?");
      $stmt->execute([$person_id]);
      $abilities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
			
      $stmt = $db->prepare("SELECT user_id FROM logpas WHERE login = ?");
      $stmt->execute([$login]);
      $person_id = $stmt->fetchColumn();

      if (!empty($dates[0]['name'])) {
        $values['fio'] = $dates[0]['name'];
      }
      if (!empty($dates[0]['year'])) {
        $values['year'] = $dates[0]['year'];
      }
      if (!empty($dates[0]['email'])) {
        $values['email'] = $dates[0]['email'];
      }
      if (!empty($dates[0]['limbs'])) {
        $values['limbs'] = $dates[0]['limbs'];
      }
      if (!empty($dates[0]['gender'])) {
        $values['gender'] = $dates[0]['gender'];
      }
      if (!empty($dates[0]['checkbox'])) {
        $values['checkbox'] = $dates[0]['checkbox'];
      } 
      if (!empty($dates[0]['biography'])) {
        $values['biography'] = $dates[0]['biography'];
      } 
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    printf('<header><p>Вход с логином %s; uid: %d</p><a href=logout.php>Выйти</a></header>', $_SESSION['login'], $_SESSION['uid']);
  }
  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['fio'])|| !preg_match('/^([a-zA-Z\'\-]+\s*|[а-яА-ЯёЁ\'\-]+\s*)$/u', $_POST['fio'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
  }

	if (empty($_POST['year'])|| !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('year_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  }
	// if (empty($_POST['checkbox'])|| !($_POST['checkbox'] == 'on' || $_POST['checkbox'] == 1)) {
  //   // Выдаем куку на день с флажком об ошибке в поле fio.
  //   setcookie('checkbox', '1', time() + 24 * 60 * 60);
  //   $errors = TRUE;
  // }
	if($_POST['checkbox']==''){
    //print('Чекбокс<br/>');
    $errors = TRUE;
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
}
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('checkbox_value', $_POST['checkbox'], time() + 30 * 24 * 60 * 60);
  }
	if (empty($_POST['email']) || !preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $_POST['email'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }
	// if (empty($_POST['limbs'])|| !is_numeric($_POST['limbs']) ||($_POST['limbs']<1)||($_POST['limbs']>4)) {
  //   // Выдаем куку на день с флажком об ошибке в поле fio.
  //   setcookie('limbs', '1', time() + 24 * 60 * 60);
  //   $errors = TRUE;
  // }
	if($_POST['limbs'] !== '1' && $_POST['limbs'] !== '2' && $_POST['limbs'] !== '3' && $_POST['limbs'] !== '4'){  
		//print('Укажите количество конечностей<br/>');
		$errors = TRUE;
		setcookie('limbs_error', '1', time() + 24 * 60 * 60);
	}
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60);
}
	if (empty($_POST['gender'])|| !($_POST['gender']=='M' || $_POST['gender']=='F')) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
  }
	if (empty($_POST['ability']) || !is_array($_POST['ability'])) {  
    //print('Укажите способности<br/>');
    $errors = TRUE;
    setcookie('ability_error', '1', time() + 24 * 60 * 60);
}
else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('ability_value', json_encode($_POST['ability']), time() + 30 * 24 * 60 * 60);
}
	if (empty($_POST['biography']) || strlen($_POST['biography'])>150) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('biography_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);
  }

// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', '', 100000);
		setcookie('year_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('limbs_error', '', 100000);
    setcookie('biography_error', '', 100000);
    setcookie('ability_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }
	$user = 'u52804';
	$pass = '3418446';
	$db = new PDO('mysql:host=localhost;dbname=u52804', $user, $pass, [PDO::ATTR_PERSISTENT => true]);
	
  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.
		$login = $_SESSION['login'];
    try {
      $stmt = $db->prepare("SELECT id FROM users WHERE login = ?");
      $stmt->execute([$login]);
      $person_id = $stmt->fetchColumn();

      $stmt = $db->prepare("UPDATE users SET name = ?, year = ?, email = ?, limbs = ?, gender = ?, biography = ?, checkbox = ? WHERE id = ?");
      $stmt->execute([$_POST['fio'], $_POST['year'], $_POST['email'], $_POST['limbs'], $_POST['gender'], $_POST['biography'], $_POST['checkbox'], $person_id]);
      
      $stmt = $db->prepare("SELECT ability_id FROM user_ab WHERE person_id = ?");
      $stmt->execute([$person_id]);
      $abil = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

      if (array_diff($abil, $_POST['abilities'])) {
        $stmt = $db->prepare("DELETE FROM user_ab WHERE person_id = ?");
        $stmt->execute([$person_id]);

        $stmt = $db->prepare("INSERT INTO user_ab (user_id, ability_id) VALUES (?, ?)");
        foreach ($_POST['abilities'] as $superpower_id) {
          $stmt->execute([$person_id, $superpower_id]);
        }
      }
		}
 catch (PDOException $e) {
	print('Error : ' . $e->getMessage());
	exit();
}
		
			}
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    // Генерируем уникальный логин ID
		$login = 'u'.substr(uniqid(),-5);
		$password = substr(md5(uniqid()),0,10);
		$password_hash=password_hash($password,PASSWORD_DEFAULT);
		setcookie('login', $login);
		setcookie('password', $password);
// Вставляем данные в базу
$stmt = $db->prepare("INSERT INTO users (name, year, email, limbs, gender, biography, checkbox) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$_POST['fio'], $_POST['year'], $_POST['email'], $_POST['limbs'], $_POST['gender'], $_POST['biography'], $_POST['checkbox']]);
$person_id = $db->lastInsertId();
$stmt = $db->prepare("INSERT INTO user_ab (user_id, abitity_id) VALUES (?, ?)");
      foreach ($_POST['ability'] as $superpower_id) {
        $stmt->execute([$person_id, $superpower_id]);
      }

    // TODO: Сохранение данных формы, логина и хеш md5() пароля в базу данных.
			$stmt = $db->prepare("INSERT INTO logpas (user_id, login, password) VALUES (?, ?, ?)");
      $stmt->execute([$person_id, $login, md5($password)]);
    }
		 

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
