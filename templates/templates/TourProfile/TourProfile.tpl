{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
    <table border="0" class="tablebg1" width="100%">
        <tr class="cat">
            <th colspan="2">Турнир</th>
        </tr>
        <tr class="row1">
            <td colspan="2">
               <!-- {$Tour.t_Id}<br> -->
<h1>{$Tour.t_Name}</h1>
Тип турнира: {$Tour->t_TourTypeId('Model_TournamentType')->ttype_Name}<br>
Дата турнира: {$Tour.t_DateTime->format('d.m.Y H:i')}<br>
Место проведения: {$Tour->t_CourtId('Model_Court')->c_Name}<br>
Максимальное кол-во {if $Tour->t_TeamSize==1}игроков
                    {elseif $Tour->t_TeamSize==2}пар
                    {else} команд <small>({$Tour->t_TeamSize} чел.)</small>
                    {/if}:{$Tour->t_TourMaxPlayersCount|replace:'0':'без ограничения'}<br>
Ссылка на обсуждение: 
{if $Tour.t_URL}
	<a href="{$Tour.t_URL}">
		<img height="16" src="images/LinkToWeb.png">
	</a>
{else}
    отсутствует :(
{/if}        

                <div align="right"><a href="?ctrl=Tours&act=EditTour&TourId={$Tour.t_Id}" title="доступно только организатору турнира и админам">редактировать</a></div>
            </td>
        </tr>
        
        <tr class="cat">
            <td width="250" align="center"><b>Заявка</b></td>
            <td align="center"><b>Группы турнира</b></td>
        </tr>
        
        <tr class="row1">
        
            <td valign="top" width="250">
                <div id="CallsForTour">форма заявок загружается ...</div>
    		</td>
    
            <td valign="top">
                <a href="?ctrl=TourGroups&act=AdminList&TourId={$Tour.t_Id}" title="доступно только организатору турнира и админам">Редактировать группы турнира</a>
                <div id="TourGroups">группы турнира загружаются ...</div>
            </td>

        <tr>
    </table>
    
    
    <!-- Запустим jqueryUI -->
    <script>
        $(function() {
        
    		// загрузим форму заявок
            $( "#CallsForTour" ).load("?ctrl=CallsForTour&TourId={$Tour.t_Id}&embedded");
            
    		// загрузим Группы турнира
            $( "#TourGroups" ).load("?ctrl=TourGroups&TourId={$Tour.t_Id}");
            
        });
    </script>

{/block}