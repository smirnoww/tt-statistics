{* Smarty *}

{* Таблица с турнирами *}
<table class="tablebg" cellspacing="1">  <!-- width="550"-->
<tr class="cat">
    <th>#</th>
    <th>Дата</th>
    <th>Название</th>
    <th>Место</th>
    <th>Действия</th>
</tr>
{$TourCounter=1}
{foreach $Tours as $tour}
	{* Строка с заголовком турнира*}
	<tr class="row1">
		<td>
        	{$TourCounter++}
		</td>
		<td>
            {$tour.t_DateTime->format('d.m.Y')}
		</td>
		<td>
            <a href="?ctrl=TourProfile&t_Id={$tour.t_Id}">{$tour.t_Name}</a>
            {if $tour.t_URL}
    			<a href="{$tour.t_URL}" test="test">
	    			<img height="16" src="images/LinkToWeb.png">
		    	</a>
		    {/if}
		</td>
		<td>
            {$tour->t_CourtId('Model_Court')->c_Name}
		</td>
    	<td>                
    	    <a href="?ctrl=Tours&act=EditTour&TourId={$tour.t_Id}&BackURL=%3Fctrl%3DTours%26act%3DAdminList%26YearTabN%3D{$smarty.request.YearTabN}" >редактировать</a>
            {* <a href="?ctrl=Tours&act=Delete&TourId={$tour.t_Id}" onClick="return window.confirm('Уверены, что хотите удалить? Восстановление будет не возможно!')">удалить</a> *}
    	</td>
	</tr>
{/foreach}

</table>
