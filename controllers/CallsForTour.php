<?php

Class Controller_CallsForTour Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']              = anonym_AR;
        $AccessMatrix['GetCallsTable']      = anonym_AR;
        $AccessMatrix['CallOnline']         = anonym_AR;
        $AccessMatrix['DeCallOnline']       = anonym_AR;
        $AccessMatrix['PrintCalls']         = admin_AR;
        $AccessMatrix['EditCalls']          = admin_AR;
        $AccessMatrix['SaveTourCallsList']  = admin_AR;

        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    }


	// онлайн форма заявок в турнир
	function Index($r) {
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$t_Id = $r['Params']['TourId'];
		else
			die('Не задан id турнира');

		if (isset($r['Params']['embedded'])) 
            $embedded = true;
        else 
            $embedded = false;

		if (isset($r['Params']['JQuery'])) 
            $JQuery = true;
        else 
            $JQuery = false;

        $tour = Model_Tour::GetOne($t_Id);

		// Если турнир ещё не прошёл, то разрешим регистрацию
		if ($tour->t_DateTime > new DateTime())
            $AllowCall = true;
		else
            $AllowCall = false;


	    $smarty = $r['smarty'];
		$smarty->assign('Tour', $tour);	
		$smarty->assign('AllowCall', $AllowCall);	
		$smarty->assign('Embedded', $embedded);	
		$smarty->assign('JQuery', $JQuery);	
		

		$template = 'CallsForTour/OnlineCallsForm.tpl';
		if ($embedded) 
            $prefix = '';
        else 
            $prefix = 'extends:Layout.tpl|';

//echo $prefix.$template."-$JQuery-";

		$smarty->display($prefix.$template);
	}   //   Index($r)
	
	
    // Возвращает список заявок
	function GetCallsTable($r) {	
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else
			die('Не задан id турнира');

        $CallsForTour = Model_CallForTour::GetTourCalls($TourId);
        
	    $smarty = $r['smarty'];
		$smarty->assign('CallList', $CallsForTour);	
		$smarty->display('CallsForTour/CallsTable.tpl');
	}
	
	
	// показывает списко заявок, отсортированный по рейтингу на белом фоне для печати
	function PrintCalls($r) {
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else
			die('Не задан id турнира');

        $CallsForTour = Model_CallForTour::GetTourCalls($TourId,'cft_PlayerRating DESC');

	    $smarty = $r['smarty'];
		$smarty->assign('CallList', $CallsForTour);	
		$smarty->display('CallsForTour/CallsTable.tpl');
	}   //  function PrintCalls($r)
	
	// Добавить игрока в заявку основным для AJAX запросов. возвращает результат выполнения операции
	function CallOnline($r) {
        // Очистим кэш
        $r['router']->clearCache();

		$result = '...';
	
		// Проверим, что передан Турнир
		if (isset($r['Params']['TourId'])) 
			$TourId = $r['Params']['TourId'];
		else {
			echo "Для подачи заявки должен быть определён идентификатор турнира! Передайте, пожалуйста, администрации сайта скриншот этого сообщения для исправления этой ошибки.";
			return;
		}

        $tour = Model_Tour::GetOne($TourId);

		// Проверим, что регистрация не закрыта
		if (!$tour->RegistrationAvailable()) {
			echo "Регистрация на турнир закрыта ".$tour->t_DateTime->format('d.m.Y H:i')."!";
			return;
		}
		
		// Проверим, что игрок авторизован
		if ($r['Auth']->AuthPlayerId<0) {
			echo "Для изменения заявки необходимо авторизоваться на форуме и привязать профиль игрока к пользователю форума! Обратитесь к организаторам в теме турнира.";
			return;
		}

		// Получим комментарий
		$Comment = isset($r['Params']['Comment']) 
		            ? $r['Params']['Comment'] 
		            : '';

		//Проверим, что игрок не заявился ранее
        $OldCallsCount = Model_CallForTour::GetCount(
                                                    'cft_TourId = :TourId and 
                                                    cft_PlayerId = :PlayerId', 
                                                    array(':TourId'=>$TourId,
                                                            ':PlayerId'=>$r['Auth']->AuthPlayerId)
                                                );

		// Если игрока ещё не было в заявке, то добавим его
		if ($OldCallsCount == 0) {
			$cft = new Model_CallForTour();
				
			$cft->cft_TourId	 		= $TourId;
	//		$cft->cft_CallDateTime		= Date('d.m.Y H:i:s');
			$cft->cft_PlayerId			= $r['Auth']->AuthPlayerId;
			$cft->cft_PlayerRating		= $r['Auth']->AuthPlayer->GetRate();
			$cft->cft_Comment			= $Comment;
			
			$cft->save();
			$result = 'Ваша заявка добавлена.';
		}
		else  // Если игрок уже был в заявке, то 
			$result = 'Вы уже участвуете в этом турнире. Повторно заявляться нельзя.';
		
        header('Access-Control-Allow-Origin: http://tt-saratov.ru');
		
		// Вернём результат действия
		echo $result;
	}   //  function CallOnline($r)
	
	//снять игрока из заявки для AJAX запросов. возвращает результат выполнения операции
	function DeCallOnline($r) {
        // Очистим кэш
        $r['router']->clearCache();

		$result = '...';
	
		// Проверим, что передан Турнир
		if (isset($r['Params']['TourId'])) 
			$TourId = $r['Params']['TourId'];
		else {
			echo "Для подачи заявки должен быть определён идентификатор турнира! Передайте, пожалуйста, администрации сайта скриншот этого сообщения для исправления этой ошибки.";
			return;
		}

        $tour = Model_Tour::GetOne($TourId);

		// Проверим, что регистрация не закрыта
		if (!$tour->RegistrationAvailable()) {
			echo "Регистрация на турнир закрыта ".$tour->t_DateTime->format('d.m.Y H:i')."!";
			return;
		}
		
		// Проверим, что игрок авторизован
		if ($r['Auth']->AuthPlayerId<0) {
			echo "Для изменения заявки необходимо авторизоваться на форуме и привязать профиль игрока к пользователю форума! Обратитесь к организаторам в теме турнира.";
			return;
		}

		// Получим заявку игрока
        $Calls = Model_CallForTour::GetList(
												'cft_TourId = :TourId and 
												cft_PlayerId = :PlayerId',
												array(),
												array(':TourId'=>$TourId,
														':PlayerId'=>$r['Auth']->AuthPlayerId)
											);

		$Call = array_shift($Calls);

		// Если игрока нет в заявке, то 
		if ($Call === NULL)
			$result = 'Вас нет в заявке на этот турнир.';
		
		// Если заявка есть, то удалим её
		if (is_object($Call))
			if (get_class($Call)=='Model_CallForTour') {
				$Call->delete();
				$result = 'Ваша заявка удалена.';
			}
		
		
        header('Access-Control-Allow-Origin: http://tt-saratov.ru');
		
		// Вернём результат действия
		echo $result;
	}
	
	// Форма редактирования заявок для организатора
	function EditCalls($r) {	
		$smarty = $r['smarty'];

		try {
			// Проверим, что передан id турнира 
			if (isset($r['Params']['TourId'])) 
				$TourId = $r['Params']['TourId'];
			else		
				throw new Exception("Для вывода списка заявок должен быть передан идентификатор турнира!");
			
			$Tour = Model_Tour::GetOne($TourId);
			$CallsForTour = Model_CallForTour::GetTourCalls($TourId);

			$smarty->assign('Tour',$Tour);	
			$smarty->assign('CallsForTour',$CallsForTour);	
			$smarty->display('CallsForTour/AdminCallsList.tpl');
			
		}
		catch (Exception $e) {
			$smarty->append('Message',Array('Type'=>'Error',
											'Title'=>'Ошибка вывода заявок на турнир',
											'Body'=>$e->getMessage(),
											'TechInfo'=>$e));	
			$smarty->display('Layout.tpl');
		
		}	}
		
	// Сохраним список заявок на турнир
	function SaveTourCallsList($r) {
        // Очистим кэш
        $r['router']->clearCache();

		// Определим Id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else {
			echo "Для сохранения заявок должен быть определён идентификатор турнира! Передайте, пожалуйста, администрации сайта скриншот этого сообщения для исправления этой ошибки.";
			return;
    	}   	    
    	
		// Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=TourProfile&t_Id=$TourId"; // Профиль турнира

        $Acts = $r['Params']['cft_Act'];

        foreach ($Acts as $cft_Id => $act) {

            if ($act!='none') {
                $cft_model = new Model_CallForTour(null, $r['Params']['cft_Data'][$cft_Id]);
                

                if ($act=='del')
                    $cft_model->Delete();
                else {
                    $cft_model->Save();
                }
            } 
        }
//echo "<pre>".json_encode($r['QueriesLog'],JSON_PRETTY_PRINT)."<pre>";
//die();
		//Вернёмся на исходную страницу
		header("Location: $BackURL");
	}
	
	// Добавить игрока в заявку основным для AJAX запросов. возвращает результат выполнения операции
	function ClearCache($r) {
        // Очистим кэш
        $r['router']->clearCache();
	}
		
}
?>