{* Smarty *}

{*extends file="AdminLayout.tpl"*}
<p>
    Найдено {$PlayersList|count}.
</p>
<table class="tablebg" cellspacing="1">
    <tr class="cat">
        <td>Id</td>
        <td>Имя</td>
        <td>Аватар</td>

        <td>Действия</td>
    </tr>
    {foreach $PlayersList as $Player}
        <tr class="row1">
            <td>{$Player.p_Id}</td>
            <td>{$Player.p_Name}</td>
            <td name="asdf"><input type="button" value="Показать аватар" ShowAvatarPlayerId="{$Player.p_Id}"></td>

            <td>
                <a href="?ctrl=Players&act=Edit&PlayerId={$Player.p_Id}">Редактировать</a>
                <a href="?ctrl=Players&act=Delete">Удалить</a>
            </td>
        </tr>
    {/foreach}
</table>

<script type="text/javascript">
    $(function(){
        $('input[ShowAvatarPlayerId]').click(function(event){
                                            var target = $( event.target );
                                            var p_Id = target.attr('ShowAvatarPlayerId')
                                            $(event.target.parentNode).html('<img width="80" heigth="80" src="?ctrl=Players&act=GetAvatar&PlayerId='+p_Id+'">');
                                        });
    });

{*    
    function dump(obj) {
        var out = "";
        if(obj && typeof(obj) == "object"){
            for (var i in obj) {
                out += i + ": " + obj[i] + "\n";
            }
        } else {
            out = obj;
        }
        alert(out);
    }    
*} 
</script>