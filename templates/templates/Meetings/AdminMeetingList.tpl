{* Smarty *}

{extends file="Tours/AdminTourLayout.tpl"}

{block name=head}
    {if $Tour.t_TourTypeId==1}
        <style>
            TD.assistant {
                display: none;
            }
        </style>
    {else}
        <style>
            TD.assistant {
                display: block;
            }
        </style>
    {/if}
{/block}

{block name=AdminTourBody}

		
	<table>
		<tr>
			<td align="right">t_Id:</td><td><input	id="TourId" 	name="TourId"		type="text"		value="{$Tour.t_Id}"	size="5" readonly></td>
		</tr>
		<tr>
			<td align="right">t_Info:</td><td>{$Tour.t_Name}</td>
		</tr>
		<tr>
			<td align="right">g_Id:</td><td><input	id="GroupId" 	name="GroupId"		type="text"		value="{$Group.g_Id}"	size="5" readonly></td>
		</tr>
		<tr>
			<td align="right">g_Name:</td><td>{$Group.g_Name}{include 'TourGroups/GroupIcon.tpl' GroupColor={$Group.g_Color}}</td>
		</tr>
	</table>
    
    <p><a href="?ctrl=TourProfile&TourId={$Tour.t_Id}">вернуться в профиль турнира</a></p>
    
 	<h3>Редактирование встреч</h3>
	
	<input type="file" id="TTWRXMLfile" name="file" />
	<button id="loadTTWRXML">Загрузить файл</button>
	
	<input	id="TourId" name="TourId" type="hidden" value="{$Tour.t_Id}">
	
	<h4>Список встреч</h4>

	<table border="1" id="MeetingsList">
	    <thead>
    		<tr class="cat">
    			<td>Act</td>
    			<td>Id</td>
    			<td>Победитель</td>
    			<td class="assistant">Ассистент</td>
    			<td colspan="2">Счёт</td>
    			<td>Проигравший</td>
    			<td class="assistant">Ассистент</td>
    			<td>Влияет<br>на рейтинг</td>
    			<td>Примечание</td>
    			<td>Youtube id</td>
    			<td>Act</td>
    		</tr>
    	</thead>
		
        <tbody id="MeetingsListTBody">
    		{foreach $Meetings as $Meeting}
    			{include "Meetings/AdminMeetingTR.tpl" Meeting=$Meeting}
    		{/foreach}
        </tbody>

        <tfoot>
    		<tr>
    			<td colspan="5">
    			    Встреч: <span id="MeetingCounter"></span> 
    			</td>
    			<td colspan="7" align="right">
    				<input id="addMeeting" name="addMeeting" type="button" value="Добавить встречу (Ins)" title="Добавить встречу"> 
    			</td>
    		</tr>
    	</tfoot>
	</table>
	
    <input id="SaveAll" type="button" value="Сохранить всё">


	<script type="text/javascript">
		var NewM_Id = 0; // Перед добавлением каждой строки будет вычитаться 1
		var autocompleteObj = {
							source: "?ctrl=Players&act=FilteredPlayers&GroupId={$Group.g_Id}",
							minLength: 3,
							select: PlayerSelected
						};

        $(function() {
            // Зададим горячую клавишу Insert для добавления встречи
			
            $(document).keyup(function(event){
                if ( event.which == 45 ) {
                    event.preventDefault();
                    $('#addMeeting').click();
                }
            });
			
			
			// обработчик кнопки Загрузить...
			$('#loadTTWRXML').click( function() {
				var files = document.getElementById('TTWRXMLfile').files;
				// alert(files.length);
				if (!files.length) {
				  alert('Please select a file!');
				  return;			
				}
				
				var file = files[0];
				var reader = new FileReader();
				reader.onload = XMLloaded;
				reader.readAsText(file);
			});
			
    		// Назначим обработчики общих кнопок
    		$('#addMeeting').bind('click', AddMeeting);
        	$('#SaveAll').bind('click', SaveAll);

        	CountMeetings();
        }); // ready()
	    

		function XMLloaded(evt) {
			// Obtain the read file data    
			var XMLString = evt.target.result;
			var xml = $.parseXML(XMLString);
			var Players=[];
			NewPlayers = $(xml).find('NewPlayers')[0];
			$(NewPlayers).find('Player').each(function( index, playerEl ) {
													Players[$(playerEl).attr('id')]=$(playerEl).find('name')[0].textContent;
											    }
											);
															
			$(xml).find('Game').each(function( index, gameEl ) {
    									// alert(gameEl.textContent);
                            			NewM_Id -= 1;
                            			
    									var newM = { m_Id:NewM_Id, m_TourId:{$Tour.t_Id}, m_GroupId:{$Group.g_Id}, m_AffectRating:1, row_Act:"new", "new":"_New" };
    									$(gameEl).find('Player').each(function( index, playerEl ) {
    																		// alert(playerEl.textContent);
    																		var bWin = $(playerEl).find('bWin')[0].textContent;
    																		if (bWin=='true') { 
    																			newM.m_WinnerPlayerId = $(playerEl).attr('id');
    																			newM.WinnerName = Players[newM.m_WinnerPlayerId];
    																		}
    																			
    																		if (bWin=='false')
    																			newM.m_LoserPlayerId = $(playerEl).attr('id');
    																			newM.LoserName = Players[newM.m_LoserPlayerId];
    																	});
    																	
    									setWL = $(gameEl).find('setWL')[0].textContent;
    									scores = setWL.split(':');
    									scores.sort();
    									newM.m_LoserScore = scores[0];
    									newM.m_WinnerScore = scores[1];
    
    									typeRes = $(gameEl).find('typeRes')[0].textContent;
    									if (typeRes != 0) 
    										newM.m_AffectRating = 0;
    										
                            			var m_Data = { 'm_Data':newM };
                            			
                            			$.post(
                            				'?ctrl=Meetings&act=MeetingTR&MeetingId='+NewM_Id,
                            				m_Data,
                            				function(data) {
                            					$('#MeetingsListTBody').append(data);    	                
                            					CountMeetings();
                            					$("#WinnerName_"+NewM_Id).focus();		
                            				}
                            			);
								    }
								);
			//alert('XMLloaded: '+fileString);
			
		}   //  XMLloaded(evt)
		

		// обработка кнопки "Добавить встречу"
		function AddMeeting(event) {
			NewM_Id -= 1;
			
			$.get(
				'?ctrl=Meetings&act=MeetingTR&MeetingId='+NewM_Id,
				function(data) {
					$('#MeetingsListTBody').append(data);    	                
					CountMeetings();
					$("#WinnerName_"+NewM_Id).focus();		
				}
			);
		}

		
		function EditMeeting(event) {
			m_id = event.target.getAttribute('m_id'); 
			if ($("#m_Act_"+m_id).val()!='new') {
				$("#m_Act_"+m_id).val('edit');
    			$("#MeetingTR_"+m_id).removeClass();
    			$("#MeetingTR_"+m_id).addClass('editActTR');
			}
		}					

		
        // Сохранение одной записи
        function SaveMeeting(event) {
			var m_id = event.target.getAttribute('m_id');
			var postData = $("#MeetingTR_"+m_id+" input").serialize();
			$.post(
			     "?ctrl=Meetings&act=SaveMeeting&TourId={$Tour.t_Id}&GroupId={$Group.g_Id}",
			     postData,
			     function(data, textStatus, jqXHR) { 
			        $("#MeetingTR_"+m_id).replaceWith(data);
			     }
			).fail(function(msg) {
                alert( msg.responseText );
            });
		}


		function DeleteMeeting(event) {
			if (!confirm('Точно удалить навсегда?'))
				return;
				
			m_id = event.target.getAttribute('m_id');
			$("#MeetingTR_"+m_id).removeClass();
    		$("#MeetingTR_"+m_id).addClass('delActTR');

			if (m_id>0)
				$.get(
					 "?ctrl=Meetings&act=DeleteMeeting&MeetingId="+m_id,
					 function(data) { 
						if (data=='Ok')
							$("#MeetingTR_"+m_id).remove();
						else
							alert(data);

                        CountMeetings();
					 }
				);
			else {
				$("#MeetingTR_"+m_id).remove();
                CountMeetings();
            }
				
			
		}
		
        // Сохранение всех изменений
    	function SaveAll(event) {
            $('[name="m_Act"]').each(
                function( index, element ) {
                    if ($(element).val()=='edit' || $(element).val()=='new') {
                        var m_id = $(element).attr('m_id');
						$('#m_Save_'+m_id).click();
            		}
                    
                }
            );
    	}     
			
		// обработка выбора игрока
		function PlayerSelected( event, ui ) {

			targetObj = $(event.target);
			targetObjname = targetObj.attr('name');
			var m_id = targetObj.attr('m_id');
			var p_id = ui.item.p_Id;
			
			if (targetObjname.indexOf('WinnerName')>=0) 
				$("#m_WinnerPlayerId_"+m_id).attr('value',p_id);

			else if (targetObjname.indexOf('Winner2Name')>=0)
				$("#m_Winner2PlayerId_"+m_id).attr('value',p_id);
				
			else if (targetObjname.indexOf('LoserName')>=0)
				$("#m_LoserPlayerId_"+m_id).attr('value',p_id);

			else if (targetObjname.indexOf('Loser2Name')>=0)
				$("#m_Loser2PlayerId_"+m_id).attr('value',p_id);

			else
				alert('Ошибка обработки выбора игрока. Обратитесь к разработчику.');

			EditMeeting(event);
		}

		// удаление ассистента игрока
		function PartnerPlayerNameChange(event) {
		
			targetObj = $(event.target);
			
			// если что-то введено, то выходим - не будем очищать ид партнёра
			if (targetObj.val().trim() > '') 
				return;
				
			targetObjname = targetObj.attr('name');
			var m_id = targetObj.attr('m_id');

			if (targetObjname.indexOf('WinnerName')>=0)
				$("#m_WinnerPlayerId_"+m_id).val(null);
				
			else if (targetObjname.indexOf('Winner2Name')>=0)
				$("#m_Winner2PlayerId_"+m_id).val(null);
				
			else if (targetObjname.indexOf('LoserName')>=0)
				$("#m_LoserPlayerId_"+m_id).val(null);

			else if (targetObjname.indexOf('Loser2Name')>=0)
				$("#m_Loser2PlayerId_"+m_id).val(null);
			
			else
				alert('Ошибка обработки выбора игрока. Обратитесь к разработчику.');
				
		}

        // Настраивает обработчики события для элементов строки. Вызывается в AdminMeetingTR.tpl
        function AdjustMeetingTR(m_Id) {
    		$("#m_Delete_"+m_Id).bind('click', DeleteMeeting);
    		$("#m_Save_"+m_Id).bind('click', SaveMeeting); 
    		$("#MeetingTR_"+m_Id).children().bind('change',EditMeeting);
    		
        	$("#WinnerName_"+m_Id).autocomplete(autocompleteObj); // autocompleteObj опрделелён в AdminMeetingList.tpl
        	$("#Winner2Name_"+m_Id).autocomplete(autocompleteObj);
        	$("#LoserName_"+m_Id).autocomplete(autocompleteObj);
        	$("#Loser2Name_"+m_Id).autocomplete(autocompleteObj);
    
        	$("#Winner2Name_"+m_Id).change(PartnerPlayerNameChange);
        	$("#Loser2Name_"+m_Id).change(PartnerPlayerNameChange);
        }
        
        
        // Посчитаем и выведем количество встреч в таблице
        function CountMeetings() {
            $("#MeetingCounter").html($("#MeetingsListTBody tr").length);
        }

	</script>

{/block}

