<?php
// declare(strict_types=1);
namespace Core;

use Config\Config;

class Model extends Config
{
    //define the properties
    /** Table and fillables fields */
    protected $table = '';
    protected $fillables = [];
    private static $fill = [];
    protected $hidden = [];
    protected static $query;
    private static $stm;
    private static $bindParams = array();

    /* define the table name with base classe if table is not specified*/
    function __construct()
    {
        if (empty($this->table)) {
            $this->table = get_called_class();
        }
    }

    /** get the table name */
    final public static function tableName()
    {
        return (new static)->table;
    }

    /** get the hidden fields */
    final public static function hidden()
    {
        return (new static)->hidden;
    }
    /** Get all itens */
    final public static function all()
    {
        self::$query = "SELECT * FROM " . self::tableName();
        $DB = (new static)->Instance()->Connection();
        return $DB->query(self::$query);
    }

    /** get the query  */
    final public static function get()
    {
        $DB = (new static)->Instance()->Connection();
        return $DB->query(self::$query);
    }
    /** Create a select, with fields if is necessary
     * 
     */
    final public static function select(array $fields = null, $table = null)
    {
        //Init the select
        self::$query .= "SELECT ";

        //if has fields, make the fields query
        if ($fields == null) {
            self::$query .= "* FROM ";
        } else {
            //divide the fields and make a query
            self::$query .= self::splitFields($fields)['fields'] . " FROM ";
        }
        //add the table name
        self::$query .= ($table != null ? $table : self::tableName());
        return (new static);
    }

    /** Add a select count */
    final public static function selectCount($col, $alias, $table = null)
    {
        //init the select
        self::$query .= "SELECT COUNT($col) AS $alias FROM " . ($table != null ? $table : self::tableName());
        return (new static);
    }

    /** Add a inner join*/
    final public static function innerJoin(string $table, string $primaryKey, string $foreignKey)
    {
        //inicia o select
        self::$query .= " INNER JOIN " . $table . " ON $table.$primaryKey = " . self::tableName() . ".$foreignKey ";
        return (new static);
    }

    /** Add a outer join*/
    final public static function leftJoin(string $table, string $primaryKey, string $foreignKey)
    {
        //Init the select
        self::$query .= " LEFT JOIN " . $table . " ON $table.$primaryKey = " . self::tableName() . ".$foreignKey ";
        return (new static);
    }

    /** Add a right join*/
    final public static function rightJoin(string $table, string $primaryKey, string $foreignKey)
    {
        //inicia o select
        self::$query .= " RIGHT JOIN " . $table . " ON $table.$primaryKey = " . self::tableName() . ".$foreignKey ";
        return (new static);
    }

    /** execute an update */
    final public static function update($data, $table = null)
    {
        self::$bindParams = $data;
        self::$fill = (new static)->fillables;
        self::$query = "UPDATE " . ($table != null ? $table : self::tableName()) . ' SET ' . self::prepareBind($data);
        return (new static);
    }

    /** execute a insert */
    final public static function insert(array $data, $table = null)
    {
        self::$bindParams = $data;
        self::$fill = (new static)->fillables;
        $fields = self::splitFields($data);
        self::$query = "INSERT INTO " . ($table != null ? $table : self::tableName()) . " (" . $fields['fields'] . ') VALUES( ' . self::prepareBind($fields['values'], true) . ')';
        return (new static);
    }
    /** delete a record */
    final public static function delete(string $table = null)
    {
        self::$query = "DELETE FROM " . ($table != null ? $table : self::tableName());
        return (new static);
    }

    /** bind the params */
    private static function bindParam()
    {
        $types = "";
        $itens = [];
        foreach (self::$bindParams as $field => $value) {
            if (in_array($field, self::$fill)) {
                $types .= "s";
                $itens[] = $value;
            }
        }
        if (count($itens) > 0)
            self::$stm->bind_param($types, ...$itens);
    }

    /**
     * 
     * Execute a query
     */
    final public static function execute()
    {
        $connection = (new static)->Instance()->Connection();
        self::$stm = $connection->prepare(self::$query);
        if (self::$stm != false) {
            self::bindParam();
            self::$stm->execute();
            $results = self::$stm->get_result();
            $temp = [];
            $count = 0;
            if (!is_bool($results)) {
                while ($row = $results->fetch_assoc()) {
                    foreach ($row as $item => $value) {
                        if (!in_array($item, (new static)->hidden)) {
                            $temp[$count][$item] = $value;
                        }
                    }
                    $count++;
                }
                return $temp;
            } else {
                return $connection;
            }
        } else {
            return $results;
        }
    }

