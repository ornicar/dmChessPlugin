<?php

class dmChessAssetLoader
{
  protected
  $response;
  
  public function __construct(dmWebResponse $response)
  {
    $this->response = $response;
  }
  
  public function execute()
  {
    foreach($this->getJavascripts() as $javascript)
    {
      $this->response->addJavascript($javascript);
    }
  
    foreach($this->getStylesheets() as $stylesheet)
    {
      $this->response->addStylesheet($stylesheet);
    }
  }
  
  protected function getJavascripts()
  {
    return array(
      'lib.jquery',
      'lib.metadata',
      'lib.ui-core',
      'lib.ui-widget',
      'lib.ui-mouse',
      'lib.ui-position',
      'lib.ui-draggable',
      'lib.ui-droppable',
      'dmChessPlugin.ctrl',
      'dmChessPlugin.game'
    );
  }
  
  protected function getStylesheets()
  {
    return array(
      'dmChessPlugin.board',
      'dmChessPlugin.table'
    );
  }
}