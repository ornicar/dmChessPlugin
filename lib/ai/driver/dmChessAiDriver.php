<?php

abstract class dmChessAiDriver extends dmConfigurable
{
  protected
  $player;
  
  public function __construct(DmChessPlayer $player)
  {
    $this->player = $player;
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }
  
  abstract public function move();
  
}