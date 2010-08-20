<?php

function social_button($service, $route, $options = array())
{
  $image = isset($options['button-type'])?$options['button-type']:'default';
  $image .= '.png';
  return link_to(image_tag('/sfMelodyPlugin/images/'.$service.'/'.$image, array('alt' => 'Connect with '.$service)), $route, $options);
}
