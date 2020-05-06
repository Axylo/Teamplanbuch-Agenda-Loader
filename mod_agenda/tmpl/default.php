<?php
   /**
      * @package Agenda Loader
      * @subpackage mod_agenda
      * @copyright Copyright (C) 2019 Alex Schwerzmann, All rights reserved.
      * @license GNU General Public License version 2 or later
   */
defined('_JEXEC') or die;
?>

<?php if ($events == null) : ?>
  <p class="alert alert-warning text-left my-3">Hoppla - das ging daneben...</p>

<?php elseif ($events->meta->code == 400) : ?>
  <p class="alert alert-warning text-left my-3"><?= $events->meta->code . ' - ' . $events->meta->error_message; ?></p>

<?php else : /* https://css-tricks.com/accessible-simple-responsive-tables/ */?>   
 
	<?php 
		//get number of columns from module settings
		$columns = htmlentities($params->get('columns',''), ENT_COMPAT, 'UTF-8'); 
	?>
	
	<div class="Rtable Rtable--<?php echo $columns ?>cols Rtable--collapse">
		<?php foreach ($events as $event) : ?>
				<div class="Rtable-cell Rtable-cell--datesheet">
					<br>
					<div class="month-and-year"><?php echo $event['Monatsname']; ?> <?php echo $event['Jahr'];?></div>
					<p class="spacer"></p>
					<div class="day"><?php echo $event['Datum'];?></div>
				</div>
				<div class="Rtable-cell .Rtable-cell--foot">
					<p class="title"><?php echo $event['Titel'];?></p>
					<p class="time"><?php echo $event['Zeit_Von_Bis'];?> Uhr</p>
					<p class="place"><?php echo $event['Ort'];?></p>
				</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>