<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$helper->setLimeTest($t = new lime_test());

$t->comment('Create a new game');

$timer = dmDebug::timer('dm_chess_check_test');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();
$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$king = $player1->King;

$t->is($board->getPieceByHumanPos('e1'), $king, 'King is on e1');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$helper
->move($game, 'e7', 'e6')
->move($game, 'd8', 'h4');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$helper
->cannotMove($game, 'f2', 'f3', 'king protection');

$helper
->move($game, 'h4', 'd4')
->move($game, 'f2', 'f3');

$helper
->cannotMove($game, 'e1', 'f2', 'king suicide');

$helper
->move($game, 'd2', 'd3')
->move($game, 'c1', 'e3')
->move($game, 'e1', 'f2');

$helper
->cannotMove($game, 'e3', 'f4', 'king protection');

$t->comment('Kill ennemy queen');

$helper
->move($game, 'e3', 'd4');

$t->comment('Attack with bishop');

$helper
->move($game, 'f8', 'c5')
->move($game, 'c5', 'd4');

$t->ok($king->isAttacked(), 'King is attacked');
$t->ok(!$player1->isMate(), 'Player 1 is not mate');

$helper
->move($game, 'e2', 'e3', 'Protect with pawn');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$helper
->move($game, 'd4', 'e3', 'Kill pawn');

$t->ok($king->isAttacked(), 'King is attacked');
$t->ok(!$player1->isMate(), 'Player 1 is not mate');

$helper
->move($game, 'f2', 'e3', 'Kill bishop with king');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$t->comment('Attack with knight');

$helper
->move($game, 'f3', 'f4')
->move($game, 'g8', 'f6')
->move($game, 'f6', 'g4');

$t->ok($king->isAttacked(), 'King is attacked');
$t->ok(!$player1->isMate(), 'Player 1 is not mate');

$helper
->cannotMove($game, 'g1', 'f3', 'this move does not protect the king');

$helper
->move($game, 'e3', 'f3');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$helper
->move($game, 'g4', 'h2');

$t->ok($king->isAttacked(), 'King is attacked');
$t->ok(!$player1->isMate(), 'Player 1 is not mate');

$helper
->cannotMove($game, 'f3', 'g4', 'the king is still under attack');

$helper
->move($game, 'h1', 'h2');

$t->ok(!$king->isAttacked(), 'King is not attacked');

$t->comment('Total time: '.$timer->getElapsedTime());