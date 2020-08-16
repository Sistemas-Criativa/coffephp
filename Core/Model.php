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
    protected $hidden = [];
    protected $locked = [];
    protected $json = [];
    protected static $query;
    protected $key = 'id';
    private static $stm;
    private static $where = false;
    private static $bindParams = array();
    private static $object = [];
    private static $primaryKey = [];
    private static $foreignKey = [];
    private static $name = [];
    private static $starts = ['SELECT', 'UPDATE', 'DELETE', 'INSERT'];

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
    private static function MakeQueryObject($object)
    {
        $class = static::class;
        $temp = [];
        for ($i = 0; $i < sizeof($object); $i++) {
            $obj = new $class;
            foreach ($object[$i] as $item => $value) {
                if (!in_array($item, (new static)->hidden)) {
                    $obj->$item = (in_array($item, (new static)->json) ? json_decode($value) : $value);
                }
            }
            for ($j = 0; $j < sizeof(self::$object); $j++) {
                $name = self::$name[$j];
                $match = false;
                $foreignKey = self::$foreignKey[$j];
                $primaryKey = self::$primaryKey[$j];
                $tempobj = [];
                for ($k = 0; $k < sizeof(self::$object[$j]); $k++) {
                    if ($object[$i][$primaryKey] == self::$object[$j][$k]->$foreignKey) {
                        $tempobj[] = self::$object[$j][$k];
                        $match = true;
                    }
                }
                if ($match == false) {
                    $obj->$name = [];
                } else {
                    $obj->$name = $tempobj;
                }
            }
            $temp[] = $obj;
        }
        self::$object = [];
        self::$primaryKey = [];
        self::$foreignKey = [];
        self::$name = [];
        return $temp;
    }
    /** Get all itens */
    final public static function all()
    {
        self::$query = "SELECT * FROM " . self::tableName();
        return self::execute();
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
            $temp = [];
            foreach ($fields as $item => $value) {
                $temp[] = [$item => $value];
            }
            //divide the fields and make a query
            self::$query .= self::splitFields($temp)['fields'] . " FROM ";
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
    final public static function update(array $data, $table = null)
    {
        self::$bindParams = self::verifyFillables($data);
        self::$bindParams = self::verifyLocked(self::$bindParams);
        self::$query = "UPDATE " . ($table != null ? $table : self::tableName()) . ' SET ' . self::prepareBind(self::$bindParams);
        return (new static);
    }

    /** execute a insert */
    final public static function insert(array $data, $table = null)
    {
        self::$bindParams = self::verifyFillables($data);
        $fields = self::splitFields(self::$bindParams);
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
        for ($i = 0; $i < sizeof(self::$bindParams); $i++) {
            foreach (self::$bindParams[$i] as $field => $value) {
                $types .= "s";
                $itens[] = (in_array($field, (new static)->json) ? json_encode($value) : $value);
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
        $select = false;
        foreach (self::$starts as $reserved) {
            if (strpos(self::$query, $reserved . ' ') === 0) {
                $select = true;
                break;
            }
        }

        if (!$select) {
            self::$query = "SELECT * FROM " . self::tableName() . self::$query;
        }

        $connection = (new static)->Instance()->Connection();
        self::$query = $connection->real_escape_string(self::$query);
        self::$stm = $connection->prepare(self::$query);
        self::$query = "";
        self::$where = false;
        if (self::$stm != false) {
            self::bindParam();
            self::$bindParams = array();
            self::$stm->execute();
            $results = self::$stm->get_result();
            self::$stm->free_result();
            $temp = [];
            if ($results != false) {
                $obj = $results->fetch_all(MYSQLI_ASSOC);
                $temp = self::MakeQueryObject($obj);
                return $temp;
            } else {
                return $connection;
            }
        } else {
            return array();
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
                    $temp[] = [$item => $value];
                }
            }
        }
        return $temp;
    }

    /**
     * Verify fillables itens
     */
    private final static function bindWhere($field, $value)
    {
        self::$bindParams[] = [$field => $value];
    }
    /**
     * Verify locked itens
     */
    private final static function verifyLocked(array $data)
    {
        $temp = [];
        foreach ($data as $item) {
            foreach ($item as $subitem => $value) {
                if (!in_array($subitem, (new static)->locked)) {
                    $temp[] = [$subitem => $value];
                }
            }
        }
        return $temp;
    }

    /**
     * Save the changes on model
     */
    public function save()
    {
        $temp = [];
        foreach ($this as $prop => $value) {
            $temp[$prop] = $value;
        }

        self::update($temp);
        if (isset($temp[(new static)->key])) {
            self::where((new static)->key, '=', $temp[(new static)->key]);
        }
        if (self::execute()->errno == 0) {
            return true;
        }
        return false;
    }


    /**
     * Save the changes on model
     */
    public function destroy()
    {
        self::delete(self::tableName());
        $key = (new static)->key;
        if (isset($this->$key)) {
            self::where((new static)->key, '=', $this->$key);
        }
        if (self::execute()->errno == 0) {
            return true;
        }
        return false;
    }

    /**
     * Create a model
     */
    final public static function create(array $params)
    {
        self::insert($params);
        $id = self::execute()->insert_id;
        $class = static::class;
        $obj = new $class;
        foreach ($params  as $item => $value) {
            $obj->$item = $value;
        }
        $obj->id = $id;
        return $obj;
    }

    final public static function with($object, string $primaryKey, string $foreignKey, string $name)
    {
        self::$object[] = $object;
        self::$primaryKey[] = $primaryKey;
        self::$foreignKey[] = $foreignKey;
        self::$name[] = $name;
        return (new static);
    }
    /** write the query in sql */
    final public static function toSQL()
    {
        $select = false;
        foreach (self::$starts as $reserved) {
            if (strpos(self::$query, $reserved . ' ') === 0) {
                $select = true;
                break;
            }
        }

        if (!$select) {
            self::$query = "SELECT * FROM " . self::tableName() . self::$query;
        }


        $values = [];
        for ($i = 0; $i < sizeof(self::$bindParams); $i++) {
            foreach (self::$bindParams[$i] as $item) {
                $values[] = $item;
            }
        }

        if (sizeof($values) > 0) {
            $query = self::$query;
            self::$query = "";
            self::$where = false;
            self::$bindParams = array();
            $query = str_replace("?", "#%s#", $query);
            $query = sprintf($query, ...$values);
            $query = str_replace("#", "'", $query);
            return sprintf($query, ...$values);
        }
        return self::$query;
    }

    /** add where to query */
    final public static function where($field = '', $operator = '', $value = '', bool $valueIsFunction = false)
    {
        //add the condition
        if ($field == '' || $operator == '' || $value == '') {
            self::$query .= (self::$where == false ? ' WHERE ' : '');
        } else {
            if (!$valueIsFunction)
                self::$bindParams[] = [$field => $value];

            self::$query .= (self::$where == false ? ' WHERE ' : '') . $field . ' ' . $operator . ' ' . ($valueIsFunction ? $value : self::prepareBind([$field], true));
        }
        if (self::$where == false) {
            self::$where = true;
        }

        return (new static);
    }

    /** add and to query */
    final public static function and($field = '', $operator = '', $value = '', bool $valueIsFunction = false)
    {
        //add the condition
        if ($field == '' || $operator == '' || $value == '') {
            self::$query .= ' AND ';
        } else {
            if (!$valueIsFunction)
                self::$bindParams[] = [$field => $value];

            self::$query .= ' AND ' . $field . ' ' . $operator . ' ' . ($valueIsFunction ? $value : self::prepareBind([$field], true));
        }
        return (new static);
    }

    /** add or to query */
    final public static function or($field = '', $operator = '', $value = '', bool $valueIsFunction = false)
    {
        //add the condition
        if ($field == '' || $operator == '' || $value == '') {
            self::$query .= ' OR ';
        } else {
            if (!$valueIsFunction)
                self::$bindParams[] = [$field => $value];

            self::$query .= ' OR ' . $field . ' ' . $operator . ' ' . ($valueIsFunction ? $value : self::prepareBind([$field], true));
        }
        return (new static);
    }

    /** add in to query */
    final public static function in($conditionField)
    {
        self::$where = false;
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
    final public static function orderBy(array $order)
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
    public static function toRAW()
    {
        $query = self::$query;
        self::$query = "";
        self::$where = false;
        self::$bindParams = array();
        return  $query;
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
        for ($i = 0; $i < sizeof($fields); $i++) {
            foreach ($fields[$i] as $item => $value) {
                if (is_numeric($item)) {
                    $return['fields'] .= $value . ($total < sizeof($fields) ? ',' : '');
                } else {
                    $return['values'][] = $value;
                    $return['fields'] .= $item . ($total < sizeof($fields) ? ',' : '');
                }
                $total++;
            }
        }

        return $return;
    }

    /** prepare the bind */
    private static function prepareBind(array $fields, bool $insert = false)
    {
        $return = "";
        foreach ($fields as $field) {
            if ($insert) {
                $return .= "?,";
            } else {
                foreach ($field as $subfield => $value) {
                    $return .= $subfield . "=?,";
                }
            }
        }
        $return = substr($return, 0, -1);
        return $return;
    }
}
