<?php

class dmChessAiServer extends dmConfigurable
{
  protected
  $serviceContainer;
  
  public function __construct(dmBaseServiceContainer $serviceContainer, array $options)
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }
  
  public function isEnabled()
  {
    return $this->getOption('enabled');
  }
  
  public function execute($forsythe)
  {
    return $this->getCrafty()->execute($forsythe);
  }
  
  protected function getCrafty()
  {
    $crafty = $this->serviceContainer->getService('dm_chess_crafty');
    $crafty->setOption('level', $this->getOption('level'));
    
    return $crafty;
  }
}