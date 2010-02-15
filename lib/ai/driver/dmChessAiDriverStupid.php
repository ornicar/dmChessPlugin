<?php

class dmChessAiDriverStupid extends dmChessAiDriver
{

  public function move()
  {
    $targetKeysByPiece = $this->player->getTargetKeysByPieces();
    
    // choose random piece
    do
    {
      $pieceId = array_rand($targetKeysByPiece);
    }
    while(empty($targetKeysByPiece[$pieceId]));
    
    // choose random move
    $toSquareKey = $targetKeysByPiece[$pieceId][array_rand($targetKeysByPiece[$pieceId])];
  
    
    if (!$this->player->getPieceById($pieceId)->moveToSquareKey($toSquareKey))
    {
      throw new dmException('Illegal move: '.$move['from'].'->'.$move['to']);
    }
    
    return true;
  }
}