{* Smarty *}
{* Список встреч *}
{* Используется в: *}
{*     статистике личных встреч *}
{*     статистике разрядов *}

    {if $Meetings|count==0}
    Встречи отсутствуют
    {/if}
        {$Last_t_Id=-1}
        <table>
        
        {foreach $Meetings as $meeting}
        {if $Last_t_Id!=$meeting.t_Id}
        <tr>
            <td colspan="4" align="center">
            <br>
                <a href="{if empty($meeting.t_URL)}#{else}{$meeting.t_URL}{/if}">
                    <b>{$meeting.MeetingDate} {IF isset($meeting.ttype_Name)} - {$meeting.ttype_Name}{/IF} {IF !empty($meeting.t_Name)} - {$meeting.t_Name}{/IF} {IF isset($meeting.c_Name)} - {$meeting.c_Name}{/IF}</b>
                </a>
            </td>
        </tr>
        {/if}
        
        <tr>
            <td align="right">
        	    {$rankData = $meeting->m_WinnerPlayerId('Model_Player')->GetRank($meeting.t_DateTime)}
                <a href="?ctrl=Profile&PlayerId={$meeting->m_WinnerPlayerId}">{$meeting.WinnerName}</a>
        	    {if is_array($rankData)}<img src="images/black_medal_16.png" title="{$rankData.r_Name} ({$rankData.pr_DateFrom|date_format:"%d.%m.%Y"}-{$rankData.pr_DateTo|date_format:"%d.%m.%Y"|default:"..."})" onclick="alert(this.title);">{/if}
                {if isset($meeting.Winner2Name)}
                    <br>
                    <a href="?ctrl=Profile&PlayerId={$meeting->m_Winner2PlayerId}">{$meeting.Winner2Name}</a>
                {/if}
            </td>
            <td align="center">{$meeting.Score}</td>
        	<td>
        	    {$rankData = $meeting->m_LoserPlayerId('Model_Player')->GetRank($meeting.t_DateTime)}
        	    <a href="?ctrl=Profile&PlayerId={$meeting->m_LoserPlayerId}">{$meeting.LoserName}</a>
        	    {if is_array($rankData)}<img src="images/black_medal_16.png" title="{$rankData.r_Name} ({$rankData.pr_DateFrom|date_format:"%d.%m.%Y"}-{$rankData.pr_DateTo|date_format:"%d.%m.%Y"|default:"..."})" onclick="alert(this.title);">{/if}
        	    {if isset($meeting.Loser2Name)}
            	    <br>
            	    <a href="?ctrl=Profile&PlayerId={$meeting->m_Loser2PlayerId}">{$meeting.Loser2Name}</a>
        	    {/if}
        	</td>
        	<td>
        	    {if !empty($meeting.m_VideoURL)}
            	    <a href="http://tt-saratov.ru/statistics/jak/randompic_module.php?VideoURL={$meeting.m_VideoURL}"><img src="images/video.png" title="Видео встречи"></a>
        		{/if} 
        		
        	    {if !empty($meeting.m_Note)}<img src="images/note.gif" title="{$meeting.m_Note}">{/if} 
        
        	    {if $meeting.m_AffectRating==0}<img src="images/stop.gif" title="Не влияет на рейтинг">{/if}
        	</td>
        </tr>
        	
        {$Last_t_Id=$meeting.t_Id}
        	
        {/foreach}
        </table>
