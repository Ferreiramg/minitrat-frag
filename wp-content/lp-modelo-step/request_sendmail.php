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

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mail.minitrat.com.br';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 587; // Use a porta apropriada para TLS
    $phpmailer->SMTPSecure = ''; // Defina como vazio para desativar o uso de SSL/TLS
    $phpmailer->Username = 'modelo.ideal@minitrat.com.br';
    $phpmailer->Password = 'XL8^cU=bE#0f';
}

add_action('phpmailer_init', 'mailtrap');

$map_keys = [
    'tipo_empree' => 'Tipo de empreendimento',
    'fase_empree' => 'Fase do empreendimento',
    'padrao' => 'Padrão',
    'modelo_empree' => 'Modelo de empreendimento',
    'total_empree' => 'Total de empreendimentos',
    'distancia_ponto' => 'Distância do ponto de energia',
    'turno_um' => 'Turno 1',
    'turno_dois' => 'Turno 2',
    'turno_tres' => 'Turno 3',
    'op_turno_um' => 'Turno 1 Operacional',
    'op_turno_dois' => 'Turno 2 Operacional',
    'op_turno_tres' => 'Turno 3 Operacional',
    'nome' => 'Nome',
    'telefone' => 'Telefone',
    'email' => 'E-mail',
    'uf' => 'UF',
    'cidade' => 'Cidade',
    'receber_notificacao' => 'Receber notificação',
    'created_at' => 'Data de envio',
];
$map_keys_json = [
    'setor' => 'Setor',
    'hora_fim' => 'Fim do turno',
    'hora_inicio' => 'Início do turno',
    'numero_refeicoes' => 'Número de refeições',
    'numero_funcionarios' => 'Número de funcionários',
    'abertura_horas' => 'Abre às',
    'fecha_horas' => 'Fecha às',
    'numero_visitantes' => 'Número de visitantes',

];
$map_empree = [
    'R' => 'Residêncial',
    'C' => 'Comercial',
    'I' => 'Industrial',
];
$map_fase_empree = [
    'I' => 'Inícial',
    'M' => 'Intermediária',
    'F' => 'Final',
];
$map_padrao = [
    'A' => 'Alto',
    'M' => 'Médio',
    'B' => 'Baixo',
];

function extractJson($data, $map_keys_json)
{
    $json = json_decode($data, true);

    if (!is_array($json)) return htmlspecialchars($data);

    if (isset($json['numero_funcionarios']) && empty($json['numero_funcionarios'])) return "Sem Informações";

    if (isset($json['abertura_horas'])) {
        $json['abertura_horas'] = $json['abertura_horas'] . ':' . $json['abertura_minutos'];
        unset($json['abertura_minutos']);
    }

    if (isset($json['fecha_horas'])) {
        $json['fecha_horas'] = $json['fecha_horas'] . ':' . $json['fecha_minutos'];
        unset($json['fecha_minutos']);
    }

    $html = '<table>';

    ksort($json);

    foreach ($json as $chave => $valor) {
        $html .= '<tr>';
        $html .= '<td>' . ($map_keys_json[$chave] ?? $chave) . '</td>';
        $html .= '<td>' . $valor . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    return $html;
}


validateCsrfToken();

try {


    $pdo = Model::connect();

    $solicitacao = new Solicitacao($pdo);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Minitrat | Modelo Ideal <modelo.ideal@minitrat.com.br>',
    );

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $post = filter_input_array(INPUT_GET, FILTER_DEFAULT, true);

        $id = $post['id'];

        $dados = $solicitacao->getByID($id);

        $tabelaHTML = '<table>';

        foreach ($dados as $chave => $valor) {

            if (!empty($valor) && array_key_exists($chave, $map_keys)) {

                $json = json_decode($valor, true);

                $valor = $chave == 'tipo_empree' ? $map_empree[$valor] : $valor;

                $valor = $chave == 'fase_empree' ? $map_fase_empree[$valor] : $valor;

                $valor = $chave == 'padrao' ? $map_padrao[$valor] : $valor;

                $tabelaHTML .= '<tr>';
                $tabelaHTML .= '<td>' . $map_keys[$chave] . '</td>';
                $tabelaHTML .= '<td>' . extractJson($valor, $map_keys_json) . '</td>';
                $tabelaHTML .= '</tr>';
            }
        }

        $tabelaHTML .= '</table>';

        //template
        ob_start();

        include __DIR__ . '/email-notifica-gestao.php';

        $html = ob_get_clean();
        //sendmail
        wp_mail("modelo.ideal@minitrat.com.br", 'Nova Solicitação de Serviço', $html, $headers);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode([
        'error' => $th->getMessage(),
        'code' => $th->getCode(),
        'trance' => 'Line:' . $th->getLine() . '❗ Trance:' . $th->getTraceAsString(),
    ]);
}
