<?php

function social_button($service, $route, $options = array())
{
  use_stylesheet('/sfMelodyPlugin/css/button.css');
  
  $options = array_merge(array('button-type' => 'default', 'type' => 'image', 'class' => $service.'-button'), $options);

  $content = null;

  switch ($options['type'])
  {
    case 'text':
      $content = $options['content'];
      break;
    default:
    case 'image':
      $image = $options['button-type'].'.png';
      $content = image_tag('/sfMelodyPlugin/images/'.$service.'/'.$image, array('alt' => 'Connect with '.$service));
      break;
  }

  unset($options['button-type'], $options['type'], $options['image']);
  return link_to($content, $route, $options);
}
