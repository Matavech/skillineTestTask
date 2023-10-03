<?php

use Bitrix\Iblock\ElementTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}
CModule::IncludeModule("iblock");
// На тестовом сервере последняя активность пользователей не вносится с таблицу без подключенного модуля socialnetwork.
// Сделал это чтобы работал метод IsOnLine
global $USER;
if ($USER->IsAuthorized())
{
	CUser::SetLastActivityDate($USER->GetID());
}

class UserListComponent extends CBitrixComponent
{
	public static int $countOnPage = 5;

	public function executeComponent()
	{
		$this->fetchUserList();
		$this->includeComponentTemplate();
	}

	protected function fetchUserList()
	{

		//Теперь если в будущем добавится фильтр, то count будет реальным
		//Пока что фильтр - пустой массив
		$filter = [];
		if ($filter)
		{
			$query = \Bitrix\Main\UserTable::getList([
														 'filter' => $filter,
													 ]);
			$count = $query->getCount();
		}
		else
		{
			$count = \Bitrix\Main\UserTable::getCount();
		}

		$this->arResult['BUTTONS_COUNT'] = ceil($count / self::$countOnPage);

		$pageNumber = (int)\Bitrix\Main\Context::getCurrent()->getRequest()->get('page');
		$this->arResult['PAGE'] = 1;
		if ($pageNumber && $pageNumber <= $this->arResult['BUTTONS_COUNT'])
		{
			$this->arResult['PAGE'] = $pageNumber;
		}

		$offset = 0;
		if ($this->arResult['BUTTONS_COUNT'] > 1)
		{
			$offset = $this->arResult['PAGE'] * self::$countOnPage - self::$countOnPage;
		}

		$query = \Bitrix\Main\UserTable::getList([
													 'select' => [
														 'ID',
														 'NAME',
														 'LAST_NAME',
														 'SECOND_NAME',
														 'LAST_LOGIN',
														 'WORK_PHONE',
														 'PERSONAL_PHOTO',
														 'ELEMENTS.NAME',
													 ],
													 'order' => ['LAST_LOGIN' => 'ASC'],
													 'filter' => $filter,
													 'limit' => self::$countOnPage,
													 'offset' => $offset,
													 'runtime' => [
														 new \Bitrix\Main\Entity\ReferenceField(
															 'ELEMENTS',
															 ElementTable::class,
															 ['=this.ID' => 'ref.CREATED_BY'],
															 ['join_type' => 'LEFT']
														 ),
													 ],
												 ]);

		while ($row = $query->fetch())
		{
			$userId = $row['ID'];
			if (!isset($this->arResult['USERS'][$userId]))
			{
				$this->arResult['USERS'][$userId] = [
					'ID' => $row['ID'],
					'FULL_NAME' => $row['LAST_NAME'] . ' ' . $row['NAME'] . ' ' . $row['SECOND_NAME'],
					'LAST_LOGIN' => FormatDate([
												   "today" => "today",
												   "yesterday" => "yesterday",
												   "d" => 'j F',
												   "" => 'j F Y',
											   ], $row['LAST_LOGIN'], time() + CTimeZone::GetOffset())
						. ' ('
						. $row['LAST_LOGIN']
						. ')',
					'ONLINE' => CUser::IsOnLine($row['ID'], 120) ? 'Online' : 'Offline',
					'AVATAR' => $row['PERSONAL_PHOTO'] ? CFile::GetPath($row['PERSONAL_PHOTO']) : '',
					'WORK_PHONE' => $row['WORK_PHONE'],
				];
			}
			if (!empty($row['MAIN_USER_ELEMENTS_NAME']))
			{
				$this->arResult['USERS'][$userId]['ELEMENTS'][] = $row['MAIN_USER_ELEMENTS_NAME'];
			}
		}

	}
}