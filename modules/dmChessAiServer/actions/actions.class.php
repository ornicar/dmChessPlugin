<?php
/**
 * Dm chess ai server actions
 */
class dmChessAiServerActions extends myFrontModuleActions
{
  
  public function executePlay(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($forsythe = $request->getParameter('forsythe')) &&
      ($level = (int)$request->getParameter('level', 3)) &&
      $level >= 1 &&
      $level <= 8 &&
      ($aiServer = $this->getServiceContainer()->getService('dm_chess_ai_server')) &&
      $aiServer->isEnabled()
    );
    
    $newForsythe = $aiServer->setOption('level', $level)->execute($forsythe);
    
    $this->getService('dispatcher')->notify(new sfEvent($this, 'dm.chess_server.play', array(
      'old_forsythe' => $forsythe,
      'new_forsythe' => $newForsythe,
      'level' => $level,
      'time' => microtime(true) - dm::getStartTime()
    )));
    
    return $this->renderText($newForsythe);
  }
}