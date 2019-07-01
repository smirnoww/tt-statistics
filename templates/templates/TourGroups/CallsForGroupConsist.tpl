{* Smarty *}

{block name=body}

{$TableId="CallList_"}
{$TableId=$TableId|cat:$Tour->t_Id}

{$TableBodyId="CallListBody_"}
{$TableBodyId=$TableBodyId|cat:$Tour->t_Id}


{*----------------------------------------------------------*}
{*                  Таблица с заявками                      *}

	<table border="1" id="{$TableId}">
	    <thead>
            <tr class="cat" align="center"> 
    			<td>№ пп</td>
    			<td>Игрок</td>
    			<td>Рейтинг</td>
    			<td>Группа</td>
    		</tr>
	    </thead>
        <tbody id="{$TableBodyId}"> 		
        </tbody>
	</table>





{*==========================================================*}
{*                          javascript                      *}

<script language="javascript">


//----------------------------------------------------------//
//                  Функция загрузки заявок                 //

	{literal}

		function LoadCalls(TourId, rowTemplate) {
//alert("LoadCalls("+TourId+", ...)");
            // Очистим старые данные
	{/literal}
			$('#{$TableBodyId} tr').remove();
			
			// загрузка JSON данных
			var jsonCallsURL = '?ctrl=CallsForTour&act=GetTourCalls&TourId='+TourId;
//prompt(jsonCallsURL, jsonCallsURL);

            $.getJSON(jsonCallsURL, 
                        function(json){ 
            				var MyCallExists = false;
            				// заполняем table данными из JSON объекта
                            $.each( json, function( key, val ) {
//alert( key + ": " + val );
                                commentHTML = val.cft_Comment>'' ? '<img src="images/speechBalloon.png" title="'+val.cft_Comment+'">':'';
                                GroupsHTML ='';
                                if (val.GroupId)
                                    for (var key in val.GroupId)
                                        GroupsHTML += '<img src="?ctrl=TourGroups&act=GetGroupIcon&GroupId='+val.GroupId[key]+'">';
                                    
                                
                                // Если ещё не включён в группу, то выведем кнопку для добавления в редактируемую группу
                                if (GroupsHTML=='')
                                    GroupsHTML = '<input type="button" value=">>" PlayerId="'+val.cft_PlayerId+'" PlayerName="'+val.PlayerName+'" title="Добавить в группу -> {$Group->g_Name}">';
                                
                                
            					newRow = rowTemplate.replace(
            												new RegExp('#RowNum#', 'g'),		    	key+1
            										).replace(  
            												new RegExp('#cft_Id#', 'g'),		    	val.cft_Id
            										).replace(  
            												new RegExp('#cft_CallDateTime#', 'g'),  	val.cft_CallDateTime
            										).replace(
            												new RegExp('#cft_PlayerId#', 'g'),	    	val.cft_PlayerId
            										).replace(
            												new RegExp('#PlayerName#', 'g'),	    	val.PlayerName
            										).replace(
            												new RegExp('#cft_PlayerRating#', 'g'),	    Math.round(val.cft_PlayerRating)
            										).replace(
            												new RegExp('#cft_Comment#', 'g'),	    	commentHTML
            										).replace(
            												new RegExp('#cft_AssistToPlayerId#', 'g'),	val.cft_AssistToPlayerId
            										).replace(
            												new RegExp('#Groups#', 'g'),	            GroupsHTML
            										);
            										
            					$('#{$TableBodyId}:last').append(newRow);
            					
            					if (val.cft_PlayerId == {$Auth->AuthPlayerId})
            						MyCallExists = true;
                            
                              });

                            // обработчик кнопки добавления в группу
                            $('input[value=">>"]').click(function(eventData){
                                                            addNewRow({
                                                                    gp_Id:NewGp_Id, 
                                                                    gp_GroupId:{$Group->g_Id}, 
                                                                    gp_PlayerId:$(eventData.target).attr('PlayerId'), 
                                                                    p_Name:$(eventData.target).attr('PlayerName'), 
                                                                    row_Act:'new', 
                                                                    "new":"_New" 
                                                            })
                                                            $(eventData.target).val('~');
                                                            $(eventData.target).unbind( "click" );
                                                            $(eventData.target).click(function(){ alert('Второй раз воспользоваться кнопкой нельзя, что бы один игрок не попал в группу дважды. Если вы удалили его из группы и хотите вернуть, воспользуйтесь стандартным добавлением игрока'); })
                                                        });

            				// Если я есть в заявке, то покажем кнопку отмены заявки, иначе кнопку подачи заявки
        					$('#addMe').hide();
        					$('#deleteMe').hide();
        					if (MyCallExists) {
        						$('#deleteMe').show();
        					} 
        					else {
        						$('#addMe').show();
        					}


		            	}).fail(function( jqxhr, textStatus, error ) {
                            var err = textStatus + ", " + error;
                            console.log( "Request Failed: " + err );
                        });	
		} // function LoadCalls

//                  Функция загрузки заявок                 //
//----------------------------------------------------------//


//----------------------------------------------------------//
//               Шаблон строки в таблице заявок             //
		var RowTemplate = '<tr  id="CallTR_#cft_id#">' +
													'	<td align="center" title="Заявка подана #cft_CallDateTime#">#RowNum#</td>'+
													'	<td><a href="?ctrl=Profile&PlayerId=#cft_PlayerId#">#PlayerName##cft_Comment#</a></td>'+
													'	<td align="right">#cft_PlayerRating#</td>'+
													'	<td align="center">#Groups#</td>'+
													'</tr>'
		


{*----------------------------------------------------------*}
{*            Инициализация после загрузки страницы         *}

$(function() {

	// Первоначальная загрузка актуальных заявок
	LoadCalls({$Tour->t_Id}, RowTemplate);

});

</script>

{/block}
