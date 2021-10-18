<?php
script('deckimportfromtrello', 'script');
style('deckimportfromtrello', 'style');
/** @var array $_ */
/** @var \OCP\IL10N $l */
?>

<form id="deckimportfromtrello_form" class="section">

	<h2><?php p($l->t('Deck Import')); ?></h2>
	<p class="settings-hint">
        <?php p($l->t('Please select a json file with your Trello export.')); ?>
	</p>
</form>
