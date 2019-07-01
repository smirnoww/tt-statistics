{* Smarty *}
<!DOCTYPE HTML SYSTEM>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>{$title|default:"Настольный теннис в Саратове - Любительская лига - Статистика"}</title>
    

    <script type="text/javascript" src="jquery/external/jquery/jquery.js"></script>
    <script type="text/javascript" src="jquery/jquery-ui.js"></script>
    <link rel="stylesheet" href="jquery/jquery-ui.css" >
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">  
    <link rel="stylesheet" type="text/css" href="css/statistics.css">

    <link rel="shortcut icon" href="images/favicon.ico">    

    <script type="text/javascript">
    
    // возвращает cookie с именем name, если есть, если нет, то undefined
    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    
    // отключим кэширование AJAX
    $.ajaxSetup ({
        // Disable caching of AJAX responses
        cache: false
    });
     </script>
	{block name=head}{/block}
</head>

<body class="ltr">
	{block name=header}
		{include file="Header.tpl"}
	{/block}


{*
<!-- меню надо будет включить для админов
-->
*}
	{block name=menu}
		{include file="Menu.tpl"}
	{/block}

	{block name=message}
		{include file="Message.tpl"}
	{/block}

	
	{block name=body}
	{/block}

	{block name=footer}
		<p align="right"> (c) 2013</p>
		<p align="right"> 
            <!--LiveInternet counter--><script type="text/javascript"><!--
            document.write("<a href='//www.liveinternet.ru/click' "+
            "target=_blank><img src='//counter.yadro.ru/hit?t23.6;r"+
            escape(document.referrer)+((typeof(screen)=="undefined")?"":
            ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
            screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
            ";h"+escape(document.title.substring(0,80))+";"+Math.random()+
            "' alt='' title='LiveInternet: показано число посетителей за"+
            " сегодня' "+
            "border='0' width='88' height='15'><\/a>")
            //--></script><!--/LiveInternet-->
		</p>
	{/block}

{* Если среда для разработки, то выведем запросы к БД*}
{if $Registry.Env=='DEV'}
<div>
    <hr>
    DB Queries - {$Registry.QueriesLog|@count}: <br>
    <pre>
        {$Registry.QueriesLog|json_encode:128}
    </pre>
</div>
{/if}

</body>

</html>