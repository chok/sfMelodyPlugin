<?php
class sfFriendfeedMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'https://friendfeed.com/account/oauth/request_token';
    $this->request_auth_url = 'https://friendfeed.com/account/oauth/authorize';
    $this->access_token_url = 'https://friendfeed.com/account/oauth/access_token';
  }
}