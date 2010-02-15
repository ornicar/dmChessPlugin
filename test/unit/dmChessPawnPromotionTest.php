<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$helper->setLimeTest($t = new lime_test());

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();
$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$t->ok(!$player1->isMate(), 'Player 1 is not mate');
$t->ok(!$player2->isMate(), 'Player 2 is not mate');

$pawn = $board->getPieceByHumanPos('h2');

$helper
->move($game, 'h2', 'h4');

$board->getPieceByHumanPos('h8')->kill();
$board->getPieceByHumanPos('h7')->kill();

$helper
->move($game, 'h4', 'h5')
->move($game, 'h5', 'h6')
->move($game, 'h6', 'h7')
->move($game, 'h7', 'h8');

$t->ok(!$pawn->exists(), 'The pawn does not exist anymore');

$queen = $board->getPieceByHumanPos('h8');

$t->ok($queen->exists(), 'The piece in h8 exists');
$t->is($queen->type, 'queen', 'The piece is of type queen');
$t->ok($queen instanceof DmChessQueen, 'The piece is an instance of DmChessQueen');
$t->is(count($player1->Queens), 2, 'Player 1 has two queens');

$events = array(
  array(
    'action'  => 'piece_move',
    'from'    => $board->humanPosToKey('h7'),
    'to'      => $board->humanPosToKey('h8')
  ),
  array(
    'action'    => 'pawn_promotion',
    'type'      => 'queen',
    'old_piece' => $pawn->id,
    'new_piece' => $queen->id,
    'square'    => $board->humanPosToKey('h8')
  )
);

$playerEvents = $player1->getEvents();
unset($playerEvents[0]['piece']);

$t->is_deeply($playerEvents, $events, 'The move event has been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'h7 h8', 'The string event is '.$stringEvents);

$helper->move($game, 'h8', 'h2');

$t->is($notation = $helper->get('dm_chess_forsythe')->gameToForsythe($game), 'rnbqkbn1/ppppppp1/8/8/8/8/PPPPPPPQ/RNBQKBNR w KQq', 'Forsythe notation is correct: '.$notation);