<?php
/**
 * Created by PhpStorm.
 * User: wilder10
 * Date: 29/03/18
 * Time: 17:14
 */

namespace Controller;

use Model\Recipe;
use Model\RecipeManager;
use Model\CategoryManager;
use Model\UploadManager;
use Model\EventRecipeManager;

/**
 * Class RecipeController
 *
 */
class RecipeController extends AbstractController
{
    /**
     * Display item listing
     *
     * @return string
     */
    public function listRecipe()
    {
        $recipeManager = new RecipeManager();
        $recipes = $recipeManager->selectRecipesLimit();
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        return $this->twig->render(
            'Recipe/list_recipe.html.twig',
            [
                'recipes' => $recipes,
                'categories' => $categories
            ]
        );
    }


    /**
     * Ajouter une recette !
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addRecipe()
    {
        $data = $_POST;
        $errors = null;
        if (!empty($_POST)) {
            try {
                if (empty(trim($_POST['comment']))) {
                    throw new \Exception('Merci d\'ajouter un commentaire!');
                }
                if (empty(trim($_POST['name']))) {
                    throw new \Exception('Le champ nom ne doit pas etre vide !');
                }
                if (isset($_FILES)) {
                    $upload = new UploadManager();
                    $filename = $upload->upload($_FILES['img']);
                    $data['img'] = $filename;
                }
                $recipeManager = new RecipeManager();
                $recipeManager->insert($data);
                header('Location:/admin/recipeList');
                exit();
            } catch (\Exception $e) {
                $errors = $e->getMessage();
            }
        }
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();
        return $this->twig->render(
            'Admin/Recipe/addRecipe.html.twig',
            ['categories' => $categories, 'errors' => $errors, 'post' => $data]
        );
    }

    public function showRecipe(int $id)
    {
        $recipeManager = new RecipeManager();
        $recipe = $recipeManager->selectRecipesById($id);
        $showEvents = $recipeManager->showLinkedEvent($id);

        return $this->twig->render('Recipe/show-one-recipe.html.twig', ['recipe' => $recipe, "showEvents" => $showEvents]);
    }


    /**
     * @param int $id
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showAdminRecipe(int $id)
    {
        $recipeManager = new RecipeManager();
        $recipe = $recipeManager->selectRecipesById($id);
        $showEvents = $recipeManager->showLinkedEvent($id);

        return $this->twig->render('Admin/Recipe/show-one-recipe-admin.html.twig', ['recipe' => $recipe, "showEvents" => $showEvents]);
    }

    public function deleteRecipe()
    {
        $recipeManager = new RecipeManager();
        if (!empty($_POST)) {
            $id = $_POST['id'];
        }

        $recipeManager->delete($id);

        header('Location: /admin/recipeList');
    }


    public function adminListRecipe()
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        return $this->twig->render('Admin/Recipe/recipeList.html.twig', ['categories' => $categories]);
    }

    public function searchRecipe()
    {
        $recipeManager = new RecipeManager();
        $recipes = $recipeManager->selectRecipesLimit(trim($_POST['name']), $_POST['categoryId'], $_POST['page'], THUMB_LIMIT);

        return $this->twig->render('Recipe/inc_listRecipe.html.twig', ['recipes' => $recipes]);
    }

    public function searchRecipeAdmin()
    {
        $recipeManager = new RecipeManager();
        $recipes = $recipeManager->selectRecipesLimit(trim($_POST['name']), $_POST['categoryId'], $_POST['page'], MEDIA_LIMIT);

        return $this->twig->render('Admin/Recipe/search_recipeList.html.twig', ['recipes' => $recipes]);
    }


    public function setNote($recipeId, $note)
    {
        if (!empty($recipeId) && !empty($note)) {
            $recipeId = trim($recipeId);
            $note = trim($note);

            $recipeManager = new recipeManager();
            $recipeManager->updateNote($recipeId, $note);
            header('Location: /recipe/' . $recipeId);
        }
    }

    public function updateRecipe(int $id)
    {
        $data = $_POST;
        $errors = null;
        $recipeManager = new RecipeManager();
        try {
            if (!empty($_POST)) {
                if (empty(trim($_POST['comment']))) {
                    throw new \Exception('Merci d\'ajouter un commentaire!');
                }
                if (empty(trim($_POST['name']))) {
                    throw new \Exception('Le champ nom ne doit pas etre vide !');
                }

                if (!empty($_FILES['filename']['name'])) {

                    $recipe = $recipeManager->selectRecipesById($id);
                    $imageName = $recipe->getImg();

                    // upload du fichier
                    $upload = new UploadManager();
                    $filename = $upload->upload($_FILES['filename']);
                    $data['img'] = $filename;
                    if (!empty($imageName)) {
                        $upload->unlink($imageName);
                    }

                }
                // update de tous les champs
                $recipeManager->update($id, $data);
                header('Location:/admin/recipeList');
            }
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll();
            $recipe = $recipeManager->selectRecipesById($id);

        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }
        return $this->twig->render('Admin/Recipe/updateRecipe.html.twig', ['categories' => $categories, 'data' => $recipe, 'errors' => $errors]);
    }

    public function unlinkEventFromRecipe()
    {
        $eventRecipeManager = new EventRecipeManager();
        if (!empty($_POST['recipeId']) && !empty($_POST['eventId'])) {
            $eventRecipeManager->unlink($_POST['eventId'], $_POST['recipeId']);
        }

        header('Location: /admin/recipe/' . $_POST['recipeId']);
    }

}
