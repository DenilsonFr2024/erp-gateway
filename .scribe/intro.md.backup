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