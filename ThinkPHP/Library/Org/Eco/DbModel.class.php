<?php
namespace Org\Eco;

class DbModel
{

    protected static $_instances = array();

    /**
     *
     * @return \Org\Eco\DbModel
     */
    static public function instance($w = 'w', $table = '')
    {
        if (empty(self::$_instances[$w])) {
            $cls = __CLASS__;
            self::$_instances[$w] = new $cls($w);
        }
        if (! empty($table)) {
            self::$_instances[$w]->table($table);
        }
        return self::$_instances[$w];
    }

    protected $_debug = false;

    protected $_errorInfo = null;
    protected $_errorFlag = false;

    protected $_which = '';

    /**
     *
     * @var \PDO
     */
    protected $_pdo = null;

    private   $_recover_link	=	true;
    private   $_link_dbname		=	'';
    private   $_link			=	null;

    /**
     *
     * @var \PDO
     */
    protected $_connection = null;

    protected $_transaction = false;

    protected $_sqlStr = '';

    protected $_field = '';

    protected $_joins = array();

    protected $_wheres = array();

    protected $_likes = array();

    protected $_bindParam = array();

    protected $_bindNo = 0;

    protected $_where = '';

    protected $_group = '';

    protected $_having = '';

    protected $_order = '';

    protected $_limit = '';

    protected $_sets = array();

    protected function __construct($w = 'w')
    {
        if (empty($w)) {
            $w = 'w';
        }
        switch (DEMO_MOLD) {
            case 1:
                $this->_which = 'demo';
                break;
            case 0:
            default:
                $this->_which = $w;
                break;
        }
    }
    /**
     * 连接指定数据库
     * @access public
     * @param string $dbname 数据库名称
     * @param boolean $keep 是否保存连接
     * @return Model
     */
    public function link($dbname = null, $keep = null)
    {
        if (! is_null($keep)) {
            $this->_recover_link = !$keep;
        }
        if (empty($dbname)) {
            $this->_link_dbname = '';
            $this->_recover_link = false;
        } else {
            $this->_link_dbname = $dbname;
        }
        return $this;
    }
    
    public function recoverLink()
    {
        if ($this->_link_dbname) {
            $this->_link_dbname = '';
        }
        return $this;
    }
    
    protected function recoverSelf()
    {
        $this->recoverLink();
        M()->link();
        return $this;
    }

