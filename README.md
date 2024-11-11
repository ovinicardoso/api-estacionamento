# Star Parking

O Star Parking é um sistema completo para o controle de um estacionamento. Ele utiliza um Arduino para monitoramento e controle de acesso, uma API para a comunicação e processamento dos dados, um banco de dados para armazenar informações, e uma interface web para gerenciamento e visualização em tempo real.

O projeto foi desenvolvido com a ideia de facilitar a administração de vagas de estacionamento, registrando entradas e saídas de veículos e exibindo esses dados de forma intuitiva para o usuário.

## Tecnologias Usadas

- PHP (para a API)
- MySQL (banco de dados)
- Arduino (monitoramento e controle)
- C++ (para o código do Arduino)
- HTML, CSS, JavaScript (para o front-end)
- Postman (para testes da API)
- XAMPP (para ambiente de desenvolvimento)
- Linux (sistema operacional)

## Instalação

1. **Clone o repositório**
2. **Crie um banco de dados no MySQL e execute o script de banco de dados presente no repositório para criar as tabelas necessárias**
3. **Inicie o servidor Apache e MySQL**
4. **Navegue até a pasta 'api-estacionamento' e configure a conexão com o banco de dados**
5. **Navegue até a pasta 'front-estacionamento' e abra o arquivo 'index.php' em seu navegador**
6. **Abra o código do Arduino na IDE do Arduino e envie o código para o dispositivo**

### Endpoints - Cartão

1. **GET `/api/cartao`**
   - Retorna todos os cartões cadastrados.
   
2. **GET `/api/cartao?id={id}`**
   - Retorna os detalhes de um cartão específico com base no `ID_Cartao`.
   
3. **POST `/api/cartao`**
   - Cria um novo cartão. Enviar JSON com:
     - Para cadastro de nome: `{ "Nome_Cartao": "nome_do_cartao" }`
     - Para cadastro de UID do cartão: `{ "NS_Cartao": "uid_do_cartao" }`

4. **PUT `/api/cartao`**
   - Atualiza um cartão existente. Enviar JSON com:
     - `{ "ID_Cartao": "id", "Nome_Cartao": "novo_nome", "NS_Cartao": "novo_uid" }`

5. **DELETE `/api/cartao`**
   - Deleta um cartão com base no `ID_Cartao`. Enviar JSON com:
     - `{ "ID_Cartao": "id" }`

---

### Endpoints - Movimentação

1. **GET `/api/movimentacao`**
   - Retorna todas as movimentações com informações de cartões e vagas.

2. **GET `/api/movimentacao?id={id}`**
   - Retorna os detalhes de uma movimentação específica com base no `ID_Movimentacao`.

3. **POST `/api/movimentacao`**
   - Cria uma nova movimentação ou atualiza o status de uma vaga baseado no sensor. Enviar JSON com:
     - `{ "hora_entrada": "data_hora", "hora_saida": "data_hora", "id_cartao": "id_cartao", "id_vaga": "id_vaga" }`
     - Para atualização de status: `{ "sensorNumero": "numero_sensor", "status": "ativado/desativado" }`

4. **PUT `/api/movimentacao`**
   - Atualiza uma movimentação existente. Enviar JSON com:
     - `{ "id_movimentacao": "id", "hora_entrada": "nova_hora_entrada", "hora_saida": "nova_hora_saida", "id_cartao": "id_cartao", "id_vaga": "id_vaga" }`

5. **DELETE `/api/movimentacao`**
   - Deleta uma movimentação com base no `ID_Movimentacao`. Enviar JSON com:
     - `{ "id_movimentacao": "id" }`

---

### Endpoints - Pessoa

1. **GET `/api/pessoa`**
   - Retorna todas as pessoas cadastradas.

2. **POST `/api/pessoa`**
   - Cria uma nova pessoa. Enviar JSON com:
     - `{ "Nome_Pessoa": "nome", "Telefone": "telefone", "Email": "email", "ID_Cartao": "id_cartao" }`

3. **PUT `/api/pessoa`**
   - Atualiza uma pessoa existente. Enviar JSON com:
     - `{ "ID_Pessoa": "id", "Nome_Pessoa": "novo_nome", "Telefone": "novo_telefone", "Email": "novo_email", "ID_Cartao": "novo_id_cartao" }`

4. **DELETE `/api/pessoa`**
   - Deleta uma pessoa com base no `ID_Pessoa`. Enviar JSON com:
     - `{ "ID_Pessoa": "id" }`

---

### Endpoints - Vaga

1. **GET `/api/vaga`**
   - Retorna todas as vagas cadastradas.

2. **POST `/api/vaga`**
   - Cria uma nova vaga. Enviar JSON com:
     - `{ "Nome_Vaga": "nome", "Ocupado": "0/1" }`

3. **PUT `/api/vaga`**
   - Atualiza o nome ou status de uma vaga. Enviar JSON com:
     - `{ "ID_Vaga": "id", "Nome_Vaga": "novo_nome", "Ocupado": "0/1" }`

4. **DELETE `/api/vaga`**
   - Deleta uma vaga com base no `ID_Vaga`. Enviar JSON com:
     - `{ "ID_Vaga": "id" }`

## Características

- CRUD de pessoas
- CRUD de cartões
- CRUD de vagas
- Gerenciamento de movimentações

## Testes

Utilize o Postman para testar os endpoints da API. Mais detalhes serão adicionados na seção de Endpoints.

## Licença

Este projeto não possui uma licença definida.

## Autores

- Enrico Tondato - Desenvolvedor do Front-End
- Izaque Nogueira - Desenvolvedor do Sistema Arduino
- Vinicius Cardoso - Desenvolvedor da API
*Alunos do 4º semestre de ADS da Fatec Cruzeiro 2024*

## Referências

- [Repositório da API de Estacionamento](https://github.com/ovinicardoso/star-parking/)
