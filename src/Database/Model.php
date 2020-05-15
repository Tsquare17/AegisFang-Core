<?php

namespace AegisFang\Database;

use AegisFang\Utils\Strings;

/**
 * Class Model
 * @package AegisFang\Database
 */
class Model
{
    /**
     * @var Query The query object.
     */
    protected Query $query;

    /**
     * @var array An array of the tables columns and values.
     */
    protected array $columns = [];

    /**
     * Model constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;

        $this->setProperties();
    }

    /**
     * Get the assumed name of the table.
     *
     * @return string
     */
    public function getTable(): string
    {
        $class = explode('\\', get_class($this));

        return Strings::pascalToSnake(end($class));
    }

    /**
     * Create the model's column properties.
     */
    public function setProperties(): void
    {
        $columnQuery = (new Query())->select('column_name')
            ->from('information_schema.columns')
            ->where('table_schema', $this->query->getConnection()->getName())
            ->where('table_name', $this->getTable())
            ->execute()->fetchAll();

        foreach ($columnQuery as $column) {
            $this->columns[$column['column_name']] = '';
        }
    }

    /**
     * Set the model's column property values.
     *
     * @param $result
     */
    public function hydrate($result): void
    {
        foreach ($this->columns as $key => $value) {
            $this->columns[$key] = $result[$key];
        }
    }

    /**
     * Fetch a row by id.
     *
     * @param $id
     *
     * @return Model
     */
    public function findById($id): Model
    {
        $query = new Query();
        $query->select()
            ->from($this->getTable())
            ->where(Strings::getSingular($this->getTable()) . '_id', $id);

        $result = $query->execute()->fetch();

        $this->hydrate($result);

        return $this;
    }

    /**
     * Get an array of results.
     *
     * @return array
     */
    public function array(): array
    {
        return $this->columns;
    }

    /**
     * Get a column's value.
     *
     * @param $column
     *
     * @return mixed|null
     */
    public function __get($column)
    {
        return $this->columns[$column] ?? null;
    }

    /**
     * Set a column value.
     *
     * @param $column
     * @param $value
     */
    public function __set($column, $value)
    {
        $this->columns[$column] = $value;
    }

    /**
     * Check if a column is set.
     *
     * @param $column
     *
     * @return bool
     */
    public function __isset($column)
    {
        return isset($this->columns[$column]);
    }
}
