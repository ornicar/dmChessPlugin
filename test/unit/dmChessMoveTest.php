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

$t->ok($player1->Pawns[0]->moveToPos(1, 3), 'Move accepted');

$t->is($board->getPieceByPos(1, 2), null, 'No more pawn on A2');

$t->is($board->getPieceByPos(1, 3), $player1->Pawns[0], 'The pawn is on A3');

$t->ok($player1->Pawns[0]->moveToPos(1, 4), 'Move accepted');

$t->is($board->getPieceByPos(1, 2), null, 'No more pawn on A2');
$t->is($board->getPieceByPos(1, 3), null, 'No more pawn on A3');

$t->is($board->getPieceByPos(1, 4), $player1->Pawns[0], 'The pawn is on A4');

$t->ok(!$player1->Pawns[0]->moveToPos(1, 6), 'Move refused');

$t->ok($player1->Knights[0]->moveToPos(1, 3), 'Move accepted');

$t->ok(!$player1->Knights[0]->moveToPos(3, 2), 'Move refused');

$t->ok($player1->Knights[0]->moveToPos(2, 5), 'Move accepted');

$t->ok($player1->Knights[0]->moveToPos(3, 7), 'Move accepted');

$t->is($board->getPieceByPos(3, 7), $player1->Knights[0], 'Knight is on C7');

$t->ok($player2->Pawns[2]->isDead, 'Player2 Pawn is dead');