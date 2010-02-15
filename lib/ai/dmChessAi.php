<?php

class dmChessAi extends dmConfigurable
{
  protected
  $serviceContainer;
  
  public function __construct(dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }
  
  public function move()
  {
    return $this->serviceContainer
    ->getService('dm_chess_ai_driver_'.$this->getOption('driver'))
    ->setOption('level', $this->getOption('level'))
    ->move();
  }
  
}