<?php
class BasesfMelodyActions extends sfActions
{
  protected function getOrmAdapter($model)
  {
    return sfMelodyOrmAdapter::getInstance($model);
  }
}