<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<table border="1">
	<tr>
		<th>ID</th>
		<th>Фотография</th>
		<th>ФИО</th>
		<th>Телефон</th>
		<th>Статус</th>
		<th>Дата последнего входа</th>
		<th>Список имен элементов инфоблока</th>
	</tr>
	<?php foreach ($arResult['USERS'] as $USER) : ?>
	<tr>
		<td> <?= $USER['ID'] ?> </td>
		<td><img style="max-width:100px;width:100%" src="<?=$USER['AVATAR']?>" alt="Фото не добавлено"></td>
		<td><?= $USER['FULL_NAME']?></td>
		<td>  <a href="tel:<?= $USER['WORK_PHONE'] ?>"><?= $USER['WORK_PHONE'] ?></a> </td>
		<td><?= $USER['ONLINE'] ?></td>
		<td><?= $USER['LAST_LOGIN']?></td>
		<td><?= is_array($USER['ELEMENTS']) ? implode(', ', $USER['ELEMENTS']) : $USER['ELEMENTS']?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if ($arResult['BUTTONS_COUNT'] > 1) : ?>
	Страницы: <?php for ($i=1; $i<=$arResult['BUTTONS_COUNT']; $i++) : ?>
	<?php if($i === $arResult['PAGE']) : ?>
		<strong> <?=$i?> </strong>
	<?php else :?>
		<a href="/?page=<?=$i?>"> <?=$i?> </a>
	<?php endif;?>
<?php endfor; ?>
<?php endif; ?>
