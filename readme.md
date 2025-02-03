
# 📄 Document Visualizer

**Document Visualizer** é uma biblioteca em PHP desenvolvida para facilitar a visualização de documentos como planilhas Excel, imagens e PDFs diretamente no navegador. Ideal para desenvolvedores que buscam uma solução pronta para integrar em suas aplicações web.

## 🚀 Funcionalidades

- **Visualização de Planilhas Excel**: Renderiza arquivos Excel como tabelas HTML.
- **Visualização de Imagens**: Suporte a diferentes formatos de imagem, com funcionalidades de zoom.
- **Visualização de PDFs**: Exibe arquivos PDF com suporte a zoom e navegação de páginas (próxima/anterior).
- **Toolbar Personalizável**: Permite adicionar botões personalizados à interface de visualização, para atender necessidades específicas.

## 🛠 Requisitos

- **PHP 8.4.1+**: [Baixar PHP](https://windows.php.net/downloads/releases/php-8.4.1-nts-Win32-vs17-x64.zip)
- **Composer 2.7.7+**: [Baixar Composer](https://getcomposer.org/Composer-Setup.exe)

## 💻 Instalação

Para instalar a biblioteca, siga os passos abaixo:

1. **Instalar via Composer:**

   Execute o seguinte comando para adicionar a biblioteca ao seu projeto:

   ```bash
   composer require ryan-junio-oliveira/document-visualizer
   ```

2. **Carregue as dependências no seu projeto**:

   Após a instalação, o Composer irá gerenciar o autoload automaticamente. Para garantir que os arquivos da biblioteca estejam carregados, adicione o seguinte código no início do seu projeto:

   ```php
   require 'vendor/autoload.php';
   ```

## 🧑‍💻 Exemplo de Uso

A seguir, um exemplo básico de como utilizar o **Document Visualizer**:

```php
<?php

use RyanJunioOliveira\DocumentVisualizer\DocumentViewer;

require('vendor/autoload.php');

$viewer = new DocumentViewer('teste2.pdf', 'Visualizador de documentos', $addtionalContent);

echo $viewer->visualize();
```

## 🛠 Customização de Toolbar

É possível adicionar botões personalizados à barra de ferramentas da visualização. Por exemplo:

```php
<?php

use RyanJunioOliveira\DocumentVisualizer\DocumentViewer;

require('vendor/autoload.php');

$addtionalContent = '
    <button id="print" class="icon-button bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md shadow transition-transform transform hover:scale-105">
        <i class="fas fa-print"></i>
    </button>
    <script>
        document.getElementById("print").addEventListener("click", () => {
            window.print();
        });
    </script>
';

$viewer = new DocumentViewer('teste2.pdf', 'Visualizador de documentos', $addtionalContent);

?>

<div class="w-full max-w-80 m-auto mt-16">
    <?php echo $viewer->visualize(); ?>
</div>
```

## 🤝 Contribuições

Sinta-se à vontade para abrir issues ou pull requests! Toda contribuição é bem-vinda.

1. Faça um **fork** do projeto.
2. Crie uma nova branch para sua funcionalidade (`git checkout -b funcionalidade-nova`).
3. Faça commit de suas alterações (`git commit -m 'Adiciona nova funcionalidade'`).
4. Faça push para a branch (`git push origin funcionalidade-nova`).
5. Abra um Pull Request.

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - consulte o arquivo [LICENSE](LICENSE) para mais detalhes.
