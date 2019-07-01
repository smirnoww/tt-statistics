{* Smarty *}

{extends file="AdminLayout.tpl"}

{block name=AdminBody}

<h2>Обсчёт рейтинга "{$Rating.r_Name}"!</h2>

{* Таблица с турнирами *}
<table class="tablebg" cellspacing="1">  <!-- width="550"-->
<tr class="cat"><th>#</th><th>Дата</th><th>Название</th><th>Место</th><th>Обсчитан</th></tr>
{$TourCounter=1}
{foreach $Dates as $date => $dateTours}
    <tr class="cat" align="center">
        <td colspan="4" align="center">
            <b>{$date}</b>
        </td>
        <td>
           <a href="?ctrl=Ratings&act=Calculate&RatingId={$Rating.r_Id}&Date={$date}&TourCount={$TourCount}">Обсчитать день</a>
        </td>
    </tr>
    {foreach $dateTours as $tour}
    	{* Строка с заголовком турнира*}
    	<tr class="row1">
    		<td>
            	{$TourCounter++}
    		</td>
    		<td>
                {$tour.t_DateTime->format('d.m.Y')}
    		</td>
    		<td>
    			<a href="?ctrl=TourProfile&t_Id={$tour.t_Id}">
                    {$tour.t_Name}
                </a>
                {if $tour.t_URL}
        			<a href="{$tour.t_URL}">
    	    			<img height="16" src="images/LinkToWeb.png">
    		    	</a>
    		    {/if}
    		</td>
    		<td>
                {$tour->t_CourtId('Model_Court')->c_Name}
    		</td>
    		<td align="center" title="Играло на рейтинг {$tour->PlayerInMeetingCount} участников.
Обсчитан рейтинг для {$tour->PlayerWithRatingCount} участников">
    		    <p>
                    {if $tour->Calced}
                        <font color="green">обсчитан</font>
                    {else}
                        <font color="red">не обсчитан</font>
                    {/if}
    		    </p>
    		</td>
    	</tr>
    {/foreach}
{/foreach}

</table>

{/block}