{* Smarty *}

{extends file="Layout.tpl"}

{block name=head}

<script type="text/javascript">
    $(function() {
        $('#formulaId').on('change', function() {
                                                // Get the new selected value
                                                var selectedFormulaId = $(this).val();
                                                if (selectedFormulaId==3) 
                                                    $('.scoreDependenced').show();
                                                else
                                                    $('.scoreDependenced').hide();
                                           });
        $('#formulaId').change();
    });


    function is_numeric( mixed_var ) {
        return ( mixed_var == '' ) ? false : !isNaN( mixed_var );
    }
    
    
    function warnElem(elem, warn){
        var bgcolor = warn ? '#FF0000' : '#FFFFFF';
    	$(elem.parent()).attr("style", "background-color: "+bgcolor+";"); //attr('bgcolor', bgcolor);
    	var warnHint = (warn===true ? 'Необходимо ввести число' : (typeof warn === "string" ? warn : ''));
    	$(elem).attr('title',warn ? warnHint : '');
    }   //  function warnElem(elem, warn)
    
    
    function checksrc(){
    	var mysrcrate_elem = $('#mysrcrate');
    	
    	var allright = true;
    	
    	if (!$.isNumeric(mysrcrate_elem.val())){
    		allright = false;
    		warnElem(mysrcrate_elem, true);
    	}
    	else
    		warnElem(mysrcrate_elem,false);
    
    	
    	var tourcoef_elem = $('#tourcoef');
    	if (!$.isNumeric(tourcoef_elem.val())){
    		allright = false;
    		warnElem(tourcoef_elem,true);
    	}
    	else
    		warnElem(tourcoef_elem,false);
    	
    	
    	var tblMeetingsTR = $('#meetingstable tbody tr');
    	tblMeetingsTR.each(
    	    function( index, element ) {
        		var opprate_elem = $(element).find('input[name="OppRate"]');
        		
        		if (!$.isNumeric(opprate_elem.val())){
        			allright = false;
        			warnElem(opprate_elem,true);
        		}
        		else
        			warnElem(opprate_elem,false);
        			
        		var deltaScore = Math.abs($(element).find('input.myScore').val()-$(element).find('input.oppScore').val());
                if (deltaScore==0) {
                    allright = false;
                    warnElem($(element).find('input.myScore'),'Счёт не может быть ничейным');
                    warnElem($(element).find('input.oppScore'),'Счёт не может быть ничейным');
                }
                else {
                    warnElem($(element).find('input.myScore'), false);
                    warnElem($(element).find('input.oppScore'), false);
                }
    	    }
    	);

    	return allright;
    	
    }   //  function checksrc()
    
    
    function recalc() {
    
    	if (!checksrc())
    		return;
    
    	var mysrcrate = parseFloat($('#mysrcrate').val()); 
    	var formulaId = parseInt($('#formulaId').val()); 
    
    	var winformula = '(100-(РТВ-РТП))/10';
    	var loseformula = '-(100-(РТВ-РТП))/20';

    	if (formulaId==2) 
    		loseformula = '-(100-(РТВ-РТП))/15';

    	if (formulaId==3) {
    	    winformula = '(100-(РТВ-РТП))*КРС/10';
    		loseformula = '-(100-(РТВ-РТП))*КРС*0.8/10';
    	}

    	var PR = 0.0; 
    	
    	var tourcoef = parseFloat($('#tourcoef').val()); 
    	
    	tblMeetingsTR = $('#meetingstable tbody tr');
    	
    	tblMeetingsTR.each(
    	    function( index, element ) {

        		var opprate = $(element).find('input[name="OppRate"]').val();
        		
        		var MeetingResult = $(element).find('input[name="MeetingResult"]').val();
        		var res = (MeetingResult=="Я выиграл" ? true : false);
        
        		var myPR = 0.0;
        		var oppPR = 0.0;
        		var deltaScore = Math.abs($(element).find('input.myScore').val()-$(element).find('input.oppScore').val());
        		deltaScore = Math.min(deltaScore, 3);   //delta can't be more then 3
        		var krs = $('.scoreDiffCoef[scoreDiff='+deltaScore+']').val();
        		
                $(element).find('td[name="PRCalculation"]').empty();
                
                // I'm winner
        		if (res && (mysrcrate-opprate)<100)
        		{
        			switch (formulaId) {
                        case 1:
                			myPR = (100-(mysrcrate-opprate))/10.0;
            				oppPR = -(100-(mysrcrate-opprate))/20.0;
                        break;
                        case 2:
                			myPR = (100-(mysrcrate-opprate))/10.0;
            				oppPR = -(100-(mysrcrate-opprate))/15.0;
                        break;
                        case 3:
                			myPR = (100-(mysrcrate-opprate))/10.0*krs;
            				oppPR = -(100-(mysrcrate-opprate))*0.8/10.0*krs;
                        break;
                        default:
                            alert('Всё сломалось!');
                        break;
                    }

        			
        			formula = '<b>ПРв</b> = '+winformula + ' = '+winformula.replace('РТВ',mysrcrate).replace('РТП',opprate).replace('КРС',krs) + ' = <b>'+myPR.toFixed(3)+'</b>';			
        			$(element).find('td[name="PRCalculation"]').html(formula);
        		}
                // I'm loser
        		if (!res && opprate-mysrcrate<100){
        			switch (formulaId) {
                        case 1:
                			myPR = -(100.0-(opprate-mysrcrate))/20.0;
            				oppPR = (100.0-(opprate-mysrcrate))/10.0;
                        break;
                        case 2:
                			myPR = -(100.0-(opprate-mysrcrate))/15.0;
            				oppPR = (100.0-(opprate-mysrcrate))/10.0;
                        break;
                        case 3:
                			myPR = -(100.0-(opprate-mysrcrate))*0.8/10.0*krs;
            				oppPR = (100.0-(opprate-mysrcrate))/10.0*krs;
                        break;
                        default:
                            alert('Всё сломалось!');
                        break;
                    }


        			formula = '<b>ПРп</b> = '+loseformula + ' = '+loseformula.replace('РТВ',opprate).replace('РТП',mysrcrate).replace('КРС',krs) + ' = <b>'+myPR.toFixed(3)+'</b>';			
        			$(element).find('td[name="PRCalculation"]').html(formula);
        		}

        		PR += myPR;

        		$(element).find('td[name="MyDelta"]').html((myPR*tourcoef).toFixed(3));
        		$(element).find('td[name="OpponentDelta"]').html((oppPR*tourcoef).toFixed(3));
    	    }
        );    // цикл по встречам	


    	$('#spr').val( PR.toFixed(3) );
    
    	$('#resformula').html('(КЗС*СПР+РТ) = '.replace('КЗС',tourcoef).replace('СПР',PR.toFixed(3)).replace('РТ',mysrcrate));

    	$('#myresultrate').val((mysrcrate+PR*tourcoef).toFixed(3));
    	
    	if (PR>0) 
    		sign = '+';
    	else 
    		sign = '';
    	$('#myresultdelta').html(sign+(PR*tourcoef).toFixed(3));
    }   //  function recalc()
    
    
    function addmeeting(){
        var maxN = 0;
        
        $('#meetingstable tbody tr').each(function() {
          var value = parseFloat($(this).attr('MeetingNumber'));
          maxN = (value > maxN) ? value : maxN;
        });    
        
        maxN += 1;
	    $.get(
	            '?ctrl=Ratings&act=CalcTR&MeetingNumber='+maxN,
	            function(data) {
					$('#meetingstable tbody').append(data);
					$('#formulaId').change();
				}
	    );
    }	
    
    
    function delmeeting(N){
    	$('#meetingstable tbody tr[MeetingNumber='+N+']').remove();
    }
    
    
    function changewinner(N){
        var btn = $('#meetingstable tbody tr[MeetingNumber='+N+'] input[name="MeetingResult"]');
        
        btn.val(btn.val()=='Я выиграл'?'Я проиграл':'Я выиграл');
    }
    
