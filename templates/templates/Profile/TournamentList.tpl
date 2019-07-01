{* Smarty *}

{* Плашки со статистикой год *}
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Принял участие в турнирах">
	<img class="VertMidAlign" height="24" src="images/tours.png"> x {$YearStatistics.TourCount}
</span>
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Всего провёл встреч">
	<img class="VertMidAlign" height="24" src="images/vs.png"> х {$YearStatistics.Meetings}
</span>
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Выиграл встреч">
	<img class="VertMidAlign" height="24" src="images/win.png"> х {$YearStatistics.Wins}
</span>

{$TotalWinPercent=$YearStatistics.Wins*100/$YearStatistics.Meetings}
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Выиграно встреч">
	<img class="VertMidAlign" height="24" src="images/percent.png"> = {$TotalWinPercent|round}%
</span>

<br>
<br>

<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято первых мест">
	<img class="VertMidAlign" height="24" src="images/medal1.png"> x {$YearStatistics.FirstPlaces}
</span>
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято вторых мест">
	<img class="VertMidAlign" height="24" src="images/medal2.png"> x {$YearStatistics.SecondPlaces}
</span>
<span style="white-space:nowrap; padding-left:25px; padding-right:25px;" title="Занято третьих мест">
	<img class="VertMidAlign" height="24" src="images/medal3.png"> x {$YearStatistics.ThirdPlaces}
</span>

<br>
<br>

{* Таблица с турнирами *}
<table class="tablebg" cellspacing="1">  <!-- width="550"-->
<tr class="cat"><td align="center">+</td><td>Турнир</td><td colspan="3">Результат</td></tr>
{foreach $Tournaments as $Tournament}
	{* Строка с заголовком турнира*}
	<tr class="row1">
		<td>
			<input type="button" value="+" id="showMeetingButton_{$Tournament.t_Id}" title="Показать встречи" data-t_id="{$Tournament.t_Id}">
		</td>
		<td  style="padding-left:25px; padding-right:25px;">
			<a href="{$Tournament.t_URL}">{$Tournament.t_DateTime|date_format:"%d.%m.%Y"} - {$Tournament.ttype_Name} - {$Tournament.t_Name} - {$Tournament.c_Name}</a>
		</td>
		<td align="center">{$Tournament.gp_Place} из {$Tournament.PlayersNum}</td>
		<td>
			{if $Tournament.VideoAmount>0}<img src="images/video.png" title="В этом турнире есть видеоролики - {$Tournament.VideoAmount} шт.">{/if}
		</td>
		<td>
			<a href="?ctrl=Ratings&act=Calculator&PlayerId={$Player->p_Id}&TourId={$Tournament.t_Id}">
				<img height="16" alt="расчет рейтинга" title="Показать расчет рейтинга" src="images/calc.png">
			</a>
		</td>
	</tr>
	
	{* Строка со встречами турнира*}
	<tr id="detailTR_{$Tournament.t_Id}" class="row1" data-loaded="False" style="display: none;">
		<td id="detailTD_{$Tournament.t_Id}" colspan="5" align="center">
			<img src="images/tt.gif"><br>Идёт загрузка встреч...
		</td>
	</tr>
{/foreach}
</table>

<script>
//  alert('onClick binding...');
//  alert('onClick binding...'+$( "[id^='showMeetingButton']" ).length);
$( "[id^='showMeetingButton']" ).unbind( "click" ); // что бы многократное подключение обработчика всегда срабатывало нормально. необходимо перед этим отключать обработчик
$( "[id^='showMeetingButton']" ).click(function() {
	var t_Id = $(this).data('t_id');
	var detailTR = $('#detailTR_'+t_Id);
	var detailTD = $('#detailTD_'+t_Id);
	detailTR.toggle();
	if ($(this).attr('value')=='+')
		$(this).attr('value', '-');
	else
		$(this).attr('value', '+');
	
  if (detailTR.data('loaded')!='True') {
	detailTD.load('?ctrl=Profile&act=PlayerTourMeetings&PlayerId={$Player->p_Id}&TourId='+t_Id);
	detailTR.data('loaded','True');
  }
});
</script>