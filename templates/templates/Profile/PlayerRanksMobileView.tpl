{* Smarty *}
<style>
	.expiredRank {
		color:gray;
	}
</style>

<div>
    {* Таблица присвоенных разрядов *}
	<b><u>Присвоенные разряды/звания</u></b>
	<div>
    	{IF $PlayerRanks}
    	<table>
    		<thead>
    			<tr class="cat">
    				<td>Квалификация</td>
    				<td>Присвоена</td>
    				<td>До</td>
    				<td>Примечание</td>
    			</tr>
    		</thead>
    		<tbody>
    		{foreach $PlayerRanks as $pr}
    			<tr {IF !$pr.Active}class="expiredRank"{/IF}>
    				<td title="{$pr.r_Description}" align="center">
    					{$pr.r_Name}
    				</td>
    				<td>
    					{$pr.pr_DateFrom}
    				</td>
    				<td>
    					{$pr.pr_DateTo|default:"бессрочно"}
    				</td>
    				<td>
    					{$pr.pr_Note}
    				</td>
    			</tr>
    		{/foreach}
    		</tbody>
    	</table>
    	{ELSE}
        	Отсутствуют...
    	{/IF}
    </div>



<script type="text/javascript">
    var reportRankDialog;
    
    $(function(){

		$( ".date" ).datepicker({
			firstDay: 	1,
			dayNamesMin:[ "Вс", "Пн", "Вт", "Ср", "Че", "Пт", "Сб" ],
			dateFormat:	"dd.mm.yy",
			changeYear:	true
		});
		
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){ dd='0'+dd; } 
        if(mm<10){ mm='0'+mm; } 
        var formatedToday = dd+'.'+mm+'.'+yyyy;
        var formatedYearAgo = dd+'.'+mm+'.'+(yyyy-1);

		$( "#EarningRankWinsFrom" ).datepicker( "setDate", formatedYearAgo );
		$( "#EarningRankWinsTo" ).datepicker( "setDate", formatedToday );

		$( "#ShowEarningRankWins" ).click( function() {
		    var from = $.datepicker.formatDate( "yy-mm-dd", $( "#EarningRankWinsFrom" ).datepicker( "getDate" ) );
		    var to = $.datepicker.formatDate( "yy-mm-dd", $( "#EarningRankWinsTo" ).datepicker( "getDate" ) );
    		$('#EarningRankWinsDiv').load('?ctrl=Profile&act=EarningRankWins&PlayerId={$p_Id}&from='+from+'&to='+to);
		} );
		
		$( "#ShowEarningRankWins" ).click();

        // обработка сообщения о разряде
        reportRankDialog = $( "#reportRankDialog" ).dialog({ 
            autoOpen: false ,
            modal: true,
            buttons: {
                "Отправить": function(){
                    reportRankDialog.find( "form" )[0].submit();
                    //reportRankDialog.dialog( "close" );
                },
                "Отменить": function() {
                    reportRankDialog.dialog( "close" );
                }
            },
            close: function() {
                reportRankDialog.find( "form" )[0].reset();
            },
            position: { my: "center", at: "center", of: window }
        });
        $( "#reportRank" ).click(function() {
            reportRankDialog.dialog( "open" );
            // return false;
        });
    });
</script>

</div>
