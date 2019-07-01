{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
<div>
<h2>Турниры по годам</h2>
<div id="YearTabsHor">
    <ul>
        {foreach $Years as $year}
            <li><a href="?ctrl=Tours&act=GetTours&year={$year}">{$year}</a></li>
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
                        	{$ActiveTabCookieName='TourList_ActiveYearTab'}
    						activate:   function( event, ui ) { 
    						                //Запомним открытую закладку в куках
    						                var activeTabN = $( event.target ).tabs( "option", "active" );
    						                document.cookie = '{$ActiveTabCookieName}='+activeTabN;
    						            },
						    active: getCookie('{$ActiveTabCookieName}') {* восстановим открытую закладку из куков*}

						});
</script>
<!--test-->
</div>
{/block}