{* Smarty *}
<style>
	.expiredRank {
		color:gray;
	}
</style>

<div>

    {* Таблица присвоенных разрядов *}
	<h4>Присвоенные разряды/звания</h4>
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
	<div>
	    <a href="#" id="reportRank">Сообщить о наличии звания/разряда</a>
    </div>

    <br><br>
    
    {* Таблица разрядов, на которые претендуем *}
	<h4>Количество побед над спортсменами, имеющими спортивный разряд, за прошедший год</h4>
	<div>
	    <ul>
	    <li>Рассматриваются только победы над игроками того же пола.</li> 
        <li>Победа над одним и тем же спортсменом засчитывается как одна победа.</li>
        <li>Победы, зафиксированные по техническим причинам, не учитываются.</li>
        </ul>
	</div>
    <div>
    	{IF $VictoriesCount}
    	<table>
    		<thead>
    			<tr class="cat">
    				<td>Квалификация побеждённого соперника</td>
    				<td>Количество побед</td>
    				<td>В зачёт побед</td>
    			</tr>
    		</thead>
    		<tbody>
    		{foreach $VictoriesCount as $vc}
    			<tr align="center">
    				<td title="{$vc.r_Description}">
    					{$vc.r_Name}
    				</td>
    				<td>
    					{$vc.VictoryCount}
    				</td>
    				<td>
    					{$vc.LoserCount}
    				</td>
    			</tr>
    		{/foreach}
    		</tbody>
    	</table>
    	{ELSE}
        	Отсутствуют...
    	{/IF}
    </div>
	
	<br><br>
	
	<h4>Победы над игроками, имеющими звание/разряд</h4>
	с <input id="EarningRankWinsFrom" class="date" size="10"> по <input id="EarningRankWinsTo" class="date" size="10"> <button id="ShowEarningRankWins">Показать</button>
    <div id="EarningRankWinsDiv" />

    {* Диалог сообщения о разряде *}
    <div id="reportRankDialog" title="Сведения о квалификации">
        <form enctype="multipart/form-data" action="?ctrl=Profile&act=reportRank" method="post">
            <input type="hidden" name="p_Id" value="{$p_Id}">

            <label for="rank">Квалификация</label>
            <br>
            <input type="text" name="rank" id="rank">
            <br>   
            <label for="from">Присвоена</label>
            <br>
            <input name="from" id="from" class="date" size="10">
            <br>   
            <label for="to">До</label>
            <br>
            <input name="to" id="to" class="date" size="10">
            <br>   
            <label for="note">Примечание</label>
            <br>
            <input name="note" id="note" size="40">
            <br>   
            <label for="file">Подтверждение <small>(.jpg .jpeg .png .pdf файлы размером не более 10 Mb.)</small></label>
            <br>
            <input type="file" name="file" id="file">

            <hr>   

            <b>Контакты сообщившего лица для уточнений</b> <br>(не распространяются):
            <br>   
            <label for="senderName">ФИО</label>
            <br>   
            <input name="senderName" size="40">
            <br>   
            <label for="senderTel">Телефон</label>
            <br>   
            <input name="senderTel" size="15">
            <br>   
            <label for="senderEMail">E-mail</label>
            <br>   
            <input name="senderEMail" size="40">
        </form>
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
