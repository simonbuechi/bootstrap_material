<?php

/**
 * @file
 * template.php
 */

define('JS_BOOTSTRAP_MATERIAL', 200);
 
/**
 * Preprocess html.tpl.php.
 *
 * @see bootstrap_material_js_alter()
 */
function bootstrap_material_preprocess_html(&$vars) {
  // Add class to help us style admin pages.
  if (path_is_admin(current_path())) {
    $vars['classes_array'][] = 'admin';
  }
  // Prepare to initialize.
  drupal_add_js('(function ($){ $.material.init(); })(jQuery);', array(
    'type' => 'inline', 
    'group' => JS_BOOTSTRAP_MATERIAL, 
    'scope' => 'footer', 
    'weight' => 2
  ));
}

/**
 * Implements hook_js_alter().
 *
 * Make sure the library files provided by MDB load last, then initialize.
 *
 * @see bootstrap_material_preprocess_html()
 */
function bootstrap_material_js_alter(&$js) { 
  
  $file = path_to_theme() . '/js/bootstrap_material.js';
  
  $js[$file] = drupal_js_defaults($file);
  $js[$file]['group'] = JS_BOOTSTRAP_MATERIAL;
  $js[$file]['scope'] = 'footer';
  $js[$file]['weight'] = $weight = 0;
  
  // Ensure we initialize only after files are loaded.
  foreach ($js as $key => $val) {
    if (is_int($key) && $val['group'] == JS_BOOTSTRAP_MATERIAL) {
      $weight++;
      $js[$key]['weight'] = $weight;
    }
  }
}

/**
 * Overrides theme_menu_local_tasks().
 *
 * Overrides Bootstrap module's override. Let's not turn the secondary menu 
 * into a pagination element.
 *
 * @see bootstrap_menu_local_tasks()
 */
function bootstrap_material_menu_local_tasks(&$variables) {
  $output = '';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<div class="element-invisible">' . t('Primary tabs') . '</div>';
    $variables['primary']['#prefix'] .= '<ul class="primtabs">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }

  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<div class="element-invisible">' . t('Secondary tabs') . '</div>';
    $variables['secondary']['#prefix'] .= '<ul class="tabs--secondary">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }

  return $output;
}

function bootstrap_material_menu_local_tasks_alter(&$data, $router_item, $root_path) {
  foreach ($data['tabs'] as $key1 => $tabs) {
    foreach ($tabs['output'] as $key2 => $item) {
        $data['tabs'][$key1]['output'][$key2]['#link']['localized_options']['attributes']['class'][] = 'btn btn-raised btn-primary';
      
    }
  }
}

function bootstrap_material_bootstrap_colorize_text_alter(&$texts) {
  $texts['contains'][t('Apply')] = 'primary btn-raised';
  $texts['contains'][t('Next')] = 'primary btn-raised';  
  $texts['contains'][t('Execute')] = 'primary btn-raised';  
  $texts['contains'][t('View changes')] = 'primary btn-raised';  
  $texts['contains'][t('Save')] = 'success btn-raised';
  $texts['contains'][t('Log in')] = 'success btn-raised';  
  $texts['contains'][t('Confirm')] = 'success btn-raised';    
  $texts['contains'][t('Reset')] = 'danger btn-raised';
  $texts['contains'][t('Cancel')] = 'danger btn-raised';
  $texts['contains'][t('Delete')] = 'danger btn-raised';  
  $texts['contains'][t('Add another')] = 'primary';  
}

/**
 * hook_preprocess_flag
 */
function bootstrap_material_preprocess_flag(&$variables) {
  $variables['flag_classes_array'][] = 'btn btn-raised btn-primary';
}

/*
 // search page using search_api, pages, views (instead of core search)
function bootstrap_material_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    // HTML5 placeholder attribute
    // $form['search_block_form']['#attributes']['placeholder'] = t('search...');
    $form['#submit'][] = 'my_search_form_submit_function';
  }
}
function my_search_form_submit_function(&$form, &$form_state) {
  $search_str = 's/' . $form_state['values']['search_block_form'];
  $form_state['rebuild'] = TRUE;
  drupal_goto($path = $search_str);
}
*/

 /**
 * hook_preprocess_views_view
 */
