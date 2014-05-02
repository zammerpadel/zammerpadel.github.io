<?php
$languages = getLanguagesEnabled();
?>

<ul class="langSelector">
<?php foreach ($languages as $lang):?>
	<li><a href="<?php echo WWWROOT . "actions/language/set.php?l=".$lang?>" class="<?php echo (getLanguage()==$lang)?"active":"";?>"><?php echo getFullLanguajeName($lang)?></a></li>


    <?php endforeach;?>
<!--     <li><a href="http://www.nearpod.com/feed/" class="feed" target="_blank">Nearpod Feed</a></li> -->
</ul>
