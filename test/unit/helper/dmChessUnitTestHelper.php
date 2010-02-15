<?php

require_once(getcwd() .'/config/ProjectConfiguration.class.php');

require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

class dmChessUnitTestHelper extends dmUnitTestHelper
{
  protected
  $limeTest;
  
  public function setLimeTest(lime_test $t)
  {
    $this->limeTest = $t;
  }
  
  public function move(DmChessGame $game, $from, $to, $message = '')
  {
    $piece = $game->Board->getSquareByKey($game->Board->humanPosToKey($from))->getPiece();
    if($this->limeTest)
    {
      $this->limeTest->ok($game->movePieceByHumanPos($from, $to), $piece->type.' '.$from.'->'.$to.' '.$message);
    }
    return $this;
  }
  
  public function cannotMove(DmChessGame $game, $from, $to, $message = '')
  {
    $piece = $game->Board->getSquareByKey($game->Board->humanPosToKey($from))->getPiece();
    if($this->limeTest)
    {
      $this->limeTest->ok(!$game->movePieceByHumanPos($from, $to), $piece->type.' cannot '.$from.'->'.$to.' '.$message);
    }
    return $this;
  }
  
  public function checkTargetKeys(DmChessPlayer $player, array $expectedTargets, lime_test $t)
  {
    $board = $player->Game->Board;
    foreach($player->getPieces() as $piece)
    {
      $piecePos = $board->keyToHumanPos($piece->squareKey);
      
      $string = dmArray::get($expectedTargets, $piecePos, '');
      $targets1 = empty($string) ? array() : explode(' ', dmArray::get($expectedTargets, $piecePos, ''));
      $targets2 = $board->keyToHumanPos($piece->getTargetKeys());
      sort($targets1);
      sort($targets2);
      
      $t->is($targets2, $targets1, sprintf('%s targets: %s ( %s )', $piece, implode(' ', $targets2), implode(' ', $targets1)));
    }
  }
  
  public function checkPieceSquareInteraction(DmChessGame $game, lime_test $t)
  {
    $t->comment('Test piece/square interaction');
    $board = $game->getBoard();

    $ok = true;
    foreach($game->getPieces() as $piece)
    {
      if(!$piece->get('is_dead'))
      {
        $ok &= ($piece->getSquare()->getPiece() === $piece);
        
        $ok &= ($board->getSquareByKey($piece->getSquareKey())->getPiece() === $piece);
      }
    }
    foreach($board->getSquares() as $square)
    {
      if ($square->getPiece())
      {
        $ok &= ($square->getPiece()->getSquare() === $square);
      }
    }
    
    if($ok)
    {
      $t->pass('Test piece/square interaction OK');
    }
    else
    {
      $t->fail('Test piece/square interaction FAILED');
    }
  }
  
}