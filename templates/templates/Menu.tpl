{* Smarty *}

	<table border="0">
		<tr class="cat" align="center">
			<td><a href="?ctrl=Index">Главная</a></td>
		{*	<td><a href="?ctrl=TournamentTypes">Типы турниров</a></td>	*}
			<td><a href="?ctrl=Courts">Площадки/игродромы</a></td>
			<td><a href="?ctrl=Tours">Турниры</a></td>

			{IF $Auth->CR($player_AR)}<td><a href="?ctrl=Profile&PlayerId={$Auth->AuthPlayerId}">Мой профиль</a></td>{/IF}
			
			{IF $Auth->CR($admin_AR)}<td><a href="?ctrl=Ratings">Рейтинги</a></td>{/IF}
		{*	{IF $Auth->CR($admin_AR)}<td><a href="http://tt-saratov.ru/statistics/jak/admrating.php?module=playersadmin">Игроки</a></td>{/IF}  *}
			{IF $Auth->CR($admin_AR+$tourorg_AR)}<td><a href="?ctrl=Organizer">Проведение турниров</a></td>{/IF}
			{IF $Auth->CR($admin_AR)}<td><a href="?ctrl=Admin">Администрирование</a></td>{/IF}
		</tr>
	</table>
 

