{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
<h1>Проведение турниров</h1>
	{* Admin menu *}
	<table border="0">
		<tr class="row1" align="center">
			<td><a href="?ctrl=Organizer&act=NewTour">Новый турнир</a></td>
			<td><a href="?ctrl=Organizer&act=ActiveToursList">Мои текущие турниры</a></td>
			<td><a href="?ctrl=Organizer&act=OldToursList">Мои старые турниры</a></td>
			<td><a href="?ctrl=Organizer&act=OrganizerPlayersList">Игроки</a></td>
		</tr>
	</table>

	{block name=AdminBody}
		Добро пожаловать в проведение турниров, {$Auth->AuthPlayer->p_Name|default:$Auth->username}!
	{/block}

{/block}

