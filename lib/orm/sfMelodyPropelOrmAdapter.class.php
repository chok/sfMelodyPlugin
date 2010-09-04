<?php
/**
 * Allow to override propel operation for melody specific usage
 *
 * @author Maxime Picaud
 * @since 29 août 2010
 */
class sfMelodyPropelOrmAdapter extends sfMelodyOrmAdapter
{
  public function findByMelody($melody)
  {
    $this->checkModels('sfGuardUser', 'findByMelody');

    $c = new Criteria();

    $user_factory = $melody->getUserFactory();
    $config = $user_factory->getConfig();
    $user = $user_factory->getUser();
    $keys = $user_factory->getKeys();

    foreach($keys as $key)
    {
      $constant_key = strtoupper($key);
      $method = 'get'.sfInflector::classify($key);

      $reflection = new ReflectionClass('sfGuardUserPeer');

      if($reflection->hasConstant($constant_key) && is_callable(array($user, $method)))
      {
        $constant = $reflection->getConstants($constant_key);
        $c->add($constant, $user->$method());
      }
      else
      {
        throw new sfException(sprintf('sfGuardUser doesn\'t have field "%s"', $key));
      }
    }

    return sfGuardUserPeer::doSelectOne($c);
  }
}