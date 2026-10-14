<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#080d18">
<meta name="description" content="Portal de APIs da Ulimbo Tech — documentação, integração e ecossistema digital.">
<title>Ulimbo Tech APIs — Developer Portal</title>
<style>
:root{
  --bg:#080d18;
  --panel:#101827;
  --panel2:#0c1422;
  --line:#253247;
  --line-soft:#ffffff12;
  --text:#edf3ff;
  --muted:#9eacc2;
  --lime:#b8f36b;
  --cyan:#68e1e8;
  --purple:#a89bff;
  --red:#ff8b8b;
  --radius:15px;
  --radius-sm:10px;
  --shadow:0 30px 80px #0005;
  --transition:.18s ease;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth;scroll-padding-top:90px}
body{
  margin:0;
  background:radial-gradient(ellipse at 80% -10%,#173044 0,transparent 36%),var(--bg);
  color:var(--text);
  font:15px/1.6 Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  -webkit-font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
button{font:inherit}
::selection{background:var(--lime);color:#07101a}

.wrap{max-width:1180px;margin:auto;padding:0 26px}

/* ---------- Topbar ---------- */
.topbar{
  position:sticky;top:0;z-index:50;
  height:76px;
  border-bottom:1px solid var(--line-soft);
  display:flex;align-items:center;justify-content:space-between;gap:20px;
  background:rgba(8,13,24,.82);
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
}
.brand{display:flex;align-items:center;gap:12px;font-weight:800;letter-spacing:-.4px}
.logo{
  width:38px;height:38px;border-radius:12px;
  background:linear-gradient(135deg,var(--lime),var(--cyan));
  color:#07101a;display:grid;place-items:center;
  font-weight:950;font-size:18px;
  transition:transform var(--transition);
}
.brand:hover .logo{transform:rotate(-6deg) scale(1.05)}
.brand small{display:block;color:var(--muted);font-size:11px;font-weight:600;letter-spacing:1.6px;text-transform:uppercase}
.nav{display:flex;gap:24px;color:var(--muted);font-size:13px}
.nav a{position:relative;transition:color var(--transition)}
.nav a::after{
  content:"";position:absolute;left:0;bottom:-6px;height:2px;width:0;
  background:var(--lime);transition:width var(--transition);
}
.nav a:hover{color:var(--lime)}
.nav a:hover::after{width:100%}
.status{
  font-size:12px;color:var(--lime);
  border:1px solid #b8f36b55;background:#b8f36b0d;
  padding:7px 11px;border-radius:99px;
  display:inline-flex;align-items:center;gap:7px;
  transition:background var(--transition);
}
.status:hover{background:#b8f36b1a}
.status .pulse{
  width:7px;height:7px;border-radius:50%;background:var(--lime);
  box-shadow:0 0 0 0 #b8f36b99;
  animation:pulse 2s infinite;
}
@keyframes pulse{
  0%{box-shadow:0 0 0 0 #b8f36b99}
  70%{box-shadow:0 0 0 8px #b8f36b00}
  100%{box-shadow:0 0 0 0 #b8f36b00}
}

/* ---------- Hero ---------- */
.hero{
  padding:84px 0 60px;
  display:grid;grid-template-columns:1.1fr .9fr;gap:54px;align-items:center;
}
.eyebrow{color:var(--lime);font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase}
.hero h1{
  font-size:clamp(40px,5.3vw,68px);line-height:1.02;letter-spacing:-3px;
  margin:18px 0;
  background:linear-gradient(180deg,#fff,#c9d7e9);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.hero h1 span{
  background:linear-gradient(135deg,var(--lime),var(--cyan));
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.lead{font-size:17px;color:var(--muted);max-width:610px}
.actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}
.btn{
  border:1px solid var(--line);border-radius:var(--radius-sm);
  padding:11px 17px;font-weight:750;font-size:13px;
  display:inline-flex;align-items:center;gap:8px;cursor:pointer;
  transition:transform var(--transition),background var(--transition),border-color var(--transition);
}
.btn.primary{background:var(--lime);border-color:var(--lime);color:#09111b}
.btn.primary:hover{background:#c6ff7d;border-color:#c6ff7d}
.btn:hover{transform:translateY(-2px)}
.btn:focus-visible{outline:2px solid var(--cyan);outline-offset:3px}

.hero-card{
  border:1px solid #31445b;
  background:linear-gradient(150deg,#121e30,#0b1320);
  border-radius:18px;overflow:hidden;box-shadow:var(--shadow);
  transition:transform .3s ease,box-shadow .3s ease;
}
.hero-card:hover{transform:translateY(-4px);box-shadow:0 40px 90px #0007}
.window{
  height:46px;border-bottom:1px solid var(--line);
  display:flex;align-items:center;padding:0 16px;gap:7px;
  color:var(--muted);font-size:12px;
}
.dot{height:8px;width:8px;border-radius:50%;background:#46546a}
.dot:nth-child(1){background:#ff8b8b}
.dot:nth-child(2){background:#ffd479}
.dot:nth-child(3){background:var(--lime)}
.window span:last-child{margin-left:8px}
.code{
  padding:22px;
  font:12px/1.9 ui-monospace,SFMono-Regular,Menlo,monospace;
  overflow:auto;color:#c9d7e9;
}
.code::-webkit-scrollbar{height:6px;width:6px}
.code::-webkit-scrollbar-thumb{background:#2a3a52;border-radius:99px}
.green{color:var(--lime)}
.blue{color:#83d9ff}
.violet{color:#c1b5ff}
.orange{color:#ffbf83}

/* ---------- Sections ---------- */
.section{padding:48px 0}
.section-head{
  display:flex;justify-content:space-between;gap:24px;align-items:end;
  margin-bottom:24px;
}
.section h2{font-size:30px;letter-spacing:-1px;margin:7px 0}
.section p.sub{color:var(--muted);margin:0;max-width:650px}

/* ---------- Cards grid ---------- */
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:15px}
.card{
  background:linear-gradient(145deg,#111b2b,#0c1421);
  border:1px solid var(--line);
  border-radius:var(--radius);
  padding:22px;
  transition:transform var(--transition),border-color var(--transition),box-shadow var(--transition);
}
.card:hover{
  transform:translateY(-4px);
  border-color:#3a4d68;
  box-shadow:0 18px 40px #0005;
}
.card .icon{
  height:38px;width:38px;border-radius:11px;
  display:grid;place-items:center;
  background:#b8f36b14;color:var(--lime);
  font-weight:900;margin-bottom:17px;
  font-size:17px;
}
.card h3{margin:0 0 8px;font-size:17px}
.card p{margin:0;color:var(--muted);font-size:13px}
.card code{color:var(--cyan)}
.tag{
  display:inline-block;margin-top:17px;
  border:1px solid var(--line);
  padding:4px 8px;border-radius:6px;
  color:#b9c8dc;font-size:11px;
}

/* ---------- Docs ---------- */
.docs{display:grid;grid-template-columns:240px 1fr;gap:22px}
.sidebar,.docpanel{
  border:1px solid var(--line);
  border-radius:var(--radius);
  background:#0d1523;
}
.sidebar{padding:18px;height:max-content;position:sticky;top:96px}
.sidebar strong{
  display:block;font-size:11px;letter-spacing:1.4px;
  color:#75869e;margin:5px 9px 12px;text-transform:uppercase;
}
.sidebar a{
  display:block;color:var(--muted);
  padding:9px 10px;border-radius:7px;font-size:13px;
  transition:background var(--transition),color var(--transition);
}
.sidebar a:hover,.sidebar a.active{background:#b8f36b12;color:var(--lime)}
.sidebar a.active{font-weight:700}
.docpanel{padding:24px;min-width:0}
.docpanel h3{margin:0 0 6px;font-size:21px;letter-spacing:-.4px}
.docpanel>p{color:var(--muted);margin-top:0}

.endpoint{
  display:flex;align-items:center;gap:12px;flex-wrap:wrap;
  padding:12px;background:#080f1b;
  border:1px solid var(--line);
  border-radius:9px;margin:18px 0;
  font:12px ui-monospace,monospace;
}
.method{
  font-weight:900;color:#07101a;background:var(--lime);
  padding:5px 8px;border-radius:5px;
  letter-spacing:.5px;
}
.endpoint code{overflow-wrap:anywhere;color:var(--cyan)}
.copy{
  margin-left:auto;background:#172235;
  border:1px solid #34445b;color:var(--text);
  padding:6px 9px;border-radius:6px;cursor:pointer;
  font-size:11px;
  transition:background var(--transition),border-color var(--transition),transform var(--transition);
}
.copy:hover{background:#1e2c44;border-color:#4a5e7a}
.copy:active{transform:scale(.96)}
.copy:focus-visible{outline:2px solid var(--cyan);outline-offset:2px}

.codeblock{
  position:relative;background:#070c14;
  border:1px solid #1f2c3e;border-radius:var(--radius-sm);
  padding:17px;overflow:auto;
  font:12px/1.8 ui-monospace,monospace;
  color:#c9d7e9;white-space:pre;
}
.codeblock::-webkit-scrollbar{height:6px;width:6px}
.codeblock::-webkit-scrollbar-thumb{background:#2a3a52;border-radius:99px}

.label{
  color:#75869e;font-size:11px;letter-spacing:1.2px;
  text-transform:uppercase;font-weight:800;margin:21px 0 8px;
}
.note{
  border-left:3px solid var(--cyan);
  background:#68e1e80b;
  padding:12px 15px;color:#b8c7db;font-size:13px;
  margin-top:18px;border-radius:0 8px 8px 0;
}

/* ---------- Table ---------- */
.table-wrap{
  overflow-x:auto;border:1px solid var(--line);
  border-radius:12px;
}
.table-wrap::-webkit-scrollbar{height:6px}
.table-wrap::-webkit-scrollbar-thumb{background:#2a3a52;border-radius:99px}
table{border-collapse:collapse;width:100%;min-width:580px;font-size:13px}
th,td{text-align:left;padding:13px 15px;border-bottom:1px solid var(--line)}
th{
  color:#9baec7;background:#101a2a;font-size:11px;
  text-transform:uppercase;letter-spacing:1px;
}
td{color:#c9d6e7}
tr:last-child td{border-bottom:0}
tbody tr{transition:background var(--transition)}
tbody tr:hover{background:#ffffff04}
td code{color:var(--cyan)}
.pill{
  font-size:10px;color:var(--lime);background:#b8f36b12;
  padding:4px 7px;border-radius:5px;
  letter-spacing:.5px;font-weight:700;
}

/* ---------- Roadmap ---------- */
.roadmap{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.step{
  padding:17px;border:1px solid var(--line);
  border-radius:12px;background:#0e1725;
  transition:transform var(--transition),border-color var(--transition);
  position:relative;overflow:hidden;
}
.step::before{
  content:"";position:absolute;top:0;left:0;height:2px;width:0;
  background:linear-gradient(90deg,var(--lime),var(--cyan));
  transition:width .35s ease;
}
.step:hover{transform:translateY(-3px);border-color:#3a4d68}
.step:hover::before{width:100%}
.step b{display:block;margin:9px 0 5px;font-size:15px}
.step p{color:var(--muted);font-size:12px;margin:0}
.step .num{color:var(--lime);font:12px ui-monospace,monospace;letter-spacing:1px}

/* ---------- Footer ---------- */
.footer{
  border-top:1px solid var(--line-soft);
  margin-top:40px;padding:27px 0;
  color:var(--muted);font-size:12px;
  display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;
}
.footer a:hover{color:var(--lime)}

/* ---------- Toast ---------- */
.toast{
  position:fixed;bottom:24px;left:50%;transform:translate(-50%,80px);
  background:var(--lime);color:#07101a;
  padding:10px 18px;border-radius:99px;
  font-size:13px;font-weight:700;
  box-shadow:0 12px 30px #0006;
  opacity:0;pointer-events:none;
  transition:opacity .25s ease,transform .25s ease;
  z-index:100;
}
.toast.show{opacity:1;transform:translate(-50%,0)}

/* ---------- Responsive ---------- */
@media(max-width:850px){
  .hero{grid-template-columns:1fr;padding-top:58px;gap:30px}
  .grid{grid-template-columns:1fr 1fr}
  .docs{grid-template-columns:1fr}
  .sidebar{
    display:flex;gap:5px;overflow:auto;position:static;
    -ms-overflow-style:none;scrollbar-width:none;
  }
  .sidebar::-webkit-scrollbar{display:none}
  .sidebar strong{display:none}
  .sidebar a{white-space:nowrap}
  .roadmap{grid-template-columns:1fr 1fr}
  .nav{display:none}
}
@media(max-width:560px){
  .wrap{padding:0 17px}
  .hero h1{letter-spacing:-2px}
  .grid,.roadmap{grid-template-columns:1fr}
  .section-head{display:block}
  .docpanel{padding:17px}
  .endpoint{align-items:flex-start}
  .copy{margin-left:0}
  .topbar{height:auto;padding:14px 0;flex-wrap:wrap;gap:10px}
  .status{order:3;width:100%;justify-content:center}
}
@media(prefers-reduced-motion:reduce){
  *{animation:none!important;transition:none!important}
  html{scroll-behavior:auto}
}
</style>
</head>
<body>
<header class="wrap topbar">
  <a class="brand" href="#inicio" aria-label="Ulimbo Tech — início">
    <div class="logo" aria-hidden="true">U</div>
    <div>ULIMBO TECH<small>API DEVELOPER PORTAL</small></div>
  </a>
  <nav class="nav" aria-label="Navegação principal">
    <a href="#apis">APIs</a>
    <a href="#documentacao">Documentação</a>
    <a href="#respostas">Respostas</a>
    <a href="#roadmap">Roadmap</a>
  </nav>
  <a class="status" href="#documentacao">
    <span class="pulse" aria-hidden="true"></span>API v1 · Em desenvolvimento
  </a>
</header>

<main>
<section class="wrap hero" id="inicio">
  <div>
    <div class="eyebrow">Construir. Integrar. Escalar.</div>
    <h1>Uma plataforma.<br><span>Novas possibilidades.</span></h1>
    <p class="lead">As APIs da Ulimbo Tech ligam aplicações, serviços e dados num ecossistema preparado para empresas que precisam de crescer — com uma arquitetura modular e atenção especial à conectividade limitada.</p>
    <div class="actions">
      <a class="btn primary" href="#documentacao">Explorar documentação ↗</a>
      <a class="btn" href="#apis">Conhecer as APIs ↓</a>
    </div>
  </div>
  <div class="hero-card" aria-label="Exemplo de pedido REST">
    <div class="window">
      <i class="dot" aria-hidden="true"></i><i class="dot" aria-hidden="true"></i><i class="dot" aria-hidden="true"></i>
      <span>pedido de exemplo · REST</span>
    </div>
    <div class="code"><span class="green">GET</span> /api/v1/health<br><br><span class="violet">// Resposta ilustrativa do Gateway</span><br>{<br>&nbsp; <span class="blue">"success"</span>: <span class="orange">true</span>,<br>&nbsp; <span class="blue">"data"</span>: {<br>&nbsp;&nbsp;&nbsp; <span class="blue">"service"</span>: <span class="green">"ERPGateway"</span>,<br>&nbsp;&nbsp;&nbsp; <span class="blue">"status"</span>: <span class="green">"healthy"</span>,<br>&nbsp;&nbsp;&nbsp; <span class="blue">"version"</span>: <span class="green">"1.0.0"</span><br>&nbsp; },<br>&nbsp; <span class="blue">"meta"</span>: {<br>&nbsp;&nbsp;&nbsp; <span class="blue">"request_id"</span>: <span class="green">"..."</span>,<br>&nbsp;&nbsp;&nbsp; <span class="blue">"version"</span>: <span class="green">"v1"</span><br>&nbsp; }<br>}</div>
  </div>
</section>

<section class="wrap section" id="apis">
  <div class="section-head">
    <div>
      <div class="eyebrow">Ecossistema</div>
      <h2>APIs para cada necessidade</h2>
      <p class="sub">Uma visão do ecossistema planeado. Os serviços abaixo representam áreas de produto; nem todos estão disponíveis para integração pública.</p>
    </div>
  </div>
  <div class="grid">
    <article class="card">
      <div class="icon" aria-hidden="true">↔</div>
      <h3>Ulimbo ERP Gateway</h3>
      <p>Ponto de entrada para APIs, convenções de resposta consistentes e identificação de pedidos. Base técnica para integrar os módulos do ERP.</p>
      <span class="tag">Base técnica em desenvolvimento</span>
    </article>
    <article class="card">
      <div class="icon" aria-hidden="true">▤</div>
      <h3>ERP &amp; Gestão</h3>
      <p>Futura integração com faturação, vendas, stock, compras, finanças e recursos humanos, respeitando permissões e regras de negócio.</p>
      <span class="tag">Planeado</span>
    </article>
    <article class="card">
      <div class="icon" aria-hidden="true">◉</div>
      <h3>Identidade &amp; Acesso</h3>
      <p>Área prevista para autenticação, autorização e gestão de identidades entre aplicações e serviços.</p>
      <span class="tag">Planeado</span>
    </article>
    <article class="card">
      <div class="icon" aria-hidden="true">⌁</div>
      <h3>Sincronização offline</h3>
      <p>Direção de arquitetura para cenários com conectividade instável: operações locais e sincronização controlada quando a ligação regressa.</p>
      <span class="tag">Em desenho</span>
    </article>
    <article class="card">
      <div class="icon" aria-hidden="true">▥</div>
      <h3>Relatórios &amp; Dados</h3>
      <p>Futura camada de consulta e análise para indicadores operacionais, financeiros e relatórios de gestão.</p>
      <span class="tag">Planeado</span>
    </article>
    <article class="card">
      <div class="icon" aria-hidden="true">✳</div>
      <h3>Integrações externas</h3>
      <p>Espaço para integrações com parceiros e serviços externos. Cada integração será publicada após validação técnica e regulamentar.</p>
      <span class="tag">A definir</span>
    </article>
  </div>
</section>

<section class="wrap section" id="documentacao">
  <div class="section-head">
    <div>
      <div class="eyebrow">Começar</div>
      <h2>Documentação da API</h2>
      <p class="sub">Exemplo baseado no contrato atual do ERPGateway. Ajuste o endereço-base ao ambiente local ou de teste configurado.</p>
    </div>
  </div>
  <div class="docs">
    <aside class="sidebar" aria-label="Índice da documentação">
      <strong>Nesta página</strong>
      <a class="active" href="#base">URL base</a>
      <a href="#health">Health check</a>
      <a href="#contrato">Formato de resposta</a>
      <a href="#erros">Erros e validação</a>
    </aside>
    <div class="docpanel">
      <div id="base">
        <h3>URL base</h3>
        <p>O Gateway usa o prefixo de versão <code>/api/v1</code>. Em desenvolvimento local, o endereço configurado é:</p>
        <div class="endpoint">
          <span class="pill">BASE URL</span>
          <code id="baseurl">http://erp-gateway.test:8888/api/v1</code>
          <button class="copy" type="button" data-copy="http://erp-gateway.test:8888/api/v1">Copiar</button>
        </div>
        <div class="note">Este endereço é local e serve apenas como exemplo de desenvolvimento. Antes de publicar a API, configure HTTPS, domínio de ambiente e controlos de acesso.</div>
      </div>

      <div id="health" style="margin-top:30px">
        <h3>Health check</h3>
        <p>Endpoint de diagnóstico para confirmar que o Gateway responde.</p>
        <div class="endpoint">
          <span class="method">GET</span>
          <code>/api/v1/health</code>
          <button class="copy" type="button" data-copy="curl -i http://erp-gateway.test:8888/api/v1/health">Copiar cURL</button>
        </div>
        <div class="label">Pedido</div>
        <pre class="codeblock">curl -i http://erp-gateway.test:8888/api/v1/health</pre>
        <div class="label">Exemplo de resposta</div>
        <pre class="codeblock">{
  "success": true,
  "data": {
    "service": "ERPGateway",
    "status": "healthy",
    "version": "1.0.0"
  },
  "meta": {
    "request_id": "identificador-do-pedido",
    "timestamp": "2026-10-13T09:00:00+00:00",
    "version": "v1"
  }
}</pre>
      </div>

      <div id="contrato" style="margin-top:30px">
        <h3>Contrato de resposta</h3>
        <p>As respostas da API seguem um envelope comum para tornar os resultados previsíveis entre endpoints.</p>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Campo</th><th>Tipo</th><th>Finalidade</th></tr>
            </thead>
            <tbody>
              <tr><td><code>success</code></td><td>boolean</td><td>Indica se a operação foi bem-sucedida.</td></tr>
              <tr><td><code>data</code></td><td>object / array / null</td><td>Resultado principal da operação.</td></tr>
              <tr><td><code>error</code></td><td>object / null</td><td>Informação de erro quando aplicável.</td></tr>
              <tr><td><code>meta</code></td><td>object</td><td>Metadados, como request_id, timestamp e versão.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="note">O contrato de sucesso usa <code>success</code>, <code>data</code> e <code>meta</code>. Em respostas de erro, o Gateway pode incluir também o objeto <code>error</code>. Consulte a implementação real de cada endpoint.</div>
      </div>

      <div id="erros" style="margin-top:30px">
        <h3>Erros e validação</h3>
        <p>Os endpoints de teste ajudam a verificar o tratamento de erros durante o desenvolvimento. Não devem ser expostos publicamente em produção sem revisão.</p>
        <div class="endpoint">
          <span class="method">GET</span>
          <code>/api/v1/test-error</code>
          <button class="copy" type="button" data-copy="/api/v1/test-error">Copiar rota</button>
        </div>
        <div class="endpoint">
          <span class="method">GET</span>
          <code>/api/v1/test-validation</code>
          <button class="copy" type="button" data-copy="/api/v1/test-validation">Copiar rota</button>
        </div>
        <div class="label">Exemplo de erro</div>
        <pre class="codeblock">{
  "success": false,
  "data": null,
  "error": {
    "code": "RESOURCE_NOT_FOUND",
    "message": "Resource not found.",
    "details": null
  },
  "meta": {
    "request_id": "identificador-do-pedido",
    "timestamp": "2026-10-13T09:00:00+00:00",
    "version": "v1"
  }
}</pre>
      </div>
    </div>
  </div>
</section>

<section class="wrap section" id="respostas">
  <div class="section-head">
    <div>
      <div class="eyebrow">Consistência</div>
      <h2>Princípios de integração</h2>
      <p class="sub">A documentação pública deve refletir apenas endpoints efetivamente implementados e testados.</p>
    </div>
  </div>
  <div class="grid">
    <article class="card"><h3>Versionamento</h3><p>Prefixo atual: <code>/api/v1</code>. Mudanças incompatíveis devem ser planeadas numa nova versão.</p></article>
    <article class="card"><h3>Rastreabilidade</h3><p>O Gateway usa identificadores de pedido para ajudar a correlacionar chamadas e diagnosticar problemas.</p></article>
    <article class="card"><h3>Segurança primeiro</h3><p>Antes da produção: HTTPS, autenticação, autorização, limites de pedidos, gestão de segredos e registos sem dados sensíveis.</p></article>
  </div>
</section>

<section class="wrap section" id="roadmap">
  <div class="section-head">
    <div>
      <div class="eyebrow">Evolução</div>
      <h2>Da base técnica ao ecossistema</h2>
    </div>
  </div>
  <div class="roadmap">
    <div class="step"><span class="num">FASE 01</span><b>Gateway e contrato</b><p>Rotas versionadas, formato JSON consistente e identificação de pedidos.</p></div>
    <div class="step"><span class="num">FASE 02</span><b>Segurança e acesso</b><p>Autenticação, autorização, políticas de acesso e proteção de endpoints.</p></div>
    <div class="step"><span class="num">FASE 03</span><b>APIs do ERP</b><p>Publicação gradual de módulos com validação, testes e documentação.</p></div>
    <div class="step"><span class="num">FASE 04</span><b>Parceiros e escala</b><p>Integrações externas, observabilidade e ambientes de produção.</p></div>
  </div>
</section>
</main>

<footer class="wrap footer">
  <span>© 2026 Ulimbo Tech · APIs &amp; Developer Portal</span>
  <span>Documentação de apresentação — confirme rotas e políticas antes de integrar.</span>
</footer>

<div class="toast" id="toast" role="status" aria-live="polite">Copiado ✓</div>

<script>
(function(){
  const toast = document.getElementById('toast');
  let toastTimer;

  function showToast(msg){
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(()=>toast.classList.remove('show'),1500);
  }

  async function copyText(value){
    try{
      if(navigator.clipboard && window.isSecureContext){
        await navigator.clipboard.writeText(value);
      }else{
        const area = document.createElement('textarea');
        area.value = value;
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        document.execCommand('copy');
        area.remove();
      }
      showToast('Copiado ✓');
    }catch(e){
      showToast('Selecione e copie manualmente');
    }
  }

  document.querySelectorAll('[data-copy]').forEach(btn=>{
    btn.addEventListener('click',()=>copyText(btn.dataset.copy));
  });

  // Sidebar active state on scroll
  const sections = ['base','health','contrato','erros'];
  const links = document.querySelectorAll('.sidebar a');
  const observer = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        links.forEach(l=>l.classList.toggle('active', l.getAttribute('href') === '#' + entry.target.id));
      }
    });
  },{rootMargin:'-30% 0px -60% 0px'});
  sections.forEach(id=>{
    const el = document.getElementById(id);
    if(el) observer.observe(el);
  });
})();
</script>
</body>
</html>