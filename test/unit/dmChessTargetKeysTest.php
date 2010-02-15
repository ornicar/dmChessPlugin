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

$helper->checkPieceSquareInteraction($game, $t);

$helper->checkTargetKeys($player1, array(
  'a2' => 'a3 a4',
  'b2' => 'b3 b4',
  'c2' => 'c3 c4',
  'd2' => 'd3 d4',
  'e2' => 'e3 e4',
  'f2' => 'f3 f4',
  'g2' => 'g3 g4',
  'h2' => 'h3 h4',
  'b1' => 'a3 c3',
  'g1' => 'f3 h3'
), $t);