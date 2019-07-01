{* Smarty *}
<script>
    //Cross-browser JSON Serialization in JavaScript: http://www.sitepoint.com/javascript-json-serialization/
    var JSON = JSON || {};
    // implement JSON.parse de-serialization
    JSON.parse = JSON.parse || function (str) {
    	if (str === "") str = '""';
    	eval("var p=" + str + ";");
    	return p;
    };


    // Загрузка списка соперников
    function LoadOpponents(p_Id) {
		var Qxmlhttp;
		Qxmlhttp=new XMLHttpRequest();
		Qxmlhttp.open("GET",'{$curPageURL}?ctrl=Players&act=GetOpponents&p_Id='+p_Id, false);
		Qxmlhttp.send();

		if (Qxmlhttp.readyState==4 && Qxmlhttp.status==200) {
			//document.getElementById("divhidden").innerHTML = 'URL:{$curPageURL}?ctrl=Players&act=GetOpponents&FirstPlayerId='+p_Id+"\n"+Qxmlhttp.responseText;
			opponents = JSON.parse(Qxmlhttp.responseText);
			
			secondPlayerCombo = document.getElementById('SecondPlayer');
			
            // удаляем старых оппонентов
            while (secondPlayerCombo.options.length > 0) {
        		secondPlayerCombo.remove(0);
        	}
        	
        	// заполняем новыми
	        opponents.forEach(function(item, i, arr){
                var option = document.createElement("option");
                option.text = item.p_Name;
                option.value = item.p_Id;
                secondPlayerCombo.appendChild(option);
			});
			
		}
		else {
		    alert('Ошибка загрузки соперников!');
		}
    }
    
    // Загрузка статистики асинхронно
    function ShowHeadToHeadStat(FirstPlayerId, SecondPlayerId) {
		document.getElementById("HeadToHeadStatTD").innerHTML='<img src="images/tt.gif"><br>Идёт загрузка встреч ...';
    
		var Qxmlhttp;
		Qxmlhttp=new XMLHttpRequest();
		Qxmlhttp.onreadystatechange=function() {
			if (Qxmlhttp.readyState==4 && Qxmlhttp.status==200) {
				document.getElementById("HeadToHeadStatTD").innerHTML=Qxmlhttp.responseText;
			}
		}
		Qxmlhttp.open("GET",'{$curPageURL}?ctrl=Statistics&act=HeadToHeadStat&FirstPlayerId='+FirstPlayerId+'&SecondPlayerId='+SecondPlayerId,true);
		Qxmlhttp.send();
    }
</script>

{$Last_t_Id=-1}
<table>
    <tr align="center">
        <td align="center" colspan="3">
                    <h2>Статистика личных встреч</h2>
        </td>
    </tr>
    <tr>
        <td align="right">
			{if isset($FixedPlayer)} <!-- фиксированный игрок -->
				<select id="FirstPlayer" style="display: none">
					<option value="{$FixedPlayer->p_Id}">{$FixedPlayer->p_Name}</option>
				</select>
				<b>{$FixedPlayer->p_Name}</b>
			{else}  <!-- Список игроков -->
				{literal}
					<select id="FirstPlayer" onchange="if (this.value!=-1) {
															for (var i=0; i<this.length; i++){
																if (this.options[i].value == '-1' )
																	this.remove(i);
															}
															LoadOpponents(this.value);
														}">
				{/literal}
						<option value="-1">... выберите игрока ...</option>
						{foreach $PlayersList as $player}
							<option value="{$player->p_Id}">{$player->p_Name}</option>
						{/foreach}
					</select>
			{/if}
        </td>
        
        <td align="center">и</td>
        
        <td align="left">
            <select id="SecondPlayer">
                <option value="-1">... затем соперника ...</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="3">
            <input type="button" value="Показать" onclick="ShowHeadToHeadStat(document.getElementById('FirstPlayer').value, document.getElementById('SecondPlayer').value);">
        </td>
    
    </tr>
    <tr>
        <td align="center" colspan="3" id="HeadToHeadStatTD">
            
        </td>
    
    </tr>
</table>

{* Загрузим соперников для предопределённого игрока *}
{if isset($FixedPlayer)} <!-- фиксированный игрок -->
	<script>
		LoadOpponents({$FixedPlayer->p_Id});
	</script>
{/if}