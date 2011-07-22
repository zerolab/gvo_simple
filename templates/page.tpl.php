<!DOCTYPE html>
<html lang="<?php print $language->language ?>">
<head>
  <?php print $head; ?>
  <title><?php print $title; ?></title>
  <?php print $styles ?>
</head>
<body <?php print drupal_attributes($attr) ?>>
  <div id="header">
    <div id="branding">
    <?php if ($logo) : ?>
      <a href="<?php print $base_path ?>" title="<?php print $site_name ?>" id="logo">
        <img src="<?php print $logo ?>" alt="<?php print $site_name ?> logo" />
      </a>
    <?php elseif ($site_name) : ?>
      <h1 class='site-name'><?php print $site_name ?></h1>
    <?php endif; ?>
    </div>
    <?php if ($header) print $header ?>    
  </div>
  <div id="navigation">
  <?php if (isset($primary_links)) : ?>
    <?php print theme('links', $primary_links, array('class' => 'links primary-links')) ?>
  <?php endif; ?>
  <?php if (isset($secondary_links)) : ?>
    <?php print theme('links', $secondary_links, array('class' => 'links secondary-links')) ?>
  <?php endif; ?>
  </div>
  
  <?php if ($help || ($show_messages && $messages)) : ?>
  <div id="messages">
    <?php print $messages ?>
  </div>
  <?php endif; ?>

  <?php if ($featured) : ?>
  <div id="featured">
    <?php print $featured ?>
  </div>
  <?php endif; ?>
  
  <div id="page">
    <?php if ($breadcrumb) print $breadcrumb ?>
    <?php if ($title) : ?><h2 class="title"><?php print $title ?></h2><?php endif; ?>
    <?php if ($tabs) print $tabs ?>
    
    <div id="content">
      <?php print $content ?>
    </div>
    
    <?php if ($side) : ?>
    <div id="side">
      <?php print $side ?>
    </div>
    <?php endif; ?>
  </div>
  
  <div id="footer">
    <?php print $feed_icons ?>
    <?php print $footer ?>
    <?php print $footer_message ?>
  </div>
  
<?php print $scripts ?>
<?php print $closure ?>

<?php if ($is_admin) : ?>
	<script src="<?php print base_path() . path_to_theme()?>/js/hashgrid.js"></script>
<?php endif; ?>
</body>
</html>