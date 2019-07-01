{* Smarty *}
{extends file="Tours/AdminTourLayout.tpl"}

{block name=AdminTourBody}
<a href="?ctrl=Tours&act=NewTour">Добавить турнир</a>
<h2>Турниры по годам</h2>
<div id="YearTabsHor">
    <ul>
        {foreach $Years as $year}
            <li><a href="?ctrl=Tours&act=GetAdminTours&year={$year}">{$year}</a></li>
        {/foreach}
    </ul>
</div>

<script>
     $( "#YearTabsHor" ).tabs({
							beforeLoad: function( event, ui ) { // Загружаем содержимое страниц только один раз
																if ( ui.tab.data( "loaded" ) ) {
																	event.preventDefault();
																	return;
																}
														 
																ui.jqXHR.success(function() {
																	ui.tab.data( "loaded", true );
																});
										},
                        	{$ActiveTabCookieName='AdminTourList_ActiveYearTab'}
    						activate:   function( event, ui ) { 
    						                //Запомним открытую закладку в куках
    						                var activeTabN = $( event.target ).tabs( "option", "active" );
    						                document.cookie = '{$ActiveTabCookieName}='+activeTabN;
    						            },
						    active: getCookie('{$ActiveTabCookieName}') {* восстановим открытую закладку из куков*}
						});
</script>

{/block}