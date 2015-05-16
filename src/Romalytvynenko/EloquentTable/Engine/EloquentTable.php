<?php
namespace Romalytvynenko\EloquentTable\Engine;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class EloquentTable
{

    private $model;

    private $data;

    private $hooks;

    /*
     * Available sizes per page
     */
    public $sizes = [10,25,50,100];

    /**
     * Default configs
     * @var array
     */
    private $configs = [
        'orderBy' => 'id',
        'order'   => 'desc',
        'itemsPerPage' => 10,
        'sortable' => [
            'id'
        ]
    ];

    /**
     * @param $model
     * @param $args
     */
    public function __construct($model, $args = [])
    {
        $this->model = $model;

        // Fetch configs
        $this->makeConfigs($args);

        // Prepare data (ordering, paging, etc)
        $this->prepareData();
    }

    /**
     * Get current page
     * @return int
     */
    public function getPage()
    {
        return \Input::has('page')? intval(\Input::get('page')) : 0;
    }

    /**
     * Get number of items per page
     * @return int
     */
    public function getItemsPerPage()
    {
        return \Input::has('itemsPerPage')? intval(\Input::get('itemsPerPage')) : $this->getConfig('itemsPerPage');
    }

    /**
     * Get current active search keyword
     * @return string|null
     */
    public function getSearchKeyword()
    {
        return \Input::has('search')? \Input::get('search') : null;
    }

    /**
     * Get order direction
     * @return mixed|null
     */
    public function getOrderDirection()
    {
        return \Input::has('order')? \Input::get('order') : $this->getConfig('order');
    }

    /**
     * Get order columns
     * @return mixed|null
     */
    public function getOrderBy()
    {
        return \Input::has('column')? \Input::get('column') : $this->getConfig('orderBy');
    }

    public function prepareData()
    {
        $column = $this->getOrderBy();
        $order = $this->getOrderDirection();
        $items = $this->getItemsPerPage();

        /**
         * @var $class \Eloquent
         */
        $class = $this->model;
        $query = $this->preGet($class::getQuery());

        $this->configs['allItemsCount'] = $this->processSearch($query, 'count');

        $this->data = $this->processSearch($query->orderBy($column, $order)
            ->skip($this->getPage() * $items)
            ->take($items)
        );
    }

    /**
     * @param $query \Illuminate\Database\Query\Builder
     * @return mixed
     */
    private function preGet($query) {
        $closure = $this->getConfig('preGet');

        if(is_callable($closure)) {
            $closure = $this->getConfig('preGet');
            $query = call_user_func($closure, $query);
        }

        return $query;
    }

    /**
     * @param $query
     * @param string $action
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     * @return mixed
     */
    public function processSearch($query, $action = 'get')
    {
        $allowed = ['get', 'count'];

        if(!in_array($action, $allowed)) throw new MethodNotAllowedHttpException($allowed);

        if($this->getSearchKeyword() && count($this->getConfig('searchable'))) {
            $query = $query->where(function($query) {
                foreach($this->getConfig('searchable') as $field) {
                    $query = $query->orWhere($field, 'LIKE', '%' . $this->getSearchKeyword() . '%');
                }
            });
        }

        return $query->$action();
    }

    public function makeConfigs($args)
    {
        $this->configs = array_merge($this->configs, $args);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getConfig($key)
    {
        return array_key_exists($key, $this->configs)? $this->configs[$key] : null;
    }

    /**
     * Links closure to column
     * @param $column
     * @param $closure
     */
    public function columnOutput($column, $closure)
    {
        $this->hooks[$column] = $closure;
    }

    /**
     * Output item value using hooks
     * @param $item
     * @param $key
     * @return mixed
     */
    public function outputValue($item, $key)
    {
        if(is_array($this->hooks) && array_key_exists($key, $this->hooks)) {
            return $this->hooks[$key]($item);
        }
        return $item->$key;
    }

    /**
     * Render table
     */
    public function show($tableView = 'romalytvynenko/eloquent-table::table')
    {
        echo \View::make($tableView)
            ->withData($this->data)
            ->withConfigs($this->configs)
            ->withTable($this)
            ->render();
    }

    # LINKS SECTION (todo: use separated class)

    /**
     * Return current route link
     * @return string
     */
    public function getCurrentRoute()
    {
        return '/' . \Request::path();
    }

    /**
     * Check show next link
     * @return bool
     */
    public function showNextLink()
    {
        $pages = ceil($this->getConfig('allItemsCount')/$this->getItemsPerPage());
        return ($pages != 1 && $pages > $this->getPage() + 1);
    }

    /**
     * Get previous link
     * @return string
     */
    public function getPrevLink()
    {
        $page = ($this->getPage() == 1)? null : $this->getPage() - 1;
        return $this->getActionLink(['page' => $page]);
    }

    /**
     * Get next link
     * @return string
     */
    public function getNextLink()
    {
        return $this->getActionLink(['page' => $this->getPage() + 1]);
    }

    /**
     * Create new link based on current set values
     */
    public function getActionLink($action = [])
    {
        $data = array_merge(\Input::all(), $action);

        $postfix = [];
        foreach($data as $key => $value) {

            if($value !== null && $value !== '') {
                $postfix[] = $key . '=' . $value;
            }
        }

        $postfix = (count($postfix) > 0)? '?' . implode('&', $postfix) : '';

        return $this->getCurrentRoute() . $postfix;
    }
} 