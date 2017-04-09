<?php
namespace KFS;

use \PDO;
use \PDOException;

class Page implements \JsonSerializable {
  private static $names;

  private $id;
  private $name;
  private $title;
  private $content;
  private $sort;
  private $enabled;

  public static function getAll() {
    $query = 'SELECT id, name, title, content, sort, enabled FROM pages ORDER BY sort ASC;';
    $stmt = Database::getInstance()->query($query);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Page');
  }

  public static function getAllEnabled() {
    $query = "SELECT id, name, title, content, sort, enabled FROM pages WHERE enabled='Y' ORDER BY sort ASC;";
    $stmt = Database::getInstance()->query($query);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Page');
  }

  public static function isPage($name) {
    if ($names === NULL) {
      $query = "SELECT name FROM pages;";
      $stmt = Database::getInstance()->query($query);
      self::$names = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    return in_array($name, self::$names);
  }

  public static function findById($id) {
    $query = 'SELECT id, name, title, content, sort, enabled FROM pages WHERE id = :id;';
    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('KFS\\Page');
  }

  public static function findByName($name) {
    $query = 'SELECT id, name, title, content, sort, enabled FROM pages WHERE name = :name;';
    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':name', $name);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('KFS\\Page');
  }

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getContent() {
    return $this->content;
  }

  public function getSort() {
    return $this->sort;
  }

  public function getEnabled() {
    return $this->enabled;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function setContent($content) {
    $this->content = $content;
  }

  public function setSort($sort) {
    $this->sort = $sort;
  }

  public function setEnabled($enabled) {
    $this->enabled = $enabled ? 'Y' : 'N';
  }

  public function isEnabled() {
    return $this->enabled == 'Y';
  }

  public function render() {
    $content = str_replace('{{season}}', Data::getSeasonText(), $this->content);
    echo Markdown::parse($content);
  }

  public function validate() {
    if (empty($this->getName()))
      Alerts::addError('Name cannot be empty!');

    if (empty($this->getTitle()))
      Alerts::addError('Title cannot be empty!');

    if (empty($this->getContent()))
      Alerts::addError('Content cannot be empty!');

    if (!is_numeric($this->getSort()))
      Alerts::addError('Invalid sort value!');

    if (Alerts::hasAlerts())
      return false;

    $query = 'SELECT id FROM pages WHERE name=:name AND id <> :id;';

    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':name', $this->getName());
    $stmt->bindValue(':id', $this->getId());
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      Alerts::addError('A page with that name already exists!');
      return false;
    }

    return true;
  }

  public function save() {
    if ($this->getId() === NULL)
      return $this->insert();

    $query = 'UPDATE pages'
      . ' SET name=:name, title=:title, content=:content, sort=:sort, enabled=:enabled'
      . ' WHERE id=:id;';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':name', $this->getName());
      $stmt->bindValue(':title', $this->getTitle());
      $stmt->bindValue(':content', $this->getContent());
      $stmt->bindValue(':sort', $this->getSort());
      $stmt->bindValue(':enabled', $this->getEnabled());
      $stmt->bindValue(':id', $this->getId());
      $stmt->execute();
      $db->commit();
      return true;
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to update page: {$e->getMessage()}");
      return false;
    }
  }

  public function delete() {
    if ($this->getId() === NULL)
      return true;

    try {
      $query = 'DELETE FROM pages WHERE id = :id;';
      $stmt = Database::getInstance()->prepare($query);
      $stmt->bindValue(':id', $this->getId());
      $stmt->execute();

      return $stmt->rowCount() === 1;
    } catch (PDOException $e) {
      Alerts::addError("Failed to delete page: {$e->getMessage()}");
      return false;
    }
  }

  public function printForm($action = NULL) {
    if ($action === NULL)
      $action = $_SERVER['REQUEST_URI'];
    ?>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-horizontal page-form">
      <?php if ($this->getId() !== NULL): ?>
      <input type="hidden" id="id" name="id" value="<?= $this->getId() ?>">
      <?php endif; ?>

      <?php
      static::printTextField('name', 'Name', $this->getName());
      static::printTextField('title', 'Title', $this->getTitle());
      static::printTextField('sort', 'Sort', $this->getSort());
      ?>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="enabled" value="Y" <?php if ($this->isEnabled()) echo 'checked'; ?>>
              Enabled
            </label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="page-content" class="col-sm-2 control-label">Content</label>
        <div class="col-sm-10">
          <textarea name="content" id="page-content" wrap="soft" class="form-control" placeholder="Content" rows="15"><?= $this->getContent() ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="submit" class="col-sm-2 control-label">Done?</label>
        <div class="col-sm-10">
          <button type="submit" name="<?= $this->getId() === NULL ? 'create' : 'update' ?>" class="form-control btn btn-primary">
            Save
          </button>
        </div>
      </div>
    </form>
    <?php
  }

  public function jsonSerialize() {
    return array(
      'id' => (int)$this->getId(),
      'name' => $this->getName(),
      'title' => $this->getTitle(),
      'content' => $this->getContent(),
      'sort' => (int)$this->getSort(),
      'enabled' => $this->isEnabled()
    );
  }

  private function insert() {
    $query = 'INSERT INTO pages(name, title, content, sort, enabled)'
      . 'VALUES(:name, :title, :content, :sort, :enabled);';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':name', $this->getName());
      $stmt->bindValue(':title', $this->getTitle());
      $stmt->bindValue(':content', $this->getContent());
      $stmt->bindValue(':sort', $this->getSort());
      $stmt->bindValue(':enabled', $this->getEnabled());
      $stmt->execute();
      $this->id = $db->lastInsertId();
      $db->commit();
      return true;
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to insert new page: {$e->getMessage()}");
      return false;
    }
  }

  private static function printTextField($name, $text, $value) {
    ?>
    <div class="form-group">
      <label for="<?= $name ?>" class="col-sm-2 control-label"><?= $text ?></label>

      <div class="col-sm-10">
        <input type="text" name="<?= $name ?>" id="<?= $name ?>" placeholder="<?= $text ?>" class="form-control" value="<?= $value ?>">
      </div>
    </div>
    <?php
  }
}