</script>

{/block}


{block name=body}

<div style="margin-left:15px">

<h1>Калькулятор рейтинга</h1>

<table border="0" class="tablebg">
	<tr class="row1">
		<td>	<span style="font-size: 14px;">Формула расчёта</span>		</td>
		<td>	
			<select id="formulaId">
				<option value="1">ФНТР (у проигравшего отнимается в 2 раза меньше)</option>
				<option value="2" {IF $Tour}{IF $Tour->GetFormulaId()==2}selected{/IF}{/IF}>Саратов с 01.08.2013 (у проигравшего отнимается в 1.5 раза меньше)</option>
				<option value="3" {IF $Tour}{IF $Tour->GetFormulaId()==3}selected{/IF}{/IF}>Саратов с 01.01.2026 у проигравшего дельта 0.8 + учёт разницы в счёте (КРС)</option>
			</select>
		</td>
	</tr>
	<tr class="row1 scoreDependenced">
		<td>	<span style="font-size: 14px;">Коэффициэнты для разницы в счёте</span>		</td>
		<td>	
		    <span style="font-size: 14px;">1</span>:<input required class="scoreDiffCoef" scoreDiff="1" type="number" step="0.1" value="{$Rate|default:0.8}" style="width: 50px;">		
		    <span style="font-size: 14px;">2</span>:<input required class="scoreDiffCoef" scoreDiff="2" type="number" step="0.1" value="{$Rate|default:1}" style="width: 50px;">		
		    <span style="font-size: 14px;">3+</span>:<input required class="scoreDiffCoef" scoreDiff="3" type="number" step="0.1" value="{$Rate|default:1.2}" style="width: 50px;">		
		</td>
	</tr>
	<tr class="row1">
		<td>	<span style="font-size: 14px;">Мой исходный рейтинг (РТ)</span>		</td>
		<td>	<input required id="mysrcrate" type="text" value="{$Rate|default:300}">	</td>
	</tr>
	<tr class="row1">
		<td>	<span style="font-size: 14px;">КЗС турнира</span>	</td>
		<td>	<input required id="tourcoef" type="text" value="{$Tour.t_Coefficient|default:0.2}">	</td>
	</tr>
