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
        $recipes = $recipeManager->selectAllRecipe();

        return $this->twig->render('Recipe/list_recipe.html.twig', ['recipes' => $recipes]);
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
                    $filename = $upload->upload($_FILES);
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
            'Admin/addRecipe.html.twig',
            ['categories' => $categories, 'errors' => $errors, 'post' => $data]
        );
    }

    public function showRecipe(int $id)
    {
        $recipeManager = new RecipeManager();
        $recipe = $recipeManager->selectRecipesById($id);


        return $this->twig->render('Recipe/show-one-recipe.html.twig', ['recipe' => $recipe ]);
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


        return $this->twig->render('Admin/Recipe/show-one-recipe-admin.html.twig', ['recipe' => $recipe ]);
    }

    public function deleteRecipe()
    {

        $recipeManager = new RecipeManager();

        $recipeManager->delete();

        header('Location: /admin/recipeList');
    }

    public function adminListRecipe()
    {
        $recipeManager = new RecipeManager();
        $recipes = $recipeManager->selectAllRecipe();

        return $this->twig->render('Admin/Recipe/recipeList.html.twig', ['recipes' => $recipes]);
    }

    public function searchRecipe()
    {
        $recipeManager = new RecipeManager();

        if (empty(trim($_POST['recipe']))) {
            $recipes = $recipeManager->selectAllRecipe();
        }  else {
            $recipes=$recipeManager->selectRecipesLikeName(trim($_POST['recipe']));
        }

        return $this->twig->render('Recipe/inc_listRecipe.html.twig', ['recipes' => $recipes ]);
    }
}
