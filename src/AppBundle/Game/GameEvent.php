<?php
/**
 * Created by PhpStorm.
 * User: sdetrev
 * Date: 11/06/2015
 * Time: 11:37
 */

namespace AppBundle\Game;

use Composer\EventDispatcher\Event;

class GameEvent extends Event {

    private $game;

    public function __construct(Game $game) {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }


}