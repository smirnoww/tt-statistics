{* SMARTY *}
{* Информация о группе турнира в профиль турнира *}

<div id="Group_{$Group.g_Id}">
    <table border="0" class="tablebg1" width="100%">
        <tr class="cat">
            <td width="180">
                Участники ({$GroupPlayers|count})
            </td>
            <td>
                Встречи ({$Meetings|count})
            </td>
        </tr>
        <tr class="row1" valign="top">
            <td width="180">
                <table border="1">
                    <tr class="cat" align="center">
                        <td>Место</td><td>Имя</td>
                    </tr>
                    {foreach $GroupPlayers as $grPlayer}
                        {$Player = $grPlayer->gp_PlayerId('Model_Player')}
                        <tr class="row1">
                            <td align="center" title="{$grPlayer->gp_Note|escape}">
                                {$grPlayer->gp_Place}
                        	    {if $grPlayer.gp_Note}
                        	        <img src="images/note.gif" title="{$grPlayer.gp_Note|escape}">
                        	    {/if}
                            </td>
                            <td>
                                <a href="?ctrl=Profile&PlayerId={$grPlayer->gp_PlayerId}">{$Player->p_Name}</a>
                            </td>
                        </tr>
                    {/foreach}
                </table>
                <div align="right"><a href="?ctrl=TourGroups&act=AdminGroupConsist&TourId={$Group.g_TourId}&GroupId={$Group.g_Id}" title="доступно только организатору турнира и админам">редактировать</a></div>
            </td>
            <td>
                <div id="GroupMeetingsList_{$Group.g_Id}" align="center"><img src="images/tt.gif"><br>Идёт загрузка встреч...</div>
                
                <div align="right"><a href="?ctrl=Meetings&act=AdminMeetingsList&GroupId={$Group.g_Id}" title="доступно только организатору турнира и админам">редактировать</a></div>
            </td>
        </tr>
    </table>
</div>

<script>
	$('#GroupMeetingsList_{$Group.g_Id}').load('?ctrl=TourGroups&act=TourGroupMeetings&TourId={$Group.g_TourId}&GroupId={$Group.g_Id}');
</script>
