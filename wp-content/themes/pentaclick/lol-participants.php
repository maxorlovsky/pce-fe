<div class="holder" id="participants"></div>
<article class="participants-content-wrapper">
    <div class="content" id="participants-content">
        <?
        $q = mysql_query(
    		'SELECT id, name FROM `teams` WHERE '.
    		' `tournament_id` = '.(int)cOptions('tournament-lol-number').' AND '.
    		' `game` = "lol" AND '.
            ' `approved` = 1 AND '.
            ' `deleted` = 0'
        );
        if (mysql_num_rows($q) == 0) {
            ?><p class="empty-list"><?=_e('no_teams_registered', 'pentaclick')?></p><?
        }
        else {
            $i = 1;
            while($r = mysql_fetch_object($q)) { ?>
                <div class="block" title="<?=$r->name?> #<?=$i?>">
                    <h3 title="<?=$r->name?>">
                        <?=strlen($r->name) > 14?substr($r->name,0,13).'...':$r->name?>
                    </h3>
                    <span class="team-num">#<?=$i?></span>
                    <div class="clear"></div>
                    <div class="hidden player-list">
                        <h4><?=_e('players', 'pentaclick')?>:</h4>
                        <ul>
                            <?
                            $q2 = mysql_query(
                        		'SELECT name, player_id FROM `players` WHERE '.
                        		' `tournament_id` = '.(int)cOptions('tournament-lol-number').' AND '.
                        		' `team_id` = '.$r->id.' '.
                                ' ORDER BY player_num '
                            );
                            while ($r2 = mysql_fetch_object($q2)) {
                                ?><li><a href="http://www.lolking.net/summoner/euw/<?=$r2->player_id?>" target="_blank"><?=$r2->name?></a></li><?
                            }
                            ?> 
                        </ul>
                    </div>
                </div>
                <?
                ++$i;
            }
        } ?>
        <div class="clear"></div>
    </div>
</article>