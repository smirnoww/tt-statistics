{* Smarty *}

{extends file="Tours/AdminTourLayout.tpl"}

{block name=AdminTourBody}


	<table>
		<tr>
			<td align="right">t_Id:</td><td></td>
		</tr>
		<tr>
			<td align="right">tt_Name:</td><td>{$Tour->t_TourTypeId('Model_TournamentType')->ttype_Name}</td>
		</tr>
		<tr>
			<td align="right">t_DateTime:</td><td>{$Tour.t_DateTime->format('d.m.Y H:i')}</td>
		</tr>
		<tr>
			<td align="right">t_Info:</td><td>{$Tour.t_Name}</td>
		</tr>
		<tr>
			<td align="right">g_Name:</td><td>{$Group.g_Id} - {$Group.g_Name}</td>
		</tr>
	</table>
	
    <p><a href="?ctrl=TourProfile&TourId={$Tour->t_Id}">вернуться в профиль турнира</a></p>

	<table>
	    <tr valign="top">
			<td>
                <h3>Заявки на турнир</h3>
			    {include 'TourGroups/CallsForGroupConsist.tpl'}
			</td>
			<td>
                <h3>Состав группы {$Group->g_Name}{include 'TourGroups/GroupIcon.tpl' GroupColor={$Group.g_Color}}</h3>
                <input type="file" id="TTWRXMLfile" name="file" />
                <button id="loadTTWRXML">Загрузить файл</button>

            	<form action="?ctrl=TourGroups&act=SaveGroupConsist" method="POST" enctype="multipart/form-data">
                    <input	id="TourId" name="TourId" type="hidden" value="{$Tour.t_Id}">
                    <table border="1" id="GroupConsist">
            			<tr class="cat">
            				<td>gp_Act</td>
            				<td>gp_Id</td>
            				<td>p_Name</td>
            				<td>gp_Place</td>
            				<td>gp_Note</td>
            				<td>Изменение<br>рейтинга</td>
            				<td>Act</td>
            			</tr>
            			<tr>
            				<td colspan="7" align="right">
            					<input id="addPlayerToGroup" name="addPlayerToGroup" type="button" value="Добавить игрока (Ins)" title="Добавить игрока в группу"> 
            				</td>
            			<tr>
            		</table>
            		<input id="submit" name="submit" type="submit" value="Сохранить состав группы">
                </form>
			</td>
	    </tr>
	</table>
		
		



	<script>
	    var NewGp_Id = -1;
        $(function() {
            // Зададим горячую клавишу Insert для добавления встречи
            $(document).keyup(function(event){
                if ( event.which == 45 ) {
                    event.preventDefault();
                    $('#addPlayerToGroup').click();
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
        });

		function XMLloaded(evt) {
			// Obtain the read file data    
			var XMLString = evt.target.result;
			var xml = $.parseXML(XMLString);
			var Players=[];
			NewPlayers = $(xml).find('NewPlayers')[0];
			$(NewPlayers).find('Player').each(function( index, playerEl ) {
																Players[$(playerEl).attr('id')]=$(playerEl).find('name')[0].textContent;
															});
															

			PlayersEl = $(xml).find('Tournament > Players > Player');
			$(PlayersEl).each(function( index, playerEl ) {
    							var newPlayer = { gp_Id:NewGp_Id, gp_GroupId: {$Group->g_Id}, row_Act:"new", "new":"_New" };
    							newPlayer.gp_PlayerId = $(playerEl).attr('id');
    							newPlayer.p_Name = Players[newPlayer.gp_PlayerId];
    							newPlayer.gp_Place = $(playerEl).attr('place');
    							addNewRow(newPlayer);
							});
															
		}   //  XMLloaded(evt)

	{literal}

    	function PlayerSelected( event, ui ) {
            // set p_Id after select
            var gp_id = $(this).attr('gp_id');
            $('#gp_PlayerId_'+gp_id).val(ui.item.p_Id);
    
            EditGroupPlayer(event);
        }
    
        
    	function DeleteGroupPlayer(event) {
    		gp_id = event.target.getAttribute('gp_id');
    
    		if ($("#gp_Act_"+gp_id).val()=='new') {
    			$("#GroupPlayerTR_"+gp_id).remove();
    		}
    		else {
    			if ($("#gp_Act_"+gp_id).val()!='del') {
    				$("#gp_Act_"+gp_id).val('del');
    				$("#GroupPlayerTR_"+gp_id).css('background-color','#EE0000');
    			}
    			else {
    				$("#gp_Act_"+gp_id).val('edit');
    				$("#GroupPlayerTR_"+gp_id).css('background-color','#0000EE');
    			}
    		}
    		
    	}
    
    
    	function EditGroupPlayer(event) {
    		gp_id = event.target.getAttribute('gp_id'); 
    		if ($("#gp_Act_"+gp_id).val()!='new') {
    			$("#gp_Act_"+gp_id).val('edit');
    			$("#GroupPlayerTR_"+gp_id).css('background-color','#0000EE');
    		}
    	}					
    	{/literal}
    
        // добавляет строку в таблицу, подставляя в шаблон данные
        function addRow(RowObject) {
            
            rowTemplate = '{strip}
        	<tr id="GroupPlayerTR_#gp_Id#">
        		<td><input	id="gp_Act_#gp_Id#" 	    gp_id="#gp_Id#" 	name=gp_Act[#gp_Id#]			    	type="text"		value="#row_Act#"		size="4"	readonly></td>
        		<td><input	id="gp_Id_#gp_Id#" 		    gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_Id#new#]		type="text"		value="#gp_Id#"			size="4"	readonly>
        		<input	id="gp_GroupId_#gp_Id#" 		gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_GroupId]		type="hidden"	value="#gp_GroupId#"	size="5"	readonly></td>
        		
        		<td>
        			<input	id="gp_PlayerId_#gp_Id#" 	gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_PlayerId]		type="text"		value="#gp_PlayerId#"   size="5"	readonly>
        			<input	id="gp_PlayerName_#gp_Id#"  gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][p_Name]			type="text"		value="#p_Name#"        size="24"	>
        		</td>
        		<td><input	id="gp_Place_#gp_Id#" 		gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_Place]	    	type="text"		value="#gp_Place#"		size="4"	></td>
        		<td><input	id="gp_Note_#gp_Id#" 		gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_Note]			type="text"		value="#gp_Note#"	    size="48"	></td>
        		<td><input	id="gp_RatingDelta_#gp_Id#" gp_id="#gp_Id#" 	name=gp_Data[#gp_Id#][gp_RatingDelta]	type="text"		value="#gp_RatingDelta#"size="6"	></td>
        		
        		<td>
        			<input id="gp_Delete_#gp_Id#" 		gp_id="#gp_Id#" 	name="gp_Delete#gp_Id#" 				type="button"	value="Х" title="Удалить"> 
        		</td>
        	</tr>{/strip}';
        	
        	// Заменим в шаблоне данными из переданного объекта
        	for (var key in RowObject) 
        	    if (RowObject[key]!==null)
                    rowTemplate = rowTemplate.replace(new RegExp("#"+key+"#",'g'), RowObject[key]);
        	
        	// Оставшиеся шаблоны заменим пустой строкой
            rowTemplate = rowTemplate.replace(/#\w*#/g, '');
        	
    		$('#GroupConsist tr').eq(-2).before(rowTemplate);
        }
    
        //Добавляет строку, задаёт для неё обработчики и оформляет её
        function addNewRow(RowObject) {
            addRow(RowObject);
            
    		$("#gp_Delete_"+NewGp_Id).bind('click', DeleteGroupPlayer);
    		$("#GroupPlayerTR_"+NewGp_Id).children().bind('change',EditGroupPlayer);
        	$("#gp_PlayerName_"+NewGp_Id).autocomplete({
        											source: "?ctrl=Players&act=FilteredPlayers",
        											minLength: 3,
        											select: PlayerSelected
        										});
    
    		$("#GroupPlayerTR_"+NewGp_Id).css('background-color','#00EE00');
    		$("#gp_PlayerName_"+NewGp_Id).focus();
            
    		NewGp_Id -= 1;
        }
        
    	function addPlayerToGroup(event) {
            addNewRow({ gp_Id:NewGp_Id, gp_GroupId:{$Group->g_Id}, row_Act:"new", "new":"_New" });
    	}
    
        //Добавим все строки в таблицу
    	{* цикл по участникам *}
    	{foreach $GroupPlayers as $GroupPlayer}
    	    addRow({$GroupPlayer|json_encode});
    	{/foreach}
        
    	$('#addPlayerToGroup').bind('click', addPlayerToGroup);
    	$("input[name*='gp_Delete']").bind('click', DeleteGroupPlayer);
    	$("input[gp_Id]").bind('change', EditGroupPlayer);
    	
    	{literal}
    	$("input[name*='p_Name']").autocomplete({
    											source: "?ctrl=Players&act=FilteredPlayers",
    											minLength: 3,
    											select: PlayerSelected
    										});
    	{/literal}
	</script>



{/block}

