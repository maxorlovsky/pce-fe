<?php

class team
{
    public $team = array();
    
	public function __construct($params = array()) {
        $rows = Db::fetchRows(
            'SELECT `id`, `name`, `avatar` FROM `users` '.
            'WHERE `id` = 1 OR '. //max
            '`id` = 44 OR '. //serge
            '`id` = 112 OR '. //anya
            '`id` = 132 OR '. //arturs
            '`id` = 209 OR '. //zanko
            '`id` = 213 ' //aven
        );
        
        $this->team = array(
            1 => array('avatar' => '', 'name' => '', 'role' => 'Founder', 'contact' => 'maxtream (at) pcesports (dot) com'),
            213 => array('avatar' => '', 'name' => '', 'role' => 'Co-founder', 'contact' => 'aven (at) pcesports (dot) com'),
            112 => array('avatar' => '', 'name' => '', 'role' => 'Graphic designer', 'contact' => '&nbsp;'),
            44 => array('avatar' => '', 'name' => '', 'role' => 'Communications manager', 'contact' => 'connect (at) pcesports (dot) com'),
            3 => array('avatar' => '', 'name' => 'Angel-ada', 'role' => 'Community manager (VK)', 'contact' => '&nbsp;'),
            4 => array('avatar' => '', 'name' => 'Acolent', 'role' => 'Shoutcaster', 'contact' => '&nbsp;'),
            132 => array('avatar' => '', 'name' => '', 'role' => 'Graphic designer', 'contact' => '&nbsp;'),
            5 => array('avatar' => '', 'name' => 'Soldecroix', 'role' => 'Hearthstone judge', 'contact' => '&nbsp;'),
            209 => array('avatar' => '', 'name' => '', 'role' => 'LoL EUNE manager', 'contact' => 'izaanko (at) pcesports (dot) com'),
        );
        
        if ($rows) {
            foreach($rows as $v) {
                $this->team[$v->id]['name'] = $v->name;
                $this->team[$v->id]['avatar'] = $v->avatar;
            }
        }
	}
	
	public function showTemplate() {
		include_once _cfg('pages').'/'.get_class().'/index.tpl';
	}
}