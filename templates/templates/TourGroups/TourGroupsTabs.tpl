{* SMARTY *}
<div id="GroupTabs">
	<ul>
		{foreach $GroupsList as $group}
    		<li id="Tour_{$group.g_TourId}_Group_{$group.g_Id}_Tab"><a href="?ctrl=TourGroups&act=ShowGroup&GroupId={$group.g_Id}">{$group.g_Name|escape}
                {include 'TourGroups/GroupIcon.tpl' GroupColor={$group.g_Color}}
    		</a></li>  
		{/foreach}

	</ul>
</div>

<!-- Запустим jqueryUI -->
<script>
    $(function() {
        // включим закладки
        $( "#GroupTabs" ).tabs({
    						beforeLoad: function( event, ui ) { 
    						                    // Загружаем содержимое страниц только один раз
												if ( ui.tab.data( "loaded" ) ) {
													event.preventDefault();
													return;
												}
										 
												ui.jqXHR.success(function() {
													ui.tab.data( "loaded", true );
												});
    									},
                    		{$ActiveTabCookieName='TourProfile_'|cat:$group.g_TourId|cat:'_ActiveGroupTab'}
    						activate:   function( event, ui ) { 
    						                //Запомним открытую закладку в куках
    						                var activeTabN = $( event.target ).tabs( "option", "active" );
    						                document.cookie = '{$ActiveTabCookieName}='+activeTabN;
    						            },
						    active: getCookie('{$ActiveTabCookieName}') {* восстановим открытую закладку из куков*}
    							});
	});
</script>
