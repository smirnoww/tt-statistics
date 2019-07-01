{* Smarty *}
{extends file="AdminLayout.tpl"}

{block name=AdminBody}
<h3>Редактирование сведений об игроке</h3>

<form action="?ctrl=Players&act=Save" method="POST" enctype="multipart/form-data">
    <input name="playerData[p_Id]" type="hidden" value="{$Player.p_Id}">
    {include file="Players/PlayerFormFields.tpl"}
    <input type="submit" value="Сохранить">
</form>

{* $Player|json_encode *}

{/block}
