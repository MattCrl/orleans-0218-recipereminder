<?php
/**
 * Created by PhpStorm.
 * User: wilder17
 * Date: 03/04/18
 * Time: 14:24
 */

namespace Controller;

use Model\Event;
use Model\EventManager;
use Model\CategoryManager;
use Model\UploadManager;

class EventController extends AbstractController
{

    /**
     * Display event listing
     *
     * @return string
     */
    public function listEvent()
    {
        $eventManager = new EventManager();
        $events = $eventManager->selectAll();

        return $this->twig->render('Event/eventslist.html.twig', ['events' => $events]);
    }
    public function addEvent()
    {
        $errors = null;
        if (!empty($_POST)) {
            try {
                if (empty($_POST['comment'])) {
                    throw new \Exception('Merci d\'ajouter un commentaire!');
                }
                if (empty($_POST['name'])) {
                    throw new \Exception('Le champ nom ne doit pas etre vide !');
                }
                $data = $_POST;
var_dump($_FILES);
                if (!empty ($_FILES)){
                $upload = new UploadManager();
                $filename = $upload->upload($_FILES);
                $data['img'] = $filename;
                }

                $EventManager = new EventManager();
                $EventManager->insert($data);
              //  header('Location:Admin/recipe.html.twig');
            } catch (\Exception $e) {
                $errors = $e->getMessage();
            }
        }
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();
        return $this->twig->render('Admin/addEvent.html.twig', ['categories' => $categories, 'errors' => $errors, 'post' => $_POST]);
    }
}
