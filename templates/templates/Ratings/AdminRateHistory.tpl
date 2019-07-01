{* Smarty *}

{extends file="AdminLayout.tpl"}

{block name=AdminBody}

    <h2>{$Player.p_Name}</h2>
    <table border="1" id="RatingList">
	    <thead>
            <tr align="center">  <!-- заголовок -->
                <th>Act</th>
                <th>Id</th>
                <th>Дата</th>
                <th>Рейтинг</th>
                <th>Примечание</th>
                <th>Действия</th>
            </tr>
    	</thead>
		
        <tbody id="RatingListTBody">
        	{foreach $RateHistory as $Rate}
        	    {include "Ratings/AdminRateTR.tpl" Rate=$Rate}
        	{/foreach}
        </tbody>

        <tfoot>
    		<tr>
    			<td colspan="6" align="right">
    				<input id="addRating" name="addRating" type="button" value="Добавить значение (Ins)" title="Добавить рейтинг"> 
    			</td>
    		</tr>
    	</tfoot>
    </table>
    <input id="SaveAll" type="button" value="Сохранить всё">

	<script type="text/javascript">
		var NewPR_Id = 0; // Перед добавлением каждой строки будет вычитаться 1


        $(function() {
            // Зададим горячую клавишу Insert для добавления рейтинга
			
            $(document).keyup(function(event){
                if ( event.which == 45 ) {
                    event.preventDefault();
                    $('#addRating').click();
                }
            });
        }); // ready()
        
        
        // обработка кнопки "Добавить рейтинг"
    	function AddRate(event) {
    		NewPR_Id -= 1;

    	    $.get(
    	            '?ctrl=PlayerRateHistories&act=RateTR&PlayerRateId='+NewPR_Id,
    	            function(data) {
    					$('#RatingListTBody').append(data);    	                
						$("#pr_Date_"+NewPR_Id).focus();		
					}
    	    );
    	}     
    	
    	
    	function EditRate(event) {
    		pr_id = event.target.getAttribute('pr_id'); 
    		if ($("#pr_Act_"+pr_id).val()!='new') {
    			$("#pr_Act_"+pr_id).val('edit');
    			
    			$("#RatingTR_"+pr_id).removeClass();
    			$("#RatingTR_"+pr_id).addClass('editActTR');
    		}
    	}
    	
    	
        function DeleteRate(event) {
			if (!confirm('Точно удалить навсегда?'))
				return;
				
			pr_id = event.target.getAttribute('pr_id');
			$("#RatingTR_"+pr_id).removeClass();
    		$("#RatingTR_"+pr_id).addClass('delActTR');

			if (pr_id>0)
				$.get(
					 "?ctrl=PlayerRateHistories&act=DeleteRate&PlayerRateId="+pr_id,
					 function(data) { 
						if (data=='Ok')
							$("#RatingTR_"+pr_id).remove();
						else
							alert(data);
					 }
				);
			else
				$("#RatingTR_"+pr_id).remove();
		}


        // Сохранение одной записи
        function SaveRate(event) {
			var pr_id = event.target.getAttribute('pr_id');
			var postData = $("#RatingTR_"+pr_id+" input").serialize();
			$.post(
			     "?ctrl=PlayerRateHistories&act=SaveRate&PlayerId={$Player.p_Id}&RatingId={$RatingId}",
			     postData,
			     function(data, textStatus, jqXHR) { 
			        $("#RatingTR_"+pr_id).replaceWith(data);
			     }
			).fail(function(msg) {
                alert( msg.responseText );
            });
		}
					
        // Сохранение всех изменений
    	function SaveAll(event) {
            $('[name="pr_Act"]').each(
                function( index, element ) {
                    if ($(element).val()=='edit' || $(element).val()=='new') {
                        var pr_id = $(element).attr('pr_id')
						$('#pr_Save'+pr_id).click();
            		}
                    
                }
            );
    	}     

        // Настраивает обработчики события для элементов строки. Вызывается в AdminRateTR.tpl
        function AdjustRateTR(pr_Id) {
            $( "#pr_Date_"+pr_Id ).datepicker( { "firstDay": 1, dateFormat:'dd.mm.yy' });
    
    		$("#pr_Delete"+pr_Id).bind('click', DeleteRate);
    		$("#pr_Save"+pr_Id).bind('click', SaveRate); 
    		$("#RatingTR_"+pr_Id).children().bind('change', EditRate);
        }

    	$('#addRating').bind('click', AddRate);
    	$('#SaveAll').bind('click', SaveAll);
	</script>
{/block}
