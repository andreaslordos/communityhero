<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use ImporterIndexInterface;

require_once dirname(__FILE__) . '/ImporterIndexInterface.php';

class ImporterIndex implements ImporterIndexInterface {

    /**
     * @var int
     */
    public $pid;

    /**
     * @var int
     */
    public $index;

    /**
     * @var array
     */
    public $article;

    /**
     * ImporterIndex constructor.
     *
     * @param int $pid
     * @param int $index
     * @param array $article
     */
    public function __construct($pid, $index, $article) {
        $this->pid = $pid;
        $this->index = $index;
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * @return mixed
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * @return mixed
     */
    public function getArticle() {
        return $this->article;
    }

}