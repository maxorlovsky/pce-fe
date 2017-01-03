<?php

class uniconhs extends System
{	
    public $participant;
    public $participants;
    public $regIsOpen = 0;
    public $project = 'unicon';

	public function __construct($params = array()) {
        parent::__construct();

        $this->heroes = array(
            1 => 'warrior',
            2 => 'hunter',
            3 => 'mage',
            4 => 'warlock',
            5 => 'shaman',
            6 => 'rogue',
            7 => 'druid',
            8 => 'paladin',
            9 => 'priest',
        );
	}
	
	public function showTemplate() {
        $rows = Db::fetchRows(
            'SELECT `name` AS `battletag`, `contact_info` '.
            'FROM `participants_external` '.
            'WHERE `project` = "'.$this->project.'" AND '.
            '`deleted` = 0 '.
            'ORDER BY `id` ASC'
        );
        if ($rows) {
            foreach($rows as &$v) {
                $v->contact_info = json_decode($v->contact_info);
            }
        }
        $this->participants = $rows;
        unset($v);

        if (isset($_GET['val3']) && $_GET['val3'] && isset($_GET['val4']) && $_GET['val4']) {
            //Might be a participant
            $row = Db::fetchRow(
                'SELECT * FROM `participants_external` '.
                'WHERE `id` = '.(int)$_GET['val3'].' AND '.
                '`link` = "'.Db::escape($_GET['val4']).'" AND '.
                '`deleted` = 0 '.
                'LIMIT 1'
            );
            if ($row) {
                if (isset($_GET['val5']) && $_GET['val5'] == 'leave') {
                    Db::query(
                        'UPDATE `participants_external` SET '.
                        '`deleted` = 1, '.
                        '`update_timestamp` = NOW() '.
                        'WHERE `id` = '.(int)$row->id.' AND '.
                        '`deleted` = 0 AND '.
                        '`project` = "'.$this->project.'" '
                    );
                }
                else {
                    $this->participant = $row;
                    $this->participant->contact_info = json_decode($this->participant->contact_info);
                }
            }
        }
        
        if ($this->participant && $this->regIsOpen == 1) {
            include_once _cfg('widgets').'/'.get_class().'/participant.tpl';
        }
        include_once _cfg('widgets').'/'.get_class().'/index.tpl';
	}

    public function editInTournament($data) {
        $err = array();
        $suc = array();
        parse_str($data['form'], $post);
        
        $heroesPicked = array();
        for($i=1;$i<=3;++$i) {
            if (!$post['hero'.$i]) {
                $err['hero'.$i] = '0;'.t('pick_hero');
            }
            
            if (in_array($post['hero'.$i], $heroesPicked)) {
                $err['hero'.$i] = '0;'.t('same_hero_picked');
            }
            
            if ($post['hero'.$i]) {
                $heroesPicked[] = $post['hero'.$i];
            }
        }
        if ($post['hero1'] == $post['hero2'] && $post['hero1'] != 0) {
            $err['hero2'] = '0;'.t('same_hero_picked');
        }
        
        if ($err) {
            $answer['ok'] = 0;
            if ($suc) {
                $err = array_merge($err, $suc);
            }
            $answer['err'] = $err;
        }
        else {
            $answer['ok'] = 1;
            $answer['err'] = $suc;
            
            $contact_info = json_encode(array(
                'hero1' => $post['hero1'],
                'hero2' => $post['hero2'],
                'hero3' => $post['hero3'],
                'phone' => Db::escape($post['phone']),
            ));
            
            Db::query(
                'UPDATE `participants_external` SET '.
                '`contact_info` = "'.Db::escape($contact_info).'", '.
                '`update_timestamp` = NOW() '.
                'WHERE `id` = '.(int)$post['participant'].' AND '.
                '`link` = "'.Db::escape($post['link']).'" AND '.
                '`deleted` = 0 AND '.
                '`project` = "'.$this->project.'" '
            );
        }
        
        return json_encode($answer);
    }
    
