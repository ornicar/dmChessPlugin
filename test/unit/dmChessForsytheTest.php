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

$forsythe = $helper->get('dm_chess_forsythe');

$t->is($notation = $forsythe->gameToForsythe($game), 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq', 'Forsythe notation is correct: '.$notation);

$t->ok($board->getPieceByPos(5, 2)->moveToPos(5, 4), 'White pawn E2.E4 accepted');

$t->is($notation = $forsythe->gameToForsythe($game), 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq', 'Forsythe notation is correct: '.$notation);

$t->ok($board->getPieceByPos(3, 7)->moveToPos(3, 5), 'Black pawn C7.C5 accepted');

$t->is($notation = $forsythe->gameToForsythe($game), 'rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq', 'Forsythe notation is correct: '.$notation);

$t->ok($board->getPieceByPos(7, 1)->moveToPos(6, 3), 'White knight G1.F3 accepted');

$t->is($notation = $forsythe->gameToForsythe($game), 'rnbqkbnr/pp1ppppp/8/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R b KQkq', 'Forsythe notation is correct: '.$notation);