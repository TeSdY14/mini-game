<?php


class Personnage
{
    private $_id;
    private $_degats;
    private $_nom;
    private $_niveau;
    private $_experience;
    private $_pforce;

    // Constante si on se frappe soit même
    const CEST_MOI = 1;
    const PERSO_TUE = 2;
    const PERSO_FRAPPE = 3;

    public function __construct(array $data)
    {
       $this->hydrate($data);
    }

    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @param Personnage $enemy
     * @return int
     */
    public function frapper(Personnage $enemy): int
    {
        // checker que l'on tape bien un ennemi
        if ($enemy->getId() === $this->_id) {
            return self::CEST_MOI;
        }
        // ajouter du niveau au personnage attaquant
        $this->xpUp();
        // indiquer au personnage frapper ses dégâts
        return $enemy->recevoirDegats($this->_pforce);
    }

    public function recevoirDegats(int $force): int {
        // augmentation de 5 des dégâts
        $this->_degats += $force;
        // si dégâts = 100 (ou plus) le personnage est mort
        if ($this->_degats >= 100) {
            return self::PERSO_TUE;
        }
        // Sinon : on retourne la valeur signifiant que le personnage a bien été frappé
        return self::PERSO_FRAPPE;
    }

    public function xpUp() {
        $this->setExperience($this->_experience+(random_int(1,9)));
        if ($this->_experience >= 100) {
            $this->setExperience(1);
            $this->setPforce($this->_pforce+(random_int(1,5)));
            $this->setNiveau($this->getNiveau()+1);
        }
    }

    public function getId(): int
    {
        return $this->_id;
    }

    public function setId(int $id)
    {
        $this->_id = $id;
    }

    public function getDegats(): int
    {
        return $this->_degats;
    }

    public function setDegats(int $degats)
    {
        $this->_degats = $degats;
    }

    public function getNom(): string
    {
        return $this->_nom;
    }

    public function setNom(string $nom)
    {
        $this->_nom = $nom;
    }

    public function nomValid(): bool
    {

        return !empty($this->_nom);
    }

    /**
     * @return int
     */
    public function getNiveau(): int
    {
        return $this->_niveau;
    }

    /**
     * @param mixed $niveau
     */
    public function setNiveau(int $niveau)
    {
        $this->_niveau = $niveau;
    }

    /**
     * @return int
     */
    public function getExperience(): int
    {
        return $this->_experience;
    }

    /**
     * @param mixed $experience
     */
    public function setExperience(int $experience)
    {
        $this->_experience = $experience;
    }

    /**
     * @return mixed
     */
    public function getPforce(): int
    {
        return $this->_pforce;
    }

    /**
     * @param mixed $force
     */
    public function setPforce(int $force)
    {
        $this->_pforce = $force;
    }
}