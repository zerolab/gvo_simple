<div class="node node-<?php print $node->type ?> node-<?php print $node->nid ?><?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
  <?php if (!$page) : ?>
    <h2 class="title"><a href="<?php print $node_url?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
  <?php endif; ?>
  
  <?php if ($submitted) : ?>
    <p class="submitted"><?php print $submitted ?></p>
  <?php endif;?>
  
  <?php print $content ?>
  
  <?php if (!empty($links) || !empty($terms)): ?>
  <div class="meta">
  <?php if ($terms): ?>
    <div class="terms">
      <span><?php print t('Tags: ') ?></span><?php print $terms ?>
    </div>
  <?php endif;?>
  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>
  </div>
  <?php endif;?>
</div>