    /**
     * Verify fillables itens
     */
    private final static function verifyFillables(array $data)
    {
        $temp = [];
        foreach ($data as $item => $value) {
            if (!is_numeric($item)) {
                if (in_array($item, (new static)->fillables)) {
                    $temp[$item] = $value;
                }
            }
        }
        return $temp;
    }
    /**
     * Create a model
     */
    final public static function create(array $params)
    {
        $params = self::verifyFillables($params);
        self::insert($params);
        $params['id'] = self::execute()->insert_id;
        return $params;
    }
    /** write the query in sql */
    final public static function toSQL()
    {
        if (sizeof(self::$bindParams) > 0) {
            $values = array_values(self::$bindParams);
            self::$query = str_replace("?", "#%s#", self::$query);
            self::$query = sprintf(self::$query, ...$values);
            self::$query = str_replace("#", "'", self::$query);
            return sprintf(self::$query, ...$values);
        }
        return self::$query;
    }

    /** add where to query */
    final public static function where(array $conditions = array())
    {
        //add the condition
        if (sizeof($conditions) == 3) {
            self::$query .= ' WHERE ' . $conditions[0] . ' ' . $conditions[1] . ' ' . "'" . $conditions[2] . "'";
        } else {
            self::$query .= ' WHERE ';
        }
        return (new static);
    }

    /** add and clause */
    final public static function and(array $conditions)
    {
        //add the condition
        if (sizeof($conditions) == 3) {
            self::$query .= ' AND ' . $conditions[0] . ' ' . $conditions[1] . ' '  . "'" .  $conditions[2]  . "'";
        }
        return (new static);
    }

    /** add or clause */
    final public static function or(array $conditions)
    {
         //add the condition
        if (sizeof($conditions) == 3) {
            self::$query .= ' OR ' . $conditions[0] . ' ' . $conditions[1] . ' '  . "'" .  $conditions[2]  . "'";
        }
        return (new static);
    }
    /** add in to query */
    final public static function in($conditionField)
    {
        //add the condition
        self::$query .= $conditionField . ' IN(';
        return (new static);
    }
    /** adiciona condições a consulta */
    final public static function endin()
    {
        self::$query .= ' )';
        return (new static);
    }

    /** order a query */
    final public static function order(array $order)
    {

        //add the order
        self::$query .= ' ORDER BY ';
        $total = 1;

        //add the order array
        foreach ($order as $item => $value) {
            self::$query .= $item . " " . $value . ($total < sizeof($order) ? ',' : '');
            $total++;
        }
        return (new static);
    }

    /**
     * group a query 
     *
     * @return void
     */
    final public static function group(array $group)
    {
        //add the group
        self::$query .= ' GROUP BY ';
        $total = 1;
        foreach ($group as $item => $value) {
            self::$query .= $value . ($total < sizeof($group) ? ',' : '');
            $total++;
        }
        return (new static);
    }

    /** Limit the number of records*/
    final public static function limit($quantity = null)
    {
        if (!$quantity) {
            //add the limit
            if (filter_var($quantity, FILTER_VALIDATE_INT)) {
                self::$query .= ' LIMIT ' . $quantity;
            }
        }
        return (new static);
    }

    /** add the pre to a sql query */
    public static function pre()
    {
        self::$query = '<pre>' . self::$query . '</pre>';
        return (new static);
    }

    /** executa a raw query*/
    final public static function raw($query)
    {
        self::$query = $query;
        return (new static);
    }

    /** split the fields */
    private static function splitFields($fields)
    {
        $total = 1;
        $return = array('fields' => "", 'values' => []);
        foreach ($fields as $item => $value) {
            if (is_numeric($item)) {
                $return['fields'] .= $value . ($total < sizeof($fields) ? ',' : '');
            } else {
                $return['values'][] = $value;
                $return['fields'] .= $item . ($total < sizeof($fields) ? ',' : '');
            }
            $total++;
        }

        return $return;
    }

    /** prepare the bind */
    private static function prepareBind(array $fields, bool $insert = false)
    {
        $return = "";
        foreach ($fields as $field => $value) {
            if ($insert) {
                $return .= "?,";
            } else {
                $return .= $field . "=?,";
            }
        }
        $return = substr($return, 0, -1);
        return $return;
    }
}