function bootstrap_material_preprocess_views_view(&$vars) {
  // Wrap exposed filters in a fieldset.
  if ($vars['exposed']) {
    drupal_add_js('misc/form.js');
    drupal_add_js('misc/collapse.js');
    // Default collapsed
    $collapsed = TRUE;
    $class = array('collapsible', 'collapsed');
    if (count($_GET) > 1){
      // assume other get vars are exposed filters, so expand fieldset to show applied filters
      $collapsed = FALSE;
      $class = array('collapsible');
    }
    $fieldset['element'] = array(
      '#title' => t('Filter'),
      '#collapsible' => TRUE,
      '#collapsed' => $collapsed,
      '#attributes' => array('class' => $class),
      '#children' => $vars['exposed'],
    );
    $vars['exposed'] = theme('fieldset', $fieldset);
  }
}

function bootstrap_material_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'views_exposed_form') {
    // HTML5 placeholder attribute
    // $form['search_block_form']['#attributes']['placeholder'] = t('search...');
    $form['search_api_views_fulltext']['#attributes']['placeholder'] = t('search...');
  }
  if ($form_id == 'finder_form_content_finder') {
    $form['finder_form']['title']['#attributes']['placeholder'] = t('search...');
  }  
}


function bootstrap_material_views_bulk_operations_form_alter(&$form, &$form_state, $vbo) {
//  $form['operations_fieldset']['#collapsible'] = TRUE;
//  $form['operations_fieldset']['#collapsed'] = TRUE;
  $form['select']['#collapsible'] = TRUE;
  $form['select']['#collapsed'] = TRUE;
}


/**
 * Overrides theme_menu_local_action().
 *
 * Overrides Bootstrap module's override. All we're doing is making the action
 * link buttons bigger by removing the 'btn-xs' class.
 *
 * @see bootstrap_menu_local_action()
 */
/*
function bootstrap_material_menu_local_action($variables) {
  $link = $variables['element']['#link'];

  $options = isset($link['localized_options']) ? $link['localized_options'] : array();

  // If the title is not HTML, sanitize it.
  if (empty($options['html'])) {
    $link['title'] = check_plain($link['title']);
  }

  $icon = _bootstrap_iconize_text($link['title']);

  // Format the action link.
  $output = '';
  if (isset($link['href'])) {
    // Turn link into a mini-button and colorize based on title.
    if ($class = _bootstrap_colorize_text($link['title'])) {
      if (!isset($options['attributes']['class'])) {
        $options['attributes']['class'] = array();
      }
      $string = is_string($options['attributes']['class']);
      if ($string) {
        $options['attributes']['class'] = explode(' ', $options['attributes']['class']);
      }
      $options['attributes']['class'][] = 'btn btn-raised';
      $options['attributes']['class'][] = 'btn-' . $class;
      if ($string) {
        $options['attributes']['class'] = implode(' ', $options['attributes']['class']);
      }
    }
    // Force HTML so we can render any icon that may have been added.
    $options['html'] = !empty($options['html']) || !empty($icon) ? TRUE : FALSE;
    $output .= l($icon . $link['title'], $link['href'], $options);
  }
  else {
    $output .= $icon . $link['title'];
  }

  return $output;
}
*/

/*
function bootstrap_material_preprocess_node(&$variables) {
  $variables['content']['links']['node']['#links']['node-readmore']['attributes']['class'][] = 'btn btn-raised btn-default';
  $variables['content']['links']['node']['#links']['comment-add']['attributes']['class'][] = 'btn btn-raised btn-default';
}

function bootstrap_material_more_link($variables) {
  return '<div class="more-link btn btn-raised btn-default">' . l(t('More'), $variables['url'], array('attributes' => array('title' => $variables['title']))) . '</div>';
}




function bootstrap_material_preprocess_page(&$vars) {

  $menu_tree = menu_tree_all_data('main-menu');
  $tree_output_prepare = menu_tree_output($menu_tree);
  $vars['primary_navigation'] = drupal_render($tree_output_prepare);
}
*/