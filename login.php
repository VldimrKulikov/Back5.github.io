
<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
		
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
	$messages = array();
  $errors = array();
  $errors['login'] = !empty($_COOKIE['login_error']);
	if (!empty($errors['login'])) {
    setcookie('login_error', '', 100000);
    $messages['login'] = '<p class="msg">set login</p>';
  }
  $errors['password'] = !empty($_COOKIE['password_error']);
	if (!empty($errors['password'])) {
    setcookie('password_error', '', 100000);
    $messages['password'] = '<p class="msg">set pass</p>';
  }
  $errors['auth'] = !empty($_COOKIE['auth_error']);
  if (!empty($errors['auth'])) {
    setcookie('auth_error', '', 100000);
    $messages['auth'] = '<p class="msg">пользователь не существует</p>';
  }
	include('loginform.php');
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
	$login = $_POST['login'];
  $password = $_POST['password'];

  if (empty($login)) {
    setcookie('login_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  if (empty($password)) {
    setcookie('password_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if ($errors) {
    header('Location: login.php');
    exit();
  }
  // Если все ок, то авторизуем пользователя.
	$user = 'u52804';
  $pass = '3418446';
  $db = new PDO('mysql:host=localhost;dbname=u52804', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  $stmt = $db->prepare('SELECT user_id FROM logpas WHERE (login = ?) AND (password = ?) ');
  $stmt->execute([$login, md5($password)]);

  if ($stmt->rowCount() > 0) {
    $_SESSION['login'] = $_POST['login'];
    $stmt = $db->prepare("SELECT user_id FROM logpas WHERE login = ?");
    $stmt->execute([$login]);
    $_SESSION['uid'] = $stmt->fetchColumn();
    header('Location: ./');
  } else {
    setcookie('auth_error', '1', time() + 24 * 60 * 60);
		// Делаем перенаправление.
    header('Location: login.php');
    exit();
  }
}
