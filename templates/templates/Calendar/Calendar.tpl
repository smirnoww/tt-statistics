{* Smarty *}

{if empty($CalendarId)}{$CalendarId="StandUpCalendar"}{/if}
{if empty($urlPrefix)}{$urlPrefix="#"}{/if}

<table id="{$CalendarId}" border="1">
  <thead>
    <tr align="center"><td id="backBtn">Л</td>  <td colspan="5"></td>  <td id="forwardBtn">Ы</td> </tr>
    <tr align="center"><td>ѕн<td>¬т<td>—р<td>„т<td>ѕт<td>—б<td>¬с</tr>
  <tbody>
</table>

<script>
	function DrawCalendar(id, year, month, selectedDate) {
		adaptedMonth = month-1;
		var LastDateOfMonth = new Date(year,month,0), 
			Dlast = LastDateOfMonth.getDate(), 
			DNlast = LastDateOfMonth.getDay(),
			DNfirst = new Date(year, adaptedMonth, 1).getDay(),
			calendar = '<tr align="center">',
			monthNames=["январь","‘евраль","ћарт","јпрель","ћай","»юнь","»юль","јвгуст","—ент€брь","ќкт€брь","Ќо€брь","ƒекабрь"];
		//alert(Dlast);
		//return;
		// пропустим дни недели от предыдущего мес€ца
		if (DNfirst != 0) 
			for(var  i = 1; i < DNfirst; i++) calendar += '<td></td>';
		else
			for(var  i = 0; i < 6; i++) calendar += '<td></td>';
		
		
		//цикл по дн€м мес€ца
		for(var  i = 1; i <= Dlast; i++) {
			var tdAttr = '',
				pAttr = '';
		
			var day = new Date(year,adaptedMonth,i).getDay();
			if ((day == 0) || (day == 6))
				tdAttr = 'bgcolor="red"';

			if (i == new Date().getDate() && year == new Date().getFullYear() && adaptedMonth == new Date().getMonth())
				tdAttr = 'bgcolor="lightgreen" title="сегодн€"';
			else if (new Date(year,adaptedMonth,i) - selectedDate == 0) 
				tdAttr = 'bgcolor="lightblue" title="выбранна€ дата"';

			var dd  = i.toString();
			var mm = month.toString();
			dateForURL = year + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
			calendar += '<td '+tdAttr+'><a href="{$urlPrefix}'+dateForURL+'">' + i + '</a></td>';
		  
			// если воскресенье , то создадим новую строку дл€ следующей недели
			if ((day == 0) && (i < Dlast)) {
				calendar += '<tr align="center">';
			}
		  
		}
		
		// если последний день не пришЄлс€ на вс, то добьЄм строку пустыми клетками до конца недели
		if (DNlast != 0)
			for(var  i = DNlast; i < 7; i++) calendar += '<td>&nbsp;';
		
		
		document.querySelector('#'+id+' tbody').innerHTML = calendar;
		document.querySelector('#'+id+' thead td:nth-child(2)').innerHTML = monthNames[adaptedMonth] +' '+ year;
		document.querySelector('#'+id+' thead td:nth-child(2)').setAttribute("month", month);
		document.querySelector('#'+id+' thead td:nth-child(2)').setAttribute("year", year);
	} // DrawCalendar(...)

	
	
	// Ќачальна€ инициализаци€ календар€
	var selectedDate = new Date({$CurrentDate|date_format:"%Y"},{$CurrentDate|date_format:"%m"}-1,{$CurrentDate|date_format:"%d"});
	DrawCalendar("{$CalendarId}", {$CurrentDate|date_format:"%Y"}, {$CurrentDate|date_format:"%m"}, selectedDate);

	
	// переключатель минус мес€ц
	document.getElementById('backBtn').onclick = function() {
		Month = parseInt(document.querySelector('#{$CalendarId} thead td:nth-child(2)').getAttribute("month"));
		Year = parseInt(document.querySelector('#{$CalendarId} thead td:nth-child(2)').getAttribute("year"));
		
		if (Month==1) {
			Month = 12;
			Year--;
		} 
		else Month--;
		
		DrawCalendar("{$CalendarId}", Year, Month, selectedDate);
	}

	// переключатель плюс мес€ц
	document.getElementById('forwardBtn').onclick = function() {
		Month = parseInt(document.querySelector('#{$CalendarId} thead td:nth-child(2)').getAttribute("month"));
		Year = parseInt(document.querySelector('#{$CalendarId} thead td:nth-child(2)').getAttribute("year"));
		
		if (Month==12) {
			Month = 1;
			Year++;
		} 
		else Month++;
		

		DrawCalendar("{$CalendarId}", Year, Month, selectedDate);
	}

</script>

