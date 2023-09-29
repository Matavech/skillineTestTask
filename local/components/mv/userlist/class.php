<?php
CONST COUNT_ON_PAGE = 5;

// На тестовом сервере последняя активность пользователей не вносится с таблицу без подключенного модуля socialnetwork.
// Сделал это чтобы работал метод IsOnLine
if($GLOBALS['USER']->IsAuthorized()) {
	CUser::SetLastActivityDate($GLOBALS['USER']->GetID());
}


class UserListComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		$this->fetchUserList();
		$this->includeComponentTemplate();
	}

	protected function fetchUserList()
	{

		$count = \Bitrix\Main\UserTable::getCount();
		$this->arResult['BUTTONS_COUNT'] = ceil($count / COUNT_ON_PAGE);
		$this->arResult['PAGE'] = (int)\Bitrix\Main\Context::getCurrent()->getRequest()->get('page') ?: 1;
		if ($this->arResult['PAGE'] <= 1 || $this->arResult['PAGE'] > $this->arResult['BUTTONS_COUNT'])
		{
			$offset = 0;
		}
		else
		{
			$offset = $this->arResult['PAGE'] * COUNT_ON_PAGE - COUNT_ON_PAGE;
		}

		$query = \Bitrix\Main\UserTable::query()->setSelect(['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LAST_LOGIN', 'WORK_PHONE', 'PERSONAL_PHOTO'])
												->setOrder(['LAST_LOGIN' => 'ASC'])
												->setLimit(COUNT_ON_PAGE)
												->setOffset($offset);
		$usersRaw = $query->fetchAll();
		foreach ($usersRaw as $userRaw)
		{
			$user['ID'] = $userRaw['ID'];
			$user['FULL_NAME'] = $userRaw['LAST_NAME'] . ' ' . $userRaw['NAME'] . ' ' . $userRaw['SECOND_NAME'];
			$user['LAST_LOGIN'] = FormatDate([
												 "today" => "today",
												 "yesterday" => "yesterday",
												 "d" => 'j F',
												 "" => 'j F Y',
											 ], $userRaw['LAST_LOGIN'], time() + CTimeZone::GetOffset())
									. ' (' . $userRaw['LAST_LOGIN'] . ')';
			$user['ONLINE'] = CUser::IsOnLine($userRaw['ID'], 120)  ? 'Online' : 'Offline';
			$user['AVATAR'] = CFile::GetPath($userRaw['PERSONAL_PHOTO']);
			$user['WORK_PHONE'] = $userRaw['WORK_PHONE'];
			$this->arResult['USERS'][] = $user;
		}
	}
}