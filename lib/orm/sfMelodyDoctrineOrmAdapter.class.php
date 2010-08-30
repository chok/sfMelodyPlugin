<?php
/**
 * Allow to override propel operation for melody specific usage
 *
 * @author Maxime Picaud
 * @since 29 août 2010
 */
class sfMelodyDoctrineOrmAdapter extends sfMelodyOrmAdapter
{
  public function retrieveOrCreateByMelody($melody)
  {
    $this->checkModels('sfGuardUser', 'retrieveOrCreateByMelody');

    $q = Doctrine::getTable('sfGuardUser')
         ->createQuery('u')
         ->limit(1);

    $user_factory = $melody->getUserFactory();

    $config = $user_factory->getConfig();
    $user = $user_factory->getUser();

    foreach($config as $field => $field_config)
    {
      $method = 'get'.sfInflector::camelize('_'.$field);
      if(is_callable(array($user, $method)))
      {
        $q->addWhere('u.'.$field.' = ?', $user->$method());
      }
    }



  }
}