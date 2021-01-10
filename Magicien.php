<?php


class Magicien extends Personnage
{

    private $_magie;

    public function lancerUnSort(Personnage $perso) {
        $perso->recevoirDegats($this->_magie);
    }

    public function xpUp() {
        parent::xpUp();

        if($this->_magie >= 100) {
            $this->_magie += 10;
        }

    }
}