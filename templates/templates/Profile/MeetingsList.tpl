{* Smarty *}
<!-- табличка со встречами -->
<table border="0"><tbody>
    {foreach $Meetings as $Meeting}
        <tr class="row1">
            <td align="right">
                <a href="?ctrl=Profile&PlayerId={$Meeting.m_WinnerPlayerId}"{if $Meeting.m_WinnerPlayerId==$Player.p_Id} style="color: green;"{/if}>
                    {$Meeting.WinnerName} ({$PlayersRate[$Meeting.m_WinnerPlayerId]|number_format:0:".":"'"})
                </a>
                {if $Meeting.m_Winner2PlayerId}
                    <br><a href="?ctrl=Profile&PlayerId={$Meeting.m_Winner2PlayerId}"{if $Meeting.m_Winner2PlayerId==$Player.p_Id} style="color: green;"{/if}>{$Meeting.Winner2Name} ({$PlayersRate[$Meeting.m_Winner2PlayerId]|number_format:0:".":"'"})</a>
                {/if}
            </td>
        
            
            <td align="center">{$Meeting.Score}</td>
            
            <td>
                <a href="?ctrl=Profile&p_Id={$Meeting.m_LoserPlayerId}"{if $Meeting.m_LoserPlayerId==$Player.p_Id} style="color: red;"{/if}>
					{$Meeting.LoserName} ({$PlayersRate[$Meeting.m_LoserPlayerId]|number_format:0:".":"'"})
				</a>
                {if $Meeting.m_Loser2PlayerId}
                    <br><a href="?ctrl=Profile&p_Id={$Meeting.m_Loser2PlayerId}"{if $Meeting.m_Loser2PlayerId==$Player.p_Id} style="color: red;"{/if}>{$Meeting.Loser2Name} ({$PlayersRate[$Meeting.m_Loser2PlayerId]|number_format:0:".":"'"})</a>
                {/if}
            </td>
            
            <td>
                {if $Meeting.m_VideoURL}
                    <a href="?ctrl=Index&act=ShowVideo&VideoIds={$Meeting.m_VideoURL}">
            		    <img src="images/video.png" title="Видео встречи">
            		</a>
        		{/if}
        	</td>
          
        	<td>
        	    {if $Meeting.m_Note}
        	        <img src="images/note.gif" title="{$Meeting.m_Note}">
        	    {/if}
        	    {if !$Meeting.m_AffectRating}
        	        <img src="images/stop.gif" title="Не виляет на рейтинг">
        	    {/if}
        	</td>
        </tr> 
    {/foreach}
</tbody></table>