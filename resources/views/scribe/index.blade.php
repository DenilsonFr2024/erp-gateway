<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Ulimbo ERP Gateway — API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://erp-gateway.test:8888";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Pesquisar">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introducao" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introducao">
                    <a href="#introducao">Introdução</a>
                </li>
                                    <ul id="tocify-subheader-introducao" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="url-base">
                                <a href="#url-base">URL base</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="formato-das-respostas">
                                <a href="#formato-das-respostas">Formato das respostas</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="identificacao-dos-pedidos">
                                <a href="#identificacao-dos-pedidos">Identificação dos pedidos</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacao">
                                <a href="#autenticacao">Autenticação</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="utilizacao-da-documentacao">
                                <a href="#utilizacao-da-documentacao">Utilização da documentação</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-autenticacao-dos-pedidos" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticacao-dos-pedidos">
                    <a href="#autenticacao-dos-pedidos">Autenticação dos pedidos</a>
                </li>
                            </ul>
                    <ul id="tocify-header-sistema" class="tocify-header">
                <li class="tocify-item level-1" data-unique="sistema">
                    <a href="#sistema">Sistema</a>
                </li>
                                    <ul id="tocify-subheader-sistema" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="sistema-GETapi-v1-health">
                                <a href="#sistema-GETapi-v1-health">Verificar integridade do gateway</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">Ver coleção do Postman</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">Ver especificação OpenAPI</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentação gerada com Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Última atualização: 11/10/2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introducao">Introdução</h1>
<p>Bem-vindo à documentação oficial da API do <strong>Ulimbo ERP Gateway</strong>.</p>
<p>O Ulimbo ERP Gateway é a camada de comunicação central do ecossistema Ulimbo ERP. O seu objetivo é facilitar a comunicação entre os diferentes módulos do sistema através de endpoints HTTP e respostas JSON padronizadas.</p>
<p>Esta documentação descreve os endpoints disponíveis, os parâmetros aceites, os formatos de resposta, os códigos de erro e os metadados utilizados na comunicação entre serviços.</p>
<h2 id="url-base">URL base</h2>
<p><code>http://erp-gateway.test:8888</code></p>
<p>Esta é a URL do ambiente local de desenvolvimento. Em produção, deverá ser utilizada a URL oficial do ambiente de produção.</p>
<h2 id="formato-das-respostas">Formato das respostas</h2>
<p>As respostas da API seguem uma estrutura padronizada:</p>
<ul>
<li><code>success</code>: indica se a operação foi bem-sucedida.</li>
<li><code>data</code>: contém os dados devolvidos pela operação.</li>
<li><code>error</code>: contém informações sobre o erro, quando aplicável.</li>
<li><code>meta</code>: contém metadados, incluindo o identificador do pedido, a data e hora da resposta e a versão da API.</li>
</ul>
<h2 id="identificacao-dos-pedidos">Identificação dos pedidos</h2>
<p>A API utiliza o cabeçalho <code>X-Request-ID</code> para identificar cada pedido. Quando fornecido com um formato válido, o identificador é preservado; caso contrário, é gerado um novo identificador.</p>
<p>O identificador devolvido no cabeçalho da resposta corresponde ao valor apresentado em <code>meta.request_id</code>.</p>
<h2 id="autenticacao">Autenticação</h2>
<p>A autenticação ainda não está implementada. Os endpoints documentados devem ser considerados endpoints de desenvolvimento até serem aplicadas as medidas de autenticação e autorização necessárias.</p>
<h2 id="utilizacao-da-documentacao">Utilização da documentação</h2>
<p>Cada endpoint apresenta a sua finalidade, exemplos de pedidos e respostas e, quando aplicável, os parâmetros e cabeçalhos necessários.</p>

        <h1 id="autenticacao-dos-pedidos">Autenticação dos pedidos</h1>
<p>Esta API ainda não utiliza autenticação.</p>

        <h1 id="sistema">Sistema</h1>

    <p>Pontos de extremidade para monitorizar o Ulimbo ERP Gateway.</p>

                                <h2 id="sistema-GETapi-v1-health">Verificar integridade do gateway</h2>

<p>
</p>

<p>Verifica se o Ulimbo ERP Gateway está a responder
e devolve informações básicas sobre o serviço.</p>

<span id="example-requests-GETapi-v1-health">
<blockquote>Exemplo de pedido:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://erp-gateway.test:8888/api/v1/health" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://erp-gateway.test:8888/api/v1/health"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-health">
            <blockquote>
            <p>Exemplo de resposta (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;service&quot;: &quot;ERPGateway&quot;,
        &quot;status&quot;: &quot;healthy&quot;,
        &quot;version&quot;: &quot;1.0.0&quot;,
        &quot;uptime&quot;: &quot;operational&quot;
    },
    &quot;meta&quot;: {
        &quot;request_id&quot;: &quot;ULIMBO-TEST-001&quot;,
        &quot;timestamp&quot;: &quot;2026-10-11T02:00:00+00:00&quot;,
        &quot;version&quot;: &quot;v1&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-health" hidden>
    <blockquote>Resposta recebida<span
                id="execution-response-status-GETapi-v1-health"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-health"
      data-empty-response-text="<Resposta vazia>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-health" hidden>
    <blockquote>O pedido falhou com o erro:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-health">

Verifique se tem ligação à rede.
Se for responsável pela API, confirme que o serviço está em execução e que o CORS está configurado.
Pode consultar a consola das ferramentas de desenvolvimento do navegador para obter informações de diagnóstico.</code></pre>
</span>
<form id="form-GETapi-v1-health" data-method="GET"
      data-path="api/v1/health"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-health', this);">
    <h3>
        Pedido&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-health"
                    onclick="tryItOut('GETapi-v1-health');">Experimentar ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-health"
                    onclick="cancelTryOut('GETapi-v1-health');" hidden>Cancelar 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-health"
                    data-initial-text="Enviar pedido 💥"
                    data-loading-text="⏱ A enviar..."
                    hidden>Enviar pedido 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/health</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Cabeçalhos</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-health"
               value="application/json"
               data-component="header">
    <br>
<p>Exemplo: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-health"
               value="application/json"
               data-component="header">
    <br>
<p>Exemplo: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
