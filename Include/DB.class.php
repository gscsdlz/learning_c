<?php
class DB {
    private static $pdo = null;
    public function __construct() {
        if (self::$pdo == null) {
            try {
                self::$pdo = new PDO ( 'mysql:dbname='.DBNAME.';host='.DBHOST, DBUSER, DBPASS );
                self::$pdo->exec("SET NAMES 'UTF8';");
            } catch ( PDOException $e ) {
                echo $e->getMessage ();
            }
        }
    }
    /**
     *
     * @param string $sql 查询字符串
     * @param array $args 动态绑定参数
     * @return 根据SQL语句自动调整返回值
     * SELECT 返回具体数据
     * DELETE UPDATE 返回受影响行数
     * INSERT 返回刚插入的ID值
     */
    public function query($sql, $args = array()) {
        try {
            $result = self::$pdo->prepare ( $sql );

            $result->execute ($args);
            if($sql[0] == 'S') {//SELECT
                return $result;
            }
            else if($sql[0] == 'I')
                return self::$pdo->lastInsertId();
            else
                return $result->rowCount();
        } catch ( PDOException $e ) {
            echo $e->getMessage ();
        }
    }

    /**
     * @param $sql
     * @param $args
     * @param int $fetchModel默认为数字索引
     * @return array
     * 查询并获取全部结果的数组
     */
    public function query_fetch_all($sql, $args = null, $fetchModel = PDO::FETCH_NUM) {
        try {
            $result = $this->query($sql, $args);
            $args = array();
            while($row = $result->fetch($fetchModel)) {
                $args[] = $row;
            }
            return $args;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    /**
     *
     * @param string $sql 查询字符串
     * @param array $args 动态绑定参数
     * @return 返回一行数据或者null
     */
    public function query_one($sql, $args = null) {
        try {
            $result = $this->query($sql, $args);
        } catch ( PDOException $e ) {
            echo $e->getMessage ();
        }

        if ($result->rowCount () != 0) {
            $row = $result->fetch ( PDO::FETCH_NUM );
            return $row;
        } else {
            return null;
        }
    }
    /**
     * 支持事务处理的函数
     * 一般用于多表更新，插入，删除
     * $args形式为字符串数组格式如下
     * array(
     *     "SQL语句" => array('动态绑定参数', '...', '...'),
     *     "SQL语句" => array() //如果不需要动态绑定，也需要使用空数组，保持兼容
     * )
     *
     * @var array $argArray
     */
    public function transaction_query($args) {
        self::$pdo->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        self::$pdo->beginTransaction ();
        try {
            foreach ( $args as $sql => $arg ) {
                $lastId = $this->query( $sql , $arg);
                if (! $lastId) {
                    throw new PDOException ( "ERROR" );
                }
            }
            self::$pdo->commit ();
        } catch ( PDOException $e ) {
            echo $e->getMessage ();
            self::$pdo->rollback ();
            return -1;
        }
        return $lastId;
    }
}