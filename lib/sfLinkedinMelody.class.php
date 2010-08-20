<?php
class sfLinkedinMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'https://api.linkedin.com/uas/oauth/requestToken';
    $this->request_auth_url = 'https://www.linkedin.com/uas/oauth/authorize';
    $this->access_token_url = 'https://api.linkedin.com/uas/oauth/accessToken';
  }
}