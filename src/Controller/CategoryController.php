<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace Controller;

use Model\CategoryManager;

/**
 * Class ItemController
 *
 */
class CategoryController extends AbstractController
{

    /**
     * Display item listing
     *
     * @return string
     */
    public function dishCat()
    {
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll();

        return $this->twig->render('admin/category.html.twig', ['categories' => $categories]);
    }
}