    /**
     *
     * @return \PDO
     */
    protected function connect()
    {
        if($this->_link_dbname){
            if (empty($this->_link)) {
                $conf = array();
                $conf['host'] = C('DB_HOST');
                $conf['dbname'] = C('DB_NAME');
                $conf['user'] = C('DB_USER');
                $conf['pass'] = C('DB_PWD');
                $this->_link = new \PDO('mysql:host=' . $conf['host'] . ';dbname=' . $this->_link_dbname . '', $conf['user'], $conf['pass']);
                $this->_link->exec("SET NAMES utf8mb4");
            }
            return $this->_link;
        }else{
            if (empty($this->_pdo)) {
                $conf = array();
                $conf['host'] = C('DB_HOST');
                $conf['dbname'] = C('DB_NAME');
                $conf['user'] = C('DB_USER');
                $conf['pass'] = C('DB_PWD');
                $this->_pdo = new \PDO('mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'] . '', $conf['user'], $conf['pass']);
                $this->_pdo->exec("SET NAMES utf8mb4");
            }
            return $this->_pdo;
        }
    }

    protected function reset()
    {
        $this->_errorInfo = array(
            0,
            0,
            ''
        );
        $this->_field = '';
        $this->_joins = array();
        $this->_wheres = array();
        $this->_likes = array();
        $this->_bindParam = array();
        $this->_bindNo = 0;
        $this->_where = '';
        $this->_group = '';
        $this->_having = '';
        $this->_order = '';
        $this->_limit = '';
        $this->_sets = array();
    }

    protected function cacheKeys()
    {
        $keys = array();
        $keys['_field'] = $this->_field;
        $keys['_joins'] = $this->_joins;
        $keys['_wheres'] = $this->_wheres;
        $keys['_likes'] = $this->_likes;
        $keys['_where'] = $this->_where;
        $keys['_group'] = $this->_group;
        $keys['_having'] = $this->_having;
        $keys['_order'] = $this->_order;
        $keys['_limit'] = $this->_limit;
        return $keys;
    }

    public function debug($debug = true)
    {
        $this->_debug = $debug;
    }
    
    public function isError(){
        switch (func_num_args()) {
            case 0:
                return $this->_errorFlag;
            default:
                $this->_errorFlag = func_get_arg(0);
                break;
        }
        return $this;
    }

    public function error($w = null)
    {
        if (is_null($w)) {
            return $this->_errorInfo;
        } else {
            switch ($w) {
                case 0:
                case 1:
                    return $this->_errorInfo[1];
                    break;
                case 2:
                default:
                    return $this->_errorInfo[2];
                    break;
            }
        }
    }

    protected function dberror($stmt)
    {
        $this->isError(true);
        $this->_errorInfo = $stmt->errorInfo();
        if ($this->_debug || LOG_ERROR) {
            $error = $this->_errorInfo;
            if (! empty($error[2])) {
                $sql = $this->_sql();
                $this->logerror($error[1] . ' : ' . $sql . '; ' . $error[2]);
                if ($this->_debug) {
                    throw new \Exception($error[2] . ' : ' . $sql, $error[1]);
                }
            }
        }
    }

    protected function logerror($e)
    {
        $this->isError(true);
        if (LOG_ERROR) {
            $msg = '';
            if (is_string($e)) {
                $msg = $e;
            } elseif ($e instanceof \PDOException) {
                $msg = $e->getMessage();
            }
        }
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function table($table = '')
    {
        if (! empty($table)) {
            $this->reset();
            $this->join($table);
        }
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function join($table)
    {
        if (empty($table)) {
            return $this;
        }
        if (! empty($this->_joins)) {
            if (stripos($table, ' join ') === false) {
                $table = 'INNER JOIN ' . $table;
            }
        }
        $this->_joins[] = $table;
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function where($where)
    {
        if (is_array($where)) {
            $this->_wheres = array_merge($this->_wheres, $where);
        } elseif (is_string($where)) {
            if (! empty($this->_where)) {
                $this->_where .= ' AND ';
            }
            $this->_where .= $where;
        }
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function like($key, $field)
    {
        if (empty($field)) {
            return $this;
        }
        $like = 'LCASE(' . $field . ')';
        $like .= " LIKE BINARY ? ESCAPE '#'";
        $key = strtolower($key);
        $key = str_replace("#", "##", $key);
        $key = str_replace("%", "#%", $key);
        $key = str_replace("_", "#_", $key);
        $this->_likes[$like] = '%' . $key . '%';
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function field($field, $clear = false)
    {
        if ($clear) {
            $this->_field = '';
        }
        if (is_string($field)) {
            if (! empty($this->_field)) {
                $this->_field .= ',';
            }
            $this->_field .= $field;
        }
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function order($order, $clear = false)
    {
        if ($clear) {
            $this->_order = '';
        }
        if (is_string($order)) {
            if (! empty($this->_order)) {
                $this->_order .= ',';
            }
            $this->_order .= $order;
        }
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function group($group, $clear = false)
    {
        if ($clear) {
            $this->_group = '';
        }
        if (is_string($group)) {
            if (! empty($this->_group)) {
                $this->_group .= ',';
            }
            $this->_group .= $group;
        }
        return $this;
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    public function limit($offset, $length = null)
    {
        if (is_null($length) && strpos($offset, ',')) {
            list ($offset, $length) = explode(',', $offset);
        }
        $this->_limit = intval($offset) . ($length ? ',' . intval($length) : '');
        return $this;
    }

    /**
     * 启动事务
     */
    public function startTrans()
    {
        $this->_transaction = true;
        $this->connect()->beginTransaction();
    }

    /**
     * 用于非自动提交状态下面的查询提交
     */
    public function commit()
    {
        if ($this->_transaction) {
            $this->connect()->commit();
            $this->_transaction = false;
        }
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        if ($this->_transaction) {
            $this->connect()->rollBack();
            $this->_transaction = false;
        }
    }

    public function execute($sql, $data = null)
    {
        $stmt = $this->connect()->prepare($sql);
        try {
            if (empty($data)) {
                $result = $stmt->execute();
            } else {
                $result = $stmt->execute($data);
            }
            if (false === $result) {
                $this->dberror($stmt);
            }
            $this->recoverSelf();
            return $result;
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function query($sql, $data = null)
    {
        $stmt = $this->connect()->prepare($sql);
        try {
            if (empty($data)) {
                $result = $stmt->execute();
            } else {
                $result = $stmt->execute($data);
            }
            if ($result) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            $this->recoverSelf();
            return $result;
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    protected function fetchWhereSql()
    {
        $sql = '';
        if (! empty($this->_where) || ! empty($this->_wheres) || ! empty($this->_likes)) {
            $sql .= ' WHERE';
            $has = false;
            if (! empty($this->_where)) {
                $has = true;
                $sql .= ' ' . $this->_where;
            }
            if (! empty($this->_wheres)) {
                foreach ($this->_wheres as $col => $val) {
                    if ($has) {
                        $sql .= ' AND';
                    } else {
                        $has = true;
                    }
                    $this->_bindNo ++;
                    $bcol = ':bcol' . $this->_bindNo;
                    if (is_array($val)) {
                        if ($val[0] == 'in') {
                            $sql .= ' ' . $col . ' ' . $val[0] . ' (';
                            $n = count($val[1]);
                            for ($i = 0; $i < $n; $i ++) {
                                if ($i > 0) {
                                    $sql .= ',';
                                }
                                $bcol = ':bcol' . $this->_bindNo;
                                $sql .= $bcol;
                                $this->_bindParam[$bcol] = $val[1][$i];
                                if ($i < $n - 1) {
                                    $this->_bindNo ++;
                                }
                            }
                            $sql .= ')';
                        } else {
                            $sql .= ' ' . $col . ' ' . $val[0] . ' ' . $bcol;
                            $this->_bindParam[$bcol] = $val[1];
                        }
                    } else {
                        $sql .= ' ' . $col . ' = ' . $bcol;
                        $this->_bindParam[$bcol] = $val;
                    }
                }
            }
            if (! empty($this->_likes)) {
                foreach ($this->_likes as $like => $val) {
                    if ($has) {
                        $sql .= ' AND';
                    } else {
                        $has = true;
                    }
                    $this->_bindNo ++;
                    $bcol = ':bcol' . $this->_bindNo;
                    $like = str_replace('?', $bcol, $like);
                    $sql .= ' ' . $like;
                    $this->_bindParam[$bcol] = $val;
                }
            }
        }
        return $sql;
    }

    public function fetchSelectSql()
    {
        $sql = 'SELECT';
        if (empty($this->_field)) {
            $sql .= ' *';
        } else {
            $sql .= ' ' . $this->_field;
        }
        if (empty($this->_joins)) {
            return false;
        }
        $sql .= ' FROM';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $this->_bindParam = array();
        $this->_bindNo = 0;
        $sql .= $this->fetchWhereSql();
        if (! empty($this->_group)) {
            $sql .= ' GROUP BY ' . $this->_group;
        }
        if (! empty($this->_having)) {
            $sql .= ' HAVING ' . $this->_having;
        }
        if (! empty($this->_order)) {
            $sql .= ' ORDER BY ' . $this->_order;
        }
        if (! empty($this->_limit)) {
            $sql .= ' LIMIT ' . $this->_limit;
        }
        $this->_sqlStr = $sql;
        return $sql;
    }

    public function _sql()
    {
        $str = $this->_sqlStr;
        if (! empty($this->_bindParam)) {
            $str = strtr($str, array_map(function ($val) {
                return '\'' . addslashes($val) . '\'';
            }, $this->_bindParam));
        }
        return $str;
    }

    /**
     *
     * @return \PDOStatement
     */
    protected function bindParam($stmt)
    {
        if (! empty($this->_bindParam)) {
            foreach ($this->_bindParam as $parameter => $variable) {
                $stmt->bindParam($parameter, $this->_bindParam[$parameter]);
            }
        }
        return $stmt;
    }

    /**
     *
     * @return \PDOStatement
     */
    protected function prependQuery()
    {
        $stmt = $this->connect()->prepare($this->fetchSelectSql());
        return $this->bindParam($stmt);
    }

    public function select($options = array())
    {
        $stmt = $this->prependQuery();
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $array = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $stmt = null;
                if (empty($array)) {
                    $array = array();
                }
                $this->recoverSelf();
                return $array;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function getField($field = '')
    {
        if (! empty($field)) {
            $this->_field = $field;
        }
        $stmt = $this->prependQuery();
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                return false;
            } else {
                $array = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
                $stmt->closeCursor();
                $stmt = null;
                return $array;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            return false;
        }
    }

    public function getFields($fields = '', $unique = true)
    {
        if (! empty($fields)) {
            $this->_field = $fields;
        }
        $stmt = $this->prependQuery();
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                return false;
            } else {
                $style = \PDO::FETCH_GROUP | \PDO::FETCH_ASSOC;
                if ($unique) {
                    $style |= \PDO::FETCH_UNIQUE;
                }
                $array = $stmt->fetchAll($style);
                $stmt->closeCursor();
                $stmt = null;
                return $array;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            return false;
        }
    }

    public function find($options = array())
    {
        $this->limit(1);
        $stmt = $this->prependQuery();
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                return false;
            } else {
                $array = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $stmt = null;
                $array = empty($array) ? $array : $array[0];
                return $array;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            return false;
        }
    }

    public function add($data, $ignore = false)
    {
        $this->set($data);
        $sql = $ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $cols = '';
        $vals = '';
        foreach ($this->_sets as $col => $val) {
            if (! empty($cols)) {
                $cols .= ',';
                $vals .= ',';
            }
            $this->_bindNo ++;
            $bcol = ':bcol' . $this->_bindNo;
            $cols .= $col;
            $vals .= ' ' . $bcol;
            $this->_bindParam[$bcol] = $val;
        }
        $sql .= ' (' . $cols . ')';
        $sql .= ' VALUES (' . $vals . ')';
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $id = $this->connect()->lastInsertId();
                $this->recoverSelf();
                return $id;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function addAll($dataList, $ignore = false)
    {
        if (empty($dataList)) {
            $this->recoverSelf();
            return null;
        }
        $sql = $ignore ? 'INSERT IGNORE INTO' : 'INSERT INTO';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $colarr = array();
        $cols = '';
        $vals = '';
        $data = $dataList[0];
        foreach ($data as $col => $val) {
            if (! empty($cols)) {
                $cols .= ',';
                $vals .= ',';
            }
            $this->_bindNo ++;
            $bcol = ':bcol' . $this->_bindNo;
            $cols .= $col;
            array_push($colarr, $col);
            $vals .= ' ' . $bcol;
            $this->_bindParam[$bcol] = $val;
        }
        $sql .= ' (' . $cols . ')';
        $sql .= ' VALUES (' . $vals . ')';
        $n = count($dataList);
        $m = count($colarr);
        for ($i = 1; $i < $n; $i ++) {
            $sql .= ', (';
            for ($j = 0; $j < $m; $j ++) {
                if ($j > 0) {
                    $sql .= ',';
                }
                $this->_bindNo ++;
                $bcol = ':bcol' . $this->_bindNo;
                $sql .= ' ' . $bcol;
                $this->_bindParam[$bcol] = $dataList[$i][$colarr[$j]];
            }
            $sql .= ')';
        }
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $ret = $stmt->rowCount();
                $this->recoverSelf();
                return $ret;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function duplicateUpdate($data, $key)
    {
        $sql = 'INSERT INTO';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $cols = '';
        $vals = '';
        foreach ($data as $col => $val) {
            if (! empty($cols)) {
                $cols .= ',';
                $vals .= ',';
            }
            $this->_bindNo ++;
            $bcol = ':bcol' . $this->_bindNo;
            $cols .= $col;
            $vals .= ' ' . $bcol;
            $this->_bindParam[$bcol] = $val;
        }
        $sql .= ' (' . $cols . ')';
        $sql .= ' VALUES (' . $vals . ')';
        $sql .= ' ON DUPLICATE KEY UPDATE ';
        $has = false;
        foreach ($data as $col => $val) {
            if ($col == $key) {
                continue;
            }
            if ($has) {
                $sql .= ',';
            } else {
                $has = true;
            }
            $this->_bindNo ++;
            $bcol = ':bcol' . $this->_bindNo;
            $sql .= $col . ' = ' . $bcol;
            $this->_bindParam[$bcol] = $val;
        }
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $ret = $stmt->rowCount();
                $this->recoverSelf();
                return $ret;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            $this->recoverSelf();
            return false;
        }
    }

    public function set($field, $val = '')
    {
        if (is_array($field)) {
            $this->_sets = array_merge($this->_sets, $field);
        } elseif (is_string($field)) {
            $this->_sets[$field] = $val;
        }
        return $this;
    }

    public function save($data = null)
    {
        $this->set($data);
        $sql = 'UPDATE';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $sql .= ' SET ';
        $has = false;
        foreach ($this->_sets as $col => $val) {
            if ($has) {
                $sql .= ',';
            } else {
                $has = true;
            }
            $this->_bindNo ++;
            $bcol = ':bcol' . $this->_bindNo;
            $sql .= $col . ' = ' . $bcol;
            $this->_bindParam[$bcol] = $val;
        }
        $sql .= $this->fetchWhereSql();
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $ret = $stmt->rowCount();
                $this->recoverSelf();
                return $ret;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function saveAll($cols, $setnum, $dataList)
    {
        if (empty($dataList)) {
            $this->recoverSelf();
            return null;
        }
        $sql = 'UPDATE';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $sql .= ' SET ';
        $has = false;
        for ($i = 0; $i < $setnum; $i ++) {
            if ($has) {
                $sql .= ',';
            } else {
                $has = true;
            }
            $bcol = ':bcol' . $i;
            $sql .= $cols[$i] . ' = ' . $bcol;
        }
        $where = $this->fetchWhereSql();
        $sql .= $where;
        $n = count($cols);
        if ($n > $setnum) {
            if (empty($where)) {
                $sql .= ' WHERE';
            } else {
                $sql .= ' AND';
            }
            $has = false;
            for ($i = $setnum; $i < $n; $i ++) {
                if ($has) {
                    $sql .= ' AND';
                } else {
                    $has = true;
                }
                $bcol = ':bcol' . $i;
                if (is_array($cols[$i])) {
                    $sql .= ' ' . $cols[$i][0] . ' ' . $cols[$i][1] . ' ' . $bcol;
                } else {
                    $sql .= ' ' . $cols[$i] . ' = ' . $bcol;
                }
            }
        }
        $this->_sqlStr = $sql;
        $ret = false;
        try {
            $stmt = $this->connect()->prepare($sql);
            $n = count($dataList);
            $m = count($cols);
            for ($i = 0; $i < $n; $i ++) {
                $_sql = $sql;
                $data = array();
                for ($j = 0; $j < $m; $j ++) {
                    $bcol = ':bcol' . $j;
                    if (is_array($cols[$j])) {
                        $val = $dataList[$i][$cols[$j][0]];
                    } else {
                        $val = $dataList[$i][$cols[$j]];
                    }
                    $_sql = str_replace($bcol, $val, $_sql);
                    $data[$bcol] = $val;
                }
                $result = $stmt->execute($data);
                if (false === $result) {
                    $this->dberror($stmt);
                    $ret = false;
                } else {
                    $ret = $stmt->rowCount();
                }
            }
            $this->recoverSelf();
            return $ret;
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function delete()
    {
        $sql = 'DELETE FROM';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $sql .= $this->fetchWhereSql();
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $ret = $stmt->rowCount();
                $this->recoverSelf();
                return $ret;
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
    }

    public function checkResult($result)
    {
        if ($result === false) {
            $this->rollback();
            $callback = \Org\Eco\ExceptionHandler::instance()->getCallback();
            if (empty($callback)) {
                \Org\Eco\Response::instance()->error('服务器访问失败，请联系管理员');
            } else {
                $callback(new \Org\Eco\DbException($this->_errorInfo[2], $this->_errorInfo[1]));
            }
        }
    }

    public function pagination($options = array())
    {
        if (empty($this->_joins)) {
            $this->recoverSelf();
            return false;
        }
        $tot = 0;
        $rows = array();
        $sql = 'SELECT COUNT(*)';
        $sql .= ' FROM';
        $n = count($this->_joins);
        for ($i = 0; $i < $n; $i ++) {
            $sql .= ' ' . $this->_joins[$i];
        }
        $this->_bindParam = array();
        $this->_bindNo = 0;
        $sql .= $this->fetchWhereSql();
        if (! empty($this->_group)) {
            $sql .= ' GROUP BY ' . $this->_group;
        }
        if (! empty($this->_having)) {
            $sql .= ' HAVING ' . $this->_having;
        }
        $sql .= ' LIMIT 1';
        $this->_sqlStr = $sql;
        $stmt = $this->connect()->prepare($sql);
        $this->bindParam($stmt);
        try {
            $result = $stmt->execute();
            if (false === $result) {
                $this->dberror($stmt);
                $this->recoverSelf();
                return false;
            } else {
                $result = $stmt->fetch(\PDO::FETCH_NUM);
                $stmt->closeCursor();
                $stmt = null;
                $tot = empty($result) ? '0' : $result[0];
            }
        } catch (\PDOException $e) {
            $this->logerror($e);
            if ($this->_debug)
                throw $e;
            $this->recoverSelf();
            return false;
        }
        if (! empty($tot)) {
            $rows = $this->select();
        }
        $array = array(
            'tot' => $tot,
            'rows' => $rows
        );
        $this->recoverSelf();
        return $array;
    }

    public function fieldsFormat($fields)
    {
        if (empty($fields)) {
            return '';
        }
        $fields = trim($fields);
        $fields = preg_replace("/\\s{1,}/i", ",", $fields);
        $fields = preg_replace("/,{2,}/i", ",", $fields);
        return $fields;
    }

    public function fields2no($fields, $tblas = false)
    {
        $fields = $this->fieldsFormat($fields);
        $fields = preg_replace_callback("/[0-9a-zA-Z\\.\\_\\-\\*`]+/i", function ($match) use ($tblas) {
            static $i = 0;
            if (substr($match[0], - 1) == '*') {
                return $match[0];
            }
            $colas = $match[0] . ' as c' . ($i ++);
            if ($tblas && strpos($match[0], '.') === false) {
                $colas = 'a.' . $colas;
            }
            return $colas;
        }, $fields);
        return $fields;
    }

    public function fields2val()
    {
        switch (func_num_args()) {
            case 2:
                $fields = func_get_arg(0);
                $cols = func_get_arg(0);
                $ignore = func_get_arg(1);
                break;
            case 3:
                $fields = func_get_arg(0);
                $cols = func_get_arg(1);
                $ignore = func_get_arg(2);
                break;
            default:
                return false;
        }
        $fields = $this->fieldsFormat($fields);
        $cols = $this->fieldsFormat($cols);
        $ignore = $this->fieldsFormat($ignore);
        $fields = explode(",", $fields);
        $cols = explode(",", $cols);
        $ignore = explode(",", $ignore);
        $data = array();
        $n = count($cols);
        for ($i = 0; $i < $n; $i ++) {
            $key = 'c' . $i;
            if (in_array($cols[$i], $fields)) {
                $val = I('post.' . $key, '');
                if (empty($val)) {
                    if (! in_array($cols[$i], $ignore)) {
                        return false;
                    }
                }
                $data[$cols[$i]] = $val;
            }
        }
        return $data;
    }

    public function fields2all()
    {
        switch (func_num_args()) {
            case 2:
                $fields = func_get_arg(0);
                $cols = func_get_arg(0);
                $ignore = func_get_arg(1);
                break;
            case 3:
                $fields = func_get_arg(0);
                $cols = func_get_arg(1);
                $ignore = func_get_arg(2);
                break;
            default:
                return false;
        }
        $fields = $this->fieldsFormat($fields);
        $cols = $this->fieldsFormat($cols);
        $ignore = $this->fieldsFormat($ignore);
        $fields = explode(",", $fields);
        $cols = explode(",", $cols);
        $ignore = explode(",", $ignore);
        $dataList = array();
        $fno = - 1;
        $n = count($cols);
        for ($i = 0; $i < $n; $i ++) {
            $key = 'c' . $i;
            if (! in_array($cols[$i], $fields)) {
                continue;
            }
            $val = I('post.' . $key, '');
            $val = trim($val, ',');
            if (empty($val)) {
                if (! in_array($cols[$i], $ignore)) {
                    return false;
                }
            }
            $asso = explode(",", $val);
            if (! empty($asso)) {
                if ($fno == - 1) {
                    $fno = $i;
                    $first = true;
                } else {
                    $first = false;
                }
                $m = count($asso);
                for ($j = 0; $j < $m; $j ++) {
                    $tag = $asso[$j];
                    if (empty($tag)) {
                        if (! in_array($cols[$i], $ignore)) {
                            return false;
                        }
                    }
                    $k = count($dataList);
                    if ($first) {
                        $fno = $i;
                        $dataList[$k] = array(
                            $cols[$i] => $tag
                        );
                    } else {
                        $dataList[$k - 1][$cols[$i]] = $tag;
                    }
                }
            }
        }
        return $dataList;
    }

    public function getPostNo($cols, $field)
    {
        $cols = $this->fieldsFormat($cols);
        $cols = explode(",", $cols);
        $n = count($cols);
        for ($i = 0; i < $n; $i ++) {
            if ($cols[$i] == $field) {
                return 'c' . $i;
            }
        }
        return $field;
    }

    public function getOrder($toArr = false)
    {
        if (empty($this->_order)) {
            return $toArr ? array() : '';
        }
        $this->_order = preg_replace("/\\s{1,}/i", " ", $this->_order);
        if ($toArr) {
            $ret = array();
            $sts = explode(',', $this->_order);
            foreach ($sts as $st) {
                $st = trim($st);
                $val = explode(' ', $st);
                if (count($val) == 2) {
                    $ret[$val[0]] = $val[1];
                }
            }
            return $ret;
        } else {
            return $this->_order;
        }
    }
}