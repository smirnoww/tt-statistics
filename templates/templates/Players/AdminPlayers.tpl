{* Smarty *}

{extends file="AdminLayout.tpl"}

{block name=AdminBody}

<h2>Управление игроками</h2>
<p name="request">
<input id="PlayerSearch" type="text"><input id="PlayerSearchBtn" type="submit" value="Найти">
</p>
<div id="PlayersDiv"></div>

<script type="text/javascript">

    $( function() {
    
        $("#PlayerSearchBtn").click(function(event) {
            var needle = $("#PlayerSearch").val();
            if (needle.length <3)
                alert('Введите как минимум 3 буквы');
            else
                $('#PlayersDiv').load('?ctrl=Players&act=AdminList&needle='+encodeURIComponent(needle));
        });

        $("#PlayerSearch").keyup(function(event) {
            if(event.keyCode == 13){
                $("#PlayerSearchBtn").click();
            }
        });
        
        var preSearch = "{$smarty.cookies.AdminPlayerSearch}";	//	getCookie('AdminPlayerSearch');
        if (preSearch.length>=3) {
            $("#PlayerSearch").val(preSearch);
            $("#PlayerSearchBtn").click();
        }

    });

</script>
{/block}