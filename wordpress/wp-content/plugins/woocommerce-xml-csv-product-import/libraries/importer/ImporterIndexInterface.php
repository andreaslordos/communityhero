<?php
/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/14/17
 * Time: 5:37 PM
 */

Interface ImporterIndexInterface{

    /**
     * @return mixed
     */
    public function getPid();

    /**
     * @return mixed
     */
    public function getIndex();

    /**
     * @return mixed
     */
    public function getArticle();

}