    public function registerInTournament($data) {
        $err = array();
        $suc = array();
        parse_str($data['form'], $post);
        
        $battleTagBreakdown = explode('#', $post['battletag']);

        if (!$post['battletag']) {
            $err['battletag'] = '0;'.t('field_empty');
        }
        else if (!isset($battleTagBreakdown[0]) || !$battleTagBreakdown[0] || !isset($battleTagBreakdown[1]) || !is_numeric($battleTagBreakdown[1])) {
            $err['battletag'] = '0;'.t('field_battletag_incorrect');
        }
        else {
            $post['battletag'] = trim($battleTagBreakdown[0]).'#'.trim($battleTagBreakdown[1]);
            $suc['battletag'] = '1;'.t('approved');
        }
        
        if (!$post['email']) {
            $err['email'] = '0;'.t('field_empty');
        }
        else if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $err['email'] = '0;'.t('email_invalid');
        }
        else {
            $suc['email'] = '1;'.t('approved');
        }
        
        if (!$post['agree']) {
            $err['agree'] = '0;'.t('must_agree_with_rules');
        }
        else {
            $suc['agree'] = '1;'.t('approved');
        }
        
        $heroesPicked = array();
        for($i=1;$i<=3;++$i) {
            if (!$post['hero'.$i]) {
                $err['hero'.$i] = '0;'.t('pick_hero');
            }
            
            if (in_array($post['hero'.$i], $heroesPicked)) {
                $err['hero'.$i] = '0;'.t('same_hero_picked');
            }
            
            if ($post['hero'.$i]) {
                $heroesPicked[] = $post['hero'.$i];
            }
        }
        if ($post['hero1'] == $post['hero2'] && $post['hero1'] != 0) {
            $err['hero2'] = '0;'.t('same_hero_picked');
        }
        
        if ($err) {
            $answer['ok'] = 0;
            if ($suc) {
                $err = array_merge($err, $suc);
            }
            $answer['err'] = $err;
        }
        else {
            $answer['ok'] = 1;
            $answer['err'] = $suc;
            
            $contact_info = json_encode(array(
                'hero1' => $post['hero1'],
                'hero2' => $post['hero2'],
                'hero3' => $post['hero3'],
                'phone' => Db::escape($post['phone'])
            ));

            $code = substr(sha1(time().rand(0,9999)).$post['battletag'], 0, 32);
            Db::query(
                'INSERT INTO `participants_external` SET '.
                '`ip` = "'.Db::escape(isset($_SERVER['HTTP_CF_CONNECTING_IP'])?$_SERVER['HTTP_CF_CONNECTING_IP']:$_SERVER['REMOTE_ADDR']).'", '.
                '`name` = "'.Db::escape($post['battletag']).'", '.
                '`email` = "'.Db::escape($post['email']).'", '.
                '`contact_info` = "'.Db::escape($contact_info).'", '.
                '`link` = "'.$code.'", '.
                '`project` = "'.$this->project.'" '
            );

            $lastId = Db::lastId();
            $tournamentName = 'UniCon 2015 Hearthstone BYOD';
            $url = 'http://www.unicon.lv/pc15&participant='.$lastId.'&link='.$code.'&.html';
            $additionalText = 'Because there are more participants than we imagined. We decide to add "check in" right on UniCon.<br />There are only 32 slots available for tournament, so the first people who will come to event, find Pentaclick eSports booth and register, will be fully checked in and registered.';

            $text = Template::getMailTemplate('reg-player-widget');

            $text = str_replace(
                array('%name%', '%tournamentName%', '%url%', '%additionalText%', '%teamName%'),
                array($post['battletag'], $tournamentName.' tournament', $url, $additionalText, 'UniCon Latvia and Pentaclick eSports'),
                $text
            );
        
            $this->sendMail($post['email'], $tournamentName.' participation', $text);

            $answer['ok'] = 1;
        }
         
        return json_encode($answer);
    }
}