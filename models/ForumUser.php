<?php
Class Model_ForumUser Extends Model_Base {

    protected static $DBInstanceName    = 'forum';
	protected static $TableName         = 'phpbb1_users';
	protected static $PrimaryKey        = 'user_id';

	public function save() {
        throw new Exception("Model_ForumUser is readonly and cannot be saved");
    }
    
    public function delete() {
        throw new Exception("Model_ForumUser is readonly and cannot be deleted");
    }
	
    // Вернём день рождения как дату
    public function __get($name) {
		if ($name=='user_birthday_asDateTime') {
	        $user_birthday = parent::__get('user_birthday');
			$dateComponents = explode('-', $user_birthday);
			if (count($dateComponents)==3) {
				$dateComponentsTrimmed = array_map('trim', $dateComponents);
				foreach ($dateComponentsTrimmed as $key => $component)
					$dateComponentsTrimmed[$key] = str_pad($component, 2, '0', STR_PAD_LEFT);
				
				$value = Model_DateTime::createFromFormat('d-m-Y',"{$dateComponentsTrimmed[0]}-{$dateComponentsTrimmed[1]}-{$dateComponentsTrimmed[2]}");
				$value->setDefaultFormat('d.m.Y');
			}
			else
				$value = null;
			
		}
		else
        	$value = parent::__get($name);
				
        return $value;
    }

}

?>