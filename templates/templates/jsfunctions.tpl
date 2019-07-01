{* Smarty *}
<script type="text/javascript">
    /**
    * Функция Скрывает/Показывает блок 
    * @author ox2.ru дизайн студия
    **/
    function showHide(element_id, visible) {
        //Если элемент с id-шником element_id существует
        if (document.getElementById(element_id)) { 
            //Записываем ссылку на элемент в переменную obj
            var obj = document.getElementById(element_id); 
            //Если css-свойство display не block, то: 
			if(typeof(visible)==='undefined') {
				if (obj.style.display == "block")
					visible = false;
				else
					visible = true;
			}
            if (visible) { 
                obj.style.display = "block"; //Показываем элемент
            }
            else obj.style.display = "none"; //Скрываем элемент
        }
        //Если элемент с id-шником element_id не найден, то выводим сообщение
        else alert("Элемент с id: " + element_id + " не найден!"); 
    }   
</script>
