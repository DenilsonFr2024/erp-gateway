# Introdução

Bem-vindo à documentação oficial da API do **Ulimbo ERP Gateway**.

O Ulimbo ERP Gateway é a camada de comunicação central do ecossistema Ulimbo ERP. O seu objetivo é facilitar a comunicação entre os diferentes módulos do sistema através de endpoints HTTP e respostas JSON padronizadas.

Esta documentação descreve os endpoints disponíveis, os parâmetros aceites, os formatos de resposta, os códigos de erro e os metadados utilizados na comunicação entre serviços.

## URL base

`http://erp-gateway.test:8888`

Esta é a URL do ambiente local de desenvolvimento. Em produção, deverá ser utilizada a URL oficial do ambiente de produção.

## Formato das respostas

As respostas da API seguem uma estrutura padronizada:

- `success`: indica se a operação foi bem-sucedida.
- `data`: contém os dados devolvidos pela operação.
- `error`: contém informações sobre o erro, quando aplicável.
- `meta`: contém metadados, incluindo o identificador do pedido, a data e hora da resposta e a versão da API.

## Identificação dos pedidos

A API utiliza o cabeçalho `X-Request-ID` para identificar cada pedido. Quando fornecido com um formato válido, o identificador é preservado; caso contrário, é gerado um novo identificador.

O identificador devolvido no cabeçalho da resposta corresponde ao valor apresentado em `meta.request_id`.

## Autenticação

A autenticação ainda não está implementada. Os endpoints documentados devem ser considerados endpoints de desenvolvimento até serem aplicadas as medidas de autenticação e autorização necessárias.

## Utilização da documentação

Cada endpoint apresenta a sua finalidade, exemplos de pedidos e respostas e, quando aplicável, os parâmetros e cabeçalhos necessários.
## Contrato base de comunicação — v1

### Métodos HTTP

- GET: consultar recursos sem os alterar.
- POST: criar recursos ou executar operações.
- PUT: substituir integralmente um recurso.
- PATCH: atualizar parcialmente um recurso.
- DELETE: solicitar a remoção de um recurso.

Os métodos efetivamente disponíveis dependem de cada endpoint. Esta lista define a convenção, não significa que todos já estejam implementados.

### Headers

- `Accept: application/json`: indica que o cliente espera uma resposta JSON.
- `Content-Type: application/json`: necessário quando o pedido contém um corpo JSON.
- `X-Request-ID`: opcional no pedido. Se estiver ausente ou for inválido, o Gateway gera um identificador e devolve-o na resposta.

O identificador no header `X-Request-ID` deve corresponder a `meta.request_id`.
Todas as respostas da API são devolvidas em JSON. O cabeçalho `Accept: application/json` é recomendado para explicitar o formato esperado pelo cliente.
### Códigos HTTP convencionados

- 200: sucesso.
- 201: recurso criado.
- 204: sucesso sem conteúdo.
- 400: pedido inválido ou regra de negócio não satisfeita.
- 401: autenticação necessária.
- 403: acesso não autorizado.
- 404: recurso não encontrado.
- 405: método não permitido.
- 409: conflito.
- 422: dados inválidos.
- 429: limite de pedidos excedido.
- 500: erro interno.
- 503: serviço indisponível.

### Metadados

As respostas JSON de sucesso e erro incluem `meta` com:

- `request_id`: identificador do pedido.
- `timestamp`: data e hora em UTC, no formato ISO 8601.
- `version`: versão do contrato, atualmente `v1`.

### Paginação

Os endpoints que devolvem listas paginadas devem aceitar:

- `page`: página solicitada; valor por defeito 1.
- `per_page`: quantidade de resultados por página; valor por defeito 20 e máximo 100.

As respostas paginadas devem incluir `meta.pagination`:

    {
      "current_page": 1,
      "per_page": 20,
      "total": 245,
      "last_page": 13,
      "has_more_pages": true
    }

Os valores são ilustrativos e devem refletir os resultados reais da consulta. Esta convenção não significa que já existam endpoints de negócio paginados.

### Estado da implementação

Este documento define o contrato pretendido. A implementação de cada regra deve ser confirmada pelos testes automatizados. A autenticação ainda não está implementada; os endpoints atuais destinam-se ao desenvolvimento.
