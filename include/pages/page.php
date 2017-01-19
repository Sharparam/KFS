<?php
use KFS\Alerts;
use KFS\Page;

requireAdmin();

function setFromPost(&$page) {
  $page->setName($_POST['name']);
  $page->setTitle($_POST['title']);
  $page->setContent($_POST['content']);
  $page->setSort($_POST['sort']);
  $page->setEnabled(isset($_POST['enabled']) && $_POST['enabled'] === 'Y');
}

if (!isset($_POST['update']) && !isset($_POST['delete']) && isset($_GET['id'])) {
  $page = Page::findById($_GET['id']);
} elseif (isset($_POST['delete']) && isset($_POST['id'])) {
  $page = Page::findById($_POST['id']);
  if ($page !== NULL && $page->delete()) {
    header('Location: /?p=admin');
    exit();
  } else {
    Alerts::addError('Failed to delete page!');
  }
} elseif (isset($_POST['update']) && isset($_POST['id'])) {
  $page = Page::findById($_POST['id']);

  if ($page === NULL) {
    Alerts::addError('Unable to find page for editing!');
  } else {
    setFromPost($page);

    if ($page->validate() && $page->save()) {
      header('Location: /?p=admin');
      exit();
    }
  }
} elseif (isset($_POST['create'])) {
  $page = new Page();
  setFromPost($page);

  if ($page->validate() && $page->save()) {
    header('Location: /?p=admin');
    exit();
  }
} else {
  $page = new Page();
}

if (Alerts::hasAlerts())
  Alerts::printAll();

$page->printForm();
