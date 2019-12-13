# API DSW REAVALIAÇÃO

A API DSW é um exemplo de um CRUD com backend PHP e Banco de Dados Mysql ou Postgres. A API possui apenas uma tabela de PRODUTOS e um endpoint especícfico para acessar as funcionalidades de CRUD para esta tabela. Além do endpoint PRODUTO, a API conta com o endpoint LOGIN para simular a autenticação de usuário necessária em atividades de  POST, PUT ou DELETE.


# INSTALAÇÃO

A instalação da API consiste em extrair os arquivos do download  ou clonar o repositório no diretório raiz do servidor Web Apache. Normalmente a pasta HTDOCS ou WWW, depende do seu Sistema Operacional ou configuração do Apache.

Para o funcionamento correto da API também é necessário a instalação dos bancos de dados suportados. Na pasta **_model/dbs_** existem os arquivos para importação do banco de acordo  com o SGDB disponível (mysql ou postgres). As informações de acesso ao banco de dados devem ser configuradas no arquivo ***config.php***.

```php
<?php
global $host,$name,$user,$password,$drive;

$host="localhost";
$name="apiprod";
$user="root";
$password="";
$drive="mysql";
//$drive="pgsql"; //para trocar de SGBD
```

A Api utilia o módulo de sobreescrita de URL do Apache, portanto o ***mod_rewriter*** deverá estar habilitado.
Veja aqui como habilitar o módulo de sobreescrita de URL do Apache.

