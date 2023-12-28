<?php

include_once dirname(dirname(__DIR__)) . '/wp-config.php';

include_once __DIR__ . "/app.php";


$steps = [
    'R' => 'residencial',
    'C' => 'comercial',
    'I' => 'empresa',
];

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

try {

    $pdo = Model::connect();

    $solicitacao = new Solicitacao($pdo);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        header('Content-type: application/json; charset=UTF-8');

        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT, true);


        if (isset($post['id']) && intval($post['id']) > 0) {
            $id = $post['id'];
            unset($post['id']);
            $solicitacao->update($post, $id);

            echo json_encode([
                'success' => true,
                'next' => $steps[$post['tipo_empree']] ?? 'cadastro-final',
                'data' => $post,
                'id' => $id,
            ]);
            return null;
        }

        $post['created_at'] = date('Y-m-d H:i:s');
        
        $id = $solicitacao->insert($post);

        echo json_encode([
            'success' => true,
            'next' => $steps[$post['tipo_empree']],
            'data' => $post,
            'id' => $id,
        ]);
    }
} catch (\Throwable $th) {
    echo json_encode([
        'error' => $th->getMessage(),
        'code' => $th->getCode(),
        'trance' => 'Line:' . $th->getLine() . '❗ Trance:' . $th->getTraceAsString(),
    ]);
}
