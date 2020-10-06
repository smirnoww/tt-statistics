{* Smarty *}


{block name=body}

{* если есть история рейтинга, то выведем график*}
{if $RatingHistory|@count>0}
    <script type="text/javascript">
    
{literal}
    /**Parses string formatted as YYYY-MM-DD to a Date object.
     * If the supplied string does not match the format, an 
     * invalid Date (value NaN) is returned.
     * @param {string} dateStringInRange format YYYY-MM-DD, with year in
     * range of 0000-9999, inclusive.
     * @return {Date} Date object representing the string.
     */
    
      function parseISO8601(dateStringInRange) {
        var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
            date = new Date(NaN), month,
            parts = isoExp.exec(dateStringInRange);
    
        if(parts) {
          month = +parts[2];
          date.setFullYear(parts[1], month - 1, parts[3]);
          if(month != date.getMonth() + 1) {
            date.setTime(NaN);
          }
        }
        return date;
      }
      
    Date.prototype.format=function(format){var returnStr="";var replace=Date.replaceChars;for(var i=0;i<format.length;i++){var curChar=format.charAt(i);if(i-1>=0&&format.charAt(i-1)=="\\"){returnStr+=curChar}else if(replace[curChar]){returnStr+=replace[curChar].call(this)}else if(curChar!="\\"){returnStr+=curChar}}return returnStr};Date.replaceChars={shortMonths:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],longMonths:["January","February","March","April","May","June","July","August","September","October","November","December"],shortDays:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],longDays:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],d:function(){return(this.getDate()<10?"0":"")+this.getDate()},D:function(){return Date.replaceChars.shortDays[this.getDay()]},j:function(){return this.getDate()},l:function(){return Date.replaceChars.longDays[this.getDay()]},N:function(){return this.getDay()+1},S:function(){return(this.getDate()%10==1&&this.getDate()!=11?"st":(this.getDate()%10==2&&this.getDate()!=12?"nd":(this.getDate()%10==3&&this.getDate()!=13?"rd":"th")))},w:function(){return this.getDay()},z:function(){var d=new Date(this.getFullYear(),0,1);return Math.ceil((this-d)/86400000)}, W:function(){var d=new Date(this.getFullYear(),0,1);return Math.ceil((((this-d)/86400000)+d.getDay()+1)/7)},F:function(){return Date.replaceChars.longMonths[this.getMonth()]},m:function(){return(this.getMonth()<9?"0":"")+(this.getMonth()+1)},M:function(){return Date.replaceChars.shortMonths[this.getMonth()]},n:function(){return this.getMonth()+1},t:function(){var d=new Date();return new Date(d.getFullYear(),d.getMonth(),0).getDate()},L:function(){var year=this.getFullYear();return(year%400==0||(year%100!=0&&year%4==0))},o:function(){var d=new Date(this.valueOf());d.setDate(d.getDate()-((this.getDay()+6)%7)+3);return d.getFullYear()},Y:function(){return this.getFullYear()},y:function(){return(""+this.getFullYear()).substr(2)},a:function(){return this.getHours()<12?"am":"pm"},A:function(){return this.getHours()<12?"AM":"PM"},B:function(){return Math.floor((((this.getUTCHours()+1)%24)+this.getUTCMinutes()/60+this.getUTCSeconds()/ 3600) * 1000/24)}, g:function(){return this.getHours()%12||12},G:function(){return this.getHours()},h:function(){return((this.getHours()%12||12)<10?"0":"")+(this.getHours()%12||12)},H:function(){return(this.getHours()<10?"0":"")+this.getHours()},i:function(){return(this.getMinutes()<10?"0":"")+this.getMinutes()},s:function(){return(this.getSeconds()<10?"0":"")+this.getSeconds()},u:function(){var m=this.getMilliseconds();return(m<10?"00":(m<100?"0":""))+m},e:function(){return"Not Yet Supported"},I:function(){return"Not Yet Supported"},O:function(){return(-this.getTimezoneOffset()<0?"-":"+")+(Math.abs(this.getTimezoneOffset()/60)<10?"0":"")+(Math.abs(this.getTimezoneOffset()/60))+"00"},P:function(){return(-this.getTimezoneOffset()<0?"-":"+")+(Math.abs(this.getTimezoneOffset()/60)<10?"0":"")+(Math.abs(this.getTimezoneOffset()/60))+":00"},T:function(){var m=this.getMonth();this.setMonth(0);var result=this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/,"$1");this.setMonth(m);return result},Z:function(){return-this.getTimezoneOffset()*60},c:function(){return this.format("Y-m-d\TH:i:sP")},r:function(){return this.toString()},U:function(){return this.getTime()/1000}};        
{/literal}
        
        function DateToMonthCount(d) {
            var date = new Date(d);
            
            var mm = date.getMonth();
        	var yy = date.getFullYear() - 2000;
        	
        	return yy*12+mm;
        }
        function MonthCountToStr(mc) {
            var MonthArray = [ "январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь" ];
        
            var mm = mc % 12;
            var yy = ((mc-mm) / 12) + 2000;
            
            return MonthArray[mm]+" "+yy;
        }
    
        function setPeriodText() {
            $( "#PeriodFrom_text" ).text(MonthCountToStr($( "#ChartPeriod_slider" ).slider( "values", 0 )));
            $( "#PeriodTo_text" ).text(MonthCountToStr($( "#ChartPeriod_slider" ).slider( "values", 1 )));
        }


        var RatingHistoryDT; // DataTable for rating history
        var RatingHistoryView; // Filtered rating history for visualization
        

        //========================================================================================================================
        // ПОДГОТОВКА ДАННЫХ ДЛЯ ГРАФИКА
        function prepareDateForChart() {
  
            // Таблица с историей рейтинга
        	RatingHistoryDT = new google.visualization.DataTable();
        	RatingHistoryDT.addColumn('string', 'Дата');
        	RatingHistoryDT.addColumn('date', 'ДатаДатой');
        	RatingHistoryDT.addColumn('number', '{$RatingHistory[$RatingHistory|@count-1].pr_Rate}');
        	RatingHistoryDT.addColumn({ type:'string', role:'tooltip', 'p':{ 'html': true } });

            var d;
            {foreach $RatingHistory as $Rate}
                {$RateDate = $Rate.pr_Date->format('Y-m-d')}
                // d = new Date(Date.parse('{$RateDate}')); //хороший код, но не работает в ie
                d = parseISO8601('{$RateDate}');
                RatingHistoryDT.addRow([d.format('d.m.Y'), d,  {$Rate.pr_Rate|round:0}, d.format('d.m.Y')+':  {$Rate.pr_Rate|round:0}{if $Rate.pr_Note}\n{$Rate.pr_Note|wordwrap:45:"\\n"}{/if}'] );
            {/foreach}
        	RatingHistoryView = new google.visualization.DataView(RatingHistoryDT);						
        	RatingHistoryView.hideColumns([1]);
        	
        	// выведем график
        	drawChart();

        }
        
        
        //========================================================================================================================
        // ВЫВОД ГРАФИКА
        function drawChart(mc_from, mc_to) {
{literal}
            if (typeof(mc_from)==='undefined') 
                mc_from = $( "#ChartPeriod_slider" ).slider( "values", 0 );
            var mm_from = mc_from % 12;
            var yy_from = ((mc_from-mm_from) / 12) + 2000;
            
            if (typeof(mc_to)==='undefined') 
                mc_to = $( "#ChartPeriod_slider" ).slider( "values", 1 );
            var mm_to = mc_to % 12;
            var yy_to = ((mc_to-mm_to) / 12) + 2000;
            

        	filteredRows = RatingHistoryDT.getFilteredRows([{column: 1, minValue: new Date(yy_from, mm_from, 1), maxValue: new Date(yy_to, mm_to, 31)}]);
        	RatingHistoryView.setRows(filteredRows);
        	var options =	{
        				        tooltip: {isHtml: true},
        				        width: 550, height: 240,
        					    title: 'История изменения рейтинга',
        				        vAxis: {title: 'Рейтинг',  titleTextStyle: {color: 'red'}},
        				        legend: 'none'
        					};
        	
        	var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        	chart.draw(RatingHistoryView, options);            
{/literal}
        }
        
        {* Определим дату начала и окончания истории рейтинга *}
        {$BeginHistory=$RatingHistory[0].pr_Date->format('Y-m-d')}
        {$EndHistory=$RatingHistory[$RatingHistory|@count-1].pr_Date->format('Y-m-d')}

        //var FromMonthCount    = DateToMonthCount(Date.parse('{$BeginHistory}')); //хороший код, но не работает в ie
        //var ToMonthCount  = DateToMonthCount(Date.parse('{$EndHistory}')); //хороший код, но не работает в ie
        var FromMonthCount  = DateToMonthCount(parseISO8601('{$BeginHistory}'));
        var ToMonthCount    = DateToMonthCount(parseISO8601('{$EndHistory}'));
        
        var InitBegtinPeriodMonthCount = Math.max(FromMonthCount,ToMonthCount-11); // сначала покажем график за последний год

        //========================================================================================================================
        // ПОСЛЕ ЗАГРУЗКИ СТРАНИЦЫ    
        $(function() {
            {* ========================================= *}    
            {* Запуск слайдера для выбора интервала *}
            $( "#ChartPeriod_slider" ).slider({
                                                range: true,
                                                min: FromMonthCount,
                                                max: ToMonthCount,
                                                values: [ InitBegtinPeriodMonthCount , ToMonthCount ],
                                                slide: function( event, ui ) { // напишем период текстом при перемещении слайдера
                                                                                $( "#PeriodFrom_text" ).text(MonthCountToStr(ui.values[ 0 ]));
                                                                                $( "#PeriodTo_text" ).text(MonthCountToStr(ui.values[ 1 ]));
                                                                                
                                                                                drawChart(ui.values[0], ui.values[1]);
                                                        }
                                            });
            // Начальная установка периода текстом                                
            setPeriodText();

            {* ========================================= *}    
            {* Запустим график когда загрузится google corechart*}
            google.setOnLoadCallback(prepareDateForChart);
        }); // $(function() {
        // ПОСЛЕ ЗАГРУЗКИ СТРАНИЦЫ    
        //========================================================================================================================
    
    
        
    </script>
    <style>
       P.PeriodText { 
        border:0; 
        color:#f6931f; 
        font-weight:bold; 
      }
    #ChartPeriod_slider div.ui-slider-range.ui-widget-header {
        background:#3366cc;
    }
    #ChartPeriod_slider {
        background:#c2d2f0; border-color:#3366cc;
    }
    </style>
    
    
    {* Текущее значение рейтингка *}
    <table>
        <tr>
            <td>Текущий рейтинг:</td>
            <td>{$RatingHistory[$RatingHistory|@count-1].pr_Rate|round:0}</td>
        </tr>
        <tr>
            <td>Максимальный рейтинг ({$MaxRatingHistoryRow.pr_Date}):</td>
            <td>{$MaxRatingHistoryRow.pr_Rate|round:0}</td>
        </tr>
    </table>

    
    
    {* Контейнер для графика*}
    <div id="ChartContainer" style="width:550">
        <div id="chart_div"><img src="images/tt.gif"></div>

        <p align="center" class="PeriodText">
          <span id="PeriodFrom_text" style="display:inline-block; width:200px; text-align:right"></span> - <span id="PeriodTo_text" style="display:inline-block; width:200px; text-align:left"></span>
        </p>
        <div id="ChartPeriod_slider" style=""></div> 
        
        <br>
        
        {* выведем кнопки для быстрого выбора периода*}
        {$FirstYear = $BeginHistory|truncate:4:""}
        {$LastYear = $EndHistory|truncate:4:""}
        {if $LastYear>$FirstYear} {* если история растянута на несколько лет, то будем показывать кнопки быстрого выбора периода *}
            <p align="right">
                <input type="button" value="Вся история" onclick="$('#ChartPeriod_slider').slider( 'values', [FromMonthCount, ToMonthCount] ); setPeriodText(); drawChart();">
                {for $year = $FirstYear to $LastYear}
                    <input type="button" value="{$year}" onclick="$('#ChartPeriod_slider').slider( 'values', [({$year}-2000)*12, ({$year}-2000)*12+11] ); setPeriodText(); drawChart();">
                {/for}
                <input type="button" value="За последний год" onclick="$('#ChartPeriod_slider').slider( 'values', [InitBegtinPeriodMonthCount, ToMonthCount] ); setPeriodText(); drawChart();">
            </p>
        {/if}
        
    </div>
{else}
    История рейтинга пока отсутствует. После участия в турнире здесь появится график.
{/if}
{/block}