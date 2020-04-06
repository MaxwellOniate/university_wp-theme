<?php

function university_custom_rest()
{
  register_rest_field('post', 'authorName', [
    'get_callback' => function () {
      return get_the_author();
    }
  ]);
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL)
{
  if (!$args['title']) {
    $args['title'] = get_the_title();
  }
  if (!$args['subtitle']) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }
  if (!$args['img']) {
    if (get_field('page_banner_background_image')) {
      $args['img'] = get_field('page_banner_background_image')['sizes']['page-banner'];
    } else {
      $args['img'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }


?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['img']; ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div>
    </div>
  </div>
<?php
}

function university_files()
{
  wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=AIzaSyAoGlSJuWN6Fc2BJOfrIP92DWU18ybsUEo', NULL, '1.0', true);
  wp_enqueue_script('main-js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, '1.0', true);
  wp_enqueue_style('google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_stylesheet_uri());
  wp_localize_script('main-js', 'universityData', [
    'root_url' => get_site_url()
  ]);
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('professor-landscape', 400, 260, true);
  add_image_size('professor-portrait', 480, 650, true);
  add_image_size('page-banner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query)
{
  if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'ASC');
    $query->set('meta_query', [
      'key' => 'event_date',
      'compare' => '>=',
      'value' => date('Ymd'),
      'type' => 'numeric'
    ]);
  }
  if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
    $query->set('posts_per_page', -1);
  }
  if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
    $query->set('posts_per_page', -1);
  }
}

add_action('pre_get_posts', 'university_adjust_queries');

function university_api_key($api)
{
  $api['key'] = 'AIzaSyAoGlSJuWN6Fc2BJOfrIP92DWU18ybsUEo';
  return $api;
}

add_filter('acf/fields/google_map/api', 'university_api_key');
