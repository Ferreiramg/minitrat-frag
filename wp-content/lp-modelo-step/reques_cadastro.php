<?php

require_once dirname(dirname(__DIR__)) . '/wp-config.php';

require_once dirname(dirname(__DIR__)) . '/wp-load.php';

require_once __DIR__ . "/app.php";

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = '6f69441906f23f';
    $phpmailer->Password = 'd5657604883d56';
}

add_action('phpmailer_init', 'mailtrap');

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
    $content = trim(file_get_contents(__DIR__ . '/mail-agradecimento.html'));

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Seu Nome <luis@email.com>',
    );

    $pdo = Model::connect();

    $solicitacao = new Solicitacao($pdo);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT, true);

        $id = $post['id'];

        unset($post['id']);

        if ($solicitacao->update($post, $id)) {

            wp_mail($post['email'], 'A Minitrat agradece o seu interesse!', $content, $headers);

            echo json_encode([
                'success' => true,
                'data' => $post,
                'id' => $id,
            ]);
        }
    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode([
        'error' => $th->getMessage(),
        'code' => $th->getCode(),
        'trance' => 'Line:' . $th->getLine() . '❗ Trance:' . $th->getTraceAsString(),
    ]);
}
