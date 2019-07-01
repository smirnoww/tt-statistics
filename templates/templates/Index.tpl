{* Smarty *}
{extends file="Layout.tpl"}

{block name=body}
    Добро пожаловать в систему расчёта рейтинга, {$Auth->AuthPlayer->p_Name|default:$Auth->username}! 
{/block}
 