</table> 



<table id="meetingstable" class="tablebg" border="0">
    <thead>
    	<tr class="cat">
    		<th>X</th><th>Расчет моего приращения рейтинга</th><th>Моя дельта</th><th>Исход встречи</th><th>Рейтинг соперника</th><th>Дельта соперника</th>
    	</tr>
    </thead>
    <tbody>
        {* Если встреч не передано, то покажем одну по умолчанию *}
        {if $Meetings|@count == 0}
    	    {$Meeting.N = 1}
    	    {$Meeting.OpponentRate = 300}
    	    {$Meeting.IWon = 1}
    	    {$Meeting.OpponentName = ""}

            {$Meetings[]=$Meeting}
        {else}
            {$CalcAfterShow = true}
        {/if}
        
    	{foreach $Meetings as $Meeting}
            {include "Ratings/CalculatorTR.tpl" Meeting=$Meeting}
    	{/foreach}
    </tbody>
</table>

<p style="font-size: 14px;"><input id="addmeeting" type="button" value="Добавить встречу" onclick="addmeeting();"></p>
<p style="font-size: 14px;">Сумма приращения рейтинга (СПР) = <input id="spr" type="text" readonly></p>
<p style="font-size: 14px;">Мой итоговый рейтинг (КЗС*СПР+РТ) = <span id="resformula"></span><input id="myresultrate" type="text" readonly> Дельта = <span id="myresultdelta"></span></p>

<p style="font-size: 14px;"><input id="recalc" type="button" value="Рассчитать" onclick="recalc();"></p><br>

<p style="font-size: 14px; text-decoration: underline;">Инструкция по использованию калькулятора</p><br>
<p style="font-size: 14px;">1. Выберите формулу</p>
<p style="font-size: 14px;">1. Задайте свой изначальный рейтинг и КЗС</p>
<p style="font-size: 14px;">2. В таблице встреч турнира задайте рейтинг соперника и кнопкой выставляете исход встречи ("Я выиграл"/"Я проиграл"). Для формулы с учётом счёта необходимо указать счёт по партиям.</p>
<p style="font-size: 14px;">3. При необходимости расчитать сразу несколько встреч вы можете добавить встречи по кнопке "Добавить встречу". Для удаления встречи нажмите крестик в соответствующей строке.</p>
<p style="font-size: 14px;">4. Нажмите кнопку "Расчитать", чтобы получить все данные по изменению рейтинга</p>
<br>
<p style="font-size: 14px;"><input type="button" name="back" value="Вернуться" onclick="history.go(-1); "></p>
</div>

{if $CalcAfterShow}
	<script> recalc(); </script>
{/if}		



{/block}
