<html>
  <head>
    <style>
/* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
.error {
  border: 2px solid red;
}
    </style>
  </head>
  <body>
    <!-- <form action="" method="POST">
      <input name="fio" ?php if ($errors['fio']) {print 'class="error"';} ?> value="?php print $values['fio']; ?>" />
      <input type="submit" value="ok" />
    </form> -->

		<form action="" method="POST">
	FIO:
  <input name="fio" <?php if ($errors['fio']) {print 'class="error"';} ?> value="<?php print $values['fio']; ?>" />
  <select name="year" <?php if ($errors['year']) {print 'class="error"';} ?> value="<?php print $values['year']; ?>">
    <?php 
    for ($i = 1922; $i <= 2022; $i++) {
      printf('<option value="%d">%d год</option>', $i, $i);
    }
    ?>
  </select>
			<br />	
			<p>
	<label>
		Напишите биографию:<br />
		<textarea name="biography" <?php if ($errors['biography']) {print 'class="error"';} ?> value="<?php print $values['biography']; ?>">начальное значение</textarea>
	</label><br /><p\>
			<label>
				Email
				<br />
				<input name="email" <?php if ($errors['email']) {print 'class="error"';} ?> value="<?php print $values['email']; ?>" value="Введите почту">
			</label>
			</p>

			<p class="text-center">Кол-во конечностей</p>
        <div class="container-fluid btn-group mb-3" role="group">

          <input type="radio" class="btn-check" name="limbs" id="option1" value="1" <?php if($values['limbs'] == "1") {print 'checked';}?>>
          <label class="btn btn-outline-primary" for="option1">1</label>

          <input type="radio" class="btn-check" name="limbs" id="option2" value="2" <?php if($values['limbs'] == "2") {print 'checked';}?>>
          <label class="btn btn-outline-primary" for="option2">2</label>

          <input type="radio" class="btn-check" name="limbs" id="option3" value="3" <?php if($values['limbs'] == "3") {print 'checked';}?>>
          <label class="btn btn-outline-primary" for="option3">3</label>

          <input type="radio" class="btn-check" name="limbs" id="option4" value="4" <?php if($values['limbs'] == "4") {print 'checked';}?>>
          <label class="btn btn-outline-primary" for="option4">4</label>

        </div>
							 <p>Выберите пол: <br>
							 
<label><input type="radio" checked="checked"
					name="gender" value="M" />
					 M</label>
				<label><input type="radio"
					name="gender" value="F" />
					 F</label></p>
	<label>Сверхспособности:<br />
		<select name="ability[]" multiple="multiple"id="superpowers" <?php if ($errors['ability']) {print 'class="error"';} ?> value="<?php print $values['ability']; ?>">
			<option value="1"<?php if(in_array("1", $values['ability'])) {print('selected="selected"');} ?>>Левитация</option>
			<option value="2"<?php if(in_array("1", $values['ability'])) {print('selected="selected"');} ?> >Прохождение сквозь стены </option>
			<option value="3"<?php if(in_array("1", $values['ability'])) {print('selected="selected"');} ?> >Бессмертие</option>
		</select>
	</label>

		<p>
			Чекбокс:<br />
      <label><input type="checkbox"
        name="checkbox" />
        C контрактом ознакомлен</label><br />
		</p>
		<p>
			<input type="submit" value="Отправить" />
		</p>
		</form>
		<?php
if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}
// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>
  </body>
</html>