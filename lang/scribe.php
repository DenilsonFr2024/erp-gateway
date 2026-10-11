<?php

return [
    'labels' => [
        'search' => 'Pesquisar',
        'base_url' => 'URL base',
    ],

    'auth' => [
        'none' => 'Esta API ainda não utiliza autenticação.',
        'instruction' => [
            'query' => <<<'TEXT'
Para autenticar os pedidos, inclua o parâmetro **`:parameterName`** na consulta URL.
TEXT,
            'body' => <<<'TEXT'
Para autenticar os pedidos, inclua o parâmetro **`:parameterName`** no corpo do pedido.
TEXT,
            'query_or_body' => <<<'TEXT'
Para autenticar os pedidos, inclua o parâmetro **`:parameterName`** na consulta URL ou no corpo do pedido.
TEXT,
            'bearer' => <<<'TEXT'
Para autenticar os pedidos, inclua o cabeçalho **`Authorization`** com o valor **`"Bearer :placeholder"`**.
TEXT,
            'basic' => <<<'TEXT'
Para autenticar os pedidos, inclua um cabeçalho **`Authorization`** no formato **`"Basic {credentials}"**.
O valor de `{credentials}` deve conter o nome de utilizador ou identificador e a palavra-passe, unidos por dois pontos (:), e codificados em Base64.
TEXT,
            'header' => <<<'TEXT'
Para autenticar os pedidos, inclua o cabeçalho **`:parameterName`** com o valor **`":placeholder"`**.
TEXT,
        ],
        'details' => <<<'TEXT'
Os endpoints que exigem autenticação estão identificados com a indicação correspondente na documentação.
TEXT,
    ],

    'headings' => [
        'introduction' => 'Introdução',
        'auth' => 'Autenticação dos pedidos',
    ],

    'endpoint' => [
        'request' => 'Pedido',
        'headers' => 'Cabeçalhos',
        'url_parameters' => 'Parâmetros da URL',
        'body_parameters' => 'Parâmetros do corpo',
        'query_parameters' => 'Parâmetros da consulta',
        'response' => 'Resposta',
        'response_fields' => 'Campos da resposta',
        'example_request' => 'Exemplo de pedido',
        'example_response' => 'Exemplo de resposta',
        'responses' => [
            'binary' => 'Dados binários',
            'empty' => 'Resposta vazia',
        ],
    ],

    'try_it_out' => [
        'open' => 'Experimentar ⚡',
        'cancel' => 'Cancelar 🛑',
        'send' => 'Enviar pedido 💥',
        'loading' => '⏱ A enviar...',
        'received_response' => 'Resposta recebida',
        'request_failed' => 'O pedido falhou com o erro',
        'error_help' => <<<'TEXT'
Verifique se tem ligação à rede.
Se for responsável pela API, confirme que o serviço está em execução e que o CORS está configurado.
Pode consultar a consola das ferramentas de desenvolvimento do navegador para obter informações de diagnóstico.
TEXT,
    ],

    'links' => [
        'postman' => 'Ver coleção do Postman',
        'openapi' => 'Ver especificação OpenAPI',
    ],
];
