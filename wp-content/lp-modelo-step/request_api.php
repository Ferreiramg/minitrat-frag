<?php

require_once dirname(dirname(__DIR__)) . '/wp-config.php';

require_once dirname(dirname(__DIR__)) . '/wp-load.php';

require_once __DIR__ . "/app.php";

function validateCsrfToken()
{
    if (
        isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
        isset($_COOKIE['csrf_token']) &&
        hash_equals($_COOKIE['csrf_token'], $_SERVER['HTTP_X_CSRF_TOKEN'])
    ) {

        return true;
    } else {
        http_response_code(403);
        die('Token CSRF inválido');
    }
}

validateCsrfToken();

header('Content-type: application/json; charset=UTF-8');
try {
    // URL da API
    $api_url = 'http://localhost:8000/api/nova-solicitacao';

    $post = filter_input_array(INPUT_GET, FILTER_DEFAULT, true);

    $id = $post['id'];

    $pdo = Model::connect();

    $solicitacao = new Solicitacao($pdo);

    $dados = $solicitacao->getByID($id);

    if (empty($dados)) {
        throw new \InvalidArgumentException('Solicitação não encontrada', 404);
    }

    // Inicializa a sessão cURL
    $ch = curl_init($api_url);

    // Configura as opções de requisição
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);

    $resposta = curl_exec($ch);

    // Verifica se houve algum erro
    if (curl_errno($ch)) {
        echo 'Erro ao enviar a requisição cURL: ' . curl_error($ch);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode([
        'error' => $th->getMessage(),
        'code' => $th->getCode(),
        'trance' => 'Line:' . $th->getLine() . '❗ Trance:' . $th->getTraceAsString(),
    ]);
} finally {
    // Fecha a sessão cURL
    curl_close($ch);
}
