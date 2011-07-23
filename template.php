<?php

/**
 * Implementation of hook_theme()
 */
function gvo_simple_theme() {
  $items = array();

  // Consolidate a variety of theme functions under a single template type.
  $items['block'] = array(
    'template' => 'block',
    'path' => drupal_get_path('theme', 'gvo_simple') .'/templates',
  );
  $items['box'] = array(
    'arguments' => array('title' => NULL, 'content' => NULL, 'region' => 'main'),
    'template' => 'box',
    'path' => drupal_get_path('theme', 'gvo_simple') .'/templates',
  );
  $items['comment'] = array(
    'arguments' => array('comment' => NULL, 'node' => NULL, 'links' => array()),
    'template' => 'comment',
    'path' => drupal_get_path('theme', 'gvo_simple') .'/templates',
  );
  $items['node'] = array(
    'arguments' => array('node' => NULL, 'teaser' => FALSE, 'page' => FALSE),
    'template' => 'node',
    'path' => drupal_get_path('theme', 'gvo_simple') .'/templates',
  );

  return $items;
}

/**
 * Implementation of hook_preprocess_page()
 */
function gvo_simple_preprocess_page(&$vars) {
  $attr = array();
      
  if (isset($vars['node'])) {
    $vars['template_files'][] = 'page-'. $vars['node']->type;
  }
  
  if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $vars['template_files'][] = 'page-ajax';
  }
  
  if (drupal_is_front_page()) {
    $vars['template_files'][] = 'page-front';
    $vars['id'] = 'front';
  }
  else if (arg(0) == 'node' && (arg(1) == 'add' || arg(2) == 'edit')) {
  	$vars['side'] = '';
  }
  
  $attr['class'] = gvo_simple_body_classes_by_path($vars);  
  $vars['attr'] = $attr;
  
  if (isset($_GET['print'])) {

    $vars['template_files'][] = 'print-page';
    
    if (isset($vars['node']->type)) {
      $vars['template_files'][] = 'print-page-'. $vars['node']->type;
    }

    $css = drupal_add_css();

    unset($css['all']);
    unset($css['screen']);

    $css['all'] = $css['print'];    
  }
  else {

    if ($vars['user']->uid) {
      // add admin styles only for logged in users
      drupal_add_css(path_to_theme() .'/css/admin.css', 'theme', 'screen');
    }
  }
  
  $vars['styles'] = drupal_get_css($css);
  
  // Strip duplicate head charset metatag
  $matches = array();
  preg_match_all('/(<meta http-equiv=\"Content-Type\"[^>]*>)/', $vars['head'], $matches);

  if( count($matches) > 1) {
    $vars['head'] = preg_replace('/<meta http-equiv=\"Content-Type\"[^>]*>/', '', $vars['head'], 1); // strip 1 only
  }
}

/**
 * Implementation of hook_preprocess_node()
 */
function gvo_simple_preprocess_node(&$vars) {
  $vars['template_files'] = array();
  $vars['template_files'][] = 'node';
  $vars['template_files'][] = 'node-' . $vars['node']->type;

  // hide comment form on nodes loaded via ajax
  if (isset($_GET['ajax']) && $_GET['ajax'] == 1 ){
    $vars['node']->comment = 0;
    $vars['comments'] = $vars['comment_form'] = '';
  }
}

/**
 * Override of theme_username().
 * Bits and pieces taken from http://drupal.org/project/tao
 */
function gvo_simple_username($object) {
  
  //Display full name as provided via Content Profile
  if (module_exists('content_profile') && $object->uid) {
    $content_profile = content_profile_load('profile', $object->uid);
    
    if ($content_profile->title) {
      $object->name = check_plain($content_profile->title);
      $object->profile_nid = $content_profile->nid;
    }
    unset($content_profile);
  }
  if (!empty($object->name)) {
    // Shorten the name when it is too long or it will break many tables.
    $name = drupal_strlen($object->name) > 20 ? drupal_substr($object->name, 0, 15) .'...' : $object->name;
    $name = check_plain($name);

    // Default case -- we have a real Drupal user here.
    if ($object->uid && user_access('access user profiles')) {
      $user_path = 'user/'. $object->uid;

      if ($object->profile_nid) {
        $user_path = 'node/'. $object->profile_nid;
      }
      return l($name, $user_path, array('attributes' => array('class' => 'username', 'title' => t('View profile.'))));
    }
    // Handle cases where user is not registered but has a link or name available.
    else if (!empty($object->homepage)) {
      return l($name, $object->homepage, array('attributes' => array('class' => 'username', 'rel' => 'nofollow')));
    }
    // Produce an unlinked username.
    else {
      return "<span class='username'>{$name}</span>";
    }
  }
  return "<span class='username'>". variable_get('anonymous', t('Anonymous')) ."</span>";
}

/**
 * Implementation of hook_preprocess_block()
 */
function gvo_simple_preprocess_block(&$vars) {
	if ($vars['block']->region == 'header') {		
		unset($vars['block']->subject);
	}
}

/**
 * Returns the themed submitted-by string for the node.
 */
function gvo_simple_node_submitted($node) {
  return t('!datetime by !username',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created, 'custom', 'F j, Y'),
    ));
}


/**
 * Allow themable wrapping of all comments.
 */
function gvo_simple_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    return '<div id="comments"><h2>'. t('Comments') .'</h2>'. $content .'</div>';
  }
}

/**
 * Returns the themed submitted-by string for the comment.
 */
function gvo_simple_comment_submitted($comment) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

/*******************************************************************************
 * Helper functions
 ******************************************************************************/

/**
 * Adds a class based on the current path
 */
function gvo_simple_body_classes_by_path(&$vars) {  
  
  if (!($current_path = $vars['node']->path)) {
    $vars['body_classes']  .= ' page-' . check_plain(arg(0));
  }
  else {
    $current_path = explode('/', $current_path);
    $vars['body_classes'] .= ' page-' . $current_path[0];

    if (isset($current_path[1])) {
      $vars['body_classes'] .= ' page-' . $current_path[0] . '-' . $current_path[1];
    }
  }

  // check for sidebar contents and replace with corresponding class
  if (!empty($vars['side'])) {
    $vars['body_classes'] = str_replace('no-sidebars', 'sidebar', $vars['body_classes']);   
  }
  
  // some cleanup
  $classes = explode(' ', $vars['body_classes']);
  
  return implode(' ', array_unique($classes));  
}