[Como habilitar o mod_rewriter no Apache (DEBIAN e Derivados).](https://www.digitalocean.com/community/tutorials/como-reescrever-urls-com-mod_rewrite-para-apache-no-debian-10-pt)

Após configurar ó módulo _mod_rewrite_ no Apache será necessário alterar a URL da API no arquivo ***.htaccess***. Edite a opção ***RewriteBase*** com o caminho correto para o destino ao qual foi instalada a API no seu servidor.
Exemplo:

```.htaccess
...
	<IfModule mod_rewrite.c>
	...
		RewriteBase /caminho/da/api/
	...
	</IfModule>
...
```
O valor do ***RewriteBase*** será a URL da API no servidor local (http://localhost/caminho/da/api).

# UTILIZAÇÃO

A API disponibiliza dois endpoints em GET, o primeiro para listagem de PRODUTOS cadastrados no banco e o segundo para verificação de autenticação de usuário. Todas as respostas da API são em formado **JSON**. Os exemplos consideram que a api foi instalada na pasta ***dsw_api*** dentro do diretório raiz do servidor HTTP.

## Listando PRODUTOS - GET

Para listar todos os produtos do banco de dados basta acessar o endpoint **_produto_**.  As operações de leitura não necessitam de autenticação do usuário.
Exemplo:

URL :  http://localhost/dsw_api/produto/

RESPOSTA:

```js 
     [
     "id_prod":  "1",
    "nome":  "fkor",
    "descricao":  "Dolore et magni a quae et cupiditate ut.",
    "qtd_estoque":  "4",
    "preco":  "2631100.97",
    "importado":  "0",
    "descontos":  [
    "esse",
    "velit",
    "hic",
    "qui"
    ],
    "itens_extras":  [
		    "accusamus",
		    "veniam"
		    ]
    },
    {
    "id_prod":  "2",
    "nome":  "eooj",
    "descricao":  "Aperiam est qui cupiditate inventore.",
    "qtd_estoque":  "5",
    "preco":  "0.85",
    "importado":  "0",
    "descontos":  [
		    "esse",
		    "velit",
		    "qui"
    ],
    "itens_extras":  [
		    "accusamus",
		    "veniam",
		    "sint"
    ]
    },
    {
    "id_prod":  "3",
    "nome":  "kgoy",
    "descricao":  "Aut ut dolores est eos ut officia sit quod.",
    "qtd_estoque":  "1",
    "preco":  "54558458.10",
    "importado":  "1",
    "descontos":  [
	    "esse",
	    "hic",
	    "qui"
    ],
    "itens_extras":  [
	    "cumque",
	    "accusamus",
	    "veniam"
    ]
    },
    ...]
   ``` 

### Parâmetros de Listagem

#### ID do Produto

É possível acessar um único produto pelo seu ID apenas colocando o valor do ID depois do endpoint _produto_  ( http://.../.../_produto_/**ID**) como no exemplo abaixo:

URL :  http://localhost/dsw_api/produto/3

RESPOSTA:
```js
    {
    "id_prod":  "3",
    "nome":  "kgoy",
    "descricao":  "Aut ut dolores est eos ut officia sit quod.",
    "qtd_estoque":  "1",
    "preco":  "54558458.10",
    "importado":  "1",
    "descontos":  [
        "esse",
        "hic",
        "qui"
    ],
    "itens_extras":  [
        "cumque",
        "accusamus",
        "veniam"
    ]
    }
```


#### Quantidade de Produtos

Para controlar quantos produtos serão listados é possível passar a quantidade (QTD) como segundo parâmetro depois do ID ( http://.../.../_produto_/ID/**QTD**)).

URL :  http://localhost/dsw_api/produto/1/5

RESPOSTA: Retornará uma lista de objetos JSON com os 5 protudos a apartir do ID 1 inclusive.

#### Ordem de ID Decrescente 

Para controlar quantos produtos serão listados é possível passar a quantidade (QTD) como segundo parâmetro depois do ID ( http://.../.../_produto_/ID/**QTD**)).

URL :  http://localhost/dsw_api/produto/1/5/1

RESPOSTA: Retornará uma lista de objetos JSON com os 5 protudos a apartir do ID 1 inclusive em ordem DECRESCENTE, ou seja, o ID 5 será o primeiro e o ID 1 o último da lista.

#### Listagem Reversa

Para listar os últimos produtos cadastrados no banco de dados deverá ser passado o valor do **ID** negativo. Por exemplo, -1, no parâmetro ID significa o último elemento, independente do seu ID verdadeiro no banco. Também é possível combinar com o parâmetro de quantidade e ordenação. Veja alguns exemplos:

URL :  http://localhost/dsw_api/produto/-1

RESPOSTA: Retorna um o bjeto JSON representando o ***último*** produto cadastrado.

URL :  http://localhost/dsw_api/produto/-1

RESPOSTA: Retorna um o bjeto JSON representando o ***penúltimo*** produto cadastrado.

URL :  http://localhost/dsw_api/produto/-1/3

RESPOSTA: Retorna um lista de objetos JSON com o s ***três últimos*** produtos.

URL :  http://localhost/dsw_api/produto/-1/3/1

RESPOSTA: Retorna um lista de objetos JSON com o s ***três últimos*** produtos, em ordem decrescente.


## Criando um novo PRODUTO - POST

Para a criação de um novo produto é necessário um requisição do tipo **POST** para o endpoint ***produto***. Nesta operação o usuário deverá estar devidamente aunteticado, o que se resume a existir uma sessão no PHP. Na seção de Login pode ser visto como autenticar o usuário. Os parâmetros devem ser enviados via POST e no formato do objeto [FormData](https://developer.mozilla.org/pt-BR/docs/Web/API/FormData).

#### URL: http://.../.../produto/
#### Parâmetros:
 - nome
 - descricao
 - qtd_estoque
 - preco
 - importado
 - desco [ ] Array de descontos (Select Multiplo)
 - itens [ ] Array de itens adicionais (CheckList)
	
Exemplo:
 	
	- nome: TESTE
	- descricao: novo produto
	- qtd_estoque: 21
	- preco: 12
	- importado: 0
	- desco[]: 1
	- desco[]: 2
	- desco[]: 3
	- itens[]: Grantia Extendida

## Editando um PRODUTO - PUT

Para a atualização dos dados de um PRODUTO já cadastrado no banco, a API utiliza o mesmo endpoint ***produto*** porem com o verbo **PUT**. Neste caso os parâmtros são os mesmos da operação de criação, porém o parâmetro ***ID_PROD***, com o ID do produto, é necessário. O envio dos dados é feito pelo CORPO (BODY) da requisição em Formato JSON. Pode ser usado o JSON.stringify(obj) para converter o objeto com os dados a serem enviados na requisição. Para esta operação o usuário deverá estar autenticado, caso contrário a API retornará um JSON com a mensagem de erro de permissão.

MÉTODO: ***PUT***

PARÂMETROS: 
```js
   {
    	"id_prod": 104,
    	"nome": "Postman TESTE",
    	"descricao": "teste POSTMAN",
    	"qtd_estoque": 12,
    	"preco": 50,
    	"importado": 1,
    	"desco": "Sem descontos",
    	"itens": "Sem itens"
    }
```
URL: http://localhost/dsw_api/produto

RESPOSTA:
```js
{
	"confirm":  true,
	"msg":  "Produto atualizado!",
	"produto":  {
		"id_prod":  104,
		"nome":  "Postman TESTE",
		"descricao":  "teste POSTMAN",
		"qtd_estoque":  12,
		"preco":  50,
		"importado":  1,
		"desco":  "Sem descontos",
		"itens":  "Sem itens"
	}
}
```

## Excluindo um PRODUTO - DELETE

Para DELETAR  um PRODUTO já cadastrado no banco, a API utiliza o mesmo endpoint ***produto*** porem com o verbo **DELETE**. Para esta operação o único parâmtro necessário é o ***ID_PROD***, com o ID do produto a ser excluido. O envio do parâmetro é feito pelo CORPO (BODY) da requisição em Formato JSON. Pode ser usado o JSON.stringify(obj) para converter o objeto com os dados a serem enviados na requisição. Para esta operação o usuário deverá estar autenticado, caso contrário a API retornará um JSON com a mensagem de erro de permissão.


MÉTODO: ***DELETE***

PARÂMETROS: ***{id_prod: 99}*** 

URL: http://localhost/dsw_api/produto

RESPOSTA:
```js
{"confirm":  true, "msg":  "DELETE PROD ID: 99"}
```

## Autenticação

Para a autenticação do usuário é necessária uma requisição do tipo **POST** para o endpoint ***login*** enviando os parâmetros *user* e *key* com os dados de autenticação. O formato de envio dos dados na requisição deverá ser do tipo *POST* e através do objeto *[FormData](https://developer.mozilla.org/pt-BR/docs/Web/API/FormData)*. A API deverá retornar um JSON com o campo booleano "login" para a verificação.


MÉTODO: POST

PARÂMETROS: [FormData](https://developer.mozilla.org/pt-BR/docs/Web/API/FormData)
 - **user** :  *ts1*
 - **key** :  *ts1@dsw*

**Dados do único usuário cadastrado*

URL: http://localhost/dsw_api/login

RESPOSTA:
```js
{ "login":  true }
```


<!--stackedit_data:
eyJoaXN0b3J5IjpbLTE5OTkyNTUzMjgsLTE2MjAwMDg2NDYsMz
U4ODk4OTE5XX0=
-->
