{* Smarty *}
{extends file="Courts/AdminCourtLayout.tpl"}

{block name=AdminCourtBody}
<h3>Изменение площадки</h3>

<form action="?ctrl=Courts&act=Save" method="POST" enctype="multipart/form-data">
    <input name="courtData[c_Id]" type="hidden" value="{$Court.c_Id}">
    {include file="Courts/CourtFields.tpl"}
    <input type="submit" value="Сохранить">
</form>

{/block}