{* Smarty *}
{extends file="Courts/AdminCourtLayout.tpl"}

{block name=AdminCourtBody}
<h3>Добавление новой площадки</h3>

<form action="?ctrl=Courts&act=Save" method="POST" enctype="multipart/form-data">
    {include file="Courts/CourtFields.tpl"}
    <input type="submit" value="Добавить">
</form>

{/block}