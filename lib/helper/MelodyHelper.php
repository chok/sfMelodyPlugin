<?php

function _get_social_button_templates($service)
{
  $social_button_templates = array('messenger' => array('content' => '<span id="messenger-bg"></span><span>Connect</span>', 'type' => 'text'));

  return isset($social_button_templates[$service])?$social_button_templates[$service]:array();
}



function social_button($service, $route, $options = array())
{
  $default_options = array(
                           'path'         => '/sfMelodyPlugin/images/'.$service.'/',
                           'image'				=> 'default',
                           'type'					=> 'image',
                           'class'				=> $service.'-button',
                           'stylesheet'		=> '/sfMelodyPlugin/css/melody.css',
                           'content'			=> ''
                          );

  $options = array_merge($default_options, _get_social_button_templates($service), $options);

  if ($options['stylesheet'])
  {
    use_stylesheet($options['stylesheet']);
  }

  $content = null;

  switch ($options['type'])
  {
    case 'text':
      $content = $options['content'];
      break;
    default:
    case 'image':
      $content = image_tag($options['path'].$options['image'].'.png', array('alt' => 'Connect with '.$service));
      break;
  }

  unset($options['path'], $options['image'], $options['type'], $options['stylesheet'], $options['content']);

  return link_to($content, $route, $options);
}
