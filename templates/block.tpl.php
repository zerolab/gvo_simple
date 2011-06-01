<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?>">

<?php if (!empty($block->subject)): ?>
  <h2 class="block-title"><?php print $block->subject ?></h2>
<?php endif;?>

  <?php print $block->content ?>
  
</div>