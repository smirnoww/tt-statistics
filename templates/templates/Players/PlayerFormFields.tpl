{* Smarty *}
{* For including in New and Edit form *}
<style>
	.forDelete {
		background-color:red
	}
	.deletePlayerRank{
		cursor: pointer
	}
</style>
	<table>
        <tr>
            <td align="right">ФИО:</td>				    
            <td>
                <input type="text" name="playerData[p_Name]" 		size="40"  value="{$Player.p_Name|escape}"/ > 
                <label for="sex_M">М</label><input name="playerData[p_Sex]" type="radio" value="1" {IF $Player.p_Sex}checked{/IF}>
                <label for="sex_F">Ж</label><input name="playerData[p_Sex]" type="radio" value="0" {IF !$Player.p_Sex}checked{/IF}> 
            </td>
        </tr>
	    <tr>
			<td align="right">Пользователь форума:</td>	
			<td>
				<select id="p_Login" 	name="playerData[p_Login]">
		        	<option value="-1">пользователь форума неизвестен</option>',
    				{foreach $ForumUsers as $fUser}
						<option value="{$fUser.username_clean}" {IF $Player.p_Login==$fUser.username_clean}selected{/IF}>{$fUser.username} ({$fUser.user_birthday_asDateTime})</option>
				    {/foreach}
				</select>
				<input type="checkbox" name="playerData[p_ActivatedLogin]"  {IF $Player.p_Activatedlogin}checked{/IF}/ > подтверждён
			</td>
		</tr>
	    <tr><td align="right">Дата рождения:</td>   	<td><input type="text" name="playerData[p_Birthdate]"	size="10" maxlength="10" placeholder="дд.мм.гггг" value="{$Player.p_Birthdate}"/></td></tr>
	    <tr><td align="right">Город/нас.пункт:</td>   	<td><input type="text" name="playerData[p_City]"	    size="40" maxlength="255" value="{$Player.p_City}" /></td></tr>
	    <tr><td align="right">Аватар:</td>		    	<td><input type="file" name="p_Avatar"> <img width="80" heigth="80" src="?ctrl=Players&act=GetAvatar&PlayerId={$Player.p_Id}"></td></tr>
	    <tr><td align="right">Фото:</td>		    	<td><input type="file" name="p_Photo">  <img width="80" heigth="80" src="?ctrl=Players&act=GetPhoto&PlayerId={$Player.p_Id}"></td></tr>
	    <tr>
			<td align="right">E-Mail:</td>
			<td>
				<input type="text" id="p_EMail" 				name="playerData[p_EMail]"  	size="40" value="{$Player.p_EMail}">
				<input type="checkbox" id="p_EMailConfirmed"	name="playerData[p_EMailConfirmed]" title="E-Mail подтверждён" {IF $Player.p_EMailConfirmed}checked{/IF}/ >
			</td>
		</tr>
	    <tr><td align="right">Контакты:</td>	    	<td><input type="text" name="playerData[p_Contacts]"	size="40" value="{$Player.p_Contacts}" /></td></tr>
	    <tr><td align="right">Доп. информация:</td>	    <td><textarea name="playerData[p_Info]"	cols="60" rows="10">{$Player.p_Info|replace:'<br>':"\n"}</textarea></td></tr>
	</table>

	<h3>Квалификация</h3>
	<input type="button" id="addRank" value="Добавить"/>
	<table>
		<thead>
			<tr>
				<th>Квалификация</th>
				<th>Присвоена</th>
				<th>Организацией</th>
				<th>До</th>
				<th>Примечание</th>
			</tr>
		</thead>
		<tbody id="RankTBody">
			{foreach $PlayerRanks as $rank}
				{include file="Players/PlayerFormEditRankTR.tpl"}
			{/foreach}
		</tbody>
	</table>



<script type="text/javascript">

	var newPR_ID = 0;
	$(function(){
		AdjustRankTR();
		
		$('#addRank').click(function(event){
			newPR_ID--;
			$.get(
				'?ctrl=Players&act=GetPlayerFormRankTR',
				{
					"pr_Id"			: newPR_ID,
					"pr_PlayerId"	: {$Player.p_Id}
				},
				function(data) {
					$('#RankTBody').append(data);
					AdjustRankTR();
				},
				"html"
			);
		});
	});


	// настройка строк с разрядами
	function AdjustRankTR() {
		$( ".date" ).datepicker({
			firstDay: 	1,
			dayNamesMin:[ "Вс", "Пн", "Вт", "Ср", "Че", "Пт", "Сб" ],
			dateFormat:	"dd.mm.yy",
			changeYear:	true,
			onSelect: 	function( dateText, inst ) {
							var TR = $(this).closest('TR');
							var newDate = new Date( inst.currentYear+1, inst.currentMonth, inst.currentDay);
							//myDate.setFullYear(myDate.getFullYear() + 1);
							newDate.setDate(newDate.getDate() - 1);
							$(TR).find('INPUT.expireDate').val($.datepicker.formatDate('dd.mm.yy', newDate));				
						}
		});

		$('.deletePlayerRank').click(function(event){
			var TR = $(event.target).closest('TR');
			TR.toggleClass('forDelete');
			$(TR).find('input.deleteThis').val(function( index, value ) {
				return (parseInt(value)+1) % 2;
			});
		});
	}
</script>

