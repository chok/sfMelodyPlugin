<?php
class sfMyspaceMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'http://api.myspace.com/request_token';
    $this->request_auth_url = 'http://api.myspace.com/authorize';
    $this->access_token_url = 'http://api.myspace.com/access_token';
  }
}