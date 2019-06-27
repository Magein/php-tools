<?php

namespace magein\php_tools\object;

class QueryResult
{
    /**
     * @var array
     */
    private $list = [];

    /**
     * @var array
     */
    private $page = [];

    /**
     * QueryResult constructor.
     * @param array $list
     * @param array $page
     */
    public function __construct($list = [], $page = [])
    {
        $this->setList($list ?: []);
        $this->setPage($page ?: []);
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList(array $list): void
    {
        $this->list = $list;
    }

    /**
     * @return array
     */
    public function getPage(): array
    {
        return $this->page;
    }

    /**
     * @param array $page
     */
    public function setPage(array $page): void
    {
        $this->page = $page;
    }
}