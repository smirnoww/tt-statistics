{* Smarty *}


<table>
    <tr align="center">
	    <td>  <!-- заголовок с аватарами игроков -->
	        <table>
	            <tr align="center">
	                <td valign="bottom">
	                    <a href="?ctrl=Profile&PlayerId={$FirstPlayer->p_Id}">
	                        <img height="80" src="?ctrl=Players&act=GetAvatar&PlayerId={$FirstPlayer->p_Id}">
	                        <h3>{$FirstPlayer->p_Name}</h3>
	                    </a>
	                    {$FirstPlayer->NumberOfWins} побед
	                </td>
	                <td valign="top" align="center">
	                    <h2>vs</h2>
	                    всего встреч: {$Meetings|@count}
	                </td>
	                <td valign="bottom">
	                    <a href="?ctrl=Profile&PlayerId={$SecondPlayer->p_Id}">
	                        <img height="80" src="?ctrl=Players&act=GetAvatar&PlayerId={$SecondPlayer->p_Id}">
	                        <h3>{$SecondPlayer->p_Name}</h3>
	                    </a>
	                    {$SecondPlayer->NumberOfWins} побед
	                </td>
	            </tr>
	        </table>
	        
	    </td>
    </tr>
    
    <tr align="center">
	    <td> <!-- Список личных встреч -->
		{include './MeetingsList.tpl'} {* MeetingsList.tpl из той же папки, где и этот шаблон *}
		</td>
    </tr>
</table>
