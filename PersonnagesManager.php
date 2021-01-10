<?php


class PersonnagesManager
{

    /**
     * @var PDO
     */
    private $_db;

    public function __construct(PDO $_db) {
        $this->setDb($_db);
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->_db;
    }

    /**
     * @param PDO $db
     */
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }

    public function get($info): Personnage
    {
        if (is_int($info)) {
            $q = $this->_db->query("SELECT id, nom, degats, experience, niveau, pforce FROM personnages WHERE id=" . $info);
        } else {
            $q = $this->_db->prepare("SELECT id, nom, degats, experience, niveau, pforce FROM personnages WHERE nom=:nom");
            $q->execute([':nom' => $info]);
        }

        return new Personnage($q->fetch(PDO::FETCH_ASSOC));
    }

    public function getList($nom): array
    {
        $persos = [];

        $q = $this->_db->prepare("SELECT id, nom, degats, experience, niveau, pforce FROM personnages WHERE nom <> :nom ORDER BY nom");
        $q->execute([':nom' => $nom]);

        while($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
            $persos[] = new Personnage($donnees);
        }

        return $persos;
    }

    public function add(Personnage $perso) {
        $q = $this->_db->prepare('INSERT INTO personnages(nom) VALUES(:nom)');
        $q->execute([':nom' => $perso->getNom()]);

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
            'experience' => 0,
            'niveau' => 1,
            'pforce' => 1,
        ]);
    }

    public function update(Personnage $perso) {
        $q = $this->_db->prepare("UPDATE personnages SET degats = :degats WHERE id=:id");
        $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $q->execute();
    }

    public function updateXp(Personnage $perso) {
        $q = $this->_db->prepare("UPDATE personnages SET experience = :experience WHERE id=:id");
        $q->bindValue(':experience', $perso->getExperience(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $q->execute();
    }

    public function delete(Personnage $perso) {
        $this->_db->exec('DELETE FROM personnages WHERE id = ' . $perso->getId());
    }

    public function count() {
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }

    public function exists($info): bool
    {
        if (is_int($info)) {
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id='.$info)->fetchColumn();
        }

        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom=:nom');
        $q->execute([':nom' => $info]);
        return (bool) $q->fetchColumn();
    }
}