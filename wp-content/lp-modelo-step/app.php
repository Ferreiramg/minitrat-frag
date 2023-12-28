<?php
if (!defined('DOMAIN')) {
    define('DOMAIN', !empty($_SERVER['HTTPS']) ? "https://" . $_SERVER['HTTP_HOST'] : "http://" . $_SERVER['HTTP_HOST']);
}

$path = pathinfo($_SERVER['REQUEST_URI']);


class Model
{
    private $conn;

    public function __construct()
    {
        $this->conn = static::connect();
    }
    public static function connect()
    {
        try {
            $db_name = DB_NAME;
            $host = DB_HOST;

            $conn = new \PDO("mysql:host={$host};dbname={$db_name}", DB_USER, DB_PASSWORD);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (\Exception $th) {

            echo json_encode([
                'error' => $th->getMessage(),
                'code' => $th->getCode(),
                //'trance' => 'Line:' . $th->getLine() . '❗ Trance:' . $th->getTraceAsString(),
            ]);
        }
    }

    public static function csrfToken()
    {
        $token = bin2hex(random_bytes(32));
        setcookie('csrf_token', $token, 0, '/');
        return $token;
    }
}


class Solicitacao
{
    private $conn;

    private $table = 'wp_mi_solicitacoes';

    private $columns = ["tipo_empree", "fase_empree", "padrao", "modelo_empree", "total_empree", "distancia_ponto", "turno_um", "turno_dois", "turno_tres", "nome", "telefone", "email", "uf", "cidade", "receber_notificacao", "created_at"];

    public function __construct(\PDO $pdo)
    {
        $this->conn = $pdo;
    }

    public function insert(array $values)
    {

        $columns = implode(',', array_keys($values));
        $binds = implode(', :', array_keys($values));

        $stmt = $this->conn->prepare("INSERT INTO wp_mi_solicitacoes({$columns}) VALUES(:{$binds})");

        if ($stmt->execute($values)) {
            return $this->conn->lastInsertId();
        }

        throw new \Exception("Falha ao adicionar. tente novamente", 400);
    }

    public function update(array $values, int $id)
    {
        $sets = implode('=?,', array_keys($values)) . '=?';

        $stmt = $this->conn->prepare(
            "UPDATE  wp_mi_solicitacoes set {$sets} where id=?"
        );

        if ($stmt->execute([...array_values($values), $id]))
            return $id;

        throw new \Exception("Falha ao atualizar. tente novamente", 400);
    }

    public function getByID(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM wp_mi_solicitacoes where id=?");

        if ($stmt->execute([$id])) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        throw new \Exception("Falha ao buscar. tente novamente", 400);
    }
}
