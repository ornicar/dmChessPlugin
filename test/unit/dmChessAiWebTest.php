<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();

$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$sc = $helper->get('service_container');
$sc->mergeParameter('dm_chess_ai.options', array('driver' => 'web', 'level' => 1));

$forsythe = $helper->get('dm_chess_forsythe');

for($it=0; $it<10; $it++)
{
  $player = $game->getCurrentPlayer();
  $board->compile();
  
  $sc->setParameter('dm_chess.player', $player);
  $helper->get('dm_chess_ai')->move();
  
  $t->ok(!$player->isMyTurn(), 'Player '.$player.' has played');
  
  $board->compile();
  $helper->checkPieceSquareInteraction($game, $t);
  
  $t->comment($player.' move: '.$player->getStringEvents());
  
  $t->comment('Forsythe notation: '.$forsythe->gameToForsythe($game));
}